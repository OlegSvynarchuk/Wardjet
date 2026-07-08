<?php
/**
 * Template Name: Contact Page
 */

get_header();
?>


<div id="contact">

    <!--<?php get_template_part('template-parts/wardjet-secondary-hero', null, ['show_overlay'=>true]);?>-->




    <section id="" class="content-area contactform">
        <div id="" class="site-main" role="main">
            <div class="container">
                <div class="row">

                    <div class="col-12 col-sm-8">
                        <div class=" wow animate__fadeIn">
                            <h3 class="heading"><?php the_field('contact_heading'); ?></h3>
                            <?php the_field('contact_description'); ?>
                        </div>
                        <div style="width: 100%; overflow: visible;">
						<script type="text/javascript" src="<?=get_field('contact_id')?>"></script>
						</div>
<!--                         <?php
                        gravity_form( 3, false, false, false, '', true, 12 );
                        ?>

                        <?php 
                        $label_link = get_field('contact_privacy_label_link');
                        if( $label_link ): 
                            $label_link_url = $label_link['url'];
                            $label_link_title = $label_link['title'];
                            $label_link_target = $label_link['target'] ? $label_link['target'] : '_self';
                            ?>
                            <script type="text/javascript">
                             jQuery.noConflict();
                             jQuery(document).ready(function($) {
                              $(".gchoice_3_7_1 label").html('<?php the_field('contact_privacy_label'); ?>  <a href="<?php echo esc_url( $label_link_url ); ?>" target="<?php echo esc_attr( $label_link_target ); ?>"> <?php echo esc_html( $label_link_title ); ?> </a>');
                              });
                              </script>
                        <?php endif; ?> -->
                  </div>
                    <div class="col-12 col-sm-3 offset-sm-1 wow animate__fadeIn">
                        <?php the_field('contact_details')?>
                    </div>
              </div>
          </div>
      </div><!-- #main -->
    </section><!-- #primary -->


    <section id="map" class="content-area map">
        <iframe src="<?=get_field('snazzy_maps_url')?>" width="100%" height="100%" style="border:none;"></iframe>
    </section>

    <?php get_template_part('template-parts/agg-contact');?>
</div>

<?php
get_footer();
