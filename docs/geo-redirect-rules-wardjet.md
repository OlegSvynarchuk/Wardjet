# WardJet — Geo-Redirect Rules (spec for Kinsta support)

Country/city → locale-homepage redirects for **production** (`www.wardjet.com`).
The dev host is cPanel/Apache with no GeoIP, so this is **not testable on dev** — it's
implemented on Kinsta (nginx + GeoIP) via **MyKinsta → Tools → Nginx Configuration**,
or by sending the rules block below to Kinsta support.

Adapted from the working AXYZ blueprint ([nginx-geo-rules-WORKING.txt](nginx-geo-rules-WORKING.txt)),
re-mapped to WardJet's 6 locales, with the blueprint's UTM-stripping bug removed.

---

## Principles (must hold)

1. **Homepage only.** Redirect fires *only* on `/` (site root). Deep links (`/products/…`,
   campaign landing pages) are **never** geo-redirected.
2. **302 (temporary), never 301.** Geo choice is per-visitor; a 301 would be cached by
   browsers/CDNs and stick regardless of location. Use nginx `redirect`, not `permanent`.
3. **Preserve the query string.** No trailing `?` on any target (nginx appends `$args`
   by default). This keeps `utm_*`, `gclid`, `fbclid` intact for ad attribution — the
   blueprint's FR rule had a trailing `?` that stripped them; **removed here.**
4. **Respect an explicit language choice.** If the visitor picked a locale from the
   switcher, do not bounce them. Requires the switcher to append `?src=switch` (see
   Prerequisite below). Requests carrying `src=switch` bypass all geo rules.
5. **US stays at `/`.** US visitors get the default en-us home; no redirect.
6. **Canonical www.** Bare `wardjet.com` → `www.wardjet.com`, path-preserving (301 is fine here).

---

## Country / city → locale map

| Visitor geo | Locale | Redirect target |
|---|---|---|
| **Quebec** — Montreal, Quebec City, Laval (city-level, evaluated first) | fr-ca | `/ca/fr` |
| **France, Belgium, Switzerland** | fr-ca | `/ca/fr` |
| **Poland** | pl-pl | `/pl/pl` |
| **Spanish-speaking** — ES, MX, GT, HN, SV, NI, CR, PA, CU, DO, CO, VE, EC, PE, BO, PY, CL, AR, UY, GQ | es-us | `/us/es` |
| **United Kingdom** (GB) | en-uk | `/uk/en` |
| **Canada** — non-Quebec (evaluated after the city rules) | en-ca | `/ca/en` |
| **United States** (US) | en-us | *(no redirect — stays at `/`)* |
| **Rest of world** | *DECISION — see open items* | `/` (en-us) *recommended*, or `/uk/en` (blueprint) |

Order matters: **Quebec cities → French EU → Poland → Spanish → UK → generic Canada.**
Quebec must precede generic Canada so Québécois land on French, not `/ca/en`.

---

## Nginx rules (draft to hand to Kinsta)

