# Pending blueprint migrations

Items from the AXYZ blueprint theme intentionally **not yet** ported to wardjet,
to revisit when the relevant feature is built. (Materials CPT + sub-routers are
excluded permanently — AXYZ-only, out of scope for wardjet.)

---

## News ticker → locale-aware REST  *(port when the ticker is implemented)*

The news ticker fetches `news_and_events` over the REST API. For ticker items to
link to the correct **locale** URL (not the bare `/news_and_events/slug/`), port
the blueprint's REST permalink filter.

**Blueprint source:** `axyzlive/public/wp-content/themes/wardjet/functions.php`
(fn ~line 1073, hook ~line 1068).

```php
add_filter( 'rest_prepare_news_and_events', 'wj_rest_locale_permalink', 1000, 3 );

function wj_rest_locale_permalink( $response, $post, $request ) {
    if ( ! ( $response instanceof WP_REST_Response ) || ! ( $post instanceof WP_Post ) ) return $response;

    $code   = wj_get_saved_lang_for_post( $post->ID );           // wardjet: post meta 'region_language_code'
    $prefix = wj_locale_to_prefix( $code );                      // wardjet equivalent: lc_locale_to_prefix()
    $base   = wj_get_rewrite_base( $post->post_type );           // NOT in wardjet yet — see helper below
    $slug   = $post->post_name ?: sanitize_title( $post->post_title ) ?: $post->ID;

    if ( $post->post_status === 'publish' ) {
        $post->temp_lang_code = $code;                           // honored by wardjet permalink filter (permalinks.php/seo.php)
        $link = get_permalink( $post );
    } else {
        $link = trailingslashit( home_url( "/{$prefix}/{$base}/{$slug}/" ) );
    }
    $template = trailingslashit( home_url( "/{$prefix}/{$base}/%postname%/" ) );

    $data = $response->get_data();
    $data['link']               = $link;
    $data['permalink_template'] = $template;
    $data['generated_slug']     = $slug;
    $response->set_data( $data );
    return $response;
}
```

**Helper dependencies to reconcile when porting:**

- `wj_locale_to_prefix($code)` → **use wardjet's `lc_locale_to_prefix()`**.
- `wj_get_saved_lang_for_post($id)` → wardjet stores locale in post meta
  `region_language_code`; read that (see `seo.php` / `admin-language-column.php`).
- `$post->temp_lang_code` → **already supported** by wardjet's permalink filter
  (`wj-multilingual/includes/permalinks.php` + `seo.php`).
- `wj_get_rewrite_base($post_type)` → **not in wardjet**; trivial to add:
  ```php
  function wj_get_rewrite_base( $pt ) {
      $o = get_post_type_object( $pt );
      return ( $o && ! empty( $o->rewrite['slug'] ) ) ? trim( $o->rewrite['slug'], '/' ) : trim( $pt, '/' );
  }
  ```

**Optional / separate (NOT the ticker):** the blueprint also adds a `count`
orderby to REST for industry + testimonial. Port only if their admin/REST
ordering needs it:
```php
add_filter( 'rest_industry_collection_params',    'filter_add_rest_orderby_params', 10, 1 );
add_filter( 'rest_testimonial_collection_params', 'filter_add_rest_orderby_params', 10, 1 );
function filter_add_rest_orderby_params( $params ) { $params['orderby']['enum'][] = 'count'; return $params; }
```

---

## Audit result for the rest of the blueprint theme (for reference)

Everything else from the blueprint's theme/functions is already migrated:
- **wj-multilingual plugin** — present and a *superset* of the blueprint (adds
  `menu-locations.php`, `admin-language-column.php`, `acf-locale-fields.php`).
- **CPT locale rewrites** (`custom_post_types_rewrite_rules`) — reimplemented in
  `routing.php` with wardjet's CPTs (products/series/… vs blueprint routers/software).
- **Admin language column** — relocated to `admin-language-column.php`.
- **Locations rendering, dynamic menus, front-page detection** — reimplemented.
- **Language-settings admin page** — intentionally skipped: locales are code-defined
  (`seo_supported_locales()`), and the one `language_codes` consumer
  (`menu-locations.php`) has a correct hardcoded default for all 6 locales.
