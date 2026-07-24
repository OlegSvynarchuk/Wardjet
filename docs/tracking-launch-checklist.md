# Tracking suppression on the dev site — launch re-activation checklist

On the dev site (`wardjet.pixels2pixels.ch`) the analytics / marketing tags are
suppressed so **dev traffic does not pollute the production accounts**.
Suppression is **host-conditional** and lifts automatically on production — the
only manual step at launch is confirming the production domain.

## The host gate

`themes/wardjet/functions.php` defines `wj_tracking_allowed()`:

- Returns **TRUE only** when `HTTP_HOST` is `wardjet.com` or `www.wardjet.com`.
- On any other host (dev / staging) it returns **FALSE** → tracking suppressed.

> ⚠️ **If production launches on a domain other than `wardjet.com` / `www.wardjet.com`,
> add it to `$prod_hosts` inside `wj_tracking_allowed()`** — otherwise tracking
> stays OFF in production. This is the one thing that must be checked at launch.

## Auto-reactivates on production (nothing to do but the domain check above)

All of these live inside the `if ( ! wj_tracking_allowed() )` block, so a matching
production host restores them automatically:

1. **Google Tag Manager** (`GTM-5P6ZD8G`) — gated in `header.php` (script) and
   `footer.php` (noscript).
2. **GA4 / Google Site Kit** (`G-ZJHT4P0VKQ`) — Site Kit tag output blocked via the
   `googlesitekit_analytics-4_tag_blocked` / `googlesitekit_tagmanager_tag_blocked` filters.
3. **lead-forensics-roi** — `lfv2_inject_script` (wp_head) + `lfv2_inject_noscript`
   (wp_body_open) removed off-prod.
4. **WPCode global snippets** (Enhanced Conversions / dataLayer) —
   `wpcode_global_frontend_header/body/footer` removed off-prod.

## NOT gated — still firing on dev *and* prod (left active by decision)

The **"Header and Footer" plugin** (`hefo` option, `head` + `body` fields) injects
these marketing tags. They were intentionally left on:

- Google Optimize (`OPT-MCD6XLB`)
- Google Ads gtag (`AW-1071248013`) + call-conversion
- Meta Pixel (`1669090714482867`)
- Simpli.fi tag
- LinkedIn Insight (partner `9436321`)

These reach their platforms from dev too. To also suppress them on dev later, add
this inside the same non-prod block (both the hefo head function and the anonymous
`wp_body_open` closure read `$hefo_options` and emit nothing for empty fields):

```php
if ( isset( $GLOBALS['hefo_options'] ) && is_array( $GLOBALS['hefo_options'] ) ) {
    foreach ( array( 'head', 'head_home', 'body', 'mobile_body' ) as $f ) {
        $GLOBALS['hefo_options'][ $f ] = '';
    }
}
```

The hefo **footer** field is functional (UTM/gclid capture, Buttonizer link carry)
and is left intact.

## Reference

- **Live tracking IDs** — verify these are WARDJET's real production accounts before launch:
  GTM `GTM-5P6ZD8G`; GA4 `G-ZJHT4P0VKQ` (property 304281905 / account 135648754);
  Meta Pixel `1669090714482867`; Google Ads `AW-1071248013`; LinkedIn partner `9436321`.
- Google Site Kit Search Console property is the production `sc-domain:wardjet.com`.
- UTM persistence: `utms-carry-pages` plugin (active) + hefo footer JS — both functional, left on.
- **Opcache**: php-fpm caches `functions.php` (`validate_timestamps=On`, ~2s revalidate).
  After editing tracking gates, force-refresh with a web-context `opcache_reset()` for an
  instant pickup; WP-CLI's opcache is a separate SAPI and does not reset the fpm cache.
