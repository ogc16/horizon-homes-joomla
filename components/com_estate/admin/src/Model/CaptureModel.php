<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_estate
 *
 * @copyright   Copyright (C) Horizon Homes Real Estate.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Estate\Administrator\Model;

use Joomla\CMS\Filesystem\File;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\Database\ParameterType;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Admin data model backing the guided 3D capture workspace.
 *
 * @since  1.0.0
 */
class CaptureModel extends BaseDatabaseModel
{
    /**
     * The default guided capture sequence (room, position key, instructions).
     * Inserted when a tour is first created so the realtor knows the plan.
     *
     * @var    array
     * @since  1.0.0
     */
    protected const DEFAULT_SHOTS = [
        ['slot' => 1, 'room_label' => 'Entrance & Lobby', 'position_key' => 'A1', 'instructions' => 'Stand at the entrance door, camera at 1.5 m (eye level), and shoot a full 360 panorama facing INTO the space before stepping in. Keep the tripod centre on the door edge.'],
        ['slot' => 2, 'room_label' => 'Living / Lounge', 'position_key' => 'A2', 'instructions' => 'Move to the centre of the living area. Overlap at least 30% with the previous shot, keep the nodal point level and leave 10 cm clearance from every wall.'],
        ['slot' => 3, 'room_label' => 'Kitchen', 'position_key' => 'B1', 'instructions' => 'Place the camera above the counter line (so counters do not block the view), steer clear of mirrors and shiny appliances, and capture the full run of cabinets.'],
        ['slot' => 4, 'room_label' => 'Dining Area', 'position_key' => 'B2', 'instructions' => 'Shoot from the centre of the table at 1.5 m. Remove clutter from the tabletop first so the space looks spacious.'],
        ['slot' => 5, 'room_label' => 'Bedroom', 'position_key' => 'C1', 'instructions' => 'Shoot from the foot of the bed at eye level so the bed faces the camera. Extra bed linen, towels and half-open wardrobes read poorly in 360; tidy them before shooting.'],
        ['slot' => 6, 'room_label' => 'Bathroom', 'position_key' => 'C2', 'instructions' => 'Stand inside the doorway so the mirror is angled away from the lens. A damp towel on the floor or used toiletries will be visible in every direction - clear the room.'],
        ['slot' => 7, 'room_label' => 'Outdoor / Balcony', 'position_key' => 'D1', 'instructions' => 'Aim the camera level with the railing / balustrade and stitch the view back into the last indoor shot so the tour walks seamlessly outside.'],
    ];

    /**
     * Load the listing being captured.
     *
     * @param   integer  $id  The listing id.
     *
     * @return  object|null
     *
     * @since   1.0.0
     */
    public function getListing($id)
    {
        $db    = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__estate_listings'))
            ->where($db->quoteName('id') . ' = :id')
            ->bind(':id', (int) $id, ParameterType::INTEGER)
            ->setLimit(1);

        $db->setQuery($query);

        return $db->loadObject();
    }

    /**
     * Return the listing tour, creating it (with the default shot plan) on first visit.
     *
     * @param   integer  $listingId  The listing id.
     *
     * @return  object  The tour row.
     *
     * @since   1.0.0
     */
    public function getOrCreateTour($listingId)
    {
        $listingId = (int) $listingId;
        $db        = $this->getDatabase();

        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__estate_tours'))
            ->where($db->quoteName('listing_id') . ' = :listing')
            ->bind(':listing', $listingId, ParameterType::INTEGER);

        $db->setQuery($query);
        $tour = $db->loadObject();

        if ($tour) {
            return $tour;
        }

        $query = $db->getQuery(true)
            ->insert($db->quoteName('#__estate_tours'))
            ->columns($db->quoteName(['listing_id', 'state', 'notes']))
            ->values(implode(', ', [$listingId, 1, $db->quote('Guided 360 capture in progress.')]));

        $db->setQuery($query)->execute();
        $tourId = (int) $db->insertid();

        foreach (self::DEFAULT_SHOTS as $index => $shot) {
            $query = $db->getQuery(true)
                ->insert($db->quoteName('#__estate_tour_shots'))
                ->columns($db->quoteName([
                    'tour_id', 'listing_id', 'slot', 'room_label', 'position_key',
                    'instructions', 'image', 'published', 'ordering',
                ]))
                ->values(implode(', ', [
                    $tourId,
                    $listingId,
                    (int) $shot['slot'],
                    $db->quote($shot['room_label']),
                    $db->quote($shot['position_key']),
                    $db->quote($shot['instructions']),
                    $db->quote(''),
                    1,
                    $index + 1,
                ]));

            $db->setQuery($query)->execute();
        }

        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__estate_tours'))
            ->where($db->quoteName('id') . ' = ' . $tourId);

        $db->setQuery($query);

        return $db->loadObject();
    }

