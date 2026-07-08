<?php

add_filter( 'body_class', 'custom_class' );
function custom_class( $classes ) {
	$classes[] = 'industry-single';
    return $classes;
}

$bg = get_field('hero_background_image');
$body_style = 'style="background-image:url(\''. esc_url($bg). '\')"';
get_header();
?>

<section class="single-industry-intro mt-5 mb-5">
	<div class="container">
		<div class="row justify-content-between">
			<div class="col-sm-4">
				<p class="intro-title"><?=get_field('intro_title')?></p>
			</div>

			<div class="col-sm-7">
				<?php 
				$items = get_field('hero_paragraphs');

				foreach($items as $item):
				?>
					<p class="hero-p">
						<b><?=$item['label'];?></b><br/>
						<?=$item['content'];?>
					</p>
				<?php 
				endforeach;
				?>
			</div>
		</div>
	</div>
</section>
<section class="mb-5">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-sm-10">
				<?php
				$items = get_field('content_paragraphs');

				foreach($items as $item):
				?>
					<p class="content-p">
						<b><?=$item['title'];?></b><br/>
						<?=$item['content'];?>						
					</p>
				<?php 
				endforeach;
				?> 

			</div>
		</div>
	</div>
</section>

<section class="full-width-gallery">
	<div id="industrySingleCarousel" class="carousel slide carousel-slide-counter" data-ride="ride">
		<div class="carousel-inner">
			<?php 
			$bgs=get_field("gallery");

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
				<div class="col-sm-1">
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

<section class="suggested-products">
	<div class="container">
		<div class="row justify-content-center top-bottom-border">
			<div class="col-12">
				<h2 class="heading">Suggested Products</h2>
			</div>			
			<?php 
			$products = get_field('suggested_product');

			foreach($products as $product):	
				$post = $product['product'];
				setup_postdata($post);
				$img = get_field('product_image');
			?>
				<div class="col-sm-3 suggested-product">
					<img src="<?=$img['url']?>" class="img-responsive">
					<br/>
					<?=get_the_title()?>
					<?=$product['content']?>				
					<p class="cta"><a href="" class="btn">Learn</a></p>
				</div>
			<?php 
				wp_reset_postdata();
			endforeach;
			?>
		</div>
	</div>
</section>

<section class="">
	<div class="container">
		<h2 class="heading">Video Gallery</h2>
		<div id="VideoCarousel" class="carousel slide carousel-side-nav" data-ride="false">
			<div class="row justify-content-center align-items-center">
				<div class="col-sm-1">
					<a class="carousel-control-prev" href="#VideoCarousel" role="button" data-slide="prev">
						<img src="/wp-content/themes/wardjet/inc/assets/img/left.svg" alt="Previous"/>
					</a>
				</div>

				<div class="col-sm-10">
					<div class="carousel-inner">
						<?php 
						$count=0;
						while( have_rows('video_gallery') ): 
							the_row(); 
						?>
						<div class="carousel-item <?=($count==0)?' active':''?>">
							<?=get_sub_field('url')?>
							<iframe width="100%" height="500" src="<?=get_sub_field('url')?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
						</div>
						<?php 
							$count++;
						endwhile;
						?>
					</div>
				</div>
				<div class="col-sm-1">
					<a class="carousel-control-next" href="#VideoCarousel" role="button" data-slide="next">
						<img src="/wp-content/themes/wardjet/inc/assets/img/right.svg" alt="Next"/>
					</a>
				</div>
			</div>

			<div class="row">
				<div class="col-sm-12">
					<ul class="carousel-indicators position-relative">
					<?php 
					$count = 0;
					while( have_rows('video_gallery') ):
						the_row(); 
						$url = get_sub_field('url');
						$video_code = str_replace('https://www.youtube.com/embed/', '', $url);
					?>
						<li data-target="#VideoCarousel" data-slide-to="<?php echo $count;?>" class="<?=($count==0)?'active':''?>">
							<img src="http://img.youtube.com/vi/<?=$video_code?>/1.jpg"/>
							<br/>
							<?=get_sub_field('video_title')?>
							<br/>
							<?=get_sub_field('content')?>
						</li>
					<?php 
						$count++;
					endwhile;
					?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</section>


<?php
get_template_part('template-parts/reviews');
?>
]

<section id="" class="content-area partnership">
	<div id="" class="site-main" role="main">
		<div class="container">

			<div class="row">

				<div class="col-lg-12">

					<?php 
					$partner_images = get_field('partners');
					if( $partner_images ): ?>
						<div class="row">
							<?php foreach( $partner_images as $partner_image ): ?>
								<div class="col text-center">

									<img src="<?php echo esc_url($partner_image['url']); ?>" alt="<?php echo esc_attr($partner_image['alt']); ?>" />

								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>


					<h1 class="heading"><?php the_field('partner_heading'); ?></h1>

				</div>
			</div>



		</div>
	</div><!-- #main -->
</section><!-- #primary -->

<?php
get_footer();
?>