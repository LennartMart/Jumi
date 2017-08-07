<?php
/**
* @version   $Id$
* @package   Jumi
* @copyright (C) 2008 - 2015 Edvard Ananyan
* @license   GNU/GPL v3 http://www.gnu.org/licenses/gpl.html
*/
defined('_JEXEC') or die('Restricted access');
require_once JPATH_COMPONENT . '/router.php';

if(!defined('DS')){
    define('DS',DIRECTORY_SEPARATOR);
}

jimport('joomla.application.component.controller');

$controller = JControllerLegacy::getInstance('Jumi');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();

