<?php
/**
 * ACF: the three core locale meta fields, as local field groups.
 *
 *   region_language_code  (select)  — the post's locale, e.g. en-us / fr-ca
 *   translation_group_id  (text)    — shared id linking a translation set
 *   is_frontpage          (radio)   — 'yes' on a locale's homepage page (pages only)
 *
 * All three write plain post meta under their own names, which is exactly what
 * routing.php / permalinks.php / seo.php read via get_post_meta()/get_field().
 * The blueprint stored these in the DB; here they travel with the plugin.
 */

if (!defined('ABSPATH')) exit;
if (!function_exists('acf_add_local_field_group')) return;

add_action('acf/init', function () {

    // Post types that carry a locale + translation group.
    $locale_types = ['post', 'products', 'series', 'industry', 'accessories',
                     'testimonial', 'news_and_events', 'webinar', 'blog', 'video', 'page'];

    // region_language_code choices track wj_allowed_locales() when available.
    $codes  = function_exists('wj_allowed_locales')
        ? wj_allowed_locales()
        : ['en-us', 'es-us', 'en-ca', 'fr-ca', 'en-uk', 'pl-pl'];
    $labels = [
        'en-us' => 'en-us — English (US)',
        'es-us' => 'es-us — Spanish (US)',
        'en-ca' => 'en-ca — English (Canada)',
        'fr-ca' => 'fr-ca — French (Canada)',
        'en-uk' => 'en-uk — English (UK)',
        'pl-pl' => 'pl-pl — Polish',
    ];
    $choices = [];
    foreach ($codes as $c) $choices[$c] = $labels[$c] ?? $c;

    $type_location = [];
    foreach ($locale_types as $pt) {
        $type_location[] = [[
            'param'    => 'post_type',
            'operator' => '==',
            'value'    => $pt,
        ]];
    }

    acf_add_local_field_group([
        'key'    => 'group_wj_locale',
        'title'  => 'Locale / Translation',
        'fields' => [
            [
                'key'           => 'field_wj_region_language_code',
                'label'         => 'Region / Language code',
                'name'          => 'region_language_code',
                'type'          => 'select',
                'instructions'  => 'The locale this content belongs to. Drives the /cc/ll/ URL prefix.',
                'choices'       => $choices,
                'default_value' => 'en-us',
                'allow_null'    => 0,
                'ui'            => 1,
                'return_format' => 'value',
            ],
            [
                'key'          => 'field_wj_translation_group_id',
                'label'        => 'Translation group id',
                'name'         => 'translation_group_id',
                'type'         => 'text',
                'instructions' => 'Shared id linking all translations of the same content. Same id across locales.',
            ],
        ],
        'location'        => $type_location,
        'menu_order'      => 0,
        'position'        => 'side',
        'style'           => 'default',
        'label_placement' => 'top',
        'active'          => true,
    ]);

    acf_add_local_field_group([
        'key'    => 'group_wj_frontpage',
        'title'  => 'Locale Homepage',
        'fields' => [
            [
                'key'           => 'field_wj_is_frontpage',
                'label'         => 'Is locale front page?',
                'name'          => 'is_frontpage',
                'type'          => 'radio',
                'instructions'  => 'Set to Yes on the homepage page for this locale (one per locale).',
                'choices'       => ['no' => 'No', 'yes' => 'Yes'],
                'default_value' => 'no',
                'layout'        => 'horizontal',
                'return_format' => 'value',
            ],
        ],
        'location'        => [[[
            'param'    => 'post_type',
            'operator' => '==',
            'value'    => 'page',
        ]]],
        'menu_order'      => 1,
        'position'        => 'side',
        'style'           => 'default',
        'label_placement' => 'top',
        'active'          => true,
    ]);
});
