<?php

/**
 * Joomla! 3.x component Jumi
 *
 * @version $Id: view.html.php 2012-04-05 14:30:25 svn $
 * @author Edvard Ananyan
 * @package Joomla
 * @subpackage Jumi
 * @license GNU/GPL
 *
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.view');

class JumiVieweditApplication extends JViewLegacy
{
    function display($tpl = null)
    {
        //get the data
        $application = $this->get('Data');

        $isNew = ($application->id < 1);

        $text = $isNew ? JText::_('New') : JText::_('Edit');
        JToolBarHelper::title(JText::_('Jumi Application') . ': <small><small>[ ' . $text . ' ]</small></small>', 'manage.png');
        JToolBarHelper::save();
        if ($isNew) {
            JToolBarHelper::cancel();
        }
        else {
            JToolBarHelper::apply();
            // for existing items the button is renamed `close`
            JToolBarHelper::cancel('cancel', 'Close');
        }
        JToolBarHelper::help('screen.applications.edit');

        $this->row = $application;

        parent::display($tpl);
    }
}

?>