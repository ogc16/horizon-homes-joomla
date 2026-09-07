<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_estate
 *
 * @copyright   Copyright (C) Horizon Homes Real Estate.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Estate\Administrator\Controller;

use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Session\Session;
use Joomla\Component\Estate\Administrator\Model\CaptureModel;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Guided 3D-tour photo capture (guided capture workflow).
 *
 * Walks a realtor through a pre-defined sequence of 360� panorama positions,
 * accepts one panorama upload per shot slot and manages the tour state
 * (none -> drafting -> ready for review -> published).
 *
 * @since  1.0.0
 */
class CaptureController extends BaseController
{
    /**
     * Allowed panorama upload extensions.
     *
     * @var    string[]
     * @since  1.0.0
     */
    protected const EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    /**
     * Render the capture workspace for a listing.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function display($cachable = false, $urlparams = [])
    {
        $listingId = (int) $this->input->get('listing_id', 0, 'int');

        if ($listingId <= 0) {
            $this->app->enqueueMessage('Please choose a listing first.', 'warning');
            $this->setRedirect('index.php?option=com_estate&view=listings');

            return;
        }

        parent::display();
    }

    /**
     * Store one uploaded panorama for a shot slot.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function upload()
    {
        $listingId = (int) $this->input->get('listing_id', 0, 'int');
        $shotId    = (int) $this->input->get('shot_id', 0, 'int');

        if (!Session::checkToken() || $listingId <= 0 || $shotId <= 0) {
            $this->fail('Invalid request.', $listingId);

            return;
        }

        $model = $this->getModel('Capture');
        $shot  = $model->getShot($shotId);

        if (!$shot || (int) $shot->listing_id !== $listingId) {
            $this->fail('That shot slot does not belong to this listing.', $listingId);

            return;
        }

        $file = $this->input->files->get('file', null, 'array');

        if (!is_array($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            $this->fail('No file was uploaded.', $listingId);

            return;
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, self::EXTENSIONS, true)) {
            $this->fail('Only JPG, PNG and WebP panoramas are allowed.', $listingId);

            return;
        }

        if ((int) $file['size'] > 20 * 1024 * 1024) {
            $this->fail('The panorama exceeds the 20 MB limit.', $listingId);

            return;
        }

        $destinationDir = JPATH_SITE . '/images/estate/tours/' . $listingId;

        if (!Folder::create($destinationDir)) {
            $this->fail('Could not create the upload folder. Check write permissions on images/.', $listingId);

            return;
        }

        $relativeDir    = 'images/estate/tours/' . $listingId;
        $storedName     = 'shot-' . (int) $shot->slot . '-' . bin2hex(random_bytes(4)) . '.' . $extension;
        $relativePath   = $relativeDir . '/' . $storedName;
        $absoluteTarget = JPATH_SITE . '/' . $relativePath;

        if (!File::upload($file['tmp_name'], $absoluteTarget)) {
            $this->fail('Uploading the panorama failed.', $listingId);

            return;
        }

        // Drop any previously stored file for this slot.
        $model->deleteStoredFile($shot->image);

        $model->setShot((int) $shot->id, $relativePath);

        $this->app->enqueueMessage('Panorama saved for ' . htmlspecialchars($shot->room_label, ENT_QUOTES, 'UTF-8') . '.', 'message');
        $this->backToCapture($listingId);
    }

    /**
     * Remove an uploaded panorama from a shot slot.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function clear()
    {
        $listingId = (int) $this->input->get('listing_id', 0, 'int');
        $shotId    = (int) $this->input->get('shot_id', 0, 'int');

        if (!Session::checkToken() || $listingId <= 0 || $shotId <= 0) {
            $this->fail('Invalid request.', $listingId);

            return;
        }

        $model = $this->getModel('Capture');
        $shot  = $model->getShot($shotId);

        if (!$shot || (int) $shot->listing_id !== $listingId) {
            $this->fail('That shot slot does not belong to this listing.', $listingId);

            return;
        }

        $model->deleteStoredFile($shot->image);
        $model->clearShot((int) $shot->id);

        $this->app->enqueueMessage('Panorama removed.', 'message');
        $this->backToCapture($listingId);
    }

    /**
     * Persist the tour state and notes.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function saveState()
    {
        $tourId = (int) $this->input->get('tour_id', 0, 'int');
        $state  = (int) $this->input->get('state', 0, 'int');
        $notes  = (string) $this->input->get('notes', '', 'raw');
        $listingId = (int) $this->input->get('listing_id', 0, 'int');

        if (!Session::checkToken() || $tourId <= 0 || !array_key_exists($state, CaptureModel::STATE_LABELS)) {
            $this->fail('Invalid request.', $listingId);

            return;
        }

        $this->getModel('Capture')->saveTourState($tourId, $state, $notes);

        $this->app->enqueueMessage('Tour state updated.', 'message');
        $this->backToCapture($listingId);
    }

    /**
     * Return to the listings list.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function back()
    {
        $this->setRedirect('index.php?option=com_estate&view=listings');
    }

    /**
     * Redirect back to the capture workspace.
     *
     * @param   integer  $listingId  The listing id.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    protected function backToCapture($listingId)
    {
        $this->setRedirect('index.php?option=com_estate&view=capture&listing_id=' . (int) $listingId);
    }

    /**
     * Report a failure and return to the capture workspace.
     *
     * @param   string   $message    The error message.
     * @param   integer  $listingId  The listing id.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    protected function fail($message, $listingId)
    {
        $this->app->enqueueMessage($message, 'error');
        $this->backToCapture($listingId);
    }
}