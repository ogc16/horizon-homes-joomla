<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_estate
 *
 * @copyright   Copyright (C) Horizon Homes Real Estate.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;

HTMLHelper::_('formbehavior.chosen', 'select');
?>
<form action="<?php echo Route::_('index.php?option=com_estate&view=listings'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="row">
		<div class="col-md-12">
			<div id="filter-bar" class="btn-toolbar">
				<div class="filter-search btn-group pull-left">
					<label for="filter_search" class="element-invisible">Search</label>
					<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" />
					<button type="submit" class="btn">Search</button>
					<button type="button" class="btn" onclick="document.getElementById('filter_search').value='';this.form.submit();">Clear</button>
				</div>
			</div>

			<table class="table table-striped">
				<thead>
					<tr>
						<th width="1%"><?php echo HTMLHelper::_('grid.checkall'); ?></th>
						<th>Title</th>
						<th>Agent</th>
						<th>City</th>
						<th>Price (KES)</th>
						<th>Status</th>
						<th>Published</th>
						<th>Featured</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($this->items as $i => $item) : ?>
						<tr class="row<?php echo $i % 2; ?>">
							<td><?php echo HTMLHelper::_('grid.id', $i, $item->id); ?></td>
							<td>
								<a href="<?php echo Route::_('index.php?option=com_estate&task=listing.edit&id=' . (int) $item->id); ?>">
									<?php echo $this->escape($item->title); ?>
								</a>
							</td>
							<td><?php echo $this->escape($item->agent_name); ?></td>
							<td><?php echo $this->escape($item->city); ?></td>
							<td><?php echo number_format((float) $item->price, 0); ?></td>
							<td><?php echo $this->escape($item->status); ?></td>
							<td><?php echo $item->published ? 'Yes' : 'No'; ?></td>
							<td><?php echo $item->featured ? 'Yes' : 'No'; ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<?php echo $this->pagination->getListFooter(); ?>
		</div>
	</div>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
