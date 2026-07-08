<?php
/**
 * Navigation menu localization.
 *
 * Two filters (down from 3):
 *
 *   prio 20 — Builder: rewrites managed CPT menu URLs for current locale,
 *             appends language-switcher with translated URLs + search box,
 *             handles homepage clean URLs and US-English "/" redirect.
 *
 *   prio 50 — Manual-URL prefixer: inserts /cc/ll/ into manually-entered
 *             CPT menu URLs (skips language-switcher items).
 */

if (!defined('ABSPATH')) exit;

if (!function_exists('_custom_nav_menu_item')) {
    function _custom_nav_menu_item($title, $url, $order, $parent = 0) {
        $item = new stdClass();
        $item->ID = '1000000' + strval($order) + strval($parent);
        $item->db_id = $item->ID;
        $item->title = $title;
        $item->url = $url;
        $item->menu_order = $order;
        $item->menu_item_parent = $parent;
        $item->type = '';
        $item->object = '';
        $item->object_id = '';
        $item->classes = [];
        $item->target = '';
        $item->attr_title = '';
        $item->description = '';
        $item->xfn = '';
        $item->status = '';
        return $item;
    }
}

if (!function_exists('get_translated_post_by_group_id')) {
    function get_translated_post_by_group_id($post_id, $lang_code, $post_type) {
        static $cache = [];
        $key = "{$post_id}:{$lang_code}:{$post_type}";
        if (isset($cache[$key])) return $cache[$key];

        $translation_group_id = get_post_meta($post_id, 'translation_group_id', true);
        if (empty($translation_group_id)) {
            $cache[$key] = false;
            return false;
        }

        $posts = get_posts([
            'post_type'      => $post_type,
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'fields'         => 'ids',
            'meta_query'     => [
                'relation' => 'AND',
                ['key' => 'translation_group_id', 'value' => $translation_group_id],
                ['key' => 'region_language_code', 'value' => $lang_code, 'compare' => '='],
            ],
        ]);
        $cache[$key] = !empty($posts) ? $posts[0] : false;
        return $cache[$key];
    }
}

/* ------------- Shared config ------------- */
function wj_multilingual_languages(): array {
    return [
        ['label' => 'US - English',  'acf_field' => 'translation_us_en', 'code' => 'en-us', 'locale' => 'en_US'],
        ['label' => 'US - Español',  'acf_field' => 'translation_us_es', 'code' => 'es-us', 'locale' => 'es_US'],
        ['label' => 'CA - English',  'acf_field' => 'translation_ca_en', 'code' => 'en-ca', 'fallback' => 'en-us', 'locale' => 'en_CA'],
        ['label' => 'CA - Français', 'acf_field' => 'translation_ca_fr', 'code' => 'fr-ca', 'locale' => 'fr_CA'],
        ['label' => 'UK - English',  'acf_field' => 'translation_uk_en', 'code' => 'en-uk', 'fallback' => 'en-us', 'locale' => 'en_GB'],
        ['label' => 'PL - Polski',   'acf_field' => 'translation_pl_pl', 'code' => 'pl-pl', 'locale' => 'pl_PL'],
    ];
}
function wj_multilingual_lang_label_to_code(): array {
    static $map = null;
    if ($map !== null) return $map;
    $map = [];
    foreach (wj_multilingual_languages() as $l) $map[$l['label']] = $l['code'];
    return $map;
}

/* =====================================================================
 * Filter 1 (prio 20): Builder — rewrites CPT URLs, appends switcher
 * with final translated URLs (no separate post-processing needed).
 * ===================================================================== */
