<?php
get_header();
$bg_url = get_field('hero_background_image');
?>
<div id="product">
	<?php get_template_part('template-parts/agg-mainhero');?>
	<section class="content-area products" style="background-image:url(<?=$bg_url?>)">
		<div class="container">
			<div class="row">
				<div class="col-sm-4  mt-5 pt-4 mx-auto text-center">
					<h1 class="heading"><?=get_the_title()?></h1>
					<p><?=get_field('intro_text'); ?></p>					
				</div>
				<div class="col-sm-8 mx-auto text-center">
					<img src="">
				</div>
			</div>
		</div>
	</section><!-- #primary -->
	<section>
		<div class="container">
			<?php 
			if (get_field('pdf_brochure')):
			?>
				<div class="row">
					<div class="col-sm-3 offset-sm-1">
						<p class="cta"><a href="<?=get_field('pdf_brochure')?>">Download PDF</a></p>
					</div>
				</div>
			<?php 
			endif;
			?>
			<?php 
			$selling_points = get_field('selling_points');

			while(have_rows('selling_points')):
				the_row();
			?>
				<div class="row justify-content-center selling_point">
					<div class="col-sm-3">
						<h2><?=get_sub_field('title')?></h2>
						<img src="<?=get_sub_field('image')?>">						
					</div>
					<div class="col-sm-6 offset-sm-1">
						<?=get_sub_field('content');?>
						<?php 
						$link = get_sub_field('cta');
						?>
						<?php 
						if ($link):
						?>					
							<p class="cta">
								<a href="<?=$link['url']?>" target="<?=$link['target']?>"><?=$link['title']?></a>
							</p>
						<?php 
						endif;
						?>
					</div>
				</div>
			<?php 
			endwhile; ?>
	</section>

	<section class="mt-5 mb-5">
		<div class="container top-bottom-border pt-5">
			<div class="row justify-content-center">

				<?php 
				while(have_rows('content_paragraphs')):
					the_row();
				?>
					<div class="col-sm-10">
						<p><b><?=get_sub_field('label')?></b><br/>
							<?=get_sub_field('content')?>
						</p>
					</div>
				<?php 
				endwhile;
				?>
			</div>
		</div>
	</section>


	<section class="mb-5">
		<div class="container">
			<?php
			$count = 0; 
			while(have_rows('specifications')):
				the_row();
				$color = $count%2?'odd':'even';
			?>
				<div class="row justify-content-center specification">
					<div class="col-sm-5 <?=$color?>">
						<?=get_sub_field('label')?>
					</div>
					<div class="col-sm-5 <?=$color?> imperial">
						<?php
						$imperial = get_sub_field('imperial_value');
						echo $imperial;
						?>
					</div>
					<div class="col-sm-5 <?=$color?> metric d-none">
						<?php 
						$metric = get_sub_field('metric_value');
						echo $metric?$metric:$imperial;
						?>
					</div>
				</div>
			<?php 
				$count++;
			endwhile;
			?>
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
						<img src="/wp-content/themes/wardjet/inc/assets/img/left.svg"/>
					</div>
					<div class="col-sm-1">
						1 of 2
					</div>
					<div class="col-sm-1">
						<img src="/wp-content/themes/wardjet/inc/assets/img/right.svg"/>
					</div>
				</div>
			</div>
		</div>
	</section>
	<?php 
	endif;
	?>
	<section class="review">
		<div class="container">
			<?php
			get_template_part('template-parts/reviews');
			?>
		</div>
	</section>
<?php
get_footer();
?>