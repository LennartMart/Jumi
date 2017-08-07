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

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport( 'joomla.application.component.view');

class JumiViewshowApplications extends JViewLegacy {
    function display($tpl = null) {

        //toolbar
        JToolBarHelper::publishList();
        JToolBarHelper::unpublishList();
        JToolBarHelper::addNew();
        JToolBarHelper::editList();
        JToolBarHelper::deleteList();
        JToolBarHelper::help( 'screen.applications' );

        // Get data from the model
        $items = $this->get( 'Data');
        $filter = $this->get('Filter');
        $pagination = $this->get('Pagination');

        $this->assignRef( 'items', $items );
        $this->assignRef('filter', $filter);
        $this->assignRef('pagination', $pagination);

        JHtmlSidebar::addFilter(
                JText::_('JOPTION_SELECT_PUBLISHED'),
                'filter_published',
                @JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->filter->published, true)
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
                'm.published' => JText::_('JSTATUS'),
                'm.title' => JText::_('JGLOBAL_TITLE'),
                'm.path' => JText::_('Path'),
                'm.id' => JText::_('Id'),
        );
    }
}

?>