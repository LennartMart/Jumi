<?php

/**
 * @package     Jumi
 * @subpackage  com_jumi
 *
 * @copyright   (C) 2026 LennartMart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Jumi\Component\Jumi\Administrator\View\Application;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * View to edit a single Jumi application.
 *
 * @since  4.0.0
 */
class HtmlView extends BaseHtmlView
{
    /**
     * The Form object
     *
     * @var    \Joomla\CMS\Form\Form
     * @since  4.0.0
     */
    protected $form;

    /**
     * The active item
     *
     * @var    object
     * @since  4.0.0
     */
    protected $item;

    /**
     * The model state
     *
     * @var    \Joomla\Registry\Registry
     * @since  4.0.0
     */
    protected $state;

    /**
     * Display the view.
     *
     * @param   string  $tpl  The name of the template file to parse.
     *
     * @return  void
     *
     * @since   4.0.0
     */
    public function display($tpl = null)
    {
        $this->form  = $this->get('Form');
        $this->item  = $this->get('Item');
        $this->state = $this->get('State');

        if (\count($errors = $this->get('Errors'))) {
            throw new GenericDataException(implode("\n", $errors), 500);
        }

        $this->addToolbar();

        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return  void
     *
     * @since   4.0.0
     */
    protected function addToolbar()
    {
        $this->getDocument()->getWebAssetManager()->useScript('keepalive')->useScript('form.validate');

        $isNew = ($this->item->id == 0);

        ToolbarHelper::title(
            $isNew ? Text::_('COM_JUMI_MANAGER_APPLICATION_NEW') : Text::_('COM_JUMI_MANAGER_APPLICATION_EDIT'),
            'code jumi'
        );

        $toolbar = Toolbar::getInstance('toolbar');

        $toolbar->apply('application.apply');

        $saveGroup = $toolbar->dropdownButton('save-group');

        $saveGroup->configure(
            function (Toolbar $childBar) {
                $childBar->save('application.save');
                $childBar->save2new('application.save2new');
            }
        );

        if ($isNew) {
            $toolbar->cancel('application.cancel', 'JTOOLBAR_CANCEL');
        } else {
            $toolbar->cancel('application.cancel', 'JTOOLBAR_CLOSE');
        }
    }
}
