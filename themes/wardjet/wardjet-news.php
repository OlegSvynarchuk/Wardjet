<?php
/**
* Template Name: News & Events page
*/

get_header(); ?>
<div id="news">
    <?php get_template_part('template-parts/wardjet-secondary-hero', null, ['title_align'=>'center', 'show_overlay'=>true]);?>


    <section class="blog-news-events">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <?php
                    //get all industries
                    //order by name

                    $args = array(
                        'post_type' => 'post',
                        'post_status' => 'publish',
                        'posts_per_page' => 10,
                        'page' => $page,
                        'orderby' => 'date',
                        'order' => 'DESC'
                    );

                    $loop = new WP_Query( $args );

                    while ( $loop->have_posts() ) :
                        $loop->the_post();
                        $img = get_field('mosaic_icon');
                        ?>
                        <div class="news-item">
                            <div class="news-item-thumbnail">
                                <?=get_the_post_thumbnail()?>
                            </div>
                            <div class="news-item-details">
                                <h4><a href="<?=get_permalink()?>"><?=get_the_title()?></a></h4>
                                <ul class="news-meta">
                                    <li><i class="far fa-calendar-alt"></i><?=get_the_date()?></li>
                                </ul>
                                <?php the_excerpt()?>
                            </div>
                        </div>
                    <?php
                    endwhile;
                    wp_reset_postdata();
                    ?>
                    <div class="pagination">
                        <?php
                        echo paginate_links( array(
                            'base'         => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
                            'total'        => $loop->max_num_pages,
                            'current'      => max( 1, get_query_var( 'paged' ) ),
                            'format'       => '?paged=%#%',
                            'show_all'     => false,
                            'type'         => 'plain',
                            'end_size'     => 2,
                            'mid_size'     => 1,
                            'prev_next'    => true,
                            'prev_text'    => sprintf( '<i></i> %1$s', __( '&lt;&lt;', 'text-domain' ) ),
                            'next_text'    => sprintf( '%1$s <i></i>', __( '&gt;&gt;', 'text-domain' ) ),
                            'add_args'     => false,
                            'add_fragment' => '',
                        ) );
                        ?>
                    </div>
                </div>

                <?php get_sidebar(); ?>

            </div>

        </div>
    </section>


    <?php get_template_part('template-parts/agg-contact');?>
</div>
<?php
get_footer();