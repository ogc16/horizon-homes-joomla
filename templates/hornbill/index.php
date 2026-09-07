<?php

/**
 * @package     Joomla.Site
 * @subpackage  Templates.hornbill
 *
 * @copyright   Copyright (C) Horizon Homes Real Estate.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<jdoc:include type="head" />
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/hornbill/css/template.css">
</head>
<body class="site <?php echo $this->params->get('pageclass_sfx', ''); ?>">
	<header class="site-header">
		<div class="container site-header__inner">
			<a class="brand" href="<?php echo $this->baseurl; ?>">
				<span class="brand__mark">HH</span>
				<span class="brand__name">Horizon Homes</span>
			</a>
			<nav class="main-nav" id="main-nav">
				<div class="main-nav__burger" onclick="document.getElementById('main-nav').classList.toggle('open')">☰</div>
				<ul class="main-nav__list">
					<jdoc:include type="modules" name="menu" style="none" />
				</ul>
			</nav>
		</div>
	</header>

	<main class="site-main">
		<jdoc:include type="message" />
		<jdoc:include type="component" />
	</main>

	<footer class="site-footer">
		<div class="container site-footer__grid">
			<div class="site-footer__col">
				<h4>Horizon Homes</h4>
				<p>Trusted real estate brokerage connecting families, professionals and investors with the right property.</p>
			</div>
			<div class="site-footer__col">
				<h4>Contact</h4>
				<p>info@horizonhomes.ke</p>
				<p>+254 700 123 456</p>
				<p>Miotoni Lane, Karen, Nairobi</p>
			</div>
			<div class="site-footer__col">
				<h4>Quick Links</h4>
				<ul>
					<li><a href="<?php echo Route::_('index.php'); ?>">Home</a></li>
					<li><a href="<?php echo Route::_('index.php?option=com_estate&view=listings'); ?>">Properties</a></li>
					<li><a href="<?php echo Route::_('index.php?option=com_estate&task=listings.about'); ?>">About Us</a></li>
				</ul>
			</div>
		</div>
		<div class="container site-footer__bottom">
			&copy; <?php echo date('Y'); ?> Horizon Homes Real Estate. All rights reserved.
		</div>
	</footer>
</body>
</html>