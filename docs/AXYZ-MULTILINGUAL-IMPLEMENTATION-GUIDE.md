# AXYZ Multilingual System — Implementation Guide

This document captures the multilingual setup used on axyz.com so the same
architecture can be replicated on wardjet.com (or any other WordPress site).

The system replaces WPML with a thinner, purpose-built solution that lives
inside a single custom plugin (`wj-multilingual`) and a small amount of
nginx/Kinsta config. No WPML, no third-party translation plugin runtime.

---

## 0. What's portable vs what's site-specific

Everything in this guide falls into one of two buckets. Most of the
**architecture** (router, permalink filter, helpers, switcher cascade,
canonical redirects, SEO output) is portable as-is. The **content model**
(which CPTs exist, what the URLs look like for them, which countries
redirect where) is data you provide per site.

### Portable architecture (works as-is)

- 2-segment URL prefix pattern (`/cc/ll/`)
- `region_language_code` + `translation_group_id` + `is_frontpage` meta schema
- Memoized locale helpers (`lc_get_locale_from_url`, `lc_locale_to_prefix`)
- Router (parse_request) — resolves CPT singles by `translation_group_id`
  with aliasable-locale fallback
- `post_type_link` filter — inserts the `/cc/ll/` prefix
- Language switcher cascade — looks up translation, falls back through
  alias and en-us
- Canonical redirects — preserves query string, handles fallback flag
- hreflang + canonical SEO output
- Per-locale menu location pattern (`{base}-{lang}-{region}`)
- Per-locale ACF field variants (`{field}_{lang}_{region}`)
- nginx geo redirect skeleton + `src=switch` bypass
- UTM-carry plugin
- `wj_redirect` filter that preserves query string on locale redirects

### Site-specific configuration (must be defined per site)

| What | Where it lives | Axyz example | What to define for wardjet |
|---|---|---|---|
| **List of supported locales** | `wj_allowed_locales()` in `permalinks.php` + `seo_supported_locales()` in `locale.php` | 6 codes: `en-us`, `es-us`, `en-ca`, `fr-ca`, `en-uk`, `pl-pl` | wardjet's locales — may be fewer or different |
| **Aliasable locales** (fall back to en-us silently) | `wj_multilingual_aliasable_locales()` in `routing.php` + `$aliasable_locales` in `menu.php` | `['en-uk', 'en-ca']` | TBD — depends on wardjet's translation coverage |
| **Multilingual CPTs** (which post types get `/cc/ll/` prefix) | `$scoped` array in `permalinks.php` post_type_link filter | `routers, industry, accessories, software, materials, video, blog, news_and_events, webinar, testimonial, post` | Different CPTs (waterjets, applications, brands, etc.) |
| **Base → CPT slug map** (URL base aliases, e.g. plural ↔ singular) | `wj_multilingual_base_to_pt()` in `routing.php` | `testimonials → testimonial`, `webinars → webinar`, `blogs → blog` | wardjet's CPT URL bases |
| **Aliasable CPTs** (subset where en-us serves under en-uk/en-ca URL too) | `$alias_cpts` in `permalinks.php` | `blog, news_and_events, testimonial, webinar` | wardjet's en-us-only content types |
| **Country → locale homepage map** | nginx geo rules | `FR/BE/CH → /ca/fr`, `GB → /uk/en`, `CA → /ca/en`, Spanish countries → `/us/es`, `PL → /pl/pl` | TBD per wardjet's audience |
| **City-specific overrides** | nginx geo rules | Montreal/Quebec/Laval → `/ca/fr` | Whatever cities matter for wardjet (or none) |
| **Legacy WPML slug map** (only if migrating from WPML) | template_redirect rule #5 in `canonical.php` | `systemes-de-routeurs → routers`, `frezarki → routers`, `accesorios → accessories`, … | Match wardjet's old localized slugs (or skip entirely if not WPML migration) |
| **Localized non-prefixed CPT slugs** (translated bases used as legacy URLs) | template_redirect rule #4 in `canonical.php` | `blogi, blogues, baza-wiedzy, seminarium-internetowe, seminario-web, webinaire, wiadomosci-i-wydarzenia, noticias-y-eventos, nouvelles-et-evenements` | Match wardjet's translated URL bases |
| **Taxonomy section labels** to localize | ACF options page setup | `events, news, blog-industry, blog-material, technical_topics, …` | wardjet's taxonomies (likely waterjet-cutting topics) |
| **Per-locale menu locations** to register | theme `functions.php` `register_nav_menus()` | `main-{locale}`, `footer-main-{locale}`, `footer-nav-{locale}` × 6 | Same pattern, different `{base}` names if needed |
| **Per-locale ACF field variants** (e.g. footer links) | ACF options page setup | `footer_links`, `footer_links_us_en`, `footer_links_fr_ca`, … | wardjet's option fields |
| **HQ ordering per locale** (which country card appears first in footer) | hardcoded `$order_map` in `footer.php` | en-us → us, ca, uk; fr-ca → ca, us, uk; en-uk → uk, us, ca | wardjet's HQ list & priorities |

