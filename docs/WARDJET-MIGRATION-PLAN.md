# WardJet Multilingual Migration Plan (WPML → wj-multilingual)

**Goal:** replicate the axyz multilingual + frontend system on the wardjet dev
site (`wardjet.pixels2pixels.ch`), retiring WPML, using the **axyzlive backup as
the 100% blueprint**. Only the **CPT set** may differ.

**Principles (non-negotiable, so we can test without pain):**
- Non-destructive and reversible at every phase. Full backup before touching anything.
- **Never run WPML and wj-multilingual routing at the same time.** WPML stays
  active (read-only source of translation links) until the cutover moment.
- Additive first: bring in code + ACF + meta *inert*, verify, then flip routing.
- Each phase ends in a **testable checkpoint**. If it fails, we roll back that phase only.

---

## Reference topology

| Thing | Location | Role |
|---|---|---|
| Blueprint (done) | `C:\Users\Harmonity\Downloads\axyzlive\public` | axyz site, already on wj-multilingual. Copy from here. |
| Target (todo) | `wardjet.pixels2pixels.ch` (SSH `pixelspi@162.55.0.170:22222`) | dev site, **still on WPML**. Migrate this. |
| Repo (staging) | `C:\Users\Harmonity\Local Sites\wardjet` | holds `wj-multilingual`, `utms-carry-pages`, docs. Byte-identical to blueprint plugins. |

Blueprint theme = **`wardjet`** (same theme name as dev, June-2026 version, WPML-free).
Dev theme = **`wardjet` v3.3.6** (2024, still WPML-coupled). Same lineage → we will
**diff, not blind-overwrite**.

### Blueprint config (axyz values — the "CPTs can differ" part)
- **Locales (6):** `en-us` (source, served at `/`), `es-us`, `en-ca`, `fr-ca`, `en-uk`, `pl-pl`
- **URL prefix:** `region/lang` → `/us/en/`, `/ca/fr/`, `/uk/en/` …
- **Aliasable** (fall back to en-us): `en-uk`, `en-ca`
- **Scoped CPTs:** `post, routers, industry, accessories, software, materials, testimonial, news_and_events, webinar, blog, video`
- **Auto-alias CPTs:** `blog, news_and_events, testimonial, webinar`
- **Meta schema:** `region_language_code`, `translation_group_id`, `is_frontpage`

---

## ⚠️ Known divergences from the blueprint (confirm early)

1. **Host is cPanel/Apache, not Kinsta/nginx.** The blueprint's geo redirects live
   in nginx config (`nginx-geo-rules-WORKING.txt`). On the dev host (CloudLinux +
   `.htaccess`) these must be **re-implemented in `.htaccess`/mod_geoip or a PHP
   hook**, or deferred. Do NOT assume the nginx file drops in.
2. **`proc_open()` disabled** on the host → WP-CLI shell-outs (`wp db import/export`,
   `wp db check`) fail. Use `mysql`/`mysqldump` directly (available), or `wp db query`.
3. **`leadpages` plugin** throws a harmless `HTTP_HOST` CLI warning — prefix WP-CLI
   with `HTTP_HOST=wardjet.pixels2pixels.ch` to silence.
4. **CPT set may differ.** Blueprint CPTs are the axyz set; dev's registered CPTs
   must be inventoried (Phase 0) and the plugin's `$scoped` / `base_to_pt` /
   `$alias_cpts` lists adjusted to match. This is the main "data you provide" step.

---

## Phase 0 — Safety net + assessment (read-only, no changes)

**Backup**
- [ ] Full DB dump (`mysqldump`) + `wp-content` tar of the dev site, downloaded locally.
- [ ] Snapshot current `.htaccess` and `wp-config.php`.

**Inventory the dev site (WP-CLI, read-only)**
- [ ] Registered CPTs + taxonomies (`wp post-type list`, `wp taxonomy list`).
- [ ] WPML languages + default language + translated post counts per type.
- [ ] Active plugins (confirm ACF Pro, FacetWP, Max Mega Menu, Rank Math present).
- [ ] Existing ACF field groups (to avoid key collisions).
- [ ] Permalink structure.

