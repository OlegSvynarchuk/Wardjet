<div class="hotspot-wrapper">
    <div class="container">
        <div id="hotspotImg" class="responsive-hotspot-wrap row">
            <?php echo wp_get_attachment_image(get_field('hotspot_background'), 'full', '', array('class' => 'img-responsive')); ?>
            <?php $hotspots = get_field('hotspots'); if($hotspots): ?>
                <?php foreach($hotspots AS $hotspot): ?>
                    <div class="hot-spot" x="<?php echo $hotspot['x_position']; ?>" y="<?php echo $hotspot['y_position']; ?>">
                        <div class="circle">+</div>
                        <div class="tooltip">
                            <div class="text-row">
                                <h4><?php echo $hotspot['title']; ?></h4>
                                <p><?php echo $hotspot['details']; ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
