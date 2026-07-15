<?php $block = $args['block'];
$is_reverse = (isset($block['section_style']) && $block['section_style'] === 'right_image');
$modifier = $is_reverse ? ' feature-block--reverse' : '';
// Track feature block index for mobile alternating order
global $wj_feature_block_index;
if (!isset($wj_feature_block_index)) $wj_feature_block_index = 0;
$fb_index = $wj_feature_block_index++;
?>
<section class="feature-block feature-block--n<?php echo esc_attr($fb_index); ?><?php echo esc_attr($modifier); ?>">
    <div class="feature-block__inner">
        <div class="feature-block__image">
            <?php if (array_key_exists('image_type', $block) && $block['image_type'] == 'image_hotspot') : ?>
                <?php echo do_shortcode('[devvn_ihotspot id="' . $block['image_hotspots'] . '"]'); ?>
            <?php else : ?>
                <?php echo wp_get_attachment_image($block['image'], 'large'); ?>
            <?php endif; ?>
        </div>
        <div class="feature-block__content">
            <h2 class="feature-block__title"><?php echo wp_kses_post($block['title']); ?></h2>
            <div class="feature-block__description"><?php echo wp_kses_post($block['description']); ?></div>
        </div>
    </div>
</section>
