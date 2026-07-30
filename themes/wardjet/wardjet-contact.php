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
                  </div>
                    <div class="col-12 col-sm-3 offset-sm-1 wow animate__fadeIn">
                        <?php the_field('contact_details')?>
                    </div>
              </div>
          </div>
      </div><!-- #main -->
    </section><!-- #primary -->


    <section id="map" class="content-area map">
        <?php
        // Locale-aware map — same grey style + the 4 HQ pins as the former Snazzy
        // embed, but the view follows the URL locale: Europe for pl-pl / en-uk,
        // North America otherwise. Rendered inline so PHP can drive center/zoom.
        $wj_locale = function_exists('lc_get_locale_from_url') ? lc_get_locale_from_url() : 'en-us';
        $wj_eu     = in_array($wj_locale, array('pl-pl', 'en-uk'), true);
        $wj_lat    = $wj_eu ? 51.5 : 43.32027;
        $wj_lng    = $wj_eu ? 8.3  : -79.918754;
        $wj_zoom   = $wj_eu ? 5    : 4;
        ?>
        <div id="wj-map" style="width:100%;height:100%;"></div>
        <script>
        function wjInitContactMap() {
            var map = new google.maps.Map(document.getElementById('wj-map'), {
                center: { lat: <?php echo $wj_lat; ?>, lng: <?php echo $wj_lng; ?> },
                zoom: <?php echo (int) $wj_zoom; ?>,
                mapTypeId: 'roadmap',
                styles: [{"featureType":"all","elementType":"geometry.fill","stylers":[{"weight":"2.00"}]},{"featureType":"all","elementType":"geometry.stroke","stylers":[{"color":"#9c9c9c"}]},{"featureType":"all","elementType":"labels.text","stylers":[{"visibility":"on"}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#f2f2f2"}]},{"featureType":"landscape","elementType":"geometry.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"landscape.man_made","elementType":"geometry.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":-100},{"lightness":45}]},{"featureType":"road","elementType":"geometry.fill","stylers":[{"color":"#eeeeee"}]},{"featureType":"road","elementType":"labels.text.fill","stylers":[{"color":"#7b7b7b"}]},{"featureType":"road","elementType":"labels.text.stroke","stylers":[{"color":"#ffffff"}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#46bcec"},{"visibility":"on"}]},{"featureType":"water","elementType":"geometry.fill","stylers":[{"color":"#c8d7d4"}]},{"featureType":"water","elementType":"labels.text.fill","stylers":[{"color":"#070707"}]},{"featureType":"water","elementType":"labels.text.stroke","stylers":[{"color":"#ffffff"}]}]
            });
            [[43.32027,-79.918754],[41.095767,-81.442984],[52.6685,-2.423438],[50.250391,19.083943]]
                .forEach(function(p){ new google.maps.Marker({ map: map, position: { lat: p[0], lng: p[1] } }); });
        }
        </script>
        <script defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBPpzopXR36Pjl4HUxL6EwJEGL_18vsHxg&callback=wjInitContactMap"></script>
    </section>

    <?php get_template_part('template-parts/agg-contact');?>
</div>

<?php
get_footer();
