<?php
/**
 * The header for our theme
 *
 * @package WP_Bootstrap_Starter
 */

global $body_style;

/**
 * --- Helpers for region/lang menu picking (safe inline) ---
 * Detect /{region}/{lang}/ from URL and pick the right menu location,
 * falling back to {base}-us-en, then plain {base} if needed.
 */
if (!function_exists('wj_current_region_lang')) {
    function wj_current_region_lang(): array {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        if (preg_match('#^/([a-z]{2})/([a-z]{2})(?:/|$)#i', $path, $m)) {
            return [ strtolower($m[1]), strtolower($m[2]) ]; // [region, lang], e.g. ['ca','fr']
        }
        return ['us','en']; // global fallback
    }
}
if (!function_exists('wj_pick_menu_location')) {
    function wj_pick_menu_location(string $base, string $region, string $lang): string {
        $want     = strtolower($base . '-' . $region . '-' . $lang); // e.g. primary-uk-en
        $fallback = strtolower($base . '-us-en');
        $locs = get_nav_menu_locations();

        if (isset($locs[$want]) && !empty($locs[$want]))        return $want;
        if (isset($locs[$fallback]) && !empty($locs[$fallback])) return $fallback;
        if (isset($locs[$base]) && !empty($locs[$base]))         return $base; // legacy/plain base

        // Return desired key even if unassigned (WP will render nothing, but avoids notices)
        return $want;
    }
}

/**
 * --- Locale-aware homepage helpers (inline) ---
 * Find the real locale homepage (page where ACF is_frontpage="yes" and region_language_code matches),
 * otherwise fall back to /{region}/{lang}/.
 */
