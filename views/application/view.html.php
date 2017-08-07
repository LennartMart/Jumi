<?php

/**
 * @version   $Id$
 * @package   Jumi
 * @copyright (C) 2008 - 2015 Edvard Ananyan
 * @license   GNU/GPL v3 http://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * @package     Jumi
 */
class JumiViewApplication extends JViewLegacy
{
    function display($tpl = null)
    {
        // Initialise variables.

        $database = JFactory::getDBO();
        $user = JFactory::getUser();
        $document = JFactory::getDocument();
        $mainframe = JFactory::getApplication();
        $jinput = $mainframe->input;
        $fileid = $jinput->getInt('fileid');

        $database->setQuery("select * from #__jumi where id = ". $database->quote($fileid) . "and published = 1");
        $appl = $database->loadObject();

        if (!is_object($appl))
            echo '<div style="color:#FF0000;background:#FFFF00;">' . JText::_("The Jumi Application is Unpublished or Removed") . '</div>';

        $document->setTitle($appl->title);

        eval('?>' . $appl->custom_script);

        if (!empty($appl->path)) {
            $filepath = JPATH_BASE . DS . $appl->path;
            if (is_file($appl->path)) {
                require ($appl->path);
            }
            elseif (is_file($filepath))
                require $filepath;
            else
                echo '<div style="color:#FF0000;background:#FFFF00;">The file ' . $filepath . ' does not exists.</div>';
        }

        parent::display($tpl);
    }
}
