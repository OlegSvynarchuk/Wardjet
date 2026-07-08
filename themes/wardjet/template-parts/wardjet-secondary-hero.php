<?php 

$show_overlay = isset($args['show_overlay']) && $args['show_overlay'];


if (isset($args['vertical_align_center']) && $args['vertical_center'])
{
	$align_center = $args['vertical_center'];
}
else
{
	$align_center = !get_field('page_title_copy');
}

$bg = "background: url('".get_field('hero_banner')."') no-repeat center center / cover";
if ($show_overlay) {
	$bg = "background: linear-gradient(180deg, rgba(9,60,113,0) 0%, rgba(9,60,113,0) 40%, rgba(9,60,113,1) 100%), 
	       url('".get_field('hero_banner')."') no-repeat top center / cover";
}

$title = get_field('page_title');
if (!$title) {
	$title = get_the_title();
}



?>

<section class="content-area secondary-hero d-flex <?=$align_center?'align-items-center':'align-items-end'?> <?=$args['extra_classes']?>" style="<?=$bg?>">
	<div class="container">
		<div class="row justify-content-center wow animate__fadeIn">
			<div class="col-sm-12 text-<?=$args['title_align']?$args['title_align']:'center'?>">
				<h1 class="secondary-title"><?=$title?></h1>
			</div>

			<?php 
			if (get_field('page_title_copy')):
			?>
				<div class="mt-4 col-sm-8 text-<?=$args['title_align']?$args['title_align']:'center'?>  wow animate__fadeIn">
					<?=get_field('page_title_copy')?>
				</div>
			<?php 
			endif;
			?>				
		</div>
	</div>
</section>