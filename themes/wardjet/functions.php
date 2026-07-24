<?php
/**
 * WP Bootstrap Starter functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WP_Bootstrap_Starter
 */

if ( ! function_exists( 'wp_bootstrap_starter_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function wp_bootstrap_starter_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on WP Bootstrap Starter, use a find and replace
	 * to change 'wp-bootstrap-starter' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'wp-bootstrap-starter', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => esc_html__( 'Primary', 'wp-bootstrap-starter' ),
        'header-nav' => esc_html__( 'header-nav', 'wp-bootstrap-starter' ),
        'footer-main' => esc_html__( 'footer-main', 'wp-bootstrap-starter' ),
        'footer-nav' => esc_html__( 'footer-nav', 'wp-bootstrap-starter' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'comment-form',
		'comment-list',
		'caption',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'wp_bootstrap_starter_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

    function wp_boostrap_starter_add_editor_styles() {
        add_editor_style( 'custom-editor-style.css' );
    }
    add_action( 'admin_init', 'wp_boostrap_starter_add_editor_styles' );

}
endif;
add_action( 'after_setup_theme', 'wp_bootstrap_starter_setup' );


/**
 * Add Welcome message to dashboard
 */
function wp_bootstrap_starter_reminder(){
        $theme_page_url = 'https://afterimagedesigns.com/wp-bootstrap-starter/?dashboard=1';

            if(!get_option( 'triggered_welcomet')){
                $message = sprintf(__( 'Welcome to WP Bootstrap Starter Theme! Before diving in to your new theme, please visit the <a style="color: #fff; font-weight: bold;" href="%1$s" target="_blank">theme\'s</a> page for access to dozens of tips and in-depth tutorials.', 'wp-bootstrap-starter' ),
                    esc_url( $theme_page_url )
                );

                printf(
                    '<div class="notice is-dismissible" style="background-color: #6C2EB9; color: #fff; border-left: none;">
                        <p>%1$s</p>
                    </div>',
                    $message
                );
                add_option( 'triggered_welcomet', '1', '', 'yes' );
            }

}
add_action( 'admin_notices', 'wp_bootstrap_starter_reminder' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function wp_bootstrap_starter_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'wp_bootstrap_starter_content_width', 1170 );
}
add_action( 'after_setup_theme', 'wp_bootstrap_starter_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function wp_bootstrap_starter_widgets_init() {
    register_sidebar( array(
        'name'          => esc_html__( 'Sidebar', 'wp-bootstrap-starter' ),
        'id'            => 'sidebar-1',
        'description'   => esc_html__( 'Add widgets here.', 'wp-bootstrap-starter' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );
    register_sidebar( array(
        'name'          => esc_html__( 'Footer 1', 'wp-bootstrap-starter' ),
        'id'            => 'footer-1',
        'description'   => esc_html__( 'Add widgets here.', 'wp-bootstrap-starter' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );
    register_sidebar( array(
        'name'          => esc_html__( 'Footer 2', 'wp-bootstrap-starter' ),
        'id'            => 'footer-2',
        'description'   => esc_html__( 'Add widgets here.', 'wp-bootstrap-starter' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );
    register_sidebar( array(
        'name'          => esc_html__( 'Footer 3', 'wp-bootstrap-starter' ),
        'id'            => 'footer-3',
        'description'   => esc_html__( 'Add widgets here.', 'wp-bootstrap-starter' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );
}
add_action( 'widgets_init', 'wp_bootstrap_starter_widgets_init' );


/**
 * Enqueue scripts and styles.
 */
function wp_bootstrap_starter_scripts() {
	// load bootstrap css
    if ( get_theme_mod( 'cdn_assets_setting' ) === 'yes' ) {
        wp_enqueue_style( 'wp-bootstrap-starter-bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css' );
        wp_enqueue_style( 'wp-bootstrap-starter-fontawesome-cdn', 'https://use.fontawesome.com/releases/v5.15.1/css/all.css' );
    } else {
        wp_enqueue_style( 'wp-bootstrap-starter-bootstrap-css', get_template_directory_uri() . '/inc/assets/css/bootstrap.min.css' );
        wp_enqueue_style( 'wp-bootstrap-starter-fontawesome-cdn', get_template_directory_uri() . '/inc/assets/css/fontawesome.min.css' );
    }
	// load bootstrap css
	// load AItheme styles
	// load WP Bootstrap Starter styles
	wp_enqueue_style( 'wp-bootstrap-starter-style', get_stylesheet_uri(), [], '2.3' );
    if(get_theme_mod( 'theme_option_setting' ) && get_theme_mod( 'theme_option_setting' ) !== 'default') {
        wp_enqueue_style( 'wp-bootstrap-starter-'.get_theme_mod( 'theme_option_setting' ), get_template_directory_uri() . '/inc/assets/css/presets/theme-option/'.get_theme_mod( 'theme_option_setting' ).'.css', false, '' );
    }
    if(get_theme_mod( 'preset_style_setting' ) === 'poppins-lora') {
        wp_enqueue_style( 'wp-bootstrap-starter-poppins-lora-font', 'https://fonts.googleapis.com/css?family=Lora:400,400i,700,700i|Poppins:300,400,500,600,700' );
    }
    if(get_theme_mod( 'preset_style_setting' ) === 'montserrat-merriweather') {
        wp_enqueue_style( 'wp-bootstrap-starter-montserrat-merriweather-font', 'https://fonts.googleapis.com/css?family=Merriweather:300,400,400i,700,900|Montserrat:300,400,400i,500,700,800' );
    }
    if(get_theme_mod( 'preset_style_setting' ) === 'poppins-poppins') {
        wp_enqueue_style( 'wp-bootstrap-starter-poppins-font', 'https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700' );
    }
        wp_enqueue_style( 'wp-bootstrap-starter-roboto-font', 'https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900,900i' );
    if(get_theme_mod( 'preset_style_setting' ) === 'arbutusslab-opensans') {
        wp_enqueue_style( 'wp-bootstrap-starter-arbutusslab-opensans-font', 'https://fonts.googleapis.com/css?family=Arbutus+Slab|Open+Sans:300,300i,400,400i,600,600i,700,800' );
    }
    if(get_theme_mod( 'preset_style_setting' ) === 'oswald-muli') {
        wp_enqueue_style( 'wp-bootstrap-starter-oswald-muli-font', 'https://fonts.googleapis.com/css?family=Muli:300,400,600,700,800|Oswald:300,400,500,600,700' );
    }
    if(get_theme_mod( 'preset_style_setting' ) === 'montserrat-opensans') {
        wp_enqueue_style( 'wp-bootstrap-starter-montserrat-opensans-font', 'https://fonts.googleapis.com/css?family=Montserrat|Open+Sans:300,300i,400,400i,600,600i,700,800' );
    }
    if(get_theme_mod( 'preset_style_setting' ) === 'robotoslab-roboto') {
        wp_enqueue_style( 'wp-bootstrap-starter-robotoslab-roboto', 'https://fonts.googleapis.com/css?family=Roboto+Slab:100,300,400,700|Roboto:300,300i,400,400i,500,700,700i' );
    }
    if(get_theme_mod( 'preset_style_setting' ) && get_theme_mod( 'preset_style_setting' ) !== 'default') {
        wp_enqueue_style( 'wp-bootstrap-starter-'.get_theme_mod( 'preset_style_setting' ), get_template_directory_uri() . '/inc/assets/css/presets/typography/'.get_theme_mod( 'preset_style_setting' ).'.css', false, '' );
    }
    //Color Scheme
    /*if(get_theme_mod( 'preset_color_scheme_setting' ) && get_theme_mod( 'preset_color_scheme_setting' ) !== 'default') {
        wp_enqueue_style( 'wp-bootstrap-starter-'.get_theme_mod( 'preset_color_scheme_setting' ), get_template_directory_uri() . '/inc/assets/css/presets/color-scheme/'.get_theme_mod( 'preset_color_scheme_setting' ).'.css', false, '' );
    }else {
        wp_enqueue_style( 'wp-bootstrap-starter-default', get_template_directory_uri() . '/inc/assets/css/presets/color-scheme/blue.css', false, '' );
    }*/

	wp_enqueue_script('jquery');

    // Internet Explorer HTML5 support
    wp_enqueue_script( 'html5hiv',get_template_directory_uri().'/inc/assets/js/html5.js', array(), '3.7.0', false );
    wp_script_add_data( 'html5hiv', 'conditional', 'lt IE 9' );

	// load bootstrap js
    if ( get_theme_mod( 'cdn_assets_setting' ) === 'yes' ) {
        wp_enqueue_script('wp-bootstrap-starter-popper', 'https://cdn.jsdelivr.net/npm/popper.js@1/dist/umd/popper.min.js', array(), '', true );
    	wp_enqueue_script('wp-bootstrap-starter-bootstrapjs', 'https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js', array(), '', true );
    } else {
        wp_enqueue_script('wp-bootstrap-starter-popper', get_template_directory_uri() . '/inc/assets/js/popper.min.js', array(), '', true );
        wp_enqueue_script('wp-bootstrap-starter-bootstrapjs', get_template_directory_uri() . '/inc/assets/js/bootstrap.min.js', array(), '', true );
    }
    wp_enqueue_script('wp-bootstrap-starter-themejs', get_template_directory_uri() . '/inc/assets/js/theme-script.min.js', array(), '', true );
	wp_enqueue_script( 'wp-bootstrap-starter-skip-link-focus-fix', get_template_directory_uri() . '/inc/assets/js/skip-link-focus-fix.min.js', array(), '20151215', true );

    wp_enqueue_script(
        'slick-carousel',
        "https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js", 
        array('jquery'),
        '0.2', // version number
        true // load in footer
    );
    wp_enqueue_style( 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.css' );
    wp_enqueue_style( 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.css' );


    wp_enqueue_style( 'magnific-css', get_template_directory_uri() . '/inc/assets/css/magnific-popup.css',[], '1.0' );
    wp_enqueue_script('magnific-js', get_template_directory_uri().'/inc/assets/js/jquery.magnific-popup.min.js', array(), '1.0', true);


    // Montserrat font (weights 300-700, incl. 600). Theme CSS uses 'Montserrat'
    // everywhere; without this enqueue it silently falls back to Arial. (Blueprint parity.)
    wp_enqueue_style( 'montserrat-font', 'https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap' );

    //custom css
    wp_enqueue_style( 'custom-css', get_template_directory_uri() . '/inc/assets/css/wardjet-custom.css',[], time() );
    // Header nav styles (Figma two-row header, ported from blueprint). Depends on
    // custom-css so it loads LAST and overrides wardjet-custom.css + Max Mega Menu.
    wp_enqueue_style( 'header-css', get_template_directory_uri() . '/inc/assets/css/parts/header.css', ['custom-css'], time() );
    // Home video hero (ported from blueprint).
    wp_enqueue_style( 'hero-video-carousel-css', get_template_directory_uri() . '/inc/assets/css/parts/hero-video-carousel.css', [], time() );
    // News ticker (ported from blueprint).
    wp_enqueue_style( 'ticker-css', get_template_directory_uri() . '/inc/assets/css/parts/ticker.css', [], time() );
    // Features slider (ported from blueprint).
    wp_enqueue_style( 'features-slider-css', get_template_directory_uri() . '/inc/assets/css/parts/features-slider.css', [], time() );
    // Icon strip (ported from blueprint).
    wp_enqueue_style( 'icon-strip-css', get_template_directory_uri() . '/inc/assets/css/parts/icon-strip.css', [], time() );
    // Products section (grouped: Abrasive / Water Only).
    wp_enqueue_style( 'products-section-css', get_template_directory_uri() . '/inc/assets/css/parts/products-section.css', [], time() );
    // KPIs stats band (ported from blueprint).
    wp_enqueue_style( 'new-kpis-css', get_template_directory_uri() . '/inc/assets/css/parts/new-kpis.css', [], time() );
    // Industries grid (materials removed for wardjet).
    wp_enqueue_style( 'ind-mat-grid-css', get_template_directory_uri() . '/inc/assets/css/parts/ind-mat-grid.css', [], time() );
    // Our Companies (ported 1:1 from blueprint).
    wp_enqueue_style( 'our-companies-css', get_template_directory_uri() . '/inc/assets/css/parts/our-companies.css', [], time() );
    // Partnerships logo carousel (ported from blueprint).
    wp_enqueue_style( 'partnerships-css', get_template_directory_uri() . '/inc/assets/css/parts/partnerships.css', [], time() );
    // Locations page (ported from blueprint).
    wp_enqueue_style( 'locations-css', get_template_directory_uri() . '/inc/assets/css/parts/locations.css', [], time() );
    // Contact section + footer styles (server-side additions; keep in repo so deploys don't drop them).
    wp_enqueue_style( 'contact-css', get_template_directory_uri() . '/inc/assets/css/parts/contact.css', [], time() );
    wp_enqueue_style( 'footer-css', get_template_directory_uri() . '/inc/assets/css/parts/footer.css', [], time() );
    // Single product (series) sections — ported from blueprint (Figma 18:5482).
    wp_enqueue_style( 'video-carousel-css', get_template_directory_uri() . '/inc/assets/css/parts/video-carousel.css', [], time() );
    wp_enqueue_style( 'router-renders-css', get_template_directory_uri() . '/inc/assets/css/parts/router-renders.css', [], time() );
    wp_enqueue_style( 'feature-block-css', get_template_directory_uri() . '/inc/assets/css/parts/feature-block.css', [], time() );
    wp_enqueue_style( 'crossbeam-css', get_template_directory_uri() . '/inc/assets/css/parts/crossbeam.css', ['feature-block-css'], time() );
    wp_enqueue_style( 'router-features-strip-css', get_template_directory_uri() . '/inc/assets/css/parts/router-features-strip.css', [], time() );
    wp_enqueue_style( 'selected-products-css', get_template_directory_uri() . '/inc/assets/css/parts/selected-products.css', [], time() );
    wp_enqueue_style( 'competitive-chart-css', get_template_directory_uri() . '/inc/assets/css/parts/competitive-chart.css', [], time() );
    wp_enqueue_style( 'series-gallery-css', get_template_directory_uri() . '/inc/assets/css/parts/series-gallery.css', [], time() );
    wp_enqueue_style( 'brochure-section-css', get_template_directory_uri() . '/inc/assets/css/parts/brochure-section.css', [], time() );
    wp_enqueue_script('wowjs', get_template_directory_uri().'/inc/assets/js/wow.min.js', array(), '', true);
    //custom js

    wp_enqueue_script('hotspot', get_template_directory_uri().'/inc/assets/js/jquery.hotspot.js', array(), '', true);
    wp_enqueue_script('wardjet-custom-js', get_template_directory_uri() . '/inc/assets/js/wardjet-custom.js', array( 'jquery' ), '
        1.9.8', true );
    // Partnerships carousel (standalone; ported from blueprint).
    wp_enqueue_script('wardjet-partnerships-js', get_template_directory_uri() . '/inc/assets/js/parts/partnerships.js', array( 'jquery' ), '1.0.0', true );

    // News ticker AJAX loader (hybrid: server-render + REST refresh). Ported from blueprint.
    wp_enqueue_script('wardjet-ticker-js', get_template_directory_uri() . '/inc/assets/js/ticker.js', array(), '1.0.0', true);
    wp_localize_script('wardjet-ticker-js', 'wjTicker', array(
        'endpoint' => esc_url_raw( rest_url('wardjet/v1/ticker') ),
    ));

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'wp_bootstrap_starter_scripts' );



/**
 * Add Preload for CDN scripts and stylesheet
 */
function wp_bootstrap_starter_preload( $hints, $relation_type ){
    if ( 'preconnect' === $relation_type && get_theme_mod( 'cdn_assets_setting' ) === 'yes' ) {
        $hints[] = [
            'href'        => 'https://cdn.jsdelivr.net/',
            'crossorigin' => 'anonymous',
        ];
        $hints[] = [
            'href'        => 'https://use.fontawesome.com/',
            'crossorigin' => 'anonymous',
        ];
    }
    return $hints;
} 

add_filter( 'wp_resource_hints', 'wp_bootstrap_starter_preload', 10, 2 );



function wp_bootstrap_starter_password_form() {
    global $post;
    $label = 'pwbox-'.( empty( $post->ID ) ? rand() : $post->ID );
    $o = '<form action="' . esc_url( home_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" method="post">
    <div class="d-block mb-3">' . __( "To view this protected post, enter the password below:", "wp-bootstrap-starter" ) . '</div>
    <div class="form-group form-inline"><label for="' . $label . '" class="mr-2">' . __( "Password:", "wp-bootstrap-starter" ) . ' </label><input name="post_password" id="' . $label . '" type="password" size="20" maxlength="20" class="form-control mr-2" /> <input type="submit" name="Submit" value="' . esc_attr__( "Submit", "wp-bootstrap-starter" ) . '" class="btn btn-primary"/></div>
    </form>';
    return $o;
}
add_filter( 'the_password_form', 'wp_bootstrap_starter_password_form' );



/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load plugin compatibility file.
 */
require get_template_directory() . '/inc/plugin-compatibility/plugin-compatibility.php';

/**
 * Load custom WordPress nav walker.
 */
if ( ! class_exists( 'wp_bootstrap_navwalker' )) {
    require_once(get_template_directory() . '/inc/wp_bootstrap_navwalker.php');
}

/**
 * Home video hero — ACF field group (video_carousel repeater). Ported from blueprint.
 */
require_once(get_template_directory() . '/inc/acf-hero-video-carousel.php');

/**
 * News ticker — backend logic (items/date+locale filters, REST route, admin
 * settings page + list column) and ACF meta (ticker_until, display locales).
 * Ported from blueprint.
 */
require_once(get_template_directory() . '/inc/ticker-functions.php');
require_once(get_template_directory() . '/inc/acf-ticker-meta.php');

/**
 * Features slider — ACF field group (title/subtitle + items repeater). Ported from blueprint.
 */
require_once(get_template_directory() . '/inc/acf-features-slider.php');

/**
 * Products section — ACF field group (title/subtitle/CTA + icon-strip items).
 * Ported from blueprint (location: products page template).
 */
require_once(get_template_directory() . '/inc/acf-products-section.php');

/**
 * Our Companies — ACF field group (companies repeater). Ported 1:1 from blueprint.
 */
require_once(get_template_directory() . '/inc/acf-our-companies.php');

/**
 * Contact section extras — ACF field group for the localized contact/location card
 * (contact_localized: location card + Global Presence). Server-side addition; kept
 * in the repo so future functions.php deploys don't drop it.
 */
require_once(get_template_directory() . '/inc/acf-contact-extra.php');

/**
 * Locations page — ACF field group + render hooks (ported from blueprint).
 * Locale locations pages: en-us 11850, en-ca 12678, en-uk 12728,
 * es-us 13241, fr-ca 13243, pl-pl 13245.
 */
require_once(get_template_directory() . '/inc/acf-locations.php');

/**
 * Industry CPT fields — `industry_features` (hover list on the homepage grid)
 * + `grid_image`. Ported from the blueprint's acf-industries.php.
 */
require_once(get_template_directory() . '/inc/acf-industries.php');

/**
 * Single product (series) — ACF field groups (ported from blueprint).
 * acf-router-renders: `router_renders` gallery + `features_show_numbers` (retargeted to the `series` CPT).
 * acf-product-specs:  `product_display_mode` + `product_specs` on the `products` CPT (spec cards).
 */
require_once(get_template_directory() . '/inc/acf-router-renders.php');
require_once(get_template_directory() . '/inc/acf-product-specs.php');

if (!function_exists('wj_locations_page_ids')) {
    function wj_locations_page_ids() {
        return array(11850, 12678, 12728, 13241, 13243, 13245);
    }
}

// Render locations sections (+ contact) before page content on locations pages.
add_filter('the_content', function ($content) {
    if (!is_page() || !in_the_loop() || !is_main_query()) { return $content; }
    if (!in_array(get_the_ID(), wj_locations_page_ids(), true)) { return $content; }
    ob_start();
    get_template_part('template-parts/locations-sections');
    get_template_part('template-parts/agg-contact');
    $html = ob_get_clean();
    if (trim($content)) {
        $content = '<div class="locations-legacy-content" style="display:none !important;" aria-hidden="true">' . $content . '</div>';
    }
    return $html . $content;
});

// page-locations body class.
add_filter('body_class', function ($classes) {
    if (is_page() && in_array(get_the_ID(), wj_locations_page_ids(), true)) {
        $classes[] = 'page-locations';
    }
    return $classes;
});

// Hide the default page title on locations pages.
add_filter('the_title', function ($title, $id = null) {
    if (!is_admin() && is_page() && in_the_loop() && is_main_query()
        && in_array(get_the_ID(), wj_locations_page_ids(), true)) {
        return '';
    }
    return $title;
}, 10, 2);




if( function_exists('acf_add_options_page') ) {
    
    acf_add_options_page(array(
        'page_title'    => 'Theme General Settings',
        'menu_title'    => 'Theme Settings',
        'menu_slug'     => 'theme-general-settings',
        'capability'    => 'edit_posts',
        'redirect'      => false
    ));
    
    acf_add_options_sub_page(array(
        'page_title'    => 'Theme Header Settings',
        'menu_title'    => 'Header',
        'parent_slug'   => 'theme-general-settings',
    ));
    
    acf_add_options_sub_page(array(
        'page_title'    => 'Theme Footer Settings',
        'menu_title'    => 'Footer',
        'parent_slug'   => 'theme-general-settings',
    ));
    
}

function move_yoast_below_acf() {
    return 'low';
}
add_filter( 'wpseo_metabox_prio', 'move_yoast_below_acf');

//$link comes from ACF
function build_link($link){
    if (!$link || !$link['url'])
        return '';

    return '<a href="'.esc_url( $link['url'] ).'" target="'.esc_attr( $link['target'] ).'">'.esc_html( $link['title'] ).'</a>';
}



function my_acf_field_checkbox( $field ){
    
    global $wpdb;
    $querystr = "SELECT * FROM {$wpdb->posts} WHERE post_type = 'industry' ORDER BY post_title";
    $my_cpts = $wpdb->get_results($querystr, OBJECT);

    $my_cpt_arr = array();

    if($my_cpts){
        foreach($my_cpts as $my_cpt):
            $my_cpt_arr[$my_cpt->ID] = $my_cpt->post_title;
        endforeach;
    }
    $field['choices'] = $my_cpt_arr;

    return $field;
};

add_filter('acf/load_field/name=industries_list', 'my_acf_field_checkbox');


if ( ! function_exists( '_custom_nav_menu_item' ) ) {
function _custom_nav_menu_item( $title, $url, $order, $parent = 0 ){
  $item = new stdClass();
  $item->ID = 1000000 + $order + $parent;
  $item->db_id = $item->ID;
  $item->title = $title;
  $item->url = $url;
  $item->menu_order = $order;
  $item->menu_item_parent = $parent;
  $item->type = '';
  $item->object = '';
  $item->object_id = '';
  $item->classes = array();
  $item->target = '';
  $item->attr_title = '';
  $item->description = '';
  $item->xfn = '';
  $item->status = '';
  return $item;
}
}

add_shortcode('wj_megamenu', 'my_megamenu');
function my_megamenu()
{
    ob_start();
    include('megamenu-content.php');
    return ob_get_clean();

}


// if a video is present it'll display it, otherwise display the static image
function display_series_product_image($image, $video, $link=null)
{
    if ($link == null){
        $link = get_the_permalink();
    } 

    echo '<a href="'.$link.'">';
    if ($video){
        echo '<video class="embed-responsive embed-responsive-16by" autoplay loop>';
        echo '<source src="'.$video['url'].'" type="video/mp4">';
        echo '</video>';
    }
    else
    {
        echo '<img src="'.$image['url'].'"/>';
    }
    echo '</a>';        
}

function display_series_image()
{
    display_series_product_image(get_field('series_image'), get_field('series_animated_image'));
}

function display_product_image($link=null)
{
    display_series_product_image(get_field('product_image'), get_field('product_animated_image'), $link);  
}

function add_geo_info_to_form($entry, $form)
{
    $ip = $_SERVER['REMOTE_ADDR'];
    $x = file_get_contents("http://ip-api.com/json/".$ip); 
    $data = json_decode($x, true);
    if ($form->id == 1)
    {
        $_POST['input_3'] = $data['country'];
        $_POST['input_4'] = $data['city'];        
    }
    else
    {
        $_POST['input_8'] = $data['country'];
        $_POST['input_9'] = $data['city'];         
    }
}
add_action( 'gform_pre_submission_1', 'add_geo_info_to_form', 10, 2 );
add_action( 'gform_pre_submission_3', 'add_geo_info_to_form', 10, 2 );

function get_youtube_embed_url($url)
{
    if (strpos($url, 'embed') === FALSE)
    {
        //figure out the video code
        if (preg_match('#(.+)youtube.com/watch\?v=(.+)#', $url, $matches))
        {
            //https://www.youtube.com/watch?v=EJV7aG5Ga1M
            $video_code = $matches[2];
            $url = 'https://www.youtube.com/embed/'.$video_code;
        }

        if (preg_match('#(.+)youtu.be/(.+)#', $url, $matches))
        {
            //https://youtu.be/EJV7aG5Ga1M
            $video_code = $matches[2];
            $url = 'https://www.youtube.com/embed/'.$video_code;
        }
    }  

    return $url;  
}

function get_youtube_icon($url)
{
    if (strpos($url, 'embed') === FALSE)
    {
        $url = get_youtube_embed_url($url);
    }

    $code = str_replace('https://www.youtube.com/embed/', '', $url);
    return 'http://img.youtube.com/vi/'.$code.'/hqdefault.jpg';
}

add_post_type_support( 'testimonial', 'excerpt' );

/**
 * Normalize malformed doubled-path URLs with a 301 before other redirects run.
 * Ported verbatim from the blueprint. Only fires on impossible-in-real-URLs
 * shapes (doubled locale prefix, doubled /industry/, axyz /section/construction/);
 * legitimate URLs are untouched (early return when nothing changed). No deps.
 */
function axyz_normalize_broken_paths_before_redirects() {
    if ( is_admin() ) {
        return;
    }

    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    $path        = parse_url( $request_uri, PHP_URL_PATH );
    $query       = parse_url( $request_uri, PHP_URL_QUERY );

    if ( ! is_string( $path ) ) {
        return;
    }

    $original_path = $path;

    /*
     * 1) Collapse double locale prefix
     *    /us/es/us/es/folletos  -> /us/es/folletos
     *    /ca/fr/ca/fr/brochures -> /ca/fr/brochures
     */
    $path = preg_replace(
        '#^/([a-z]{2}/[a-z]{2})/\1(?=/|$)#i',
        '/$1',
        $path
    );

    /*
     * 2) Collapse /industry/{anything}/industry/{slug}
     *    /industry/woods/industry/woodworking
     *    /us/en/industry/woods/industry/woodworking
     *    -> /industry/woodworking or /us/en/industry/woodworking
     */
    $path = preg_replace(
        '#^(/(?:[a-z]{2}/[a-z]{2}/)?)industry/[^/]+/industry/#i',
        '$1industry/',
        $path
    );

    /*
     * 3) Collapse /section/construction/section/construction/...
     *    -> /section/construction/...  (axyz-only shape; no-op on WARDJET)
     */
    $path = preg_replace(
        '#^(/(?:[a-z]{2}/[a-z]{2}/)?)(section/construction)/\2(?=/|$)#i',
        '$1$2',
        $path
    );

    // If nothing changed, do nothing.
    if ( $path === $original_path ) {
        return;
    }

    // Rebuild target URL (keep query string if present).
    $target = home_url( $path );
    if ( $query ) {
        $target .= '?' . $query;
    }

    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        error_log( '[AXYZ URL NORMALIZER] ' . $original_path . '  =>  ' . $path );
    }

    wp_redirect( $target, 301 );
    exit;
}
// Run very early, before Rank Math / wp_old_slug_redirect.
add_action( 'template_redirect', 'axyz_normalize_broken_paths_before_redirects', 1 );

/**
 * Analytics/tag output is allowed only on the production host(s). On staging/dev
 * (e.g. wardjet.pixels2pixels.ch) GTM + GA4 are suppressed so dev traffic never
 * pollutes production Google Analytics / Tag Manager. Auto-enables on production —
 * no manual toggle. If the production domain differs, edit $prod_hosts (or hook
 * the 'wj_tracking_allowed' filter).
 */
if ( ! function_exists( 'wj_tracking_allowed' ) ) {
    function wj_tracking_allowed() {
        $prod_hosts = array( 'wardjet.com', 'www.wardjet.com' );
        $host = strtolower( preg_replace( '/:\d+$/', '', (string) ( $_SERVER['HTTP_HOST'] ?? '' ) ) );
        return (bool) apply_filters( 'wj_tracking_allowed', in_array( $host, $prod_hosts, true ), $host );
    }
}
// Off-production: suppress every analytics/marketing tag so dev traffic never
// reaches the production properties.
if ( ! wj_tracking_allowed() ) {
    // Google Site Kit — GA4 + Tag Manager tag output (filters, checked at render).
    add_filter( 'googlesitekit_analytics-4_tag_blocked', '__return_true' );
    add_filter( 'googlesitekit_tagmanager_tag_blocked', '__return_true' );

    // lead-forensics-roi injects its own script + noscript; WPCode global
    // Header/Body/Footer output carries enhanced-conversion / dataLayer snippets.
    // These register at/after plugin load, so strip the callbacks from inside
    // each target hook at priority 0 — just before they fire.
    add_action( 'wp_head', function () {
        remove_action( 'wp_head', 'wpcode_global_frontend_header', 10 );
        remove_action( 'wp_head', 'lfv2_inject_script', 1 );
    }, 0 );
    add_action( 'wp_body_open', function () {
        remove_action( 'wp_body_open', 'wpcode_global_frontend_body', 1 );
        remove_action( 'wp_body_open', 'lfv2_inject_noscript', 1 );
    }, 0 );
    add_action( 'wp_footer', function () {
        remove_action( 'wp_footer', 'wpcode_global_frontend_footer', 10 );
    }, 0 );
}