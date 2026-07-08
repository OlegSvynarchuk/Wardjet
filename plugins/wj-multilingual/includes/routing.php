<?php
/**
 * Unified parse_request router for the AXYZ multilingual system.
 *
 * Replaces 7 stacked theme handlers (filter_custom_post_type_by_language_parse_request,
 * the slug-only router, two disablers, wj_locale_router, wj_content_locale_router,
 * the locale-homepage router) with one coherent flow:
 *
 *   /cc/ll/                  → load that locale's homepage (page with is_frontpage=yes)
 *   /cc/ll/{base}/{slug}/    → resolve CPT single by translation_group_id, falling back
 *                              to en-us for aliasable locales (en-uk, en-ca), then to
 *                              the originally-requested slug.
 *
 * Sets $GLOBALS['forced_lang_code'] for the permalink filter to honor.
 * Sets $GLOBALS['is_fallback_language'] when the resolved post's locale doesn't match
 * the requested one (used by canonical/redirect logic).
 */

if (!defined('ABSPATH')) exit;

if (!function_exists('wj_get_frontpage_id_for_locale')) {
    function wj_get_frontpage_id_for_locale(string $code): int {
        static $cache = [];
        $code = strtolower($code);
        if (isset($cache[$code])) return $cache[$code];

        $ids = get_posts([
            'post_type'      => 'page',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'fields'         => 'ids',
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
            'meta_query'     => [
                'relation' => 'AND',
                ['key' => 'region_language_code', 'value' => $code],
                ['key' => 'is_frontpage', 'value' => ['yes','1','true'], 'compare' => 'IN'],
            ],
        ]);
        $cache[$code] = !empty($ids) ? (int) $ids[0] : 0;
        return $cache[$code];
    }
}

/**
 * URL base segment → CPT name. Some bases are plural/legacy aliases.
 */
function wj_multilingual_base_to_pt(): array {
    static $map = null;
    if ($map !== null) return $map;
    $map = [
        'products'        => 'products',
        'series'          => 'series',
        'industry'        => 'industry',
        'accessories'     => 'accessories',
        'video'           => 'video',
        'blog'            => 'blog',
        'news_and_events' => 'news_and_events',
        'webinar'         => 'webinar',
        'webinars'        => 'webinar',     // legacy
        'testimonial'     => 'testimonial', // legacy
        'testimonials'    => 'testimonial',
    ];
    return $map;
}

/**
 * Aliasable locales fall back to en-us when their own translation is missing.
 */
function wj_multilingual_aliasable_locales(): array {
    return ['en-uk', 'en-ca'];
}

/**
 * Single source of truth for the parse_request locale routing.
 * Runs at priority 0 so it wins over any leftover legacy handlers.
 */
function wj_multilingual_route($wp) {
    if (is_admin()) return;

    $path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '/';

    // Case 1: /cc/ll/ exactly → load that locale's homepage page (clean URL preserved)
    if (preg_match('#^/([a-z]{2})/([a-z]{2})/?$#i', $path, $m)) {
        $code = strtolower($m[2] . '-' . $m[1]);
        $pid  = wj_get_frontpage_id_for_locale($code);
        if ($pid) {
            $wp->query_vars                 = [];
            $wp->query_vars['page_id']      = $pid;
            $wp->query_vars['post_type']    = 'page';
            $wp->query_vars['post_status']  = 'publish';
            $GLOBALS['forced_lang_code']         = $code;
            $GLOBALS['wj_locale_root_frontpage'] = true;
        }
        return;
    }

    // Case 2: /cc/ll/{base}/{slug}/ → CPT single via translation_group_id
    if (empty($wp->query_vars['post_type'])) return;

    $pt = is_array($wp->query_vars['post_type'])
        ? reset($wp->query_vars['post_type'])
        : $wp->query_vars['post_type'];

    $base_to_pt = wj_multilingual_base_to_pt();
    $managed_pts = array_values(array_unique(array_values($base_to_pt)));
    if (!in_array($pt, $managed_pts, true)) return;

    if (!preg_match('#^/([a-z]{2})/([a-z]{2})/([a-z0-9_-]+)/([a-z0-9_-]+)/?$#i', $path, $m)) return;

    $req_locale = strtolower($m[2] . '-' . $m[1]);
    $base       = strtolower($m[3]);
    $slug       = sanitize_title_for_query($m[4]);

    if (!isset($base_to_pt[$base]) || $base_to_pt[$base] !== $pt) return;

    // Find any post with this slug for this CPT (any locale)
    $any = get_posts([
        'post_type'      => $pt,
        'name'           => $slug,
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'fields'         => 'ids',
    ]);
    if (empty($any)) return; // let WP 404
    $any_id = (int) $any[0];

    $target_id = 0;
    $group     = get_post_meta($any_id, 'translation_group_id', true);

    if ($group) {
        $t = get_posts([
            'post_type'      => $pt,
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'fields'         => 'ids',
            'meta_query'     => [
                ['key' => 'translation_group_id', 'value' => $group],
                ['key' => 'region_language_code', 'value' => $req_locale],
            ],
        ]);
        if (!empty($t)) $target_id = (int) $t[0];
    }

    // Aliasable locales fall back to en-us
    if (!$target_id && in_array($req_locale, wj_multilingual_aliasable_locales(), true) && $group) {
        $t = get_posts([
            'post_type'      => $pt,
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'fields'         => 'ids',
            'meta_query'     => [
                ['key' => 'translation_group_id', 'value' => $group],
                ['key' => 'region_language_code', 'value' => 'en-us'],
            ],
        ]);
        if (!empty($t)) $target_id = (int) $t[0];
    }

    // Last resort: serve the originally-requested slug as-is
    if (!$target_id) $target_id = $any_id;

    $wp->query_vars['p']           = $target_id;
    $wp->query_vars['name']        = '';
    $wp->query_vars['post_type']   = $pt;
    $wp->query_vars['post_status'] = 'publish';

    $GLOBALS['forced_lang_code'] = $req_locale;
    $actual = strtolower((string) get_post_meta($target_id, 'region_language_code', true));
    $GLOBALS['is_fallback_language'] = ($actual && $actual !== $req_locale);
}
add_action('parse_request', 'wj_multilingual_route', 0);

