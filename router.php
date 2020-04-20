<?php

/**
 * @version   $Id$
 * @package   Jumi
 * @copyright (C) 2008 - 2015 Edvard Ananyan
 * @license   GNU/GPL v3 http://www.gnu.org/licenses/gpl.html
 */

use Joomla\CMS\Factory;

function JumiBuildRoute(&$query)
{
    $db = Factory::getDBO();
    $segments = array();

    if (isset($query['fileid'])) {
        $db->setQuery('select alias from #__jumi where id = ' . $db->quote($query['fileid']));
        $segments[] = $db->loadResult();
        unset($query['fileid']);
    }

    return $segments;
}

function JumiParseRoute($segments)
{
    $db = Factory::getDBO();
    $vars = array();

    $db->setQuery('select id from #__jumi where alias = ' . $db->quote($segments[0]));
    $vars['fileid'] = $db->loadResult();

    return $vars;
}
