<?php
/**
 * ACF: per-locale term label fields on the content CPT taxonomies.
 * Three text fields (es-us, fr-ca, pl-pl) appear on every term edit screen.
 * Empty translation = fall back to the default English term name.
 */

if (!defined('ABSPATH')) exit;
if (!function_exists('acf_add_local_field_group')) return;

add_action('acf/init', function () {
    $taxes = function_exists('wj_term_loc_taxonomies') ? wj_term_loc_taxonomies() : [
        'events', 'news',
        'blog-industry', 'blog-material', 'blog-product', 'technical_topics',
        'webinar-industry', 'webinar-material', 'webinar-category',
        'testimonial_industries',
    ];

    $location = [];
    foreach ($taxes as $t) {
        $location[] = [[
            'param'    => 'taxonomy',
            'operator' => '==',
            'value'    => $t,
        ]];
    }

    acf_add_local_field_group([
        'key'      => 'group_wj_term_localization',
        'title'    => 'Localized Labels',
        'fields'   => [
            [
                'key'          => 'field_wj_term_label_es_us',
                'label'        => 'Spanish (es-us)',
                'name'         => 'label_es_us',
                'type'         => 'text',
                'instructions' => 'Spanish label shown on /us/es/. Leave empty to keep the default English name.',
            ],
            [
                'key'          => 'field_wj_term_label_fr_ca',
                'label'        => 'French (fr-ca)',
                'name'         => 'label_fr_ca',
                'type'         => 'text',
                'instructions' => 'French label shown on /ca/fr/. Leave empty to keep the default English name.',
            ],
            [
                'key'          => 'field_wj_term_label_pl_pl',
                'label'        => 'Polish (pl-pl)',
                'name'         => 'label_pl_pl',
                'type'         => 'text',
                'instructions' => 'Polish label shown on /pl/pl/. Leave empty to keep the default English name.',
            ],
        ],
        'location'        => $location,
        'menu_order'      => 0,
        'position'        => 'normal',
        'style'           => 'default',
        'label_placement' => 'top',
        'active'           => true,
    ]);
});
