<?php
/**
 * ACF: "Our Companies" section field group.
 *
 * Ported 1:1 from the axyz homepage group (identical field keys/names) so the
 * companies repeater (logo / heading / cta) maps exactly. Registered here in
 * PHP because axyz stored this group in the DB rather than an inc/ file.
 *
 * @package wardjet
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key'    => 'group_our_companies',
        'title'  => 'Our Companies',
        'fields' => array(
            array(
                'key'   => 'field_60f076a1085af',
                'label' => 'Companies Heading',
                'name'  => 'companies_heading',
                'type'  => 'text',
            ),
            array(
                'key'   => 'field_60f076aa085b0',
                'label' => 'Companies Copy',
                'name'  => 'companies_copy',
                'type'  => 'textarea',
                'rows'  => 3,
            ),
            array(
                'key'           => 'field_6180bda8c6863',
                'label'         => 'Companies Image',
                'name'          => 'companies_image',
                'type'          => 'image',
                'return_format' => 'array',
            ),
            array(
                'key'          => 'field_60f076bf085b1',
                'label'        => 'Companies',
                'name'         => 'companies',
                'type'         => 'repeater',
                'layout'       => 'block',
                'button_label' => 'Add Company',
                'sub_fields'   => array(
                    array(
                        'key'           => 'field_60f076cc085b2',
                        'label'         => 'Logo',
                        'name'          => 'logo',
                        'type'          => 'image',
                        'return_format' => 'id',
                    ),
                    array(
                        'key'   => 'field_61439b23fd1f3',
                        'label' => 'Heading',
                        'name'  => 'heading',
                        'type'  => 'text',
                    ),
                    array(
                        'key'   => 'field_6143952bf380b',
                        'label' => 'Content',
                        'name'  => 'content',
                        'type'  => 'wysiwyg',
                    ),
                    array(
                        'key'           => 'field_60f076dc085b4',
                        'label'         => 'CTA',
                        'name'          => 'cta',
                        'type'          => 'link',
                        'return_format' => 'array',
                    ),
                    array(
                        'key'           => 'field_60f076d5085b3',
                        'label'         => 'Picture',
                        'name'          => 'picture',
                        'type'          => 'image',
                        'return_format' => 'array',
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param'    => 'page_template',
                    'operator' => '==',
                    'value'    => 'wardjet-homepage.php',
                ),
            ),
        ),
        'menu_order' => 5,
        'position'   => 'normal',
        'style'      => 'default',
        'active'     => true,
    ));
});
