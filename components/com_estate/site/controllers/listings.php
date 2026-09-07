<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_estate
 *
 * @copyright   Copyright (C) Horizon Homes Real Estate.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

/**
 * Listings controller (site).
 *
 * @since  1.0.0
 */
class EstateControllerListings extends BaseController
{
	/**
	 * Display the listings gallery, or a single listing when an alias is present.
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function display()
	{
		$input      = Factory::getApplication()->input;
		$alias      = $input->get('alias', '', 'string');
		$view       = $this->getView('Listings', 'html');
		$model      = $this->getModel('Listings', 'EstateModel');
		$view->setModel($model, true);

		if (!empty($alias))
		{
			$view->setLayout('item');
			$item = $model->getListing($alias);

			if (!$item)
			{
				Factory::getApplication()->enqueueMessage('Listing not found.', 'warning');
			}

			$view->item = $item;
		}
		else
		{
			$filters = array(
				'city'          => $input->get('city', '', 'string'),
				'property_type' => $input->get('property_type', '', 'string'),
				'sale_or_rent'  => $input->get('sale_or_rent', '', 'string'),
				'q'             => $input->get('q', '', 'string'),
			);

			$view->items = $model->getListings(array_filter($filters));
			$view->filters = array_filter($filters);
		}

		$view->display();
	}

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
		$input = $app->input;
		$db    = Factory::getDbo();

		// Minimum CSRF protection for guests (token hidden field).
		\Joomla\CMS\Session\Session::checkToken() or die('Invalid Token');

		$data = array(
			'listing_id'     => (int) $input->get('listing_id', 0, 'int'),
			'name'           => trim($input->get('name', '', 'string')),
			'email'          => trim($input->get('email', '', 'string')),
			'phone'          => trim($input->get('phone', '', 'string')),
			'message'        => trim($input->get('message', '', 'string')),
			'preferred_date' => $input->get('preferred_date', '', 'string'),
			'ip'             => $input->server->get('REMOTE_ADDR', '', 'string'),
		);

		// Lightweight validation (business rules).
		$errors = array();

		if (empty($data['name']) || strlen($data['name']) < 2)
		{
			$errors[] = 'Please provide your name.';
		}

		if (!\Joomla\CMS\Filter\InputFilter::getInstance()->clean($data['email'], 'email'))
		{
			$errors[] = 'Please provide a valid email address.';
		}

		if ($data['listing_id'] <= 0)
		{
			$errors[] = 'Missing listing reference.';
		}

		if (!empty($errors))
		{
			$app->enqueueMessage(implode('<br />', $errors), 'error');

			return;
		}

		$columns = array('listing_id', 'name', 'email', 'phone', 'message', 'preferred_date', 'ip', 'state', 'created');
		$values  = array(
			$db->quote($data['listing_id']),
			$db->quote($data['name']),
			$db->quote($data['email']),
			$db->quote($data['phone']),
			$db->quote($data['message']),
			$db->quote($data['preferred_date'] ?: null),
			$db->quote($data['ip']),
			$db->quote(0),
			$db->quote(Factory::getDate()->toSql()),
		);

		$query = $db->getQuery(true)
			->insert($db->quoteName('#__estate_bookings'))
			->columns($db->quoteName($columns))
			->values(implode(',', $values));

		try
		{
			$db->setQuery($query)->execute();
			$app->enqueueMessage('Thank you! Your viewing request has been received. Our agent will contact you shortly.', 'message');
		}
		catch (\Exception $e)
		{
			$app->enqueueMessage('Sorry, we could not save your request. Please try again.', 'error');
		}

		$return = $input->get('return', '', 'string');

		if (empty($return))
		{
			$return = 'index.php?option=com_estate&view=listings';
		}

		$app->redirect(Route::_($return, false));
	}

	/**
	 * The about / company page uses a simple static view.
	 *
	 * @since   1.0.0
	 */
	public function about()
	{
		$view = $this->getView('Listings', 'html');
		$view->setLayout('about');
		$view->display();
	}
}
