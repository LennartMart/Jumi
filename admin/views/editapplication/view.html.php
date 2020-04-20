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
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
// no direct access
defined('_JEXEC') or die('Restricted access');


class JumiVieweditApplication extends HtmlView
{
    function display($tpl = null)
    {
        //get the data
        $application = $this->get('Data');

        $isNew = ($application->id < 1);

        $text = $isNew ? Text::_('New') : Text::_('Edit');
        ToolbarHelper::title(Text::_('Jumi Application') . ': <small><small>[ ' . $text . ' ]</small></small>', 'manage.png');
        ToolbarHelper::save();
        if ($isNew) {
            ToolbarHelper::cancel();
        }
        else {
            ToolbarHelper::apply();
            // for existing items the button is renamed `close`
            ToolbarHelper::cancel('cancel', 'Close');
        }
        ToolbarHelper::help('screen.applications.edit');

        $this->row = $application;

        parent::display($tpl);
    }
}
