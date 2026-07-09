<?php
/**
 * Template Name: Careers page
 */

get_header();
?>


<div id="careers">

    <?php get_template_part('template-parts/wardjet-secondary-hero', null, ['title_align'=>'center', 'show_overlay'=>true]);?>


    <section id="form-area" class="content-area">
        <div class="container">
            <div class="row career-form wow animate__fadeIn">
                <div class="col-lg-12">
                <h1 class="heading text-left"><?php the_field('heading'); ?></h1>
                <?php
                    // Per-locale form: translated Careers copy set via ACF `form_id`
                    // on the localized page; falls back to the English form (#2).
                    $career_form_id = get_field('form_id') ? (int) get_field('form_id') : 2;
                    gravity_form( $career_form_id, false, false, false, '', true, 12 );
                    ?>
                    <?php 
                    $label_link = get_field('privacy_link');
                    if( $label_link ): 
                        $label_link_url = $label_link['url'];
                        $label_link_title = $label_link['title'];
                        $label_link_target = $label_link['target'] ? $label_link['target'] : '_self';
                        ?>
                        <script type="text/javascript">
                         jQuery.noConflict();
                         jQuery(document).ready(function($) {

                          $(".gchoice_<?php echo (int) $career_form_id; ?>_7_1 label").html('<?php the_field('privacy_label'); ?>  <a href="<?php echo esc_url( $label_link_url ); ?>" target="<?php echo esc_attr( $label_link_target ); ?>"> <?php echo esc_html( $label_link_title ); ?> </a>');

                      });
                  </script>
              <?php endif; ?>
                </div>
            </div>
        </div>
    </section><!-- #primary -->

<?php get_template_part('template-parts/agg-contact');?>
</div>

<?php
get_footer();
