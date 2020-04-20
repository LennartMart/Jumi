<?php

/**
 * Joomla! 3.0 Jumi
 *
 * @version $Id: manageanswers.php 2012-04-05 14:30:25 svn $
 * @author Edvard Ananyan
 * @package Joomla
 * @subpackage Jumi
 * @license GNU/GPL
 *
 * Jumi
 *
 */
// Import Joomla! libraries
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\Utilities\ArrayHelper;

// no direct access
defined('_JEXEC') or die('Restricted access');

class JumiModeleditApplication extends BaseDatabaseModel
{

    /**
     * Constructor that retrieves the ID from the request
     *
     * @access  public
     * @return  void
     */
    function __construct()
    {
        parent::__construct();

        $jinput = Factory::getApplication()->input;
        $id = $jinput->getInt('cid', 0);
        $this->setId($id);
    }

    /**
     * Method to set the hello identifier
     *
     * @access  public
     * @param   int Hello identifier
     * @return  void
     */
    function setId($id)
    {
        // Set id and wipe data
        $this->_id = $id;
        $this->_data = null;
    }

    /**
     * Method to get a data
     * @return object with data
     */
    function &getData()
    {
        // Load the data
        if (empty($this->_data)) {
            $query = 'SELECT * FROM #__jumi WHERE id = ' . $this->_db->quote($this->_id);
            $this->_db->setQuery($query);
            $this->_data = $this->_db->loadObject();
        }
        if (!$this->_data) {
            $this->_data = new stdClass();
            $this->_data->id = 0;
            $this->_data->name = null;
        }
        OutputFilter::objectHTMLSafe($this->_data, ENT_QUOTES);
        return $this->_data;
    }


    /**
     * Method to store a record
     *
     * @access  public
     * @return  boolean True on success
     */
    function store()
    {
        $jinput = Factory::getApplication()->input;

        $applid = $jinput->getInt('cid', 0);

        $title = $this->_db->quote($jinput->getString('title'));
        $alias = $this->_db->quote($jinput->getString('alias'));
        $custom_script = $this->_db->quote(stripslashes($_POST['custom_script']));
        $path = $this->_db->quote($jinput->getString('path'));
        if ($applid == 0) {
            $query = "insert into #__jumi (title, alias, custom_script, path) values($title,$alias,$custom_script,$path)";
            $this->_db->setQuery($query);
            if (!$this->_db->query())
                return false;
        } else {
            $query = "update #__jumi set title = $title, alias = $alias, custom_script = $custom_script, path = $path where id = $applid";
            $this->_db->setQuery($query);
            if (!$this->_db->query())
                return false;
        }

        return true;
    }

    /**
     * Method to delete record(s)
     *
     * @access  public
     * @return  boolean True on success
     */
    function delete()
    {
        $jinput = Factory::getApplication()->input;
        $cids = $jinput->post->get('cid', array(), 'ARRAY');

        if (count($cids)) {
            foreach ($cids as $id) {
                $query = "delete from #__jumi where id = " . $this->_db->quote($id);
                $this->_db->setQuery($query);
                $this->_db->query();
                if ($this->_db->getErrorMsg())
                    return false;
            }
        }

        return true;
    }

    /**
     * Method to delete record(s)
     *
     * @access  public
     * @return  boolean True on success
     */
    function publish($publish)
    {
        $jinput = Factory::getApplication()->input;
        $cids = $jinput->post->get('cid', array(), 'ARRAY');
        ArrayHelper::toInteger($cids);
        $cids_sql = implode(',', $cids);

        if (count($cids)) {
            $query = "UPDATE #__jumi SET published = " . (int) $publish . " WHERE id in ($cids_sql)";
            $this->_db->setQuery($query);
            if (!$this->_db->query())
                return false;
        }

        return true;
    }
}
