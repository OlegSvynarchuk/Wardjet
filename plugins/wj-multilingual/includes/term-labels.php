<?php
/**
 * Term-label localization for content CPT taxonomies.
 *
 * Translations live in term meta keys: label_es_us, label_fr_ca, label_pl_pl.
 * Default term name (English) is used for en-us and as fallback.
 * Display only — admin and DB keep the original term name.
 *
 * Performance notes (vs. the original theme implementation):
 *   - Taxonomies list flipped to O(1) isset() lookup (was in_array).
 *   - lc_get_locale_from_url() is already memoized in locale.php.
 *   - Per-request term-meta cache: same term traversed by get_term + get_terms
 *     in one render now hits the DB once.
 *   - Early bail when locale is en-us: every filter returns immediately
 *     without examining the term, since en-us is the canonical name.
 */

if (!defined('ABSPATH')) exit;

if (!function_exists('wj_term_loc_taxonomies')) {
    function wj_term_loc_taxonomies(): array {
        return [
            'events', 'news',
            'blog-industry', 'blog-material', 'blog-product', 'technical_topics',
            'webinar-industry', 'webinar-material', 'webinar-category',
            'testimonial_industries',
        ];
    }
}

/**
 * O(1) lookup for "is this taxonomy localized?" — replaces in_array() in hot filters.
 */
if (!function_exists('wj_term_loc_taxonomies_lookup')) {
    function wj_term_loc_taxonomies_lookup(): array {
        static $flipped = null;
        if ($flipped === null) {
            $flipped = array_flip(wj_term_loc_taxonomies());
        }
        return $flipped;
    }
}

if (!function_exists('wj_term_loc_current_locale')) {
    function wj_term_loc_current_locale(): string {
        return function_exists('lc_get_locale_from_url') ? lc_get_locale_from_url() : 'en-us';
    }
}

if (!function_exists('wj_term_loc_meta_key')) {
    function wj_term_loc_meta_key(string $locale): string {
        return 'label_' . str_replace('-', '_', strtolower($locale));
    }
}

if (!function_exists('wj_get_term_label')) {
    /**
     * Resolve a term's localized label, with per-request cache.
     * Returns $fallback (the term name) for en-us or when no override exists.
     */
    function wj_get_term_label(int $term_id, ?string $locale = null, $fallback = null) {
        if ($locale === null) $locale = wj_term_loc_current_locale();
        if ($locale === 'en-us') return $fallback;

        static $cache = [];
        $key = $term_id . '|' . $locale;
        if (!array_key_exists($key, $cache)) {
            $val = get_term_meta($term_id, wj_term_loc_meta_key($locale), true);
            $cache[$key] = ($val !== '' && $val !== null) ? $val : null;
        }
        return $cache[$key] !== null ? $cache[$key] : $fallback;
    }
}

if (!function_exists('wj_get_tax_label')) {
    /**
     * Localized H2/section label for a taxonomy on archive pages.
     * Priority: ACF Options Page override → hardcoded map → English → ucfirst slug.
     */
    function wj_get_tax_label(string $tax, ?string $locale = null): string {
        if ($locale === null) $locale = wj_term_loc_current_locale();

        if (function_exists('get_field')) {
            $tax_norm = str_replace('-', '_', $tax);
            $loc_norm = str_replace('-', '_', $locale);
            $override = get_field('tax_section_' . $tax_norm . '_' . $loc_norm, 'option');
            if (is_string($override) && $override !== '') return $override;
        }

        $map = [
            'events'             => ['en-us' => 'Events',           'es-us' => 'Eventos',          'fr-ca' => 'Événements',     'pl-pl' => 'Wydarzenia'],
            'news'               => ['en-us' => 'News',             'es-us' => 'Noticias',         'fr-ca' => 'Actualités',     'pl-pl' => 'Aktualności'],
            'blog-industry'      => ['en-us' => 'Industry',         'es-us' => 'Industria',        'fr-ca' => 'Industrie',      'pl-pl' => 'Branża'],
            'blog-material'      => ['en-us' => 'Material',         'es-us' => 'Material',         'fr-ca' => 'Matériau',       'pl-pl' => 'Materiał'],
            'blog-product'       => ['en-us' => 'Product',          'es-us' => 'Producto',         'fr-ca' => 'Produit',        'pl-pl' => 'Produkt'],
            'technical_topics'   => ['en-us' => 'Technical Topics', 'es-us' => 'Temas Técnicos',   'fr-ca' => 'Sujets Techniques','pl-pl' => 'Tematy Techniczne'],
            'webinar-industry'   => ['en-us' => 'Industry',         'es-us' => 'Industria',        'fr-ca' => 'Industrie',      'pl-pl' => 'Branża'],
            'webinar-material'   => ['en-us' => 'Material',         'es-us' => 'Material',         'fr-ca' => 'Matériau',       'pl-pl' => 'Materiał'],
            'webinar-category'   => ['en-us' => 'Webinar',          'es-us' => 'Webinar',          'fr-ca' => 'Webinaire',      'pl-pl' => 'Webinar'],
        ];
        if (!isset($map[$tax])) return ucfirst($tax);
        return $map[$tax][$locale] ?? $map[$tax]['en-us'];
    }
}

/* =====================================================================
 * Filters — bail early when locale is en-us; admin always bails.
 * ===================================================================== */

add_filter('get_term', function ($term) {
    if (is_admin() || !is_object($term) || empty($term->term_id)) return $term;
    if (wj_term_loc_current_locale() === 'en-us') return $term;

    $taxes = wj_term_loc_taxonomies_lookup();
    if (!isset($taxes[$term->taxonomy ?? ''])) return $term;

    $label = wj_get_term_label((int) $term->term_id);
    if ($label) $term->name = $label;
    return $term;
});