The recommended pattern is to **make these data-driven**, ideally:
- A single `wj-multilingual-config.php` file (or `WJM_*` constants in
  the plugin) holding the locale list, aliasable list, CPT list, etc.
- Filters/options so the theme can extend them without forking the plugin

For axyz everything's inline in each include — fine, but for wardjet
consider centralising into one config array so future locale additions
or CPT changes touch one place.

### Quick-start mental model

Think of the system as having three layers:

```
┌─────────────────────────────────────────────────────────────┐
│ Layer 1 — URL routing                                       │
│   /us/en/ → locale homepage   /us/en/{cpt}/{slug} → CPT     │
│   Handled by: routing.php (parse_request)                   │
│   What you customize: which CPTs are multilingual          │
└─────────────────────────────────────────────────────────────┘
┌─────────────────────────────────────────────────────────────┐
│ Layer 2 — URL generation (permalinks)                       │
│   $post → URL with correct /cc/ll/ prefix                   │
│   Handled by: permalinks.php (post_type_link filter)        │
│   What you customize: scoped post types + aliasable list   │
└─────────────────────────────────────────────────────────────┘
┌─────────────────────────────────────────────────────────────┐
│ Layer 3 — Cross-locale navigation (switcher)                │
│   For each locale, find the equivalent URL of current page  │
│   Handled by: menu.php (nav menu items filter)              │
│   What you customize: aliasable locales + fallback logic    │
└─────────────────────────────────────────────────────────────┘

Cross-cutting concerns:
   • Canonical redirects + UTM preservation → canonical.php
   • SEO (hreflang/canonical tags) → seo.php
   • Term + taxonomy label localization → term-labels.php + ACF integrations
   • Geo-based country redirects → nginx (no plugin code)
```

When you adapt for wardjet, focus on the data in section 0 above. The
PHP code in `includes/*.php` mostly stays the same; only the lists at
the top of each file change.

---

## 1. Locales supported

6 locales. The codes use the lowercase `lang-region` format:

| Locale code | URL prefix    | hreflang  | Source content?       |
|-------------|---------------|-----------|------------------------|
| `en-us`     | `/us/en/`     | `en-US`   | Primary source        |
| `es-us`     | `/us/es/`     | `es-US`   | Translated            |
| `en-ca`     | `/ca/en/`     | `en-CA`   | Aliasable (falls back to en-us) |
| `fr-ca`     | `/ca/fr/`     | `fr-CA`   | Translated            |
| `en-uk`     | `/uk/en/`     | `en-GB`   | Aliasable (falls back to en-us) |
| `pl-pl`     | `/pl/pl/`     | `pl-PL`   | Translated            |

**Conceptual split:**

- **Source locale**: `en-us` (no prefix on the canonical root, served at `/`)
- **Aliasable locales** (`en-uk`, `en-ca`): when there's no translation for a
  piece of content, the en-us source is served under the aliased prefix.
- **Translated locales** (`es-us`, `fr-ca`, `pl-pl`): need their own
  translation; if missing, the language switcher links back to en-us.

