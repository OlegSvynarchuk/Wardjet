<?php
/**
 * Per-locale nav-menu locations + selection helpers.
 *
 * Registers {base}-{region}-{lang} locations (primary/header-nav/footer-main/
 * footer-nav) for each configured locale, and provides the helpers the theme
 * uses to pick the right location for the current /cc/ll/ URL. Ported from the
 * blueprint theme (register_dynamic_menus / wj_current_region_lang /
 * wj_pick_menu_location) so the theme stays minimal.
 */

if (!defined('ABSPATH')) exit;

if (!function_exists('register_dynamic_menus')) {
    function register_dynamic_menus() {
        // Optional Settings → Language Settings option; defaults to the 6 locales.
        $raw = (string) get_option('language_codes', "us/en\nus/es\nca/en\nca/fr\nuk/en\npl/pl");
        $lines = preg_split('/\r\n|\r|\n/', $raw);

        $codes = [];
        foreach ($lines as $line) {
            $line = strtolower(trim($line));
            if (!$line) continue;
            if (preg_match('#^[a-z]{2}/[a-z]{2}$#', $line)) $codes[] = $line;
        }
        if (!in_array('us/en', $codes, true)) array_unshift($codes, 'us/en');

        $menus = [];
        foreach ($codes as $code) {
            list($region, $lang) = explode('/', $code);
            $suffix      = $region . '-' . $lang;              // us-en
            $humanSuffix = strtoupper($region . '/' . $lang);  // US/EN
            $menus['primary-'     . $suffix] = 'Primary ('     . $humanSuffix . ')';
            $menus['header-nav-'  . $suffix] = 'Header Nav ('  . $humanSuffix . ')';
            $menus['footer-main-' . $suffix] = 'Footer Main (' . $humanSuffix . ')';
            $menus['footer-nav-'  . $suffix] = 'Footer Nav ('  . $humanSuffix . ')';
        }
        register_nav_menus($menus);
    }
    add_action('after_setup_theme', 'register_dynamic_menus', 11);
}

if (!function_exists('wj_current_region_lang')) {
    // Parse /{region}/{lang}/ from current URL; default to us/en.
    function wj_current_region_lang(): array {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        if (preg_match('#^/([a-z]{2})/([a-z]{2})(?:/|$)#i', (string) $path, $m)) {
            return [ strtolower($m[1]), strtolower($m[2]) ];
        }
        return ['us','en'];
    }
}

if (!function_exists('wj_pick_menu_location')) {
    // base ("primary"|"header-nav"|"footer-main"|"footer-nav") + region/lang →
    // location slug, transparently falling back to the US/EN menu.
    function wj_pick_menu_location(string $base, string $region, string $lang): string {
        $want     = $base . '-' . strtolower($region) . '-' . strtolower($lang);
        $fallback = $base . '-us-en';
        $locs = get_nav_menu_locations();
        if (isset($locs[$want]) && !empty($locs[$want])) return $want;
        return $fallback;
    }
}

/**
 * Pick a locale-specific ACF option field for the current locale.
 *
 * Tries, in order:
 *   {base}_{lang}_{region}   e.g. footer_headquarters_es_us  (blueprint suffix)
 *   {base}_{region}_{lang}   e.g. footer_headquarters_us_es
 *   {lang}_{base}            e.g. es_footer_headquarters      (ACFML prefix — wardjet's data)
 *   {base}                   e.g. footer_headquarters         (default / en-us)
 * Returns the first field name that actually has content, else the base.
 */
if (!function_exists('lc_pick_locale_field')) {
    function lc_pick_locale_field(string $base): ?string {
        list($region, $lang) = function_exists('wj_current_region_lang') ? wj_current_region_lang() : ['us','en'];
        $region = strtolower($region); $lang = strtolower($lang);
        $candidates = [
            $base . '_' . $lang . '_' . $region,
            $base . '_' . $region . '_' . $lang,
            $lang . '_' . $base,
            $base,
        ];
        foreach ($candidates as $field) {
            if (function_exists('have_rows') && have_rows($field, 'option')) return $field;
            $val = function_exists('get_field') ? get_field($field, 'option') : null;
            if (!empty($val)) return $field;
        }
        return $base;
    }
}

/**
 * Per-locale menus WITHOUT changing theme templates or breaking Max Mega Menu.
 *
 * The theme keeps calling wp_nav_menu() on the base locations (primary,
 * header-nav, footer-main, footer-nav) — so Max Mega Menu (enabled on
 * primary + footer-main) keeps wrapping them. Here we remap those base
 * locations, per request, to the menu assigned to the current locale's
 * {base}-{region}-{lang} location (falling back to {base}-us-en). This reuses
 * the per-locale location assignments; no hardcoded menu IDs.
 */
if (!function_exists('wj_remap_menu_locations_for_locale')) {
    function wj_remap_menu_locations_for_locale($locs) {
        if (is_admin() || !is_array($locs)) return $locs;
        if (!function_exists('wj_current_region_lang')) return $locs;

        list($region, $lang) = wj_current_region_lang();
        foreach (['primary', 'header-nav', 'footer-main', 'footer-nav'] as $base) {
            $want     = $base . '-' . $region . '-' . $lang;
            $fallback = $base . '-us-en';
            if (!empty($locs[$want]))          $locs[$base] = $locs[$want];
            elseif (!empty($locs[$fallback]))  $locs[$base] = $locs[$fallback];
        }
        return $locs;
    }
    add_filter('theme_mod_nav_menu_locations', 'wj_remap_menu_locations_for_locale');
}
