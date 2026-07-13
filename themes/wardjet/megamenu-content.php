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
                    $img_url = is_array($img) ? (isset($img['url']) ? $img['url'] : '') : (is_string($img) ? $img : '');
                    $link  = isset($item['link']) ? $item['link'] : array();
                    $url   = isset($link['url']) ? $link['url'] : '';
                    $title = isset($link['title']) ? $link['title'] : '';
                    $target = !empty($link['target']) ? ' target="' . esc_attr($link['target']) . '"' : '';
                    $img_alt = (is_array($img) && !empty($img['alt'])) ? $img['alt'] : $title;
                ?>
                <a href="<?php echo esc_url($url); ?>" class="mm-product-card"<?php echo $target; ?>>
                    <span class="mm-product-card__img"><?php if ($img_url) : ?><img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($img_alt); ?>"><?php endif; ?></span>
                    <span class="mm-product-card__title"><?php echo esc_html($title); ?></span>
                </a>
                <?php endforeach; ?>
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
