<?php
/**
 * News Ticker Functions
 *
 * Backend functions and settings for the news ticker.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Ticker Settings Page
 */
add_action('admin_menu', 'wj_ticker_add_settings_page');
function wj_ticker_add_settings_page() {
    add_options_page(
        __('News Ticker Settings', 'wardjet'),
        __('News Ticker', 'wardjet'),
        'manage_options',
        'wj-ticker-settings',
        'wj_ticker_settings_page_html'
    );
}

/**
 * Register Settings and Fields
 */
add_action('admin_init', 'wj_ticker_register_settings');
function wj_ticker_register_settings() {
    register_setting('wj_ticker_settings_group', 'wj_ticker_options', 'wj_ticker_sanitize_options');

    // Enable/Disable Section
    add_settings_section(
        'wj_ticker_main_section',
        __('Ticker Configuration', 'wardjet'),
        null,
        'wj-ticker-settings'
    );

    // Enable Ticker
    add_settings_field(
        'wj_ticker_enabled',
        __('Enable Ticker', 'wardjet'),
        'wj_ticker_enabled_callback',
        'wj-ticker-settings',
        'wj_ticker_main_section'
    );

    // Item Count
    add_settings_field(
        'wj_ticker_count',
        __('Number of Items', 'wardjet'),
        'wj_ticker_count_callback',
        'wj-ticker-settings',
        'wj_ticker_main_section'
    );

    // Events Categories
    add_settings_field(
        'wj_ticker_events_categories',
        __('Events Categories', 'wardjet'),
        'wj_ticker_events_categories_callback',
        'wj-ticker-settings',
        'wj_ticker_main_section'
    );

    // News Categories
    add_settings_field(
        'wj_ticker_news_categories',
        __('News Categories', 'wardjet'),
        'wj_ticker_news_categories_callback',
        'wj-ticker-settings',
        'wj_ticker_main_section'
    );
}

/**
 * Sanitize Options
 */
function wj_ticker_sanitize_options($input) {
    $sanitized = [];

    // Enable/Disable
    $sanitized['enabled'] = isset($input['enabled']) ? 1 : 0;

    // Item Count (integer, default 10)
    $sanitized['count'] = isset($input['count']) ? absint($input['count']) : 10;
    if ($sanitized['count'] < 1) $sanitized['count'] = 10;
    if ($sanitized['count'] > 50) $sanitized['count'] = 50;

    // Events Categories (array of term IDs)
    $sanitized['events_categories'] = isset($input['events_categories']) && is_array($input['events_categories'])
        ? array_map('absint', $input['events_categories'])
        : [];

    // News Categories (array of term IDs)
    $sanitized['news_categories'] = isset($input['news_categories']) && is_array($input['news_categories'])
        ? array_map('absint', $input['news_categories'])
        : [];

    return $sanitized;
}

/**
 * Field Callbacks
 */
function wj_ticker_enabled_callback() {
    $options = get_option('wj_ticker_options', []);
    $checked = isset($options['enabled']) && $options['enabled'] ? 'checked' : '';
    echo '<input type="checkbox" name="wj_ticker_options[enabled]" value="1" ' . $checked . '>';
    echo '<p class="description">' . __('Display the news ticker on the homepage.', 'wardjet') . '</p>';
}

function wj_ticker_count_callback() {
    $options = get_option('wj_ticker_options', []);
    $value = isset($options['count']) ? $options['count'] : 10;
    echo '<input type="number" name="wj_ticker_options[count]" value="' . esc_attr($value) . '" min="1" max="50" step="1">';
    echo '<p class="description">' . __('Number of items to display (1-50).', 'wardjet') . '</p>';
}

function wj_ticker_events_categories_callback() {
    $options = get_option('wj_ticker_options', []);
    $selected = isset($options['events_categories']) ? $options['events_categories'] : [];

    $terms = get_terms([
        'taxonomy' => 'events',
        'hide_empty' => true,
    ]);

    if (empty($terms) || is_wp_error($terms)) {
        echo '<p class="description">' . __('No event categories found.', 'wardjet') . '</p>';
        return;
    }

    foreach ($terms as $term) {
        $checked = in_array($term->term_id, $selected) ? 'checked' : '';
        echo '<label style="display: block; margin: 5px 0;">';
        echo '<input type="checkbox" name="wj_ticker_options[events_categories][]" value="' . esc_attr($term->term_id) . '" ' . $checked . '>';
        echo ' ' . esc_html($term->name) . ' <span class="count">(' . esc_html($term->count) . ')</span>';
        echo '</label>';
    }
    echo '<p class="description">' . __('Select event categories to display in the ticker.', 'wardjet') . '</p>';
}

