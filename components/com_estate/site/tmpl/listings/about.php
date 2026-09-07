<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_estate
 *
 * Presentation template: about / the company page.
 *
 * Receives the agent list ("agents") and figures ("stats") from the view.
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;

/** @var \Joomla\Component\Estate\Site\View\Listings\HtmlView $this */
$agents = isset($this->agents) ? $this->agents : [];
$stats  = isset($this->stats) ? $this->stats : null;
?>
<section class="estate-about">
	<h1>About Horizon Homes</h1>
	<p class="estate-about__lead">
		For over a decade we have connected Kenyan families, professionals and investors
		with their dream properties. From leafy Karen villas to smart Westlands apartments
		and industrial land — we understand the market and we put you first.
	</p>

	<div class="estate-about__story">
		<p>
			Horizon Homes started as a two-agent brokerage in Nairobi with one simple belief:
			real estate should be transparent, honest and kind to those navigating it. Today that
			same belief guides every viewing, valuation and title transfer we handle.
		</p>
		<p>
			We combine local area knowledge with a disciplined, data-driven approach, so the
			price you see reflects the real market — and the property you buy is the property
			you were promised. Whether you are buying your first home, upgrading as a family
			grows, or placing capital in commercial land, our agents walk with you from first
			search to handing over the keys.
		</p>
	</div>

	<?php if ($stats) : ?>
		<div class="estate-stats estate-stats--inline">
			<div class="estate-stat">
				<strong class="estate-stat__value"><?php echo (int) $stats->years; ?>+</strong>
				<span class="estate-stat__label">Years in the market</span>
			</div>
			<div class="estate-stat">
				<strong class="estate-stat__value"><?php echo (int) $stats->listings; ?>+</strong>
				<span class="estate-stat__label">Properties listed</span>
			</div>
			<div class="estate-stat">
				<strong class="estate-stat__value"><?php echo (int) $stats->featured; ?></strong>
				<span class="estate-stat__label">Featured homes</span>
			</div>
			<div class="estate-stat">
				<strong class="estate-stat__value"><?php echo (int) $stats->agents; ?></strong>
				<span class="estate-stat__label">Dedicated agents</span>
			</div>
		</div>
	<?php endif; ?>

	<h2>Our values</h2>
	<div class="estate-about__columns">
		<div class="estate-about__col">
			<h3>Trusted Agents</h3>
			<p>Every listing is managed by a licensed, vetted consultant who lives and works in your area.</p>
		</div>
		<div class="estate-about__col">
			<h3>Honest Pricing</h3>
			<p>Transparent valuations and no hidden fees. The price you see is the price you pay.</p>
		</div>
		<div class="estate-about__col">
			<h3>End to End Support</h3>
			<p>From first viewing to signing the title deed, we walk with you the whole way.</p>
		</div>
	</div>

	<?php if (!empty($agents)) : ?>
		<h2>Meet the team</h2>
		<div class="estate-team">
			<?php foreach ($agents as $agent) : ?>
				<div class="estate-team__card">
					<span class="estate-team__avatar"><?php echo $this->escape(substr($agent->name, 0, 1)); ?></span>
					<h3><?php echo $this->escape($agent->name); ?></h3>
					<p class="estate-team__contact">
						<?php echo $this->escape($agent->phone); ?> &middot; <?php echo $this->escape($agent->email); ?>
					</p>
					<?php if (!empty($agent->bio)) : ?>
						<p class="estate-team__bio"><?php echo $this->escape($agent->bio); ?></p>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<p class="estate-about__cta">
		<a href="<?php echo Route::_('index.php?option=com_estate&view=listings'); ?>" class="btn-primary">Browse Properties</a>
	</p>
</section>