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
    $filepath = JPATH_BASE . '/' . $item->path;

    if (is_file($item->path)) {
        require $item->path;
    } elseif (is_file($filepath)) {
        require $filepath;
    } else {
        echo '<div class="alert alert-warning">'
            . Text::sprintf('COM_JUMI_ERROR_FILE_NOT_FOUND', htmlspecialchars($item->path, ENT_QUOTES, 'UTF-8'))
            . '</div>';
    }
}
