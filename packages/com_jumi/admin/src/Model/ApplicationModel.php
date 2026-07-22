<?php

/**
 * @package     Jumi
 * @subpackage  com_jumi
 *
 * @copyright   (C) 2026 LennartMart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Jumi\Component\Jumi\Administrator\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Item Model for a single Jumi application.
 *
 * @since  4.0.0
 */
class ApplicationModel extends AdminModel
{
    /**
     * The type alias for this content type.
     *
     * @var    string
     * @since  4.0.0
     */
    public $typeAlias = 'com_jumi.application';

    /**
     * Method to get the record form.
     *
     * @param   array    $data      Data for the form.
     * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
     *
     * @return  \Joomla\CMS\Form\Form|boolean  A Form object on success, false on failure
     *
     * @since   4.0.0
     */
    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm(
            'com_jumi.application',
            'application',
            ['control' => 'jform', 'load_data' => $loadData]
        );

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  mixed  The data for the form.
     *
     * @since   4.0.0
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = Factory::getApplication()->getUserState('com_jumi.edit.application.data', []);

        if (empty($data)) {
            $data = $this->getItem();
        }

        $this->preprocessData('com_jumi.application', $data);

        return $data;
    }
}
