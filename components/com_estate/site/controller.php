<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_estate
 *
 * @copyright   Copyright (C) Horizon Homes Real Estate.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;

/**
 * Estate component site controller.
 *
 * The single controller delegates named tasks (e.g. "listings.display",
 * "booking.save") to the appropriate sub-controller following the
 * MVC pattern of the 3-tier architecture.
 *
 * @since 1.0.0
 */
class EstateController extends BaseController
{
	/**
	 * The default view to render when arriving with no task.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $default_view = 'listings';
}
