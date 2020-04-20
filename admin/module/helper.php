<?php

/**
 * @version   $Id$
 * @package   Jumi
 * @copyright (C) 2008 - 2010 Martin Hajek, 2011 Edvard Ananyan
 * @license   GNU/GPL v3 http://www.gnu.org/licenses/gpl.html
 */

use Joomla\CMS\Factory;

defined('_JEXEC') or die('Restricted access');

require_once(JPATH_SITE . DS . 'components' . DS . 'com_content' . DS . 'helpers' . DS . 'route.php');

class modJumiHelper
{
    function getCodeWritten(&$params)
    { //returns code written or ""
        return trim($params->get('code_written'));
    }

    function getStorageSource(&$params)
    { //returns filepathname or a record id or ""
        $storage = trim($params->get('source_code_storage'));
        if ($storage != "") {
            if ($id = substr(strchr($storage, "*"), 1)) { //if record id return it
                return (int) $id;
            } else { // else return filepathname
                return $params->def('default_absolute_path', JPATH_ROOT) . DS . $storage;
            }
        } else {
            return "";
        }
    }

    function getCodeStored($source)
    { //returns code stored in the database or null.
        $database = Factory::getDBO();
        $database->setQuery("select custom_script from #__jumi where id = " . $database->quote($source));
        return $database->loadResult();
    }
}