URL prefix is `region/lang` (e.g. `/us/en/`, NOT `/en/us/`). Locale code is
`lang-region` (`en-us`, NOT `us-en`). Conversion both ways is done by
`lc_locale_to_prefix()` and `lc_get_locale_from_url()`.

---

## 2. Plugin structure (`wp-content/plugins/wj-multilingual/`)

```
wj-multilingual/
├── wj-multilingual.php          # Main file (just metadata + requires)
└── includes/
    ├── locale.php               # Memoized locale helpers
    ├── routing.php              # parse_request: serves CPT singles by translation_group_id
    ├── permalinks.php           # post_type_link filter: inserts /cc/ll/ prefix
    ├── menu.php                 # nav_menu_objects filter: builds language switcher
    ├── canonical.php            # redirect_canonical + template_redirect: locale URL normalization
    ├── term-labels.php          # Localized taxonomy term labels
    ├── acf-term-labels.php      # ACF integration for term labels
    ├── acf-tax-section-labels.php # ACF integration for taxonomy section labels
    └── seo.php                  # hreflang + canonical SEO output
```

Plugin loader (`wj-multilingual.php`):

```php
require_once WJ_MULTILINGUAL_DIR . 'includes/locale.php';
require_once WJ_MULTILINGUAL_DIR . 'includes/routing.php';
require_once WJ_MULTILINGUAL_DIR . 'includes/permalinks.php';
require_once WJ_MULTILINGUAL_DIR . 'includes/menu.php';
require_once WJ_MULTILINGUAL_DIR . 'includes/canonical.php';
require_once WJ_MULTILINGUAL_DIR . 'includes/term-labels.php';
require_once WJ_MULTILINGUAL_DIR . 'includes/acf-term-labels.php';
require_once WJ_MULTILINGUAL_DIR . 'includes/acf-tax-section-labels.php';
require_once WJ_MULTILINGUAL_DIR . 'includes/seo.php';
```

All 9 includes have ABSPATH guards and `function_exists()` shims so the
theme keeps working if the plugin is deactivated.

---

## 3. Post-meta schema

Every locale-aware post (pages + multilingual CPTs) carries:

| Meta key                | Type   | Purpose                                                    |
|-------------------------|--------|-------------------------------------------------------------|
| `region_language_code`  | string | The post's locale, e.g. `en-us`, `fr-ca`                    |
| `translation_group_id`  | string | Shared ID linking translations of the same content        |
| `is_frontpage`          | enum   | `yes` for the locale's homepage page (per locale)         |

A single original page and its translations all share the same
`translation_group_id`. The router uses this ID to find sibling
translations.

ACF setup (per content type):
- Sub-field `translation_group_id` (text) on every locale-aware post type
- Sub-field `region_language_code` (text) with dropdown of 6 locales
- Sub-field `is_frontpage` (radio: yes/no) on pages only

---

## 4. Multilingual content types

### Pages (`page`)
- Each locale has its own homepage page (with `is_frontpage=yes` meta)
- Page URLs are NOT prefixed by `post_type_link` filter (page slugs already
  include the locale path: e.g. `/us/en/about/`, `/ca/fr/a-propos/`).
- Router resolves `/cc/ll/` exactly → that locale's homepage page.

### Multilingual CPTs (`scoped` group)
These get the `/cc/ll/` prefix automatically added by the `post_type_link`
filter:

```
post, routers, industry, accessories, software, materials,
testimonial, news_and_events, webinar, blog, video
```

Each CPT post stores its `region_language_code` + `translation_group_id`.

### Aliasable CPTs (subset)
Blog, news_and_events, testimonial, webinar: en-us articles can be served
under en-uk/en-ca prefix when no translation exists.

---

## 5. URL structure

```
/                                  → en-us homepage (canonical root)
/us/en/                            → ALSO en-us homepage (redirects to /)
/us/en/about/                      → en-us About page
/uk/en/                            → en-uk homepage
/ca/en/about/                      → en-ca About page (or en-us alias if no translation)
/ca/fr/a-propos/                   → fr-ca About page (own translation, own slug)
/us/en/blog/some-article/          → en-us blog post
/uk/en/blog/some-article/          → SAME post, served under en-uk prefix (alias)
/ca/fr/blog/un-article-traduit/    → French blog post (own translation)
```

