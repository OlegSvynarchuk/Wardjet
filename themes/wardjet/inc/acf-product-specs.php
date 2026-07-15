<?php
/**
 * ACF Fields for Product Series CPT — Display Mode + Specs Table
 *
 * Adds structured specs fields alongside existing product_short_description.
 * Content editor can switch between "Table" (structured) and "Text" (free text) display.
 *
 * @package AXYZ
 */

if (!defined('ABSPATH')) {
    exit;
}

function axyz_register_product_specs_fields() {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_product_specs',
        'title' => __('Product Card Display', 'axyz'),
        'fields' => array(
            array(
                'key' => 'field_product_display_mode',
                'label' => __('Card Display Mode', 'axyz'),
                'name' => 'product_display_mode',
                'type' => 'select',
                'instructions' => __('Choose how this product is displayed on the selected products grid. "Table" shows structured specs rows. "Text" shows the description as free text.', 'axyz'),
                'required' => 0,
                'choices' => array(
                    'text'  => __('Text (free description)', 'axyz'),
                    'table' => __('Table (structured specs)', 'axyz'),
                ),
                'default_value' => 'text',
                'return_format' => 'value',
            ),
            array(
                'key' => 'field_product_specs',
                'label' => __('Product Specs', 'axyz'),
                'name' => 'product_specs',
                'type' => 'repeater',
                'instructions' => __('Add spec rows (e.g. WIDTH → 5\' to 7\'). Only used when display mode is "Table".', 'axyz'),
                'required' => 0,
                'min' => 0,
                'max' => 10,
                'layout' => 'table',
                'button_label' => __('Add Spec Row', 'axyz'),
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_product_display_mode',
                            'operator' => '==',
                            'value' => 'table',
                        ),
                    ),
                ),
                'sub_fields' => array(
                    array(
                        'key' => 'field_product_spec_label',
                        'label' => __('Label', 'axyz'),
                        'name' => 'spec_label',
                        'type' => 'text',
                        'instructions' => __('e.g. WIDTH, LENGTH, GANTRY CLEARANCE', 'axyz'),
                        'required' => 1,
                        'wrapper' => array('width' => '30'),
                    ),
                    array(
                        'key' => 'field_product_spec_value',
                        'label' => __('Value', 'axyz'),
                        'name' => 'spec_value',
                        'type' => 'text',
                        'instructions' => __('e.g. 5\' to 7\'', 'axyz'),
                        'required' => 1,
                        'wrapper' => array('width' => '70'),
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'products',
                ),
            ),
        ),
        'menu_order' => 5,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
        'description' => __('Controls how this product series appears on selected products grids.', 'axyz'),
    ));
}

add_action('acf/init', 'axyz_register_product_specs_fields');
