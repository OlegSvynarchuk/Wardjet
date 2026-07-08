<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package WP_Bootstrap_Starter
 */

get_header(); ?>

	<section id="primary" class="content-area">
		<div id="main" class="site-main" role="main">
			<div class="container">
				<div class="row">
					<div class="col-12">
						<header class="page-header">
							<h1 class="page-title"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'wp-bootstrap-starter' ); ?></h1>
						</header><!-- .page-header -->

						<div class="page-content">
							<p><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try one of the links below', 'wp-bootstrap-starter' ); ?></p>
						</div><!-- .page-content -->
					</div>
				</div>
			</div>
		</div><!-- #main -->
	</section><!-- #primary -->

<?php
get_footer();
