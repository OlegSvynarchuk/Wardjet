<?php $block = $args['block']; ?>
<div class="single-feature">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-8">
                <div class="feature-details">
                    <div class="feature-title">
                        <h2><?php echo $block['title']; ?></h2>
                        <span><?php echo $block['description']; ?></span>
                    </div>
                </div>
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
        </div>
    </div>
</div>