<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_estate
 *
 * @copyright   Copyright (C) Horizon Homes Real Estate.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Factory;

/**
 * Admin listings view.
 *
 * @since  1.0.0
 */
class EstateViewListings extends HtmlView
{
	/**
	 * Display the view.
	 *
	 * @param   string  $tpl  Template name.
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function display($tpl = null)
	{
		$this->items      = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state      = $this->get('State');

		$this->addToolbar();

		parent::display($tpl);
	}

	/**
	 * Add the toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	protected function addToolbar()
	{
		ToolbarHelper::title('Property Listings', 'tree-2');
		ToolbarHelper::custom('listings.add', 'new.png', 'new_f2.png', 'New', false);
		ToolbarHelper::custom('listings.publish', 'publish.png', 'publish_f2.png', 'Publish', true);
		ToolbarHelper::custom('listings.unpublish', 'unpublish.png', 'unpublish_f2.png', 'Unpublish', true);
		ToolbarHelper::trash('listings.trash');
	}
}
