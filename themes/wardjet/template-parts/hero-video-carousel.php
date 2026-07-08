<?php
/**
 * Hero Video Carousel Template Part
 *
 * Displays a full-width video carousel with auto-play and auto-advance.
 * Uses self-hosted MP4 videos with overlay text.
 *
 * ACF Fields (video_carousel repeater):
 * - video_file: File (MP4 video)
 * - video_title: Text (overlay text)
 * - video_poster: Image (optional poster/thumbnail)
 */

if (!defined('ABSPATH')) {
    exit;
}

// Check if video carousel has slides
if (!have_rows('video_carousel')) {
    return;
}

// Count total slides
$total_slides = count(get_field('video_carousel'));
?>

<section class="content-area hero-video-carousel">
    <div class="site-main" role="main">
        <div id="heroVideoCarousel" class="carousel slide" data-ride="false" data-interval="false">
            <!-- Progress Dots -->
            <?php if ($total_slides > 1) : ?>
            <ol class="carousel-indicators">
                <?php
                $slide_index = 0;
                while (have_rows('video_carousel')) :
                    the_row();
                ?>
                    <li data-target="#heroVideoCarousel" data-slide-to="<?php echo esc_attr($slide_index); ?>" class="<?php echo $slide_index === 0 ? 'active' : ''; ?>"></li>
                <?php
                    $slide_index++;
                endwhile;
                reset_rows();
                ?>
            </ol>
            <?php endif; ?>

            <!-- Video Slides -->
            <div class="carousel-inner">
                <?php
                $slide_index = 0;
                while (have_rows('video_carousel')) :
                    the_row();

                    $video_file = get_sub_field('video_file');
                    $video_file_mobile = get_sub_field('video_file_mobile');
                    $video_title = get_sub_field('video_title');
                    $video_poster = get_sub_field('video_poster');

                    $video_url = is_array($video_file) ? $video_file['url'] : $video_file;
                    $mobile_url = '';
                    if ($video_file_mobile) {
                        $mobile_url = is_array($video_file_mobile) ? $video_file_mobile['url'] : $video_file_mobile;
                    }
                    $poster_url = '';
                    if ($video_poster) {
                        $poster_url = is_array($video_poster) ? $video_poster['url'] : $video_poster;
                    }
                ?>
                    <div class="carousel-item <?php echo $slide_index === 0 ? 'active' : ''; ?>">
                        <div class="video-container">
                            <video
                                class="hero-video"
                                autoplay
                                muted
                                loop
                                playsinline
                                preload="auto"
                                <?php if ($poster_url) : ?>
                                poster="<?php echo esc_url($poster_url); ?>"
                                <?php endif; ?>
                            >
                                <?php if ($mobile_url) : ?>
                                <source src="<?php echo esc_url($mobile_url); ?>" type="video/mp4" media="(max-width: 767px)">
                                <?php endif; ?>
                                <source src="<?php echo esc_url($video_url); ?>" type="video/mp4">
                                <?php esc_html_e('Your browser does not support the video tag.', 'axyz'); ?>
                            </video>

                            <!-- Dark Overlay -->
                            <div class="video-dark-overlay"></div>

                            <?php if ($video_title) : ?>
                            <div class="video-overlay">
                                <h2 class="video-title"><?php echo esc_html($video_title); ?></h2>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php
                    $slide_index++;
                endwhile;
                ?>
            </div>

            <!-- Navigation Arrows -->
            <?php if ($total_slides > 1) : ?>
            <a class="carousel-control-prev" href="#heroVideoCarousel" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only"><?php esc_html_e('Previous', 'axyz'); ?></span>
            </a>
            <a class="carousel-control-next" href="#heroVideoCarousel" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only"><?php esc_html_e('Next', 'axyz'); ?></span>
            </a>
            <?php endif; ?>
        </div>
    </div>
</section>
