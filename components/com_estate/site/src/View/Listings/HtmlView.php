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
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Estate\Site\Helper\CurrencyHelper;

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
     * Format a price in the visitor's local currency (KSh / USh / TSh / RWF).
     *
     * @param   mixed   $price     Numeric price.
     * @param   string  $currency  Stored currency code: KSH or USD.
     *
     * @return  string  Formatted price string in the visitor's currency.
     *
     * @since   1.0.0
     */
    public static function formatPrice($price, $currency = 'KSH')
    {
        return CurrencyHelper::format($price, $currency);
    }

    /**
     * The current display currency label (e.g. "KSh").
     *
     * @return  string
     *
     * @since   1.0.0
     */
    public static function currencyLabel()
    {
        return CurrencyHelper::label();
    }

    /**
     * Build the Pannellum multi-scene configuration JSON for a 3D tour.
     *
     * Scenes are chained in capture order (each panorama links forward and
     * backward to its neighbours) so the visitor walks the same route the
     * realtor shot.
     *
     * @param   object|null  $tour  A tour with ->shots, or null.
     *
     * @return  string  JSON for the Pannellum initialisation.
     *
     * @since   1.0.0
     */
    public function tourConfig($tour)
    {
        if (!$tour || empty($tour->shots)) {
            return '{}';
        }

        $shots = array_values($tour->shots);
        $count = count($shots);
        $scenes = [];
        $firstScene = null;
        $baseUrl    = Uri::root(true);

        foreach ($shots as $index => $shot) {
            $alias   = 'room-' . (int) $shot->id;
            $panorama = $shot->image;

            if (stripos($panorama, 'http://') !== 0 && stripos($panorama, 'https://') !== 0) {
                $panorama = $baseUrl . '/' . ltrim($panorama, '/');
            }

            $next = $shots[($index + 1) % $count];
            $prev = $shots[($index - 1 + $count) % $count];

            $hotSpots = [
                [
                    'pitch'     => 0,
                    'yaw'       => 0,
                    'type'      => 'scene',
                    'text'      => 'Next: ' . $next->room_label,
                    'sceneId'   => 'room-' . (int) $next->id,
                    'targetYaw' => 180,
                ],
                [
                    'pitch'     => 0,
                    'yaw'       => 180,
                    'type'      => 'scene',
                    'text'      => 'Back: ' . $prev->room_label,
                    'sceneId'   => 'room-' . (int) $prev->id,
                    'targetYaw' => 0,
                ],
            ];

            $scenes[$alias] = [
                'type'      => 'equirectangular',
                'panorama'  => $panorama,
                'title'     => $shot->room_label . ' (' . $shot->position_key . ')',
                'hotSpots'  => $hotSpots,
            ];

            $firstScene = $firstScene ?? $alias;
        }

        $config = [
            'default'  => [
                'firstScene'       => $firstScene,
                'autoLoad'         => true,
                'sceneFadeDuration' => 800,
            ],
            'scenes'   => $scenes,
        ];

        return json_encode($config, JSON_UNESCAPED_SLASHES);
    }
}