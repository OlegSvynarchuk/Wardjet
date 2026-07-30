<?php
/**
 * Pre-launch audit — URL enumerator.
 * Emits one line per surface:  locale|category|label|url
 * Covers 6 locales × {homepage, key page templates, one single per CPT, CPT archives}.
 * Aliased locales (en-ca/en-uk): use their own copies where they exist, else build
 * the router-aliased URL from the en-us slug under the alias prefix.
 */
if (!defined('ABSPATH')) { exit; }

$locales = array(
    'en-us' => 'us/en',
    'es-us' => 'us/es',
    'fr-ca' => 'ca/fr',
    'pl-pl' => 'pl/pl',
    'en-ca' => 'ca/en',
    'en-uk' => 'uk/en',
);
$aliasable = array('en-ca', 'en-uk');

$cpts = array('products','series','industry','accessories','testimonial','webinar','blog','news_and_events','video');

// Page templates to sample (label => template file)
$page_templates = array(
    'page:home'      => 'wardjet-homepage.php',
    'page:products'  => 'wardjet-our-products.php',
    'page:about'     => 'wardjet-about.php',
    'page:industries'=> 'wardjet-industries.php',
    'archive:news'   => 'newsandevents.php',
    'archive:blogs'  => 'blogs.php',
    'archive:webinar'=> 'page-webinars.php',
);

$home = rtrim(home_url('/'), '/');

function first_post_for_locale($post_type, $locale) {
    $q = get_posts(array(
        'post_type'      => $post_type,
        'posts_per_page' => 1,
        'post_status'    => 'publish',
        'orderby'        => 'ID',
        'order'          => 'ASC',
        'meta_query'     => array(array('key'=>'region_language_code','value'=>$locale)),
    ));
    return $q ? $q[0] : null;
}

function cpt_base($pt) {
    $o = get_post_type_object($pt);
    if ($o && !empty($o->rewrite['slug'])) return trim($o->rewrite['slug'], '/');
    return trim($pt, '/');
}

$rows = array();

// --- Page templates (per locale) ---
foreach ($page_templates as $label => $tmpl) {
    $pages = get_posts(array(
        'post_type'=>'page','posts_per_page'=>-1,'post_status'=>'publish',
        'meta_key'=>'_wp_page_template','meta_value'=>$tmpl,
    ));
    foreach ($pages as $p) {
        $loc = get_post_meta($p->ID, 'region_language_code', true);
        if (!$loc || !isset($locales[$loc])) continue;
        $rows[] = array($loc, $label, $p->post_title, get_permalink($p->ID));
    }
}

// --- CPT singles (one per CPT per locale) ---
foreach ($cpts as $pt) {
    $base = cpt_base($pt);
    foreach ($locales as $loc => $prefix) {
        $p = first_post_for_locale($pt, $loc);
        if ($p) {
            $rows[] = array($loc, "single:$pt", $p->post_name, get_permalink($p->ID));
        } elseif (in_array($loc, $aliasable, true)) {
            // Router-aliased: build from the en-us slug under the alias prefix
            $en = first_post_for_locale($pt, 'en-us');
            if ($en) {
                $url = $home . '/' . $prefix . '/' . $base . '/' . $en->post_name . '/';
                $rows[] = array($loc, "single:$pt", $en->post_name.' (aliased)', $url);
            }
        }
    }
}

// --- CPT archives that are CPT-native (testimonial) ---
foreach ($locales as $loc => $prefix) {
    $rows[] = array($loc, 'archive:testimonial', 'testimonials', $home.'/'.$prefix.'/testimonials/');
}

foreach ($rows as $r) {
    echo implode('|', $r) . "\n";
}
