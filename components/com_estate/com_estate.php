<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_estate
 *
 * @copyright   Copyright (C) Horizon Homes Real Estate.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;

// Access check (component is public for the site, redirect admins to admin).
if (!Factory::getApplication()->isClient('administrator'))
{
	$controller = BaseController::getInstance('Estate');
	$controller->execute(Factory::getApplication()->input->getCmd('task', 'display'));
	$controller->redirect();
}
