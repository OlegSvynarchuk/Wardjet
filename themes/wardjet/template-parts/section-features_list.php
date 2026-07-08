<?php $block = $args['block']; ?>
<div class="features-list dark">
    <div class="container">
        <div class="section-title">
            <h2><?php echo $block['title']; ?></h2>
            <p><?php echo $block['description']; ?></p>
        </div>
        <?php $features = $block['features']; if($features): ?>
        <div class="row">
            <?php foreach($features AS $feature): ?>
                <div class="col-sm-4">
                    <div class="feature">
                        <div class="feature-icon">
                            <i class="<?php echo $feature['icon']; ?>"></i>
                        </div>
                        <div class="feature-title">
                            <h4><?php echo $feature['title']; ?></h4>
                            <p><?php echo $feature['description']; ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>