---

## 6. Helpers in `locale.php` (memoized)

```php
lc_get_locale_from_url(): string
    // Parses /us/en/... → "en-us"; returns "en-us" for unprefixed URLs.
    // Cached per request keyed by REQUEST_URI.

lc_locale_to_prefix(string $code): string
    // "en-us" → "us/en"; cached per code.

get_current_lang_from_url(): string
    // Theme alias for lc_get_locale_from_url() (legacy name)

seo_supported_locales(): array
    // Site locale → ISO hreflang map. Used for hreflang tags.
```

---

## 7. Router (`routing.php`)

Hooked to `parse_request` at priority 0. Two cases:

**Case 1**: `/cc/ll/` exactly → load the locale's homepage page
(by `is_frontpage=yes` + `region_language_code` meta).

**Case 2**: `/cc/ll/{base}/{slug}/` → resolve CPT single by
`translation_group_id`:
1. Find ANY post with this slug (any locale)
2. Get its `translation_group_id`
3. Look up sibling with matching `region_language_code = requested locale`
4. If not found AND locale is aliasable (en-uk/en-ca) → fall back to
   en-us sibling
5. Last resort: serve the originally-requested post as-is

Sets globals:
- `$GLOBALS['forced_lang_code']` — the locale to use for permalink generation
- `$GLOBALS['is_fallback_language']` — true if served post's locale ≠
  requested locale (used by canonical filter to prevent unwanted redirects)
- `$GLOBALS['wj_locale_root_frontpage']` — true when serving a locale homepage
  via clean `/cc/ll/` URL

**Base → CPT slug map** (URL aliases for CPT bases, e.g. plural ↔ singular):

```php
'routers'         => 'routers',
'industry'        => 'industry',
'accessories'     => 'accessories',
'software'        => 'software',
'materials'       => 'materials',
'video'           => 'video',
'blog'            => 'blog',
'news_and_events' => 'news_and_events',
'webinar'         => 'webinar',
'webinars'        => 'webinar',   // legacy plural
'testimonial'     => 'testimonial',
'testimonials'    => 'testimonial', // legacy plural
```

---

## 8. Permalinks (`permalinks.php`)

Single authoritative `post_type_link` filter at priority 1000. Handles:

- Pages are intentionally excluded (their slugs already contain the locale).
- Scoped CPT list: see section 4.
- Locale priority order:
  1. `$post->temp_lang_code` (set by lang switcher / auto-alias)
  2. The post's saved `region_language_code` meta
  3. Fallback: `en-us`
- Auto-alias filter for content CPTs (blog, news_and_events, testimonial,
  webinar): when current page locale is en-uk or en-ca and the post is en-us,
  generate URL under the current locale prefix (so the slider/menu shows
  `/uk/en/blog/foo/` not `/us/en/blog/foo/`).
- Base normalization: keeps singular CPT base consistent (testimonials →
  testimonial, blogs → blog, webinars → webinar).
- Trailing slash respects the site's permalink structure.

Also exports:
- `wj_allowed_locales()` — list of 6 locale codes
- `wj_sanitize_locale_code()` — guards against invalid codes
- `wj_get_saved_lang_for_post(int $id): string` — reads saved locale meta
- `wj_trailing(string $url): string` — applies site's trailing-slash rule

---

## 9. Language switcher (`menu.php`)

Hooked to `wp_get_nav_menu_items` (or `wp_nav_menu_objects`). Appends a
language-switcher dropdown to the primary menu, with one child per locale.

For each target locale's link, picks the best URL via a cascade:

```
is_search()                                → keep current URL
is_front_page() / is_home_like             → /cc/ll/ root
is_post_type_archive('testimonial')        → /cc/ll/testimonials/
is_singular(blog/news/testimonial/webinar) →
    1. Find sibling by translation_group_id + locale
    2. If aliasable locale (en-uk/en-ca) + no sibling → show en-us content
       with temp_lang_code set to target (URL is aliased)
    3. Non-aliasable + no sibling → fall back to en-us URL
        - Check current post's own locale first (orphan handling)
        - Then look up en-us sibling via translation group
is_page()                                  → find translated page, else
                                              fallback locale page, else current URL
Managed CPTs with aliasable behavior       → translation_group_id lookup
                                              with en-us alias fallback
```

