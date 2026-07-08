<?php
/**
* Template Name: Our Products page
*/

get_header();
?>
<div id="our-products">
	<?php get_template_part('template-parts/wardjet-secondary-hero', null, ['show_overlay'=>true]);?>


	<section class="">
		<div class="container">
			<?php 

			$terms = get_terms( array(
				'taxonomy' => 'category',
				'orderby' => 'description',
				'order' => 'ASC',
				'hide_empty' => false
			) );


			foreach($terms as $term):
				$term_name = $term->name;
			?>
				<div class="row justify-content-center mt-5 mb-5">
					<div class="col-sm-10 col-12">
						<?php
							$args = array(  
								'post_type' => 'series',
								'post_status' => 'publish',
								'posts_per_page' => 100, 
								'cat' => $term->term_id,
								'orderby' => 'meta_value', 
								'order' => 'DESC',
								'meta_key' => 'order');

								$loop = new WP_Query( $args );
								if( $loop->have_posts() ) :
								?>
								<h2 class="heading top-border pt-3 text-left  wow animate__fadeIn"><?=$term_name?></h2>
								<?php
								endif;

								while($loop->have_posts()):
									$loop->the_post();
									$cta_label = get_field('cta_label');
									if (!$cta_label)
									{
										$cta_label = 'Learn More';
									}
									$cta_override = get_field('cta');
									//$post = get_sub_field('series_object');
									//setup_postdata($post);
									$link = get_permalink();
									if ($cta_override){
										$link = $cta_override['url'];
									}
								?>
									<div class="row selling_point align-items-center mt-5 mb-5">
										<div class="col-sm-12 text-left">
										</div>
										<div class="col-sm-4">
											<?php display_series_image(); ?>
										</div>
										<div class="col-sm-7 offset-sm-1 content-text wow animate__fadeIn">
											<h3 class="title"><?php the_title()?></h3>
											<p><?=get_field('series_short_description');?></p>
									
											<p class="cta">
												<a href="<?=$link?>"><?=$cta_label?></a>
											</p>
										</div>
									</div>
								<?php 
									wp_reset_postdata();
								endwhile; 
								?>
							</div>
						</div>
					<?php
					endforeach;

					wp_reset_postdata();
					?>
				</div>
			</div>
		</div>
	</section>



	<?php 
	$bgs=get_field("gallery");

	if ($bgs && count($bgs)>0):
	?>
	<section class="full-width-gallery">
		<div id="industrySingleCarousel" class="carousel slide" data-ride="ride">
			<div class="carousel-inner">
				<?php 
				foreach($bgs as $idx => $bg):
				?>
				<div class="carousel-item <?=($idx==0)?' active':''?>" style="background-image: url(<?=$bg['url']?>);">
				</div>
				<?php 
				endforeach;
				?>
			</div>
			<div class="container mt-5 mb-5">
				<div class="row justify-content-center">
					<div class="col-sm-1">
						<a href="#industrySingleCarousel" role="button" data-slide="prev">
							<img src="/wp-content/themes/wardjet/inc/assets/img/left.svg"/>
						</a>
					</div>
					<div class="col-sm-1 text-center">
						<span class="current-slide">1</span> of <?=count($bgs)?>
					</div>
					<div class="col-sm-1">
						<a href="#industrySingleCarousel" role="button" data-slide="next">
							<img src="/wp-content/themes/wardjet/inc/assets/img/right.svg"/>
						</a>
					</div>
				</div>
			</div>
		</div>
	</section>
	<?php 
	endif;
	?>

	<?php
	get_template_part('template-parts/reviews');
	?>

	<?php 
	get_template_part('template-parts/agg-contact');
	?>
<?php
get_footer();
?>