<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_estate
 *
 * @copyright   Copyright (C) Horizon Homes Real Estate.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Estate\Administrator\View\Listing;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Admin single-listing editor view.
 *
 * @since  1.0.0
 */
class HtmlView extends BaseHtmlView
{
    /**
     * The listing record being edited.
     *
     * @var    object
     * @since  1.0.0
     */
    protected $item;

    /**
     * Active agent rows.
     *
     * @var    array
     * @since  1.0.0
     */
    protected $agents;

    /**
     * Display the editor.
     *
     * @param   string  $tpl  The name of the template file.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function display($tpl = null)
    {
        $this->item   = $this->get('Item');
        $this->agents = $this->get('Agents');

        $this->addToolbar();

        parent::display($tpl);
    }

    /**
     * Add the page title, toolbar buttons and capture shortcut.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    protected function addToolbar()
    {
        $isNew = (int) $this->item->id === 0;

        ToolbarHelper::title($isNew ? 'New Listing' : 'Edit Listing', 'tree-2');

        ToolbarHelper::apply('listing.apply');
        ToolbarHelper::save('listing.save');
        ToolbarHelper::cancel('listing.cancel');

        if (!$isNew) {
            ToolbarHelper::link(
                'index.php?option=com_estate&view=capture&listing_id=' . (int) $this->item->id,
                '3D Tour Capture',
                'icon-camera'
            );
        }

        ToolbarHelper::help('COM_ESTATE');
    }
}