`$aliasable_locales = ['en-uk', 'en-ca'];`

---

## 10. Canonical redirects (`canonical.php`)

### `wp_redirect` filter (preserves query string)

```php
add_filter('wp_redirect', function ($location) {
    if (!is_string($location) || $location === '') return $location;
    if (empty($_SERVER['QUERY_STRING'])) return $location;
    if (is_admin()) return $location;
    $req = $_SERVER['REQUEST_URI'] ?? '';
    if (preg_match('#^/(wp-admin|wp-login)#i', $req)) return $location;
    if (strpos($location, '?') !== false) return $location; // already has QS
    return $location . '?' . $_SERVER['QUERY_STRING'];
}, 10, 1);
```

**Why**: every locale-normalization redirect below would otherwise drop
UTM/gclid/fbclid params. This filter re-appends the current request's
query string.

### `redirect_canonical` filter (cancels unwanted redirects)

Returns `false` (cancels) in three cases:

1. `$GLOBALS['is_fallback_language']` is true → never canonicalize across locales
2. `$GLOBALS['wj_locale_root_frontpage']` is true → keep `/cc/ll/` clean URL
3. Current and target URLs differ ONLY by the `/cc/ll/` prefix → cancel

### `template_redirect` action (URL normalization cascade)

Runs ordered cascade at priority 0:

1. `/us/en/` → `/` (canonical site root)
2. `/pl` → `/pl/pl` (Polish legacy prefix expansion)
3. (Optional, gated by `OLD_SITE_REDIRECTS` constant) Legacy 2-letter
   locale prefixes (`/fr`, `/es`, `/pl`) → full 4-letter prefix
4. Non-prefixed CPT URLs (`/blog/`, `/products/`, etc.) → `/us/en/...`
   - Includes localized slug list (`blogi`, `wiadomosci-i-wydarzenia`,
     `seminario-web`, etc.) and taxonomy slugs
5. WPML legacy CPT base slug compatibility → English equivalents
   - `systemes-de-routeurs` → `routers`
   - `frezarki` → `routers`
   - `accesorios` → `accessories`
   - etc.

Skips `wp-admin`, `wp-login.php`, `wp-json`, feeds, sitemap, xmlrpc,
favicon, robots.

---

## 11. SEO output (`seo.php`)

Outputs in `<head>` for each multilingual post:

- `<link rel="alternate" hreflang="...">` for each available translation
- `<link rel="alternate" hreflang="x-default" href="<en-us URL>">`
- `<link rel="canonical">` to the current locale's URL

Pulls translations via `translation_group_id`.

---

## 12. Localized term/taxonomy labels (`term-labels.php`,
`acf-term-labels.php`, `acf-tax-section-labels.php`)

Two layers:

- **Term labels**: individual taxonomy term names are localized per locale
  via per-term ACF fields like `term_label_es_us`, `term_label_fr_ca`.
- **Taxonomy section labels**: the heading text for FacetWP filter sections
  (e.g. "Events", "News", "Technical Topics") is localized per locale via
  ACF options page.

Frontend helpers:
- `wj_get_tax_label(string $key): string` — returns localized label for
  current locale (e.g. `wj_get_tax_label('events')` → "Événements" on /ca/fr/).

---

## 13. nginx geo redirects (Kinsta-managed)

Goes in Kinsta nginx custom config. Handles country-based homepage
redirects, the bare→www canonical redirect, and the `src=switch` bypass
for the language switcher.

Key features (full file at `nginx-geo-rules-WORKING.txt` in project root):

