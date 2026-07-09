<?php
/**
* Template Name: Industries page
*/
get_header(); ?>
<?php get_template_part('template-parts/wardjet-secondary-hero', null, ['show_overlay'=>true, 'extra_classes'=>'pb-0 pb-sm-5']);?>

<div id="industries">
    <section class="dark pb-5 pt-5 pt-sm-0">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-sm-12">
                    <div class="row no-gutters justify-content-center align-items-center row-eq-height">
                    <?php 
                    //get all industries
                    //order by name

                    // Filter industries to the current locale (each locale has its
                    // own 9 industry posts).
                    $ind_locale = 'en-us';
                    if ( function_exists('lc_get_locale_from_url') ) {
                        $ind_locale = lc_get_locale_from_url();
                    } elseif ( function_exists('wj_get_current_locale_code') ) {
                        $ind_locale = wj_get_current_locale_code();
                    }

                    $args = array(
                        'post_type' => 'industry',
                        'post_status' => 'publish',
                        'posts_per_page' => 100,
                        'orderby' => 'title',
                        'order' => 'ASC',
                        'meta_query' => array(
                            array( 'key' => 'region_language_code', 'value' => $ind_locale ),
                        ),
                    );

                    $loop = new WP_Query( $args ); 

                    while ( $loop->have_posts() ) : 
                        $loop->the_post(); 
                        $img = get_field('mosaic_icon');
                    ?>
                        <div class="col-4 col-sm-4 col-industry" style="flex-grow: 1;">
                            <div class="industry-icon position-relative">
                                <a href="<?=get_permalink()?>">
                                    <figure class="position-relative" style="margin:0 auto">
                                     <img class="img-fluid" src="<?php echo esc_url($img); ?>" alt="" />
                                        <figcaption>
                                            <div>
                                                <p class="industry-icon-title"><?=get_the_title()?></p>
                                            </div>
                                        </figcaption>
                                    </figure>
                                </a>
                            </div>
                        </div>
                    <?php 
                    endwhile;
                    wp_reset_postdata();
                    ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

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