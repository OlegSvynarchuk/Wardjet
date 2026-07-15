# Single-Product Template — Implementation Plan / Handoff

**Goal:** finish the single-product (Series) page so it matches the Figma "Product Page" (node `7:2622`), reusing the existing flexible-content blocks (blueprint-faithful). The template + blocks already exist; remaining work is mostly **CSS fidelity per section** and **per-locale content/QA**.

---

## Environment / access

- **Repo:** `wardjet/themes/wardjet` (this repo). Remote: `github.com/OlegSvynarchuk/Wardjet`, branch `main`.
- **Dev server (source of truth for DB + some files):**
  - `ssh -p 22222 pixelspi@162.55.0.170` (key in ssh-agent; site `wardjet.pixels2pixels.ch`)
  - Site root: `/home/pixelspi/public_html/wardjet.pixels2pixels.ch/`
  - Theme: `wp-content/themes/wardjet/`
  - WP-CLI: prefix commands with `HTTP_HOST=wardjet.pixels2pixels.ch wp ...`. `wp db import/export` fails (proc_open disabled) → use `mysql` directly. Run PHP data changes via `wp eval-file file.php`.
- **Blueprint (axyz, design/structure reference):** `C:\Users\djord\Downloads\axyzlive` (+ `axyzsite.sql`) — **this is on the original machine only.** If not present on the new machine, the single-product work can still proceed (template is already ported); the blueprint is needed only for cross-checking styling/data.
- **Figma:** file key `fxxMEEhuCXWpJci6VAkXki`, single-product node `7:2622`. Token saved in the `wardjet-figma-design` memory. Use the Figma MCP tools (`get_metadata` on `7:2622` for section node IDs, then `get_design_context` per section for specs).

### Deploy workflow (IMPORTANT lesson learned)
- Normal: edit a file in the repo → `scp` it to the server theme path → `php -l` to lint.
- **For shared files (`functions.php` especially): the server can be AHEAD of the repo** (earlier sessions edited it directly). **Pull the live `functions.php` first, diff, then redeploy** — do not overwrite it blindly (doing so once dropped `acf-contact-extra` + contact/footer CSS enqueues and broke the contact card + footer). Server backups exist as `functions.php.bak-*`.
- **Per-locale content lives in the DB, not git.** Change it with small `wp eval-file` scripts (`update_field(...)`).

---

## Current state (DONE)

- **Template:** `single-series.php` = flexible-content builder. Loops the `content` ACF field and renders each block via `get_template_part('template-parts/section-{acf_fc_layout}')`, then `agg-contact`. **Promo-video hero (`hero-video-carousel`) was added to the top** (commit `8dfc390`).
- **CPT:** `series` (a.k.a. products). Single URLs: `/{cc}/{ll}/series/{slug}/` (e.g. `/us/en/series/x-series/`).
- **Placeholder hero video** (attachments `12911` desktop / `12912` mobile, same as homepage) set on **all 28 series posts**. Real promo video TBD.
- Verified rendering live on `/us/en/series/x-series/`: hero-video-carousel + main-banner + 6× feature-block + features-group + selected-products, etc.
- Block CSS lives in `inc/assets/css/parts/*.css` + `wardjet-custom.css` (global). Section parts are in `template-parts/section-*.php`.

### Figma section → page-editor block map (confirmed with client)

| Figma section | Block (`acf_fc_layout`) → `section-*.php` |
|---|---|
| Promo Video hero | `hero-video-carousel` (added to template, not a `content` block) |
| Product title + image carousel | `main_banner` → `section-main_banner.php` |
| Alternating image/title/description rows | `feature_block` → `section-feature_block.php` (`section_style` left/`right_image`) |
| 3-step numbered band (title+desc) | `features_group` → `section-features_group.php` |
| "See in Action" video | `videos` → `section-videos.php` |
| Model comparison cards (specs) | `selected_products` → `section-selected_products.php` |
| Features & Benefits (stacked cards) | `competitive_chart` → `section-competitive_chart.php` |
| Image gallery carousel (dots) | `series_slider` → `section-series_slider.php` |
| Perform Better + Download Brochure | `brochure_section` → `section-brochure_section.php` |
| Get in Touch | `agg-contact` (in template) |

A representative product to test against: **x-series** (`/us/en/series/x-series/`), blocks:
`main_banner, feature_block, feature_block, crossbeam_section, videos, selected_products, competitive_chart, series_slider, features_group, brochure_section`.

---

## UPDATE 2026-07-15 — blueprint structure ported (commit `634e58b`)

