<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_estate
 *
 * @copyright   Copyright (C) Horizon Homes Real Estate.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Estate\Administrator\View\Capture;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Component\Estate\Administrator\Model\CaptureModel;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Guided 3D capture workspace view.
 *
 * @since  1.0.0
 */
class HtmlView extends BaseHtmlView
{
    /**
     * The listing being captured.
     *
     * @var    object
     * @since  1.0.0
     */
    protected $listing;

    /**
     * The listing tour.
     *
     * @var    object
     * @since  1.0.0
     */
    protected $tour;

    /**
     * The tour shot slots.
     *
     * @var    object[]
     * @since  1.0.0
     */
    protected $shots;

    /**
     * The tour-state labels.
     *
     * @var    string[]
     * @since  1.0.0
     */
    protected $stateLabels;

    /**
     * Display the capture workspace.
     *
     * @param   string  $tpl  The name of the template file.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function display($tpl = null)
    {
        $app       = Factory::getApplication();
        $listingId = (int) $app->getInput()->getInt('listing_id', 0);

        if ($listingId <= 0) {
            $app->enqueueMessage('Please choose a listing first.', 'warning');
            $app->redirect(Route::_('index.php?option=com_estate&view=listings', false));

            return;
        }

        $model = Factory::getApplication()
            ->bootComponent('com_estate')
            ->getMVCFactory()
            ->createModel('Capture', 'administrator');

        $this->listing = $model->getListing($listingId);

        if (!$this->listing) {
            $app->enqueueMessage('Listing not found.', 'error');
            $app->redirect(Route::_('index.php?option=com_estate&view=listings', false));

            return;
        }

        $this->tour = $model->getOrCreateTour($listingId);
        $this->shots = $model->getShots((int) $this->tour->id);
        $this->stateLabels = CaptureModel::STATE_LABELS;

        $this->addToolbar();

        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    protected function addToolbar()
    {
        ToolbarHelper::title('3D Tour Capture - ' . ($this->listing->title ?? ''), 'camera');

        ToolbarHelper::back('JTOOLBAR_BACK', 'index.php?option=com_estate&view=listings');
        ToolbarHelper::link(
            'index.php?option=com_estate&task=listing.edit&id=' . (int) $this->listing->id,
            'Edit listing',
            'icon-edit'
        );

        ToolbarHelper::help('COM_ESTATE');
    }
}