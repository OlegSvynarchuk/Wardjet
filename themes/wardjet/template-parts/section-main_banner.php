<?php $block = $args['block']; ?>
<section class="main-banner">
    <?php echo wp_get_attachment_image($block['background_image'], 'full'); ?>
    <div class="banner-contents">
        <div class="container">
            <div class="title">
                <div class="row">
                    <div class="col-md-12">
                        <h1><?php echo $block['title']; ?></h1>
                        <p><b><?php echo $block['subtitle']; ?></b></p>
                    </div>
                </div>
            </div>
            <?php $features = $block['features']; if($features): ?>
            <div class="features">
                <div class="row">
                    <?php foreach($features AS $feature): ?>
                        <div class="col-md-4">
                            <div class="feature">
                                <h3><?php echo $feature['title']; ?></h3>
                                <p><?php echo $feature['description']; ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>