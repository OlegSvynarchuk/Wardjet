<?php $block = $args['block']; ?>
<section class="feature-block crossbeam-section">
    <div class="container">
        <div class="row">
            <div class="col-sm-6 order-1">
                <?php $animated = $block['animated_image']; if($animated): ?>
                    <div id="myTurntable" class="turntable">
                        <ul>
                            <?php foreach($animated AS $image): ?>
                            <li data-img-src="<?php echo wp_get_attachment_image_url($image, 'large'); ?>"></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
            <div class="align-self-center col-sm-6 order-2">
                <div class="details">
                    <h2><?php echo $block['title']; ?></h2>
                    <span class="subtitle"><?php echo $block['subtitle']; ?></span>
                    <?php echo wp_get_attachment_image($block['image'], 'large'); ?>
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