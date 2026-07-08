<?php $block = $args['block']; ?>
<section class="selected-products">
    <div class="container">
        <?php $products = $block['products']; if($products): ?>
        <div class="row">
            <?php foreach($products AS $product): ?>
                <div class="col-md-4">
                    <div class="product-card">
                        <div class="product-image">
                            <?php $image = get_field('product_image', $product->ID); ?>
                            <img src="<?php echo $image['url']; ?>" />
                        </div>
                        <div class="product-details">
                            <h3 class="title font-weight-light"><?php echo $product->post_title; ?></h3>
                            <p><?php the_field('product_short_description', $product->ID); ?></p>
                        </div>
                        <div class="product-cta">
                            <a href="<?php the_permalink($product->ID); ?>"><?=__('Learn More','axyz')?></a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>