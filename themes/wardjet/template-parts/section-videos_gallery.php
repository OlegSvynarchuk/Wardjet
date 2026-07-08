<?php $block = $args['block']; ?>
<section class="videos-gallery">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <?php
                $catsArray = $block['selected_sections'];
                $sections = get_terms( 'section', array(
                    'hide_empty' => false,
                    'include' => $catsArray,
                ) );
                if ($sections): ?>
                <div class="sections-list">
                    <ul>
                        <li class="active"><a href="#" class="all">All</a></li>
                        <?php foreach($sections AS $section): ?>
                        <li><a href="#" class="<?php echo $section->slug; ?>"><?php echo $section->name; ?></a></li>
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
                        'terms' => $catsArray
                    )
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
                    <div class="col-sm-4 <?php echo $terms[0]->slug; ?>">
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