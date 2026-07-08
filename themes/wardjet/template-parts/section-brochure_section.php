<?php $block = $args['block']; ?>
<section class="brochure-section">
    <div class="container">
        <div class="row">
            <div class="col-sm-4 order-1 order-sm-2">
                <div class="image <?php echo $block['section_style']; ?>">
                    <?php echo wp_get_attachment_image($block['image'], 'large'); ?>
                </div>
            </div>
            <div class="align-self-center col-sm-8 order-2 order-sm-1">
                <div class="details">
                    <h2><?php echo $block['title']; ?></h2>
                    <?php echo $block['description']; ?>
                    <?php $cta = $block['cta']; if($cta): ?>
                        <div class="cta-wrapper">
                            <a href="<?php echo $cta['url']; ?>" <?php if($cta['target']): ?>target="_blank"<?php endif; ?>><?php echo $cta['title']; ?></a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>