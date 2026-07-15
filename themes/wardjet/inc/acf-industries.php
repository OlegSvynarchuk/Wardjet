<?php
/**
 * ACF fields for the Industry CPT (ported from the blueprint).
 *
 * `industry_features` powers the hover list on the homepage industries grid
 * (template-parts/ind-mat-grid.php renders up to 4 of them inside
 * .ind-mat-grid__features, revealed on card hover).
 *
 * NB: region_language_code / translation_group_id are intentionally NOT
 * registered here — the wj-multilingual plugin already provides them
 * (includes/acf-locale-fields.php); duplicating would create two field groups
 * writing the same meta.
 *
 * @package wardjet
 */

if (!defined('ABSPATH')) {
    exit;
}

function wardjet_register_industry_fields() {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key'    => 'group_industry',
        'title'  => __('Industry', 'wardjet'),
        'fields' => array(
            array(
                'key'          => 'field_industry_features',
                'label'        => __('Industry Features', 'wardjet'),
                'name'         => 'industry_features',
                'type'         => 'repeater',
                'instructions' => __('List of sub-items for this industry. The first 4 are shown on the homepage grid card when hovered.', 'wardjet'),
                'required'     => 0,
                'layout'       => 'vertical',
                'button_label' => __('Add Feature', 'wardjet'),
                'sub_fields'   => array(
                    array(
                        'key'         => 'field_industry_feature_item',
                        'label'       => __('Feature', 'wardjet'),
                        'name'        => 'feature',
                        'type'        => 'text',
                        'required'    => 0,
                        'placeholder' => __('e.g., Aircraft Components', 'wardjet'),
                    ),
                ),
            ),
            array(
                'key'           => 'field_industry_grid_image',
                'label'         => __('Grid Image', 'wardjet'),
                'name'          => 'grid_image',
                'type'          => 'image',
                'instructions'  => __('Background image for the homepage industries grid card. Falls back to the featured image, then the mosaic icon.', 'wardjet'),
                'required'      => 0,
                'return_format' => 'array',
                'preview_size'  => 'medium',
                'library'       => 'all',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => 'industry',
                ),
            ),
        ),
        'menu_order'      => 2,
        'position'        => 'normal',
        'style'           => 'default',
        'label_placement' => 'top',
        'active'          => true,
        'description'     => __('Industry grid card: hover feature list + grid image.', 'wardjet'),
    ));
}
add_action('acf/init', 'wardjet_register_industry_fields');
