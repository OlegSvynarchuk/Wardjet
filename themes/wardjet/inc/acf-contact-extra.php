<?php
/**
 * ACF: Inject extra sub-fields into the contact_localized repeater.
 *
 * Uses the acf/load_field filter to append new sub-fields (location card,
 * global presence, CTA buttons) into the existing DB-stored contact_localized
 * repeater — no field key required, matched by field name.
 */

if ( ! function_exists( 'acf_add_local_field' ) ) {
    return;
}

// ── Base contact_localized repeater (spine) ───────────────────────────────
// The Location Card / Global Presence / CTA sub-fields below are injected on
// top of this via acf/load_field. Registered here so the whole file is the
// single source; read by template-parts/agg-contact.php.
add_action( 'acf/init', function () {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }
    acf_add_local_field_group( array(
        'key'    => 'group_contact_localized',
        'title'  => 'Contact — Localized (right column)',
        'fields' => array(
            array(
                'key'          => 'field_contact_localized',
                'label'        => 'Per-locale contact',
                'name'         => 'contact_localized',
                'type'         => 'repeater',
                'instructions' => 'One row per locale. Populates the contact section right column (location card + global presence).',
                'layout'       => 'block',
                'button_label' => 'Add locale',
                'sub_fields'   => array(
                    array(
                        'key'     => 'field_cl_region_language_code',
                        'label'   => 'Locale',
                        'name'    => 'region_language_code',
                        'type'    => 'select',
                        'choices' => array(
                            'en-us' => 'en-us', 'es-us' => 'es-us', 'en-ca' => 'en-ca',
                            'fr-ca' => 'fr-ca', 'en-uk' => 'en-uk', 'pl-pl' => 'pl-pl',
                        ),
                    ),
                    array( 'key' => 'field_cl_left_heading', 'label' => 'Section Heading',    'name' => 'contact_left_heading', 'type' => 'text' ),
                    array( 'key' => 'field_cl_left_copy',    'label' => 'Section Subheading', 'name' => 'contact_left_copy',    'type' => 'textarea', 'rows' => 2 ),
                    array( 'key' => 'field_cl_form_url',     'label' => 'JotForm URL',        'name' => 'contact_form',         'type' => 'text',
                        'instructions' => 'Full jotform.com/jsform/{id} URL for this locale (fallback when the page has no contact_form).' ),
                ),
            ),
        ),
        'location'   => array( array( array( 'param' => 'options_page', 'operator' => '==', 'value' => 'acf-options-footer' ) ) ),
        'menu_order' => 20,
        'active'     => true,
    ) );
} );

