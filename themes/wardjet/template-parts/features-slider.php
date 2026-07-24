<?php
/**
 * Features Slider Template Part
 *
 * Displays a carousel of features with navigation.
 * Uses ACF repeater for easy management of slider items.
 *
 * ACF Fields:
 * - features_slider_title: Section title
 * - features_slider_subtitle: Section subtitle
 * - features_slider_items: Repeater (image, title, description, link)
 * - features_slider_autoplay: Enable/disable auto-play
 * - features_slider_interval: Slide interval in ms
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get section header content
$slider_title = get_field('features_slider_title');
$slider_subtitle = get_field('features_slider_subtitle');

// Get slider settings
$autoplay = get_field('features_slider_autoplay');
$interval = get_field('features_slider_interval');
$interval = $interval ? absint($interval) : 5000;

// Get features items
$features = array();

if (have_rows('features_slider_items')) :
    while (have_rows('features_slider_items')) : the_row();
        $image = get_sub_field('image');
        $link = get_sub_field('link');
        $short = get_sub_field('short_title');
        $full_title = get_sub_field('title');
        $features[] = array(
            'title' => $full_title,
            'short_title' => $short ? $short : $full_title,
            'description' => get_sub_field('description'),
            'image' => is_array($image) ? $image['url'] : $image,
            'link' => $link,
        );
    endwhile;
endif;

// Don't render if no features
if (empty($features)) :
    return;
endif;

$total_features = count($features);
?>

<section class="content-area features-slider-section">
    <div class="site-main" role="main">
        <div class="container">
            <!-- Header -->
            <div class="slider-header">
                <?php if ($slider_title) : ?>
                    <h2 class="heading"><?php echo esc_html($slider_title); ?></h2>
                <?php endif; ?>
                <?php if ($slider_subtitle) : ?>
                    <p class="slider-subtitle"><?php echo esc_html($slider_subtitle); ?></p>
                <?php endif; ?>
            </div>

            <!-- Carousel -->
            <div id="featuresSlider" data-interval="<?php echo $autoplay ? esc_attr($interval) : 'false'; ?>">
                <div class="carousel-container">
                    <!-- Previous Arrow -->
                    <button class="slider-arrow prev" type="button" aria-label="<?php esc_attr_e('Previous', 'axyz'); ?>">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>

                    <!-- Track -->
                    <div class="slider-track-wrapper">
                        <div class="features-track">
                            <?php foreach ($features as $feature) : ?>
                                <div class="features-track__slide">
                                    <?php
                                    $feature_link = $feature['link'];
                                    if (!empty($feature_link)) {
                                        $anchor = sanitize_title($feature['title']);
                                        if ($anchor) {
                                            $feature_link = strtok($feature_link, '#') . '#feature-' . $anchor;
                                        }
                                        // The link points at the (shared) waterjet-accessories PAGE, which
                                        // ACF stores with a fixed /us/en/ prefix. Pages are excluded from
                                        // the post_type_link auto-alias, so rewrite the /cc/ll/ prefix to the
                                        // current locale here (the page resolves under every locale prefix).
                                        if (function_exists('lc_get_locale_from_url') && function_exists('lc_locale_to_prefix')) {
                                            $home = rtrim(home_url(), '/');
                                            if (strpos($feature_link, $home) === 0) {
                                                $cur_prefix = lc_locale_to_prefix(lc_get_locale_from_url());
                                                $rel = ltrim(substr($feature_link, strlen($home)), '/');
                                                if (preg_match('#^[a-z]{2}/[a-z]{2}(/|$)#i', $rel)) {
                                                    $rel = preg_replace('#^[a-z]{2}/[a-z]{2}#i', $cur_prefix, $rel, 1);
                                                } else {
                                                    $rel = $cur_prefix . '/' . $rel;
                                                }
                                                $feature_link = $home . '/' . $rel;
                                            }
                                        }
                                    }
                                    ?>
                                    <?php if (!empty($feature_link)) : ?><a href="<?php echo esc_url($feature_link); ?>" class="feature-slide-link"><?php endif; ?>
                                    <div class="feature-slide">
                                        <?php if (!empty($feature['image'])) : ?>
                                            <div class="feature-slide-image">
                                                <img src="<?php echo esc_url($feature['image']); ?>" alt="<?php echo esc_attr($feature['title']); ?>" loading="lazy">
                                            </div>
                                        <?php endif; ?>
                                        <div class="feature-slide-content">
                                            <div class="feature-title-wrap">
                                                <h3 class="feature-title"><?php echo esc_html($feature['short_title']); ?></h3>
                                                <div class="accent-line"></div>
                                            </div>
                                            <?php if (!empty($feature['description'])) : ?>
                                                <p class="feature-description"><?php echo esc_html(mb_strimwidth($feature['description'], 0, 60, '...')); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php if (!empty($feature_link)) : ?></a><?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Next Arrow -->
                    <button class="slider-arrow next" type="button" aria-label="<?php esc_attr_e('Next', 'axyz'); ?>">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>

</div>
        </div>
    </div>
</section>
