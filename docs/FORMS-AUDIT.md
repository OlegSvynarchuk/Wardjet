# WardJet — Forms Audit (localization scope)

**Date:** 2026-07-09
**Rule (per client):** JotForm embeds → **leave as-is, do not touch/remove**. English
**Gravity Forms** → localize (create translated copies, serve on the localized page).
**Blueprint (axyz) is the 100% guide** for how localization is wired.

---

## 1. Form engines on the site

| Engine | Status | Localization |
|---|---|---|
| **JotForm** (embedded by URL) | Widely used | **Do not touch.** Only "localized" if a translated page embeds a different form. |
| **Gravity Forms** | 5 English forms exist; also referenced by page templates | **In scope** — localize via translated GF copies mapped per locale. |
| Gravity `[gravityform]` shortcodes in content | **None** (0) | n/a |

---

## 2. JotForm inventory — LEAVE ALONE

- **Contact section** (`template-parts/agg-contact.php`, ACF `contact_form`): ~340 page rows,
  ~10 distinct `jotform.com/jsform/{ID}` forms. Currently the live form on the homepage +
  many pages.
- **Content-embedded** JotForms (`post_content`): 29 pages, 12 news_and_events, 1 video,
  1 webinar (+ 5 oembed caches).
- **ACF/widget blocks**: `content_blocks_0_content` (23), `widgets_0_content` (11).
- **`contact_id` meta** (21): also `jotform.com/jsform/{ID}` — embed-form plugin / contact widget.

→ None of these are changed. If a specific JotForm needs a translated variant, that's done
inside JotForm and embedded on the translated page — not our concern for the GF localization.

---

## 3. Gravity Forms inventory — TO LOCALIZE

**English forms on wardjet (Gravity Forms):**

| GF ID | Title | Used by |
|---|---|---|
| 1 | Get in Touch | contact section `agg-contact` (when `contact_form_type=gravity`) |
| 2 | Careers | `wardjet-careers.php` — **hardcoded** `gravity_form(2)` |
| 3 | Contact Us | `wardjet-contact.php` — **hardcoded** `gravity_form(3)` |
| 4 | Support | `wardjet-support.php` — `gravity_form(get_field('form_id'))` (per-page ACF) |
| 5 | Get A Quote | header "Get a Quote" CTA target |

`functions.php` adds geo-info on `gform_pre_submission_1` and `_3` (forms 1 & 3 are the lead forms).

**Translated versions on wardjet:** none yet (only the 5 English forms). On axyz the
translated copies existed: **en=1, es=11, fr=13, pl=12**.

---

## 4. Blueprint localization mechanism (the guide)

The contact section is localized via the **`contact_localized` ACF Options repeater** (same
options page as the footer options). Each locale row holds:
- `region_language_code` (e.g. `es-us`)
- `contact_form_type` = `gravity` | `jotform`
- `contact_gravity_form_id` = the **translated** Gravity Form ID for that locale
- `contact_form` = JotForm URL (fallback / when type = jotform)
- plus the info-column fields (location card, global presence, Email/Call CTAs)

`agg-contact.php` reads the current locale's row: if `type=gravity` → `gravity_form(id)`,
else embeds the JotForm URL. On axyz the rows were `type=jotform` (translated GFs wired but
inactive). Standalone page templates (`wardjet-contact`, `wardjet-careers`) use **hardcoded**
GF IDs → not per-locale; `wardjet-support` uses a **per-page** `form_id` ACF → localizable
by setting the translated form on each locale's page.

---

## 5. Gravity Forms localization — IMPLEMENTED (2026-07-09)

Approach mirrors axyz: **per-page `form_id` ACF** picks a translated GF copy on each locale
page; English (en-us/en-ca/en-uk) keeps the base form.

**Translated GF copies created** (duplicated from the EN form, labels/choices/button/
confirmation translated FR/ES/PL):

| Base | FR | ES | PL |
|---|---|---|---|
| #2 Careers | #9 | #10 | #11 |
| #4 Support | #15 | #16 | #17 |
| #5 Get A Quote | #18 | #19 | — (no PL page) |

**Per-page wiring (`form_id`):**
- **Careers** (`wardjet-careers.php` — changed from hardcoded `gravity_form(2)` to
  `get_field('form_id') ?: 2`, privacy-label JS selector made dynamic): 98/12719/12769 en=#2,
  1487 fr=#9, 1651 es=#10, 7121 pl=#11. Verified render: en=gform_2, fr=9, es=10, pl=11.
- **Support** (`wardjet-support.php` — already reads `form_id`): 94/12720/12770 en=#4,
  1525 fr=#15, 1708 es=#16, 7345 pl=#17. Verified: fr=15, es=16, pl=17.
- **Get A Quote "1"** (support template): 1502 fr=#18, 1504 es=#19. Verified.

**NOT converted (JotForm — left as-is):**
- **Get-a-Quote pages** (`wardjet-contact.php`): the `gravity_form(3)` is **commented out**;
  the page renders a **per-locale JotForm** via `get_field('contact_id')` (en 241374505838259,
  fr 231715785529163, es 231716305713147, pl 231716323913149). GF #3 is unused → not touched.
  (An earlier attempt to add Contact Us GF copies #12/#13/#14 here was reverted.)
- **Contact section** (`agg-contact`, home + page bottoms): per-locale JotForm — untouched.
- **#1 Get in Touch**: not embedded on any page (historical entries only).

`wardjet-text.php` promo pages carry a vestigial `form_id` meta but the template renders no
GF — left as original.
