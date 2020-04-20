<?php

/**
 * Joomla! 3.x component Jumi
 *
 * @version $Id: view.html.php 2012-04-05 14:30:25 svn $
 * @author Edvard Ananyan
 * @package Joomla
 * @subpackage jumi
 * @license GNU/GPL
 *
 *
 */
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\HTML\HTMLHelper;

// no direct access
defined('_JEXEC') or die('Restricted access');


class JumiViewshowApplications extends HtmlView
{
    function display($tpl = null)
    {

        //toolbar
        ToolbarHelper::publishList();
        ToolbarHelper::unpublishList();
        ToolbarHelper::addNew();
        ToolbarHelper::editList();
        ToolbarHelper::deleteList();
        ToolbarHelper::help('screen.applications');

        // Get data from the model
        $items = $this->get('Data');
        $filter = $this->get('Filter');
        $pagination = $this->get('Pagination');

        $this->items = $items;
        $this->filter = $filter;
        $this->pagination = $pagination;

        JHtmlSidebar::addFilter(
            Text::_('JOPTION_SELECT_PUBLISHED'),
            'filter_published',
            @HTMLHelper::_('select.options', HTMLHelper::_('jgrid.publishedOptions'), 'value', 'text', $this->filter->published, true)
        );

        parent::display($tpl);
    }

    /**
     * Returns an array of fields the table can be sorted by
     *
     * @return  array  Array containing the field name to sort by as the key and display text as value
     *
     * @since   3.0
     */
    protected function getSortFields()
    {
        return array(
            'm.published' => Text::_('JSTATUS'),
            'm.title' => Text::_('JGLOBAL_TITLE'),
            'm.path' => Text::_('Path'),
            'm.id' => Text::_('Id'),
        );
    }
}
