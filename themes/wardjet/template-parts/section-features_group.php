<?php $block = $args['block']; ?>
<section class="features-group">
    <div class="container">
        <div class="title">
            <div class="row">
                <div class="col-sm-12">
                    <h2><?php echo $block['title']; ?></h2>
                    <p><?php echo $block['subtitle']; ?></p>
                </div>
            </div>
        </div>
        <?php $features = $block['features']; if($features): ?>
        <div class="features">
            <div class="row">
                <?php
                $features_number = count($features);
                foreach($features AS $feature): ?>
                <div class="<?php if($features_number == 4): ?>col-sm-3<?php else: ?>col-sm-4<?php endif; ?>">
                    <div class="feature">
                        <div class="feature-image">
                            <?php echo wp_get_attachment_image($feature['image'], 'medium')?>
                        </div>
                        <div class="feature-details">
                            <h4><?php echo $feature['title']; ?></h4>
                            <p><?php echo $feature['description']; ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>