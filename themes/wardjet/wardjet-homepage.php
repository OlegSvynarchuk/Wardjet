<?php
/**
 * Template Name: Homepage
 *
 * Section-based homepage, rebuilt from the axyz blueprint. Sections are added
 * one-by-one as their template-part / CSS / ACF are ported to wardjet.
 *
 * Ported sections:
 *   - Video hero      (template-parts/hero-video-carousel)
 *   - Features slider (template-parts/features-slider)
 *
 * (The previous inline homepage is backed up on the server as
 *  wardjet-homepage.php.bak-newhome-*.)
 */

get_header(); ?>
<div id="homepage">

    <?php get_template_part('template-parts/hero-video-carousel'); ?>

    <?php get_template_part('template-parts/features-slider'); ?>

    <?php get_template_part('template-parts/icon-strip'); ?>

    <?php get_template_part('template-parts/products-section'); ?>

    <?php get_template_part('template-parts/new-kpis'); ?>

    <?php get_template_part('template-parts/ind-mat-grid'); ?>

    <?php get_template_part('template-parts/our-companies'); ?>

    <?php get_template_part('template-parts/partnership'); ?>

    <?php /* Next sections go here, one at a time as they are ported. */ ?>

</div>
<?php
get_footer();