function custom_nav_menu_items($items, $menu) {
    if (!is_object($menu) || strpos($menu->slug, 'header-nav') !== 0) return $items;

    $languages = wj_multilingual_languages();

    $post_types_to_filter = ['products', 'series', 'industry', 'accessories'];
    $aliasable_locales    = ['en-uk', 'en-ca'];

    $current_url = function_exists('add_query_arg')
        ? home_url(add_query_arg(null, null))
        : ((is_ssl() ? 'https://' : 'http://') . ($_SERVER['HTTP_HOST'] ?? '') . ($_SERVER['REQUEST_URI'] ?? '/'));

    $current_lang_code = lc_get_locale_from_url();

    // Static cache for url_to_postid within this request
    static $url_cache = [];

    // Rewrite existing menu items for managed CPTs + normalize testimonials archive
    foreach ($items as $item) {
        // Cached url_to_postid
        $url_key = $item->url;
        if (!isset($url_cache[$url_key])) {
            $url_cache[$url_key] = url_to_postid($url_key);
        }
        $post_id   = $url_cache[$url_key];
        $post_type = $post_id ? get_post_type($post_id) : '';

        if ($post_id && in_array($post_type, $post_types_to_filter, true)) {
            $translated_id = get_translated_post_by_group_id($post_id, $current_lang_code, $post_type);
            if (!$translated_id && in_array($current_lang_code, $aliasable_locales, true)) {
                $translated_id = get_translated_post_by_group_id($post_id, 'en-us', $post_type);
            }
            if ($translated_id) {
                $target_post = get_post($translated_id);
                $target_post->temp_lang_code = $current_lang_code;
                $item->url = get_permalink($target_post);
            }
        }

        $home = rtrim(get_home_url(), '/');
        if (is_string($item->url) && strpos($item->url, $home) === 0) {
            $parts = explode('-', $current_lang_code);
            if (count($parts) === 2) {
                $current_lang_slug = strtolower($parts[1] . '/' . $parts[0]);
                $relative = ltrim(substr($item->url, strlen($home)), '/');
                if ($relative && preg_match('#^(?:[a-z]{2}/[a-z]{2}/)?testimonials#i', $relative)) {
                    $paged = (preg_match('#/page/([0-9]+)/?#i', $relative, $m)) ? (int) $m[1] : 1;
                    $new_rel = $current_lang_slug . '/testimonials/';
                    if ($paged > 1) $new_rel .= 'page/' . $paged . '/';
                    $new_url = $home . '/' . $new_rel;
                    $structure = get_option('permalink_structure');
                    if (is_string($structure) && substr($structure, -1) !== '/' && substr($new_url, -1) === '/') {
                        $new_url = rtrim($new_url, '/');
                    }
                    $item->url = $new_url;
                }
            }
        }
    }

    // Append the language-switcher parent + children
    $current_lang_label = 'US - English';
    foreach ($languages as $lang) {
        if ($lang['code'] === $current_lang_code) { $current_lang_label = $lang['label']; break; }
    }
    $parent    = _custom_nav_menu_item($current_lang_label, '', count($items) + 1);
    $parent_id = $parent->ID;
    $items[]   = $parent;

    // Pre-compute current page context (used by all switcher children)
    $current_post_id   = get_queried_object_id();
    $current_post_type = $current_post_id ? get_post_type($current_post_id) : '';
    $is_home_like = is_front_page()
        || (is_page() && function_exists('get_field') && get_field('is_frontpage', $current_post_id) === 'yes');
    $managed_cpts = ['products', 'series', 'industry', 'accessories',
                     'video', 'blog', 'news_and_events', 'webinar', 'testimonial'];

    foreach ($languages as $lang) {
        $link = '';

        if (is_search()) {
            $items[] = _custom_nav_menu_item($lang['label'], $current_url, count($items) + 1, $parent_id);
            continue;
        }

        // Homepage-like: clean /cc/ll/ root
        if ($is_home_like) {
            $prefix = lc_locale_to_prefix($lang['code']);
            $link = home_url('/' . $prefix . '/');
        }
        // Testimonial archive
        elseif (is_post_type_archive('testimonial')) {
            $paged = (int) get_query_var('paged');
            if ($paged < 1 && preg_match('#/page/([0-9]+)/?#', $_SERVER['REQUEST_URI'] ?? '', $m)) $paged = (int) $m[1];
            $lang_slug = strtolower(explode('-', $lang['code'])[1] . '/' . explode('-', $lang['code'])[0]);
            $link = home_url('/' . $lang_slug . '/testimonials/' . ($paged > 1 ? 'page/' . $paged . '/' : ''));
        }
        // Singular content CPTs (testimonial, webinar, news_and_events, blog)
        elseif (is_singular(['testimonial', 'webinar', 'news_and_events', 'blog']) && $current_post_id) {
            $translated_id = get_translated_post_by_group_id($current_post_id, $lang['code'], $current_post_type);
            if ($translated_id) {
                $target_post = get_post($translated_id);
                if ($target_post) {
                    $target_post->temp_lang_code = $lang['code'];
                    $link = get_permalink($target_post);
                }
            } elseif (in_array($lang['code'], $aliasable_locales, true)) {
                // Aliasable locale (en-uk, en-ca): show en-us content under target locale prefix
                $target_post = get_post($current_post_id);
                if ($target_post) {
                    $target_post->temp_lang_code = $lang['code'];
                    $link = get_permalink($target_post);
                }
            } else {
                // Non-aliasable target without its own translation: go to the en-us version.
                // Check current post first (may have no translation_group_id), then look up sibling.
                $saved_locale = strtolower((string) get_post_meta($current_post_id, 'region_language_code', true));
                $en_us_id = 0;
                if ($saved_locale === 'en-us') {
                    $en_us_id = $current_post_id;
                } else {
                    $sibling = get_translated_post_by_group_id($current_post_id, 'en-us', $current_post_type);
                    if ($sibling) $en_us_id = $sibling;
                }
                if ($en_us_id) {
                    $target_post = get_post($en_us_id);
                    if ($target_post) {
                        $target_post->temp_lang_code = 'en-us';
                        $link = get_permalink($target_post);
                    }
                } else {
                    $link = $current_url;
                }
            }
        }
        // Pages: translated, with fallback locale if configured
        elseif ($current_post_id && $current_post_type === 'page') {
            $translated_id = get_translated_post_by_group_id($current_post_id, $lang['code'], 'page')
                ?: (isset($lang['fallback']) ? get_translated_post_by_group_id($current_post_id, $lang['fallback'], 'page') : false);
            if ($translated_id) {
                $target_post = get_post($translated_id);
                $link = get_permalink($target_post);
            } else {
                $link = $current_url;
            }
        }
        // Managed CPTs with alias behavior (en-uk, en-ca → en-us)
        elseif ($current_post_id && in_array($current_post_type, $post_types_to_filter, true)) {
            $translated_id = get_translated_post_by_group_id($current_post_id, $lang['code'], $current_post_type);
            if (!$translated_id && in_array($lang['code'], $aliasable_locales, true)) {
                $target_post = get_post($current_post_id);
                if ($target_post) {
                    $target_post->temp_lang_code = $lang['code'];
                    $link = get_permalink($target_post);
                }
            } elseif ($translated_id) {
                $target_post = get_post($translated_id);
                if ($target_post) {
                    $target_post->temp_lang_code = $lang['code'];
                    $link = get_permalink($target_post);
                }
            } else {
                $link = $current_url;
            }
        }

        if (!$link) $link = $current_url;

        // US - English special case: "/" instead of "/us/en/"
        if ($lang['code'] === 'en-us') {
            $path = parse_url($link, PHP_URL_PATH) ?: '';
            if ($path === '/us/en' || $path === '/us/en/' || $path === '/' || $path === '') {
                $link = add_query_arg('src', 'switch', home_url('/'));
            }
        }

        $items[] = _custom_nav_menu_item($lang['label'], $link, count($items) + 1, $parent_id);
    }

    // Search box
    $placeholder = function_exists('get_field') ? get_field('search_placeholder', 'option') : '';
    $search_html = '
<form class="d-none" action="/" method="get" role="search" id="search-row">
    <label for="is-search-input-0">
        <input type="search" name="s" value="" placeholder="' . esc_attr($placeholder) . '" autocomplete="off" class="form-control">
    </label>
</form>
<i class="fa fa-search" id="search-icon"></i>';
    $search = _custom_nav_menu_item($search_html, '', count($items) + 1);
    $search->classes = ['search-control'];
    $items[] = $search;

    return $items;
}
add_filter('wp_get_nav_menu_items', 'custom_nav_menu_items', 20, 2);