```nginx
# Bare → www canonical (path-preserving)
if ($host = 'wardjet.com') {
    return 301 $scheme://www.wardjet.com$request_uri;
}

# City-specific Canada redirects (must run before generic CA rule)
if ($geoip_city ~* "montreal|montréal") { set $montreal 1;}
if ($geoip_city ~* "quebec|québec|quebec city") { set $quebec 1; }
if ($geoip_city ~* "laval") { set $laval 1; }

# Bypass — both src=switch flag AND the universal is_bypass flag
if ( $request_uri ~* "src=switch" ) { set $montreal 0; set $quebec 0; set $laval 0; }
if ( $request_uri ~* "src=switch" ) { set $is_bypass 1; }

# Gate the country-resolution so bypass actually neutralises country rules
if ( $is_bypass = 0 ) {
    set $domainAndLocation "${host}+&-&+-${geoip_city_country_code}";
}

# City rules (only when set)
if ($montreal = 1) { rewrite "^/?$" "https://www.wardjet.com/ca/fr" redirect; }
if ($quebec = 1)   { rewrite "^/?$" "https://www.wardjet.com/ca/fr" redirect; }
if ($laval = 1)    { rewrite "^/?$" "https://www.wardjet.com/ca/fr" redirect; }

# Country rules
# NOTE: the FR rule on the original site stripped query string (trailing ?).
# For wardjet, write it WITHOUT the trailing ? so UTMs survive:
if ( $domainAndLocation = "wardjet.com+&-&+-FR" ) { rewrite "^/?$" "https://www.wardjet.com/ca/fr" redirect; }
if ( $domainAndLocation = "wardjet.com+&-&+-BE" ) { rewrite "^/?$" "https://www.wardjet.com/ca/fr" redirect; }
# ... (BE, CH, PL, GB, CA, Spanish-speaking countries)

# Final UK fallback (catches "existing country + homepage + axyz domain"
# combination that hasn't been redirected by anything more specific)
if ( $redirectable = "011" ) { rewrite "^/$" $scheme://$host/uk/en redirect; }
```

**Critical**: Kinsta backup restores OVERRIDE nginx config along with WP
files. Keep a verified working copy of geo rules in the repo and re-apply
after any restore.

---

## 14. Multilingual menu locations

Each locale gets its own pair of menus:

```
main-en-us, main-es-us, main-en-ca, main-fr-ca, main-en-uk, main-pl-pl
footer-main-en-us, footer-main-es-us, ...
footer-nav-en-us, footer-nav-es-us, ...
```

The theme picks the right menu via:

```php
[$region, $lang] = wj_current_region_lang(); // e.g. ['ca','fr']
$location = wj_pick_menu_location('main', $region, $lang);
// Tries: main-fr-ca (current) → main-us-en (fallback) → main (legacy)
```

Helpers in theme `footer.php` / `header.php`:
- `wj_current_region_lang(): array` — returns `[region, lang]`
- `wj_pick_menu_location(string $base, string $region, string $lang): string`

---

## 15. Locale-aware ACF fields

ACF options pages and pages have per-locale field variants:

```
footer_links              → default
footer_links_us_en        → US-English variant
footer_links_fr_ca        → French-Canadian variant
footer_links_pl_pl        → Polish variant
... etc.
```

Helper in theme:

```php
$field = lc_pick_locale_field('footer_links');
// Returns 'footer_links_fr_ca' on a /ca/fr/ request,
// 'footer_links_us_en' on /us/en/, or 'footer_links' as last fallback.
```

The helper tries (in order):
1. `{base}_{lang}_{region}` (e.g. `footer_links_fr_ca`)
2. `{base}_{region}_{lang}` (e.g. `footer_links_ca_fr`)
3. `{base}` (e.g. `footer_links`) — default fallback

---

## 16. UTM tracking compatibility

The locale redirects MUST preserve query string. Two pieces:

### Server-side (`canonical.php` wp_redirect filter)
Re-appends `$_SERVER['QUERY_STRING']` to every redirect that doesn't
already have one. Excludes admin/login.

