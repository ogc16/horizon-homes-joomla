<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_estate
 *
 * @copyright   Copyright (C) Horizon Homes Real Estate.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Estate\Administrator\Controller;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Admin listings bulk-action controller.
 *
 * @since  1.0.0
 */
class ListingsController extends BaseController
{
    /**
     * Publish the selected listings.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function publish()
    {
        $this->updateListings(1);
    }

    /**
     * Unpublish the selected listings.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function unpublish()
    {
        $this->updateListings(0);
    }

    /**
     * Trash (delete) the selected listings.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function trash()
    {
        $app = Factory::getApplication();
        $db  = Factory::getApplication()->getDatabase();
        $pks = (array) $this->input->get('cid', [], 'array');

        if (!$pks) {
            $app->enqueueMessage('No items selected.', 'warning');
            $this->setRedirect('index.php?option=com_estate&view=listings');

            return;
        }

        $query = $db->getQuery(true)
            ->delete($db->quoteName('#__estate_listings'))
            ->whereIn($db->quoteName('id'), array_map('intval', $pks));

        $db->setQuery($query)->execute();

        $app->enqueueMessage('Listings deleted.');
        $this->setRedirect('index.php?option=com_estate&view=listings');
    }

    /**
     * Set the published state of the selected listings.
     *
     * @param   integer  $state  The new published state (0 or 1).
     *
     * @return  void
     *
     * @since   1.0.0
     */
    protected function updateListings($state)
    {
        $app = Factory::getApplication();
        $db  = Factory::getApplication()->getDatabase();
        $pks = (array) $this->input->get('cid', [], 'array');

        if (!$pks) {
            $app->enqueueMessage('No items selected.', 'warning');
            $this->setRedirect('index.php?option=com_estate&view=listings');

            return;
        }

        $query = $db->getQuery(true)
            ->update($db->quoteName('#__estate_listings'))
            ->set($db->quoteName('published') . ' = ' . (int) $state)
            ->whereIn($db->quoteName('id'), array_map('intval', $pks));

        $db->setQuery($query)->execute();

        $app->enqueueMessage((int) $state ? 'Listings published.' : 'Listings unpublished.');
        $this->setRedirect('index.php?option=com_estate&view=listings');
    }
}