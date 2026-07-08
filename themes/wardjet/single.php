<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WP_Bootstrap_Starter
 */

get_header(); ?>
	<section id="primary" class="content-area single-news-event" style="margin-top:50px;">
		<div id="main" class="site-main" role="main">
			<div class="container" style="margin: auto;padding:0px;">
<div class="col-sm-12 col-lg-8" style="margin: auto;padding:0px;">

		<?php if (isset($_SERVER['HTTP_REFERER'])) : ?>
    <a class="back" href="<?php echo esc_url($_SERVER['HTTP_REFERER']); ?>">Back to Learn</a>
								<?php endif; ?></div>
				<div class="row">
					<div class="col-sm-12 col-lg-8" style="margin: auto;">
						<?php
						while ( have_posts() ) : the_post();

							get_template_part( 'template-parts/content', get_post_format() );


						endwhile; // End of the loop.
						?>
					</div>
				</div>
			</div>
		</div><!-- #main -->
	</section><!-- #primary -->

<?php
get_footer();
