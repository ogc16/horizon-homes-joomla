<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_estate
 *
 * @copyright   Copyright (C) Horizon Homes Real Estate.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;

/**
 * Admin model for listing records.
 *
 * Represents the managing portion of the Business Logic Tier.
 *
 * @since  1.0.0
 */
class EstateModelListings extends ListModel
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  Configuration array.
	 *
	 * @since   1.0.0
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
				'status', 'a.status',
				'city', 'a.city',
				'state', 'a.state',
				'agent_id', 'a.agent_id',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Build the SQL query.
	 *
	 * @return  QueryInterface  The query object.
	 *
	 * @since   1.0.0
	 */
	protected function getListQuery()
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select($this->getState('list.select', 'a.*'))
			->from($db->quoteName('#__estate_listings', 'a'))
			->join('LEFT', $db->quoteName('#__estate_agents', 'ag') . ' ON (' .
				$db->quoteName('ag.id') . ' = ' . $db->quoteName('a.agent_id') . ')')
			->select('ag.name AS agent_name');

		// Publish state filter
		$published = $this->getState('filter.published');

		if (is_numeric($published))
		{
			$query->where($db->quoteName('a.published') . ' = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(' . $db->quoteName('a.published') . ' IN (0, 1))');
		}

		// Search filter
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			$query->where($db->quoteName('a.title') . ' LIKE ' . $db->quote('%' . $search . '%'));
		}

		// Agent filter
		$agent = $this->getState('filter.agent_id');

		if (is_numeric($agent))
		{
			$query->where($db->quoteName('a.agent_id') . ' = ' . (int) $agent);
		}

		// Ordering
		$orderCol  = $this->state->get('list.ordering', 'a.id');
		$orderDirn = $this->state->get('list.direction', 'DESC');
		$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

		return $query;
	}
}