**Reconcile against blueprint → produce a mapping sheet**
- [ ] **CPT map:** blueprint CPT ↔ dev CPT (same? renamed? missing? extra?).
- [ ] **Locale map:** WPML language code → target locale
      (e.g. `en→en-us`, `fr→fr-ca`, `es→es-us`, `pl→pl-pl`; decide `en-ca`/`en-uk` handling — likely aliasable, no separate WPML content).
- [ ] **Taxonomy/section-label list** to localize.
- [ ] **Old WPML slug list** (for later redirect rules).

**Diff theme custom code**
- [ ] `diff` dev `functions.php` vs blueprint `functions.php` → catalog any
      **dev-only custom functionality** that must be preserved through the port.
- [ ] Same for `header.php`, `footer.php`, and any `single-*`/archive templates.

**Checkpoint:** a written reconciliation sheet + backups in hand. No site change.

---

## Phase 1 — Bring in the code, inert (no behavior change) — ✅ DONE 2026-07-02

WPML still active and routing. Everything added here is dormant.

**Completed:** `wj-multilingual` uploaded to dev **deactivated** (all files `php -l` clean);
CPT lists adapted (products/series); ACF locale fields registered in-plugin via
`includes/acf-locale-fields.php` (`region_language_code` select, `translation_group_id`
text, `is_frontpage` radio — dormant until activation). Verified: plugin inactive, WPML
active, homepage HTTP 200, no new errors. `utms-carry-pages` already active.
(Pre-existing unrelated warning: `wardjet-homepage.php:173`.)

- [ ] Copy `wj-multilingual/` and `utms-carry-pages/` into dev `wp-content/plugins/`
      — **leave DEACTIVATED**.
- [ ] Edit plugin config lists to dev's real CPTs/locales (from Phase 0 map):
  - `permalinks.php` → `$scoped`, `$alias_cpts`, `wj_allowed_locales()`
  - `routing.php` → `wj_multilingual_base_to_pt()`, `wj_multilingual_aliasable_locales()`
  - `locale.php` → `seo_supported_locales()`
- [ ] Import ACF field groups (as JSON) for the meta schema:
      `region_language_code`, `translation_group_id` (all locale-aware types),
      `is_frontpage` (pages only), plus the per-locale Options variants
      (`footer_*`, `links_*`, `header_icon*`, `contact_*`, `tax_section_*`).
      Field groups exist but nothing reads them yet.

**Checkpoint:** deactivating/reactivating WPML unaffected; no fatals in error log;
site renders exactly as before.

---

## Phase 2 — Seed meta from WPML (additive data migration) — ✅ DONE 2026-07-02

**Result:** 1,086 posts tagged `region_language_code` (en-us 546 / es-us 231 / fr-ca 241 /
pl-pl 68) + `translation_group_id` (= WPML `trid`); `is_frontpage=yes` on pages
49/1661/1548/7160; 188 orphaned WPML rows (deleted posts) correctly skipped; 5 untagged
stragglers = `[draft] … Copy` duplicates (tag en-us or delete later). WPML untouched.


Derive the custom meta from WPML's own tables — **idempotent, dry-run first.**

- [ ] Script (WP-CLI `eval-file`) reading `icl_translations`:
  - `translation_group_id` ← WPML `trid` (shared across a translation set)
  - `region_language_code` ← WPML `language_code` mapped via Phase-0 locale map
- [ ] Set `is_frontpage=yes` + `region_language_code` on each locale's homepage page.
- [ ] Dry-run: report counts per (post_type, locale); spot-check 5 known translation sets.
- [ ] Apply. Re-runnable without duplication.

This only writes postmeta — WPML keeps working, nothing routes differently yet.

**Checkpoint:** `wp post list --meta_key=region_language_code --format=count` matches
expected totals; sample posts show correct `translation_group_id` siblings.

---

## Phase 3 — Page/URL structure (parent/child + homepages) — ✅ DONE 2026-07-02

**Scheme chosen: all locales prefixed.** Created 3 region containers (`us`=12630,
`ca`=12631, `pl`=12632); homepages became language roots (49→`us/en`, 1661→`us/es`,
1548→`ca/fr`, 7160→`pl/pl`, still served at `/` + `/cc/ll/` by router); 145 pages
re-parented under their language root. Native page URIs now = `/us/en/about-us/`,
`/us/es/…`, `/ca/fr/…`, `/pl/pl/…`. Reversible: every moved page stored `_wj_orig_parent`
(=0) + `_wj_orig_slug` (149 rows). WPML page-tree sync assisted. No collisions/stragglers.
NOTE: page URLs changed → WPML front-end URLs are transitional until Phase 4 cutover.

