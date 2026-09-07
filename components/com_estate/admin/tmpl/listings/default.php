<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_estate
 *
 * Copyright (C) Horizon Homes Real Estate.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

/** @var \Joomla\Component\Estate\Administrator\View\Listings\HtmlView $this */
?>
<form action="<?php echo Route::_('index.php?option=com_estate&view=listings'); ?>" method="post" name="adminForm" id="adminForm">
	<?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>
	<div class="table-responsive">
		<table class="table table-sm">
			<caption class="visually-hidden">Property listings</caption>
			<thead>
				<tr>
					<th class="w-1 text-nowrap"><?php echo HTMLHelper::_('grid.checkall'); ?></th>
					<th scope="col">Title</th>
					<th scope="col">Agent</th>
					<th scope="col">City</th>
					<th scope="col" class="w-10">Price</th>
					<th scope="col" class="w-8">Off-plan</th>
					<th scope="col" class="w-8">3D Tour</th>
					<th scope="col" class="w-8">Status</th>
					<th scope="col" class="w-8">Published</th>
					<th scope="col" class="w-8">Featured</th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($this->items as $i => $item) : ?>
				<?php $editUrl = Route::_('index.php?option=com_estate&task=listing.edit&id=' . (int) $item->id); ?>
				<tr class="row<?php echo $i % 2; ?>">
					<td class="text-center"><?php echo HTMLHelper::_('grid.id', $i, $item->id); ?></td>
					<th scope="row" class="h6">
						<a href="<?php echo $editUrl; ?>"><?php echo $this->escape($item->title); ?></a>
						<?php if ((int) $item->off_plan) : ?>
							<span class="badge bg-primary ms-1">Off-plan</span>
						<?php endif; ?>
					</th>
					<td><?php echo $this->escape($item->agent_name ?? ''); ?></td>
					<td><?php echo $this->escape($item->city); ?></td>
					<td class="text-end"><?php echo number_format((float) $item->price, 0); ?> <?php echo $this->escape($item->currency); ?></td>
					<td><?php echo (int) $item->off_plan ? 'Yes' : 'No'; ?></td>
					<td>
						<?php if ((int) $item->tour_id) : ?>
							<span class="badge bg-success">State <?php echo (int) $item->tour_state; ?></span>
						<?php endif; ?>
						<a class="btn btn-sm btn-outline-secondary" href="<?php echo Route::_('index.php?option=com_estate&view=capture&listing_id=' . (int) $item->id); ?>">Capture</a>
					</td>
					<td><?php echo $this->escape($item->status); ?></td>
					<td><?php echo (int) $item->published ? 'Yes' : 'No'; ?></td>
					<td><?php echo (int) $item->featured ? 'Yes' : 'No'; ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php echo $this->pagination->getListFooter(); ?>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>