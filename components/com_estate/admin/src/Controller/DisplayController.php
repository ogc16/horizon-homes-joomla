<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_estate
 *
 * @copyright   Copyright (C) Horizon Homes Real Estate.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Estate\Administrator\Controller;

use Joomla\CMS\MVC\Controller\BaseController;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Estate component admin display controller.
 *
 * @since  1.0.0
 */
class DisplayController extends BaseController
{
    /**
     * The default view.
     *
     * @var    string
     * @since  1.0.0
     */
    protected $default_view = 'listings';

    /**
     * Display the view.
     *
     * @param   boolean  $cachable   If true, the view output will be cached.
     * @param   boolean  $urlparams  An array of safe URL parameters.
     *
     * @return  DisplayController  This object to support chaining.
     *
     * @since   1.0.0
     */
    public function display($cachable = false, $urlparams = false)
    {
        return parent::display();
    }
}