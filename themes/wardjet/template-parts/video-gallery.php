<?php 
if (have_rows('video_gallery')):
?>
<section class="top-border">
	<div class="container">
		<h2 class="heading"><?=get_field('video_gallery_title')?></h2>
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
endif;
?>