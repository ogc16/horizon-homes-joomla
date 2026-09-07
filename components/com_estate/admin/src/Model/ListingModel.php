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
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\Database\ParameterType;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Admin data model for a single listing record.
 *
 * @since  1.0.0
 */
class ListingModel extends BaseDatabaseModel
{
    /**
     * Load a single listing by id.
     *
     * @param   integer  $id  The listing id.
     *
     * @return  object|null  The listing row or null.
     *
     * @since   1.0.0
     */
    public function getItem($id = 0)
    {
        $id = (int) $id;

        if ($id <= 0) {
            return (object) [
                'id'             => 0,
                'title'          => '',
                'alias'          => '',
                'agent_id'       => 1,
                'property_type'  => 'house',
                'status'         => 'available',
                'sale_or_rent'   => 'sale',
                'price'          => 0,
                'currency'       => 'KSH',
                'off_plan'       => 0,
                'developer'      => '',
                'completion_date' => null,
                'payment_plan'   => '',
                'bedrooms'       => 0,
                'bathrooms'      => 0,
                'area_sqft'      => 0,
                'address'        => '',
                'city'           => '',
                'description'    => '',
                'featured'       => 0,
                'published'      => 1,
                'main_image'     => '',
                'gallery_json'   => '',
                'ordering'       => 0,
                'agent_name'     => '',
            ];
        }

        $db    = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('a.*, ag.name AS agent_name')
            ->from($db->quoteName('#__estate_listings', 'a'))
            ->join('LEFT', $db->quoteName('#__estate_agents', 'ag') . ' ON (' .
                $db->quoteName('ag.id') . ' = ' . $db->quoteName('a.agent_id') . ')')
            ->where($db->quoteName('a.id') . ' = :id')
            ->bind(':id', $id, ParameterType::INTEGER)
            ->setLimit(1);

        $db->setQuery($query);

        return $db->loadObject();
    }

    /**
     * Load the list of active agents.
     *
     * @return  object[]  Active agent rows.
     *
     * @since   1.0.0
     */
    public function getAgents()
    {
        $db    = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__estate_agents'))
            ->where($db->quoteName('is_active') . ' = 1')
            ->order($db->quoteName('ordering') . ' ASC, ' . $db->quoteName('name') . ' ASC');

        $db->setQuery($query);

        return $db->loadObjectList();
    }

    /**
     * Check whether an alias is already used by another listing.
     *
     * @param   string   $alias  The alias.
     * @param   integer  $id     The listing id to exclude.
     *
     * @return  boolean  True when the alias is in use elsewhere.
     *
     * @since   1.0.0
     */
    public function aliasInUse($alias, $id = 0)
    {
        if ($alias === '') {
            return false;
        }

        $db    = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__estate_listings'))
            ->where($db->quoteName('alias') . ' = :alias')
            ->bind(':alias', $alias);

        if ($id) {
            $query->where($db->quoteName('id') . ' != :id')
                ->bind(':id', (int) $id, ParameterType::INTEGER);
        }

        $db->setQuery($query);

        return (int) $db->loadResult() > 0;
    }

    /**
     * Return a unique alias by suffixing the given alias.
     *
     * @param   string   $alias  The base alias.
     * @param   integer  $id     The listing id to exclude.
     *
     * @return  string   A free alias.
     *
     * @since   1.0.0
     */
    public function nextFreeAlias($alias, $id = 0)
    {
        $count = 2;

        while ($this->aliasInUse($alias . '-' . $count, $id)) {
            $count++;
        }

        return $alias . '-' . $count;
    }

    /**
     * Store a listing (insert or update).
     *
     * @param   array  $data  The listing fields.
     *
     * @return  integer  The listing id.
     *
     * @since   1.0.0
     */
    public function save($data)
    {
        $db      = $this->getDatabase();
        $columns = [
            'title', 'alias', 'agent_id', 'property_type', 'status', 'sale_or_rent',
            'price', 'currency', 'off_plan', 'developer', 'completion_date', 'payment_plan',
            'bedrooms', 'bathrooms', 'area_sqft', 'address', 'city', 'description',
            'featured', 'main_image', 'gallery_json', 'published', 'ordering',
        ];

        $row = [];

        foreach ($columns as $column) {
            $row[$column] = $data[$column] ?? (in_array($column, ['bedrooms', 'bathrooms', 'area_sqft', 'agent_id', 'ordering'], true) ? 0 : '');
        }

        if ($row['completion_date'] === '') {
            $row['completion_date'] = null;
        }

        $data['id'] = (int) ($data['id'] ?? 0);

        if ($data['id'] > 0) {
            $query = $db->getQuery(true)
                ->update($db->quoteName('#__estate_listings'))
                ->where($db->quoteName('id') . ' = ' . $data['id']);

            foreach ($row as $column => $value) {
                $query->set($db->quoteName($column) . ' = ' . $this->quoteValue($db, $value));
            }

            $db->setQuery($query)->execute();

            return $data['id'];
        }

        $query = $db->getQuery(true)
            ->insert($db->quoteName('#__estate_listings'))
            ->columns($db->quoteName(array_keys($row)));

        foreach (array_values($row) as $value) {
            $query->values($this->quoteValue($db, $value));
        }

        $db->setQuery($query)->execute();

        return (int) $db->insertid();
    }

    /**
     * Quote a value safely for a query.
     *
     * @param   \Joomla\Database\DatabaseDriver  $db     The database driver.
     * @param   mixed                            $value  The value to quote.
     *
     * @return  string  A quoted SQL literal (or NULL).
     *
     * @since   1.0.0
     */
    protected function quoteValue($db, $value)
    {
        if ($value === null) {
            return 'NULL';
        }

        return $db->quote($value);
    }
}