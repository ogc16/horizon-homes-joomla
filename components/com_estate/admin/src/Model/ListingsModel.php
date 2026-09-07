<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_estate
 *
 * @copyright   Copyright (C) Horizon Homes Real Estate.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Estate\Administrator\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Admin list model for listing records.
 *
 * @since  1.0.0
 */
class ListingsModel extends ListModel
{
    /**
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     *
     * @since   1.0.0
     */
    public function __construct($config = [])
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [
                'id', 'a.id',
                'title', 'a.title',
                'status', 'a.status',
                'city', 'a.city',
                'published', 'a.published',
                'agent_id', 'a.agent_id',
            ];
        }

        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * @param   string  $ordering   An optional ordering field.
     * @param   string  $direction  An optional direction (asc|desc).
     *
     * @return  void
     *
     * @since   1.0.0
     */
    protected function populateState($ordering = 'a.id', $direction = 'DESC')
    {
        parent::populateState($ordering, $direction);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return  \Joomla\Database\DatabaseQuery
     *
     * @since   1.0.0
     */
    protected function getListQuery()
    {
        $db    = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select('a.*')
            ->from($db->quoteName('#__estate_listings', 'a'))
            ->join('LEFT', $db->quoteName('#__estate_agents', 'ag') . ' ON (' .
                $db->quoteName('ag.id') . ' = ' . $db->quoteName('a.agent_id') . ')')
            ->join('LEFT', $db->quoteName('#__estate_tours', 't') . ' ON (' .
                $db->quoteName('t.listing_id') . ' = ' . $db->quoteName('a.id') . ')')
            ->select('ag.name AS agent_name')
            ->select('t.id AS tour_id, t.state AS tour_state');

        // Filter by published state.
        $published = $this->getState('filter.published');

        if (is_numeric($published)) {
            $query->where($db->quoteName('a.published') . ' = ' . (int) $published);
        } elseif ($published === '') {
            $query->where('(' . $db->quoteName('a.published') . ' IN (0, 1))');
        }

        // Filter by search term.
        $search = trim($this->getState('filter.search') ?? '');

        if ($search !== '') {
            $query->where($db->quoteName('a.title') . ' LIKE ' . $db->quote('%' . $search . '%'));
        }

        // Filter by agent.
        $agent = $this->getState('filter.agent_id');

        if (is_numeric($agent)) {
            $query->where($db->quoteName('a.agent_id') . ' = ' . (int) $agent);
        }

        // Ordering.
        $orderCol  = $this->getState('list.ordering', 'a.id');
        $orderDirn = $this->getState('list.direction', 'DESC');
        $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

        return $query;
    }
}