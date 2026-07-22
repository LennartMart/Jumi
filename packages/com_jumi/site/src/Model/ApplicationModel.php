<?php

/**
 * @package     Jumi
 * @subpackage  com_jumi
 *
 * @copyright   (C) 2026 LennartMart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Jumi\Component\Jumi\Site\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ItemModel;
use Joomla\Database\ParameterType;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Jumi application item model (frontend).
 *
 * @since  4.0.0
 */
class ApplicationModel extends ItemModel
{
    /**
     * Model context string.
     *
     * @var    string
     * @since  4.0.0
     */
    protected $_context = 'com_jumi.application';

    /**
     * Auto-populate the model state.
     *
     * @return  void
     *
     * @since   4.0.0
     */
    protected function populateState()
    {
        $app = Factory::getApplication();

        // The menu item may provide the file id.
        $pk = $app->getInput()->getInt('fileid');

        if (!$pk && ($menu = $app->getMenu()->getActive())) {
            $pk = (int) $menu->getParams()->get('fileid');
        }

        $this->setState('application.id', $pk);
    }

    /**
     * Get a single Jumi application.
     *
     * @param   integer  $pk  The id of the application.
     *
     * @return  object|false  Object on success, false on failure.
     *
     * @since   4.0.0
     */
    public function getItem($pk = null)
    {
        $pk = (int) ($pk ?: $this->getState('application.id'));

        if (!$pk) {
            return false;
        }

        if (!isset($this->_item)) {
            $this->_item = [];
        }

        if (!isset($this->_item[$pk])) {
            $db    = $this->getDatabase();
            $query = $db->getQuery(true)
                ->select($db->quoteName(['id', 'title', 'alias', 'path', 'custom_script', 'access', 'published']))
                ->from($db->quoteName('#__jumi'))
                ->where($db->quoteName('id') . ' = :id')
                ->where($db->quoteName('published') . ' = 1')
                ->bind(':id', $pk, ParameterType::INTEGER);

            $db->setQuery($query);

            $this->_item[$pk] = $db->loadObject() ?: false;
        }

        return $this->_item[$pk];
    }
}
