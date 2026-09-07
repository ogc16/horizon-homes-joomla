<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_estate
 *
 * @copyright   Copyright (C) Horizon Homes Real Estate.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Factory;

/**
 * Admin listings controller (form capable).
 *
 * @since  1.0.0
 */
class EstateControllerListings extends FormController
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $text_prefix = 'COM_ESTATE';

	/**
	 * Default task.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $view_item = 'listing';

	/**
	 * Publish action.
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function publish()
	{
		$this->setState('list.published', 1);
		$this->setState('list.is_publish', 1);
		$this->listAction();
	}

	/**
	 * Unpublish action.
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function unpublish()
	{
		$this->setState('list.published', 0);
		$this->setState('list.is_publish', 0);
		$this->listAction();
	}

	/**
	 * Trash action.
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function trash()
	{
		$this->listAction();
	}

	/**
	 * Shared bulk action handler.
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	protected function listAction()
	{
		$app    = Factory::getApplication();
		$db     = Factory::getDbo();
		$pks    = $app->input->get('cid', array(), 'array');
		$task   = $this->getTask();

		if (empty($pks))
		{
			$app->enqueueMessage('No items selected.', 'warning');
			$this->setRedirect('index.php?option=com_estate&view=listings');

			return;
		}

		foreach ($pks as $pk)
		{
			$query = $db->getQuery(true)
				->update($db->quoteName('#__estate_listings'))
				->where($db->quoteName('id') . ' = ' . (int) $pk);

			if ($task === 'publish')
			{
				$query->set($db->quoteName('published') . ' = 1');
				$db->setQuery($query)->execute();
			}
			elseif ($task === 'unpublish')
			{
				$query->set($db->quoteName('published') . ' = 0');
				$db->setQuery($query)->execute();
			}
			elseif ($task === 'trash')
			{
				$db->setQuery($db->getQuery(true)
					->delete($db->quoteName('#__estate_listings'))
					->where($db->quoteName('id') . ' = ' . (int) $pk))->execute();
			}
		}

		$app->enqueueMessage('Listings updated.');
		$this->setRedirect('index.php?option=com_estate&view=listings');
	}
}
