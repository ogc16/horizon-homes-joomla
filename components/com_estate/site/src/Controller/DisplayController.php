<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_estate
 *
 * @copyright   Copyright (C) Horizon Homes Real Estate.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Estate\Site\Controller;

use Joomla\CMS\MVC\Controller\BaseController;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Estate component site display controller (Business Logic Tier entry point).
 *
 * Renders either the listings gallery or a single listing depending on the
 * presence of an "alias" request parameter.
 *
 * @since  1.0.0
 */
class DisplayController extends BaseController
{
    /**
     * The default view to render when arriving with no task.
     *
     * @var    string
     * @since  1.0.0
     */
    protected $default_view = 'listings';

    /**
     * Display the listings gallery, or a single listing when an alias is present.
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
        $input = $this->input;
        $alias = $input->getCmd('alias', '');
        $layout = $alias ? 'item' : 'default';

        $view = $this->getView('Listings', 'html', '', [
            'base_path' => $this->basePath,
            'layout'    => $layout,
        ]);

        /** @var \Joomla\Component\Estate\Site\Model\ListingsModel $model */
        $model = $this->getModel('Listings');
        $view->setModel($model, true);

        if ($alias) {
            $view->set('item', $model->getListing($alias));
        } else {
            $filters = [
                'city'          => $input->getCmd('city', ''),
                'property_type' => $input->getCmd('property_type', ''),
                'sale_or_rent'  => $input->getCmd('sale_or_rent', ''),
                'q'             => $input->getString('q', ''),
            ];

            $view->set('items', $model->getListings(array_filter($filters)));
            $view->set('filters', array_filter($filters));
        }

        $view->display();

        return $this;
    }
}