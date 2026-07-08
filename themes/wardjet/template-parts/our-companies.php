<?php
/**
 * Our Companies Section
 * Redesigned with 3 company cards
 */
?>

<section class="our-companies">
	<div class="our-companies__container">
		<div class="our-companies__header">
			<h2 class="our-companies__title"><?php the_field( 'companies_heading' ); ?></h2>
			<p class="our-companies__desc"><?php the_field( 'companies_copy' ); ?></p>
		</div>

		<div class="our-companies__grid">
			<?php
			if ( have_rows( 'companies' ) ) :
				while ( have_rows( 'companies' ) ) :
					the_row();
					$logo    = get_sub_field( 'logo' );
					$heading = get_sub_field( 'heading' );
					$cta     = get_sub_field( 'cta' );
			?>
				<div class="our-companies__card">
					<div class="our-companies__card-logo">
						<?php
						if ( $logo ) {
							echo wp_get_attachment_image( $logo, 'full' );
						}
						?>
					</div>
					<div class="our-companies__card-content">
						<?php if ( $heading ) : ?>
							<p class="our-companies__card-desc"><?php echo esc_html( $heading ); ?></p>
						<?php endif; ?>
						<?php if ( $cta ) : ?>
							<a href="<?php echo esc_url( $cta['url'] ); ?>" target="<?php echo esc_attr( $cta['target'] ?: '_self' ); ?>" class="our-companies__card-link">
								<span><?php echo esc_html( $cta['title'] ?: __( 'Learn More', 'wardjet' ) ); ?></span>
								<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="#093C71" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
							</a>
						<?php endif; ?>
					</div>
				</div>
			<?php
				endwhile;
			endif;
			?>
		</div>
	</div>
</section>
