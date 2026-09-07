<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_estate
 *
 * @copyright   Copyright (C) Horizon Homes Real Estate.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Factory;

/**
 * Listings view (site).
 *
 * This is the Presentation Tier — it only formats data handed to it by
 * the model (Business Logic Tier). It performs no business rules.
 *
 * @since  1.0.0
 */
class EstateViewListings extends HtmlView
{
	/**
	 * Display the view.
	 *
	 * @param   string  $tpl  The name of the template file.
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function display($tpl = null)
	{
		$this->state   = $this->get('State') ?: null;
		$this->params  = Factory::getApplication()->getParams('com_estate');

		parent::display($tpl);
	}

	/**
	 * Format a price nicely (KES).
	 *
	 * @param   mixed  $price  Numeric price.
	 *
	 * @return  string  Formatted price string.
	 *
	 * @since   1.0.0
	 */
	public static function formatPrice($price)
	{
		$number = (float) $price;

		if ($number >= 1000000)
		{
			$value = $number / 1000000;

			return 'KES ' . number_format($value, $value == floor($value) ? 0 : 2) . 'M';
		}

		if ($number >= 1000)
		{
			return 'KES ' . number_format($number, 0);
		}

		return 'KES ' . number_format($number, 2);
	}
}
