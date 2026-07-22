<?php

/**
 * @package     Jumi
 * @subpackage  pkg_jumi
 *
 * @copyright   (C) 2026 LennartMart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;

/**
 * Installation script for the Jumi package.
 *
 * A plain (non-interface) installer script is used deliberately so the same
 * file works across Joomla 4, 5 and 6 using the legacy calling convention.
 *
 * @since  4.0.0
 */
class Pkg_JumiInstallerScript
{
    /**
     * Runs after the install/update/discover_install of the package.
     *
     * @param   string  $type    The type of change (install, update or discover_install).
     * @param   mixed   $parent  The class calling this method.
     *
     * @return  boolean  True on success.
     *
     * @since   4.0.0
     */
    public function postflight($type, $parent)
    {
        // Enable the Jumi system plugin so it works out of the box.
        try {
            $db = Factory::getContainer()->get(DatabaseInterface::class);

            $query = $db->getQuery(true)
                ->update($db->quoteName('#__extensions'))
                ->set($db->quoteName('enabled') . ' = 1')
                ->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
                ->where($db->quoteName('folder') . ' = ' . $db->quote('system'))
                ->where($db->quoteName('element') . ' = ' . $db->quote('jumi'));

            $db->setQuery($query)->execute();
        } catch (\Throwable $e) {
            // Non-fatal: the administrator can enable the plugin manually.
        }

        return true;
    }
}
