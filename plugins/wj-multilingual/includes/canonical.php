<?php
/**
 * Canonical-redirect filtering and template_redirect URL normalization.
 *
 * Replaces 4 stacked theme redirect_canonical filters (one of which was an
 * exact duplicate of another) with a single filter that handles all three
 * conditions, plus a single template_redirect action that consolidates 5
 * separate URL-rewrite actions into one ordered handler.
 */

if (!defined('ABSPATH')) exit;

/* =====================================================================
 * Preserve query string (UTMs, gclid, fbclid, _gl, etc.) across our
 * locale-normalization redirects. The redirect targets below are built
 * with home_url(...) and don't carry the original $_GET — so without
 * this filter, ad-campaign params are dropped on every 301.
 * Front-end only; admin/login paths are skipped.
 * ===================================================================== */
add_filter('wp_redirect', function ($location) {
    if (!is_string($location) || $location === '') return $location;
    if (empty($_SERVER['QUERY_STRING'])) return $location;
    if (is_admin()) return $location;
    $req = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    if (preg_match('#^/(wp-admin|wp-login)#i', $req)) return $location;
    if (strpos($location, '?') !== false) return $location; // target already has its own query string
    return $location . '?' . $_SERVER['QUERY_STRING'];
}, 10, 1);

/* =====================================================================
 * Canonical: cancel redirects in three cases
 *   1) Fallback-language requests ($GLOBALS['is_fallback_language'])
 *   2) Cross-locale paths that only differ by /cc/ll/
 *   3) Locale homepage clean URLs ($GLOBALS['wj_locale_root_frontpage'])
 * ===================================================================== */
add_filter('redirect_canonical', function ($redirect_url) {
    // Case 1: serving a fallback-locale post; never canonicalize across.
    if (!empty($GLOBALS['is_fallback_language'])) return false;

    // Case 3: clean /cc/ll/ root for locale homepages.
    if (!empty($GLOBALS['wj_locale_root_frontpage'])) return false;

    if (!is_string($redirect_url) || $redirect_url === '') return $redirect_url;

    // Case 2: cancel if the only difference between current and target is the
    // /cc/ll/ prefix (would otherwise force visitors back to a wrong locale).
    $current    = (is_ssl() ? 'https://' : 'http://') . ($_SERVER['HTTP_HOST'] ?? '') . ($_SERVER['REQUEST_URI'] ?? '');
    $cur_path   = parse_url($current, PHP_URL_PATH);
    $redir_path = parse_url($redirect_url, PHP_URL_PATH);

    if ($cur_path && $redir_path) {
        $strip = function ($p) {
            $p = ltrim($p, '/');
            return preg_replace('#^[a-z]{2}/[a-z]{2}#i', '', $p, 1);
        };
        if ($strip($cur_path) === $strip($redir_path)) return false;
    }
    return $redirect_url;
}, 10);

/* =====================================================================
 * template_redirect: locale-aware URL normalization. Consolidates 5
 * previously-separate actions into one ordered cascade. Each rule returns
 * after its redirect; if no rule matches, request continues normally.
 * ===================================================================== */
if (!defined('OLD_SITE_REDIRECTS')) define('OLD_SITE_REDIRECTS', false);

add_action('template_redirect', function () {
    if (is_admin()) return;

    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

    // Skip WP internals, sitemaps, feeds.
    if (preg_match('#^/(wp-admin|wp-login\.php|wp-json|feed|feeds|sitemap|xmlrpc|favicon|robots)#i', $path)) return;

    // 1) /us/en/ → "/" (canonical site root)
    if (preg_match('#^/us/en/?$#i', $path)) {
        wp_redirect(home_url('/'), 301);
        exit;
    }

    // Already on the new /cc/ll/... scheme: only the WPML-slug rewrite below
    // needs to act for these. All "wrap to /us/en/" rules below should bail.
    $is_locale_prefixed = (bool) preg_match('#^/[a-z]{2}/[a-z]{2}(/|$)#i', $path);

    if (!$is_locale_prefixed) {
        // 2) Polish old-prefix /pl/anything → /pl/pl/anything
        if (preg_match('#^/pl(/.*)?$#i', $path, $m)) {
            $rest   = isset($m[1]) ? $m[1] : '';
            $target = preg_replace('#//+#', '/', '/pl/pl' . $rest);
            wp_redirect(home_url($target), 301);
            exit;
        }

        // 3) Optional: legacy /fr → /ca/fr, /es → /us/es, /pl → /pl/pl
        // (gated by OLD_SITE_REDIRECTS constant; case 2 above already covers /pl)
        if (OLD_SITE_REDIRECTS && preg_match('#^/([a-z]{2})(/.*)?$#i', $path, $m)) {
            $locale_map = ['fr' => 'ca/fr', 'es' => 'us/es', 'pl' => 'pl/pl'];
            $old = strtolower($m[1]);
            if (isset($locale_map[$old])) {
                $rest   = isset($m[2]) ? $m[2] : '';
                $target = preg_replace('#//+#', '/', '/' . $locale_map[$old] . $rest);
                wp_redirect(home_url($target), 301);
                exit;
            }
        }

        // 4) Non-prefixed CPT/archive URLs → wrap to /us/en/
        if ($path !== '/') {
            $cpt_slugs       = 'accessories|industry|video|testimonials?|products|series|blogs?|webinars?|news_and_events|news-events';
            $localized_slugs = 'blogi|blogues|baza-wiedzy|seminarium-internetowe|seminario-web|webinaire|wiadomosci-i-wydarzenia|noticias-y-eventos|nouvelles-et-evenements';
            $tax_slugs       = 'section|blog-industry|blog-material|blog-product|webinar-industry|webinar-material|webinar-category|testimonial_industries|technical_topics|events|news';
            if (preg_match('#^/(' . $cpt_slugs . '|' . $localized_slugs . '|' . $tax_slugs . ')(/|$)#i', $path)) {
                wp_redirect(home_url('/us/en' . $path), 301);
                exit;
            }
        }
        return; // unprefixed path matched none of the above — let WP handle it
    }

    // 5) WPML legacy CPT base slugs → English equivalents (only on /cc/ll/...)
    if (preg_match('#^/([a-z]{2})/([a-z]{2})/([^/]+)(.*)$#i', $path, $m)) {
        $wpml_slug_map = [
            // French
            'systemes-de-routeurs' => 'routers',
            'logiciel'             => 'software',
            'accessoires'          => 'accessories',
            // Spanish
            'accesorios'           => 'accessories',
            // Polish
            'frezarki'             => 'routers',
            'oprogramowanie'       => 'software',
            'akcesoria'            => 'accessories',
            'wideo'                => 'video',
        ];
        $slug = strtolower($m[3]);
        if (isset($wpml_slug_map[$slug])) {
            $target = '/' . $m[1] . '/' . $m[2] . '/' . $wpml_slug_map[$slug] . $m[4];
            wp_redirect(home_url($target), 301);
            exit;
        }
    }
}, 0);
