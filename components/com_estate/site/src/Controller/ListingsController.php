<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_estate
 *
 * @copyright   Copyright (C) Horizon Homes Real Estate.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Estate\Site\Controller;

use Joomla\CMS\Factory;
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Listings controller (site).
 *
 * Handles the mutating "task" routes: saving a viewing-enquiry booking and
 * rendering the about page. This holds the business rules / validation logic.
 *
 * @since  1.0.0
 */
class ListingsController extends BaseController
{
    /**
     * Save a viewing-enquiry (booking) submitted from a single listing page.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function saveBooking()
    {
        $app   = Factory::getApplication();
        $input = $this->input;
        $db    = Factory::getContainer()->get('db');

        // CSRF protection.
        Session::checkToken() or die('Invalid Token');

        $filter = InputFilter::getInstance();

        $data = [
            'listing_id'     => $input->getInt('listing_id', 0),
            'name'           => $filter->clean(trim($input->getString('name', '')), 'STRING'),
            'email'          => $filter->clean(trim($input->getString('email', '')), 'EMAIL'),
            'phone'          => $filter->clean(trim($input->getString('phone', '')), 'STRING'),
            'message'        => $filter->clean(trim($input->getString('message', '')), 'STRING'),
            'preferred_date' => $input->getString('preferred_date', ''),
            'ip'             => $input->server->getString('REMOTE_ADDR', ''),
        ];

        // Lightweight validation (business rules).
        $errors = [];

        if (\strlen($data['name']) < 2) {
            $errors[] = 'Please provide your name.';
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please provide a valid email address.';
        }

        if ($data['listing_id'] <= 0) {
            $errors[] = 'Missing listing reference.';
        }

        if (!empty($errors)) {
            $app->enqueueMessage(implode('<br />', $errors), 'error');

            return;
        }

        $created = Factory::getDate()->toSql();

        $query = $db->getQuery(true)
            ->insert($db->quoteName('#__estate_bookings'))
            ->columns($db->quoteName(['listing_id', 'name', 'email', 'phone', 'message', 'preferred_date', 'ip', 'state', 'created']))
            ->values(
                implode(',', [
                    $db->quote($data['listing_id']),
                    $db->quote($data['name']),
                    $db->quote($data['email']),
                    $db->quote($data['phone']),
                    $db->quote($data['message']),
                    $db->quote($data['preferred_date'] !== '' ? $data['preferred_date'] : null),
                    $db->quote($data['ip']),
                    $db->quote(0),
                    $db->quote($created),
                ])
            );

        try {
            $db->setQuery($query)->execute();
            $app->enqueueMessage('Thank you! Your viewing request has been received. Our agent will contact you shortly.', 'message');
        } catch (\Exception $e) {
            $app->enqueueMessage('Sorry, we could not save your request. Please try again.', 'error');
        }

        $return = $input->get('return', 'index.php?option=com_estate&view=listings', 'string');

        $app->redirect(Route::_($return, false));
    }

    /**
     * Default view: properties list and single listing detail.
     *
     * Routes on the presence of an alias query parameter:
     *  - with alias    -> single listing detail layout (item.php)
     *  - without alias -> properties list layout (default.php), honouring the
     *                     q / city / property_type / sale_or_rent filters.
     *
     * @param   boolean  $cachable   Whether the view output is cacheable.
     * @param   array    $urlparams  Safe query params for caching.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function display($cachable = false, $urlparams = [])
    {
        $view  = $this->getView('Listings', 'html', '', ['base_path' => $this->basePath]);
        $model = $this->getModel('Listings');
        $view->setModel($model, true);

        $alias = $this->input->getString('alias', '');

        if ($alias !== '') {
            $item = $model->getListing($alias);

            if ($item === null) {
                Factory::getApplication()->setHeader('status', 404, true);
            }

            $view->set('item', $item);
            $view->setLayout('item');
            $view->display();

            return;
        }

        $filters = [
            'q'             => $this->input->getString('q', ''),
            'city'          => $this->input->getString('city', ''),
            'property_type' => $this->input->getString('property_type', ''),
            'sale_or_rent'  => $this->input->getString('sale_or_rent', ''),
        ];

        $filters = array_filter($filters, static function ($value) {
            return $value !== '';
        });

        $view->set('items', $model->getListings($filters, 0));
        $view->set('filters', $filters);
        $view->setLayout('default');
        $view->display();
    }

    /**
     * The homepage / landing page.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function home()
    {
        // Joomla falls back to the default (home) menu item when the URL carries
        // no Itemid, injecting its task (listings.home) even for list/detail
        // URLs. Re-dispatch those to the real list/detail rendering.
        if ($this->input->get('view', '') !== '' || $this->input->getString('alias', '') !== '') {
            $this->display();

            return;
        }

        $view = $this->getView('Listings', 'html', '', [
            'base_path' => $this->basePath,
            'layout'    => 'home',
        ]);

        $model = $this->getModel('Listings');
        $view->setModel($model, true);
        $view->set('items', $model->getListings([], 3));
        $view->set('stats', $model->getStats());
        $view->set('filters', []);
        $view->display();
    }

    /**
     * The about / company page.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function about()
    {
        $view = $this->getView('Listings', 'html', '', [
            'base_path' => $this->basePath,
            'layout'    => 'about',
        ]);

        $model = $this->getModel('Listings');
        $view->setModel($model, true);
        $view->set('agents', $model->getAgents());
        $view->set('stats', $model->getStats());
        $view->display();
    }
}