# Pre-launch audit harness

Reusable multilingual smoke-test. Run against any host (dev or production).

```bash
# 1. Enumerate URLs (server, via WP-CLI)
wp eval-file audit-enum.php > urls.txt
# 2. Crawl + extract SEO/i18n signals
bash audit-crawl.sh urls.txt > crawl.txt
# 3. Data-integrity (server)
wp eval-file audit-data.php > data.txt
```

- `audit-enum.php` — emits `locale|category|label|url` for 6 locales × {homepages, key page templates, one single per CPT, CPT archives}. Aliased locales (en-ca/en-uk) build router-aliased URLs from the en-us slug.
- `audit-crawl.sh` — per URL: HTTP status, hreflang count, `<html lang>`, switcher, localized-nav probe, canonical self-reference.
- `audit-data.php` — locale meta counts, untagged posts, duplicate-locale groups, is_frontpage flags, groups missing the en-us anchor.

See ../PRE-LAUNCH-AUDIT-2026-07-30.md for the first run's results.
