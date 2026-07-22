<?php

/**
 * @package     Jumi
 * @subpackage  System.jumi
 *
 * @copyright   (C) 2026 LennartMart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Jumi\Plugin\System\Jumi\Extension;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Database\ParameterType;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Jumi system plugin.
 *
 * Replaces the {jumi [source] [arg1] ... [argN]} syntax inside prepared content
 * items (e.g. article text) with the output of the referenced Jumi application
 * (stored record or file).
 *
 * The plugin deliberately hooks onContentPrepare rather than onAfterRender: the
 * latter scans the entire rendered page body, so any reflected user input that
 * happened to contain a {jumi ...} tag (search terms, error messages, 404 URLs,
 * ...) would be interpreted and executed. Processing only prepared content items
 * keeps the trust boundary at "who may author this content".
 *
 * @since  4.0.0
 */
final class Jumi extends CMSPlugin implements SubscriberInterface, DatabaseAwareInterface
{
    use DatabaseAwareTrait;

    /**
     * Load the language file on instantiation.
     *
     * @var    boolean
     * @since  4.0.0
     */
    protected $autoloadLanguage = true;

    /**
     * Returns an array of events this subscriber will listen to.
     *
     * @return  array
     *
     * @since   4.0.0
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'onContentPrepare' => 'onContentPrepare',
        ];
    }

    /**
     * Search a prepared content item for {jumi ...} tags and replace them.
     *
     * @param   Event  $event  The event instance.
     *
     * @return  void
     *
     * @since   4.0.0
     */
    public function onContentPrepare(Event $event): void
    {
        $app = $this->getApplication();

        // Only run on the front-end.
        if ($app && $app->isClient('administrator')) {
            return;
        }

        // Resolve the content item across the typed content event and the generic argument styles.
        if (method_exists($event, 'getItem')) {
            $item = $event->getItem();
        } else {
            $item = $event->getArgument('subject') ?? $event->getArgument('object');
        }

        if (!\is_object($item) || empty($item->text) || strpos((string) $item->text, '{jumi') === false) {
            return;
        }

        $item->text = $this->replaceTags((string) $item->text);
    }

    /**
     * Replace every {jumi ...} tag inside the given text with the referenced output.
     *
     * @param   string  $content  The content to process.
     *
     * @return  string  The processed content.
     *
     * @since   4.0.0
     */
    private function replaceTags(string $content): string
    {
        // Expression to search for.
        $regex   = '/{(jumi)\s*(.*?)}/i';
        $absPath = trim((string) $this->params->get('default_absolute_path', ''));

        // If hide_code then simply strip the Jumi tags.
        if ((int) $this->params->get('hide_code', 0) === 1) {
            return (string) preg_replace($regex, '', $content);
        }

        $nestedReplace     = (int) $this->params->get('nested_replace', 0) === 1;
        $continueSearching = true;

        while ($continueSearching) {
            $matches = [];

            if (preg_match_all($regex, $content, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    // Read the bracketed arguments: [source][arg1]...[argN].
                    $mms  = [];
                    $jumi = [];
                    preg_match_all('/\[.*?\]/', $match[2], $mms);

                    if (!empty($mms[0])) {
                        $jumi = preg_replace('/\[|\]/', '', $mms[0]);
                    }

                    // The remaining $jumi entries are available to the included code as $jumi[].
                    $storageSource = $this->getStorageSource(trim((string) array_shift($jumi)), $absPath);
                    $output        = '';

                    if ($storageSource === '') {
                        $output = '<div class="alert alert-warning">' . Text::_('PLG_SYSTEM_JUMI_ERROR_CONTENT') . '</div>';
                    } else {
                        ob_start();

                        if (\is_int($storageSource)) {
                            $codeStored = $this->getCodeStored($storageSource);

                            if ($codeStored !== null) {
                                // phpcs:ignore Squiz.PHP.Eval.Discouraged
                                eval('?>' . $codeStored);
                            } else {
                                $output = '<div class="alert alert-warning">'
                                    . Text::sprintf('PLG_SYSTEM_JUMI_ERROR_RECORD', $storageSource) . '</div>';
                            }
                        } elseif (($safePath = $this->resolveIncludePath($storageSource, $absPath)) !== null) {
                            include $safePath;
                        } else {
                            $output = '<div class="alert alert-warning">'
                                . Text::sprintf('PLG_SYSTEM_JUMI_ERROR_FILE', htmlspecialchars($storageSource, ENT_QUOTES, 'UTF-8'))
                                . '</div>';
                        }

                        if ($output === '') {
                            $output = (string) ob_get_contents();
                        }

                        ob_end_clean();
                    }

                    // Replace only the first occurrence so identical tags regenerate independently.
                    if (($start = strpos($content, $match[0])) !== false) {
                        $content = substr_replace($content, $output, $start, \strlen($match[0]));
                    }
                }

                if (!$nestedReplace) {
                    $continueSearching = false;
                }
            } else {
                $continueSearching = false;
            }
        }

        return $content;
    }

    /**
     * Resolve the storage source: a record id (int), a file path (string), or ''.
     *
     * @param   string  $source   The first Jumi argument.
     * @param   string  $absPath  The configured default absolute path.
     *
     * @return  int|string  Record id, file path, or empty string.
     *
     * @since   4.0.0
     */
    private function getStorageSource(string $source, string $absPath)
    {
        $storage = trim($source);

        if ($storage === '') {
            return '';
        }

        // "*id" syntax references a Jumi component record.
        if ($id = substr(strchr($storage, '*'), 1)) {
            return (int) $id;
        }

        return $storage;
    }

    /**
     * Safely resolve a file reference to an absolute path constrained to a trusted base directory.
     *
     * To avoid arbitrary local file inclusion / directory traversal, the resolved file must exist and
     * live inside the configured base directory (or the Joomla root when no base is configured).
     *
     * @param   string  $source   The raw file reference taken from the tag.
     * @param   string  $absPath  The configured default absolute path (may be empty).
     *
     * @return  string|null  The safe absolute path, or null when the reference is not allowed.
     *
     * @since   4.0.0
     */
    private function resolveIncludePath(string $source, string $absPath): ?string
    {
        // Reject null bytes outright.
        if (strpos($source, "\0") !== false) {
            return null;
        }

        $base     = $absPath !== '' ? $absPath : JPATH_ROOT;
        $realBase = realpath($base);

        if ($realBase === false) {
            return null;
        }

        // Always resolve the reference relative to the trusted base; ignore any leading slash so an
        // absolute path cannot escape the base directory.
        $candidate = $realBase . \DIRECTORY_SEPARATOR . ltrim($source, '/\\');
        $real      = realpath($candidate);

        if ($real === false || !is_file($real) || !is_readable($real)) {
            return null;
        }

        // The canonicalised path must remain inside the base directory (defeats ../ traversal).
        if ($real !== $realBase && strpos($real, $realBase . \DIRECTORY_SEPARATOR) !== 0) {
            return null;
        }

        return $real;
    }

    /**
     * Fetch the custom script stored in the Jumi component table for the given id.
     *
     * @param   int  $id  The record id.
     *
     * @return  string|null  The stored code or null when not found.
     *
     * @since   4.0.0
     */
    private function getCodeStored(int $id): ?string
    {
        $db    = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select($db->quoteName('custom_script'))
            ->from($db->quoteName('#__jumi'))
            ->where($db->quoteName('id') . ' = :id')
            ->bind(':id', $id, ParameterType::INTEGER);
        $db->setQuery($query);

        $result = $db->loadResult();

        return $result === null ? null : (string) $result;
    }
}
