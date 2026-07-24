<?php
/**
 * The template for displaying the footer
 *
 * @package WP_Bootstrap_Starter
 */

// --- Helpers (safe inline, won't redeclare) ---
if (!function_exists('wj_current_region_lang')) {
    function wj_current_region_lang(): array {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        if (preg_match('#^/([a-z]{2})/([a-z]{2})(?:/|$)#i', $path, $m)) {
            return [strtolower($m[1]), strtolower($m[2])];
        }
        return ['us', 'en'];
    }
}
if (!function_exists('wj_pick_menu_location')) {
    function wj_pick_menu_location(string $base, string $region, string $lang): string {
        $want     = strtolower($base . '-' . $region . '-' . $lang);
        $fallback = strtolower($base . '-us-en');
        $locs = get_nav_menu_locations();
        if (isset($locs[$want]) && !empty($locs[$want]))        return $want;
        if (isset($locs[$fallback]) && !empty($locs[$fallback])) return $fallback;
        if (isset($locs[$base]) && !empty($locs[$base]))         return $base;
        return $want;
    }
}
if (!function_exists('lc_get_locale_from_url')) {
    function lc_get_locale_from_url(): string {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
        if (preg_match('#^/([a-z]{2})/([a-z]{2})(?:/|$)#i', $path, $m)) {
            return strtolower($m[2] . '-' . $m[1]);
        }
        return 'en-us';
    }
}
if (!function_exists('lc_locale_to_prefix')) {
    function lc_locale_to_prefix(string $code): string {
        $p = explode('-', strtolower($code));
        return (count($p) === 2) ? strtolower($p[1] . '/' . $p[0]) : 'us/en';
    }
}
if (!function_exists('wj_footer_home_url')) {
    function wj_footer_home_url(): string {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '/';
        if (preg_match('#^/([a-z]{2})/([a-z]{2})(?:/|$)#i', $path, $m)) {
            $code = strtolower($m[2]) . '-' . strtolower($m[1]);
            if ($code === 'en-us') return home_url('/');
            return home_url('/' . strtolower($m[1]) . '/' . strtolower($m[2]) . '/');
        }
        return home_url('/');
    }
}
if (!function_exists('lc_pick_locale_field')) {
    function lc_pick_locale_field(string $base): ?string {
        [$region, $lang] = function_exists('wj_current_region_lang') ? wj_current_region_lang() : ['us','en'];
        $candidates = [
            $base . '_' . strtolower($lang . '_' . $region),
            $base . '_' . strtolower($region . '_' . $lang),
            $base,
        ];
        foreach ($candidates as $field) {
            if (function_exists('have_rows') && have_rows($field, 'option')) return $field;
            $val = function_exists('get_field') ? get_field($field, 'option') : null;
            if (!empty($val)) return $field;
        }
        return null;
    }
}

list($region, $lang) = wj_current_region_lang();
$footer_main_location = wj_pick_menu_location('footer-main', $region, $lang);
$footer_nav_location  = wj_pick_menu_location('footer-nav',  $region, $lang);
?>

<?php if(!is_page_template('blank-page.php') && !is_page_template('blank-page-with-container.php')): ?>

</div><!-- #content -->

