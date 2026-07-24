<?php
/**
 * SEO + hreflang for the AXYZ multilingual system.
 *
 *   - Locale-aware canonical URL via Rank Math filter; printed in <wp_head>.
 *   - Self canonicals on /cc/ll/ paths; the bare site root "/" stays as-is.
 *   - hreflang cluster: complete locale roots on the homepage and archives;
 *     real-translation only on singular pages, with x-default → en-us.
 *   - Alias-aware permalink helper for "build me a URL for this post in
 *     locale X" — used by the menu filters and hreflang output.
 *
 * Assumptions:
 *   - URL prefix: /{country}/{language}/  (e.g. /uk/en/, /us/es/)
 *   - ACF post meta: region_language_code, translation_group_id
 *   - The plugin's permalink filter honors $post->temp_lang_code for forced output
 */

if (!defined('ABSPATH')) exit;

/* =====================================================================
 * Helpers
 * ===================================================================== */

if (!function_exists('seo_permalink_for_locale')) {
    /**
     * Build a permalink for a post in a target locale.
     * - Locale homepage pages (ACF is_frontpage=yes): clean "/" or "/cc/ll/" root.
     * - Other posts: set temp_lang_code so the post_type_link filter prints the
     *   right /cc/ll/ prefix.
     */
    function seo_permalink_for_locale(int $post_id, string $target_locale): string {
        $p = get_post($post_id);
        if (!$p) return '';

        $is_homepage = false;
        if ($p->post_type === 'page' && function_exists('get_field')) {
            $flag = get_field('is_frontpage', $post_id);
            $is_homepage = in_array(strtolower((string) $flag), ['yes', '1', 'true'], true);
        }

        if ($is_homepage) {
            $code = strtolower($target_locale);
            if ($code === 'en-us') return home_url('/');
            return home_url('/' . lc_locale_to_prefix($code) . '/');
        }

        $p->temp_lang_code = strtolower($target_locale);
        return get_permalink($p);
    }
}

if (!function_exists('seo_collect_group_posts')) {
    /**
     * All posts in the same translation group, keyed by region_language_code.
     * Memoized per request: hreflang code typically iterates locales 6× per
     * page, so we only hit the DB once per post.
     */
    function seo_collect_group_posts(int $post_id): array {
        static $cache = [];
        if (isset($cache[$post_id])) return $cache[$post_id];

        $group = get_post_meta($post_id, 'translation_group_id', true);
        if (!$group) return $cache[$post_id] = [];

        $posts = get_posts([
            'post_type'      => get_post_type($post_id),
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'meta_query'     => [
                ['key' => 'translation_group_id', 'value' => $group],
            ],
        ]);
        $map = [];
        foreach ($posts as $pid) {
            $code = strtolower((string) get_post_meta($pid, 'region_language_code', true));
            if ($code) $map[$code] = (int) $pid;
        }
        return $cache[$post_id] = $map;
    }
}

if (!function_exists('seo_find_translation')) {
    function seo_find_translation(int $post_id, string $target_locale): int {
        $map = seo_collect_group_posts($post_id);
        $key = strtolower($target_locale);
        return isset($map[$key]) ? (int) $map[$key] : 0;
    }
}

if (!function_exists('seo_pick_alias_source_id')) {
    /**
     * Best source post for aliasing into a target locale when no real translation exists.
     * Priority: en-uk/en-ca → en-us; same-language variant; en-us; any in group; self.
     */
    function seo_pick_alias_source_id(int $post_id, string $target_locale): int {
        $map   = seo_collect_group_posts($post_id);
        $t     = strtolower($target_locale);
        $tLang = substr($t, 0, 2);

        if (in_array($t, ['en-uk', 'en-ca'], true) && isset($map['en-us'])) {
            return (int) $map['en-us'];
        }
        foreach ($map as $code => $pid) {
            if (substr($code, 0, 3) === $tLang . '-') return (int) $pid;
        }
        if (isset($map['en-us'])) return (int) $map['en-us'];
        if (!empty($map)) return (int) reset($map);
        return $post_id;
    }
}

