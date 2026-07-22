<?php

/**
 * @package     Jumi
 * @subpackage  com_jumi
 *
 * @copyright   (C) 2026 LennartMart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Jumi\Component\Jumi\Site\Service;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Routing class for com_jumi.
 *
 * Replaces the legacy router.php and the standalone Jumi Router system plugin.
 *
 * @since  4.0.0
 */
class Router extends RouterView
{
    /**
     * The database driver.
     *
     * @var    DatabaseInterface
     * @since  4.0.0
     */
    private $db;

    /**
     * Jumi Component router constructor.
     *
     * @param   SiteApplication    $app   The application object
     * @param   AbstractMenu       $menu  The menu object to work with
     * @param   DatabaseInterface  $db    The database object
     *
     * @since   4.0.0
     */
    public function __construct(SiteApplication $app, AbstractMenu $menu, DatabaseInterface $db)
    {
        $this->db = $db;

        $application = new RouterViewConfiguration('application');
        $application->setKey('fileid');
        $this->registerView($application);

        parent::__construct($app, $menu);

        $this->attachRule(new MenuRules($this));
        $this->attachRule(new StandardRules($this));
        $this->attachRule(new NomenuRules($this));
    }

    /**
     * Method to get the segment(s) for an application.
     *
     * @param   string  $id     ID of the application to retrieve the segments for
     * @param   array   $query  The request that is built right now
     *
     * @return  array  The segments of this item
     *
     * @since   4.0.0
     */
    public function getApplicationSegment($id, $query)
    {
        if (!strpos($id, ':')) {
            $id      = (int) $id;
            $dbquery = $this->db->getQuery(true)
                ->select($this->db->quoteName('alias'))
                ->from($this->db->quoteName('#__jumi'))
                ->where($this->db->quoteName('id') . ' = :id')
                ->bind(':id', $id, ParameterType::INTEGER);
            $this->db->setQuery($dbquery);

            $id .= ':' . $this->db->loadResult();
        }

        [$void, $segment] = explode(':', $id, 2);

        return [(int) $id => $segment];
    }

    /**
     * Method to get the id for an application.
     *
     * @param   string  $segment  Segment of the application to retrieve the ID for
     * @param   array   $query    The request that is parsed right now
     *
     * @return  mixed  The id of this item or false
     *
     * @since   4.0.0
     */
    public function getApplicationId($segment, $query)
    {
        $dbquery = $this->db->getQuery(true)
            ->select($this->db->quoteName('id'))
            ->from($this->db->quoteName('#__jumi'))
            ->where($this->db->quoteName('alias') . ' = :segment')
            ->bind(':segment', $segment);
        $this->db->setQuery($dbquery);

        return (int) $this->db->loadResult();
    }
}
