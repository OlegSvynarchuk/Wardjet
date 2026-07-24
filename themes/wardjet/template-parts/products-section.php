<?php
/**
 * Products Section Template Part — wardjet grouped layout.
 *
 * Two category groups, each a left-titled row of 3 series cards (blueprint card
 * styling). L-Series is excluded; Custom Waterjets is shown under Water Only.
 *
 *   Abrasive Systems   : A-Series, M-Series, X-Series
 *   Water Only Systems : H-Series, J-Series, Custom Waterjets
 *
 * @package wardjet
 */

if (!defined('ABSPATH')) {
    exit;
}

$current_lang_code = isset($current_lang_code) ? $current_lang_code : (function_exists('get_current_lang_from_url') ? get_current_lang_from_url() : 'en-us');

$section_title = get_field('products_section_title');
if (empty($section_title)) { $section_title = get_field('product_heading'); } // localized per-page field
if (empty($section_title)) {
    $section_title = __('Products we sell', 'wardjet');
}
$section_subtitle = get_field('products_section_subtitle');
if (empty($section_subtitle)) { $section_subtitle = get_field('product_copy'); } // localized per-page field
if (empty($section_subtitle)) {
    $section_subtitle = __('Our modular products can be tailored to fit your needs and provide a perfect solution to your challenges.', 'wardjet');
}
$cta_label = get_field('products_learn_more_label');
if (empty($cta_label)) {
    // No translation .mo files are loaded, so __() would always return English.
    // Localize explicitly per locale, matching the $explore_i18n map below.
    $cta_i18n = array(
        'es' => 'Aprende Más',
        'fr' => 'En Savoir Plus',
        'pl' => 'Dowiedz się więcej',
    );
    $ll = substr((string) $current_lang_code, 0, 2);
    $cta_label = isset($cta_i18n[$ll]) ? $cta_i18n[$ll] : __('Learn More', 'wardjet');
}

// "Explore Products" CTA — shown on the homepage (the products page passes
// hide_cta_button). Link is per-locale (localized slugs) via the ACF
// products_page_link field; label localized (values from the blueprint).
$hide_cta_button = isset($args['hide_cta_button']) && $args['hide_cta_button'];
// Products page per locale — slugs are localized (our-products / productos /
// des-produits / produkty), so map locale -> page id and use get_permalink.
$products_page_ids = array(
    'en-us' => 92, 'en-uk' => 12771, 'en-ca' => 12721,
    'es-us' => 1704, 'fr-ca' => 1522, 'pl-pl' => 7352,
);
$products_page_link = isset($products_page_ids[$current_lang_code])
    ? get_permalink($products_page_ids[$current_lang_code])
    : home_url('/');
$explore_label = get_field('products_cta_label');
if (empty($explore_label)) {
    $explore_i18n = array(
        'es' => 'Explorar Productos',
        'fr' => 'Explorer les Produits',
        'pl' => 'Przeglądaj Produkty',
    );
    $ll = substr((string) $current_lang_code, 0, 2);
    $explore_label = isset($explore_i18n[$ll]) ? $explore_i18n[$ll] : __('Explore Products', 'wardjet');
}

// Grouped series by en-us slug. L-Series excluded; Custom Waterjets under Water Only.
$product_groups = array(
    'Abrasive Systems'   => array('a-series', 'm-series', 'x-series'),
    'Water Only Systems' => array('h-series', 'j-series', 'custom-waterjets'),
);

// Localized group titles (from the series category names per locale).
$group_title_i18n = array(
    'Abrasive Systems' => array(
        'es-us' => 'Sistemas de agua abrasiva',
        'fr-ca' => "Systèmes à l'eau avec abrasifs",
        'pl-pl' => 'Systemy ścierne',
    ),
    'Water Only Systems' => array(
        'es-us' => 'Sistemas solo de agua',
        'fr-ca' => "Systèmes d'eau seulement",
        'pl-pl' => 'Systemy tylko wodne',
    ),
);