add_filter( 'acf/load_field', function( $field ) {

    // Target only the contact_localized repeater
    if ( empty( $field['name'] ) || $field['name'] !== 'contact_localized' ) {
        return $field;
    }

    $extra_sub_fields = [

        // ── Tab: Form Settings ───────────────────────────────────
        [
            'key'   => 'field_cform_tab',
            'label' => 'Form Settings',
            'name'  => '',
            'type'  => 'tab',
        ],
        [
            'key'           => 'field_cform_type',
            'label'         => 'Form Type',
            'name'          => 'contact_form_type',
            'type'          => 'select',
            'instructions'  => 'Select which form system to use. JotForm URL is kept as fallback.',
            'choices'       => [
                'gravity' => 'Gravity Forms',
                'jotform' => 'JotForm (legacy)',
            ],
            'default_value' => 'gravity',
        ],
        [
            'key'          => 'field_cform_gravity_id',
            'label'        => 'Gravity Form ID',
            'name'         => 'contact_gravity_form_id',
            'type'         => 'number',
            'instructions' => 'Enter the Gravity Forms ID (e.g. 1 for "Get in Touch").',
            'placeholder'  => '1',
            'conditional_logic' => [
                [
                    [
                        'field' => 'field_cform_type',
                        'operator' => '==',
                        'value' => 'gravity',
                    ],
                ],
            ],
        ],

        // ── Tab: Location Card ────────────────────────────────────
        [
            'key'   => 'field_cloc_tab',
            'label' => 'Location Card',
            'name'  => '',
            'type'  => 'tab',
        ],
        [
            'key'          => 'field_cloc_title',
            'label'        => 'Location Title',
            'name'         => 'contact_location_title',
            'type'         => 'text',
            'instructions' => 'e.g. Canada Headquarters (Manufacturing Site)',
            'placeholder'  => 'Canada Headquarters (Manufacturing Site)',
        ],
        [
            'key'         => 'field_cloc_company',
            'label'       => 'Company Name',
            'name'        => 'contact_location_company',
            'type'        => 'text',
            'placeholder' => 'AXYZ AUTOMATION GROUP (AAG)',
        ],
        [
            'key'          => 'field_cloc_address',
            'label'        => 'Address',
            'name'         => 'contact_location_address',
            'type'         => 'textarea',
            'instructions' => 'One line per address row.',
            'rows'         => 4,
            // Address CSS uses `white-space: pre-line`, so keep raw newlines
            // (NOT nl2br — that would add <br> on top of the \n and double-space).
            'new_lines'    => '',
        ],
        [
            'key'         => 'field_cloc_phone',
            'label'       => 'Phone Number',
            'name'        => 'contact_location_phone',
            'type'        => 'text',
            'placeholder' => '+1 (800) 361-3408',
        ],

        // ── Tab: Global Presence ──────────────────────────────────
        [
            'key'   => 'field_cglob_tab',
            'label' => 'Global Presence Card',
            'name'  => '',
            'type'  => 'tab',
        ],
        [
            'key'         => 'field_cglob_heading',
            'label'       => 'Heading',
            'name'        => 'contact_global_heading',
            'type'        => 'text',
            'placeholder' => 'Global Presence',
        ],
        [
            'key'          => 'field_cglob_copy',
            'label'        => 'Body Copy',
            'name'         => 'contact_global_copy',
            'type'         => 'wysiwyg',
            'toolbar'      => 'basic',
            'media_upload' => 0,
        ],
        [
            'key'          => 'field_cglob_regions',
            'label'        => 'Regions',
            'name'         => 'contact_global_regions',
            'type'         => 'repeater',
            'instructions' => 'Add up to 4 regions (displayed in a 2×2 grid).',
            'layout'       => 'table',
            'button_label' => 'Add Region',
            'sub_fields'   => [
                [
                    'key'          => 'field_cglob_region_name',
                    'label'        => 'Region Name',
                    'name'         => 'region_name',
                    'type'         => 'text',
                    'placeholder'  => 'North America',
                    'column_width' => '40',
                ],
                [
                    'key'          => 'field_cglob_region_countries',
                    'label'        => 'Countries',
                    'name'         => 'region_countries',
                    'type'         => 'text',
                    'placeholder'  => 'USA, Canada, Mexico',
                    'column_width' => '60',
                ],
            ],
        ],

        // ── Tab: CTA Buttons ──────────────────────────────────────
        [
            'key'   => 'field_ccta_tab',
            'label' => 'CTA Buttons',
            'name'  => '',
            'type'  => 'tab',
        ],
        [
            'key'          => 'field_ccta_email_label',
            'label'        => 'Email Us Button Label',
            'name'         => 'contact_email_us_label',
            'type'         => 'text',
            'instructions' => 'Visible button text (e.g. Email Us / Escríbenos).',
        ],
        [
            'key'           => 'field_ccta_email',
            'label'         => 'Email Us Button Link',
            'name'          => 'contact_email_us',
            'type'          => 'link',
            'instructions'  => 'Use a mailto: URL for email addresses.',
            'return_format' => 'array',
        ],
        [
            'key'          => 'field_ccta_call_label',
            'label'        => 'Call Us Button Label',
            'name'         => 'contact_call_us_label',
            'type'         => 'text',
            'instructions' => 'Visible button text (e.g. Call Us / Llámenos).',
        ],
        [
            'key'           => 'field_ccta_call',
            'label'         => 'Call Us Button Link',
            'name'          => 'contact_call_us',
            'type'          => 'link',
            'instructions'  => 'Use a tel: URL for phone numbers.',
            'return_format' => 'array',
        ],
    ];

    // Append only fields not already present (avoid duplicates on repeated calls).
    // Run each injected field (and its nested sub-fields) through acf_get_valid_field()
    // so ACF fills in its default keys (_name, multiple, return_format, …); otherwise
    // the repeater/select field classes emit "Undefined array key" notices.
    $validate = function ( $f ) use ( &$validate ) {
        if ( function_exists( 'acf_get_valid_field' ) ) {
            $f = acf_get_valid_field( $f );
        }
        if ( ! empty( $f['sub_fields'] ) && is_array( $f['sub_fields'] ) ) {
            foreach ( $f['sub_fields'] as $i => $sub ) {
                $f['sub_fields'][ $i ] = $validate( $sub );
            }
        }
        return $f;
    };

    $existing_names = array_column( $field['sub_fields'] ?? [], 'name' );

    foreach ( $extra_sub_fields as $sub ) {
        if ( empty( $sub['name'] ) || ! in_array( $sub['name'], $existing_names, true ) ) {
            $field['sub_fields'][] = $validate( $sub );
        }
    }

    return $field;
} );
