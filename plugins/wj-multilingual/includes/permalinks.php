<?php
/**
 * Permalink filtering — adds /cc/ll/ prefix to CPT URLs.
 *
 * Replaces 3 stacked theme post_type_link filters (priorities 10, 50, 1000)
 * with one canonical filter. Pages are intentionally excluded.
 *
 * Locale priority for resolving the prefix:
 *   1. $post->temp_lang_code (set by the language-switcher menu builder)
 *   2. The post's saved region_language_code meta
 *   3. Fallback: en-us
 *
 * Also moves the small helpers (wj_get_saved_lang_for_post, etc.) used by
 * other parts of the codebase, plus the localized testimonials archive link.
 */

if (!defined('ABSPATH')) exit;

if (!function_exists('wj_allowed_locales')) {
    function wj_allowed_locales(): array {
        return ['en-us', 'es-us', 'en-ca', 'fr-ca', 'en-uk', 'pl-pl'];
    }
}

if (!function_exists('wj_sanitize_locale_code')) {
    function wj_sanitize_locale_code(?string $code): string {
        $code = strtolower(trim((string) $code));
        return in_array($code, wj_allowed_locales(), true) ? $code : 'en-us';
    }
}

if (!function_exists('wj_get_saved_lang_for_post')) {
    function wj_get_saved_lang_for_post(int $post_id): string {
        if (!empty($GLOBALS['__just_saved_region_language_code'][$post_id])) {
            return $GLOBALS['__just_saved_region_language_code'][$post_id];
        }
        $meta = get_post_meta($post_id, 'region_language_code', true);
        return $meta ? wj_sanitize_locale_code($meta) : 'en-us';
    }
}

if (!function_exists('wj_trailing')) {
    function wj_trailing(string $url): string {
        $structure = get_option('permalink_structure');
        if (is_string($structure) && substr($structure, -1) === '/' && substr($url, -1) !== '/') {
            $url .= '/';
        }
        return $url;
    }
}

/**
 * Compatibility shim: the theme's older wj_locale_to_prefix() had a
 * backslash bug (`{$p[1]}\{$p[0]}`). Anything that still calls it now gets
 * the correct forward-slash version from lc_locale_to_prefix().
 */
if (!function_exists('wj_locale_to_prefix')) {
    function wj_locale_to_prefix(string $code): string {
        return lc_locale_to_prefix($code);
    }
}

/**
 * Single, authoritative post_type_link filter. Late priority so it wins
 * over any leftover legacy filters during the migration.
 */
add_filter('post_type_link', function ($link, $post) {
    if (!$post instanceof WP_Post) return $link;

    // Pages intentionally excluded — they get /cc/ll/ via their own slugs.
    $scoped = ['post', 'routers', 'industry', 'accessories', 'software',
               'materials', 'testimonial', 'news_and_events', 'webinar',
               'blog', 'video'];
    if (!in_array($post->post_type, $scoped, true)) return $link;

    $home = rtrim(get_home_url(), '/');
    if (strpos($link, $home) !== 0) return $link;

    // Auto-alias content CPTs: when viewing from en-uk or en-ca, en-us articles
    // get URLs under the current locale prefix (e.g. /uk/en/blog/slug/).
    // Routing in routing.php already serves en-us content for these URLs.
    // Skipped when temp_lang_code is already set (the switcher/menu builder owns that).
    if (empty($post->temp_lang_code) && function_exists('lc_get_locale_from_url')) {
        $alias_cpts = ['blog', 'news_and_events', 'testimonial', 'webinar'];
        if (in_array($post->post_type, $alias_cpts, true)) {
            $cur_locale = lc_get_locale_from_url();
            if (in_array($cur_locale, ['en-uk', 'en-ca'], true)
                && wj_get_saved_lang_for_post($post->ID) === 'en-us') {
                $post->temp_lang_code = $cur_locale;
            }
        }
    }

    // Locale priority: temp (switcher / auto-alias) → saved meta → en-us
    $code = !empty($post->temp_lang_code)
        ? strtolower($post->temp_lang_code)
        : wj_get_saved_lang_for_post($post->ID);

    $prefix = lc_locale_to_prefix($code); // e.g. "us/es", "ca/fr"

    $rel = ltrim(substr($link, strlen($home)), '/');

    // Base normalizations: keep singular CPT bases consistent in the URL.
    if ($post->post_type === 'testimonial') {
        $rel = preg_replace('#^([a-z]{2}/[a-z]{2}/)?testimonial(/|$)#i', '$1testimonials$2', $rel, 1);
    }
    if ($post->post_type === 'webinar') {
        $rel = preg_replace('#^([a-z]{2}/[a-z]{2}/)?webinars?(/|$)#i', '$1webinar$2', $rel, 1);
    }
    if ($post->post_type === 'blog') {
        $rel = preg_replace('#^([a-z]{2}/[a-z]{2}/)?blogs?(/|$)#i', '$1blog$2', $rel, 1);
    }

    // Insert or replace the /cc/ll prefix
    if (preg_match('#^[a-z]{2}/[a-z]{2}(/|$)#i', $rel)) {
        $rel = preg_replace('#^[a-z]{2}/[a-z]{2}#i', $prefix, $rel, 1);
    } else {
        $rel = $prefix . '/' . ltrim($rel, '/');
    }

    return wj_trailing($home . '/' . ltrim($rel, '/'));
}, 1000, 2);

/**
 * Localized archive URL for testimonials: /cc/ll/testimonials/.
 */
add_filter('post_type_archive_link', function ($link, $post_type) {
    if ($post_type !== 'testimonial') return $link;
    $code = lc_get_locale_from_url();
    $p = explode('-', $code);
    if (count($p) === 2) {
        return home_url('/' . $p[1] . '/' . $p[0] . '/testimonials/');
    }
    return $link;
}, 10, 2);