function wj_ticker_news_categories_callback() {
    $options = get_option('wj_ticker_options', []);
    $selected = isset($options['news_categories']) ? $options['news_categories'] : [];

    $terms = get_terms([
        'taxonomy' => 'news',
        'hide_empty' => true,
    ]);

    if (empty($terms) || is_wp_error($terms)) {
        echo '<p class="description">' . __('No news categories found.', 'wardjet') . '</p>';
        return;
    }

    foreach ($terms as $term) {
        $checked = in_array($term->term_id, $selected) ? 'checked' : '';
        echo '<label style="display: block; margin: 5px 0;">';
        echo '<input type="checkbox" name="wj_ticker_options[news_categories][]" value="' . esc_attr($term->term_id) . '" ' . $checked . '>';
        echo ' ' . esc_html($term->name) . ' <span class="count">(' . esc_html($term->count) . ')</span>';
        echo '</label>';
    }
    echo '<p class="description">' . __('Select news categories to display in the ticker.', 'wardjet') . '</p>';
}

/**
 * Settings Page HTML
 */
function wj_ticker_settings_page_html() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('wj_ticker_settings_group');
            do_settings_sections('wj-ticker-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

/**
 * Get Ticker Items
 *
 * Fetches news_and_events posts based on settings and current locale.
 *
 * @return array Array of WP_Post objects
 */
function wj_get_ticker_items($override_locale = null) {
    $options = get_option('wj_ticker_options', []);

    // Check if ticker is enabled
    if (empty($options['enabled'])) {
        return [];
    }

    // Get settings
    $count = isset($options['count']) ? absint($options['count']) : 10;
    $events_cats = isset($options['events_categories']) ? $options['events_categories'] : [];
    $news_cats = isset($options['news_categories']) ? $options['news_categories'] : [];

    // If no categories selected, return empty
    if (empty($events_cats) && empty($news_cats)) {
        return [];
    }

    // Editorial source of truth: ticker decisions (which categories, ticker_until)
    // live on the EN posts. Translations are resolved per visitor in
    // wj_localize_ticker_items() before render. The $override_locale param is
    // kept for callers that want to force a different source locale; otherwise
    // we always read from en-us.
    $source_locale = ($override_locale !== null) ? $override_locale : 'en-us';

    // Build per-category buckets — fetch latest posts per selected term, then interleave
    $buckets = [];

    foreach ($events_cats as $term_id) {
        $posts = get_posts([
            'post_type'      => 'news_and_events',
            'posts_per_page' => $count,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'tax_query'      => [[
                'taxonomy' => 'events',
                'field'    => 'term_id',
                'terms'    => [$term_id],
            ]],
            'meta_query' => [[
                'key'   => 'region_language_code',
                'value' => $source_locale,
            ]],
        ]);
        if (!empty($posts)) $buckets[] = $posts;
    }

    foreach ($news_cats as $term_id) {
        $posts = get_posts([
            'post_type'      => 'news_and_events',
            'posts_per_page' => $count,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'tax_query'      => [[
                'taxonomy' => 'news',
                'field'    => 'term_id',
                'terms'    => [$term_id],
            ]],
            'meta_query' => [[
                'key'   => 'region_language_code',
                'value' => $source_locale,
            ]],
        ]);
        if (!empty($posts)) $buckets[] = $posts;
    }

    if (empty($buckets)) return [];

    // Round-robin interleave: pick one from each bucket in turn until we hit $count
    $result = [];
    $seen = [];
    $exhausted = false;
    while (count($result) < $count && !$exhausted) {
        $exhausted = true;
        foreach ($buckets as $i => &$bucket) {
            if (empty($bucket)) continue;
            $post = array_shift($bucket);
            if (isset($seen[$post->ID])) continue; // skip dupes (post in multiple cats)
            $seen[$post->ID] = true;
            $result[] = $post;
            $exhausted = false;
            if (count($result) >= $count) break;
        }
        unset($bucket);
    }

    return $result;
}

/**
 * Get Ticker Category for Post
 *
 * @param int $post_id Post ID
 * @return string Category name or empty string
 */
function wj_get_ticker_category($post_id) {
    $options = get_option('wj_ticker_options', []);
    $events_cats = isset($options['events_categories']) ? $options['events_categories'] : [];
    $news_cats = isset($options['news_categories']) ? $options['news_categories'] : [];

    // Check events taxonomy first
    if (!empty($events_cats)) {
        $terms = wp_get_post_terms($post_id, 'events');
        if (!empty($terms) && !is_wp_error($terms)) {
            foreach ($terms as $term) {
                if (in_array($term->term_id, $events_cats)) {
                    $label = function_exists('wj_get_term_label') ? wj_get_term_label((int) $term->term_id, null, $term->name) : $term->name;
                    return strtoupper($label);
                }
            }
        }
    }

    // Check news taxonomy
    if (!empty($news_cats)) {
        $terms = wp_get_post_terms($post_id, 'news');
        if (!empty($terms) && !is_wp_error($terms)) {
            foreach ($terms as $term) {
                if (in_array($term->term_id, $news_cats)) {
                    $label = function_exists('wj_get_term_label') ? wj_get_term_label((int) $term->term_id, null, $term->name) : $term->name;
                    return strtoupper($label);
                }
            }
        }
    }

    return '';
}

/**
 * ─────────────────────────────────────────────────────────────────────────────
 * AJAX ticker — date-filtered, REST-delivered
 * ─────────────────────────────────────────────────────────────────────────────
 */

/**
 * Filter out items whose `ticker_until` date has passed.
 *
 * ACF stores dates as Ymd (e.g. 20260425). Missing/empty meta = evergreen.
 *
 * @param WP_Post[] $items
 * @return WP_Post[]
 */
function wj_filter_ticker_items_by_date($items) {
    if (empty($items)) return $items;
    $today = current_time('Ymd');
    return array_values(array_filter($items, function($post) use ($today) {
        $until = get_post_meta($post->ID, 'ticker_until', true);
        if (empty($until)) return true;
        return strcmp($until, $today) >= 0;
    }));
}

/**
 * Filter ticker items by the visitor's locale.
 *
 * Each EN post may have a `ticker_display_locales` checkbox array:
 *   - Empty / contains 'all' → show on every locale (default, backwards-compatible).
 *   - One or more specific locale codes → show only on those locales.
 *
 * Must run AFTER wj_get_ticker_items() (EN source) and
 * BEFORE wj_localize_ticker_items() (translation swap).
 *
 * @param WP_Post[] $items   EN-source ticker items
 * @param string    $locale  Visitor locale (e.g. 'es-us')
 * @return WP_Post[]
 */
function wj_filter_ticker_items_by_locale($items, $locale = null) {
    if (empty($items)) return $items;

    if ($locale === null) {
        $locale = 'en-us';
        if (function_exists('lc_get_locale_from_url')) {
            $locale = lc_get_locale_from_url();
        }
    }

    return array_values(array_filter($items, function($post) use ($locale) {
        $display_locales = get_post_meta($post->ID, 'ticker_display_locales', true);

        // No meta / empty / not set → treat as "all" (backwards compatible)
        if (empty($display_locales) || !is_array($display_locales)) {
            return true;
        }

        // Explicit "all" → show everywhere
        if (in_array('all', $display_locales, true)) {
            return true;
        }

        // Specific locales → only show if visitor locale is in the list
        return in_array($locale, $display_locales, true);
    }));
}

/**
 * Swap each EN ticker item for its localized translation when one exists.
 *
 * Translations are linked via shared `translation_group_id` post meta plus
 * `region_language_code`. Falls back to the EN post when no translation
 * exists for the requested locale (or when the EN post has no group ID set).
 *
 * @param WP_Post[]   $items   EN-source ticker items
 * @param string|null $locale  Display locale (defaults to current URL locale)
 * @return WP_Post[]
 */
function wj_localize_ticker_items($items, $locale = null) {
    if (empty($items)) return $items;
    if ($locale === null) {
        $locale = 'en-us';
        if (function_exists('lc_get_locale_from_url')) {
            $locale = lc_get_locale_from_url();
        }
    }
    if ($locale === 'en-us') return $items;

    $localized = [];
    foreach ($items as $post) {
        $group_id = get_post_meta($post->ID, 'translation_group_id', true);
        if (empty($group_id)) {
            $localized[] = $post;
            continue;
        }
        $matches = get_posts([
            'post_type'      => 'news_and_events',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'post__not_in'   => [$post->ID],
            'meta_query'     => [
                'relation' => 'AND',
                ['key' => 'translation_group_id', 'value' => $group_id],
                ['key' => 'region_language_code', 'value' => $locale],
            ],
        ]);
        $localized[] = !empty($matches) ? $matches[0] : $post;
    }
    return $localized;
}

/**
 * Render ticker inner-track HTML (items + dividers, duplicated for seamless loop).
 *
 * Only the inner track markup — the shell and read-more button are server-rendered.
 *
 * @param WP_Post[] $items
 * @return string
 */
function wj_render_ticker_track_html($items) {
    if (empty($items)) return '';
    ob_start();
    // Render items once; JS pads + duplicates on the client for a seamless,
    // viewport-fitting loop with constant scroll speed.
    $total = count($items);
    foreach (array_values($items) as $i => $item) :
        $category  = wj_get_ticker_category($item->ID);
        $permalink = get_permalink($item->ID);
        $title     = $item->post_title;
        ?>
        <span class="ticker-item">
            <?php if ($category) : ?>
                <span class="ticker-category"><?php echo esc_html($category); ?></span>
            <?php endif; ?>
            <a href="<?php echo esc_url($permalink); ?>" class="ticker-link">
                <?php echo esc_html($title); ?>
            </a>
        </span>
        <?php if ($i < $total - 1) : ?>
            <span class="ticker-divider">|</span>
        <?php endif; ?>
        <?php
    endforeach;
    return ob_get_clean();
}

/**
 * Resolve the localized News & Events page URL for a given locale.
 * Matches by translation_group_id="news-events-2025" + region_language_code.
 * Prefers a canonical slug (not ending in -N) when duplicates exist.
 */
function wj_get_news_events_url($locale = null) {
    if ($locale === null) {
        $locale = 'en-us';
        if (function_exists('lc_get_locale_from_url')) {
            $locale = lc_get_locale_from_url();
        }
    }

    $pages = get_posts([
        'post_type'      => 'page',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'ID',
        'order'          => 'ASC',
        'meta_query'     => [
            'relation' => 'AND',
            ['key' => 'translation_group_id', 'value' => 'news-events-2025'],
            ['key' => 'region_language_code', 'value' => $locale],
        ],
    ]);

    if (!empty($pages)) {
        // Prefer canonical slug (not a -N legacy duplicate)
        foreach ($pages as $p) {
            if (!preg_match('/-\d+$/', $p->post_name)) {
                return get_permalink($p);
            }
        }
        return get_permalink($pages[0]);
    }

    // Fallback: /country/lang/news-events/
    $parts = explode('-', $locale);
    if (count($parts) === 2) {
        return home_url('/' . $parts[1] . '/' . $parts[0] . '/news-events/');
    }
    return home_url('/us/en/news-events/');
}

/**
 * Localized "EVENTS" button label for the ticker.
 */
function wj_get_news_events_label($locale = null) {
    if ($locale === null) {
        $locale = 'en-us';
        if (function_exists('lc_get_locale_from_url')) {
            $locale = lc_get_locale_from_url();
        }
    }
    $labels = [
        'en-us' => 'EVENTS',
        'en-ca' => 'EVENTS',
        'en-uk' => 'EVENTS',
        'es-us' => 'EVENTOS',
        'fr-ca' => 'ÉVÉNEMENTS',
        'pl-pl' => 'WYDARZENIA',
    ];
    return isset($labels[$locale]) ? $labels[$locale] : $labels['en-us'];
}

/**
 * Render the CNC Shop fallback HTML for the current (or given) locale.
 * Shown when there are no live news/events to display in any locale.
 */
function wj_get_ticker_fallback_html($locale = null) {
    if ($locale === null) {
        $locale = 'en-us';
        if (function_exists('lc_get_locale_from_url')) {
            $locale = lc_get_locale_from_url();
        }
    }

    $links = [
        'en-us' => 'https://cncshop.com/',
        'es-us' => 'https://cncshop.com/',
        'en-ca' => 'https://ca.cncshop.com/',
        'fr-ca' => 'https://ca.cncshop.com/',
        'en-uk' => 'https://uk.cncshop.com/',
        'pl-pl' => 'https://www.cncshop.eu/',
    ];

    // [lead-in text, link label] per locale
    $copy = [
        'en-us' => ['For all your consumables and CNC router needs, check out our ', 'CNC Shop'],
        'en-ca' => ['For all your consumables and CNC router needs, check out our ', 'CNC Shop'],
        'en-uk' => ['For all your consumables and CNC router needs, check out our ', 'CNC Shop'],
        'es-us' => ['Para todos sus consumibles y necesidades de routers CNC, visite nuestra ', 'CNC Shop'],
        'fr-ca' => ['Pour tous vos consommables et besoins en routeurs CNC, consultez notre ', 'CNC Shop'],
        'pl-pl' => ['Wszystkie materiały eksploatacyjne i akcesoria do routerów CNC znajdziesz w naszym sklepie ', 'CNC Shop'],
    ];

    $url  = isset($links[$locale]) ? $links[$locale] : $links['en-us'];
    $text = isset($copy[$locale])  ? $copy[$locale]  : $copy['en-us'];

    return '<span class="ticker-fallback">'
         . esc_html($text[0])
         . '<a href="' . esc_url($url) . '" target="_blank" rel="noopener" class="ticker-fallback__cta">'
         . esc_html($text[1])
         . '</a>'
         . '</span>';
}

/**
 * REST: GET /wp-json/wardjet/v1/ticker
 * Response: { empty: bool, fallback?: bool, html: string }
 */
add_action('rest_api_init', function() {
    register_rest_route('wardjet/v1', '/ticker', [
        'methods'             => 'GET',
        'permission_callback' => '__return_true',
        'callback'            => 'wj_rest_ticker_callback',
    ]);
});

function wj_rest_ticker_callback(WP_REST_Request $request) {
    // Simulate the calling page's URI so lc_get_locale_from_url() returns
    // the correct locale (REST URL /wp-json/... would otherwise parse as "wardjet-wp-json").
    $path = $request->get_param('path');
    $orig_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;
    if (is_string($path) && $path !== '') {
        $_SERVER['REQUEST_URI'] = $path;
    }

    // Source: always EN editorial decisions (tax + ticker_until live on EN posts).
    $items = wj_get_ticker_items();
    $items = wj_filter_ticker_items_by_date($items);
    $items = wj_filter_ticker_items_by_locale($items);
    // Per-item translation swap: EN post → locale-specific translation when present.
    $items = wj_localize_ticker_items($items);

    // Fallback: no live news at all — show the CNC Shop banner
    // in the visitor's locale so the ticker area is never empty.
    if (empty($items)) {
        $fallback_html = wj_get_ticker_fallback_html();

        if ($orig_uri !== null) {
            $_SERVER['REQUEST_URI'] = $orig_uri;
        }

        return new WP_REST_Response([
            'empty'    => false,
            'fallback' => true,
            'html'     => $fallback_html,
        ], 200);
    }

    $html = wj_render_ticker_track_html($items);

    if ($orig_uri !== null) {
        $_SERVER['REQUEST_URI'] = $orig_uri;
    }

    return new WP_REST_Response([
        'empty' => false,
        'html'  => $html,
    ], 200);
}

/**
 * ─────────────────────────────────────────────────────────────────────────────
 * Admin UX — "Ticker" column + status filter on news_and_events list
 * ─────────────────────────────────────────────────────────────────────────────
 */

add_filter('manage_news_and_events_posts_columns', function($columns) {
    $new = [];
    foreach ($columns as $key => $label) {
        $new[$key] = $label;
        if ($key === 'title') {
            $new['wj_ticker_status'] = __('Ticker', 'wardjet');
        }
    }
    if (!isset($new['wj_ticker_status'])) {
        $new['wj_ticker_status'] = __('Ticker', 'wardjet');
    }
    return $new;
});

add_action('manage_news_and_events_posts_custom_column', function($column, $post_id) {
    if ($column !== 'wj_ticker_status') return;
    $until = get_post_meta($post_id, 'ticker_until', true);
    if (empty($until)) {
        echo '<span style="color:#666;" title="Always eligible — no end date">— Evergreen</span>';
    } else {
        $today = current_time('Ymd');
        $label = date_i18n(get_option('date_format'), strtotime($until));
        if (strcmp($until, $today) >= 0) {
            echo '<span style="color:#00a32a;">● Live until ' . esc_html($label) . '</span>';
        } else {
            echo '<span style="color:#d63638;">● Expired ' . esc_html($label) . '</span>';
        }
    }

    $display_locales = get_post_meta($post_id, 'ticker_display_locales', true);
    if (empty($display_locales) || !is_array($display_locales) || in_array('all', $display_locales, true)) {
        echo '<br><span style="color:#666;">🌍 All locales</span>';
    } else {
        $short = array_map(function($code) {
            return strtoupper(str_replace('-', ' ', $code));
        }, $display_locales);
        echo '<br><span style="color:#2271b1;">' . esc_html(implode(', ', $short)) . '</span>';
    }
}, 10, 2);

add_filter('manage_edit-news_and_events_sortable_columns', function($columns) {
    $columns['wj_ticker_status'] = 'ticker_until';
    return $columns;
});

add_action('pre_get_posts', function($query) {
    if (!is_admin() || !$query->is_main_query()) return;
    if ($query->get('post_type') !== 'news_and_events') return;

    // Sort by ticker_until
    if ($query->get('orderby') === 'ticker_until') {
        $query->set('meta_key', 'ticker_until');
        $query->set('orderby', 'meta_value');
    }

    // Filter by ticker status (?wj_ticker_status=live|expired|evergreen)
    $status = isset($_GET['wj_ticker_status']) ? sanitize_key($_GET['wj_ticker_status']) : '';
    if (!$status) return;

    $today = current_time('Ymd');
    if ($status === 'evergreen') {
        $query->set('meta_query', [[
            'relation' => 'OR',
            ['key' => 'ticker_until', 'compare' => 'NOT EXISTS'],
            ['key' => 'ticker_until', 'value' => '', 'compare' => '='],
        ]]);
    } elseif ($status === 'live') {
        $query->set('meta_query', [[
            'key'     => 'ticker_until',
            'value'   => $today,
            'compare' => '>=',
        ]]);
    } elseif ($status === 'expired') {
        $query->set('meta_query', [[
            'key'     => 'ticker_until',
            'value'   => $today,
            'compare' => '<',
        ]]);
    }
});

add_action('restrict_manage_posts', function() {
    global $typenow;
    if ($typenow !== 'news_and_events') return;
    $current = isset($_GET['wj_ticker_status']) ? sanitize_key($_GET['wj_ticker_status']) : '';
    ?>
    <select name="wj_ticker_status">
        <option value=""><?php esc_html_e('All ticker statuses', 'wardjet'); ?></option>
        <option value="live" <?php selected($current, 'live'); ?>><?php esc_html_e('Live in ticker', 'wardjet'); ?></option>
        <option value="expired" <?php selected($current, 'expired'); ?>><?php esc_html_e('Expired', 'wardjet'); ?></option>
        <option value="evergreen" <?php selected($current, 'evergreen'); ?>><?php esc_html_e('Evergreen', 'wardjet'); ?></option>
    </select>
    <?php
});

/**
 * Purge Kinsta cache for the REST endpoint when an ACF ticker_until changes.
 * (The REST endpoint is not page-cached by default, but invalidate any
 * object-cache / transient layer just in case.)
 */
add_action('acf/save_post', function($post_id) {
    if (get_post_type($post_id) !== 'news_and_events') return;
    wp_cache_delete('wj_ticker_items', 'wardjet');
}, 20);