$wj_filter_term_array = function ($terms) {
    if (is_admin() || empty($terms) || !is_array($terms)) return $terms;
    if (wj_term_loc_current_locale() === 'en-us') return $terms;

    $taxes = wj_term_loc_taxonomies_lookup();
    foreach ($terms as &$term) {
        if (!is_object($term) || empty($term->term_id)) continue;
        if (!isset($taxes[$term->taxonomy ?? ''])) continue;
        $label = wj_get_term_label((int) $term->term_id);
        if ($label) $term->name = $label;
    }
    return $terms;
};
add_filter('get_terms',           $wj_filter_term_array);
add_filter('wp_get_object_terms', $wj_filter_term_array);

// Single term archive page title
add_filter('single_term_title', function ($title) {
    if (is_admin()) return $title;
    if (wj_term_loc_current_locale() === 'en-us') return $title;

    $term = get_queried_object();
    if (!is_object($term) || empty($term->term_id)) return $title;
    $taxes = wj_term_loc_taxonomies_lookup();
    if (!isset($taxes[$term->taxonomy ?? ''])) return $title;

    return wj_get_term_label((int) $term->term_id, null, $title);
});

// FacetWP checkbox/radio label
add_filter('facetwp_facet_display_value', function ($value, $params) {
    if (is_admin()) return $value;
    if (wj_term_loc_current_locale() === 'en-us') return $value;

    $facet = $params['facet'] ?? [];
    if (empty($facet['source']) || strpos($facet['source'], 'tax/') !== 0) return $value;
    $tax = substr($facet['source'], 4);
    $taxes = wj_term_loc_taxonomies_lookup();
    if (!isset($taxes[$tax])) return $value;

    $slug = $params['row']['facet_value'] ?? '';
    if (!$slug) return $value;
    $term = get_term_by('slug', $slug, $tax);
    if (!$term || is_wp_error($term)) return $value;

    $label = wj_get_term_label((int) $term->term_id);
    return $label ?: $value;
}, 10, 2);

// Locale-filter taxonomy archive queries
add_action('pre_get_posts', function ($q) {
    if (is_admin() || !$q->is_main_query()) return;
    $matched = false;
    foreach (wj_term_loc_taxonomies() as $tax) {
        if ($q->is_tax($tax)) { $matched = true; break; }
    }
    if (!$matched) return;

    $locale = wj_term_loc_current_locale();
    $mq = $q->get('meta_query') ?: [];
    $mq[] = ['key' => 'region_language_code', 'value' => $locale];
    $q->set('meta_query', $mq);
});

// --- Admin: localized labels column + per-locale post counts ---
add_action('admin_init', function () {
    foreach (wj_term_loc_taxonomies() as $tax) {
        add_filter("manage_edit-{$tax}_columns", 'wj_term_admin_columns');
        add_filter("manage_{$tax}_custom_column", 'wj_term_admin_column_content', 10, 3);
    }
});

function wj_term_admin_columns($columns) {
    $new = [];
    foreach ($columns as $key => $label) {
        $new[$key] = $label;
        if ($key === 'name') {
            $new['wj_translations'] = 'Translations';
            $new['wj_locale_count'] = 'Locale Count';
        }
    }
    // Hide default count — locale count replaces it
    unset($new['posts']);
    return $new;
}

function wj_term_admin_column_content($content, $column_name, $term_id) {
    if ($column_name === 'wj_translations') {
        $locale_labels = [
            'label_es_us' => 'ES',
            'label_fr_ca' => 'FR',
            'label_en_ca' => 'CA',
            'label_en_uk' => 'UK',
            'label_pl_pl' => 'PL',
        ];
        $parts = [];
        foreach ($locale_labels as $meta_key => $code) {
            $val = get_term_meta($term_id, $meta_key, true);
            if ($val) {
                $parts[] = '<span title="' . esc_attr($val) . '">' . esc_html($code) . '</span>';
            }
        }
        return $parts ? implode(' · ', $parts) : '<span style="color:#999">—</span>';
    }

    if ($column_name === 'wj_locale_count') {
        // Pre-compute all locale counts in one query (cached per request per taxonomy screen)
        static $locale_counts = [];
        $term = get_term($term_id);
        if (!$term) return '';
        $screen_key = $term->taxonomy;
        if (!isset($locale_counts[$screen_key])) {
            global $wpdb;
            $counts = $wpdb->get_results($wpdb->prepare(
                "SELECT tt.term_taxonomy_id, pm.meta_value AS locale, COUNT(*) AS cnt
                 FROM {$wpdb->term_relationships} tr
                 JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                 JOIN {$wpdb->postmeta} pm ON tr.object_id = pm.post_id AND pm.meta_key = 'region_language_code'
                 JOIN {$wpdb->posts} p ON tr.object_id = p.ID AND p.post_status = 'publish'
                 WHERE tt.taxonomy = %s
                 GROUP BY tt.term_taxonomy_id, pm.meta_value",
                $term->taxonomy
            ));
            $locale_counts[$screen_key] = [];
            foreach ($counts as $row) {
                $locale_counts[$screen_key][$row->term_taxonomy_id][$row->locale] = (int) $row->cnt;
            }
        }
        $term_counts = $locale_counts[$screen_key][$term_id] ?? [];
        if (empty($term_counts)) return '<span style="color:#999">0</span>';
        $parts = [];
        foreach ($term_counts as $loc => $cnt) {
            $parts[] = strtoupper(str_replace('-', ' ', $loc)) . ': ' . $cnt;
        }
        return implode(' · ', $parts);
    }

    return $content;
}
