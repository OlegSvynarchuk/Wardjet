# Old WPML Translated CPT Slug Redirects

## Status: Implemented (2026-04-02)

These are old WPML-era translated CPT slugs that need redirecting to the new English-only slug structure.
Kinsta handles the locale prefix mapping (/fr/ → /ca/fr/, /es/ → /us/es/, /pl/ → /pl/pl/).
But the translated CPT base slug still needs mapping to the English equivalent.

## Complete Mapping

### French (fr → ca/fr)
| Old slug | New slug | Example |
|---|---|---|
| systemes-de-routeurs | routers | /fr/systemes-de-routeurs/infinite/ → /ca/fr/routers/infinite/ |
| logiciel | software | /fr/logiciel/a2mc-enterprise/ → /ca/fr/software/a2mc-enterprise/ |
| industrie | industry | /fr/industrie/plastiques/ → /ca/fr/industry/plastiques/ |
| accessoires | accessories | /fr/accessoires/ → /ca/fr/accessories/ |
| temoignages | testimonials | /fr/temoignages/ → /ca/fr/testimonials/ |
| contacter | contact | /fr/contacter/ → /ca/fr/contact/ |
| video | video | Same slug, no mapping needed |

### Spanish (es → us/es)
| Old slug | New slug | Example |
|---|---|---|
| enrutadores | routers | /es/enrutadores/innovator/ → /us/es/routers/innovator/ |
| programas | software | /es/programas/ → /us/es/software/ |
| industria | industry | /es/industria/plasticos/ → /us/es/industry/plasticos/ |
| accesorios | accessories | /es/accesorios/ → /us/es/accessories/ |
| testimonios | testimonials | /es/testimonios/ → /us/es/testimonials/ |
| video | video | Same slug, no mapping needed |
| software | software | Same slug, no mapping needed |

### Polish (pl → pl/pl)
| Old slug | New slug | Example |
|---|---|---|
| frezarki | routers | /pl/frezarki/panelbuilder/ → /pl/pl/routers/panelbuilder/ |
| oprogramowanie | software | /pl/oprogramowanie/a2mc-enterprise/ → /pl/pl/software/a2mc-enterprise/ |
| branze | industry | /pl/branze/ → /pl/pl/industry/ (already works via Kinsta) |
| akcesoria | accessories | /pl/akcesoria/ → /pl/pl/accessories/ (Kinsta redirects to akcesoria-do-ploterow-cnc) |
| wideo | video | /pl/wideo/ → /pl/pl/video/ (currently 404) |
| referencje | testimonials | /pl/referencje/ → /pl/pl/testimonials/ (currently 404) |
| baza-wiedzy | baza-wiedzy | Same slug, already covered |

## Implementation

Add to PHP `template_redirect` hook — map old translated CPT slug to English before the generic redirect:

```php
$old_slug_map = array(
    // French
    'systemes-de-routeurs' => 'routers',
    'logiciel' => 'software',
    'industrie' => 'industry',
    'accessoires' => 'accessories',
    'temoignages' => 'testimonials',
    'contacter' => 'contact',
    // Spanish
    'enrutadores' => 'routers',
    'programas' => 'software',
    'industria' => 'industry',
    'accesorios' => 'accessories',
    'testimonios' => 'testimonials',
    // Polish
    'frezarki' => 'routers',
    'oprogramowanie' => 'software',
    'branze' => 'industry',
    'akcesoria' => 'accessories',
    'wideo' => 'video',
    'referencje' => 'testimonials',
);
```

Note: Kinsta already handles the /fr/ → /ca/fr/ prefix. This mapping replaces the CPT slug portion only.

*Created: 2026-04-02*
