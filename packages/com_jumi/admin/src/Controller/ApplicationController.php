<?php

/**
 * @package     Jumi
 * @subpackage  com_jumi
 *
 * @copyright   (C) 2026 LennartMart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Jumi\Component\Jumi\Administrator\Controller;

use Joomla\CMS\MVC\Controller\FormController;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Controller for a single Jumi application.
 *
 * @since  4.0.0
 */
class ApplicationController extends FormController
{
    /**
     * The prefix to use with controller messages.
     *
     * @var    string
     * @since  4.0.0
     */
    protected $text_prefix = 'COM_JUMI_APPLICATION';
}
