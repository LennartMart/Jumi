<?php

/**
 * @package     Jumi
 * @subpackage  mod_jumi
 *
 * @copyright   (C) 2026 LennartMart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Jumi\Module\Jumi\Site\Helper;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Database\ParameterType;
use Joomla\Registry\Registry;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Helper for mod_jumi.
 *
 * @since  4.0.0
 */
class JumiHelper implements DatabaseAwareInterface
{
    use DatabaseAwareTrait;

    /**
     * Build the module output by executing the written code and/or the stored source.
     *
     * @param   Registry                 $params  The module parameters.
     * @param   CMSApplicationInterface  $app     The application.
     *
     * @return  string  The rendered output.
     *
     * @since   4.0.0
     */
    public function render(Registry $params, CMSApplicationInterface $app): string
    {
        $codeWritten   = trim((string) $params->get('code_written', ''));
        $storageSource = $this->getStorageSource($params);

        ob_start();

        try {
            // $app is intentionally in scope here so the evaluated/included code can use it.
            if ($codeWritten !== '') {
                // phpcs:ignore Squiz.PHP.Eval.Discouraged
                eval('?>' . $codeWritten);
            }

            if ($storageSource !== '') {
                if (\is_int($storageSource)) {
                    $codeStored = $this->getCodeStored($storageSource);

                    if ($codeStored !== null) {
                        // phpcs:ignore Squiz.PHP.Eval.Discouraged
                        eval('?>' . $codeStored);
                    } else {
                        echo '<div class="alert alert-warning">'
                            . Text::sprintf('MOD_JUMI_ERROR_RECORD', $storageSource) . '</div>';
                    }
                } elseif (($safePath = $this->resolveIncludePath((string) $storageSource, $params)) !== null) {
                    include $safePath;
                } else {
                    echo '<div class="alert alert-warning">'
                        . Text::sprintf('MOD_JUMI_ERROR_FILE', htmlspecialchars((string) $storageSource, ENT_QUOTES, 'UTF-8'))
                        . '</div>';
                }
            }

            if ($codeWritten === '' && $storageSource === '') {
                echo '<div class="alert alert-warning">' . Text::_('MOD_JUMI_ERROR_CONTENT') . '</div>';
            }
        } finally {
            // Always close the buffer, even when the evaluated code throws, so an
            // exception cannot leak half-rendered output into the page.
            $output = (string) ob_get_clean();
        }

        return $output;
    }

    /**
     * Resolve the source of the stored code: a record id (int) or a file path (string), or ''.
     *
     * @param   Registry  $params  The module parameters.
     *
     * @return  int|string  Record id, file path, or empty string.
     *
     * @since   4.0.0
     */
    private function getStorageSource(Registry $params): int|string
    {
        $storage = trim((string) $params->get('source_code_storage', ''));

        if ($storage === '') {
            return '';
        }

        // "*id" syntax references a Jumi component record.
        if (($pos = strpos($storage, '*')) !== false) {
            return (int) substr($storage, $pos + 1);
        }

        return $storage;
    }

    /**
     * Safely resolve a file reference to an absolute path constrained to a trusted base directory.
     *
     * Prevents arbitrary local file inclusion / directory traversal: the resolved file must exist and
     * live inside the configured base directory (or the Joomla root when none is configured).
     *
     * @param   string    $source  The raw file reference from the module parameters.
     * @param   Registry  $params  The module parameters.
     *
     * @return  string|null  The safe absolute path, or null when the reference is not allowed.
     *
     * @since   4.0.0
     */
    private function resolveIncludePath(string $source, Registry $params): ?string
    {
        if (strpos($source, "\0") !== false) {
            return null;
        }

        $base = trim((string) $params->get('default_absolute_path', ''));

        if ($base === '') {
            $base = JPATH_ROOT;
        }

        $realBase = realpath($base);

        if ($realBase === false) {
            return null;
        }

        // Ignore any leading slash so an absolute reference cannot escape the base directory.
        $candidate = $realBase . \DIRECTORY_SEPARATOR . ltrim($source, '/\\');
        $real      = realpath($candidate);

        if ($real === false || !is_file($real) || !is_readable($real)) {
            return null;
        }

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
