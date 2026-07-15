<?php
/**
 * Single product (series) template.
 *
 * Ported from the blueprint's single-routers.php and mapped onto wardjet's
 * `series` CPT. Order follows the Figma "Single Product_X-Series" (18:5482):
 *   promo-video hero -> title/subtitle + 3D renders carousel -> content blocks
 *   (feature rows, 3-step band, video, spec cards, features & benefits,
 *   gallery, brochure) -> Get in Touch.
 *
 * The `main_banner` block is intentionally NOT rendered: the renders-carousel
 * shows the title and borrows main_banner's subtitle. Sub-routers are omitted —
 * wardjet's series are flat (no parent/child) and Figma has no such section.
 */

get_header();

$blocks = get_field('content');
?>
<div id="product">

    <?php
    // Promo-video hero when a video_carousel is set; otherwise fall back to the
    // main_banner background image rendered as a static hero.
    if (have_rows('video_carousel')) :
        get_template_part('template-parts/hero-video-carousel');
    elseif (is_array($blocks)) :
        foreach ($blocks as $block) :
            if ($block['acf_fc_layout'] !== 'main_banner' || empty($block['background_image'])) {
                continue;
            }
            $bg = is_array($block['background_image'])
                ? $block['background_image']['url']
                : wp_get_attachment_url($block['background_image']);
            $title = $block['title'] ?? '';
            ?>
            <section class="hero-video-carousel">
                <div class="site-main" role="main">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <div class="video-container" style="background-image: url('<?php echo esc_url($bg); ?>'); background-size: cover; background-position: center;">
                                <div class="video-dark-overlay"></div>
                                <?php if ($title) : ?>
                                    <div class="video-overlay">
                                        <h2 class="video-title"><?php echo esc_html($title); ?></h2>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <?php
            break;
        endforeach;
    endif;
    ?>

    <?php
    // Product title + subtitle + 3D renders carousel (replaces the main_banner block).
    get_template_part('template-parts/router-renders-carousel');

    // Figma 18:5482 has no features-strip between the feature rows and the
    // 3-step band, so (unlike the blueprint's router pages) none is injected.
    if (is_array($blocks)) :
        foreach ($blocks as $block) :
            $layout = $block['acf_fc_layout'];

            // Rendered by the renders-carousel above.
            if ($layout === 'main_banner') {
                continue;
            }

            get_template_part('template-parts/section', $layout, ['block' => $block]);
        endforeach;
    endif;
    ?>

    <?php get_template_part('template-parts/agg-contact'); ?>

</div>
<?php
wp_reset_postdata();
get_footer();
