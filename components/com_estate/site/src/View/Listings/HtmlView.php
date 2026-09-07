<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_estate
 *
 * @copyright   Copyright (C) Horizon Homes Real Estate.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Estate\Site\View\Listings;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Listings view (site).
 *
 * The Presentation Tier — formats data received from the model only.
 * Performs no business rules.
 *
 * @since  1.0.0
 */
class HtmlView extends BaseHtmlView
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
        $this->params = Factory::getApplication()->getParams('com_estate');

        parent::display($tpl);
    }

    /**
     * Format a price nicely (KSh or USD).
     *
     * @param   mixed   $price     Numeric price.
     * @param   string  $currency  Currency code: KSH or USD.
     *
     * @return  string  Formatted price string.
     *
     * @since   1.0.0
     */
    public static function formatPrice($price, $currency = 'KSH')
    {
        $number = (float) $price;
        $prefix = strtoupper($currency) === 'USD' ? '$' : 'KSh ';

        if ($number >= 1000000) {
            $value = $number / 1000000;

            return $prefix . number_format($value, $value == floor($value) ? 0 : 2) . 'M';
        }

        if ($number >= 1000) {
            return $prefix . number_format($number, 0);
        }

        return $prefix . number_format($number, 2);
    }
}