/**
 * Defensive cleanup: remove any lingering theme-side parse_request handler that
 * might still be loaded during the transition. Idempotent — runs once at priority 0,
 * after wj_multilingual_route has already finished its work for this request.
 */
add_action('parse_request', function () {
    if (function_exists('filter_custom_post_type_by_language_parse_request')) {
        remove_action('parse_request', 'filter_custom_post_type_by_language_parse_request');
    }
}, 0);

/**
 * Rewrite rules so WordPress parses /cc/ll/{base}/{slug}/ into the right CPT
 * (sets post_type + name), which wj_multilingual_route() then resolves by
 * translation_group_id. The /cc/ll/ prefix itself is read from REQUEST_URI in
 * the router, so it isn't captured here. Adapted to wardjet's CPTs
 * (products, series; no routers/software/materials).
 */
function wj_multilingual_rewrite_rules() {
    add_rewrite_tag('%lang_slug%', '([^/]+)/([^/]+)');

    // CPT singles under /{cc}/{ll}/{base}/{slug}
    add_rewrite_rule('^([^/]+)/([^/]+)/products/([^/]+)/?$',    'index.php?post_type=products&name=$matches[3]', 'top');
    add_rewrite_rule('^([^/]+)/([^/]+)/series/([^/]+)/?$',      'index.php?post_type=series&name=$matches[3]', 'top');
    add_rewrite_rule('^([^/]+)/([^/]+)/industry/([^/]+)/?$',    'index.php?post_type=industry&name=$matches[3]', 'top');
    add_rewrite_rule('^([^/]+)/([^/]+)/accessories/([^/]+)/?$', 'index.php?post_type=accessories&name=$matches[3]', 'top');

    // TESTIMONIALS (singular + legacy + archive + pagination)
    add_rewrite_rule('^([^/]+)/([^/]+)/testimonials/([^/]+)/?$',          'index.php?post_type=testimonial&name=$matches[3]', 'top');
    add_rewrite_rule('^([^/]+)/([^/]+)/testimonial/([^/]+)/?$',           'index.php?post_type=testimonial&name=$matches[3]', 'top'); // legacy
    add_rewrite_rule('^([^/]+)/([^/]+)/testimonials/page/([0-9]{1,})/?$', 'index.php?post_type=testimonial&paged=$matches[3]', 'top');
    add_rewrite_rule('^([^/]+)/([^/]+)/testimonials/?$',                   'index.php?post_type=testimonial', 'top');

    // WEBINAR (singular base) + legacy plural
    add_rewrite_rule('^([^/]+)/([^/]+)/webinar/([^/]+)/?$',   'index.php?post_type=webinar&name=$matches[3]', 'top');
    add_rewrite_rule('^([^/]+)/([^/]+)/webinars/([^/]+)/?$',  'index.php?post_type=webinar&name=$matches[3]', 'top'); // legacy

    // NEWS & EVENTS
    add_rewrite_rule('^([^/]+)/([^/]+)/news_and_events/([^/]+)/?$', 'index.php?post_type=news_and_events&name=$matches[3]', 'top');

    // VIDEO
    add_rewrite_rule('^([^/]+)/([^/]+)/video/([^/]+)/?$', 'index.php?post_type=video&name=$matches[3]', 'top');

    // BLOG
    add_rewrite_rule('^([^/]+)/([^/]+)/blog/([^/]+)/?$', 'index.php?post_type=blog&name=$matches[3]', 'top');
}
add_action('init', 'wj_multilingual_rewrite_rules');

// Flush rules once on plugin activation so the locale CPT URLs work immediately.
register_activation_hook(WJ_MULTILINGUAL_DIR . 'wj-multilingual.php', function () {
    wj_multilingual_rewrite_rules();
    flush_rewrite_rules();
});
