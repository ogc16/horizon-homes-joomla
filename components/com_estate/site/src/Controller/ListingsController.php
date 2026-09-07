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
        $view->display();
    }
}