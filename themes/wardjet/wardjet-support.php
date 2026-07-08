<?php
/**
 * Template Name: Support page
 */

get_header();
?>
<div id="support">
    <?php get_template_part('template-parts/wardjet-secondary-hero', null, ['title_align'=>'center', 'show_overlay'=>true]);?>
    <section id="" class="content-area about mt-5">
        <div id="" class="site-main" role="main">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12"><?php the_field('intro_text'); ?></div>
                </div>
            </div>
        </div>
    </section>

    <section id="" class="content-area">
        <div id="" class="site-main" role="main">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                    <h1 class="heading text-left"><?php the_field('heading'); ?></h1>
                    <?php
                        gravity_form( get_field('form_id'), false, false, false, '', true, 12 );
                    ?>
                </div>
                </div>
            </div>
        </div><!-- #main -->
    </section><!-- #primary -->

<?php get_template_part('template-parts/agg-contact');?>
</div>

<?php
get_footer();
