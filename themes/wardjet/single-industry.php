<?php
get_header();

$bg = "background: linear-gradient(180deg, rgba(9,60,113,0) 0%, rgba(9,60,113,0) 30%, rgba(9,60,113,1) 100%), 
	       url('".get_field('hero_banner')."') no-repeat top center / cover";
$items = get_field('hero_paragraphs');

?>

<section class="content-area secondary-hero align-items-end d-flex" style="<?=$bg?>">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-sm-12 text-left pt-5">
				<h1 class="secondary-title"><?=get_field('intro_title')?></h1>
			</div>
		</div>
		<div class="row justify-content-center mt-4 wow animate__fadeIn">
			<div class="col-sm-4">
				<?php 
				for($i=0; $i<min(count($items),2); $i++):
				?>
					<p class="hero-p">
						<b><?=$items[$i]['label'];?></b><br/>
						<?=$items[$i]['content'];?>
					</p>
				<?php 
				endfor;
				?>
			</div>
			<div class="col-sm-8">
				<?php 
				for($j=$i; $j<count($items); $j++):
				?>
					<p class="hero-p">
						<b><?=$items[$j]['label'];?></b><br/>
						<?=$items[$j]['content'];?>
					</p>
				<?php 
				endfor;
				?>				
			</div>
		</div>
	</div>
</section>

<section class="dark pb-5 pt-5">
	<div class="container">
		<div class="row align-items-start">
			<?php 
			$idx = 1;
			while(have_rows('key_features')):
				the_row();
			?>
				<div class="col-sm">
					<div class="row align-items-start no-gutters">
						<div class="col-sm-1 col-2">
							<p class="number-icon"><?=$idx++?></p>
						</div>
						<div class="col-sm-11 col-10 wow animate__fadeIn">
							<?=get_sub_field('content')?>
						</div>
					</div>
				</div>
			<?php 
			endwhile;
			?>
		</div>
	</div>
</section>

<section class="mb-5">
	<div class="container extra-space">
		<div class="row justify-content-start">
			<div class="col-sm-8">
				<?php
				$items = get_field('content_paragraphs');

				foreach($items as $item):
				?>
					<p class="content-p wow animate__fadeIn">
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

<?php 
$bgs=get_field("gallery");

if (count($bgs) > 0):
?>
<section class="full-width-gallery">
	<div class="container">
	<div id="industrySingleCarousel" class="carousel slide carousel-slide-counter" data-ride="ride">
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

		<ol class="carousel-indicators position-relative mt-3">
			<?php 
			for($i=0; $i<count($bgs); $i++): 
				the_row(); 
			?>
				<li data-target="#industrySingleCarousel" data-slide-to="<?php echo $i; ?>" <?php if($i==0) : ?>class="active"<?php endif; ?>></li>
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

<section class="suggested-products extra-space">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-12 mb-4 bottom-border">
				<h2 class="heading">Suggested Products</h2>
			</div>			
			<?php 
			$products = get_field('suggested_product');

			foreach($products as $product):	
				$post = $product['product'];
				setup_postdata($post);
				if ($post->post_type == 'series')
				{
					$img = get_field('series_image');
					$description = get_field('series_short_description');
				}
				else
				{
					$img = get_field('product_image');
					$description = get_field('product_short_description');
				}

			?>
				<div class="col-sm-4 suggested-product wow animate__fadeIn text-center text-sm-left">
					<img src="<?=$img['url']?>" class="img-responsive">
					<div class="content-text text-center text-sm-left">
						<h3 class="title text-center text-sm-left"><?=get_the_title()?></h3>
						<p><?=$description?></p>
                        <p class="cta"><a href="<?=get_permalink()?>" class="btn"><?=__('Learn More','axyz')?></a></p>
                    </div>
					</div>
			<?php 
				wp_reset_postdata();
			endforeach;
			?>
		</div>
	</div>
</section>

<?php 
if (have_rows('video_gallery')):
?>
<section class="top-border videos-gallery">
	<div class="container">
		<h2 class="heading">Video Gallery</h2>

		<div class="row">
			<?php 
			$idx = 0;
			while( have_rows('video_gallery') ): 
				the_row();
				$idx++;
				$url = get_sub_field('url');
				$embed_url = get_youtube_embed_url($url);
				$icon = get_youtube_icon($embed_url);
			?>
			<div class="col-sm-4">
				<div class="video industry-video">
					<div class="placeholder">
						<a href="#" data-toggle="modal" data-target="#video-modal-<?=$idx?>">
                            <img src="<?=$icon?>" alt="">
                            <i class="fas fa-play"></i>
                        </a>
                    </div>
					<h5 class="text-uppercase"><?=get_sub_field('video_title')?></h5>
                    <div class="subtitle">
						<?=get_sub_field('content')?>
                    </div>	
                    <p class="cta">
                    	<a href="#" data-toggle="modal" data-target="#video-modal-<?=$idx?>"><?=__('Watch Now', 'wardjet')?></a>
                    </p>

                    <div class="modal fade youtube-video-modal" id="video-modal-<?=$idx?>" tabindex="-1" aria-labelledby="video-modal-<?=$idx?>" aria-hidden="true">
						<div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title"><?=get_sub_field('video_title')?></h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="embed-responsive embed-responsive-16by9">
                                        <iframe title="Accuracy Test Part Load" src="<?=$embed_url?>?feature=oembed" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen="" width="520" height="390" frameborder="0"></iframe>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
			</div>

			<?php 
			endwhile;
			?>
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
get_template_part('template-parts/partnership');
?>

<?php 
get_template_part('template-parts/agg-contact');
?>


<?php
get_footer();
?>