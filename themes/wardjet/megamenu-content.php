<?php
/**
 * Products mega-menu fly-out.
 *
 * Mirrors the homepage "Products We Sell" section: full-width, two groups
 * (Abrasive Systems / Water Only Systems) with yellow-underlined headings and
 * image cards (series render + name). Series are resolved from the `series` CPT
 * per locale via translation_group_id, so the panel auto-localizes and L-Series
 * is excluded (Custom Waterjets is the 3rd Water-Only card) — matching the design.
 */

if (!defined('ABSPATH')) { exit; }

$current_lang_code = function_exists('lc_get_locale_from_url') ? lc_get_locale_from_url() : 'en-us';

$product_groups = array(
    'Abrasive Systems'   => array('a-series', 'x-series', 'm-series'),
    'Water Only Systems' => array('h-series', 'j-series', 'custom-waterjets'),
);
$group_title_i18n = array(
    'Abrasive Systems'   => array('es-us' => 'Sistemas de agua abrasiva', 'fr-ca' => "Systèmes à l'eau avec abrasifs", 'pl-pl' => 'Systemy ścierne'),
    'Water Only Systems' => array('es-us' => 'Sistemas solo de agua',     'fr-ca' => "Systèmes d'eau seulement",       'pl-pl' => 'Systemy tylko wodne'),
);

if (!function_exists('wj_products_resolve_series')) {
    function wj_products_resolve_series($en_slug, $locale) {
        $en = get_posts(array('post_type' => 'series', 'name' => $en_slug, 'posts_per_page' => 1, 'post_status' => 'publish', 'meta_query' => array(array('key' => 'region_language_code', 'value' => 'en-us'))));
        if (empty($en)) return null;
        $en_post = $en[0];
        if ($locale === 'en-us') return $en_post;
        $group = get_post_meta($en_post->ID, 'translation_group_id', true);
        if ($group) {
            $loc = get_posts(array('post_type' => 'series', 'posts_per_page' => 1, 'post_status' => 'publish', 'meta_query' => array('relation' => 'AND', array('key' => 'translation_group_id', 'value' => $group), array('key' => 'region_language_code', 'value' => $locale))));
            if (!empty($loc)) return $loc[0];
        }
        return $en_post;
    }
}

$resolved = array();
foreach ($product_groups as $gt => $slugs) {
    $posts = array();
    foreach ($slugs as $slug) { $sp = wj_products_resolve_series($slug, $current_lang_code); if ($sp) $posts[] = $sp; }
    if ($posts) $resolved[$gt] = $posts;
}
if (empty($resolved)) { return; }
?>
<div class="mm-products">
    <div class="container-xxl">
        <?php foreach ($resolved as $gt => $posts) : ?>
        <div class="mm-products-group">
            <h3 class="mm-products-title"><?php echo esc_html(isset($group_title_i18n[$gt][$current_lang_code]) ? $group_title_i18n[$gt][$current_lang_code] : $gt); ?></h3>
            <div class="mm-products-grid">
                <?php foreach ($posts as $sp) :
                    $GLOBALS['post'] = $sp;
                    setup_postdata($GLOBALS['post']);
                    $series_image = get_field('series_image');
                    $att_id = null;
                    if ($series_image) {
                        if (is_array($series_image)) { $att_id = isset($series_image['ID']) ? (int) $series_image['ID'] : null; }
                        elseif (is_numeric($series_image)) { $att_id = (int) $series_image; }
                    }
                    $img = '';
                    if ($att_id) { $img = wp_get_attachment_image($att_id, 'medium_large', false, array('alt' => esc_attr(get_the_title()))); }
                    elseif (is_string($series_image) && $series_image) { $img = '<img src="' . esc_url($series_image) . '" alt="' . esc_attr(get_the_title()) . '">'; }
                    elseif (has_post_thumbnail()) { $img = get_the_post_thumbnail(null, 'medium_large'); }
                ?>
                <a href="<?php the_permalink(); ?>" class="mm-product-card">
                    <span class="mm-product-card__img"><?php echo $img; ?></span>
                    <span class="mm-product-card__title"><?php the_title(); ?></span>
                </a>
                <?php endforeach; wp_reset_postdata(); ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<style>
.mm-products { padding: 36px 0 44px; }
.mm-products-group { margin-bottom: 30px; }
.mm-products-group:last-child { margin-bottom: 0; }
.mm-products-title {
    font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 24px; line-height: 32px;
    color: #093C71; letter-spacing: 0.3px; text-align: left;
    margin: 0 0 22px 0; padding-bottom: 8px; display: inline-block; border-bottom: 3px solid #F5B600;
}
.mm-products-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px 48px; }
.mm-product-card { display: flex; flex-direction: column; align-items: center; text-align: center; text-decoration: none; }
.mm-product-card__img { width: 100%; height: 200px; display: flex; align-items: center; justify-content: center; }
.mm-product-card__img img { max-width: 100%; max-height: 100%; width: auto; height: auto; object-fit: contain; }
.mm-product-card__title { margin-top: 14px; font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 17px; color: #314158; transition: color .2s ease; }
.mm-product-card:hover .mm-product-card__title { color: #093C71; }
@media (max-width: 991.98px) { .mm-products-grid { grid-template-columns: repeat(2, 1fr); } .mm-product-card__img { height: 160px; } }
@media (max-width: 575.98px) { .mm-products-grid { grid-template-columns: 1fr; } }
</style>
