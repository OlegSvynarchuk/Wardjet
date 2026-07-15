<?php
/**
 * Features & Benefits (features_benefits layout)
 *
 * Used by the Custom Waterjets series. Renders with the same markup as
 * section-competitive_chart.php so it reads as a regular "Features & Benefits"
 * section (block title as the heading, each feature's title + content as a
 * card) and inherits competitive-chart.css.
 *
 * NB: the old `features-benefits` class is intentionally dropped — it carries
 * the Bootstrap accordion styling in wardjet-custom.css.
 */

$block    = $args['block'];
$features = isset($block['features']) ? $block['features'] : array();
?>
<section class="competitive-chart competitive-chart--features-benefits">
    <div class="competitive-chart__inner">
        <?php if (!empty($block['title'])) : ?>
            <h2 class="competitive-chart__title"><?php echo esc_html($block['title']); ?></h2>
        <?php endif; ?>

        <?php if (!empty($block['subtitle'])) : ?>
            <p class="competitive-chart__subtitle"><?php echo wp_kses_post($block['subtitle']); ?></p>
        <?php endif; ?>

        <?php if (!empty($features)) : ?>
            <div class="competitive-chart__cards">
                <?php foreach ($features as $feature) : ?>
                    <div class="competitive-chart__card">
                        <?php if (!empty($feature['title'])) : ?>
                            <h3 class="competitive-chart__card-title"><?php echo esc_html($feature['title']); ?></h3>
                        <?php endif; ?>
                        <?php if (!empty($feature['content'])) : ?>
                            <div class="competitive-chart__card-content"><?php echo wp_kses_post($feature['content']); ?></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
