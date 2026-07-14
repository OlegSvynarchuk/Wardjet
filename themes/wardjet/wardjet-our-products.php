<?php
/**
* Template Name: Our Products page
*
* Products page composition (ported 1:1 from blueprint, matching Figma "Product Page"):
* promo-video hero -> Featured Products grid -> icon strip -> KPIs -> gallery -> reviews -> contact.
*/

get_header();

$current_lang_code  = function_exists('lc_get_locale_from_url') ? lc_get_locale_from_url() : 'en-us';
$fallback_lang_code = 'en-us';
?>
<div id="our-products">
	<?php get_template_part('template-parts/hero-video-carousel'); ?>

	<?php get_template_part('template-parts/products-section', null, array('posts_per_page' => -1, 'hide_cta_button' => true)); ?>

	<?php get_template_part('template-parts/icon-strip'); ?>

	<?php get_template_part('template-parts/new-kpis'); ?>

	<?php
	$bgs = get_field('gallery');
	if ($bgs && count($bgs) > 0):
	?>
	<section class="full-width-gallery">
		<div id="industrySingleCarousel" class="carousel slide" data-ride="ride">
			<div class="carousel-inner">
				<?php foreach ($bgs as $idx => $bg): ?>
				<div class="carousel-item <?= ($idx == 0) ? ' active' : '' ?>" style="background-image: url(<?= $bg['url'] ?>);"></div>
				<?php endforeach; ?>
			</div>
			<div class="container mt-5 mb-5">
				<div class="row justify-content-center">
					<div class="col-sm-1">
						<a href="#industrySingleCarousel" role="button" data-slide="prev">
							<img src="/wp-content/themes/wardjet/inc/assets/img/left.svg"/>
						</a>
					</div>
					<div class="col-sm-1 text-center">
						<span class="current-slide">1</span> of <?= count($bgs) ?>
					</div>
					<div class="col-sm-1">
						<a href="#industrySingleCarousel" role="button" data-slide="next">
							<img src="/wp-content/themes/wardjet/inc/assets/img/right.svg"/>
						</a>
					</div>
				</div>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php get_template_part('template-parts/reviews'); ?>

	<?php get_template_part('template-parts/agg-contact'); ?>
</div>
<?php
get_footer();
