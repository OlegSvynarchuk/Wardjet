<?php $block = $args['block']; ?>
<div class="feature-section">
    <div class="container">
        <div class="row">
            <div class="col-sm-7 <?php if($block['block_style'] == 'image_left'): ?>order-1<?php else: ?>order-2<?php endif; ?>">
                <div class="feature-media">
                    <?php if($block['video']): ?>
                        <video autoplay="" loop="" style="width: 100%;" muted="">
                            <source src="<?php echo $block['video']; ?>" type="video/mp4">
                        </video>
                    <?php else: ?>
                        <?php echo wp_get_attachment_image($block['image'], 'full'); ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-sm-5 <?php if($block['block_style'] == 'image_left'): ?>order-2<?php else: ?>order-1<?php endif; ?>">
                <div class="feature-details">
                    <div class="feature-title">
                        <h2><?php echo $block['title']; ?></h2>
                        <?php if($block['subtitle']): ?>
                        <span><?php echo $block['subtitle']; ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if($block['details']): ?>
                    <p><?php echo $block['details']; ?></p>
                    <?php endif; ?>
                    <?php if($block['cta']): ?>
                        <?php $cta = $block['cta']; ?>
                        <a href="<?php echo $cta['url']; ?>" class="button button-large" <?php if($cta['target']): ?>target="_blank"<?php endif; ?>><?php echo $cta['title']; ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>