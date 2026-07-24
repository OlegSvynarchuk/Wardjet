#!/usr/bin/env bash
# ---------------------------------------------------------------------------
# WARDJET localization smoke test  (public URLs only — no SSH needed)
#   bash scripts/i18n-check.sh
#   BASE=https://www.wardjet.com/ bash scripts/i18n-check.sh
# Exit 0 = all pass, 1 = failures. INFO lines are advisory (known/expected gaps).
#
# Covers, per locale: home · products page · single product (series) ·
# industries page · single industry · videos page · single video.
# Localized slugs differ per locale, so representative URLs are listed below;
# regenerate them with scratchpad/gen-urls.php if content slugs change.
# ---------------------------------------------------------------------------
BASE="${BASE:-https://wardjet.pixels2pixels.ch/}"
PASS=0; FAIL=0; INFO=0
red=$'\e[31m'; grn=$'\e[32m'; yel=$'\e[33m'; dim=$'\e[2m'; rst=$'\e[0m'
LOCALES=(en-us es-us en-ca fr-ca en-uk pl-pl)
declare -A PFX=( [en-us]="" [es-us]="us/es/" [en-ca]="ca/en/" [fr-ca]="ca/fr/" [en-uk]="uk/en/" [pl-pl]="pl/pl/" )

# Expected per-locale homepage values --------------------------------------
declare -A HQ1=(  [en-us]="USA Headquarters" [es-us]="Sede Estados Unidos" [en-ca]="Canada Headquarters"
                  [fr-ca]="Siège Canada" [en-uk]="UK Headquarters" [pl-pl]="Siedziba w Wielkiej Brytanii" )
declare -A EXPLORE=( [en-us]="Explore Products" [es-us]="Explorar Productos" [en-ca]="Explore Products"
                     [fr-ca]="Explorer les Produits" [en-uk]="Explore Products" [pl-pl]="Przeglądaj Produkty" )
declare -A PKG=(  [en-us]="Custom Foam Inserts" [es-us]="Insertos de espuma personalizados" [en-ca]="Custom Foam Inserts"
                  [fr-ca]="Inserts en mousse sur mesure" [en-uk]="Custom Foam Inserts" [pl-pl]="Indywidualne wkłady piankowe" )

# Page-type URLs per locale (path after BASE). "-" = not present in this locale.
declare -A PRODUCTS_PAGE=( [en-us]="us/en/our-products/" [es-us]="us/es/productos/" [en-ca]="ca/en/our-products/"
                           [fr-ca]="ca/fr/des-produits/" [en-uk]="uk/en/our-products/" [pl-pl]="pl/pl/produkty/" )
declare -A INDUSTRIES_PAGE=( [en-us]="us/en/industries/" [es-us]="us/es/industrias/" [en-ca]="ca/en/industries/"
                             [fr-ca]="ca/fr/industries/" [en-uk]="uk/en/industries/" [pl-pl]="pl/pl/branze/" )
declare -A SINGLE_INDUSTRY=( [en-us]="us/en/industry/automotive-2/" [es-us]="us/es/industry/automotor/" [en-ca]="ca/en/industry/automotive/"
                             [fr-ca]="ca/fr/industry/automobile/" [en-uk]="uk/en/industry/automotive-3/" [pl-pl]="pl/pl/industry/automobilowy/" )
declare -A SINGLE_SERIES=( [en-us]="us/en/series/a-series/" [es-us]="us/es/series/serie-a/" [en-ca]="-"
                           [fr-ca]="ca/fr/series/serie-a/" [en-uk]="-" [pl-pl]="pl/pl/series/serie-a/" )
declare -A VIDEOS_PAGE=( [en-us]="us/en/waterjet-videos/" [es-us]="us/es/videos-de-apoyo/" [en-ca]="ca/en/waterjet-videos/"
                         [fr-ca]="-" [en-uk]="uk/en/waterjet-videos/" [pl-pl]="-" )
