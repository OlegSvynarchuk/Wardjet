<?php $block = $args['block'];
$products = $block['products'];
if (!$products) return;
?>
<section class="selected-products-section">
    <div class="selected-products-grid">
        <?php foreach ($products as $product) :
            $image = get_field('product_image', $product->ID);
            $desc = get_field('product_short_description', $product->ID);
            $display_mode = get_field('product_display_mode', $product->ID);
            $acf_specs = get_field('product_specs', $product->ID);

            // Determine specs source
            $specs = array();
            if ($display_mode === 'table' && !empty($acf_specs)) {
                // Use structured ACF specs
                foreach ($acf_specs as $row) {
                    if (!empty($row['spec_label']) && !empty($row['spec_value'])) {
                        $specs[] = array('label' => $row['spec_label'], 'value' => $row['spec_value']);
                    }
                }
            } elseif ($display_mode !== 'text' && $desc) {
                // Legacy fallback: parse from description text (only if mode not explicitly set to "text")
                if (preg_match('/width\s+of\s+(.+?)(?:,|$)/i', $desc, $m)) {
                    $specs[] = array('label' => __('WIDTH', 'axyz'), 'value' => trim($m[1]));
                }
                if (preg_match('/length\s+of\s+(.+?)(?:,|$)/i', $desc, $m)) {
                    $specs[] = array('label' => __('LENGTHS', 'axyz'), 'value' => trim($m[1]));
                }
                if (preg_match('/gantry\s+clearance\s+of\s+(.+?)(?:,|$)/i', $desc, $m)) {
                    $specs[] = array('label' => __('GANTRY CLEARANCE', 'axyz'), 'value' => trim($m[1]));
                }
            }
        ?>
            <div class="selected-product-card">
                <div class="selected-product-card__inner">
                    <?php if ($image) : ?>
                        <div class="selected-product-card__image">
                            <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($product->post_title); ?>" />
                        </div>
                    <?php endif; ?>
                    <h3 class="selected-product-card__title"><?php echo esc_html($product->post_title); ?></h3>
                    <?php if (!empty($specs)) : ?>
                        <div class="selected-product-card__specs">
                            <?php foreach ($specs as $spec) : ?>
                                <div class="selected-product-card__spec-row">
                                    <span class="selected-product-card__spec-label"><?php echo esc_html($spec['label']); ?></span>
                                    <span class="selected-product-card__spec-value"><?php echo esc_html($spec['value']); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php elseif ($desc) : ?>
                        <p class="selected-product-card__desc"><?php echo esc_html($desc); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <!-- Pagination dots (mobile only) -->
    <div class="selected-products-dots d-lg-none"></div>
</section>
