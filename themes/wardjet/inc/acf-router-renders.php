<?php
/**
 * ACF Fields for Router 3D Renders Carousel
 *
 * @package AXYZ
 */

if (!defined('ABSPATH')) {
    exit;
}

function axyz_register_router_renders_fields() {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_router_renders',
        'title' => __('Product 3D Renders', 'axyz'),
        'fields' => array(
            array(
                'key' => 'field_router_renders',
                'label' => __('3D Render Images', 'axyz'),
                'name' => 'router_renders',
                'type' => 'gallery',
                'instructions' => __('Upload 3D render images of the product for the carousel. Recommended: 3-5 images.', 'axyz'),
                'required' => 0,
                'return_format' => 'array',
                'preview_size' => 'medium',
                'library' => 'all',
                'min' => 0,
                'max' => 10,
                'mime_types' => 'jpg,jpeg,png,webp,avif',
            ),
            array(
                'key' => 'field_features_show_numbers',
                'label' => __('Show Feature Numbers', 'axyz'),
                'name' => 'features_show_numbers',
                'type' => 'true_false',
                'instructions' => __('Display order numbers (1, 2, 3...) on feature cards.', 'axyz'),
                'required' => 0,
                'default_value' => 1,
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'series',
                ),
            ),
        ),
        'menu_order' => 2,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
        'description' => __('3D render images carousel for single product (series) pages.', 'axyz'),
    ));
}

add_action('acf/init', 'axyz_register_router_renders_fields');
