<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_estate
 *
 * Presentation template: single listing detail + viewing-enquiry form.
 */

defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

$item       = isset($this->item) ? $this->item : null;
$gallery    = $item ? $this->getModel()->getGallery($item) : array();
$imageIndex = 0;
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
	</div>

	<div class="estate-detail__info">
		<h1 class="estate-detail__title"><?php echo $this->escape($item->title); ?></h1>
		<p class="estate-detail__price"><?php echo \EstateViewListings::formatPrice($item->price); ?>
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
			<?php echo \Joomla\CMS\HTML\HTMLHelper::_('form.token'); ?>

			<label>Your name <input type="text" name="name" required /></label>
			<label>Email <input type="email" name="email" required /></label>
			<label>Phone <input type="tel" name="phone" /></label>
			<label>Preferred date <input type="date" name="preferred_date" /></label>
			<label>Message <textarea name="message" rows="4"></textarea></label>
			<button type="submit" class="btn-primary">Request Viewing</button>
		</form>
	</div>
</div>