    /**
     * Load the shots for a tour in capture order.
     *
     * @param   integer  $tourId  The tour id.
     *
     * @return  object[]
     *
     * @since   1.0.0
     */
    public function getShots($tourId)
    {
        $db    = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__estate_tour_shots'))
            ->where($db->quoteName('tour_id') . ' = :tour')
            ->bind(':tour', (int) $tourId, ParameterType::INTEGER)
            ->order($db->quoteName('ordering') . ' ASC');

        $db->setQuery($query);

        return $db->loadObjectList() ?: [];
    }

    /**
     * Load a single shot by id.
     *
     * @param   integer  $id  The shot id.
     *
     * @return  object|null
     *
     * @since   1.0.0
     */
    public function getShot($id)
    {
        $db    = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__estate_tour_shots'))
            ->where($db->quoteName('id') . ' = :id')
            ->bind(':id', (int) $id, ParameterType::INTEGER)
            ->setLimit(1);

        $db->setQuery($query);

        return $db->loadObject();
    }

    /**
     * Attach an uploaded panorama to a shot.
     *
     * @param   integer  $shotId   The shot id.
     * @param   string   $image    The stored relative image path.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function setShot($shotId, $image)
    {
        $db    = $this->getDatabase();
        $query = $db->getQuery(true)
            ->update($db->quoteName('#__estate_tour_shots'))
            ->set($db->quoteName('image') . ' = ' . $db->quote($image))
            ->where($db->quoteName('id') . ' = ' . (int) $shotId);

        $db->setQuery($query)->execute();
    }

    /**
     * Empty the image of a shot.
     *
     * @param   integer  $shotId  The shot id.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function clearShot($shotId)
    {
        $db    = $this->getDatabase();
        $query = $db->getQuery(true)
            ->update($db->quoteName('#__estate_tour_shots'))
            ->set($db->quoteName('image') . ' = ' . $db->quote(''))
            ->where($db->quoteName('id') . ' = ' . (int) $shotId);

        $db->setQuery($query)->execute();
    }

    /**
     * Persist the tour state and notes.
     *
     * @param   integer  $tourId  The tour id.
     * @param   integer  $state   The new state (0..3).
     * @param   string   $notes   The notes.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function saveTourState($tourId, $state, $notes)
    {
        $db    = $this->getDatabase();
        $query = $db->getQuery(true)
            ->update($db->quoteName('#__estate_tours'))
            ->set($db->quoteName('state') . ' = ' . (int) $state)
            ->set($db->quoteName('notes') . ' = ' . $db->quote($notes))
            ->where($db->quoteName('id') . ' = ' . (int) $tourId);

        $db->setQuery($query)->execute();
    }

    /**
     * Remove a stored panorama file when it lives under the site images folder.
     *
     * @param   string  $path  The stored relative path.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function deleteStoredFile($path)
    {
        $path = trim((string) $path);

        if ($path === '' || stripos($path, 'images/') !== 0) {
            return;
        }

        $absolute = JPATH_SITE . '/' . $path;

        if (file_exists($absolute) && is_file($absolute)) {
            File::delete($absolute);
        }
    }
}