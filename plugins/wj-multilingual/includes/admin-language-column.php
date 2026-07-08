<?php
/**
 * Admin "Language" column on locale-aware post-list tables (pages + CPTs), so
 * content managers can see/sort which locale each item belongs to. Shows the
 * uppercased region_language_code (e.g. EN-US, FR-CA). Ported from the blueprint.
 */

if (!defined('ABSPATH')) exit;
if (!defined('WJ_LANGUAGE_COLUMN')) define('WJ_LANGUAGE_COLUMN', 'language_code');

if (!function_exists('wj_add_language_column')) {
    function wj_add_language_column($columns) {
        $new = [];
        foreach ($columns as $key => $label) {
            $new[$key] = $label;
            if ($key === 'title') $new[WJ_LANGUAGE_COLUMN] = 'Language';
        }
        return $new;
    }
}

if (!function_exists('wj_show_language_column')) {
    function wj_show_language_column($column_name, $post_id) {
        if ($column_name === WJ_LANGUAGE_COLUMN) {
            $code = get_post_meta($post_id, 'region_language_code', true);
            echo $code ? esc_html(strtoupper($code)) : 'N/A';
        }
    }
}

if (!function_exists('wj_language_sortable_columns')) {
    function wj_language_sortable_columns($columns) {
        $columns[WJ_LANGUAGE_COLUMN] = WJ_LANGUAGE_COLUMN;
        return $columns;
    }
}

if (!function_exists('wj_language_column_orderby')) {
    function wj_language_column_orderby($query) {
        if (!is_admin() || !$query->is_main_query()) return;
        if ($query->get('orderby') === WJ_LANGUAGE_COLUMN) {
            $query->set('meta_key', 'region_language_code');
            $query->set('orderby', 'meta_value');
        }
    }
    add_action('pre_get_posts', 'wj_language_column_orderby');
}

add_action('admin_head-edit.php', function () {
    echo '<style>.column-' . WJ_LANGUAGE_COLUMN . '{width:110px}</style>';
});

// Locale-aware post types (pages first — the main ask).
foreach (['page', 'post', 'products', 'series', 'industry', 'accessories',
          'testimonial', 'news_and_events', 'webinar', 'blog', 'video'] as $pt) {
    add_filter("manage_{$pt}_posts_columns", 'wj_add_language_column');
    add_action("manage_{$pt}_posts_custom_column", 'wj_show_language_column', 10, 2);
    add_filter("manage_edit-{$pt}_sortable_columns", 'wj_language_sortable_columns');
}
