<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WP_Bootstrap_Starter
 */

?>
<?php if(!is_page_template( 'blank-page.php' ) && !is_page_template( 'blank-page-with-container.php' )): ?>

</div><!-- #content -->

<?php get_template_part( 'footer-widget' ); ?>
<footer id="colophon" class="site-footer <?php echo wp_bootstrap_starter_bg_class(); ?>" role="contentinfo">
	<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5P6ZD8G"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
  <div class="container pt-3 pb-3">
    <div class="row pt-4 justify-content-center">
        <div class="col-lg-2 text-center col-sm-4">
            <div class="logo text-center">
                <?php 
                $footerLogo = get_field('footer_logo', 'option');
                if( !empty( $footerLogo ) ): ?>
                    <a href="<?php echo get_home_url() ?>" aria-label="footer_logo">
                        <?php echo wp_get_attachment_image( $footerLogo, 'full' ); ?>
                    </a>
                <?php endif; ?>
            </div>

            <div class="links mt-4 text-center">
                <?php if( have_rows('footer_links', 'option') ): ?>
                    <?php while( have_rows('footer_links', 'option') ): the_row(); ?>
                        <?php 
                        $footer_link = get_sub_field('link');
                        if( $footer_link ): 
                            $footer_link_url = $footer_link['url'];
                            $footer_link_title = $footer_link['title'];
                            $footer_link_target = $footer_link['target'] ? $footer_link['target'] : '_self';
                            ?>
                            <span class="links">
                                <?=build_link($footer_link);?>
                            </span>
                        <?php endif; ?>
                    <?php endwhile; ?>
                    <br>
                <?php endif; ?>

                <?php if( have_rows('footer_social', 'option') ): ?>
                    <div class="social mt-4 mb-4 mb-md-0">
                    <?php while( have_rows('footer_social', 'option') ): the_row(); ?>
                        <?php 
                        $social_link = get_sub_field('url');
                        if( $social_link ): 
                            $social_link_url = $social_link['url'];
                            $social_link_title = $social_link['title'];
                            $social_link_target = $social_link['target'] ? $social_link['target'] : '_self';
                            ?>
                            <span class="social-icons">
                                <a href="<?php echo esc_url( $social_link_url ); ?>" aria-label="social-media" target="<?php echo esc_attr( $social_link_target ); ?>">
                                    <i class="<?=get_sub_field('icon_code')?>"></i>
                                </a>
                            </span>
                        <?php endif; ?>

                    <?php endwhile; ?>
                    </div>
                <?php endif; ?>
            </div>            

        </div>
        <div class="col-lg-10">
            <?php wp_nav_menu( array( 
                'theme_location' => 'footer-main', 
                'menu_id'=>'menu-footer-main',
                 ) ); ?>
        </div>
        
        <div class="col-lg-7 col-12 order-2 order-sm-1">
          <div class="site-info">
            <?php
            // Locale-aware headquarters: picks footer_headquarters variant for the
            // current locale (ACFML-stored es_/fr_/pl_ variants), falls back to default.
            $hq_field = function_exists('lc_pick_locale_field') ? lc_pick_locale_field('footer_headquarters') : 'footer_headquarters';
            $hq_rows  = get_field($hq_field, 'option');
            if ( ! empty($hq_rows) && is_array($hq_rows) ):
                foreach ( $hq_rows as $hq ): ?>
                 <p class="headquarters">

                    <?php echo esc_html( $hq['heading'] ?? '' ); ?>

                    <!-- start sub-repeater -->
                    <?php if ( ! empty($hq['description_block']) && is_array($hq['description_block']) ):
                        foreach ( $hq['description_block'] as $db ): ?>
                            <span><?php echo wp_kses_post( $db['description'] ?? '' ); ?></span>
                        <?php endforeach;
                    endif; ?>
                    <!-- end sub-repeater -->
                </p>
            <?php endforeach;
            endif; ?>

        <?php wp_nav_menu( array( 'theme_location' => 'footer-nav', 'menu_id'=>'menu-footer-nav') ); ?>  &copy;  <?php echo '<a href="'.home_url().'">'.get_bloginfo('name').'</a>'; ?> <?php echo date('Y'); ?> 
            </div><!-- close .site-info -->
        </div> 

        <div class="col-sm-5 col-12 rep-info order-1 order-sm-2">
            <?php $rep_field = function_exists('lc_pick_locale_field') ? lc_pick_locale_field('rep_info') : 'rep_info'; echo get_field($rep_field, 'option'); ?>
        </div>
    </div>
</div>
</footer><!-- #colophon -->
<?php endif; ?>
</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>