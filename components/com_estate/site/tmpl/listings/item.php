<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_estate
 *
 * Presentation template: single listing detail + viewing-enquiry form.
 */

\defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

/** @var \Joomla\Component\Estate\Site\View\Listings\HtmlView $this */
$item    = isset($this->item) ? $this->item : null;
$gallery = $item ? $this->getModel()->getGallery($item) : [];
$tour    = isset($this->tour) && $this->tour ? $this->tour : null;
?>
<?php if (!$item) : ?>
	<p>This property is no longer available.</p>
	<p><a href="<?php echo Route::_('index.php?option=com_estate&view=listings'); ?>">&larr; Back to all properties</a></p>
	<?php return; ?>
<?php endif; ?>

<nav class="estate-breadcrumb">
	<a href="<?php echo Route::_('index.php?option=com_estate&view=listings'); ?>">Properties</a>
	<span>&raquo;</span>
	<span><?php echo $this->escape($item->title); ?></span>
</nav>

<div class="estate-detail">
	<div class="estate-detail__gallery">
		<?php $imageIndex = 0; ?>
		<?php foreach ($gallery as $image) : ?>
			<?php if ($imageIndex++ === 0) : ?>
				<img class="estate-detail__feature" src="<?php echo $this->escape($image); ?>" alt="<?php echo $this->escape($item->title); ?>" />
			<?php else : ?>
				<img class="estate-detail__thumb" src="<?php echo $this->escape($image); ?>" alt="" loading="lazy" />
			<?php endif; ?>
		<?php endforeach; ?>

		<?php if (!$gallery) : ?>
			<div class="estate-detail__feature estate-detail__nophoto">Image coming soon</div>
		<?php endif; ?>

		<?php if ($tour) : ?>
			<button type="button" class="estate-tour-btn" onclick="EstateTour.open()">
				<span class="estate-tour-btn__icon">&#9778;</span> Take the 360&deg; tour
			</button>
		<?php endif; ?>
	</div>

	<div class="estate-detail__info">
		<h1 class="estate-detail__title"><?php echo $this->escape($item->title); ?></h1>
		<p class="estate-detail__price"><?php echo $this->formatPrice($item->price, $item->currency ?? 'KSH'); ?>
			<span class="estate-detail__purpose">/ <?php echo $this->escape(ucfirst($item->sale_or_rent)); ?></span>
		</p>
		<p class="estate-detail__status estate-detail__status--<?php echo $this->escape($item->status); ?>">
			<?php echo $this->escape(ucfirst($item->status)); ?>
		</p>

		<ul class="estate-detail__specs">
			<li><strong><?php echo (int) $item->bedrooms; ?></strong> Bedrooms</li>
			<li><strong><?php echo (int) $item->bathrooms; ?></strong> Bathrooms</li>
			<li><strong><?php echo number_format((int) $item->area_sqft); ?></strong> sqft</li>
			<li><strong><?php echo $this->escape(ucfirst($item->property_type)); ?></strong> Type</li>
		</ul>

		<p class="estate-detail__address"><?php echo $this->escape($item->address . ', ' . $item->city); ?></p>

		<?php if ((int) $item->off_plan) : ?>
			<div class="estate-offplan">
				<span class="estate-badge estate-badge--offplan">Off-plan investment</span>
				<h2>Invest off-plan, pay at today's price</h2>
				<p>Lock in the current price now and pay through construction. Units like this typically appreciate
					well before handover &mdash; a stronger buy than resale for investors.</p>
				<ul class="estate-offplan__facts">
					<li><span>Developer</span><strong><?php echo $this->escape($item->developer); ?></strong></li>
					<?php if ($item->completion_date && $item->completion_date !== '0000-00-00') : ?>
						<li><span>Expected completion</span><strong><?php echo $this->escape(date('F Y', strtotime($item->completion_date))); ?></strong></li>
					<?php endif; ?>
					<?php if (!empty($item->payment_plan)) : ?>
						<li class="estate-offplan__plan"><span>Payment plan</span><strong><?php echo nl2br($this->escape($item->payment_plan)); ?></strong></li>
					<?php endif; ?>
				</ul>
			</div>
		<?php endif; ?>

		<div class="estate-detail__description">
			<h2>Description</h2>
			<?php echo nl2br($this->escape($item->description)); ?>
		</div>

		<div class="estate-detail__agent">
			<h3>Listed by</h3>
			<p><strong><?php echo $this->escape($item->agent_name); ?></strong></p>
			<p><?php echo $this->escape($item->agent_phone); ?> &middot; <?php echo $this->escape($item->agent_email); ?></p>
			<?php if (!empty($item->agent_bio)) : ?>
				<p class="estate-detail__agentbio"><?php echo $this->escape($item->agent_bio); ?></p>
			<?php endif; ?>
		</div>
	</div>

	<div class="estate-detail__enquiry">
		<h2>Book a Viewing</h2>
		<p>Tell us a little about yourself and our agent will get back to you to arrange a viewing.</p>
		<form method="post" action="<?php echo Route::_('index.php?option=com_estate&task=listings.saveBooking'); ?>">
			<input type="hidden" name="listing_id" value="<?php echo (int) $item->id; ?>" />
			<input type="hidden" name="return" value="<?php echo $this->escape(Uri::current()); ?>" />
			<?php echo HTMLHelper::_('form.token'); ?>

			<label>Your name <input type="text" name="name" required /></label>
			<label>Email <input type="email" name="email" required /></label>
			<label>Phone <input type="tel" name="phone" /></label>
			<label>Preferred date <input type="date" name="preferred_date" /></label>
			<label>Message <textarea name="message" rows="4"></textarea></label>
			<button type="submit" class="btn-primary">Request Viewing</button>
		</form>
	</div>
</div>

<?php if ($tour) : ?>
	<?php
		$tourConfig = $this->tourConfig($tour);
	?>
	<div id="estate-tour-modal" class="estate-modal" hidden>
		<div class="estate-modal__backdrop" onclick="EstateTour.close()"></div>
		<div class="estate-modal__panel">
			<button type="button" class="estate-modal__close" onclick="EstateTour.close()" aria-label="Close tour">&times;</button>
			<div id="estate-tour-scene" class="estate-modal__scene"></div>
		</div>
	</div>

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css">
	<script type="application/json" id="estate-tour-config"><?php echo $tourConfig; ?></script>
	<script>
	(function () {
		var EstateTour = window.EstateTour = {
			opened: false,
			viewer: null,
			config: null,
			open: function () {
				var modal = document.getElementById('estate-tour-modal');
				if (!modal) {
					return;
				}
				modal.hidden = false;
				document.body.classList.add('estate-modal-open');
				if (this.viewer) {
					this.opened = true;
					return;
				}
				if (!window.pannellum) {
					return;
				}
				this.config = JSON.parse(document.getElementById('estate-tour-config').textContent || '{}');
				this.viewer = window.pannellum.viewer('estate-tour-scene', this.config);
				this.opened = true;
			},
			close: function () {
				var modal = document.getElementById('estate-tour-modal');
				if (modal) {
					modal.hidden = true;
				}
				document.body.classList.remove('estate-modal-open');
				this.opened = false;
			}
		};

		(function loadPannellum() {
			if (window.pannellum) {
				return;
			}
			var s = document.createElement('script');
			s.src = 'https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js';
			s.async = true;
			document.body.appendChild(s);
		})();
	})();
	</script>
<?php endif; ?>