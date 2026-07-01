# Feature Plan: Locale Filtering & Management for News, Blogs, Webinars, Testimonials

## Overview
Enable content managers to create and manage localized content for news, blogs, webinars, and testimonials across all 6 locales (en-us, en-ca, en-uk, es-us, fr-ca, pl-pl). Includes localized category filtering, archive pages, and news ticker integration.

## Current State
- Blog/news/webinar/testimonial content exists only in English
- Categories are not localized
- WP FacetWP is used for filtering but doesn't integrate well with custom locale system
- News ticker shows only English content across all locales

## Approach
Replace FacetWP filtering logic with custom AJAX filtering while keeping the current UI look. Full control over locale-aware queries with no third-party dependency issues.

---

## Tasks

### 1. Taxonomy Localization (4-6 hours)
- Add `region_language_code` ACF field to category/taxonomy terms
- Create translated category terms for each locale (mirror of English categories)
- Build admin UI for content managers to manage localized terms
- Ensure backward compatibility with existing English categories

### 2. CPT Templates with Locale Filtering (6-8 hours)
- Add `region_language_code` ACF meta field to blog, news, webinar, testimonial posts
- Update archive templates to query by locale with en-us fallback (same pattern as routers/industries)
- Update single post templates if needed
- Ensure pagination works with locale filtering

### 3. Custom AJAX Filter — Replacing FacetWP (10-14 hours)
- Build custom filter UI matching current FacetWP look (dropdowns/buttons for categories)
- Create AJAX endpoint using `WP_Query` with `meta_query` (locale) + `tax_query` (category)
- Implement result rendering via AJAX (load more / pagination)
- Handle URL state (query params for shareable filtered URLs)
- Remove FacetWP dependency from these specific templates
- Keep FacetWP active for any other pages that still use it

### 4. News Ticker Locale-Aware (2-3 hours)
- Modify `wj_get_ticker_items()` to filter by `region_language_code` matching current URL locale
- Add ACF options field for content manager to control which categories/posts show per locale
- Fallback to en-us content if no localized ticker items exist

### 5. Content Manager Workflow & Testing (4-6 hours)
- Document workflow for creating localized content
- Test all locales end-to-end (create post, assign locale, assign localized category, verify filtering)
- Test ticker across all locale homepages
- Test archive pages with filtering
- Cross-browser and responsive testing

---

## Time Estimate

| Task | Hours |
|------|-------|
| Taxonomy localization + ACF fields | 4-6 |
| CPT templates with locale filtering | 6-8 |
| Custom AJAX filter (replacing FacetWP) | 10-14 |
| News ticker locale-aware | 2-3 |
| Content manager workflow + testing | 4-6 |
| **Total** | **26-37** |

---

## Technical Notes
- FacetWP custom hooks (`facetwp_indexer_row_data`, etc.) were tested previously and did not work reliably with the custom locale system
- Custom AJAX filter gives full control and avoids third-party dependency
- Same locale pattern used across the site: `region_language_code` meta field + URL-based locale detection + en-us fallback
- Categories assigned per locale — content manager selects locale and localized category when creating content

## Risks
- Migration of existing English content to include `region_language_code` meta
- Ensuring FacetWP removal doesn't affect other pages that still use it
- SEO: localized archive URLs need proper canonical/hreflang tags

---

*Created: 2026-04-01*
*Project: AXYZ Website Redesign*