```nginx
# ---- WardJet geo redirects (production, www.wardjet.com) ----
set $is_bypass 0;

# Canonical www (path-preserving)
if ($host = 'wardjet.com') { return 301 $scheme://www.wardjet.com$request_uri; }

# Quebec city-level (MUST precede generic Canada)
if ($geoip_city ~* "montreal|montréal")            { set $quebec_fr 1; }
if ($geoip_city ~* "quebec|québec|quebec city")    { set $quebec_fr 1; }
if ($geoip_city ~* "laval")                        { set $quebec_fr 1; }

# Explicit-choice bypass (switcher appends ?src=switch)
if ($request_uri ~* "src=switch") { set $quebec_fr 0; set $is_bypass 1; }

set $dl 0;
if ($is_bypass = 0) { set $dl "${host}-${geoip_city_country_code}"; }

# Quebec -> ca/fr
if ($quebec_fr = 1) { rewrite "^/?$" https://www.wardjet.com/ca/fr redirect; }

# French-speaking Europe -> ca/fr
if ($dl = "www.wardjet.com-FR") { rewrite "^/?$" https://www.wardjet.com/ca/fr redirect; }
if ($dl = "www.wardjet.com-BE") { rewrite "^/?$" https://www.wardjet.com/ca/fr redirect; }
if ($dl = "www.wardjet.com-CH") { rewrite "^/?$" https://www.wardjet.com/ca/fr redirect; }

# Poland -> pl/pl
if ($dl = "www.wardjet.com-PL") { rewrite "^/?$" https://www.wardjet.com/pl/pl redirect; }

# Spanish-speaking -> us/es
set $es_countries "ES|MX|GT|HN|SV|NI|CR|PA|CU|DO|CO|VE|EC|PE|BO|PY|CL|AR|UY|GQ";
if ($geoip_country_code ~* $es_countries) { rewrite "^/?$" https://www.wardjet.com/us/es redirect; }

# UK -> uk/en
if ($dl = "www.wardjet.com-GB") { rewrite "^/?$" https://www.wardjet.com/uk/en redirect; }

# Canada generic (AFTER Quebec) -> ca/en
if ($dl = "www.wardjet.com-CA") { rewrite "^/?$" https://www.wardjet.com/ca/en redirect; }

# US -> no redirect (default en-us at /)

# ---- Rest of world (OPTIONAL — enable only if ROW should not see en-us) ----
# set $known "US|AR|BE|BO|CA|CH|CL|CO|CR|CU|DO|EC|ES|FR|GB|GQ|GT|HN|MX|NI|PA|PE|PL|PY|SV|UY|VE";
# set $existingCountry 0;
# if ($geoip_country_code ~* $known) { set $existingCountry 1; }
# set $isHome 0;
# if ($request_uri ~* "^/$") { set $isHome 1; }
# set $redirectable "${existingCountry}${isHome}";
# if ($is_bypass = 1) { set $redirectable "no"; }
# if ($redirectable = "01") { rewrite "^/$" https://www.wardjet.com/uk/en redirect; }
```

> Kinsta uses **legacy GeoIP** (`$geoip_city`, `$geoip_country_code`,
> `$geoip_city_country_code`) — same variables the blueprint used. Confirm the module is
> enabled on the WardJet plan; if they've moved to **GeoIP2**, the variable names change
> (e.g. `$geoip2_data_country_code`) and the rules need a syntax swap — ask Kinsta which.

---

## ⚠️ Prerequisite (code change before geo goes live)

The switcher must mark explicit choices so geo doesn't bounce them. WardJet's switcher
currently does **not** emit `src=switch` (verified in `wj-multilingual/includes/menu.php`).
Without it: a US-English visitor in France clicks "US – English" → lands on `/` → geo
rule immediately sends them back to `/ca/fr` = **loop**.

Fix options (pick one before launch):
- **A. Append `?src=switch`** to every switcher link (small edit in the switcher builder).
- **B. Cookie bypass** — set a `wj_locale_choice` cookie on switch; add
  `if ($cookie_wj_locale_choice) { set $is_bypass 1; }` to the nginx block. Survives
  navigation better than a query param, but needs the cookie set client-side.

---

## Open decisions (confirm with site owner / AAG before sending to Kinsta)

1. **Rest-of-world target** — `/` (en-us) *recommended* for a US brand, or `/uk/en`
   (blueprint's choice for the Canadian AXYZ site). Uncomment the ROW block only if the latter.
2. **Switcher bypass mechanism** — query param (A) or cookie (B).
3. **GeoIP flavour on Kinsta** — legacy vs GeoIP2 (drives variable names).
4. **Bot handling** — geo redirects are on `/` only and 302, so crawl impact is minimal;
   confirm no requirement to exempt search bots explicitly.

---

## Test plan (at launch, per locale, via VPN)

- [ ] From each country (FR, PL, MX/ES, GB, CA, a Quebec IP, US, a ROW country): hit `/`
      → lands on the mapped locale home; **US and ROW-as-`/`** stay at `/`.
- [ ] Deep link (`/products/…`) from any country → **no** redirect.
- [ ] `?utm_source=test&gclid=abc` on `/` → survives the geo 302 intact.
- [ ] Switcher round-trip: pick "US – English" from a French IP → stays on `/` (bypass works, no loop).
- [ ] Redirects are **302** (curl `-I` shows `302`, not `301`).
