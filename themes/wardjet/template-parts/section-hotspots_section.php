<?php $block = $args['block']; ?>
<section class="hotspots-section">
    <div class="container">
        <?php if(!empty($block['title']) OR !empty($block['description'])): ?>
        <div class="section-title">
            <?php if($block['title']): ?>
            <h2><?php echo $block['title']; ?></h2>
            <?php endif; ?>
            <?php if($block['description']): ?>
            <p><?php echo $block['description']; ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <?php echo do_shortcode('[devvn_ihotspot id="'.$block['hotspots_id'].'"]'); ?>
    </div>
</section>
