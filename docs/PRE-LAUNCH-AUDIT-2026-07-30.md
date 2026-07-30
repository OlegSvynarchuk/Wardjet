# Pre-Launch Audit — Multilingual Migration (2026-07-30)

Pre-deployment checkup of the WPML→`wj-multilingual` migration against the 7-phase
[WARDJET-MIGRATION-PLAN.md](WARDJET-MIGRATION-PLAN.md). Two axes: **Completeness**
(did we build the plan?) and **Correctness** (does it work, no bugs?).

Method — 4 passes, broad→narrow:
1. **Automated structural crawl** (done) — 93 URLs, 6 locales × surfaces; HTTP, canonical, hreflang, `<html lang>`, switcher, menu-leakage.
2. **Data integrity** (done) — locale meta counts, untagged posts, dup-locale groups, frontpage flags, orphaned groups.
3. **Manual/visual QA** (pending) — all 6 locales, per §Pass 3.
4. **Launch gates** (pending) — tracking, geo, security, sitemap, cache.

Reusable harness: [audit/](audit/) (`audit-enum.php`, `audit-crawl.sh`, `audit-data.php`) — re-run on the production domain at go-live.

---

## Verdict

**Structurally sound. No launch-blocking bugs found in Pass 1–2.** 93/93 URLs serve
HTTP 200 across all six locales; routing, hreflang, switcher, per-locale menus and
canonical (on indexable pages) all work. Two **medium** issues to fix and a short
verify-list below; the remaining risk is in the not-yet-run manual/launch passes.

---

## Pass 1 — Structural crawl (93 URLs)

| Signal | Result |
|---|---|
| HTTP 200 | **93 / 93** — zero 404s/500s across every locale, CPT single, archive, key page |
| hreflang cluster | Present on all; count tracks real translation coverage (7 where fully translated, fewer where a CPT has fewer locales — expected) |
| Language switcher | **Present on all** (links to every other locale prefix ×3 = header/footer/mobile) |
| Header nav localized | **Y on all** — es "Productos", fr "produits", pl "Produkty" present; no English leakage in nav |
| Canonical (indexable) | Self-referential and locale-correct |

Surfaces covered per locale: homepage, products page, about, industries, news/blog/
webinar/testimonial archives, and one single each of products, series, industry,
accessories, testimonial, webinar, blog, news_and_events, video.

## Pass 2 — Data integrity

| Check | Result |
|---|---|
| `region_language_code` counts (type×locale) | Match WPML reality; **0 untagged** published CPT posts |
| Duplicate-locale in a translation group | **None** — every group ≤1 post per locale ✓ |
| `is_frontpage` flags | **Exactly 6** — one per locale ✓ |
| Untagged published pages | 4 = region containers (us/ca/pl/uk) — structural, expected |
| Groups missing en-us anchor | **15** — see DATA-1 |

---

## Bug / action list (triaged)

| # | Sev | Finding | Detail | Action |
|---|---|---|---|---|
| **BUG-1** | Med | `<html lang>` is `en-US` on **every** locale | `header.php` uses `language_attributes()`; plugin has no `locale` filter, so WP emits the site language everywhere. Inherited from blueprint. hreflang is unaffected/correct. | Add a `locale` filter in the plugin keyed off `lc_get_locale_from_url()` → emit `es-ES`/`fr-CA`/`pl-PL`/`en-*`. a11y + lang-signal. |
| **DATA-1** | Med | 15 translation groups have **no en-us member** | 7 locale-only (Polish-only news/webinars; genuinely no English source) + 8 es/fr(+pl) 2021–22 sets (video/section content) whose English original may sit under a **different** `translation_group_id` (WPML trid seeding mismatch). Impact: hreflang + switcher on those pages can't reach English. | For the es/fr sets, check if an en-us original exists mis-grouped; re-link `translation_group_id`. Leave true locale-only content as-is. |
| VERIFY-1 | Low | Some products are Rank Math `noindex` (z-2543, z-2043, gx-4816…) | Correctly emit no canonical (not a bug). | Confirm these prototype products are *meant* to be noindex. |
| VERIFY-2 | Low | Region container pages `/us/ /ca/ /pl/ /uk/` are published | Thin/empty structural pages. | Confirm they're noindex / not in sitemap. |

**Cleared during the audit (NOT bugs):** switcher "not found" was a crawler
false-negative (switcher works); "missing product canonical" is intentional noindex;
4 "untagged pages" are the region containers.

---

## Completeness reconciliation (plan phases)

| Phase | Scope | Status |
|---|---|---|
| 0 Safety/assessment | backups, CPT/locale map | ✅ Done |
| 1 Code inert | plugin + ACF locale fields | ✅ Done |
| 2 Meta seed | region/group/frontpage from WPML | ✅ Done — verified counts + 6 frontpages |
| 3 Page/URL structure | `/cc/ll/` parent-child | ✅ Done — all page URLs 200 |
| 4 Cutover routing | WPML off, plugin on | ✅ Done — all singles/archives 200 |
| 5 Menus + localized frontend | per-locale menus, term/section labels, ACF options, FacetWP labels | ✅ Done — incl. facet option + heading localization |
| 6 Redirects / geo / UTM | 301s + UTM done; **geo deferred** | 🟡 Partial — geo = production task (Apache/.htaccess) |
| 7 SEO + final QA | hreflang/canonical/sitemap; error-log watch | 🟡 In progress — this audit is part of it |

Layered content work (news/blog/webinar/testimonial localization, single-product pages,
industries, images, hero videos, grids) — built; regression-covered by Pass 1 (all 200).

---

## Pass 3 — Manual/visual QA (pending, all 6 locales)

Per-locale, click-through (scripts can't see these):
- [ ] Switcher **round-trip**: from a translated single, switch locale → lands on the *same* content's translation (or correct prefix-swap for aliased/untranslated).
- [ ] Header **mega-menu** + footer menus render the right locale, all links in-locale.
- [ ] FacetWP: **option labels + section headings** localized; filtering returns in-locale results; archive **dedup** (en fallback hidden when a translation exists).
- [ ] Content actually translated vs silently English-fallback (spot bogus mixed pages).
- [ ] **Forms** submit on each locale and carry UTMs to the CRM.
- [ ] Media: hero videos autoplay (desktop+mobile source), series renders, grids.
- [ ] The 15 DATA-1 clusters: confirm switcher/hreflang behavior is acceptable.

## Pass 4 — Launch gates (pending)

- [ ] **Tracking domain** — add the real production host to `wj_tracking_allowed()` (see [tracking-launch-checklist.md](tracking-launch-checklist.md)); verify real GTM/GA4/Pixel/Ads IDs.
- [ ] **Geo redirects** — implement country→locale on Apache/.htaccess (blueprint nginx = spec only); preserve query string.
- [ ] **News-ticker REST locale-permalink** — port per [pending-migrations.md](pending-migrations.md) now that the ticker exists (verify ticker links carry locale prefix).
- [ ] **Sitemap/robots** — per-locale URLs listed; region containers + noindex products excluded.
- [ ] **Security** — decide on `engineer1hte` admin user + File Manager Advanced plugin (per migration memory).
- [ ] **Fix BUG-1 + DATA-1**, then re-run [audit/](audit/) on production and watch error log 24–48h.

---

## Evidence
Raw crawl + data outputs captured this run; re-generate anytime with the [audit/](audit/) harness against any host.
