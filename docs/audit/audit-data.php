<?php
/**
 * Pre-launch audit — Pass 2 data integrity (read-only).
 */
if (!defined('ABSPATH')) { exit; }
global $wpdb;

$locales = array('en-us','es-us','fr-ca','pl-pl','en-ca','en-uk');
$cpts = array('post','products','series','industry','accessories','testimonial','webinar','blog','news_and_events','video','page');

echo "===== (1) region_language_code counts: post_type x locale =====\n";
printf("%-16s", 'post_type');
foreach ($locales as $l) printf("%8s", $l);
printf("%8s\n", 'UNTAG');
foreach ($cpts as $pt) {
    printf("%-16s", $pt);
    $untag = 0;
    // total published
    $total = (int) $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type=%s AND post_status='publish'", $pt));
    $sum = 0;
    foreach ($locales as $l) {
        $c = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->posts} p JOIN {$wpdb->postmeta} m ON p.ID=m.post_id
             WHERE p.post_type=%s AND p.post_status='publish' AND m.meta_key='region_language_code' AND m.meta_value=%s",
            $pt, $l));
        $sum += $c;
        printf("%8d", $c);
    }
    $untag = $total - $sum; // publishable posts with no (matching) locale tag
    printf("%8d\n", $untag);
}

echo "\n===== (2) PUBLISHED posts with NO region_language_code (scoped types) =====\n";
foreach ($cpts as $pt) {
    if ($pt === 'page') continue;
    $rows = $wpdb->get_results($wpdb->prepare(
        "SELECT p.ID, p.post_title FROM {$wpdb->posts} p
         WHERE p.post_type=%s AND p.post_status='publish'
         AND NOT EXISTS (SELECT 1 FROM {$wpdb->postmeta} m WHERE m.post_id=p.ID AND m.meta_key='region_language_code' AND m.meta_value<>'')
         LIMIT 25", $pt));
    if ($rows) {
        echo "  [$pt] ".count($rows)."+ untagged: ";
        echo implode(', ', array_map(function($r){return $r->ID;}, $rows))."\n";
    }
}

echo "\n===== (3) translation_group_id sets with DUPLICATE locale (data bug) =====\n";
$dups = $wpdb->get_results(
    "SELECT g.meta_value AS grp, r.meta_value AS loc, COUNT(*) AS n, GROUP_CONCAT(p.ID) ids
     FROM {$wpdb->posts} p
     JOIN {$wpdb->postmeta} g ON p.ID=g.post_id AND g.meta_key='translation_group_id' AND g.meta_value<>''
     JOIN {$wpdb->postmeta} r ON p.ID=r.post_id AND r.meta_key='region_language_code'
     WHERE p.post_status='publish'
     GROUP BY g.meta_value, r.meta_value HAVING n>1 LIMIT 40");
if ($dups) { foreach ($dups as $d) echo "  grp {$d->grp} loc {$d->loc}: {$d->n} posts ({$d->ids})\n"; }
else echo "  none — every group has at most one post per locale ✓\n";

echo "\n===== (4) is_frontpage flags (expect 1 per locale = 6) =====\n";
$fp = $wpdb->get_results(
    "SELECT p.ID, p.post_title, r.meta_value AS loc
     FROM {$wpdb->posts} p
     JOIN {$wpdb->postmeta} f ON p.ID=f.post_id AND f.meta_key='is_frontpage' AND f.meta_value='yes'
     LEFT JOIN {$wpdb->postmeta} r ON p.ID=r.post_id AND r.meta_key='region_language_code'
     WHERE p.post_status='publish' ORDER BY r.meta_value");
echo "  count=".count($fp)."\n";
foreach ($fp as $f) echo "   {$f->loc}: {$f->ID} {$f->post_title}\n";

echo "\n===== (5) translation groups MISSING the en-us anchor (orphaned from source) =====\n";
$orphans = $wpdb->get_results(
    "SELECT g.meta_value AS grp,
            GROUP_CONCAT(DISTINCT r.meta_value) locs, COUNT(DISTINCT p.ID) n
     FROM {$wpdb->posts} p
     JOIN {$wpdb->postmeta} g ON p.ID=g.post_id AND g.meta_key='translation_group_id' AND g.meta_value<>''
     JOIN {$wpdb->postmeta} r ON p.ID=r.post_id AND r.meta_key='region_language_code'
     WHERE p.post_status='publish'
     GROUP BY g.meta_value
     HAVING locs NOT LIKE '%en-us%' LIMIT 40");
echo "  ".count($orphans)." group(s) with no en-us member:\n";
foreach ($orphans as $o) echo "   grp {$o->grp}: [{$o->locs}] ({$o->n} posts)\n";
