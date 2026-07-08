<?php
/**
 * Industries Grid Section — wardjet.
 *
 * Adapted from the blueprint ind-mat-grid: wardjet has no `materials` CPT, so
 * the Materials/Industries toggle and the Materials panel are removed — only the
 * Industries grid is shown (locale-aware, en-us fallback).
 *
 * @package wardjet
 */

if (!defined('ABSPATH')) {
    exit;
}

$current_lang_code  = function_exists('get_current_lang_from_url') ? get_current_lang_from_url() : 'en-us';
$fallback_lang_code = 'en-us';

$industries_heading = get_field('industries_heading');
if (empty($industries_heading)) {
    $industries_heading = __('Industries', 'wardjet');
}
$industries_copy = get_field('industries_copy');
?>

<section class="ind-mat-grid">
    <div class="ind-mat-grid__container">
        <div class="ind-mat-grid__content">
            <div class="ind-mat-grid__panel active" id="industries-panel">
                <div class="ind-mat-grid__header">
                    <h2 class="ind-mat-grid__title"><?php echo esc_html($industries_heading); ?></h2>
                    <?php if ($industries_copy) : ?>
                        <div class="ind-mat-grid__desc"><?php echo wp_kses_post($industries_copy); ?></div>
                    <?php endif; ?>
                </div>
                <div class="ind-mat-grid__grid">
                    <?php
                    $industries_args = array(
                        'post_type'        => 'industry',
                        'post_status'      => 'publish',
                        'posts_per_page'   => 9,
                        // wardjet industries have no display_order meta; ordering by
                        // it (with a top-level meta_key) would exclude them all. Use
                        // menu_order + title instead so every industry is included.
                        'orderby'          => 'menu_order title',
                        'order'            => 'ASC',
                        'suppress_filters' => 0,
                        'meta_query'       => array(
                            array(
                                'key'     => 'region_language_code',
                                'value'   => $current_lang_code,
                                'compare' => '=',
                            ),
                        ),
                    );

                    $industries_query = new WP_Query($industries_args);

                    if (!$industries_query->have_posts() && $current_lang_code !== $fallback_lang_code) {
                        $industries_args['meta_query'][0]['value'] = $fallback_lang_code;
                        $industries_query = new WP_Query($industries_args);
                    }

                    if ($industries_query->have_posts()) :
                        while ($industries_query->have_posts()) :
                            $industries_query->the_post();
                            $grid_image = get_field('grid_image');
                            $image_url  = $grid_image ? (is_array($grid_image) ? $grid_image['url'] : $grid_image) : get_the_post_thumbnail_url(get_the_ID(), 'large');
                            if (!$image_url) {
                                $image_url = get_field('mosaic_icon');
                            }
                            $features = get_field('industry_features');
                    ?>
                        <a href="<?php the_permalink(); ?>" class="ind-mat-grid__card" <?php echo $image_url ? 'style="background-image: url(' . esc_url($image_url) . ')"' : ''; ?>>
                            <div class="ind-mat-grid__card-overlay"></div>
                            <div class="ind-mat-grid__card-content">
                                <span class="ind-mat-grid__card-title"><?php the_title(); ?></span>
                                <?php if ($features) : ?>
                                <ul class="ind-mat-grid__features">
                                    <?php
                                    $count = 0;
                                    foreach ($features as $feature) :
                                        if ($count >= 4) break;
                                    ?>
                                        <li><?php echo esc_html($feature['feature']); ?></li>
                                    <?php
                                        $count++;
                                    endforeach;
                                    ?>
                                </ul>
                                <?php endif; ?>
                            </div>
                        </a>
                    <?php
                        endwhile;
                        wp_reset_postdata();
                    endif;
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>
