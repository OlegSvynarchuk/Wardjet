<?php
/**
* Template Name: Industries page
*/
get_header(); ?>
<?php get_template_part('template-parts/wardjet-secondary-hero', null, ['show_overlay'=>true, 'extra_classes'=>'pb-0 pb-sm-5']);?>

<div id="industries">
    <?php // Reuse the redesigned homepage industries grid (self-contained: queries
          // industries per-locale with en-us fallback, own heading/copy). ?>
    <?php // Hide the grid's "Industries" heading here — redundant with the hero's
          // "Industries We Serve". Homepage keeps its heading (no arg passed there). ?>
    <?php get_template_part('template-parts/ind-mat-grid', null, ['hide_heading' => true]); ?>

    <?php
    get_template_part('template-parts/reviews');
    ?>

    <?php
    get_template_part('template-parts/partnership');
    ?>    

    <?php get_template_part('template-parts/agg-contact');?>
</div>
<?php
get_footer();