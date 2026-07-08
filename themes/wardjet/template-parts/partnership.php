<section id="" class="content-area partnership dark pt-5 pb-5">
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<h2 class="heading"><?php the_field('partner_heading'); ?></h2>
				<?php 
				$partner_images = get_field('partners');
				if( $partner_images ): ?>
					<div class="row">
						<?php foreach( $partner_images as $partner_image ): ?>
							<div class="col-sm col-4 text-center">

								<img src="<?php echo esc_url($partner_image['url']); ?>" alt="<?php echo esc_attr($partner_image['alt']); ?>" />

							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section><!-- #primary -->