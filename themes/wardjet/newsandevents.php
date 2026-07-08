<?php
/**
* Template Name: News and Events

*/

get_header(); ?>
<div id="news">
    <?php get_template_part('template-parts/wardjet-secondary-hero', null, ['title_align'=>'center', 'show_overlay'=>true]);?>

<div id="primary" class="container">
    <main id="main" class="site-main">
        <div class="webinars-archive-wrapper">
			
            <div class="webinars-filters">
        <div class="facetwp-facet facet-search">
            <?php echo do_shortcode('[facetwp facet="search_webinar"]'); ?>
        </div>
        
		<div class="facetwp-facet facettags facet-content-type">
			 <h2>Events</h2>
            <?php echo do_shortcode('[facetwp facet="events"]'); ?>
		</div>
		<div class="facetwp-facet facet-custom-taxonomy facet-content-type">
			<h2>News</h2>
            <?php echo do_shortcode('[facetwp facet="news"]'); ?>
        </div>
				<div class="facetwp-resett">
<span class="filters-reset">
                <a href="javascript: void(0);" onclick="FWP.reset()">Reset All Filters</a>
              </span></div>
            </div>


<div class="webinars-content">
    <div class="facetwp-template">
        <div class="webinar-items">
            <?php
            // Custom WP_Query for webinars — filtered to the current locale (+ en-us fallback)
            $wj_locale  = function_exists('lc_get_locale_from_url') ? lc_get_locale_from_url() : 'en-us';
            $wj_locales = ($wj_locale !== 'en-us') ? array($wj_locale, 'en-us') : array('en-us');
            $query = new WP_Query( array(
                'post_type' => 'news_and_events',
                'posts_per_page' => 10,
                'facetwp' => true, // Ensure FacetWP is enabled for this query
                'meta_query' => array(
                    array( 'key' => 'region_language_code', 'value' => $wj_locales, 'compare' => 'IN' )
                ),
            ) );

            if ( $query->have_posts() ) :
                // Start the loop
                while ( $query->have_posts() ) : $query->the_post();
            ?>
                <div class="webinar-item">
                    <div class="webinar-image">
						<a href="<?php the_permalink(); ?>"> <?php the_post_thumbnail('full'); ?></a>
                    </div>
                    <div class="webinar-info">

							
						
						<div class="webinar-title"><a href="<?php the_permalink(); ?>"><h2><?php the_title(); ?></h2></a></div>
                        <div class="webinar-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 20, ' '); ?></div>
                        <div class="webinar-read-more"><a href="<?php the_permalink(); ?>">Read More</a></div>
                    </div>
                </div>
            <?php
                endwhile;
                wp_reset_postdata();
            else :
                echo '<p>No News or Events found.</p>';
            endif;
            ?>
        </div>

    </div>
               
                <div class="facetwp-load-moree">
                    		            <?php echo do_shortcode('[facetwp facet="load_more"]'); ?>

                </div>
            </div>
        </div>
    </main><!-- #main -->
</div><!-- #primary -->

    <?php get_template_part('template-parts/agg-contact');?>
</div>
<?php
get_footer();