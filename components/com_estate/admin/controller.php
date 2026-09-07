<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_estate
 *
 * @copyright   Copyright (C) Horizon Homes Real Estate.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;

/**
 * Estate component admin controller.
 *
 * @since  1.0.0
 */
class EstateController extends BaseController
{
	/**
	 * The default task.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $default_view = 'listings';

	/**
	 * Method to display the dashboard.
	 *
	 * @param   boolean  $cachable   Whether the view is cacheable.
	 * @param   array    $urlparams  Allow list of URL parameters.
	 *
	 * @return  BaseController|boolean
	 *
	 * @since   1.0.0
	 */
	public function display($cachable = false, $urlparams = array())
	{
		Factory::getApplication()->getLanguage()->load('com_estate', JPATH_ADMINISTRATOR . '/components/com_estate/admin', null, true);

		return parent::display($cachable, $urlparams);
	}
}
