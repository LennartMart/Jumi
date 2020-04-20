<?php

/**
 * @version   $Id$
 * @package   Jumi
 * @copyright (C) 2008 - 2015 Edvard Ananyan
 * @license   GNU/GPL v3 http://www.gnu.org/licenses/gpl.html
 */

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die('Restricted access');


/**
 * @package     Jumi
 */
class JumiViewApplication extends HtmlView
{
    function display($tpl = null)
    {
        // Initialise variables.

        $database = Factory::getDBO();
        $user = Factory::getUser();
        $document = Factory::getDocument();
        $mainframe = Factory::getApplication();
        $jinput = $mainframe->input;
        $fileid = $jinput->getInt('fileid');

        $database->setQuery("select * from #__jumi where id = " . $database->quote($fileid) . "and published = 1");
        $appl = $database->loadObject();

        if (!is_object($appl))
            echo '<div style="color:#FF0000;background:#FFFF00;">' . Text::_("The Jumi Application is Unpublished or Removed") . '</div>';

        $document->setTitle($appl->title);

        eval('?>' . $appl->custom_script);

        if (!empty($appl->path)) {
            $filepath = JPATH_BASE . DS . $appl->path;
            if (is_file($appl->path)) {
                require($appl->path);
            } elseif (is_file($filepath))
                require $filepath;
            else
                echo '<div style="color:#FF0000;background:#FFFF00;">The file ' . $filepath . ' does not exists.</div>';
        }

        parent::display($tpl);
    }
}
