<?php
/**
 * ACF Fields for Products Section
 *
 * Registers title/subtitle fields for the products section on products pages.
 *
 * @package AXYZ
 */

if (!defined('ABSPATH')) {
    exit;
}

function axyz_register_products_section_fields() {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_products_section',
        'title' => __('Products Section', 'axyz'),
        'fields' => array(
            array(
                'key' => 'field_products_section_title',
                'label' => __('Products Section Title', 'axyz'),
                'name' => 'products_section_title',
                'type' => 'text',
                'instructions' => __('Heading above the products grid.', 'axyz'),
                'required' => 0,
                'default_value' => 'Products We Sell',
            ),
            array(
                'key' => 'field_products_section_subtitle',
                'label' => __('Products Section Subtitle', 'axyz'),
                'name' => 'products_section_subtitle',
                'type' => 'textarea',
                'instructions' => __('Description below the heading.', 'axyz'),
                'required' => 0,
                'rows' => 3,
                'default_value' => 'Discover our comprehensive range of CNC routers engineered for precision, reliability, and performance across diverse manufacturing applications',
            ),
            array(
                'key' => 'field_products_cta_label',
                'label' => __('Explore Products Button Label', 'axyz'),
                'name' => 'products_cta_label',
                'type' => 'text',
                'instructions' => __('Label for the "Explore Products" button. Leave empty for default.', 'axyz'),
                'required' => 0,
                'default_value' => 'Explore Products',
            ),
            // Icon Strip
            array(
                'key' => 'field_icon_strip_items',
                'label' => __('Icon Strip Items', 'axyz'),
                'name' => 'icon_strip_items',
                'type' => 'repeater',
                'instructions' => __('4 feature highlights with icons. Leave empty to use defaults.', 'axyz'),
                'required' => 0,
                'min' => 0,
                'max' => 6,
                'layout' => 'table',
                'button_label' => __('Add Item', 'axyz'),
                'sub_fields' => array(
                    array(
                        'key' => 'field_icon_strip_item_title',
                        'label' => __('Title', 'axyz'),
                        'name' => 'title',
                        'type' => 'text',
                        'required' => 1,
                    ),
                    array(
                        'key' => 'field_icon_strip_item_subtitle',
                        'label' => __('Subtitle', 'axyz'),
                        'name' => 'subtitle',
                        'type' => 'text',
                        'required' => 0,
                    ),
                    array(
                        'key' => 'field_icon_strip_item_icon',
                        'label' => __('Icon', 'axyz'),
                        'name' => 'icon',
                        'type' => 'select',
                        'required' => 1,
                        'choices' => array(
                            'trophy'  => __('Trophy', 'axyz'),
                            'globe'   => __('Globe', 'axyz'),
                            'headset' => __('Headset', 'axyz'),
                            'cog'     => __('Cog', 'axyz'),
                        ),
                        'default_value' => 'trophy',
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'page_template',
                    'operator' => '==',
                    'value' => 'wardjet-our-products.php',
                ),
            ),
        ),
        'menu_order' => 1,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
        'description' => __('Title and subtitle for the products grid section.', 'axyz'),
    ));
}

add_action('acf/init', 'axyz_register_products_section_fields');