- [ ] Confirm each locale has a homepage page flagged `is_frontpage=yes`.
- [ ] Establish the **parent/child page structure** per blueprint so page slugs carry
      the `/cc/ll/` path (e.g. `/us/en/about/`, `/ca/fr/a-propos/`). Pages are NOT
      prefixed by the permalink filter — their slug path *is* the locale path.
- [ ] Set permalinks to `/%postname%/` with trailing slash (don't flush live yet).

**Checkpoint:** on a staging copy, `/cc/ll/` resolves to the right homepage page id
(via the router) — validated before touching the live dev routing.

---

## Phase 4 — Cutover routing — ✅ DONE 2026-07-02 (on dev)

**Flipped:** deactivated all 6 WPML components (sitepress, acfml, wpml-string-translation,
wpml-media-translation, wp-seo-multilingual, gravityforms-multilingual); activated
`wj-multilingual`; flushed rewrites. No fatals.

**Theme edits (minimal, backed up as `*.pre-cutover-20260702`):**
- `header.php`: removed the old WPML-based `custom_nav_menu_items()` + its filter (name
  collision with the plugin, which provides a superset: locale switcher **+ search box**).
- `functions.php`: guarded `_custom_nav_menu_item()` with `function_exists()`.

**Added missing rewrite rules to plugin** `routing.php` (`wj_multilingual_rewrite_rules`,
init hook + activation flush) — the blueprint left these in the theme. Without them WP
never sets `post_type` for `/cc/ll/{base}/{slug}/` and CPT singles 404.

**Verified:** all pages (every locale) 200; all 9 CPT singles 200; translated CPTs resolve
via `translation_group_id` (`/ca/fr/products/…`, `/us/es/products/…`); aliasable en-uk/en-ca
serve en-us; hreflang cluster (en-US/es-US/fr-CA/pl-PL/x-default) emitted; switcher renders.

**Known follow-ups (Phase 5, not blockers):** archive locale-filtering templates
(blogs/news/webinars/testimonial show mixed languages until ported); per-locale menus
(one menu currently, CPT URLs rewritten + switcher injected); CPT archive URLs
(`/us/en/products/`) beyond testimonials; `/us/en/` serves home instead of 301→`/`.

## Phase 4 — Cutover routing (the one risky flip — do in a window)

Do this on a **staging clone first**, then repeat on dev during a maintenance window.

- [ ] Deactivate **WPML** (and its add-ons: String Translation, Media, etc.).
- [ ] Activate `wj-multilingual` + `utms-carry-pages`.
- [ ] Port theme frontend (diff-aware, preserving Phase-0 dev-only code):
  - `header.php` (locale helpers, `wj_pick_menu_location`, CTA rendering, switcher)
  - `footer.php` (per-locale menus, `lc_pick_locale_field`, HQ reorder)
  - `functions.php` custom code: dynamic per-locale menu registration
    (`register_dynamic_menus`, `language_codes` option + Language Settings admin page),
    admin language column, REST locale-permalink, ticker endpoint, image sizes, etc.
  - CPT-filtered templates: `blogs.php`, `newsandevents.php`, `page-webinars.php`,
    `archive-testimonial.php`, `wardjet-industries.php`, and template-parts
    `products-section.php` (translation_group ordering), `ind-mat-grid.php`,
    `sub-routers-grid.php`, `agg-contact.php`.
- [ ] `wp rewrite flush`.

**Checkpoint (smoke test):**
- `/` = en-us home; `/us/en/` → 301 `/`
- `/ca/fr/` = French home; a CPT single at `/ca/fr/{base}/{slug}/`
- an aliasable URL `/uk/en/blog/{slug}/` serves en-us content
- no fatals; `wp_head` emits canonical + hreflang

---

## Phase 5 — Menus + localized frontend — 🔄 IN PROGRESS

**✅ Archive locale filtering (2026-07-02):** added `region_language_code` meta_query
(current locale + en-us fallback, per blueprint) to `blogs.php`, `newsandevents.php`,
`page-webinars.php`, `archive-testimonial.php`. Verified counts filter correctly; pages
200. Backups `*.pre-phase5-20260702`. NOTE: FacetWP facet *options/labels* still global
+ English headings (facet-label localization via `wj_get_tax_label()` is a further step).

**✅ Per-locale menus (2026-07-02):** added plugin include `menu-locations.php`
(`register_dynamic_menus` + `wj_current_region_lang` + `wj_pick_menu_location`, ported from
blueprint) → 24 `{base}-{region}-{lang}` locations. Assigned the 16 existing WPML-translated
menus (main/header-nav/footer-main/footer-nav × en/es/fr/pl) to their locale locations.
Verified: each locale renders its own menu (en Products / fr Des produits / es Productos /
pl Produkty). Minor: a few Polish menu items untranslated (content, editable in Appearance→Menus).

**IMPORTANT (menu approach — revised):** editing the templates to point at `{base}-{region}-{lang}`
locations BROKE **Max Mega Menu** (enabled only on `primary` + `footer-main`), making the header
mega menu disappear. Correct approach: theme templates stay on the **base** locations (`primary`,
`header-nav`, `footer-main`, `footer-nav`) so Mega Menu keeps wrapping them, and a
`theme_mod_nav_menu_locations` filter (`wj_remap_menu_locations_for_locale` in
`menu-locations.php`) swaps *which menu each base location resolves to* per current locale
(reusing the per-locale location assignments; no hardcoded IDs). Header/footer restored from
`*.pre-phase5menu` backups. Verified: mega menu intact + per-locale on all locales, footer menus
+ switcher present, no stale-cache.

**Remaining (polish):** FacetWP facet-label localization (headings still English, plugin
provides `wj_get_tax_label()` + `facetwp_facet_display_value`); locale-aware footer/header
ACF option variants (footer HQ, header CTAs, contact — plugin/theme use `lc_pick_locale_field`).

---
### (original plan)
## Phase 5 — Menus + localized frontend

- [ ] Register the per-locale menu locations (blueprint pattern:
      `primary-{r}-{l}`, `header-nav-{r}-{l}`, `footer-main-{r}-{l}`, `footer-nav-{r}-{l}`)
      driven by the `language_codes` option.
- [ ] Create/assign a menu per locale to each location.
- [ ] Populate per-locale ACF Options (footer HQ, header CTAs, contact defaults).
- [ ] Localize taxonomy **term labels** + **section labels** (term meta + Options).
- [ ] Verify FacetWP facets show localized labels via `facetwp_facet_display_value`.

**Checkpoint:** switch locale from every homepage → correct menus, footer, labels.

---

## Phase 6 — Redirects, geo, UTM — 🔄 MOSTLY DONE 2026-07-02

**✅ Old→new URL redirects (301):** verified working via WP canonical + the plugin's
permalink filter + `canonical.php` cascade. All resolve correctly:
- flat en pages `/about-us/` → `/us/en/about-us/`; en CPTs `/products/{slug}/` → `/us/en/products/{slug}/`
- WPML dir prefixes `/es/…`→`/us/es/…`, `/fr/…`→`/ca/fr/…`, `/pl/…`→`/pl/pl/…`
- `/us/en/` → `/`; same-slug collisions (e.g. `industries`) resolve to en-us
- WPML slug-translation was only ON for `testimonial` (tiny), so no widespread translated bases.

**✅ UTM preservation:** query string (`utm_*`, `gclid`, `fbclid`, `utm_campaign`) survives
every 301 (via `canonical.php` `wp_redirect` filter); `utms-carry-pages` active for
client-side carry across internal links.

**Edge cases:** non-existent old slugs (e.g. `/contact/`) hit WP's 404-guess and may land on
the wrong locale — add explicit rules only if such URLs are known-indexed. `/careers/` →
external UltiPro is pre-existing **Page Links To** behavior, not migration.

**⏳ Geo redirects — DEFERRED to production.** Country→locale homepage redirect is a
production-domain concern and the host is **Apache/.htaccess (not nginx/Kinsta)**, so the
blueprint nginx rules need an Apache/mod_geoip (or geo-plugin) reimplementation. Not
meaningfully testable on the staging domain.

---
### (original plan)
## Phase 6 — Redirects, geo, UTM

- [ ] `canonical.php` template_redirect cascade: `/us/en/`→`/`, legacy non-prefixed
      CPT URLs → `/us/en/…`, and the **old WPML slug map** (from Phase 0).
- [ ] **Geo redirects** re-implemented for Apache/.htaccess (blueprint nginx rules are
      a spec, not drop-in). Preserve query string (UTMs) — no trailing `?`.
- [ ] Confirm `utms-carry-pages` carries `utm_*`/`gclid`/`fbclid`/`_gl` across internal links.

**Checkpoint:** VPN test country→locale landing; `?utm_source=test` survives clicks + redirects.

---

## Phase 7 — SEO + final QA

- [ ] hreflang cluster correct per locale; canonical matches current locale URL; x-default → en-us.
- [ ] Sitemap lists per-locale URLs.
- [ ] Form submissions carry UTMs from each locale.
- [ ] Rank Math analytics table collations OK (blueprint pitfall #8).
- [ ] Watch error log 24–48h; then plan the equivalent go-live on production.

---

## Phase 0 RESULTS (assessed 2026-07-02, dev site, read-only)

**Decision:** migration runs **directly on the dev site** (not a staging clone).
Phase 0 backups are therefore the only rollback path — mandatory before Phase 1.

**Environment confirmed present:** ACF Pro, FacetWP, Max Mega Menu, Rank Math (+Pro),
Custom Post Type UI (registers the CPTs), Gravity Forms. **`utms-carry-pages` already
active.** Full WPML stack active: `sitepress-multilingual-cms`, `acfml`,
`wpml-string-translation`, `wpml-media-translation`, `wp-seo-multilingual`,
`gravityforms-multilingual`. Permalinks already `/%postname%/`. `page_on_front=49`.
Custom meta not present yet (0 rows) — clean slate.

### CPT reconciliation (blueprint → dev)
CPTs are **CPT-UI managed** (data, portable), not theme code (except blueprint's `materials`).

| Blueprint scoped CPT | Dev CPT | Note |
|---|---|---|
| `routers` | **`products`** | dev uses `products`, **hierarchical (parent/child)** — see wrinkle below |
| `industry` | `industry` | ✓ |
| `accessories` | `accessories` | ✓ (rewrite `accessories`) |
| `testimonial` | `testimonial` | ✓ (rewrite `testimonials`) |
| `webinar` | `webinar` | ✓ |
| `blog` | `blog` | ✓ |
| `news_and_events` | `news_and_events` | ✓ |
| `video` | `video` | ✓ |
| `software` | — | not on dev |
| `materials` | — | not on dev |
| — | **`series`** | extra on dev (rewrite `series`) |
| `post` | `post` | ✓ |

→ **Dev locale-aware CPT set:** `post, products, series, industry, accessories, testimonial, webinar, blog, news_and_events, video`. Adjust plugin `$scoped` / `base_to_pt` / `$alias_cpts` accordingly (drop routers/software/materials, add products/series).

### Locale map (WPML → target locale) — CONFIRMED: match blueprint, all 6 locales
WPML active languages: **en** (default), **es**, **fr**, **pl**. Blueprint target locales
were `en-us, es-us, en-ca, fr-ca, en-uk, pl-pl`. Proposed mapping (matches blueprint):

| WPML lang | → target locale | prefix | notes |
|---|---|---|---|
| en | `en-us` | `/us/en/` (source, served at `/`) | default |
| es | `es-us` | `/us/es/` | translated |
| fr | `fr-ca` | `/ca/fr/` | translated |
| pl | `pl-pl` | `/pl/pl/` | translated |
| (none) | `en-ca` | `/ca/en/` | **alias** of en-us (no WPML content) |
| (none) | `en-uk` | `/uk/en/` | **alias** of en-us (no WPML content) |

### Translated content is real (WPML counts, en/es/fr/pl)
pages 54/33/34/28 · products 37/29/29/0 · industry 9/9/9/9 · accessories 30/26/26/16 ·
video 165/118/118/0 · webinar 95/0/2/0 · blog 97/0/0/0 · news_and_events 151/3/5/4 ·
testimonial 4/0/1/0 · series 10/10/10/8 · post 81/3/7/4.
Phase-2 migration has ~real trids to derive `translation_group_id` from.

### ⚠️ Technical wrinkles found
1. **`products` is HIERARCHICAL (parent/child).** The blueprint router/permalink logic
   (`routing.php`, `permalinks.php`) assumes **flat** `/cc/ll/{base}/{slug}/` (single
   base + single slug regex). Nested product URLs (`/cc/ll/products/{parent}/{child}/`)
   need the router regex + permalink prefix logic extended to handle multi-segment
   hierarchical paths. **This is the main code adaptation beyond config lists** and ties
   directly to the "parent/child structure" requirement.
2. **WPML element-type typo:** both `post_accessories` (correct) and `post_accesories`
   (misspelled, es 11 / fr 11, no `en`) exist in `icl_translations`. Treat the misspelled
   set as legacy/orphaned during Phase-2 seeding; verify before mapping.

### en-ca / en-uk alias pages (2026-07-03)
Pages (unlike CPTs) can't be router-aliased, so en-ca (`/ca/en/`) and en-uk (`/uk/en/`)
had no resolvable pages. Created (blueprint-style, script `wj-alias-pages.php`, idempotent):
new `uk` container + `ca/en` (12676) & `uk/en` (12726) homepage roots; **50 published en-us
pages copied into each locale** (100 pages) with full content+ACF meta, `region_language_code`
set, `translation_group_id` linked to the en-us original, nested for URL resolution. Copies
tagged `_wj_alias_copy` / `_wj_alias_src` / `_wj_alias_locale` (reversible). Homepage
products/series query maps aliasable→en-us so those homepages show en-us content. All
`/ca/en/…` and `/uk/en/…` URLs now 200.

### en-ca / en-uk CPT copies (2026-07-03)
Matching the blueprint (which copied routers/industry/accessories/materials), created en-ca +
en-uk copies of published en-us **products (36), industry (9), accessories (21)** = 132 CPT
posts (script `wj-alias-cpts.php`, idempotent). CPTs are flat (no nesting) — forced SAME slug
as en-us (router resolves by translation_group_id, not slug uniqueness); copied all meta +
taxonomy terms; linked `translation_group_id`; tagged `_wj_alias_copy`. Result: `/ca/en/…` &
`/uk/en/…` singles 200, hreflang now lists en-CA/en-GB, grids show locale copies with in-locale
links. NOT copied: blog/news/webinar/testimonial/video/series (those alias via router / grid
fallback). Reversible via `_wj_alias_copy`.

### Phase 1a progress (2026-07-02) — plugin CPT adaptation done locally (repo)
- CPT lists swapped in 4 files: `permalinks.php` (`$scoped`), `routing.php`
  (`base_to_pt`), `menu.php` (`$post_types_to_filter`, `$managed_cpts`, `$cpt_bases`),
  `canonical.php` (`$cpt_slugs`). `routers/software/materials` → `products/series`.
- **`products`/`series` are FLAT** in practice (all 94 products `post_parent=0`;
  permalinks `/products/{slug}/`) → blueprint flat router works, **no hierarchical code**.
- **"Features"/"software" are not CPTs.** "Features" is a custom `#` menu parent;
  its items (Pumps, …) are custom links to **`accessories`** posts / pages. "Software"
  content also lives under **`accessories`**. Nothing to add.
- **Deferred to Phase 6:** WPML legacy slug map in `canonical.php` still lists axyz's
  old slugs (`systemes-de-routeurs`→routers, `logiciel`→software, …). Inert on wardjet;
  rebuild from wardjet's real old WPML slugs later (old "software" slugs → `accessories`).
- Locale/SEO/term-label config left **identical to blueprint** (per confirmed decisions).

### CONFIRMED decisions (2026-07-02)
- **Locale mapping = exact blueprint:** en→en-us (`/`), es→es-us (`/us/es/`),
  fr→fr-ca (`/ca/fr/`), pl→pl-pl (`/pl/pl/`); **all 6 locales**, en-ca (`/ca/en/`)
  & en-uk (`/uk/en/`) as en-us aliases. → plugin locale config stays **identical to blueprint**.
- **Migrate directly on dev** (no staging clone) → Phase 0 backup is the only rollback.
- **Only plugin changes:** CPT lists + hierarchical-products routing. Locale/SEO lists unchanged.

## Open items still to confirm
1. Nested URL shape for hierarchical `products` (assume `/cc/ll/products/{parent}/{child}/` unless told otherwise).
2. Geo-redirect mechanism on Apache (which countries → which locale) — deferred to Phase 6.
