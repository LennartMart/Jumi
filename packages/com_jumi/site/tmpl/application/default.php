<?php

/**
 * @package     Jumi
 * @subpackage  com_jumi
 *
 * @copyright   (C) 2026 LennartMart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Language\Text;

/** @var \Jumi\Component\Jumi\Site\View\Application\HtmlView $this */

$item = $this->item;

// Execute the custom script (Jumi's core behaviour: admin-authored code).
if (!empty($item->custom_script)) {
    eval('?>' . $item->custom_script);
}

// Optionally include a file after the custom script.
if (!empty($item->path)) {
    // Constrain the include to the Joomla root to prevent directory traversal / arbitrary file inclusion.
    $safePath = null;

    if (strpos((string) $item->path, "\0") === false) {
        $realBase  = realpath(JPATH_ROOT);
        $candidate = $realBase . '/' . ltrim((string) $item->path, '/\\');
        $real      = realpath($candidate);

        if (
            $realBase !== false
            && $real !== false
            && is_file($real)
            && ($real === $realBase || strpos($real, $realBase . \DIRECTORY_SEPARATOR) === 0)
        ) {
            $safePath = $real;
        }
    }

    if ($safePath !== null) {
        require $safePath;
    } else {
        echo '<div class="alert alert-warning">'
            . Text::sprintf('COM_JUMI_ERROR_FILE_NOT_FOUND', htmlspecialchars((string) $item->path, ENT_QUOTES, 'UTF-8'))
            . '</div>';
    }
}