/* =====================================================================
 * Filter 2 (prio 50): Manual-URL prefixer for non-switcher CPT menu items.
 * Inserts /cc/ll/ into menu URLs that point to a CPT base but were entered
 * manually (no permalink filter to do it for them).
 * ===================================================================== */
add_filter('wp_get_nav_menu_items', function ($items) {
    if (empty($items)) return $items;

    $home   = rtrim(get_home_url(), '/');
    $prefix = lc_locale_to_prefix(lc_get_locale_from_url());

    $label2code = wj_multilingual_lang_label_to_code();

    // Identify language-switcher parents/children so we can skip them.
    $switch_parent_ids = [];
    foreach ($items as $it) {
        if (isset($label2code[$it->title]) && (empty($it->url) || $it->url === '#')) {
            $switch_parent_ids[(string) $it->ID] = true;
        }
    }
    $is_switch_item = [];
    if ($switch_parent_ids) {
        foreach ($items as $it) {
            if ($it->menu_item_parent && isset($switch_parent_ids[(string) $it->menu_item_parent])) {
                $is_switch_item[(string) $it->ID] = true;
            }
        }
    }

    $cpt_bases = '(products|series|industry|accessories|testimonial|testimonials|webinar|webinars|news_and_events)';

    foreach ($items as $it) {
        if (isset($is_switch_item[(string) $it->ID])) continue;

        $url = $it->url;
        if (!$url || $url === '#') continue;

        $is_same_site_abs = (strpos($url, $home) === 0);
        $is_root_rel      = (!$is_same_site_abs && substr($url, 0, 1) === '/');
        if (!$is_same_site_abs && !$is_root_rel) continue;

        $rel = $is_same_site_abs ? ltrim(substr($url, strlen($home)), '/') : ltrim($url, '/');
        if ($rel === '') continue;
        if (preg_match('#^(wp-admin|wp-login\.php|wp-json)(/|$)#i', $rel)) continue;

        $rel_nop = preg_replace('#^[a-z]{2}/[a-z]{2}/#i', '', $rel, 1);
        if (!preg_match('#^' . $cpt_bases . '(/|$)#i', $rel_nop)) continue;

        if (preg_match('#^[a-z]{2}/[a-z]{2}(/|$)#i', $rel)) {
            $rel = preg_replace('#^[a-z]{2}/[a-z]{2}#i', $prefix, $rel, 1);
        } else {
            $rel = $prefix . '/' . ltrim($rel, '/');
        }

        $new = ($is_same_site_abs ? $home : '') . '/' . ltrim($rel, '/');
        $structure = get_option('permalink_structure');
        if (is_string($structure) && substr($structure, -1) === '/' && substr($new, -1) !== '/') $new .= '/';

        $it->url = $new;
    }
    return $items;
}, 50);
