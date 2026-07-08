<?php
/**
 * News Ticker Template Part
 *
 * Displays a horizontal scrolling ticker with news/events.
 *
 * @package Wardjet
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// ─────────────────────────────────────────────────────────────────
// Hybrid render: server-render initial items so the ticker works even
// when /wp-json/* is blocked (Cloudflare bot challenge). JS still
// refreshes from REST when reachable so cached pages can update.
// ─────────────────────────────────────────────────────────────────
$wj_ticker_options = get_option('wj_ticker_options', []);
if (!empty($wj_ticker_options['enabled'])) {
    $wj_news_url   = wj_get_news_events_url();
    $wj_news_label = wj_get_news_events_label();

    // Mirror the REST callback (wj_rest_ticker_callback) so first paint
    // has the same content the JS fetch would have inserted.
    $wj_items = wj_get_ticker_items();
    $wj_items = wj_filter_ticker_items_by_date($wj_items);
    $wj_items = wj_filter_ticker_items_by_locale($wj_items);
    $wj_items = wj_localize_ticker_items($wj_items);

    if (!empty($wj_items)) {
        $wj_initial_html = wj_render_ticker_track_html($wj_items);
        $wj_is_fallback  = false;
    } else {
        $wj_initial_html = wj_get_ticker_fallback_html();
        $wj_is_fallback  = true;
    }

    $wj_shell_class = 'news-ticker news-ticker--ready';
    if ($wj_is_fallback) {
        $wj_shell_class .= ' news-ticker--fallback';
    }
    ?>
    <div class="<?php echo esc_attr($wj_shell_class); ?>" data-ticker-loader>
        <div class="ticker-track-wrapper">
            <div class="ticker-track"><?php echo $wj_initial_html; // already escaped in render helpers ?></div>
        </div>
        <a href="<?php echo esc_url($wj_news_url); ?>" class="read-more"><?php echo esc_html($wj_news_label); ?></a>
    </div>
    <?php
}
return;
// ─────────────────────────────────────────────────────────────────
// LEGACY SERVER-RENDERED PATH BELOW — kept for reference, unreachable
// ─────────────────────────────────────────────────────────────────

// Get ticker items
$items = wj_get_ticker_items();

// If no items or ticker disabled, don't render
if (empty($items)) {
    return;
}
?>

<?php
// Build locale-aware news & events URL
$path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
$news_url = '/us/en/news-events/';
if (preg_match('#^/([a-z]{2})/([a-z]{2})(?:/|$)#i', $path, $m)) {
    $news_url = '/' . strtolower($m[1]) . '/' . strtolower($m[2]) . '/news-events/';
}
?>
<div class="news-ticker">
    <div class="ticker-track-wrapper">
    <div class="ticker-track">
        <?php
        // Duplicate items for seamless loop
        $total_items = count($items);
        $loops = 2; // Render twice for seamless infinite scroll
        for ($loop = 0; $loop < $loops; $loop++) :
            foreach ($items as $item) :
                $category = wj_get_ticker_category($item->ID);
                $permalink = get_permalink($item->ID);
                $title = esc_html($item->post_title);
        ?>
            <span class="ticker-item">
                <?php if ($category) : ?>
                    <span class="ticker-category"><?php echo esc_html($category); ?></span>
                <?php endif; ?>
                <a href="<?php echo esc_url($permalink); ?>" class="ticker-link">
                    <?php echo esc_html($title); ?>
                </a>
            </span>
            <span class="ticker-divider">|</span>
        <?php
            endforeach;
        endfor;
        ?>
    </div>
    </div>
    <a href="<?php echo esc_url(home_url($news_url)); ?>" class="read-more"><?php esc_html_e('READ MORE', 'axyz'); ?></a>
</div>
