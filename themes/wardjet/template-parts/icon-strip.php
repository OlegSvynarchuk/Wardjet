<?php
/**
 * Icon Strip Template Part (AXYZQuickInfo)
 *
 * Static section with 4 feature icons displayed in a grid.
 * Located underneath the features slider on the homepage.
 *
 * @package wardjet
 */

if (!defined('ABSPATH')) {
    exit;
}

// Try ACF fields first, fallback to static defaults
$acf_items = get_field('icon_strip_items');
if ($acf_items && is_array($acf_items) && count($acf_items) > 0) {
    $items = $acf_items;
} else {
    $items = array(
        array(
            'title'    => __('Industry Leader', 'axyz'),
            'subtitle' => __('30+ years of CNC excellence', 'axyz'),
            'icon'     => 'trophy',
        ),
        array(
            'title'    => __('Global Reach', 'axyz'),
            'subtitle' => __('Operating in 180+ countries', 'axyz'),
            'icon'     => 'globe',
        ),
        array(
            'title'    => __('24/7 Support', 'axyz'),
            'subtitle' => __('Expert assistance worldwide', 'axyz'),
            'icon'     => 'headset',
        ),
        array(
            'title'    => __('Custom Solutions', 'axyz'),
            'subtitle' => __('Tailored to your needs', 'axyz'),
            'icon'     => 'cog',
        ),
    );
}

// Icon images mapped by index
$icon_images = array(
    content_url('/uploads/2026/03/icon1.avif'),
    content_url('/uploads/2026/03/icon2.avif'),
    content_url('/uploads/2026/03/icon3.avif'),
    content_url('/uploads/2026/03/icon4.avif'),
);
?>

<section class="icon-strip-section">
    <div class="icon-strip-grid">
        <?php foreach ($items as $index => $item) : ?>
            <div class="icon-strip-item">
                <div class="icon-strip-icon">
                    <?php if (isset($icon_images[$index])) : ?>
                        <img src="<?php echo esc_url($icon_images[$index]); ?>" alt="<?php echo esc_attr($item['title']); ?>" />
                    <?php endif; ?>
                </div>
                <div class="icon-strip-text">
                    <h3 class="icon-strip-title"><?php echo esc_html($item['title']); ?></h3>
                    <p class="icon-strip-subtitle"><?php echo esc_html($item['subtitle']); ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
