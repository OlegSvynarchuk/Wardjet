<?php
/**
 * ACF Options Page: Taxonomy Section Labels.
 * Admins can override the blue-bg H2 labels above each FacetWP filter group
 * for English / Spanish / French / Polish, per taxonomy. Empty = use the
 * hardcoded default in wj_get_tax_label().
 */

if (!defined('ABSPATH')) exit;
if (!function_exists('acf_add_local_field_group')) return;

add_action('acf/init', function () {
    if (function_exists('acf_add_options_page')) {
        acf_add_options_page([
            'page_title'  => 'Taxonomy Section Labels',
            'menu_title'  => 'Taxonomy Labels',
            'menu_slug'   => 'wj-tax-labels',
            'capability'  => 'manage_options',
            'parent_slug' => 'options-general.php',
            'icon_url'    => 'dashicons-translation',
            'position'    => 80,
        ]);
    }

    $taxes = [
        'events'           => 'Events',
        'news'             => 'News',
        'blog-industry'    => 'Blog → Industry',
        'blog-material'    => 'Blog → Material',
        'blog-product'     => 'Blog → Product',
        'technical_topics' => 'Blog → Technical Topics',
        'webinar-industry' => 'Webinar → Industry',
        'webinar-material' => 'Webinar → Material',
        'webinar-category' => 'Webinar → Category',
    ];

    $locales = [
        'en_us' => 'English (en-us)',
        'es_us' => 'Spanish (es-us)',
        'fr_ca' => 'French (fr-ca)',
        'pl_pl' => 'Polish (pl-pl)',
    ];

    $fields = [];
    foreach ($taxes as $tax_slug => $tax_label) {
        $tax_norm = str_replace('-', '_', $tax_slug);
        $fields[] = [
            'key'   => 'field_wj_tax_section_' . $tax_norm . '_tab',
            'label' => $tax_label,
            'type'  => 'tab',
            'placement' => 'top',
        ];
        foreach ($locales as $loc_norm => $loc_label) {
            $fields[] = [
                'key'          => 'field_wj_tax_section_' . $tax_norm . '_' . $loc_norm,
                'label'        => $loc_label,
                'name'         => 'tax_section_' . $tax_norm . '_' . $loc_norm,
                'type'         => 'text',
                'instructions' => 'Leave empty to use the built-in default.',
            ];
        }
    }

    acf_add_local_field_group([
        'key'      => 'group_wj_tax_section_labels',
        'title'    => 'Section Labels',
        'fields'   => $fields,
        'location' => [[[
            'param'    => 'options_page',
            'operator' => '==',
            'value'    => 'wj-tax-labels',
        ]]],
        'menu_order'      => 0,
        'position'        => 'normal',
        'style'           => 'default',
        'label_placement' => 'top',
        'active'          => true,
    ]);
});
