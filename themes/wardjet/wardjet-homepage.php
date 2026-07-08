<?php
/**
* Template Name: Homepage
 */

get_header(); ?>
<div id="homepage">

	<?php get_template_part('template-parts/agg-mainhero');?>

	<?php 
	$industries_image = get_field('industries_bg_image');
	?>
	<section id="" class="content-area industries">
		<div id="" class="site-main" role="main">
			<div class="container ">
				<div class="row justify-content-center">
					<div class="col-lg-12 text-center">

						<h2 class="heading"><?=get_field('industries_heading')?></h2>

						<?=get_field('industries_copy')?>

					   <div class="row no-gutters justify-content-center align-items-center row-eq-height">
						<?php 
						//get all industries
						//order by name

						$wj_locale = function_exists('lc_get_locale_from_url') ? lc_get_locale_from_url() : 'en-us';
						$args = array(
							'post_type' => 'industry',
							'post_status' => 'publish',
							'posts_per_page' => 100,
							'orderby' => 'title',
							'order' => 'ASC',
							'meta_query' => array(
								array( 'key' => 'region_language_code', 'value' => $wj_locale, 'compare' => '=' )
							),
						);

						$loop = new WP_Query( $args );
						// Alias/untranslated locales: fall back to en-us industries if none for this locale
						if ( ! $loop->have_posts() && $wj_locale !== 'en-us' ) {
							$args['meta_query'] = array(
								array( 'key' => 'region_language_code', 'value' => 'en-us', 'compare' => '=' )
							);
							$loop = new WP_Query( $args );
						}

						while ( $loop->have_posts() ) : 
							$loop->the_post(); 
							$img = get_field('mosaic_icon');
						?>
							<div class="col-6 col-sm-4 col-industry" style="flex-grow: 1;">
								<div class="industry-icon position-relative">
									<a href="<?=get_permalink()?>" aria-label="<?=get_the_title()?>">
										<figure class="position-relative" style="margin:0 auto">
										 <img class="img-fluid" src="<?php echo esc_url($img); ?>" alt="" />
											<figcaption>
												<div>
													<p class="industry-icon-title"><b><?=get_the_title()?></b></p>
												</div>
											</figcaption>
										</figure>
									</a>
								</div>
							</div>
						<?php 
						endwhile;
						wp_reset_postdata();
						?>
						</div>
					</div>
				</div>
			</div>
		</div><!-- #main -->
	</section><!-- #primary -->

	<?php get_template_part('template-parts/kpis');?>

	<section class="content-area products">
		<div id="main" class="site-main" role="main">
			<div class="container">
				<div class="row wow animate__fadeIn">
					<div class="col-lg-8  mt-5 pt-4 mx-auto text-center">
						<h2 class="heading"><?php the_field('product_heading'); ?></h2>
					</div>
					<div class="col-lg-8 mx-auto text-center">
						<p><?php the_field('product_copy'); ?></p>
					</div>
				</div>
			</div>
		</div><!-- #main -->
	</section><!-- #primary -->



	<section  class="content-area products-listing">
		<div id="main" class="site-main" role="main">
			<div class="container">
				<div class="row">
					<div class="col-lg-12"><!-- query area -->
						<?php
						$cta_label = get_field('cta_label_listing');
						$terms = get_terms( array(
							'taxonomy' => 'category',
							'orderby' => 'description',
							'order' => 'ASC',
							'hide_empty' => false
						) );

						if (!empty($terms) && ! is_wp_error( $terms )) {

							foreach ($terms as $term) {
								$term_name = $term->name;
								/* category query start */  
								?>
								<div class="wrapper">
									<?php
									$category = $term->name;
									$wj_locale = function_exists('lc_get_locale_from_url') ? lc_get_locale_from_url() : 'en-us';
									// Aliasable locales (en-ca, en-uk) have no series of their own → show en-us
									if ( in_array($wj_locale, array('en-ca','en-uk'), true) ) $wj_locale = 'en-us';
									$args = array(
										'post_type'   => 'series',
										'cat' => $term->term_id,
										'posts_per_page' => 3,
										'post_status' => 'publish',
										'order' => 'DESC',
										'meta_key' => 'order',
										'orderby' => 'meta_value',
										'meta_query' => array(
											array( 'key' => 'region_language_code', 'value' => $wj_locale, 'compare' => '=' )
										)
									);
									$wj_products = new WP_Query( $args );
									if( $wj_products->have_posts() ) :
										$column_class = ($wj_products->post_count<3)?'col-12 d-md-flex col-sm-12':'col-12 col-sm-4';
										$row_class = ($wj_products->post_count>=3)?"prods-vertical":"prods-horizontal";
										?>
										<h3 class="product_category text-center text-sm-left"><?=$term->name?></h3>
										<div class="row <?=$row_class?>">
											<?php
											while( $wj_products->have_posts() ) :
												$wj_products->the_post();
												?>
												<div class="<?=$column_class?> prods-item  wow animate__fadeIn">
													<div class="content-image text-center text-sm-left">
														<?php 
														display_series_image();
														?>
													</div>
													<div class="content-text text-center text-sm-left">
														<h3 class="title text-center text-sm-left">
															<?php the_title(); ?>
														</h3>
														<p class=""><?php the_field('series_short_description') ?></p>
														<p class="cta">
															<a href="<?php the_permalink(); ?>"><?=$cta_label?></a>
														</p>
													</div>
												</div>
												<?php
											endwhile;
											wp_reset_postdata();
											?>
										</div>

										<?php
									else :
										esc_html_e( ' ', '' );
									endif;
									?>
								</div>
								<?php
								/* category query end */
							}  
						} ?>
					</div><!-- query area -->
				</div>
			</div>
		</div><!-- #main -->
	</section><!-- #primary -->

	<!-- #video	
	<section class="content-area dark pt-5" style="background: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(23,60,109,1) 50%, rgba(23,60,109,1) 100%), url(<?=get_field('support_image')['url']?>) no-repeat top left; ">
		<div class="container">
			<div class="row justify-content-end wow animate__fadeIn">
				<div class="col-sm-5 col-12">
	                <p class="icon-tag text-center text-sm-left"><img src="<?=get_field('support_icon')['url']?>"/> <span><?=get_field('support_icon_label')?></span></p>
					<h2 class="heading text-left"><?=get_field('support_heading')?></h2>
					<?=get_field('support_content')?>
					<p class="cta"><?=build_link(get_field('support_cta'))?></p>
				</div>
			</div>
		</div>
	</section>

	  <section class="content-area video">
		<div class="container">
			<div class="video-wrapper embed-responsive embed-responsive-16by9">
				<iframe  src="<?=get_field('video')?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
			</div>
		</div>
	</section> -->