declare -A SINGLE_VIDEO=( [en-us]="us/en/video/precision-you-can-see-cutting-glass-with-wardjet-waterjets/" [es-us]="us/es/video/reemplazo-de-la-vejiga-de-la-minitolva/"
                          [en-ca]="-" [fr-ca]="ca/fr/video/19-reparation-de-fuite-a-haute-pression/" [en-uk]="-" [pl-pl]="-" )

# Helpers -------------------------------------------------------------------
ok()   { PASS=$((PASS+1)); printf "  ${grn}PASS${rst} %s\n" "$1"; }
bad()  { FAIL=$((FAIL+1)); printf "  ${red}FAIL${rst} %s\n" "$1"; }
info() { INFO=$((INFO+1)); printf "  ${yel}INFO${rst} %s\n" "$1"; }
fetch(){ curl -sL --max-time 25 "$1"; }
code() { curl -sL --max-time 25 -o /dev/null -w '%{http_code}' "$1"; }
finalurl(){ curl -sL --max-time 25 -o /dev/null -w '%{url_effective}' "$1"; }
has()  { if printf '%s' "$2" | grep -qiF -- "$3"; then ok "$1"; else bad "$1 (missing \"$3\")"; fi; }
hq_first(){ local got; got=$(printf '%s' "$2" | grep -oiE 'footer-hq__title[^>]*>[^<]*' | head -1 | sed -E 's/.*>//;s/[: ]*$//'); \
  if printf '%s' "$got" | grep -qiF -- "$3"; then ok "$1 (= $got)"; else bad "$1 (got \"$got\" want \"$3\")"; fi; }

# assert a localized page/CPT exists (200) AND doesn't fall back out of its locale prefix
page_ok(){ local label="$1" loc="$2" path="$3"
  if [ "$path" = "-" ]; then info "$label — no $loc content (shared/untranslated)"; return; fi
  local url="${BASE}${path}" c fu
  c=$(code "$url"); fu=$(finalurl "$url")
  local pre="${PFX[$loc]}"
  if [ "$c" != "200" ]; then bad "$label ($loc) HTTP $c"
  elif [ -n "$pre" ] && ! printf '%s' "$fu" | grep -qF "/$pre"; then bad "$label ($loc) fell back to ${fu#$BASE}"
  else ok "$label ($loc) 200"; fi; }

echo "== WARDJET i18n check @ $BASE =="

# 1) Homepage content per locale
for loc in "${LOCALES[@]}"; do
  home="${BASE}${PFX[$loc]}"; echo ""; echo "── HOME $loc ${dim}($home)${rst}"
  c=$(code "$home"); [ "$c" = "200" ] && ok "home 200" || bad "home HTTP $c"
  H=$(fetch "$home")
  hq_first "footer HQ #1" "$H" "${HQ1[$loc]}"
  has "Explore-Products localized" "$H" "${EXPLORE[$loc]}"
  has "Packaging features localized" "$H" "${PKG[$loc]}"
  n=$(printf '%s' "$H" | grep -oiE 'partnerships__logo' | wc -l | tr -d ' ')
  [ "${n:-0}" -ge 8 ] && ok "partner logos ($n)" || bad "partner logos ($n want >=8)"
done

# 2) Page-type coverage per locale
echo ""; echo "── PAGE TYPES (200 + stays in locale)"
for loc in "${LOCALES[@]}"; do
  echo "  · $loc"
  page_ok "  products page"   "$loc" "${PRODUCTS_PAGE[$loc]}"
  page_ok "  industries page" "$loc" "${INDUSTRIES_PAGE[$loc]}"
  page_ok "  single industry" "$loc" "${SINGLE_INDUSTRY[$loc]}"
  page_ok "  single product"  "$loc" "${SINGLE_SERIES[$loc]}"
  page_ok "  videos page"     "$loc" "${VIDEOS_PAGE[$loc]}"
  page_ok "  single video"    "$loc" "${SINGLE_VIDEO[$loc]}"
done

echo ""; echo "== $((PASS+FAIL)) assertions: ${grn}$PASS pass${rst}, ${red}$FAIL fail${rst}, ${yel}$INFO info${rst} =="
[ "$FAIL" -eq 0 ]
