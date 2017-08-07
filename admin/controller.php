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

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );

/**
 * jumi Controller
 *
 * @package Joomla
 * @subpackage com_jumi
 */

class JumiController extends JControllerLegacy{
    function display($cachable = false, $urlparams = array())
    {

        addSub( 'Application Manager', 'showapplications');

        //Set the default view, just in case
        $view = JRequest::getCmd('view');
        if(empty($view)) {
            JRequest::setVar('view', 'showApplications');
        };

        parent::display();
    }// function
};

?>