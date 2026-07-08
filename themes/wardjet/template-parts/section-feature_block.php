<?php $block = $args['block']; ?>
<section class="feature-block">
    <div class="container">
        <div class="row">
            <div class="col-sm-6 order-1 <?php if($block['section_style'] == 'right_image'): ?>order-sm-2<?php endif; ?>">
                <?php if($block['image_type'] == 'image_hotspot'): ?>
                    <?php echo do_shortcode('[devvn_ihotspot id="'.$block['image_hotspots'].'"]'); ?>
                <?php else: ?>
                <div class="image <?php echo $block['section_style']; ?>">
                    <?php echo wp_get_attachment_image($block['image'], 'large'); ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="align-self-center col-sm-6 order-2 <?php if($block['section_style'] == 'right_image'): ?>order-sm-1<?php endif; ?>">
                <div class="details">
                    <h2><?php echo $block['title']; ?></h2>
                    <?php echo $block['description']; ?>
                </div>
            </div>
        </div>
    </div>
</section>