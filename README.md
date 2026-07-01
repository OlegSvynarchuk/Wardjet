# Wardjet

Reference kit for replicating the axyz.com custom multilingual system on **wardjet.com** (WordPress). The goal: mimic the axyz setup — remove WPML, drop in the custom plugin, and apply the supporting config — as quickly as possible.

## Start here

1. Read **`docs/AXYZ-MULTILINGUAL-IMPLEMENTATION-GUIDE.md`** — the full summary of how the axyz system works and how to port it. Its §0 lists exactly what is *portable as-is* vs what must be *redefined per site*, and §17 is a step-by-step wardjet implementation checklist.
2. Install the two plugins from `plugins/` (see below) into `wp-content/plugins/` and activate.
3. Apply the nginx geo rules (`docs/nginx-geo-rules-WORKING.txt`) on the host.
4. Add the WPML slug redirects (`docs/old-wpml-slug-redirects.md`) when migrating off WPML.

## Contents

### `docs/`
- **AXYZ-MULTILINGUAL-IMPLEMENTATION-GUIDE.md** — the master summary: architecture (portable vs site-specific), plugin structure, URL/router model, language switcher, canonical redirects, SEO/hreflang, nginx geo rules, wardjet checklist + rollout pitfalls.
- **nginx-geo-rules-WORKING.txt** — working Kinsta nginx geo-redirect rules (country → locale homepage, `src=switch` bypass, bare→www).
- **old-wpml-slug-redirects.md** — map of old WPML-era translated CPT slugs → new English slugs, for the WPML-removal migration.
- **feature-plan-localized-content.md** — feature plan / task list for locale-aware content filtering (news, blogs, webinars, testimonials).
- **quote-localized-content-management.md** — scope-of-work quote for the localized content management system.

### `plugins/`
- **wj-multilingual/** — the custom plugin that replaces WPML (router, permalinks, language switcher, canonical redirects, term/tax labels, hreflang/SEO). One include per responsibility.
- **utms-carry-pages/** — carries UTM / gclid / fbclid params across internal links so attribution survives locale redirects.

## Environment map

| Location | Role |
|---|---|
| **axyz (Kinsta remote)** | Source of truth for the original `wj-multilingual` plugin + config being copied from |
| **Local `wardjet` site** (Local by Flywheel) | Where the wardjet build/testing happens |
| **wardjet.pixels2pixels.ch** (dev server, `public_html/`) | Deploy/staging target |
| **This GitHub repo** | Reference kit / transfer mechanism between them |

## ⚠️ Source of truth

The up-to-date plugin code lives on the **live axyz server**. A fresh live axyz backup is being restored locally to serve as the current baseline for wardjet; once that's in place, the `plugins/` here should be reconciled against it before relying on exact line counts / CPT lists.
