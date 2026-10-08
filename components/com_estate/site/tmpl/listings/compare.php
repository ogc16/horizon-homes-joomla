<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_estate
 *
 * Presentation template: side-by-side property comparison table.
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;

/** @var \Joomla\Component\Estate\Site\View\Listings\HtmlView $this */
$items  = isset($this->items) ? $this->items : [];
$allIds = isset($this->allIds) ? $this->allIds : [];
$base   = Route::_('index.php?option=com_estate&view=listings');
?>

<div class="estate-compare">
	<h1>Compare Properties</h1>

	<?php if (empty($items)) : ?>
		<div class="estate-compare__empty">
			<p>No properties selected for comparison.</p>
			<p><a href="<?php echo $base; ?>">&larr; Browse properties</a> and select up to 4 to compare.</p>
		</div>
	<?php else : ?>
		<table class="estate-compare__table">
			<thead>
				<tr>
					<th></th>
					<?php foreach ($items as $item) : ?>
						<td>
							<?php if ($item->main_image) : ?>
								<img class="estate-compare__img" src="<?php echo $this->escape($item->main_image); ?>" alt="<?php echo $this->escape($item->title); ?>" />
							<?php endif; ?>
							<div class="estate-compare__title">
								<a href="<?php echo Route::_('index.php?option=com_estate&view=listings&alias=' . $item->alias); ?>">
									<?php echo $this->escape($item->title); ?>
								</a>
							</div>
							<a class="estate-compare__remove-btn" href="<?php echo $base . '&task=listings.compare&ids=' . implode(',', array_diff($allIds, [$item->id])); ?>">Remove</a>
						</td>
					<?php endforeach; ?>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th class="estate-compare__label">Price</th>
					<?php foreach ($items as $item) : ?>
						<td><strong><?php echo $this->formatPrice($item->price, $item->currency ?? 'KSH'); ?></strong> / <?php echo $this->escape(ucfirst($item->sale_or_rent)); ?></td>
					<?php endforeach; ?>
				</tr>
				<tr>
					<th class="estate-compare__label">Status</th>
					<?php foreach ($items as $item) : ?>
						<td><?php echo $this->escape(ucfirst($item->status)); ?></td>
					<?php endforeach; ?>
				</tr>
				<tr>
					<th class="estate-compare__label">Bedrooms</th>
					<?php foreach ($items as $item) : ?>
						<td><?php echo (int) $item->bedrooms; ?></td>
					<?php endforeach; ?>
				</tr>
				<tr>
					<th class="estate-compare__label">Bathrooms</th>
					<?php foreach ($items as $item) : ?>
						<td><?php echo (int) $item->bathrooms; ?></td>
					<?php endforeach; ?>
				</tr>
				<tr>
					<th class="estate-compare__label">Area</th>
					<?php foreach ($items as $item) : ?>
						<td><?php echo number_format((int) $item->area_sqft); ?> sqft</td>
					<?php endforeach; ?>
				</tr>
				<tr>
					<th class="estate-compare__label">Type</th>
					<?php foreach ($items as $item) : ?>
						<td><?php echo $this->escape(ucfirst($item->property_type)); ?></td>
					<?php endforeach; ?>
				</tr>
				<tr>
					<th class="estate-compare__label">City</th>
					<?php foreach ($items as $item) : ?>
						<td><?php echo $this->escape($item->city); ?></td>
					<?php endforeach; ?>
				</tr>
				<tr>
					<th class="estate-compare__label">Address</th>
					<?php foreach ($items as $item) : ?>
						<td><?php echo $this->escape($item->address); ?></td>
					<?php endforeach; ?>
				</tr>
				<?php
					$hasOffPlan = false;
					foreach ($items as $item) {
						if ((int) $item->off_plan) { $hasOffPlan = true; break; }
					}
				?>
				<?php if ($hasOffPlan) : ?>
					<tr>
						<th class="estate-compare__label">Off-plan</th>
						<?php foreach ($items as $item) : ?>
							<td><?php echo (int) $item->off_plan ? 'Yes' : 'No'; ?></td>
						<?php endforeach; ?>
					</tr>
					<tr>
						<th class="estate-compare__label">Developer</th>
						<?php foreach ($items as $item) : ?>
							<td><?php echo $this->escape($item->developer ?: '—'); ?></td>
						<?php endforeach; ?>
					</tr>
					<tr>
						<th class="estate-compare__label">Completion</th>
						<?php foreach ($items as $item) : ?>
							<td><?php echo ($item->completion_date && $item->completion_date !== '0000-00-00') ? $this->escape(date('M Y', strtotime($item->completion_date))) : '—'; ?></td>
						<?php endforeach; ?>
					</tr>
				<?php endif; ?>
				<tr>
					<th class="estate-compare__label">Agent</th>
					<?php foreach ($items as $item) : ?>
						<td><?php echo $this->escape($item->agent_name ?: '—'); ?></td>
					<?php endforeach; ?>
				</tr>
			</tbody>
		</table>

		<p style="margin-top:20px;"><a href="<?php echo $base; ?>">&larr; Back to all properties</a></p>
	<?php endif; ?>
</div>
