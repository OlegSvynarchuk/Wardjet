<?php
if( have_rows('wardjet_reviews') ): 
	$count=0;
?>
	<section class="review">
		<div class="container">

			<div id="reviewCarousel" class="carousel slide carousel-side-nav" data-ride="false">
				<div class="row align-items-center justify-content-center">
					<div class="col-sm-10 col-10">
						<div class="carousel-inner">
							<?php while( have_rows('wardjet_reviews') ): 
								the_row(); 
								?>
								<div class="carousel-item text-center <?php if($count==0) : echo ' active'; endif; ?>">
								<?php 
								$review_logo = get_sub_field('logo');
								if( !empty( $review_logo ) ): ?>
									<img class="img-fluid review-logo" src="<?php echo esc_url($review_logo['url']); ?>" alt="<?php echo esc_attr($review_logo['alt']); ?>" />
								<?php endif; ?>
								<p class="review mt-3"><?php the_sub_field('review'); ?></p>
								<p class="name"><?php the_sub_field('name'); ?></p>
								<p class="position"><?php the_sub_field('position'); ?></p>
							  </div>
							  <?php $count++; ?> 
						  <?php endwhile; ?>
					  </div>
					  	<ol class="carousel-indicators position-relative">
							<?php 
							$count = 0;
							while( have_rows('wardjet_reviews') ): 
								the_row(); 
							?>
								<li data-target="#reviewCarousel" data-slide-to="<?php echo $count; ?>" <?php if($count==0) : ?>class="active"<?php endif; ?>></li>
							<?php 
								$count++;
							 endwhile; 
							 ?>
						</ol>
					</div>
				</div>
			</div>
		</div>
	</section>
<?php endif; ?>