<?php
/**
 * Products mega-menu fly-out.
 *
 * Editable, per-locale source: renders the ACF option repeaters `left_column`
 * + `right_column` (locale-aware via wj-multilingual) so content managers keep
 * editing each locale's groups/items in the Header Settings fields. Styled to
 * the Figma "Products We Sell" layout: full-width stacked groups with
 * yellow-underlined headings and image cards.
 */

if (!defined('ABSPATH')) { exit; }

// Resolve the current locale's variant of each field (es_left_column, etc.).
$left_field  = function_exists('lc_pick_locale_field') ? lc_pick_locale_field('left_column')  : 'left_column';
$right_field = function_exists('lc_pick_locale_field') ? lc_pick_locale_field('right_column') : 'right_column';
$left  = get_field($left_field, 'option');
$right = get_field($right_field, 'option');

// Merge both columns into one list of full-width groups (order: left, then right).
$sections = array();
if (is_array($left))  { foreach ($left as $s)  { if (!empty($s['items'])) { $sections[] = $s; } } }
if (is_array($right)) { foreach ($right as $s) { if (!empty($s['items'])) { $sections[] = $s; } } }
if (empty($sections)) { return; }
?>
<div class="mm-products">
    <div class="container-xxl">
        <?php foreach ($sections as $section) : ?>
        <div class="mm-products-group">
            <?php if (!empty($section['title'])) : ?>
            <h3 class="mm-products-title"><?php echo esc_html(trim($section['title'])); ?></h3>
            <?php endif; ?>
            <div class="mm-products-grid">
                <?php foreach ($section['items'] as $item) :
                    $img = $item['image'];
                    // Resolve an attachment ID so we can serve a sized render (with
                    // srcset) instead of the full-resolution upload — series PNGs can
                    // be 3000px/3MB, far larger than the ~240px card needs.
                    $img_id = 0;
                    if (is_array($img)) { $img_id = isset($img['ID']) ? (int) $img['ID'] : 0; }
                    elseif (is_numeric($img)) { $img_id = (int) $img; }
                    $img_url = is_array($img) ? (isset($img['url']) ? $img['url'] : '') : (is_string($img) ? $img : '');
                    $link  = isset($item['link']) ? $item['link'] : array();
                    $url   = isset($link['url']) ? $link['url'] : '';
                    // Flyout URLs are stored prefix-less (they resolve to us/en),
                    // so localize the /cc/ll/ prefix to the current locale — the
                    // series resolves under every locale's prefix.
                    if ($url !== '' && function_exists('wj_localize_internal_url')
                        && function_exists('lc_get_locale_from_url') && function_exists('lc_locale_to_prefix')) {
                        $url = wj_localize_internal_url($url, lc_locale_to_prefix(lc_get_locale_from_url()), rtrim(home_url(), '/'));
                    }
                    $title = isset($link['title']) ? $link['title'] : '';
                    $target = !empty($link['target']) ? ' target="' . esc_attr($link['target']) . '"' : '';
                    $img_alt = (is_array($img) && !empty($img['alt'])) ? $img['alt'] : $title;
                ?>
                <a href="<?php echo esc_url($url); ?>" class="mm-product-card"<?php echo $target; ?>>
                    <span class="mm-product-card__img"><?php
                    if ($img_id) {
                        echo wp_get_attachment_image($img_id, 'large', false, array('alt' => esc_attr($img_alt), 'decoding' => 'async'));
                    } elseif ($img_url) {
                        echo '<img src="' . esc_url($img_url) . '" alt="' . esc_attr($img_alt) . '" decoding="async">';
                    }
                    ?></span>
                    <span class="mm-product-card__title"><?php echo esc_html($title); ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<style>
.mm-products { padding: 20px 96px 44px; }
.mm-products .container-xxl { max-width: 100%; width: 100%; padding: 0; margin: 0; }
.mm-products-group { margin-bottom: 36px; }
.mm-products-group:last-child { margin-bottom: 0; }
.mm-products-title {
    font-family: 'Montserrat', sans-serif; font-weight: 400; font-size: 32px; line-height: 40px;
    color: #093C71; letter-spacing: 0; text-align: left;
    margin: 0 0 12px 0; padding-bottom: 6px; display: inline-block; position: relative;
}
.mm-products-title::after {
    content: ''; position: absolute; left: 0; bottom: 0;
    width: 50%; height: 2px; background: #FFF200;
}
.mm-products-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px 48px; }
.mm-product-card { display: flex; flex-direction: column; align-items: center; text-align: center; text-decoration: none; }
.mm-product-card__img { width: 100%; height: 170px; display: flex; align-items: center; justify-content: center; }
.mm-product-card__img img { max-width: 100%; max-height: 100%; width: auto; height: auto; object-fit: contain; }
/* #masthead prefix out-specifies wardjet-custom.css's `#masthead span{color:#7b868c;
   margin-right:6px;padding-right:6px}` catch-all, which otherwise greys these out
   and shifts them off-centre. */
#masthead .mm-product-card__title { margin-top: 8px; margin-right: 0; padding-right: 0; font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 20px; line-height: 28px; color: #314158; transition: color .2s ease; }
#masthead .mm-product-card:hover .mm-product-card__title { color: #093C71; }
@media (max-width: 991.98px) { .mm-products { padding: 20px; display: block; } .mm-products-group { margin-bottom: 24px; } .mm-products-grid { grid-template-columns: repeat(2, 1fr); } .mm-product-card__img { height: 130px; } }
@media (max-width: 575.98px) { .mm-products-grid { grid-template-columns: 1fr; } }
</style>
