<?php
get_header();
$bg_url = get_field('hero_background_image');
?>

<div id="product">
	<?php get_template_part('template-parts/wardjet-secondary-hero', null, ['show_overlay'=>true]);?>

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
					<div class="col-sm-6 offset-sm-1 wow animate__fadeIn">
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
		<div class="container pt-5">
			<div class="row justify-content-center">

				<?php 
				while(have_rows('content_paragraphs')):
					the_row();
				?>
					<div class="col-sm-12">
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
			<div class="row justify-content-center">
				<div class="col-12 col-sm-12">
					<ul class="nav nav-tabs" id="myTab" role="tablist">
						<li class="nav-item" role="presentation">
							<a class="nav-link active" id="home-tab" data-toggle="tab" href="#product-specifications" role="tab" aria-controls="product-specifications" aria-selected="true">Specs</a>
						</li>
						<li class="nav-item" role="presentation">
							<a class="nav-link" id="layout-tab" data-toggle="tab" href="#product-layout" role="tab" aria-controls="product-layout" aria-selected="true"><i class="fas fa-table"></i></a>
						</li>
					</ul>

					<div class="tab-content mb-4" id="TabContent">
						<div class="tab-pane fade show active pt-3 imperial" id="product-specifications" role="tabpanel" aria-labelledby="specifications-tab">

							<p class="mb-0 text-right">
								<a href="#" class="specs-toggle" data-target="#product-specifications" data-column="imperial">Imperial</a>
								<a href="#" class="specs-toggle" data-target="#product-specifications" data-column="metric"> Metric</a>  
						</p>
							<?php
							$count = 0; 
							while(have_rows('specifications')):
								the_row();
								$color = $count%2?'odd':'even';
							?>
								<div class="row justify-content-center specification">
									<div class="col-sm-6 <?=$color?>">
										<?=get_sub_field('label')?>
									</div>
									<div class="col-sm-6 <?=$color?> imperial">
										<?php
										$imperial = get_sub_field('imperial_value');
										echo $imperial;
										?>
									</div>
									<div class="col-sm-6 <?=$color?> metric">
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
						<div class="tab-pane fade pt-3" id="product-layout" role="tabpanel" aria-labelledby="layout-tab">
							<img src="<?=get_field('layout')['url']?>">
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>


	<?php 
	$bgs=get_field("gallery");

	if ($bgs && count($bgs)>0):
	?>

	<section class="">
		<div class="container">
			<div id="seriesSingleCarousel" class="carousel slide" data-ride="ride">
				<div class="carousel-inner">
					<?php 
					foreach($bgs as $idx => $bg):
					?>
						<div class="carousel-item text-center idx-<?=$idx?> <?=($idx==0)?' active':''?>">
							<img src="<?=$bg['url']?>"/>
						</div>				
					<?php 
					endforeach;
					?>
				</div>
				<ol class="carousel-indicators position-relative mt-3">
					<?php 
					for($i=0; $i<count($bgs); $i++): 
						the_row(); 
					?>
						<li data-target="#seriesSingleCarousel" data-slide-to="<?php echo $i; ?>" <?php if($i==0) : ?>class="active"<?php endif; ?>></li>
					<?php 
					 endfor; 
					 ?>
				</ol>
			</div>
		</div>
	</section>
	<?php 
	endif;
	?>

<?php get_template_part('template-parts/reviews'); ?>

<?php get_template_part('template-parts/agg-contact');?>

<?php
get_footer();
?>