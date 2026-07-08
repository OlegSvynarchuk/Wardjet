<?php $block = $args['block']; ?>
<section class="hotspot-wrapper dark">
    <div class="container">
        <div class="section-title">
            <h2><?php echo $block['title']; ?></h2>
            <p><?php echo $block['description']; ?></p>
        </div>
        <?php echo do_shortcode('[devvn_ihotspot id="'.$block['hotspots_id'].'"]'); ?>
    </div>
</section>
