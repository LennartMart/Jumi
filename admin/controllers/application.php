<?php

/**
 * Jumi! 3.0 component
 *
 * @version $Id: answers.php 2012-04-05 14:30:25 svn $
 * @author Edvard Ananyan
 * @package Jumi
 * @subpackage Jumi
 * @license GNU/GPL
 *
 *
 */

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Language\Text;

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * 
 *
 * @package Joomla
 * @subpackage Jumi
 */
class JumiControllerapplication extends JumiController
{

    /**
     * constructor (registers additional tasks to methods)
     * @return void
     */
    function __construct()
    {
        parent::__construct();

        // Register Extra tasks
        $this->registerTask('add', 'edit');
        $this->registerTask('unpublish', 'publish');
    }

    /**
     * display the edit form
     * @return void
     */
    function edit()
    {
        $jinput = Factory::getApplication()->input;
        $jinput->set('view', 'editapplication');
        $jinput->set('hidemainmenu', 1);

        parent::display();
    }

    /**
     * save a record (and redirect to main page)
     * @return void
     */
    function save()
    {
        $model = $this->getModel('editapplication');

        if ($model->store()) {
            $msg = Text::_('Application Saved');
        } else {
            $msg = Text::_('Error Saving Application');
        }

        // Check the table in so it can be edited.... we are done with it anyway
        $link = 'index.php?option=com_jumi';
        $this->setRedirect($link, $msg);
    }

    /**
     * save a record (and redirect to main page)
     * @return void
     */
    function apply()
    {
        $model = $this->getModel('editapplication');

        if ($model->store()) {
            $msg = Text::_('Changes to Application saved');
        } else {
            $msg = Text::_('Error Saving Application');
        }
        $jinput = Factory::getApplication()->input;
        $cids = $jinput->post->get('cid', array(), 'ARRAY');
        $id = count($cids) > 0 ? (int) $cids[0] : 0;
        $this->setRedirect('index.php?option=com_jumi&controller=application&task=edit&cid[]=' . $id, $msg);
    }

    /**
     * publish a record (and redirect to main page)
     * @return void
     */
    function publish()
    {
        $publish = ($this->getTask() == 'publish' ? 1 : 0);
        $model = $this->getModel('editapplication');
        if (!$model->publish($publish)) {
            $msg = Text::_('Error: One or More Applications Could not be Published/Unpublished');
        } else {
            $msg = '';
        }

        $this->setRedirect('index.php?option=com_jumi', $msg);
    }

    /**
     * remove record(s)
     * @return void
     */
    function remove()
    {
        $model = $this->getModel('editapplication');
        if (!$model->delete()) {
            $msg = Text::_('Error: One or More Applications Could not be Deleted');
        } else {
            $msg = Text::_('Application(s) Deleted');
        }

        $this->setRedirect('index.php?option=com_jumi', $msg);
    }

    /**
     * cancel editing a record
     * @return void
     */
    function cancel()
    {
        $msg = Text::_('Operation Cancelled');
        $this->setRedirect('index.php?option=com_jumi', $msg);
    }
}