if (!function_exists('lc_get_locale_from_url')) {
    function lc_get_locale_from_url(): string {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
        if (preg_match('#^/([a-z]{2})/([a-z]{2})(?:/|$)#i', $path, $m)) {
            return strtolower($m[2] . '-' . $m[1]); // /ca/fr/... => fr-ca
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
if (!function_exists('lc_locale_frontpage_url')) {
    function lc_locale_frontpage_url(?string $code = null): string {
        $code = $code ? strtolower($code) : lc_get_locale_from_url();

        $ids = get_posts([
            'post_type'      => 'page',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'fields'         => 'ids',
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
            'meta_query'     => [
                'relation' => 'AND',
                ['key' => 'region_language_code', 'value' => $code],
                ['key' => 'is_frontpage', 'value' => ['yes','1','true'], 'compare' => 'IN'],
            ],
        ]);

        if (!empty($ids)) {
            $url = get_permalink((int)$ids[0]); // e.g. /ca/fr/accueil/, /us/es/inicio/
            if ($url) return $url;
        }
        return home_url('/' . lc_locale_to_prefix($code) . '/'); // fallback to locale root
    }
}
if (!function_exists('locale_home_url')) {
    function locale_home_url(?string $code = null): string {
        return lc_locale_frontpage_url($code);
    }
}

if (!function_exists('wj_logo_home_url')) {
    function wj_logo_home_url(): string {
        // Detect current locale from URL
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '/';
        if (preg_match('#^/([a-z]{2})/([a-z]{2})(?:/|$)#i', $path, $m)) {
            $region = strtolower($m[1]);
            $lang   = strtolower($m[2]);
            $code   = $lang . '-' . $region; // en-us, fr-ca, en-uk, es-us, ...

            // US/EN → real site root "/"
            if ($code === 'en-us') {
                return home_url('/');
            }

            // All other locales → clean root "/{cc}/{ll}/"
            if (function_exists('lc_locale_to_prefix')) {
                return home_url('/' . lc_locale_to_prefix($code) . '/');
            } else {
                // very safe fallback if helper isn't available here
                return home_url('/' . $region . '/' . $lang . '/');
            }
        }

        // No locale in URL → default to "/"
        return home_url('/');
    }
}

/**
 * --- Locale-aware CTA helpers (inline) ---
 * Render header CTAs from locale-specific repeaters on ACF Options:
 *   Preferred naming (your test): links_fr_ca
 *   Also accepted:                links_ca_fr
 * Fallbacks: links_en_us -> links
 * Same-site URLs get their /xx/yy/ prefix swapped to current locale.
 */
if (!function_exists('lc_swap_locale_prefix')) {
    function lc_swap_locale_prefix(string $url, string $code): string {
        $home = rtrim(home_url(), '/');
        if (stripos($url, $home) !== 0) return $url; // external
        $rel = ltrim(substr($url, strlen($home)), '/');
        if ($rel === '') return $url;

        $target_prefix = lc_locale_to_prefix($code);                 // e.g. uk/en
        if (preg_match('#^[a-z]{2}/[a-z]{2}(/|$)#i', $rel)) {
            $rel = preg_replace('#^[a-z]{2}/[a-z]{2}#i', $target_prefix, $rel, 1);
        } else {
            $rel = $target_prefix . '/' . ltrim($rel, '/');
        }
        $new = $home . '/' . ltrim($rel, '/');
        $structure = get_option('permalink_structure');
        if (is_string($structure) && substr($structure, -1) === '/' && substr($new, -1) !== '/') {
            $new .= '/';
        }
        return $new;
    }
}
if (!function_exists('render_localized_header_ctas')) {
    function render_localized_header_ctas() {
        // current locale like en-us / fr-ca / en-uk
        $code = lc_get_locale_from_url();
        // region/lang for field names (e.g. links_fr_ca)
        [$region, $lang] = wj_current_region_lang();

        // Prefer exact field name `links_fr_ca`, then `links_ca_fr`, then fallbacks
        $candidates = [
            'links_' . strtolower($lang . '_' . $region), // links_fr_ca
            'links_' . strtolower($region . '_' . $lang), // links_ca_fr (alt naming)
            'links_en_us',                                 // optional fallback if present
            'links',                                       // global default
        ];

        $picked = '';
        foreach ($candidates as $field) {
            if (have_rows($field, 'option')) {
                $picked = $field;
                break;
            }
        }
        if (!$picked) return;

        while (have_rows($picked, 'option')): the_row();
            $link = get_sub_field('url'); // ACF Link (array: url, title, target)
            if (!$link || empty($link['url'])) continue;

            // swap same-site URL to current /xx/yy/ without changing your markup/styles
            $link['url'] = lc_swap_locale_prefix($link['url'], $code);

            // Keep theme styles intact by using your helper
            if (function_exists('build_link')) {
                echo build_link($link);
            } else {
                // ultra-safe fallback if build_link is unavailable
                $title  = !empty($link['title']) ? $link['title'] : __('Learn more', 'textdomain');
                $target = !empty($link['target']) ? $link['target'] : '_self';
                printf(
                    '<a href="%s" target="%s" rel="%s">%s</a>',
                    esc_url($link['url']),
                    esc_attr($target),
                    $target === '_blank' ? 'noopener' : 'nofollow noopener',
                    esc_html($title)
                );
            }
        endwhile;
    }
}

/**
 * --- NEW: Locale-aware header icon (US/EN vs all others) ---
 * Uses ACF Options fields:
 *   - header_icon     (for en-us)
 *   - header_icon_ca  (for all other locales)
 */
if (!function_exists('get_header_icon_id_by_locale')) {
    function get_header_icon_id_by_locale(): ?int {
        $code = lc_get_locale_from_url(); // e.g. en-us, fr-ca
        $field = ($code === 'en-us') ? 'header_icon' : 'header_icon_ca';

        $val = get_field($field, 'option'); // ACF Options
        if (!$val) return null;

        if (is_array($val) && isset($val['ID'])) return (int)$val['ID'];
        if (is_numeric($val)) return (int)$val;
        return null;
    }
}

/**
 * --- Utility bar logo for all locales ---
 * Uses ACF Options fields:
 *   - header_icon_ca  (for ca-en and ca-fr)
 *   - header_icon     (for all other locales: en-us, es-us, en-uk, pl-pl)
 */
if (!function_exists('get_header_utility_logo_ids')) {
    /**
     * Returns array of logo attachment IDs for the utility bar.
     * CA locales: [ca_logo]
     * All others: [ca_logo, us_logo]
     */
    function get_header_utility_logo_ids(): array {
        $code = lc_get_locale_from_url();
        if (!$code) $code = 'en-us';

        $parse = function($val) {
            if (!$val) return null;
            if (is_array($val) && isset($val['ID'])) return (int)$val['ID'];
            if (is_numeric($val)) return (int)$val;
            return null;
        };

        $ca_id = $parse(get_field('header_icon_ca', 'option'));
        $us_id = $parse(get_field('header_icon', 'option'));

        if (in_array($code, ['en-ca', 'fr-ca'])) {
            return array_filter([$ca_id]);
        }
        return array_filter([$ca_id, $us_id]);
    }
}
// Legacy compat
if (!function_exists('get_header_utility_logo_id')) {
    function get_header_utility_logo_id(): ?int {
        $ids = get_header_utility_logo_ids();
        return $ids ? $ids[0] : null;
    }
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-5P6ZD8G');</script>
    <!-- End Google Tag Manager -->
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>   
    <link href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.css" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.css" rel="stylesheet"/>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">

    <?php wp_head(); ?>

    <style>
        /* Prevent FOUC: hide mega sub-menus until HTML is parsed. We listen
           for DOMContentLoaded (fires ~100-300ms after navigation) rather than
           window.load (which waits for slow iframes like Zapier and would block
           menus for 3-4 seconds, making simple dropdowns feel unresponsive). */
        body:not(.menus-ready) .mega-sub-menu,
        body:not(.menus-ready) [id^="mega-menu-wrap-"] ul.mega-sub-menu {
            visibility: hidden !important;
        }
    </style>
    <script>
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function () {
                document.body.classList.add('menus-ready');
            });
        } else {
            document.body.classList.add('menus-ready');
        }
    </script>
</head>

<body <?php body_class(); ?> <?=$body_style?$body_style:''?>>

<?php 
// WordPress 5.2 wp_body_open implementation
if ( function_exists( 'wp_body_open' ) ) {
    wp_body_open();
} else {
    do_action( 'wp_body_open' );
}
?>

<?php /* axyz-specific Zapier chatbot embed intentionally omitted for wardjet. */ ?>

<div id="page" class="site">
    <a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'wp-bootstrap-starter' ); ?></a>

    <?php if(!is_page_template( 'blank-page.php' ) && !is_page_template( 'blank-page-with-container.php' )): ?>
    <header id="masthead" class="site-header navbar-static-top <?php echo wp_bootstrap_starter_bg_class(); ?>" role="banner">
        <?php
        /**
         * Dynamic Menu Switching (unified region/lang)
         */
        // wardjet dev uses the BASE nav locations; the wj-multilingual plugin's
        // theme_mod_nav_menu_locations filter remaps them to the current locale's
        // menu per request, so Max Mega Menu (enabled on base 'primary') keeps
        // rendering the products fly-out on every locale.
        $primary_location    = 'primary';
        $header_nav_location = 'header-nav';
        ?>

        <!-- Utility Bar (Blue Gradient) - Bottom Row -->
        <div class="header-utility-bar">
            <div class="container-xxl">
                <div class="row align-items-center no-gutters">
                    <!-- Utility Logos - LEFT side -->
                    <div class="col-auto">
                        <div class="utility-logo">
                            <?php
                            $utilityLogoIds = get_header_utility_logo_ids();
                            foreach ($utilityLogoIds as $logoId) {
                                echo wp_get_attachment_image($logoId, [49, 38], false, ['alt' => 'Made in', 'class' => 'utility-logo__img']);
                            }
                            ?>
                        </div>
                    </div>

                    <!-- Utility Navigation -->
                    <div class="col">
                        <?php
                        wp_nav_menu(array(
                            'theme_location' => $header_nav_location,
                            'menu_id'        => 'header-nav-menu',
                            'container'      => 'div',
                            'container_id'   => 'header-submenu-nav',
                            'container_class'=> 'justify-content-end',
                            'menu_class'     => '',
                        ));
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Navigation (White Background) - Top Row -->
        <div class="header-main-nav">
            <div class="container-xxl">
                <div class="header-main-nav__inner">
                    <!-- Branding/Logo -->
                    <div class="navbar-brand">
                        <?php if ( get_theme_mod( 'wp_bootstrap_starter_logo' ) ): ?>
                            <a href="<?php echo esc_url( wj_logo_home_url() ); ?>" aria-label="nav-logo">
                                <img src="<?php echo esc_url(get_theme_mod( 'wp_bootstrap_starter_logo' )); ?>"
                                     alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
                            </a>
                        <?php else : ?>
                            <a class="site-title" href="<?php echo esc_url( wj_logo_home_url() ); ?>">
                                <?php echo esc_html( get_bloginfo('name') ); ?>
                            </a>
                        <?php endif; ?>
                    </div>

                    <!-- Main Navigation -->
                    <div class="header-main-nav__menu">
                        <!-- Mobile Menu Button -->
                        <button aria-label="Menu" id="mobile-menu-btn" class="d-lg-none">
                            <i class="fas fa-bars"></i>
                        </button>

                        <!-- Desktop + Mobile Menu -->
                        <div id="all-header-menu" class="d-none d-lg-block">
                            <!-- Primary Menu (mega menu) -->
                            <div id="main-nav">
                                <?php
                                wp_nav_menu(array(
                                    'theme_location'  => $primary_location,
                                    'container'       => 'div',
                                    'container_id'    => 'main-nav-container',
                                    'container_class' => 'collapse navbar-collapse justify-content-end',
                                    'menu_id'         => false,
                                    'menu_class'      => 'navbar-nav',
                                    'depth'           => 3,
                                    'fallback_cb'     => 'wp_bootstrap_navwalker::fallback',
                                    'walker'          => new wp_bootstrap_navwalker()
                                ));
                                ?>
                            </div>
                            <!-- Header-nav items (shown on mobile below primary menu) -->
                            <div id="mobile-header-nav">
                                <?php
                                wp_nav_menu(array(
                                    'theme_location'  => $header_nav_location,
                                    'menu_id'         => 'mobile-headernav-menu',
                                    'container'       => false,
                                    'menu_class'      => 'mobile-headernav-list',
                                    'depth'           => 2,
                                ));
                                ?>
                            </div>
                            <!-- CTA on mobile -->
                            <div id="mobile-cta">
                                <?php render_localized_header_ctas(); ?>
                            </div>
                        </div>
                    </div>

                    <!-- CTA Button -->
                    <div id="quote-btn">
                        <?php render_localized_header_ctas(); ?>
                    </div>
                </div>
            </div>
        </div>
    </header><!-- #masthead -->

    <div id="content" class="site-content">

    <?php
    // Display News Ticker on the global front page AND each locale homepage (e.g. /us/es/, /ca/fr/).
    $wj_path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
    $wj_is_locale_home = (bool) preg_match('#^/([a-z]{2})/([a-z]{2})/?$#i', $wj_path);
    if (is_front_page() || $wj_is_locale_home) {
        get_template_part('template-parts/ticker');
    }
    ?>
    <?php endif; ?>