<?php get_template_part('footer-widget'); ?>
<footer id="colophon" class="site-footer" role="contentinfo">
    <?php if ( function_exists( 'wj_tracking_allowed' ) && wj_tracking_allowed() ) : ?>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5P6ZD8G"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <?php endif; ?>
    <div class="footer-inner">

        <!-- Row 1: Navigation Links Grid -->
        <div class="footer-nav-grid">
            <?php
            wp_nav_menu(array(
                'theme_location' => $footer_main_location,
                'menu_id'        => 'menu-footer-main',
                'container'      => false,
                'fallback_cb'    => false,
            ));
            ?>
        </div>

        <!-- Row 2: Logo + Social + HQ Addresses -->
        <div class="footer-middle">
            <div class="footer-middle__left">
                <?php
                $footerLogo = get_field('footer_logo', 'option');
                if (!empty($footerLogo)): ?>
                    <a href="<?php echo esc_url(wj_footer_home_url()); ?>" class="footer-logo" aria-label="footer_logo">
                        <?php echo wp_get_attachment_image($footerLogo, 'full'); ?>
                    </a>
                <?php endif; ?>

                <?php if (have_rows('footer_social', 'option')): ?>
                    <div class="footer-social">
                        <?php while (have_rows('footer_social', 'option')): the_row();
                            $social_link = get_sub_field('url');
                            if ($social_link):
                                $url    = $social_link['url'];
                                $target = $social_link['target'] ?: '_self';
                        ?>
                            <a href="<?php echo esc_url($url); ?>" class="footer-social__link" target="<?php echo esc_attr($target); ?>" aria-label="social-media">
                                <?php
                                $icon_code = get_sub_field('icon_code');
                                if ($icon_code === 'fab fa-x-twitter') : ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865z"/></svg>
                                <?php else : ?>
                                    <i class="<?php echo esc_attr($icon_code); ?>"></i>
                                <?php endif; ?>
                            </a>
                        <?php endif; endwhile; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="footer-middle__right">
                <?php
                $hq_field = lc_pick_locale_field('footer_headquarters');
                $hq_rows  = $hq_field ? get_field($hq_field, 'option') : null;

                if (is_array($hq_rows) && !empty($hq_rows)) {
                    // Reorder HQ cards by current locale
                    // us-en, us-es: us, ca, uk
                    // en-ca, fr-ca: ca, us, uk
                    // en-uk, pl-pl: uk, us, ca
                    $locale_code = function_exists('lc_get_locale_from_url') ? lc_get_locale_from_url() : 'en-us';
                    $order_map = [
                        'en-us' => ['us','ca','uk'],
                        'es-us' => ['us','ca','uk'],
                        'en-ca' => ['ca','us','uk'],
                        'fr-ca' => ['ca','us','uk'],
                        'en-uk' => ['uk','us','ca'],
                        'pl-pl' => ['uk','us','ca'],
                    ];
                    $desired = $order_map[$locale_code] ?? ['us','ca','uk'];

                    // Detect HQ country from heading text (multilingual)
                    $detect_country = function($row) {
                        $heading = mb_strtolower($row['heading'] ?? '', 'UTF-8');
                        // strip diacritics for safer matching
                        $no_diacritics = function($s) {
                            return strtr($s, [
                                'á'=>'a','à'=>'a','â'=>'a','ä'=>'a','ã'=>'a',
                                'é'=>'e','è'=>'e','ê'=>'e','ë'=>'e',
                                'í'=>'i','ì'=>'i','î'=>'i','ï'=>'i',
                                'ó'=>'o','ò'=>'o','ô'=>'o','ö'=>'o','õ'=>'o',
                                'ú'=>'u','ù'=>'u','û'=>'u','ü'=>'u',
                                'ç'=>'c','ñ'=>'n','ł'=>'l','ą'=>'a','ę'=>'e','ś'=>'s','ż'=>'z','ź'=>'z','ć'=>'c','ń'=>'n','ó'=>'o',
                            ]);
                        };
                        $h = $no_diacritics($heading);
                        // Canada
                        if (strpos($h, 'canad') !== false || strpos($h, 'kanad') !== false) return 'ca';
                        // UK
                        if (strpos($h, 'united kingdom') !== false || strpos($h, 'royaume') !== false || strpos($h, 'reino unido') !== false || strpos($h, 'brytan') !== false || preg_match('/\buk\b/', $h)) return 'uk';
                        // US (catch USA, US, EE.UU., Estados, Etats, Stany)
                        if (strpos($h, 'usa') !== false || strpos($h, 'united states') !== false || strpos($h, 'estados unidos') !== false || strpos($h, 'etats') !== false || strpos($h, 'stany') !== false || strpos($h, 'ee.uu') !== false || strpos($h, 'ee uu') !== false || preg_match('/\bus\b/', $h)) return 'us';
                        return '';
                    };

                    $buckets = ['us' => null, 'ca' => null, 'uk' => null];
                    $unknown = [];
                    foreach ($hq_rows as $row) {
                        $country = $detect_country($row);
                        if ($country && $buckets[$country] === null) {
                            $buckets[$country] = $row;
                        } else {
                            $unknown[] = $row;
                        }
                    }

                    $sorted = [];
                    foreach ($desired as $c) {
                        if (!empty($buckets[$c])) $sorted[] = $buckets[$c];
                    }
                    $sorted = array_merge($sorted, $unknown);

                    foreach ($sorted as $row): ?>
                        <div class="footer-hq">
                            <h4 class="footer-hq__title"><?php echo esc_html($row['heading'] ?? ''); ?></h4>
                            <?php if (!empty($row['description_block']) && is_array($row['description_block'])): ?>
                                <div class="footer-hq__details">
                                    <?php foreach ($row['description_block'] as $desc): ?>
                                        <p><?php echo wp_kses_post($desc['description'] ?? ''); ?></p>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach;
                } ?>
            </div>
        </div>

        <!-- Row 3: Copyright + Legal Links -->
        <div class="footer-bottom">
            <span class="footer-bottom__copyright">
                &copy; <?php echo esc_html(get_bloginfo('name')); ?> <?php echo date('Y'); ?> - Part of AAG Canada
            </span>
            <div class="footer-bottom__legal">
                <?php
                wp_nav_menu(array(
                    'theme_location' => $footer_nav_location,
                    'menu_id'        => 'menu-footer-nav',
                    'container'      => false,
                    'fallback_cb'    => false,
                    'depth'          => 1,
                ));
                ?>
            </div>
        </div>

    </div>
</footer><!-- #colophon -->
<?php endif; ?>
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
