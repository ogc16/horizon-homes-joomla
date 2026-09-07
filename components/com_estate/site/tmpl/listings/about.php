<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_estate
 *
 * Presentation template: about / the company page.
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;

/** @var \Joomla\Component\Estate\Site\View\Listings\HtmlView $this */
?>
<section class="estate-about">
	<h1>Horizon Homes Real Estate</h1>
	<p class="estate-about__lead">
		For over a decade we have connected Kenyan families, professionals and investors
		with their dream properties. From leafy Karen villas to smart Westlands apartments
		and industrial land — we understand the market and we put you first.
	</p>

	<h2>Why buy with us</h2>
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

	<p class="estate-about__cta">
		<a href="<?php echo Route::_('index.php?option=com_estate&view=listings'); ?>" class="btn-primary">Browse Properties</a>
	</p>
</section>