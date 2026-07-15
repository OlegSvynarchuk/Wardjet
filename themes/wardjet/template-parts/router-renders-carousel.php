<?php
/**
 * Router Renders Carousel Template Part
 *
 * Displays router name, subtitle, and 3D render images carousel.
 *
 * @package AXYZ
 */

if (!defined('ABSPATH')) {
    exit;
}

$router_title    = get_the_title();
$renders         = get_field('router_renders');

// Subtitle source: main_banner block's subtitle (the field editors see in dashboard).
// Intentionally ignores legacy `series_subtitle` field so duplicated/localized posts
// don't inherit the original language from that hidden field.
$router_subtitle = '';
$blocks = get_field('content');
if (is_array($blocks)) {
    foreach ($blocks as $block) {
        if ($block['acf_fc_layout'] === 'main_banner' && !empty($block['subtitle'])) {
            $router_subtitle = $block['subtitle'];
            break;
        }
    }
}

// Some legacy entries have literal <br> in the subtitle. Drop tags and collapse
// runs of whitespace so the line flows as a single sentence.
if ($router_subtitle !== '') {
    $router_subtitle = preg_replace('#<br\s*/?>#i', ' ', $router_subtitle);
    $router_subtitle = trim(preg_replace('/\s+/', ' ', wp_strip_all_tags($router_subtitle)));
}

// Fallback: use series_image if no 3D renders uploaded
if (empty($renders)) {
    $series_image = get_field('series_image');
    if ($series_image) {
        $renders = array($series_image);
    }
}
?>

<section class="router-renders">
    <div class="router-renders__header">
        <h1 class="router-renders__title"><?php echo esc_html($router_title); ?></h1>
        <?php if ($router_subtitle) : ?>
            <p class="router-renders__subtitle"><?php echo esc_html($router_subtitle); ?></p>
        <?php endif; ?>
    </div>

    <?php if ($renders && is_array($renders) && count($renders) > 0) : ?>
    <div class="router-renders__carousel">
        <div id="routerRendersCarousel" class="carousel slide" data-ride="carousel" data-interval="false">
            <div class="carousel-inner">
                <?php foreach ($renders as $index => $image) :
                    if (is_array($image)) {
                        $img_url = $image['url'];
                        $img_alt = !empty($image['alt']) ? $image['alt'] : $router_title;
                    } elseif (is_numeric($image)) {
                        $img_url = wp_get_attachment_url((int)$image);
                        $img_alt = $router_title;
                    } else {
                        $img_url = $image;
                        $img_alt = $router_title;
                    }
                    if (!$img_url) continue;
                ?>
                    <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                        <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($img_alt); ?>" />
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (count($renders) > 1) : ?>
            <ol class="carousel-indicators">
                <?php foreach ($renders as $index => $image) : ?>
                    <li data-target="#routerRendersCarousel" data-slide-to="<?php echo esc_attr($index); ?>" class="<?php echo $index === 0 ? 'active' : ''; ?>"></li>
                <?php endforeach; ?>
            </ol>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</section>
