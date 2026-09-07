<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_estate
 *
 * @copyright   Copyright (C) Horizon Homes Real Estate.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Estate\Administrator\View\Capture;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

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
        $this->listing     = $this->get('Listing');
        $this->tour        = $this->get('Tour');
        $this->shots       = $this->get('Shots');
        $this->stateLabels = $this->get('StateLabels');

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