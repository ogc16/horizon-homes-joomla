<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_estate
 *
 * @copyright   Copyright (C) Horizon Homes Real Estate.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Estate\Site\Helper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Country-based currency localisation.
 *
 * Prices are stored once in KSH or USD and converted for the visitor's country
 * using a fixed, published rate table:
 *
 *   1 USD = 129 KSh
 *   1 KSh = 28 USh (Uganda)
 *   1 KSh = 20 TSh (Tanzania)
 *   1 USD = 1,300 RWF (Rwanda)
 *
 * The visitor's country is resolved from the `cc` query parameter, then the
 * `estate_cc` cookie (set by the geo-IP/selector script), and finally defaults
 * to Kenya. The site never rewrites stored prices - it only converts on output.
 *
 * @since  1.0.0
 */
class CurrencyHelper
{
    /**
     * Currency code per East-African country.
     *
     * @var    string[]
     * @since  1.0.0
     */
    public const COUNTRY_CURRENCIES = [
        'KE' => 'KSH',
        'UG' => 'UGX',
        'TZ' => 'TZS',
        'RW' => 'RWF',
    ];

    /**
     * Display labels per currency.
     *
     * @var    string[]
     * @since  1.0.0
     */
    public const CURRENCY_LABELS = [
        'KSH' => 'KSh',
        'UGX' => 'USh',
        'TZS' => 'TSh',
        'RWF' => 'RWF',
    ];

    /**
     * Country select labels.
     *
     * @var    string[]
     * @since  1.0.0
     */
    public const COUNTRY_LABELS = [
        'KE' => 'Kenya (KSh)',
        'UG' => 'Uganda (USh)',
        'TZ' => 'Tanzania (TSh)',
        'RW' => 'Rwanda (RWF)',
    ];

    /**
     * Stored-currency rate used before local conversion.
     *
     * @var    float
     * @since  1.0.0
     */
    public const USD_TO_KSH = 129.0;

    /**
     * Multiplier applied to a KSH amount to reach each display currency.
     *
     * @var    float[]
     * @since  1.0.0
     */
    public const KSH_TO = [
        'KSH' => 1.0,
        'UGX' => 28.0,
        'TZS' => 20.0,
        'RWF' => 1300.0 / 129.0,
    ];

    /**
     * Resolve the visitor's display currency code.
     *
     * @return  string  One of KSH, UGX, TZS, RWF.
     *
     * @since   1.0.0
     */
    public static function targetCurrency()
    {
        $cc = strtoupper((string) ($_GET['cc'] ?? ''));
        $cc = $cc === '' ? strtoupper((string) ($_COOKIE['estate_cc'] ?? '')) : $cc;

        if (!isset(self::COUNTRY_CURRENCIES[$cc])) {
            $cc = 'KE';
        }

        return self::COUNTRY_CURRENCIES[$cc];
    }

    /**
     * Resolve the visitor's country code.
     *
     * @return  string  One of KE, UG, TZ, RW.
     *
     * @since   1.0.0
     */
    public static function targetCountry()
    {
        $cc = strtoupper((string) ($_GET['cc'] ?? ''));
        $cc = $cc === '' ? strtoupper((string) ($_COOKIE['estate_cc'] ?? '')) : $cc;

        return isset(self::COUNTRY_CURRENCIES[$cc]) ? $cc : 'KE';
    }

    /**
     * Convert a stored price into the visitor's currency.
     *
     * @param   float  $amount         The stored price.
     * @param   string $storedCurrency The stored currency code (KSH or USD).
     *
     * @return  float  The amount in the visitor's currency.
     *
     * @since   1.0.0
     */
    public static function convert($amount, $storedCurrency = 'KSH')
    {
        $amount = (float) $amount;
        $target = self::targetCurrency();

        // Normalise whatever is stored into KSH first.
        $ksh = strtoupper((string) $storedCurrency) === 'USD' ? $amount * self::USD_TO_KSH : $amount;

        return $ksh * (self::KSH_TO[$target] ?? 1.0);
    }

    /**
     * Format a stored price in the visitor's currency.
     *
     * @param   float  $amount         The stored price.
     * @param   string $storedCurrency The stored currency code (KSH or USD).
     *
     * @return  string  e.g. "KSh 14,500,000" or "TSh 290,000,000".
     *
     * @since   1.0.0
     */
    public static function format($amount, $storedCurrency = 'KSH')
    {
        $target = self::targetCurrency();
        $value  = round(self::convert($amount, $storedCurrency));

        return self::CURRENCY_LABELS[$target] . ' ' . number_format($value, 0, '.', ',');
    }

    /**
     * The label of the current display currency.
     *
     * @return  string  e.g. "KSh".
     *
     * @since   1.0.0
     */
    public static function label()
    {
        return self::CURRENCY_LABELS[self::targetCurrency()];
    }

    /**
     * HTML <option> list for the switcher control.
     *
     * @param   string  $selected  The pre-selected country code.
     *
     * @return  string  Ready-to-embed option markup.
     *
     * @since   1.0.0
     */
    public static function options($selected = '')
    {
        $selected = $selected === '' ? self::targetCountry() : strtoupper($selected);
        $html     = '';

        foreach (self::COUNTRY_LABELS as $code => $label) {
            $html .= '<option value="' . $code . '"' . ((string) $code === $selected ? ' selected' : '') . '>' . $label . '</option>';
        }

        return $html;
    }
}