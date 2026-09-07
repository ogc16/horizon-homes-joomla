<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_estate
 *
 * @copyright   Copyright (C) Horizon Homes Real Estate.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\BaseDatabaseModel;

/**
 * Models for com_estate
 *
 * @since  1.0.0
 */
class EstateModelListings extends BaseDatabaseModel
{
	/**
	 * Retrieve all published listings (optionally filtered).
	 *
	 * @param   array  $filters  Optional filters (city, type, sale_or_rent).
	 *
	 * @return  object[]  List of listing records.
	 *
	 * @since   1.0.0
	 */
	public function getListings(array $filters = array())
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('a.*, ag.name AS agent_name, ag.phone AS agent_phone, ag.email AS agent_email')
			->from($db->quoteName('#__estate_listings', 'a'))
			->join('LEFT', $db->quoteName('#__estate_agents', 'ag') . ' ON (' .
				$db->quoteName('ag.id') . ' = ' . $db->quoteName('a.agent_id') . ')')
			->where($db->quoteName('a.published') . ' = 1')
			->where($db->quoteName('a.access') . ' IN (' . implode(',', $this->getAccessLevels()) . ')')
			->order($db->quoteName('a.featured') . ' DESC, ' . $db->quoteName('a.ordering') . ' ASC');

		// Apply optional filters from the request.
		$allowed = array('city', 'property_type', 'sale_or_rent');
		foreach ($allowed as $field)
		{
			if (!empty($filters[$field]))
			{
				$query->where($db->quoteName('a.' . $field) . ' = ' . $db->quote($filters[$field]));
			}
		}

		if (!empty($filters['q']))
		{
			$search = '%' . $filters['q'] . '%';
			$query->where(
				'(' . $db->quoteName('a.title') . ' LIKE ' . $db->quote($search)
				. ' OR ' . $db->quoteName('a.city') . ' LIKE ' . $db->quote($search)
				. ' OR ' . $db->quoteName('a.address') . ' LIKE ' . $db->quote($search) . ')'
			);
		}

		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 * Fetch a single listing by id or alias.
	 *
	 * @param   string  $alias  The listing alias (slug).
	 *
	 * @return  object|null  The listing record or null.
	 *
	 * @since   1.0.0
	 */
	public function getListing($alias)
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('a.*, ag.name AS agent_name, ag.phone AS agent_phone, ag.email AS agent_email, ag.bio AS agent_bio')
			->from($db->quoteName('#__estate_listings', 'a'))
			->join('LEFT', $db->quoteName('#__estate_agents', 'ag') . ' ON (' .
				$db->quoteName('ag.id') . ' = ' . $db->quoteName('a.agent_id') . ')')
			->where($db->quoteName('a.alias') . ' = ' . $db->quote($alias))
			->where($db->quoteName('a.published') . ' = 1')
			->setLimit(1);

		$db->setQuery($query);

		return $db->loadObject();
	}

	/**
	 * Helper to get a gallery array from the stored JSON.
	 *
	 * @param   object  $item  A listing record.
	 *
	 * @return  array  List of image paths.
	 *
	 * @since   1.0.0
	 */
	public function getGallery($item)
	{
		$gallery = array();

		if (!empty($item->gallery_json))
		{
			$gallery = json_decode($item->gallery_json, true);
		}

		if (!is_array($gallery))
		{
			$gallery = array();
		}

		if (!empty($item->main_image))
		{
			array_unshift($gallery, $item->main_image);
		}

		return $gallery;
	}

	/**
	 * Retrieve the access levels the current visitor can view.
	 *
	 * @return  int[]
	 *
	 * @since   1.0.0
	 */
	protected function getAccessLevels()
	{
		$user = \Joomla\CMS\Factory::getUser();

		return $user->getAuthorisedViewLevels();
	}
}
