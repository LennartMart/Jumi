<?php

/**
 * Joomla! 3.x component Jumi
 *
 * @version $Id: controller.php 2012-04-05 14:30:25 svn $
 * @author Edvard Ananyan
 * @package Joomla
 * @subpackage Jumi
 * @license GNU/GPL
 *
 * Jumi
 *
 */

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;

// no direct access
defined('_JEXEC') or die('Restricted access');


/**
 * jumi Controller
 *
 * @package Joomla
 * @subpackage com_jumi
 */

class JumiController extends BaseController
{
    function display($cachable = false, $urlparams = array())
    {

        addSub('Application Manager', 'showapplications');

        //Set the default view, just in case
        $jinput = Factory::getApplication()->input;
        $view = $jinput->getCmd('view');
        if (empty($view)) {
            $jinput->set('view', 'showApplications');
        };

        parent::display();
    } // function


};
