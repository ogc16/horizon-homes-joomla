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
use Joomla\CMS\Uri\Uri;

/** @var \Joomla\Component\Estate\Administrator\View\Capture\HtmlView $this */

$listing     = $this->listing;
$tour        = $this->tour;
$shots       = $this->shots;
$stateLabels = $this->stateLabels;
$baseUrl     = Uri::root(true);
$published   = (int) $tour->state === 3;
$hasImages   = (bool) array_filter($shots, fn($s) => $s->image !== '');
$previewUrl  = Uri::root() . 'index.php?option=com_estate&view=listings&alias=' . urlencode($listing->alias);
?>
<h2 class="mb-1"><?php echo $this->escape($listing->title); ?></h2>
<p class="text-muted"><?php echo $this->escape($listing->address . ', ' . $listing->city); ?></p>

<div class="row mb-4">
	<div class="col-lg-9">
		<div class="card mb-3">
			<div class="card-header d-flex justify-content-between align-items-center">
				<span><span class="badge bg-primary">Panorama sequence</span> shoot the shots in order</span>
				<?php if ($hasImages && $published) : ?>
					<a class="btn btn-sm btn-success" href="<?php echo $previewUrl; ?>" target="_blank" rel="noopener">
						<span class="icon-eye"></span> Preview live 3D tour
					</a>
				<?php endif; ?>
			</div>
			<div class="card-body">
				<div class="accordion" id="shotAccordion">
					<?php foreach ($shots as $index => $shot) : $uploadUrl = Route::_('index.php?option=com_estate&task=capture.upload&listing_id=' . (int) $listing->id . '&shot_id=' . (int) $shot->id); ?>
						<div class="accordion-item">
							<h3 class="accordion-header" id="shotHeading<?php echo (int) $shot->id; ?>">
								<button class="accordion-button <?php echo $index === 0 ? '' : 'collapsed'; ?>" type="button"
									data-bs-toggle="collapse" data-bs-target="#shotBody<?php echo (int) $shot->id; ?>" aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>">
									<span class="me-2 bg-dark text-white rounded px-2 py-1"><?php echo (int) $shot->slot; ?></span>
									<?php echo $this->escape($shot->room_label); ?>
									<span class="badge bg-secondary ms-2">Position <?php echo $this->escape($shot->position_key); ?></span>
									<?php if ($shot->image !== '') : ?>
										<span class="badge bg-success ms-1">uploaded</span>
									<?php endif; ?>
								</button>
							</h3>
							<div id="shotBody<?php echo (int) $shot->id; ?>" class="accordion-collapse collapse <?php echo $index === 0 ? 'show' : ''; ?>"
								data-bs-parent="#shotAccordion" aria-labelledby="shotHeading<?php echo (int) $shot->id; ?>">
								<div class="accordion-body">
									<div class="row g-3 align-items-center">
										<div class="col-md-3 text-center">
											<?php if ($shot->image !== '') : ?>
												<a href="<?php echo $baseUrl . '/' . ltrim($shot->image, '/'); ?>" target="_blank" rel="noopener">
													<img src="<?php echo $baseUrl . '/' . ltrim($shot->image, '/'); ?>" alt="Position <?php echo $this->escape($shot->position_key); ?>" class="img-fluid rounded border" style="max-height:140px;">
												</a>
											<?php else : ?>
												<div class="d-flex align-items-center justify-content-center rounded border bg-light text-muted" style="height:140px;">
													<span class="icon-camera"></span>
												</div>
											<?php endif; ?>
										</div>
										<div class="col-md-5">
											<p class="mb-2"><span class="fw-semibold text-primary"><?php echo $this->escape($shot->position_key); ?></span> &mdash; <?php echo $this->escape($shot->instructions); ?></p>
										</div>
										<div class="col-md-4">
											<?php if ($shot->image === '') : ?>
												<form action="<?php echo $uploadUrl; ?>" method="post" enctype="multipart/form-data">
													<label for="file-shot<?php echo (int) $shot->id; ?>" class="form-label small">Upload 360&deg; panorama</label>
													<div class="input-group input-group-sm">
														<input type="file" id="file-shot<?php echo (int) $shot->id; ?>" name="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="form-control form-control-sm" required>
														<button type="submit" class="btn btn-sm btn-primary">Upload</button>
													</div>
													<?php echo HTMLHelper::_('form.token'); ?>
												</form>
											<?php else : ?>
												<form action="<?php echo $uploadUrl; ?>" method="post" enctype="multipart/form-data" class="mb-2">
													<label for="filer-shot<?php echo (int) $shot->id; ?>" class="form-label small">Replace panorama</label>
													<div class="input-group input-group-sm">
														<input type="file" id="filer-shot<?php echo (int) $shot->id; ?>" name="file" accept=".jpg,.jpeg,.png,.webp" class="form-control form-control-sm">
														<button type="submit" class="btn btn-sm btn-outline-primary">Replace</button>
													</div>
													<?php echo HTMLHelper::_('form.token'); ?>
												</form>
												<form action="<?php echo Route::_('index.php?option=com_estate&task=capture.clear&listing_id=' . (int) $listing->id . '&shot_id=' . (int) $shot->id); ?>" method="post">
													<button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remove this panorama?');">Remove</button>
													<?php echo HTMLHelper::_('form.token'); ?>
												</form>
											<?php endif; ?>
										</div>
									</div>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-3">
		<div class="card mb-3">
			<div class="card-header">Tour status</div>
			<div class="card-body">
				<form action="<?php echo Route::_('index.php?option=com_estate&task=capture.saveState&listing_id=' . (int) $listing->id); ?>" method="post">
					<input type="hidden" name="tour_id" value="<?php echo (int) $tour->id; ?>" />
					<div class="mb-2">
						<label class="form-label small text-muted" for="capture-state">State</label>
						<select class="form-select form-select-sm" name="state" id="capture-state">
							<?php foreach ($stateLabels as $value => $label) : ?>
								<option value="<?php echo (int) $value; ?>" <?php echo ((int) $tour->state === (int) $value) ? 'selected' : ''; ?>>
									<?php echo $this->escape($label); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="mb-2">
						<label class="form-label small text-muted" for="capture-notes">Notes</label>
						<textarea class="form-control form-control-sm" name="notes" id="capture-notes" rows="3"><?php echo $this->escape($tour->notes); ?></textarea>
					</div>
					<button type="submit" class="btn btn-sm btn-primary w-100">Save status</button>
					<?php echo HTMLHelper::_('form.token'); ?>
				</form>
			</div>
		</div>

		<div class="card mb-3">
			<div class="card-header">Shooting plan</div>
			<div class="card-body p-2">
				<?php echo $this->loadTemplate('floorplan'); ?>
			</div>
		</div>

		<div class="card">
			<div class="card-header">Capturing checklist</div>
			<div class="card-body">
				<ul class="small mb-0">
					<li>Use a phone on a mini tripod at 1.5 m (eye level).</li>
					<li>Shoot with your camera's built-in 360&deg; or a free app such as Google Street View / Ricoh.</li>
					<li>Leave at least 30% overlap with the previous shot.</li>
					<li>Turn on interior lights; windows make rooms look bigger when balanced.</li>
					<li>Hide mirrors, wet towels, bins, cables and pets before shooting.</li>
					<li>Walk the exact path shown on the plan: A1 &rarr; A2 &rarr; B1 &rarr; B2 &rarr; C1 &rarr; C2 &rarr; D1.</li>
					<li>Upload each panorama here; the site stitches them in order automatically.</li>
				</ul>
			</div>
		</div>
	</div>
</div>