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
use Joomla\CMS\Router\Route;

/** @var \Joomla\Component\Estate\Administrator\View\Listing\HtmlView $this */

$item  = $this->item;
$agents = $this->agents;
$typeOptions    = ['house', 'apartment', 'land', 'commercial'];
$statusOptions  = ['available', 'pending', 'sold', 'rented'];
$currencyOptions = ['KSH', 'USD'];
?>
<form action="<?php echo Route::_('index.php?option=com_estate'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="row">
		<div class="col-lg-8">
			<div class="card">
				<div class="card-body">
					<fieldset>
						<legend>Core details</legend>

						<div class="mb-3">
							<label class="form-label" for="jform_title">Title <span class="text-danger" aria-required="true">*</span></label>
							<input type="text" name="jform[title]" id="jform_title" class="form-control" required
								value="<?php echo $this->escape($item->title); ?>" />
						</div>

						<div class="row">
							<div class="col-6 mb-3">
								<label class="form-label" for="jform_alias">Alias</label>
								<input type="text" name="jform[alias]" id="jform_alias" class="form-control"
									value="<?php echo $this->escape($item->alias); ?>" placeholder="Leave blank to auto-generate" />
							</div>
							<div class="col-6 mb-3">
								<label class="form-label" for="jform_ordering">Ordering</label>
								<input type="number" name="jform[ordering]" id="jform_ordering" class="form-control"
									value="<?php echo (int) $item->ordering; ?>" />
							</div>
						</div>

						<div class="row">
							<div class="col-4 mb-3">
								<label class="form-label" for="jform_agent_id">Agent</label>
								<select name="jform[agent_id]" id="jform_agent_id" class="form-select">
									<?php foreach ($agents as $agent) : ?>
										<option value="<?php echo (int) $agent->id; ?>" <?php echo ((int) $agent->id === (int) $item->agent_id) ? 'selected' : ''; ?>>
											<?php echo $this->escape($agent->name); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="col-4 mb-3">
								<label class="form-label" for="jform_property_type">Type</label>
								<select name="jform[property_type]" id="jform_property_type" class="form-select">
									<?php foreach ($typeOptions as $option) : ?>
										<option value="<?php echo $this->escape($option); ?>" <?php echo ($option === (string) $item->property_type) ? 'selected' : ''; ?>>
											<?php echo ucfirst($this->escape($option)); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="col-4 mb-3">
								<label class="form-label" for="jform_status">Status</label>
								<select name="jform[status]" id="jform_status" class="form-select">
									<?php foreach ($statusOptions as $option) : ?>
										<option value="<?php echo $this->escape($option); ?>" <?php echo ($option === (string) $item->status) ? 'selected' : ''; ?>>
											<?php echo ucfirst($this->escape($option)); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>

						<div class="row">
							<div class="col-6 mb-3">
								<label class="form-label" for="jform_price">Price</label>
								<input type="number" step="0.01" min="0" name="jform[price]" id="jform_price" class="form-control"
									value="<?php echo (float) $item->price; ?>" />
							</div>
							<div class="col-3 mb-3">
								<label class="form-label" for="jform_currency">Currency</label>
								<select name="jform[currency]" id="jform_currency" class="form-select">
									<?php foreach ($currencyOptions as $option) : ?>
										<option value="<?php echo $this->escape($option); ?>" <?php echo ($option === (string) $item->currency) ? 'selected' : ''; ?>>
											<?php echo $this->escape($option); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="col-3 mb-3">
								<label class="form-label" for="jform_sale_or_rent">Sale / Rent</label>
								<select name="jform[sale_or_rent]" id="jform_sale_or_rent" class="form-select">
									<option value="sale" <?php echo ((string) $item->sale_or_rent === 'sale') ? 'selected' : ''; ?>>Sale</option>
									<option value="rent" <?php echo ((string) $item->sale_or_rent === 'rent') ? 'selected' : ''; ?>>Rent</option>
								</select>
							</div>
						</div>

						<div class="row">
							<div class="col-4 mb-3">
								<label class="form-label" for="jform_bedrooms">Bedrooms</label>
								<input type="number" min="0" name="jform[bedrooms]" id="jform_bedrooms" class="form-control"
									value="<?php echo (int) $item->bedrooms; ?>" />
							</div>
							<div class="col-4 mb-3">
								<label class="form-label" for="jform_bathrooms">Bathrooms</label>
								<input type="number" min="0" name="jform[bathrooms]" id="jform_bathrooms" class="form-control"
									value="<?php echo (int) $item->bathrooms; ?>" />
							</div>
							<div class="col-4 mb-3">
								<label class="form-label" for="jform_area_sqft">Area (sq ft)</label>
								<input type="number" min="0" name="jform[area_sqft]" id="jform_area_sqft" class="form-control"
									value="<?php echo (int) $item->area_sqft; ?>" />
							</div>
						</div>

						<div class="row">
							<div class="col-6 mb-3">
								<label class="form-label" for="jform_address">Address</label>
								<input type="text" name="jform[address]" id="jform_address" class="form-control"
									value="<?php echo $this->escape($item->address); ?>" />
							</div>
							<div class="col-6 mb-3">
								<label class="form-label" for="jform_city">City</label>
								<input type="text" name="jform[city]" id="jform_city" class="form-control"
									value="<?php echo $this->escape($item->city); ?>" />
							</div>
						</div>

						<div class="mb-3">
							<label class="form-label" for="jform_description">Description</label>
							<textarea name="jform[description]" id="jform_description" class="form-control" rows="10"><?php echo $this->escape($item->description); ?></textarea>
						</div>

						<div class="mb-3">
							<label class="form-label" for="jform_main_image">Main image URL</label>
							<input type="text" name="jform[main_image]" id="jform_main_image" class="form-control"
								value="<?php echo $this->escape($item->main_image); ?>" />
						</div>
					</fieldset>
				</div>
			</div>
		</div>

		<div class="col-lg-4">
			<div class="card mb-3">
				<div class="card-body">
					<fieldset>
						<legend>Publishing</legend>

						<div class="form-check form-switch mb-2">
							<input type="checkbox" name="jform[published]" id="jform_published" class="form-check-input" value="1"
								<?php echo (int) $item->published ? 'checked' : ''; ?> />
							<label class="form-check-label" for="jform_published">Published</label>
						</div>

						<div class="form-check form-switch mb-2">
							<input type="checkbox" name="jform[featured]" id="jform_featured" class="form-check-input" value="1"
								<?php echo (int) $item->featured ? 'checked' : ''; ?> />
							<label class="form-check-label" for="jform_featured">Featured on home page</label>
						</div>
					</fieldset>
				</div>
			</div>

			<div class="card border-primary">
				<div class="card-body">
					<fieldset>
						<legend>Off-plan investment</legend>

						<div class="form-check form-switch mb-3">
							<input type="checkbox" name="jform[off_plan]" id="jform_off_plan" class="form-check-input" value="1"
								<?php echo (int) $item->off_plan ? 'checked' : ''; ?> />
							<label class="form-check-label" for="jform_off_plan">Off-plan opportunity</label>
						</div>

						<div class="mb-3">
							<label class="form-label" for="jform_developer">Developer</label>
							<input type="text" name="jform[developer]" id="jform_developer" class="form-control"
								value="<?php echo $this->escape($item->developer); ?>" />
						</div>

						<div class="mb-3">
							<label class="form-label" for="jform_completion_date">Expected completion</label>
							<input type="date" name="jform[completion_date]" id="jform_completion_date" class="form-control"
								value="<?php echo $item->completion_date && $item->completion_date !== '0000-00-00' ? $this->escape($item->completion_date) : ''; ?>" />
						</div>

						<div class="mb-3">
							<label class="form-label" for="jform_payment_plan">Payment plan</label>
							<textarea name="jform[payment_plan]" id="jform_payment_plan" class="form-control" rows="5"
								placeholder="e.g. Reserve KSh 500,000 - 30% deposit - staged instalments - balance on completion."><?php echo $this->escape($item->payment_plan); ?></textarea>
						</div>
					</fieldset>
				</div>
			</div>
		</div>
	</div>

	<input type="hidden" name="jform[id]" value="<?php echo (int) $item->id; ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>