Figma node confirmed as **`18:5482`** ("Single Product_X-Series", file modified 2026-07-14) — supersedes `7:2622`.
Verified via Figma REST API (MCP connector was down; used token + `api.figma.com/v1/files/{key}/nodes`).
**Icon-strip (`AXYZQuickInfo`) is `visible:false` in the design, and `AXYZIndustries` is just the
image-gallery component (no text) = our `series_slider`** — so **no icon strip, no industries** on this page.

**Ported from blueprint → wardjet (`series`):**
- `single-series.php` rewritten to blueprint `single-routers.php` logic: video hero (w/ main_banner image
  fallback) → `router-renders-carousel` (renders title+subtitle, **`main_banner` block skipped**) →
  blocks (features strip injected after feature rows) → `agg-contact`. **Sub-routers omitted** (series are
  flat: 28 posts, 0 with parent; not in Figma).
- Added `router-renders-carousel.php`, `router-features-strip.php` (no `routers` coupling — portable as-is).
- Added `acf-router-renders.php` (**retargeted `routers` → `series`**, labels de-routered) and
  `acf-product-specs.php` (already targets `products` — powers the spec cards).
- Added 7 missing section CSS parts + enqueued them; added the two ACF requires.
  NOTE: live `functions.php` diffed identical to repo (only CRLF vs LF) — server was NOT ahead; deployed LF-normalised.

**Live result** `/us/en/series/x-series/` renders in order: hero-video-carousel → router-renders →
feature-block ×2 → router-features-strip → crossbeam → videos → selected-products → competitve-chart →
series-slider → features-group → brochure-section → contact-section. All 4 content locales 200 (es/fr/pl);
en-ca/en-uk alias to en-us.

**Promo video:** blueprint's `AXYZ_Web_Headers-3-Innovator.mp4` uploaded as
`/uploads/2026/07/product-promo-innovator.mp4` (attachment **13252**) and set on **all 28 series**
(`video_carousel_0_video_file` + `_mobile`). Old `hero-temp.mp4` no longer referenced.

### Still open
1. **`router_renders` is EMPTY** on series -> carousel falls back to the single `series_image` (no dots).
   Figma section 3 is a multi-image carousel -> needs render images per series (content).
2. **Block order != Figma**: Figma puts the 3-step band (`features_group`) right after the feature rows;
   on x-series it's position 8 in the ACF `content` field. Data reorder, not template.
3. **PHP notices**: `section-competitive_chart.php:68` (`popups`), `section-series_slider.php:15`
   (`series_slider`), `section-brochure_section.php:6` (`section_style`) — unpopulated subfields; guard or fill.
4. CSS fine-tuning vs Figma; optional en-ca/en-uk `series` copies (aliasing already works).

## Remaining work (TOMORROW)

1. **CSS fidelity, section by section vs Figma** (main task). For each block above:
   - `get_metadata` on Figma `7:2622` → get the child node id for the section → `get_design_context` for exact specs (typography, colours, radii, spacing).
   - Compare against the live render and adjust CSS in the relevant `parts/*.css` (or `wardjet-custom.css`). Known Figma cues: rounded image corners (~16px), navy Montserrat headings, blue gradient band on the 3-step, card shadows, dot indicators.
   - Enqueue any missing per-section CSS in `functions.php` (remember the shared-file caveat).
2. **Promo-video hero** — swap the placeholder for the real product/promo video when available (or per-series video). Field: `video_carousel` repeater on the series post (`video_file`, `video_file_mobile`, `video_title`, `video_poster`).
3. **Per-locale QA** — confirm es/fr/pl series pages have localized `content` blocks + `agg-contact` copy; the products fly-out already excludes L-Series.
4. **Verify** each locale renders (HTTP 200) and no CNC-router leftovers (this build is a waterjet site — de-CNC any stray copy).

## Handy commands

```bash
# which blocks a series uses
HTTP_HOST=wardjet.pixels2pixels.ch wp eval '$c=get_field("content",385); echo implode(",",array_map(fn($b)=>$b["acf_fc_layout"],$c));'
# lint after deploy
php -l wp-content/themes/wardjet/single-series.php
# set placeholder video on all series (already done; re-run if needed) — see scratchpad set-series-video.php
```

## Recent commits (pushed to origin/main)
`ff87ea4` fly-out + industries styling · `8dfc390` single-product hero video · `e95e7e7` icon-strip de-CNC · `10775a7` Products page composition · `9a0bf8d`/`16dc67f`/`6208ec9` Locations + contact/footer restore · `417018f` fly-out ACF rebuild.
