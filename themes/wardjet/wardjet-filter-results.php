<?php 
//Template Name: Sorter results

//this is used to display the filter results in the homepage

//find all product series that belong to an industry and category


$args = array(  
	'post_type' => 'series',
	'post_status' => 'publish',
	'posts_per_page' => 100, 
	'orderby' => 'title', 
	'order' => 'ASC' 
);

$industry_id = (int)$_REQUEST['industry_id'];
$term_id = (int)$_REQUEST['term_id'];
$cta_label = get_field('cta_label');

?>

	<div id="industrySingleCarousel" class="carousel slide carousel-slide-counter" data-ride="ride">
		<div class="carousel-inner">
			<?php 

			$loop = new WP_Query( $args ); 
			$idx = 0;
			$cta_label = 'Learn More';
			while ( $loop->have_posts() ) :
			    $loop->the_post(); 
				$link = get_permalink();

				$valid_term = ($term_id == 0);
				if ($term_id != 0)
				{
					$terms = get_the_terms($post->ID, 'category');
					foreach ($terms as $term)
					{
						if ($term->term_id == $term_id){
							$valid_term = true;
							break;
						}
					}
				}

				 if (($industry_id == 0  || in_array($industry_id, get_field('industries_list')))
					&& ($valid_term )):			
			?>
				<div class="carousel-item <?=($idx==0)?' active':''?>">
					<div class="row align-items-center">
						<div class="col-lg-5">
							<div class="content-image">
								<?php 
								$productSorterImage = get_field('series_image_sorter');
								if( !empty( $productSorterImage ) ): ?>
									<a href="<?php the_permalink(); ?>">
										<?php echo wp_get_attachment_image( $productSorterImage, 'full' ); ?>
									</a>
								<?php endif; ?>
						</div>
						</div>
						<div class="col-lg-6 offset-lg-1">

							<div class="">
								<h1 class="heading text-left">
									<?php the_title(); ?>
								</h1>

								<p class="phrase"><?php the_field('series_phrase') ?></p>
								<p class="description"><?php the_field('series_short_description') ?></p>
								<p class="cta">
									<a href="<?php the_permalink(); ?>"><?=$cta_label?></a>
								</p>
							</div>
						</div>
					</div>
				</div>
			<?php 
				$idx++;
				endif;
			endwhile;
			?>
		</div>
		<?php 
		if ($idx == 0):
		?>
			<h3 class="text-center">No Results Found</h3>
		<?php 
		else:
		?>
			<div class="container mt-5 mb-5">
				<div class="row justify-content-center">
					<div class="col-sm-1 col-2">
						<a href="#industrySingleCarousel" role="button" data-slide="prev">
							<img src="/wp-content/themes/wardjet/inc/assets/img/left.svg"/>
						</a>				
					</div>
					<div class="col-sm-1 col-4 text-center">
						<span class="current-slide">1</span> of <?=$idx?>
					</div>
					<div class="col-sm-1 col-2">
						<a href="#industrySingleCarousel" role="button" data-slide="next">
							<img src="/wp-content/themes/wardjet/inc/assets/img/right.svg"/>
						</a>
					</div>
				</div>
			</div>
		<?php 
		endif;
		?>
	</div>
