<?php
/**
 * Locale helpers — memoized.
 *
 * These functions are called dozens of times per request from menu filters,
 * term-label filters, ticker logic, and templates. The previous theme-side
 * implementations re-parsed $_SERVER['REQUEST_URI'] on every call. Here we
 * compute once and cache statically for the duration of the request.
 *
 * All function_exists() guards remain so the theme's own (older) copies
 * stay defensive: if this plugin is deactivated, the theme still works.
 */

if (!defined('ABSPATH')) exit;

if (!function_exists('lc_get_locale_from_url')) {
    /**
     * Parse the locale prefix from the current request URL.
     * Returns "en-us" for unprefixed URLs.
     *
     * Memoized per request keyed by REQUEST_URI so REST callbacks that
     * temporarily mutate $_SERVER['REQUEST_URI'] still get fresh values.
     */
    function lc_get_locale_from_url(): string {
        static $cache = [];
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        if (isset($cache[$uri])) return $cache[$uri];

        $path = parse_url($uri, PHP_URL_PATH) ?: '';
        $bits = explode('/', trim($path, '/'));
        if (count($bits) >= 2 && $bits[0] !== '' && $bits[1] !== '') {
            $cache[$uri] = strtolower($bits[1] . '-' . $bits[0]); // /us/en/... → en-us
        } else {
            $cache[$uri] = 'en-us';
        }
        return $cache[$uri];
    }
}

if (!function_exists('lc_locale_to_prefix')) {
    /**
     * Convert a locale code (e.g. "en-us") to a URL prefix (e.g. "us/en").
     * Returns "us/en" for malformed input.
     */
    function lc_locale_to_prefix(string $code): string {
        static $cache = [];
        if (isset($cache[$code])) return $cache[$code];
        $p = explode('-', strtolower($code));
        $cache[$code] = (count($p) === 2) ? strtolower($p[1] . '/' . $p[0]) : 'us/en';
        return $cache[$code];
    }
}

if (!function_exists('get_current_lang_from_url')) {
    /**
     * Theme template alias for lc_get_locale_from_url(). Several template-parts
     * still call this name; both resolve to the same memoized helper.
     */
    function get_current_lang_from_url(): string {
        return lc_get_locale_from_url();
    }
}

if (!function_exists('seo_supported_locales')) {
    /**
     * Site locale code → ISO hreflang code map.
     * Used by SEO and language-switcher logic.
     */
    function seo_supported_locales(): array {
        return [
            'en-us' => 'en-US',
            'es-us' => 'es-US',
            'en-ca' => 'en-CA',
            'fr-ca' => 'fr-CA',
            'en-uk' => 'en-GB', // URL uses /uk/en/, hreflang uses en-GB
            'pl-pl' => 'pl-PL',
        ];
    }
}
