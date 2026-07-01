# Wardjet

Reference material for replicating the axyz.com custom multilingual system on **wardjet.com** (WordPress). The goal: mimic the axyz setup — remove WPML, drop in the custom plugin, and apply the supporting config — as quickly as possible.

## Contents

### `docs/`
- **AXYZ-MULTILINGUAL-IMPLEMENTATION-GUIDE.md** — the full summary. Architecture (portable vs site-specific), plugin structure, URL/router model, language switcher, canonical redirects, SEO/hreflang, nginx geo rules, and a step-by-step wardjet implementation checklist + rollout pitfalls.
- **nginx-geo-rules-WORKING.txt** — working Kinsta nginx geo-redirect rules (country → locale homepage, `src=switch` bypass, bare→www).
- **feature-plan-localized-content.md** — feature plan / task list for locale-aware content filtering (news, blogs, webinars, testimonials).

### `plugins/`
- **wj-multilingual/** — the custom plugin that replaces WPML (router, permalinks, language switcher, canonical redirects, term/tax labels, hreflang/SEO). One include per responsibility.
- **utms-carry-pages/** — carries UTM / gclid / fbclid params across internal links so attribution survives locale redirects.

## ⚠️ Source of truth

The up-to-date plugin code lives on the **remote (Kinsta) server**. The copies here were staged from a local mirror and may be slightly behind remote. Reconcile against remote before relying on exact line counts / CPT lists.
