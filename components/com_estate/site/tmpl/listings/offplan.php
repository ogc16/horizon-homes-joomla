<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_estate
 *
 * Presentation template: off-plan investment opportunities page.
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;

/** @var \Joomla\Component\Estate\Site\View\Listings\HtmlView $this */
?>
<div class="estate-page">
	<h1>Off-Plan Investment Opportunities</h1>
	<p class="estate-page__lead">
		Buy a home or unit before it is built, lock in today&rsquo;s price and pay through construction.
		Off-plan buyers typically see the strongest appreciation and can pre-let before handover.
	</p>

	<div class="estate-notice">
		<p><strong>What does off-plan mean?</strong> The developer sells units from the plans before completion.
		You reserve with a deposit, then pay in staged instalments. Prices here are quoted as per the listed
		developer payment plans &mdash; always check the developer&rsquo;s sales agreement before committing.</p>
	</div>

	<?php if (empty($this->items)) : ?>
		<p class="estate-empty">No off-plan opportunities are available right now. Check back soon, or browse our
			ready properties.</p>
	<?php else : ?>
		<div class="estate-grid">
			<?php foreach ($this->items as $item) : ?>
				<?php $link = Route::_('index.php?option=com_estate&view=listings&alias=' . $item->alias); ?>
				<article class="estate-card">
					<a class="estate-card__media" href="<?php echo $link; ?>">
						<?php if ($item->main_image) : ?>
							<img src="<?php echo $this->escape($item->main_image); ?>" alt="<?php echo $this->escape($item->title); ?>" loading="lazy" />
						<?php else : ?>
							<span class="estate-card__placeholder">No image</span>
						<?php endif; ?>
						<span class="estate-badge estate-badge--offplan">Off-plan</span>
					</a>
					<div class="estate-card__body">
						<h3 class="estate-card__title"><a href="<?php echo $link; ?>"><?php echo $this->escape($item->title); ?></a></h3>
						<p class="estate-card__price"><?php echo $this->formatPrice($item->price, $item->currency ?? 'KSH'); ?>
							<span class="estate-card__purpose">/ <?php echo $this->escape(ucfirst($item->sale_or_rent)); ?></span>
						</p>
						<ul class="estate-card__specs">
							<?php if (!empty($item->developer)) : ?>
								<li><?php echo $this->escape($item->developer); ?></li>
							<?php endif; ?>
							<?php if ($item->completion_date && $item->completion_date !== '0000-00-00') : ?>
								<li>Complete <?php echo $this->escape(date('M Y', strtotime($item->completion_date))); ?></li>
							<?php endif; ?>
							<li><?php echo (int) $item->bedrooms; ?> bd</li>
							<li><?php echo (int) $item->bathrooms; ?> ba</li>
						</ul>
						<p class="estate-card__location"><?php echo $this->escape($item->city . ', ' . $item->address); ?></p>
						<?php if (!empty($item->payment_plan)) : ?>
							<p class="estate-card__plan"><?php echo $this->escape($item->payment_plan); ?></p>
						<?php endif; ?>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<p class="estate-page__foot"><a href="<?php echo Route::_('index.php?option=com_estate&view=listings'); ?>">&larr; Back to all properties</a></p>
</div>