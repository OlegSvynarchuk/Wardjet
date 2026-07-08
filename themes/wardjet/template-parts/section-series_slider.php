<?php $block = $args['block']; ?>
<section class="series-slider">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <?php $gallery = $block['gallery']; if($gallery): ?>
                    <div class="series-slides">
                        <?php foreach($gallery AS $image): ?>

                                <?php echo wp_get_attachment_image($image, 'full'); ?>

                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <?php $sslider = $block['series_slider']; if($sslider): ?>
                    <div class="series-slides">
                        <?php foreach($sslider AS $image): ?>

                            <?php echo wp_get_attachment_image($image, 'full'); ?>

                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>