if (!function_exists('seo_alias_url_for_locale')) {
    function seo_alias_url_for_locale(int $post_id, string $target_locale): string {
        $is_homepage = false;
        if (function_exists('get_field') && get_post_type($post_id) === 'page') {
            $flag = get_field('is_frontpage', $post_id);
            $is_homepage = in_array(strtolower((string) $flag), ['yes', '1', 'true'], true);
        }

        if ($is_homepage) {
            $code = strtolower($target_locale);
            if ($code === 'en-us') return home_url('/');
            if (function_exists('lc_locale_frontpage_url')) {
                return lc_locale_frontpage_url($code);
            }
            return home_url('/' . lc_locale_to_prefix($code) . '/');
        }

        return seo_permalink_for_locale(seo_pick_alias_source_id($post_id, $target_locale), $target_locale);
    }
}

/* =====================================================================
 * Canonical: locale-aware Rank Math filter (Rank Math prints the tag)
 * ===================================================================== */

add_filter('rank_math/frontend/canonical', function ($canonical) {
    $scheme = is_ssl() ? 'https://' : 'http://';
    $host   = $_SERVER['HTTP_HOST'] ?? '';
    $path   = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

    if (is_front_page() || is_home()) {
        return $scheme . $host . $path;
    }

    if (preg_match('#^/[a-z]{2}/[a-z]{2}(/|$)#i', $path)) {
        if (is_singular()) {
            $post_id = get_queried_object_id();
            if ($post_id) {
                return seo_permalink_for_locale($post_id, lc_get_locale_from_url());
            }
        }
        return $scheme . $host . $path;
    }

    return $canonical ?: ($scheme . $host . $path);
}, 50);

// Standalone canonical <link> printer removed to fix a duplicate <link
// rel="canonical"> on every page: it re-emitted the same value Rank Math already
// prints (made locale-aware by the filter above). Rank Math is active site-wide
// and always outputs one canonical, so this dedicated printer was redundant.

/* =====================================================================
 * hreflang cluster: alternates per supported locale, plus x-default.
 * ===================================================================== */

add_action('wp_head', function () {
    // Bare site root: list all locale roots + x-default → "/".
    if (is_front_page() || is_home()) {
        foreach (seo_supported_locales() as $code => $hreflang) {
            echo '<link rel="alternate" hreflang="' . esc_attr($hreflang)
               . '" href="' . esc_url(home_url('/' . lc_locale_to_prefix($code) . '/'))
               . '">' . "\n";
        }
        echo '<link rel="alternate" hreflang="x-default" href="' . esc_url(home_url('/')) . '">' . "\n";
        return;
    }

    if (!is_singular() && !is_page() && !is_post_type_archive()) return;

    $supported = seo_supported_locales();
    $post_id   = is_singular() ? get_queried_object_id() : 0;

    // Archives without a single-post context: emit locale home roots.
    if (!$post_id) {
        foreach ($supported as $code => $hreflang) {
            $href = home_url('/' . lc_locale_to_prefix($code) . '/');
            echo '<link rel="alternate" hreflang="' . esc_attr($hreflang) . '" href="' . esc_url($href) . '">' . "\n";
        }
        echo '<link rel="alternate" hreflang="x-default" href="' . esc_url(home_url('/')) . '">' . "\n";
        return;
    }

    // Singular: emit only locales with a real translation, plus self-reference.
    $own_locale = strtolower((string) get_post_meta($post_id, 'region_language_code', true));
    $self_href  = '';

    foreach ($supported as $code => $hreflang) {
        $translated = seo_find_translation($post_id, $code);
        if ($translated) {
            $href = seo_permalink_for_locale($translated, $code);
        } elseif (strtolower($code) === $own_locale) {
            $href = seo_permalink_for_locale($post_id, $code);
        } else {
            continue;
        }

        if ($href) {
            echo '<link rel="alternate" hreflang="' . esc_attr($hreflang) . '" href="' . esc_url($href) . '">' . "\n";
            if (strtolower($code) === $own_locale) $self_href = $href;
        }
    }

    // x-default → prefer en-us translation, else self.
    $en_us_id   = seo_find_translation($post_id, 'en-us');
    $en_us_href = $en_us_id
        ? seo_permalink_for_locale($en_us_id, 'en-us')
        : ($self_href ?: seo_permalink_for_locale($post_id, $own_locale ?: 'en-us'));

    echo '<link rel="alternate" hreflang="x-default" href="' . esc_url($en_us_href) . '">' . "\n";
}, 100);
