<?php $block = $args['block']; ?>
<section class="videos-gallery">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <?php
                $catsArray = (array) $block['selected_sections'];

                // The `section` taxonomy has a separate term per locale (all sharing
                // the English name). Resolve the selected section NAMES so we can pull
                // the current locale's own terms/videos regardless of which locale's
                // term IDs the block stored.
                $sel_names = array();
                foreach ( $catsArray as $t ) {
                    $tid  = is_object($t) ? $t->term_id : $t;
                    $term = get_term( $tid, 'section' );
                    if ( $term && ! is_wp_error($term) ) { $sel_names[ $term->name ] = true; }
                }
                $sel_names = array_keys( $sel_names );

                // Current locale (en-ca / en-uk have no localized videos → en-us).
                $vid_locale = 'en-us';
                if ( function_exists('lc_get_locale_from_url') ) {
                    $vid_locale = lc_get_locale_from_url();
                } elseif ( function_exists('wj_get_current_locale_code') ) {
                    $vid_locale = wj_get_current_locale_code();
                }
                if ( in_array( $vid_locale, array('en-ca','en-uk'), true ) ) { $vid_locale = 'en-us'; }

                // Every section term (any locale) sharing one of the selected names.
                $all_term_ids = $catsArray;
                if ( $sel_names ) {
                    $matched = get_terms( array( 'taxonomy' => 'section', 'hide_empty' => false, 'name' => $sel_names ) );
                    if ( $matched && ! is_wp_error($matched) ) { $all_term_ids = wp_list_pluck( $matched, 'term_id' ); }
                }

                if ($sel_names): ?>
                <div class="sections-list">
                    <ul>
                        <li class="active"><a href="#" class="all">All</a></li>
                        <?php foreach($sel_names AS $section_name): ?>
                        <li><a href="#" class="<?php echo sanitize_title($section_name); ?>"><?php echo esc_html($section_name); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="row" id="videos-area">
            <?php
            $args = array(
                'post_type' => array( 'video' ),
                'posts_per_page' => -1,
                'tax_query' => array(
                    array(
                        'taxonomy' => 'section',
                        'field' => 'id',
                        'terms' => $all_term_ids
                    )
                ),
                'meta_query' => array(
                    array( 'key' => 'region_language_code', 'value' => $vid_locale )
                ),
            );
            $q = new WP_Query( $args );
            if( $q->have_posts() ) :
                while( $q->have_posts() ) :
                    $q->the_post();
                    $terms = get_the_terms( get_the_ID(), 'section' );
                    $video = get_field('video', get_the_ID(), false);
                    
                    // Debugging output for video URL
                    echo '<!-- Debug: Video URL - ' . $video . ' -->';
                    
                    // Initialize variables for video ID and thumbnail URL
                    $video_id = '';
                    $thumbnail_url = '';
                    
                    // Extract YouTube video ID from video URL
                    if (preg_match("/(?:https?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user|shorts)\/))([^\?&\"'>]+)/", $video, $matches)) {
                        $video_id = $matches[1];
                        
                        // Debugging output for extracted video ID
                        echo '<!-- Debug: Video ID - ' . $video_id . ' -->';
                        
                        // Construct thumbnail URL using video ID
                        $thumbnail_url = 'https://img.youtube.com/vi/' . $video_id . '/hqdefault.jpg';
                        
                        // Debugging output for thumbnail URL
                        echo '<!-- Debug: Thumbnail URL - ' . $thumbnail_url . ' -->';
                    }
            ?>
                    <div class="col-sm-4 <?php echo ($terms && !is_wp_error($terms)) ? sanitize_title($terms[0]->name) : ''; ?>">
                        <div class="video">
                            <div class="placeholder">
                                <a href="#" class="open-youtube-modal" data-title="<?php echo get_the_title(); ?>" data-youtube-id="<?php echo $video_id; ?>">
                                    <img src="<?php echo $thumbnail_url; ?>" alt="Thumbnail" />
                                    <i class="fas fa-play"></i>
                                </a>
                            </div>
                            <div class="title">
                                <a href="#" class="open-youtube-modal" data-title="<?php echo get_the_title(); ?>" data-youtube-id="<?php echo $video_id; ?>"><?php the_title(); ?></a>
                            </div>
                            <div class="subtitle">
                                <?php the_content(); ?>
                            </div>
                        </div>
                    </div>
            <?php endwhile; endif; ?>
        </div>
    </div>
</section>


<div class="modal fade youtube-video-modal" id="video-modal" tabindex="-1" aria-labelledby="video-modal" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="embed-responsive embed-responsive-16by9">

                </div>
            </div>
        </div>
    </div>
</div>