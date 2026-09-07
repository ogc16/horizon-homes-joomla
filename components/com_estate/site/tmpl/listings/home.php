<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_estate
 *
 * Presentation template: homepage / landing page.
 *
 * Receives "items" (up to 3 featured listings) and "stats" from the view.
 * Rendering only — no business rules live here.
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;

/** @var \Joomla\Component\Estate\Site\View\Listings\HtmlView $this */
$items = isset($this->items) ? $this->items : [];
$stats = isset($this->stats) ? $this->stats : null;
$filters = isset($this->filters) ? $this->filters : [];

$slides = array_values(array_filter(array_map(fn ($item) => (string) $item->main_image, $items)));
?>

<section class="estate-hero" data-slides='<?php echo $this->escape(json_encode($slides)); ?>'>
	<div class="estate-hero__bg" role="presentation"></div>
	<div class="estate-hero__bg" role="presentation"></div>
	<div class="estate-hero__inner">
		<h1 class="estate-hero__title">Find your next home in Kenya</h1>
		<p class="estate-hero__sub">
			Handpicked houses, apartments, plots and commercial spaces across Nairobi
			and beyond — guided by trusted local agents.
		</p>

		<form class="estate-search" action="<?php echo Route::_('index.php?option=com_estate&view=listings'); ?>" method="get">
			<input type="hidden" name="option" value="com_estate" />
			<input type="hidden" name="view" value="listings" />
			<input type="text" name="q" placeholder="Search by title, city or address" value="<?php echo $this->escape(isset($filters['q']) ? $filters['q'] : ''); ?>" />
			<select name="property_type">
				<option value="">All types</option>
				<option value="house">House</option>
				<option value="apartment">Apartment</option>
				<option value="land">Land</option>
				<option value="commercial">Commercial</option>
			</select>
			<select name="sale_or_rent">
				<option value="">For sale or rent</option>
				<option value="sale">For Sale</option>
				<option value="rent">For Rent</option>
			</select>
			<button type="submit" class="btn-primary">Search</button>
		</form>

		<div class="estate-hero__actions">
			<a class="btn-primary" href="<?php echo Route::_('index.php?option=com_estate&view=listings'); ?>">Browse all properties</a>
			<a class="btn-ghost" href="<?php echo Route::_('index.php?option=com_estate&task=listings.about'); ?>">About us</a>
		</div>
	</div>
</section>

<script>
(function () {
	var hero = document.querySelector('.estate-hero');
	if (!hero) {
		return;
	}

	var slides = [];
	try {
		slides = JSON.parse(hero.getAttribute('data-slides') || '[]');
	} catch (e) {
		slides = [];
	}

	var layers = hero.querySelectorAll('.estate-hero__bg');
	if (!Array.prototype.slice.call(slides).every(Boolean) || slides.length < 2 || layers.length < 2) {
		return;
	}

	var idx = 1;
	var current = layers[0];
	layers[0].style.backgroundImage = "url('" + slides[0] + "')";
	layers[0].classList.add('estate-hero__bg--active');

	setInterval(function () {
		var incoming = (current === layers[0]) ? layers[1] : layers[0];
		incoming.style.backgroundImage = "url('" + slides[idx % slides.length] + "')";
		current.classList.remove('estate-hero__bg--active');
		incoming.classList.add('estate-hero__bg--active');
		current = incoming;
		idx += 1;
	}, 5000);
})();
</script>

<?php if ($stats) : ?>
	<section class="estate-stats">
		<div class="container estate-stats__grid">
			<div class="estate-stat">
				<strong class="estate-stat__value"><?php echo (int) $stats->listings; ?>+</strong>
				<span class="estate-stat__label">Properties listed</span>
			</div>
			<div class="estate-stat">
				<strong class="estate-stat__value"><?php echo (int) $stats->agents; ?></strong>
				<span class="estate-stat__label">Expert agents</span>
			</div>
			<div class="estate-stat">
				<strong class="estate-stat__value"><?php echo (int) $stats->years; ?>+</strong>
				<span class="estate-stat__label">Years in the market</span>
			</div>
			<div class="estate-stat">
				<strong class="estate-stat__value">100%</strong>
				<span class="estate-stat__label">Professional service</span>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php if (!empty($items)) : ?>
	<section class="estate-home-section">
		<div class="container">
			<h2 class="estate-home-section__title">Featured properties</h2>
			<div class="estate-grid">
				<?php foreach ($items as $item) : ?>
					<?php $link = Route::_('index.php?option=com_estate&view=listings&alias=' . $item->alias); ?>
					<article class="estate-card">
						<a class="estate-card__media" href="<?php echo $link; ?>">
							<?php if ($item->main_image) : ?>
								<img src="<?php echo $this->escape($item->main_image); ?>" alt="<?php echo $this->escape($item->title); ?>" loading="lazy" />
							<?php else : ?>
								<span class="estate-card__placeholder">No image</span>
							<?php endif; ?>
							<?php if ($item->featured) : ?>
								<span class="estate-badge estate-badge--featured">Featured</span>
							<?php endif; ?>
						</a>
						<div class="estate-card__body">
							<h3 class="estate-card__title"><a href="<?php echo $link; ?>"><?php echo $this->escape($item->title); ?></a></h3>
							<p class="estate-card__price"><?php echo $this->formatPrice($item->price); ?>
								<span class="estate-card__purpose">/ <?php echo $this->escape(ucfirst($item->sale_or_rent)); ?></span>
							</p>
							<ul class="estate-card__specs">
								<li><?php echo (int) $item->bedrooms; ?> bd</li>
								<li><?php echo (int) $item->bathrooms; ?> ba</li>
								<li><?php echo number_format((int) $item->area_sqft); ?> sqft</li>
							</ul>
							<p class="estate-card__location"><?php echo $this->escape($item->city . ', ' . $item->address); ?></p>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
<?php endif; ?>

<section class="estate-home-section">
	<div class="container">
		<h2 class="estate-home-section__title">Why work with us</h2>
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
	</div>
</section>

<section class="estate-cta">
	<div class="container estate-cta__inner">
		<h2>Ready to find the right property?</h2>
		<p>Talk to an agent today or browse what is currently available.</p>
		<div class="estate-hero__actions">
			<a class="btn-primary" href="<?php echo Route::_('index.php?option=com_estate&view=listings'); ?>">Browse properties</a>
			<a class="btn-ghost" href="<?php echo Route::_('index.php?option=com_estate&task=listings.about'); ?>">Contact agents</a>
		</div>
	</div>
</section>