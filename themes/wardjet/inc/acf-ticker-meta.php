<?php
/**
 * ACF: "Show in news ticker until" date field on news_and_events posts.
 *
 * Empty = evergreen (always eligible for ticker — use for Product Launches).
 * Set date = eligible through that date (inclusive), then silently drops out.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'acf_add_local_field_group' ) ) {
    return;
}

add_action( 'acf/init', function () {

    acf_add_local_field_group( [
        'key'      => 'group_wj_ticker_meta',
        'title'    => 'News Ticker',
        'fields'   => [
            [
                'key'           => 'field_wj_ticker_until',
                'label'         => 'Show in news ticker until',
                'name'          => 'ticker_until',
                'type'          => 'date_picker',
                'instructions'  => 'Leave empty for evergreen items (e.g. Product Launches). For events/expos/promotions, pick the last day this post should appear in the ticker. After this date the post stays on the News & Events page but stops scrolling in the ticker.',
                'required'      => 0,
                'display_format' => 'M j, Y',
                'return_format'  => 'Ymd',
                'first_day'      => 1,
            ],
            [
                'key'           => 'field_wj_ticker_display_locales',
                'label'         => 'Show in ticker for',
                'name'          => 'ticker_display_locales',
                'type'          => 'checkbox',
                'instructions'  => 'Which locale tickers should this post appear on? Leave unchecked (default) to show on every locale — same as checking "All locales".',
                'required'      => 0,
                'choices'       => [
                    'all'   => 'All locales',
                    'en-us' => 'EN (US)',
                    'es-us' => 'ES (US)',
                    'en-ca' => 'EN (CA)',
                    'fr-ca' => 'FR (CA)',
                    'en-uk' => 'EN (UK)',
                    'pl-pl' => 'PL (PL)',
                ],
                'layout'        => 'vertical',
                'return_format' => 'value',
            ],
        ],
        'location' => [
            [
                [
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => 'news_and_events',
                ],
            ],
        ],
        'position'     => 'side',
        'style'        => 'default',
        'label_placement' => 'top',
        'active'       => true,
    ] );

} );
