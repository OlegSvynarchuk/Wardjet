<?php
/**
 * ACF Fields for Locations Page
 *
 * Registers the locations field group for the locations pages (US-EN, UK-EN, CA-EN).
 *
 * @package AXYZ
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register ACF fields for locations page
 */
function axyz_register_locations_fields() {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_locations_page',
        'title' => __('Locations Page', 'axyz'),
        'fields' => array(
            // Section 1: Title & Subtitle
            array(
                'key' => 'field_locations_title',
                'label' => __('Section Title', 'axyz'),
                'name' => 'locations_title',
                'type' => 'text',
                'instructions' => __('Main heading for the locations page.', 'axyz'),
                'required' => 0,
                'default_value' => 'Locations',
            ),
            array(
                'key' => 'field_locations_subtitle',
                'label' => __('Section Subtitle', 'axyz'),
                'name' => 'locations_subtitle',
                'type' => 'textarea',
                'instructions' => __('Description text below the main heading.', 'axyz'),
                'required' => 0,
                'rows' => 3,
            ),
            // Section 2: Primary Location
            array(
                'key' => 'field_primary_region_title',
                'label' => __('Primary Region Title', 'axyz'),
                'name' => 'primary_region_title',
                'type' => 'text',
                'instructions' => __('Region name displayed above the location cards (e.g. "Canada").', 'axyz'),
                'required' => 0,
            ),
            array(
                'key' => 'field_primary_location_groups',
                'label' => __('Primary Location Groups', 'axyz'),
                'name' => 'primary_location_groups',
                'type' => 'repeater',
                'instructions' => __('Add location groups within the primary region. Each group has its own image and layout direction.', 'axyz'),
                'required' => 0,
                'min' => 1,
                'max' => 10,
                'layout' => 'block',
                'button_label' => __('Add Location Group', 'axyz'),
                'sub_fields' => array(
                    array(
                        'key' => 'field_primary_group_layout',
                        'label' => __('Layout', 'axyz'),
                        'name' => 'layout',
                        'type' => 'select',
                        'instructions' => __('Regular: cards left, image right. Reverse: image left, cards right.', 'axyz'),
                        'required' => 1,
                        'choices' => array(
                            'regular' => __('Regular (cards left, image right)', 'axyz'),
                            'reverse' => __('Reverse (image left, cards right)', 'axyz'),
                        ),
                        'default_value' => 'regular',
                    ),
                    array(
                        'key' => 'field_primary_group_image',
                        'label' => __('Group Image', 'axyz'),
                        'name' => 'group_image',
                        'type' => 'image',
                        'instructions' => __('Facility photo for this group.', 'axyz'),
                        'required' => 0,
                        'return_format' => 'array',
                        'preview_size' => 'medium',
                        'library' => 'all',
                    ),
                    array(
                        'key' => 'field_primary_group_locations',
                        'label' => __('Locations', 'axyz'),
                        'name' => 'locations',
                        'type' => 'repeater',
                        'instructions' => __('Add office locations within this group.', 'axyz'),
                        'required' => 0,
                        'min' => 1,
                        'max' => 10,
                        'layout' => 'block',
                        'button_label' => __('Add Location', 'axyz'),
                        'sub_fields' => array(
                            array(
                                'key' => 'field_primary_grp_loc_name',
                                'label' => __('Location Name', 'axyz'),
                                'name' => 'location_name',
                                'type' => 'text',
                                'instructions' => __('e.g. "Canada Headquarters (Manufacturing Site)"', 'axyz'),
                                'required' => 1,
                            ),
                            array(
                                'key' => 'field_primary_grp_loc_company',
                                'label' => __('Company Name', 'axyz'),
                                'name' => 'company_name',
                                'type' => 'text',
                                'instructions' => __('e.g. "AXYZ AUTOMATION GROUP (AAG)"', 'axyz'),
                                'required' => 0,
                            ),
                            array(
                                'key' => 'field_primary_grp_loc_address',
                                'label' => __('Address', 'axyz'),
                                'name' => 'address',
                                'type' => 'textarea',
                                'instructions' => __('Full address, one line per row.', 'axyz'),
                                'required' => 0,
                                'rows' => 4,
                                'new_lines' => 'br',
                            ),
                            array(
                                'key' => 'field_primary_grp_loc_phones',
                                'label' => __('Phone Numbers', 'axyz'),
                                'name' => 'phones',
                                'type' => 'repeater',
                                'instructions' => __('Add one or more phone numbers.', 'axyz'),
                                'required' => 0,
                                'min' => 0,
                                'max' => 5,
                                'layout' => 'table',
                                'button_label' => __('Add Phone', 'axyz'),
                                'sub_fields' => array(
                                    array(
                                        'key' => 'field_primary_grp_loc_phone',
                                        'label' => __('Phone Number', 'axyz'),
                                        'name' => 'phone_number',
                                        'type' => 'text',
                                        'required' => 0,
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            // Section 3: Other Locations Title
            array(
                'key' => 'field_other_locations_title',
                'label' => __('Other Locations Title', 'axyz'),
                'name' => 'other_locations_title',
                'type' => 'text',
                'instructions' => __('Heading for the other locations section (e.g. "Other Locations").', 'axyz'),
                'required' => 0,
                'default_value' => 'Other Locations',
            ),
            // Section 4: Other Locations Regions
            array(
                'key' => 'field_other_locations_regions',
                'label' => __('Other Locations Regions', 'axyz'),
                'name' => 'other_locations_regions',
                'type' => 'repeater',
                'instructions' => __('Add region groups. Layout alternates automatically (1st: cards left/image right, 2nd: image left/cards right, etc.).', 'axyz'),
                'required' => 0,
                'min' => 0,
                'max' => 20,
                'layout' => 'block',
                'button_label' => __('Add Region', 'axyz'),
                'sub_fields' => array(
                    array(
                        'key' => 'field_other_region_layout',
                        'label' => __('Layout', 'axyz'),
                        'name' => 'layout',
                        'type' => 'select',
                        'instructions' => __('Regular: cards left, image right. Reverse: image left, cards right.', 'axyz'),
                        'required' => 1,
                        'choices' => array(
                            'regular' => __('Regular (cards left, image right)', 'axyz'),
                            'reverse' => __('Reverse (image left, cards right)', 'axyz'),
                        ),
                        'default_value' => 'regular',
                    ),
                    array(
                        'key' => 'field_other_region_title',
                        'label' => __('Region Title', 'axyz'),
                        'name' => 'region_title',
                        'type' => 'text',
                        'instructions' => __('e.g. "United States of America (USA)". Leave empty to continue previous region without a title.', 'axyz'),
                        'required' => 0,
                    ),
                    array(
                        'key' => 'field_other_region_image',
                        'label' => __('Region Image', 'axyz'),
                        'name' => 'region_image',
                        'type' => 'image',
                        'instructions' => __('Facility photo for this region.', 'axyz'),
                        'required' => 0,
                        'return_format' => 'array',
                        'preview_size' => 'medium',
                        'library' => 'all',
                    ),
                    array(
                        'key' => 'field_other_region_locations',
                        'label' => __('Locations', 'axyz'),
                        'name' => 'locations',
                        'type' => 'repeater',
                        'instructions' => __('Add offices within this region.', 'axyz'),
                        'required' => 0,
                        'min' => 1,
                        'max' => 10,
                        'layout' => 'block',
                        'button_label' => __('Add Location', 'axyz'),
                        'sub_fields' => array(
                            array(
                                'key' => 'field_other_loc_name',
                                'label' => __('Location Name', 'axyz'),
                                'name' => 'location_name',
                                'type' => 'text',
                                'required' => 1,
                            ),
                            array(
                                'key' => 'field_other_loc_company',
                                'label' => __('Company Name', 'axyz'),
                                'name' => 'company_name',
                                'type' => 'text',
                                'required' => 0,
                            ),
                            array(
                                'key' => 'field_other_loc_address',
                                'label' => __('Address', 'axyz'),
                                'name' => 'address',
                                'type' => 'textarea',
                                'required' => 0,
                                'rows' => 4,
                                'new_lines' => 'br',
                            ),
                            array(
                                'key' => 'field_other_loc_phones',
                                'label' => __('Phone Numbers', 'axyz'),
                                'name' => 'phones',
                                'type' => 'repeater',
                                'required' => 0,
                                'min' => 0,
                                'max' => 5,
                                'layout' => 'table',
                                'button_label' => __('Add Phone', 'axyz'),
                                'sub_fields' => array(
                                    array(
                                        'key' => 'field_other_loc_phone_number',
                                        'label' => __('Phone Number', 'axyz'),
                                        'name' => 'phone_number',
                                        'type' => 'text',
                                        'required' => 0,
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
        'location' => array(
            array(array('param' => 'post', 'operator' => '==', 'value' => '11850')), // en-us
            array(array('param' => 'post', 'operator' => '==', 'value' => '12678')), // en-ca
            array(array('param' => 'post', 'operator' => '==', 'value' => '12728')), // en-uk
            array(array('param' => 'post', 'operator' => '==', 'value' => '13241')), // es-us
            array(array('param' => 'post', 'operator' => '==', 'value' => '13243')), // fr-ca
            array(array('param' => 'post', 'operator' => '==', 'value' => '13245')), // pl-pl
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => __('Locations page fields for title, subtitle, and primary region.', 'axyz'),
    ));
}

add_action('acf/init', 'axyz_register_locations_fields');
