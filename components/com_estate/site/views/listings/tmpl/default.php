<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_estate
 *
 * Presentation template: default listings gallery.
 */

defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;

$filters = isset($this->filters) ? $this->filters : array();
?>
<form class="estate-search" action="<?php echo Route::_('index.php?option=com_estate&view=listings'); ?>" method="get">
	<input type="hidden" name="option" value="com_estate" />
	<input type="hidden" name="view" value="listings" />
	<input type="text" name="q" placeholder="Search by title, city or address" value="<?php echo $this->escape(isset($filters['q']) ? $filters['q'] : ''); ?>" />
	<select name="city">
		<option value="">All cities</option>
		<option value="Nairobi" <?php echo (isset($filters['city']) && $filters['city'] == 'Nairobi') ? 'selected' : ''; ?>>Nairobi</option>
		<option value="Machakos" <?php echo (isset($filters['city']) && $filters['city'] == 'Machakos') ? 'selected' : ''; ?>>Machakos</option>
	</select>
	<select name="property_type">
		<option value="">All types</option>
		<option value="house" <?php echo (isset($filters['property_type']) && $filters['property_type'] == 'house') ? 'selected' : ''; ?>>House</option>
		<option value="apartment" <?php echo (isset($filters['property_type']) && $filters['property_type'] == 'apartment') ? 'selected' : ''; ?>>Apartment</option>
		<option value="land" <?php echo (isset($filters['property_type']) && $filters['property_type'] == 'land') ? 'selected' : ''; ?>>Land</option>
		<option value="commercial" <?php echo (isset($filters['property_type']) && $filters['property_type'] == 'commercial') ? 'selected' : ''; ?>>Commercial</option>
	</select>
	<select name="sale_or_rent">
		<option value="">For sale or rent</option>
		<option value="sale" <?php echo (isset($filters['sale_or_rent']) && $filters['sale_or_rent'] == 'sale') ? 'selected' : ''; ?>>For Sale</option>
		<option value="rent" <?php echo (isset($filters['sale_or_rent']) && $filters['sale_or_rent'] == 'rent') ? 'selected' : ''; ?>>For Rent</option>
	</select>
	<button type="submit" class="btn-primary">Search</button>
</form>

<?php if (empty($this->items)) : ?>
	<p class="estate-empty">No properties match your search. Please try different criteria.</p>
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
				<?php if ($item->featured) : ?>
					<span class="estate-badge estate-badge--featured">Featured</span>
				<?php endif; ?>
			</a>
			<div class="estate-card__body">
				<h3 class="estate-card__title"><a href="<?php echo $link; ?>"><?php echo $this->escape($item->title); ?></a></h3>
				<p class="estate-card__price"><?php echo \EstateViewListings::formatPrice($item->price); ?>
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
<?php endif; ?>
