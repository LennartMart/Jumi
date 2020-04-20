<?php

/**
 * @version   $Id$
 * @package   Jumi
 * @copyright (C) 2008 - 2015 Edvard Ananyan
 * @license   GNU/GPL v3 http://www.gnu.org/licenses/gpl.html
 */

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;

defined('_JEXEC') or die;

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
$controller = BaseController::getInstance('Jumi');
$controller->execute(Factory::getApplication()->input->get('task'));
$controller->redirect();
