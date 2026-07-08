<?php 
$left = get_field('left_column', 'option');
$right = get_field('right_column', 'option');

?>
<div class="container-xxl pt-4 pb-4">
	<div class="row product-megamenu">
		<div class="col-sm-8 col-12 left-column">
			<?php 
			foreach($left as $left_section):
			?>
				<div class="row">
					<div class="col-12">
						<h4 class="text-center text-sm-left"><?=$left_section['title']?></h4>
					</div>

					<?php 
					foreach($left_section['items'] as $item):
					?>
						<div class="col-sm-4 item col-4 text-center">
							<a href="<?=$item['link']['url']?>">
								<img src="<?=$item['image']['url']?>" class="mm-img"/><br>
								<?=$item['link']['title']?>
							</a>
						</div>
					<?php 
					endforeach;
					?>
				</div>
			<?php 
			endforeach;
			?>
		</div>
		<div class="col-sm-4 col-12 right-column">
			<?php 
			foreach($right as $right_section):
			?>
				<div class="row">
					<div class="col-12">
						<h4><?=$right_section['title']?></h4>
					</div>

					<?php 
					foreach($right_section['items'] as $item):
					?>
						<div class="col-sm-12 item col-4 item-large text-center">
							<a href="<?=$item['link']['url']?>">
								<img src="<?=$item['image']['url']?>" class="mm-img"/><br>
								<?=$item['link']['title']?>
							</a>
						</div>
					<?php 
					endforeach;
					?>
				</div>
			<?php 
			endforeach;
			?>
		</div>
	</div>
</div>