// Resolve an en-us series slug to the current locale's translated post (via
// translation_group_id), falling back to the en-us post.
if (!function_exists('wj_products_resolve_series')) {
    function wj_products_resolve_series($en_slug, $locale) {
        $en = get_posts(array(
            'post_type'      => 'series',
            'name'           => $en_slug,
            'posts_per_page' => 1,
            'post_status'    => 'publish',
            'meta_query'     => array(array('key' => 'region_language_code', 'value' => 'en-us')),
        ));
        if (empty($en)) {
            return null;
        }
        $en_post = $en[0];
        if ($locale === 'en-us') {
            return $en_post;
        }
        $group = get_post_meta($en_post->ID, 'translation_group_id', true);
        if ($group) {
            $loc = get_posts(array(
                'post_type'      => 'series',
                'posts_per_page' => 1,
                'post_status'    => 'publish',
                'meta_query'     => array(
                    'relation' => 'AND',
                    array('key' => 'translation_group_id', 'value' => $group),
                    array('key' => 'region_language_code', 'value' => $locale),
                ),
            ));
            if (!empty($loc)) {
                return $loc[0];
            }
        }
        return $en_post;
    }
}

// Resolve each group's slugs to posts; drop empty groups.
$resolved_groups = array();
foreach ($product_groups as $group_title => $slugs) {
    $posts = array();
    foreach ($slugs as $slug) {
        $sp = wj_products_resolve_series($slug, $current_lang_code);
        if ($sp) {
            $posts[] = $sp;
        }
    }
    if (!empty($posts)) {
        $resolved_groups[$group_title] = $posts;
    }
}
if (empty($resolved_groups)) {
    return;
}
?>

<section class="products-section">
    <div class="products-header">
        <h2 class="section-title"><?php echo esc_html($section_title); ?></h2>
        <?php if ($section_subtitle) : ?>
            <p class="section-subtitle"><?php echo esc_html($section_subtitle); ?></p>
        <?php endif; ?>
    </div>

    <?php foreach ($resolved_groups as $group_title => $posts) : ?>
    <div class="products-group">
        <h3 class="products-group-title"><?php echo esc_html(isset($group_title_i18n[$group_title][$current_lang_code]) ? $group_title_i18n[$group_title][$current_lang_code] : $group_title); ?></h3>
        <div class="products-grid">
            <?php foreach ($posts as $series_post) :
                $GLOBALS['post'] = $series_post;
                setup_postdata($GLOBALS['post']);
            ?>
                <a href="<?php the_permalink(); ?>" class="product-card">
                    <div class="product-card-image">
                        <?php
                        // Series 3D render first, then featured image.
                        $series_image = get_field('series_image');
                        $att_id = null;
                        if ($series_image) {
                            if (is_array($series_image)) {
                                $att_id = isset($series_image['ID']) ? (int) $series_image['ID'] : null;
                            } elseif (is_numeric($series_image)) {
                                $att_id = (int) $series_image;
                            }
                        }
                        $img_html = '';
                        if ($att_id) {
                            $img_html = wp_get_attachment_image($att_id, 'large', false, array(
                                'alt'      => esc_attr(get_the_title()),
                                'decoding' => 'async',
                            ));
                        } elseif (is_string($series_image) && $series_image) {
                            $img_html = '<img src="' . esc_url($series_image) . '" alt="' . esc_attr(get_the_title()) . '" decoding="async">';
                        } elseif (has_post_thumbnail()) {
                            $img_html = get_the_post_thumbnail(null, 'large', array('decoding' => 'async'));
                        }
                        if ($img_html) {
                            $img_html = preg_replace('/\s+loading=("[^"]*"|\'[^\']*\')/', '', $img_html);
                            $img_html = preg_replace('/<img\b/', '<img loading="eager"', $img_html, 1);
                            echo $img_html;
                        }
                        ?>
                    </div>
                    <div class="product-card-content">
                        <h3 class="product-card-title"><?php the_title(); ?></h3>
                        <?php $subtitle = get_field('series_subtitle'); if ($subtitle) : ?>
                            <p class="product-card-subtitle"><?php echo esc_html($subtitle); ?></p>
                        <?php endif; ?>
                        <?php $description = get_field('series_short_description'); if ($description) : ?>
                            <p class="product-card-description"><?php echo esc_html($description); ?></p>
                        <?php endif; ?>
                        <span class="product-card-cta">
                            <?php echo esc_html($cta_label); ?>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 5L19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                    </div>
                </a>
            <?php endforeach; wp_reset_postdata(); ?>
        </div>
        <div class="products-dots d-lg-none"></div>
    </div>
    <?php endforeach; ?>

    <?php if (!$hide_cta_button) : ?>
    <div class="products-cta-button">
        <a href="<?php echo $products_page_link ? esc_url($products_page_link) : esc_url(home_url('/')); ?>">
            <?php echo esc_html($explore_label); ?>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M5 12H19" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M12 5L19 12L12 19" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>
    </div>
    <?php endif; ?>
</section>
