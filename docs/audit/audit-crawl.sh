#!/usr/bin/env bash
# Pre-launch audit crawler. Reads a locale|category|label|url list, curls each,
# extracts structural SEO/i18n signals. Output = pipe table for post-processing.
URLS="${1:-/tmp/urls.txt}"
BODY=/tmp/_audit_body.html

declare -A PFX=( [en-us]="us/en" [es-us]="us/es" [fr-ca]="ca/fr" [pl-pl]="pl/pl" [en-ca]="ca/en" [en-uk]="uk/en" )
# Header nav "Products" per locale — a broad language-leakage probe (present on every page's header).
declare -A NAV=( [en-us]="Products" [en-ca]="Products" [en-uk]="Products" [es-us]="Productos" [fr-ca]="produits" [pl-pl]="Produkty" )

echo "LOC|CATEGORY|INIT|FINAL|HREF|HTMLLANG|SWITCH|NAVOK|CANOK|CANONICAL|URL"
while IFS='|' read -r loc cat label url; do
  [ -z "$url" ] && continue
  init=$(curl -s -o /dev/null -w '%{http_code}' "$url")
  final=$(curl -sL "$url" -o "$BODY" -w '%{http_code}')
  href=$(grep -o 'hreflang=' "$BODY" 2>/dev/null | wc -l | tr -d ' ')
  lang=$(grep -oiE '<html[^>]*lang="[^"]*"' "$BODY" 2>/dev/null | head -1 | grep -oiE 'lang="[^"]*"' | sed 's/[Ll]ang=//;s/"//g')
  canon=$(grep -oiE '<link[^>]*rel="canonical"[^>]*>' "$BODY" 2>/dev/null | grep -oiE 'href="[^"]*"' | head -1 | sed 's/[Hh]ref=//;s/"//g')
  sw=$(grep -ciE 'language-switcher|lang-switch|wj-switcher|locale-switch|switcher__' "$BODY" 2>/dev/null)
  [ "${sw:-0}" -gt 0 ] && SW=Y || SW=N
  navword="${NAV[$loc]}"
  if grep -qiF "$navword" "$BODY" 2>/dev/null; then NAVOK=Y; else NAVOK=N; fi
  pfx="${PFX[$loc]}"
  if echo "$canon" | grep -q "/$pfx/"; then CANOK=Y; else CANOK=N; fi
  # en-us home is legitimately canonical at site root
  if [ "$loc" = "en-us" ] && { echo "$url" | grep -qE '/us/en/?$'; }; then CANOK="${CANOK}*"; fi
  echo "$loc|$cat|$init|$final|$href|${lang:-?}|$SW|$NAVOK|$CANOK|$canon|$url"
done < "$URLS"