<?php
get_template_part('template-parts/reviews');
?>


<section class="content-area mt-5 mb-5">
	<div id="main" class="site-main" role="main">
		<div class="container">
			<div class="row mb-5 justify-content-center">
				<div class="col-lg-8 mx-auto text-center">
					<h2 class="heading  wow animate__fadeIn"><?php the_field('companies_heading'); ?></h2>
				</div>
				<div class="col-lg-8 mx-auto text-center">
					<p class=" wow animate__fadeIn"><?php the_field('companies_copy'); ?></p>
				</div>
			</div>

			<div class="row mt-4 align-items-center">
				<div class="col-sm-5 col-12">
					<img src="<?=get_field('companies_image')['url']?>"/>
				</div>
				<div class="col-sm-7 col-12 mt-5 mt-sm-0">
				<?php while( have_rows('companies') ): 
					the_row(); 
				?>
					<div class="row align-items-center mb-3">
						<div class="col-sm-6 text-center text-sm-left">
							<?php 
							$logo = get_sub_field('logo');
							echo wp_get_attachment_image( $logo, 'full', "", array('class' => 'companylogo') );
							?>
							<?php if (get_sub_field('heading')):
								echo '<p style="margin-bottom:0">'.get_sub_field('heading').'</p>';
							endif;
							?>
						</div>
						<div class="col-sm-6 text-center text-sm-left">
							<?php
								$cta_link = get_sub_field('cta');
								if( $cta_link ): 
							?>
								<p class="cta"><?=build_link($cta_link)?></p>
							<?php 
								endif;
							?>
						</div>
					</div>
				<?php 
				endwhile;
				?>	
				</div>
			</div>
		</div>
	</div><!-- #main -->
</section><!-- #primary -->

<?php
get_template_part('template-parts/partnership');
?>

<?php get_template_part('template-parts/agg-contact');?>

</div>
<?php
get_footer();
