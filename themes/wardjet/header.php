<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WP_Bootstrap_Starter
 */

global $body_style;

/*
 * Language switcher + search box on header-nav menus are now provided by the
 * wj-multilingual plugin (includes/menu.php :: custom_nav_menu_items), which is
 * locale-aware and replaces the old WPML-based version that lived here.
 */

/*
 * Logo link target = current locale's home.
 * en-us → "/" (real site root); every other locale → "/{cc}/{ll}/".
 * (Ported from the blueprint header's wj_logo_home_url().)
 */
if ( ! function_exists( 'wj_logo_home_url' ) ) {
    function wj_logo_home_url(): string {
        $path = parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH ) ?: '/';
        if ( preg_match( '#^/([a-z]{2})/([a-z]{2})(?:/|$)#i', $path, $m ) ) {
            $region = strtolower( $m[1] );
            $lang   = strtolower( $m[2] );
            $code   = $lang . '-' . $region; // en-us, fr-ca, es-us, pl-pl, ...
            if ( $code === 'en-us' ) {
                return home_url( '/' );
            }
            if ( function_exists( 'lc_locale_to_prefix' ) ) {
                return home_url( '/' . lc_locale_to_prefix( $code ) . '/' );
            }
            return home_url( '/' . $region . '/' . $lang . '/' );
        }
        return home_url( '/' );
    }
}


?><!DOCTYPE html>
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

<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'wp-bootstrap-starter' ); ?></a>
    <?php if(!is_page_template( 'blank-page.php' ) && !is_page_template( 'blank-page-with-container.php' )): ?>
	<header id="masthead" class="site-header navbar-static-top <?php echo wp_bootstrap_starter_bg_class(); ?>" role="banner">
        <div class="container-xxl">
            <div class="row align-items-center no-gutters">
                <div class="col-sm-1 col-12 text-center text-sm-left">
                    <?php 
                    $headerIco = get_field('header_icon', 'option');
                    $icoSize = 'full'; // (thumbnail, medium, large, full or custom size)
                    if( $headerIco ) {
                        echo wp_get_attachment_image( $headerIco, array(60, 60));
                    } 
                    ?> 
                </div>
                <div class="col-sm-2 col-12 text-center text-sm-left">
                    <div class="navbar-brand">
                        <?php if ( get_theme_mod( 'wp_bootstrap_starter_logo' ) ): ?>
                            <a href="<?php echo esc_url( wj_logo_home_url() ); ?>" aria-label="nav-logo">
                                <img src="<?php echo esc_url(get_theme_mod( 'wp_bootstrap_starter_logo' )); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" style="min-width:240px">
                            </a>
                        <?php else : ?>
                            <a class="site-title" href="<?php echo esc_url( wj_logo_home_url() ); ?>"><?php esc_url(bloginfo('name')); ?></a>
                        <?php endif; ?>

                    </div>
                </div>
                <div class="col-sm-6 col-12 position-relative ">
                    <div class="row d-sm-none no-gutters">
                        <div class="col-12  text-center">
                          <button aria-label="Menu" id="mobile-menu-btn">
                            <i class="fas fa-bars"></i>
                          </button>
                        </div>
                    </div>

                    <div id="all-header-menu" class="d-none d-sm-block">
                        <div class="row align-items-between justify-content-end no-gutters">
                            <div class="col-lg-12 order-2 order-sm-1 ">
                                <?php
                                wp_nav_menu(array(
                                'theme_location' => 'header-nav',
                                'menu_id'         => 'header-nav-menu',
                                'container'       => 'div',
                                'container_id'    => 'header-submenu-nav',
                                'container_class' => 'justify-content-end',     
                                'menu_class'      => 'navbar-nav',
                                ));
                                ?>
                            </div>                           
   <!--                                           
                            <span class="">
                                <a href="#" aria-label="Search Icon Link" id="search-icon">
                                <svg width="20" height="20" class="search-icon" role="img" viewBox="2 9 20 5" focusable="false" aria-label="Search" fill="#003b71">
                                   <path class="search-icon-path" d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" color="#003b71"></path></svg>
                               </a>    
                            </span> -->
                            <div class="col-lg-12 order-1 order-sm-2">
                                <div id="main-nav">
                                    <?php
                                    wp_nav_menu(array(
                                    'theme_location'    => 'primary',
                                    'container'       => 'div',
                                    'container_id'    => 'main-nav',
                                    'container_class' => 'collapse navbar-collapse justify-content-end',
                                    'menu_id'         => false,
                                    'menu_class'      => 'navbar-nav',
                                    'depth'           => 3,
                                    'fallback_cb'     => 'wp_bootstrap_navwalker::fallback',
                                    'walker'          => new wp_bootstrap_navwalker()
                                    ));
                                    ?>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
            <div class="col-sm-3 col-12 text-center text-sm-right pt-4 pt-sm-0 pb-4 pb-sm-0" id="quote-btn">

                <?php 
                while(have_rows('links', 'option')):
                    the_row();
                    echo build_link(get_sub_field('url'));
                ?>

                <?php 
                endwhile;
                ?>
            </div>
        </div>
    </div>
	</header><!-- #masthead -->

<div id="content" class="site-content">
		
<?php endif; ?>