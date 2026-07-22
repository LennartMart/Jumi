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
            } elseif (is_readable($storageSource)) {
                include $storageSource;
            } else {
                echo '<div class="alert alert-warning">'
                    . Text::sprintf('MOD_JUMI_ERROR_FILE', htmlspecialchars($storageSource, ENT_QUOTES, 'UTF-8'))
                    . '</div>';
            }
        }

        if ($codeWritten === '' && $storageSource === '') {
            echo '<div class="alert alert-warning">' . Text::_('MOD_JUMI_ERROR_CONTENT') . '</div>';
        }

        return (string) ob_get_clean();
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
    private function getStorageSource(Registry $params)
    {
        $storage = trim((string) $params->get('source_code_storage', ''));

        if ($storage === '') {
            return '';
        }

        // "*id" syntax references a Jumi component record.
        if ($id = substr(strchr($storage, '*'), 1)) {
            return (int) $id;
        }

        $abspath = trim((string) $params->get('default_absolute_path', ''));

        if ($abspath === '') {
            $abspath = JPATH_ROOT;
        }

        return $abspath . '/' . $storage;
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
