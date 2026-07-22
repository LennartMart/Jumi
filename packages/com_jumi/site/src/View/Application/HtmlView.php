<?php

/**
 * @package     Jumi
 * @subpackage  com_jumi
 *
 * @copyright   (C) 2026 LennartMart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Jumi\Component\Jumi\Site\View\Application;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * HTML View class for a single Jumi application (frontend).
 *
 * @since  4.0.0
 */
class HtmlView extends BaseHtmlView
{
    /**
     * The Jumi application item.
     *
     * @var    object|false
     * @since  4.0.0
     */
    protected $item;

    /**
     * The page parameters.
     *
     * @var    \Joomla\Registry\Registry
     * @since  4.0.0
     */
    protected $params;

    /**
     * Execute and display a template script.
     *
     * @param   string  $tpl  The name of the template file to parse.
     *
     * @return  void
     *
     * @since   4.0.0
     */
    public function display($tpl = null)
    {
        $app = Factory::getApplication();

        $this->item   = $this->get('Item');
        $this->params = $app->getParams();

        if (\count($errors = $this->get('Errors'))) {
            throw new GenericDataException(implode("\n", $errors), 500);
        }

        if ($this->item === false) {
            throw new GenericDataException(Text::_('COM_JUMI_ERROR_APPLICATION_NOT_FOUND'), 404);
        }

        $this->prepareDocument();

        parent::display($tpl);
    }

    /**
     * Prepare the document (page title).
     *
     * @return  void
     *
     * @since   4.0.0
     */
    protected function prepareDocument()
    {
        $app   = Factory::getApplication();
        $title = $this->item->title;

        if ($menu = $app->getMenu()->getActive()) {
            $menuTitle = $menu->getParams()->get('page_title', '');

            if ($menuTitle !== '') {
                $title = $menuTitle;
            }
        }

        if ($title !== '') {
            $this->getDocument()->setTitle($title);
        }
    }
}
