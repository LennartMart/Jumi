<?php

/**
 * @version   $Id$
 * @package   Jumi
 * @copyright (C) 2008 - 2015 Edvard Ananyan
 * @license   GNU/GPL v3 http://www.gnu.org/licenses/gpl.html
 */

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;

defined('_JEXEC') or die('Restricted access');

class JumiController extends BaseController
{
    /**
     * Method to display a view.
     *
     * @param   boolean         If true, the view output will be cached
     * @param   array           An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     *
     * @return  JController     This object to support chaining.
     * @since   1.5
     */
    public function display($cachable = false, $urlparams = false)
    {
        // Set the default view name and format from the Request.
        $jinput = Factory::getApplication()->input;
        $jinput->set('view', 'application');

        parent::display();

        return $this;
    }
}
