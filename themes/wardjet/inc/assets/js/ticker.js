/**
 * News Ticker — hybrid render + AJAX refresh
 *
 * The PHP template-part renders the ticker contents server-side so it
 * works even when /wp-json/* is blocked (Cloudflare bot challenge).
 * This script:
 *   1. Animates whatever the server rendered (first paint).
 *   2. Tries to refresh from /wp-json/wardjet/v1/ticker.
 *   3. On success, swaps in the fresh items and re-runs the marquee.
 *   4. On failure, leaves the server-rendered content untouched.
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var shell = document.querySelector('.news-ticker[data-ticker-loader]');
        if (!shell) return;

        var track = shell.querySelector('.ticker-track');
        if (!track) return;

        var wrapper = shell.querySelector('.ticker-track-wrapper');

        var sizeAndAnimate = function () {
            if (!wrapper) return;
            var wrapperW = wrapper.clientWidth || 0;
            var trackW   = track.scrollWidth   || 0;
            if (wrapperW === 0 || trackW === 0) return;

            track.style.setProperty('--ticker-start-x', wrapperW + 'px');
            track.style.setProperty('--ticker-end-x',   (-trackW) + 'px');

            var pxPerSec = 80;
            var duration = Math.max(8, (wrapperW + trackW) / pxPerSec);
            track.style.animationDuration = duration.toFixed(2) + 's';
        };

        // Re-measure on resize (debounced).
        var resizeTimer;
        window.addEventListener('resize', function () {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(sizeAndAnimate, 150);
        });

        // 1) Animate the server-rendered content immediately.
        sizeAndAnimate();

        // 2) Attempt to refresh from REST. If it fails (e.g. Cloudflare
        //    challenge on /wp-json/*), the server-rendered content stays.
        var endpoint = (window.wjTicker && wjTicker.endpoint) || '/wp-json/wardjet/v1/ticker';
        endpoint += (endpoint.indexOf('?') === -1 ? '?' : '&') +
                    'path=' + encodeURIComponent(window.location.pathname);

        fetch(endpoint, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
            .then(function (res) { return res.ok ? res.json() : null; })
            .then(function (data) {
                if (!data || data.empty || !data.html) return;

                track.innerHTML = data.html;
                shell.classList.add('news-ticker--ready');

                if (data.fallback) {
                    shell.classList.add('news-ticker--fallback');
                } else {
                    shell.classList.remove('news-ticker--fallback');
                }

                sizeAndAnimate();
            })
            .catch(function () {
                // Network/JS failure — server-rendered content remains visible.
            });
    });
})();
