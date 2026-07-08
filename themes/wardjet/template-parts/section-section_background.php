<?php $block = $args['block']; ?>
<div class="section-background" style="background-image:url('<?php echo wp_get_attachment_image_url($block['background_image'], 'full'); ?>');">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-8">
                <div class="section-media">
                    <?php echo wp_get_attachment_image($block['image'], 'full'); ?>
                </div>
                <div class="section-details">
                    <div class="section-title">
                        <h2><?php echo $block['title']; ?></h2>
                        <p><?php echo $block['description']; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>