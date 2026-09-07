<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_estate
 *
 * @copyright   Copyright (C) Horizon Homes Real Estate.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Estate\Administrator\Extension;

use Joomla\CMS\Extension\MVCComponent;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Component class for com_estate.
 *
 * @since  1.0.0
 */
class EstateComponent extends MVCComponent
{
    /**
     * The default controller to use when none is requested.
     *
     * @var    string
     * @since  1.0.0
     */
    protected $defaultController = 'display';
}