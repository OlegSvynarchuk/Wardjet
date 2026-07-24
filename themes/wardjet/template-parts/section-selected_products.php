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
                // Editor-defined structured ACF specs (the "table" display mode).
                foreach ($acf_specs as $row) {
                    if (!empty($row['spec_label']) && !empty($row['spec_value'])) {
                        $specs[] = array('label' => $row['spec_label'], 'value' => $row['spec_value']);
                    }
                }
            } elseif ($display_mode !== 'text' && $desc) {
                // Auto-parse the WARDJET spec sentence into LENGTH / WIDTH / Z-TRAVEL rows, e.g.:
                //   "Cutting area is 2' (0.6m) in length x 4' (1.2m) in width with 4" (.10m) in Z-Travel."
                //   "...cutting area that is 8.2' (2.5 m) in length and 13.1' (4.0m) in width."
                // A value is a number followed by a "(...)" metric (e.g. 2' (0.6m)); the parenthesis
                // boundaries keep each keyword from grabbing another dimension's value. Prose
                // descriptions match nothing and fall through to the paragraph below.
                $val = '\d[\d.,]*[^()a-zA-Z]*\([^)]*\)';
                // Normalize bare decimals to a leading zero (.25m -> 0.25m), but only where the
                // dot isn't already preceded by a digit — so 0.6m / 2.4m / 8.2' stay untouched.
                $norm = function ($v) { return preg_replace('/(?<!\d)\.(\d)/', '0.$1', trim($v)); };
                if (preg_match('/(' . $val . ')\s+in\s+length/i', $desc, $m)) {
                    $specs[] = array('label' => __('LENGTH', 'axyz'), 'value' => $norm($m[1]));
                }
                if (preg_match('/(' . $val . ')\s+in\s+width/i', $desc, $m)) {
                    $specs[] = array('label' => __('WIDTH', 'axyz'), 'value' => $norm($m[1]));
                }
                if (preg_match('/(' . $val . ')\s+in\s+z-?\s*travel/i', $desc, $m)) {
                    $specs[] = array('label' => __('Z-TRAVEL', 'axyz'), 'value' => $norm($m[1]));
                }
                // Legacy axyz phrasing fallback (no-op on WARDJET content; kept for safety).
                if (empty($specs)) {
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
