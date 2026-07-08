<?php
/**
 * ACF Fields for Features Slider
 *
 * Registers the features slider field group for the homepage.
 *
 * @package AXYZ
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register ACF fields for features slider
 */
function axyz_register_features_slider_fields() {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_features_slider',
        'title' => __('Features Slider', 'axyz'),
        'fields' => array(
            // Section Header
            array(
                'key' => 'field_features_slider_title',
                'label' => __('Section Title', 'axyz'),
                'name' => 'features_slider_title',
                'type' => 'text',
                'instructions' => __('Main heading for the features slider section.', 'axyz'),
                'required' => 0,
                'placeholder' => __('CNC Router Features', 'axyz'),
                'default_value' => 'CNC Router Features',
            ),
            array(
                'key' => 'field_features_slider_subtitle',
                'label' => __('Section Subtitle', 'axyz'),
                'name' => 'features_slider_subtitle',
                'type' => 'textarea',
                'instructions' => __('Description text below the main heading.', 'axyz'),
                'required' => 0,
                'rows' => 2,
                'placeholder' => __('Explore our comprehensive range of cutting-edge accessories...', 'axyz'),
                'default_value' => 'Explore our comprehensive range of cutting-edge accessories and technologies designed to maximize productivity and precision.',
            ),
            // Slider Items Repeater
            array(
                'key' => 'field_features_slider_items',
                'label' => __('Slider Items', 'axyz'),
                'name' => 'features_slider_items',
                'type' => 'repeater',
                'instructions' => __('Add feature items to display in the carousel. Each slide shows 3 items.', 'axyz'),
                'required' => 0,
                'min' => 1,
                'max' => 50,
                'layout' => 'block',
                'button_label' => __('Add Feature Item', 'axyz'),
                'sub_fields' => array(
                    array(
                        'key' => 'field_feature_image',
                        'label' => __('Image', 'axyz'),
                        'name' => 'image',
                        'type' => 'image',
                        'instructions' => __('Feature image. Recommended size: 420x200px.', 'axyz'),
                        'required' => 0,
                        'return_format' => 'array',
                        'preview_size' => 'medium',
                        'library' => 'all',
                    ),
                    array(
                        'key' => 'field_feature_title',
                        'label' => __('Title (Full)', 'axyz'),
                        'name' => 'title',
                        'type' => 'text',
                        'instructions' => __('Full feature title.', 'axyz'),
                        'required' => 1,
                        'placeholder' => __('XL Tool Changer', 'axyz'),
                    ),
                    array(
                        'key' => 'field_feature_short_title',
                        'label' => __('Short Title', 'axyz'),
                        'name' => 'short_title',
                        'type' => 'text',
                        'instructions' => __('2-3 word short title for card display.', 'axyz'),
                        'required' => 0,
                        'placeholder' => __('XL Tool Changer', 'axyz'),
                    ),
                    array(
                        'key' => 'field_feature_description',
                        'label' => __('Description', 'axyz'),
                        'name' => 'description',
                        'type' => 'textarea',
                        'instructions' => __('Brief description of the feature.', 'axyz'),
                        'required' => 0,
                        'rows' => 3,
                        'placeholder' => __('Description text...', 'axyz'),
                    ),
                    array(
                        'key' => 'field_feature_link',
                        'label' => __('Link', 'axyz'),
                        'name' => 'link',
                        'type' => 'url',
                        'instructions' => __('Optional link for this feature.', 'axyz'),
                        'required' => 0,
                        'placeholder' => __('https://...', 'axyz'),
                    ),
                ),
            ),
            // Slider Settings
            array(
                'key' => 'field_features_slider_autoplay',
                'label' => __('Auto-play', 'axyz'),
                'name' => 'features_slider_autoplay',
                'type' => 'true_false',
                'instructions' => __('Enable automatic slide advancement.', 'axyz'),
                'required' => 0,
                'default_value' => 1,
                'ui' => 1,
            ),
            array(
                'key' => 'field_features_slider_interval',
                'label' => __('Slide Interval (ms)', 'axyz'),
                'name' => 'features_slider_interval',
                'type' => 'number',
                'instructions' => __('Time between slide transitions in milliseconds.', 'axyz'),
                'required' => 0,
                'default_value' => 5000,
                'min' => 1000,
                'max' => 30000,
                'step' => 500,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'page_template',
                    'operator' => '==',
                    'value' => 'wardjet-homepage.php',
                ),
            ),
        ),
        'menu_order' => 1,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => __('Features slider section for the homepage.', 'axyz'),
    ));
}

// Hook to register fields after ACF is loaded
add_action('acf/init', 'axyz_register_features_slider_fields');
