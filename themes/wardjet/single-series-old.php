<?php
get_header();
?>
<div id="product">
	<?php get_template_part('template-parts/wardjet-secondary-hero', null, ['show_overlay'=>true]);?>


	<section class="pb-5 pt-5 dark">
		<div class="container">
			<div class="row justify-content-center align-items-start  wow animate__fadeIn">
				<?php
				while(have_rows('key_features')):
					the_row();
				?>
					<div class="col-12 col-sm">
						<p class=""><b><?=get_sub_field('heading'); ?></b><br/>
						<?=get_sub_field('content')?></p>
					</div>
				<?php 
				endwhile;
				?>
			</div>
		</div>
	</section>

	<section class="mt-5">
		<div class="container">
			<div class="row justify-content-center">
			<?php 
			$selling_points = get_field('products');

			while(have_rows('products')):
				the_row();
				$cta=get_sub_field('cta_label');
				$cta_override = get_sub_field('cta_override');
				$post=get_sub_field('product');
				setup_postdata($post);
				$link = get_permalink();
				if($cta_override)
				{
					$link=$cta_override['url'];
				}
			?>
				<div class="col-sm-4 col-12 mb-5 suggested-product text-center text-sm-left">
					<?php 
					display_product_image($link);
					?>
					<div class="wow animate__fadeIn">
						<h2><?php the_title()?></h2>
						<p><?=get_field('product_short_description');?></p>
					</div>
					<p class="cta">
						<a href="<?=$link?>"><?=$cta?></a>
					</p>
				</div>
			<?php 
				wp_reset_postdata();
			endwhile; ?>
			</div>
	</section>

<?php 
	$bgs=get_field("gallery");

	if ($bgs && count($bgs)>0):
	?>
	<section>
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

	<section class="">
		<div class="container pt-5">
			<div class="row justify-content-center wow animate__fadeIn">

				<?php 
				while(have_rows('content_paragraphs')):
					the_row();
				?>
					<div class="col-sm-6 col-12">
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

	<section>
		<div class="container mt-5 mb-5 pb-4">
			<div class="row justify-content-center">
				<div class="col-sm-12 col-12">
			<?php 
			get_template_part('template-parts/acf-table', null, ['field_name' => 'comparison_table']);
			?>
				</div>
			</div>
		</div>
	</section>



	<?php
	get_template_part('template-parts/video-gallery');
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