### Client-side (`utms-carry-pages` plugin, v1.2)
Located at `wp-content/plugins/utms-carry-pages/`. Three files:
- `utms-carry-pages.php` — enqueues JS conditionally (only if URL has UTM/gclid/fbclid/_gl)
- `utms-carry.js` — DOM mutation: appends URL params to all internal `<a href>` on:
  - DOMContentLoaded (immediate, doesn't wait for slow iframes)
  - window.load (backup for dynamic content)
  - Click capture phase (last-line defense for dynamic items)

Carry-keys: `utm_*` (any), `gclid`, `fbclid`, `_gl`.

---

## 17. Implementation checklist (for wardjet)

### Database / content prep
- [ ] Add ACF fields to all locale-aware post types:
  - `region_language_code` (text or select with 6 options)
  - `translation_group_id` (text)
  - `is_frontpage` (radio yes/no, on pages only)
- [ ] Set `is_frontpage=yes` + `region_language_code` on the locale homepage pages
- [ ] For existing en-us content that should have translations, assign matching
      `translation_group_id` to the original + each translation

### Plugin install
- [ ] Copy `wj-multilingual/` plugin to `wp-content/plugins/`
- [ ] Update branding/comment headers if desired
- [ ] Activate plugin
- [ ] Verify no fatals in error log

### Theme integration
- [ ] In theme functions.php, ensure no legacy `parse_request` handlers
      conflict with the plugin's router
- [ ] In header.php, add hreflang output from `seo.php`
- [ ] In header.php, ensure language switcher menu location is registered
- [ ] In footer.php, use `wj_current_region_lang()` + `wj_pick_menu_location()`
      helpers (copy from AXYZ footer.php)
- [ ] In template-parts, replace hard-coded en-us URLs with locale-aware
      paths via `lc_locale_to_prefix(lc_get_locale_from_url())`

### Menu setup
- [ ] Register 12 menu locations (6 locales × main + footer)
- [ ] Create 6 main menus in admin, assign to locale-specific locations
- [ ] Add language switcher menu item (or rely on plugin's auto-appended switcher)

### URL normalization
- [ ] Configure permalinks to `/%postname%/` with trailing slash
- [ ] Test `/us/en/` redirects to `/` (canonical root)
- [ ] Test `/cc/ll/{base}/{slug}/` resolves correctly
- [ ] Test legacy non-prefixed URLs (`/blog/`, `/products/`) redirect to `/us/en/...`

### nginx geo redirects
- [ ] Submit geo rules to Kinsta (template in this repo at
      `nginx-geo-rules-WORKING.txt`)
- [ ] Test bare→www redirect
- [ ] Test country redirects (use VPN: Canada, UK, Poland, Spain)
- [ ] Test `?src=switch` bypass (language switcher links should NOT be redirected)
- [ ] Verify UTMs survive geo redirects (no trailing `?` on any rule)

### UTM tracking
- [ ] Copy `utms-carry-pages/` plugin to `wp-content/plugins/`
- [ ] Activate
- [ ] Test: visit `https://www.wardjet.com/?utm_source=test123` → click
      internal links → URL bar should keep `?utm_source=test123`

### SEO sanity
- [ ] Verify hreflang tags appear on multilingual content
- [ ] Verify canonical URL matches the current locale's URL
- [ ] Verify sitemap.xml lists per-locale URLs

### Migration of existing content
- [ ] Identify which existing pages/CPTs have translations
- [ ] Assign matching `translation_group_id` to original + translations
- [ ] Set `region_language_code` on every locale-aware post
- [ ] For aliasable locales (en-uk, en-ca) without translations, no action
      needed — auto-alias serves en-us under their prefix automatically

### Final QA
- [ ] Submit test form with UTMs from each locale → confirm attribution captured
- [ ] Click language switcher from each locale → confirm correct URL
- [ ] Use VPN from each country → confirm geo redirect lands on correct prefix
- [ ] Check Search Console for hreflang errors (give it a few days)

---

## 18. Common pitfalls (from axyz rollout)

1. **WPML cleanup**: Old WPML legacy slugs (e.g. `systemes-de-routeurs`)
   need explicit redirects to English equivalents. Pattern is in
   `canonical.php` template_redirect rule #5.

2. **Orphan CPTs without translation_group_id**: When a CPT post lacks
   `translation_group_id` (e.g. blog post that was never linked to a
   translation), the language switcher's "find sibling by group" lookup
   returns nothing. menu.php handles this by checking the current post's
   own `region_language_code` meta first, then attempting sibling lookup.

3. **Page anchor IDs in localized content**: When templates generate
   anchor IDs from titles (e.g. `feature-{slug}` from
   `sanitize_title($image['title'])`), each locale needs ITS OWN title
   filled in. Use `?:` (elvis) not `??` (null coalesce) for fallback to
   `link.title` because ACF returns empty string, not null.

4. **Kinsta backup restores overwrite nginx config**: Always keep a
   verified working copy of geo rules outside Kinsta. After any restore,
   re-submit the rules to Kinsta and verify.

5. **Cloudflare Bot Protection on `/wp-json/*`**: If the domain is fronted
   by Cloudflare, Bot Fight Mode / JS Detections may challenge `/wp-json/*`
   requests, breaking the editor and any JS that fetches REST endpoints.
   Need a Custom Rule with Skip action on `/wp-json/*` paths.

6. **JotForm POST redirect path**: When using JotForm with POST-redirect
   to a thank-you page, the form data arrives via POST on the destination
   URL. Server-side hooks can capture it (init action) for Enhanced
   Conversion / lead tracking.

7. **PHP function definition placement**: Don't paste raw `<script>`
   blocks inside PHP function bodies. They cause "unexpected <" syntax
   errors. Put them either in HTML output sections, or wrap them in
   `add_action('wp_footer', function() { ?> <script>...</script> <?php });`

8. **Rank Math DB collation**: Different Rank Math analytics tables may
   have different collations (`utf8mb4_unicode_520_ci` vs
   `utf8mb4_unicode_ci`), causing query failures in the block editor.
   Fix via `ALTER TABLE ... CONVERT TO CHARACTER SET utf8mb4 COLLATE
   utf8mb4_unicode_520_ci;` on the mismatched tables.

---

## 19. File-by-file reference

| File                                        | Lines | Purpose                                                     |
|---------------------------------------------|-------|-------------------------------------------------------------|
| `wj-multilingual.php`                       | ~25   | Plugin metadata + require all includes                      |
| `includes/locale.php`                       | ~80   | Memoized locale helpers                                     |
| `includes/routing.php`                      | ~185  | `parse_request` router (CPT singles + locale homepages)     |
| `includes/permalinks.php`                   | ~136  | `post_type_link` filter + auto-alias for en-uk/en-ca        |
| `includes/menu.php`                         | ~340  | Language switcher menu builder                              |
| `includes/canonical.php`                    | ~127  | `wp_redirect` UTM filter + canonical + template_redirect    |
| `includes/term-labels.php`                  | ~280  | Localized taxonomy term labels                              |
| `includes/acf-term-labels.php`              | ~75   | ACF field sub-loader for term labels                        |
| `includes/acf-tax-section-labels.php`       | ~75   | ACF field sub-loader for section labels                     |
| `includes/seo.php`                          | ~260  | hreflang + canonical output                                 |

External dependencies:
- ACF Pro (for the per-locale field variants and repeaters)
- Max Mega Menu (used for the primary menu — language switcher injected
  via wp_get_nav_menu_items filter, mega-menu compatible)
- WPCode Lite (for inserting tracking code without touching theme files)

---

## 20. References to look at on the live axyz site

- Locale homepage pages: post IDs 49 (en-us), 18185 (es-us), 18241
  (en-ca), 18305 (fr-ca), 18357 (en-uk), 20018/20020/20022 (others)
- Multilingual feature page: post IDs 4778 (en-us), 18316 (en-ca),
  18199 (en-uk), 11051 (es-us), 11047 (fr-ca), 11050 (pl-pl)
- Theme files using multilingual helpers:
  - `template-parts/agg-contact.php` — localized contact section
  - `template-parts/features-slider.php` — locale-aware slider links
  - `template-parts/locations-sections.php` — localized location cards
  - `header.php` — language switcher menu
  - `footer.php` — locale-aware menu locations, locale-aware ACF fields,
    locale-aware HQ ordering

---

## End

Keep this file updated as the system evolves on wardjet. The plugin
structure is intentionally small — each include is one responsibility —
so reading the source of each `includes/*.php` file gives the
ground-truth implementation if this guide drifts.
