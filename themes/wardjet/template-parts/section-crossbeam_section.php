<?php
/**
 * Crossbeam section — rendered as a feature row.
 *
 * Per the Figma single-product design the crossbeam reads as just another
 * feature row (image one side, text the other), so this emits the same BEM
 * markup as section-feature_block.php and joins the same alternating index.
 * That keeps the crossbeam's extra content the generic block can't hold:
 * the 3D turntable (animated_image), the subtitle and the CTA.
 */

$block = $args['block'];

$is_reverse = (isset($block['section_style']) && $block['section_style'] === 'right_image');
$modifier   = $is_reverse ? ' feature-block--reverse' : '';

// Share the feature_block counter so the rows keep alternating in sequence.
global $wj_feature_block_index;
if (!isset($wj_feature_block_index)) {
    $wj_feature_block_index = 0;
}
$fb_index = $wj_feature_block_index++;

$animated = !empty($block['animated_image']) ? $block['animated_image'] : array();
?>
<section class="feature-block feature-block--n<?php echo esc_attr($fb_index); ?><?php echo esc_attr($modifier); ?> crossbeam-section">
    <div class="feature-block__inner">
        <div class="feature-block__image">
            <?php if ($animated) : ?>
                <div id="myTurntable" class="turntable">
                    <ul>
                        <?php foreach ($animated as $image) : ?>
                            <li data-img-src="<?php echo esc_url(wp_get_attachment_image_url($image, 'large')); ?>"></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php elseif (!empty($block['image'])) : ?>
                <?php echo wp_get_attachment_image($block['image'], 'large'); ?>
            <?php endif; ?>
        </div>

        <div class="feature-block__content">
            <?php if (!empty($block['title'])) : ?>
                <h2 class="feature-block__title"><?php echo wp_kses_post($block['title']); ?></h2>
            <?php endif; ?>

            <?php if (!empty($block['subtitle'])) : ?>
                <span class="feature-block__subtitle subtitle"><?php echo wp_kses_post($block['subtitle']); ?></span>
            <?php endif; ?>

            <div class="feature-block__description"><?php echo wp_kses_post($block['description']); ?></div>

            <?php $cta = !empty($block['cta']) ? $block['cta'] : null; ?>
            <?php if ($cta && !empty($cta['url'])) : ?>
                <div class="cta-wrapper">
                    <a href="<?php echo esc_url($cta['url']); ?>"<?php echo !empty($cta['target']) ? ' target="_blank" rel="noopener"' : ''; ?>>
                        <?php echo esc_html($cta['title']); ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
