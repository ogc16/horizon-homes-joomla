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
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Session\Session;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Admin single-listing editor (Business Logic Tier).
 *
 * Handles the create/edit/save cycle for a listing record, plus the redirect
 * flow between the editor and the listings list view.
 *
 * @since  1.0.0
 */
class ListingController extends BaseController
{
    /**
     * The back-to-list URL.
     *
     * @var    string
     * @since  1.0.0
     */
    protected $listUrl = 'index.php?option=com_estate&view=listings';

    /**
     * Render the editor for a single listing.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function edit()
    {
        $id = (int) $this->input->get('id', 0, 'int');

        $view = $this->getView('Listing', 'html', '', [
            'base_path' => $this->basePath,
        ]);

        $view->set('item', $this->getModel('Listing')->getItem($id));
        $view->set('agents', $this->getModel('Listing')->getAgents());
        $view->display();
    }

    /**
     * Save the listing and return to the list.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function save()
    {
        $this->persist();

        $this->setRedirect($this->listUrl);
    }

    /**
     * Save the listing and stay on the editor.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function apply()
    {
        $id = $this->persist();

        $this->setRedirect('index.php?option=com_estate&task=listing.edit&id=' . (int) $id);
    }

    /**
     * Leave the editor without saving.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function cancel()
    {
        $this->setRedirect($this->listUrl);
    }

    /**
     * Bind and store the submitted listing.
     *
     * @return  integer  The stored listing id.
     *
     * @since   1.0.0
     */
    protected function persist()
    {
        $app = $this->app;

        if (!Session::checkToken()) {
            $app->enqueueMessage('Invalid session token.', 'error');
            $this->setRedirect($this->listUrl);

            return 0;
        }

        $data = (array) $this->input->post->get('jform', [], 'array');

        if (empty($data['title'])) {
            $app->enqueueMessage('A title is required.', 'error');
            $this->setRedirect($this->listUrl);

            return 0;
        }

        $data['id'] = (int) ($data['id'] ?? 0);
        $data['price'] = (float) ($data['price'] ?? 0);
        $data['published'] = isset($data['published']) ? 1 : 0;
        $data['featured'] = isset($data['featured']) ? 1 : 0;
        $data['off_plan'] = isset($data['off_plan']) ? 1 : 0;
        $data['agent_id'] = (int) ($data['agent_id'] ?? 0);
        $data['bedrooms'] = (int) ($data['bedrooms'] ?? 0);
        $data['bathrooms'] = (int) ($data['bathrooms'] ?? 0);
        $data['area_sqft'] = (int) ($data['area_sqft'] ?? 0);
        $data['ordering'] = (int) ($data['ordering'] ?? 0);

        if (empty($data['alias'])) {
            $data['alias'] = OutputFilter::stringURLSafe($data['title']);
        }

        $model = $this->getModel('Listing');

        if ($model->aliasInUse($data['alias'], $data['id'])) {
            $data['alias'] = $model->nextFreeAlias($data['alias'], $data['id']);
        }

        $id = $model->save($data);

        if ($id) {
            $msg = $data['id'] ? 'Listing updated.' : 'Listing created.';
            $app->enqueueMessage($msg, 'message');
        } else {
            $app->enqueueMessage('Could not save the listing.', 'error');

            return $data['id'] ?: 0;
        }

        return (int) $id;
    }
}