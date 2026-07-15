<?php
/**
 * Router Features Strip
 *
 * Displays feature cards from main_banner block data over a router background image.
 *
 * @package AXYZ
 */

if (!defined('ABSPATH')) {
    exit;
}

$blocks = get_field('content');
$features = array();
$bg_image_id = null;

// Extract features and background from main_banner block
if (is_array($blocks)) {
    foreach ($blocks as $block) {
        if ($block['acf_fc_layout'] === 'main_banner') {
            if (!empty($block['features'])) {
                $features = $block['features'];
            }
            if (!empty($block['background_image'])) {
                $bg_image_id = $block['background_image'];
            }
            break;
        }
    }
}

if (empty($features)) {
    return;
}

// Use series_image (same as renders carousel) for background
$bg_url = '';
$series_image = get_field('series_image');
if ($series_image) {
    $bg_url = $series_image['url'];
}

// Toggle for order numbers (default: true)
$show_numbers = get_field('features_show_numbers');
if ($show_numbers === null) {
    $show_numbers = true;
}
?>

<section class="router-features-strip" style="background: linear-gradient(0deg, rgba(9, 60, 113, 0.88), rgba(9, 60, 113, 0.88))<?php echo $bg_url ? ', url(' . esc_url($bg_url) . ')' : ''; ?>; background-size: cover; background-position: center;">
    <div class="router-features-strip__inner">
        <?php foreach ($features as $i => $feature) :
            $has_title  = !empty($feature['title']);
            $has_header = $show_numbers || $has_title;
            $card_class = 'router-features-strip__card' . ($has_header ? '' : ' router-features-strip__card--no-header');
        ?>
            <div class="<?php echo esc_attr($card_class); ?>">
                <?php if ($has_header) : ?>
                    <div class="router-features-strip__header">
                        <?php if ($show_numbers) : ?>
                            <span class="router-features-strip__number"><?php echo esc_html($i + 1); ?></span>
                        <?php endif; ?>
                        <?php if ($has_title) : ?>
                            <h3 class="router-features-strip__title"><?php echo esc_html($feature['title']); ?></h3>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <p class="router-features-strip__description"><?php echo esc_html($feature['description']); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</section>
