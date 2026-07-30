<?php
/**
 * Partnerships Section
 * Logo carousel/slider with pagination
 */

// Always source the partnerships content from the CURRENT LOCALE's homepage,
// so every page that shows this section (About, Industries, single Industry, …)
// renders the same localized block as home: 8 logos + localized title/subtitle.
$wj_src_id = get_the_ID();
if ( function_exists( 'lc_get_locale_from_url' ) ) {
	$wj_home = get_posts( array(
		'post_type'      => 'page',
		'posts_per_page' => 1,
		'post_status'    => 'publish',
		'fields'         => 'ids',
		'no_found_rows'  => true,
		'meta_query'     => array(
			'relation' => 'AND',
			array( 'key' => 'is_frontpage',          'value' => 'yes' ),
			array( 'key' => 'region_language_code',  'value' => lc_get_locale_from_url() ),
		),
	) );
	if ( ! empty( $wj_home ) ) {
		$wj_src_id = $wj_home[0];
	}
}

$partners_heading = get_field( 'partnerships_heading', $wj_src_id );
$partners_desc    = get_field( 'partnerships_description', $wj_src_id );
$partners_logos   = get_field( 'partnerships_logos', $wj_src_id );

// Fallback to old field names if new ones aren't set
if ( ! $partners_heading ) {
	$partners_heading = get_field( 'partner_heading', $wj_src_id );
}
if ( ! $partners_logos ) {
	$partners_logos = get_field( 'partners', $wj_src_id );
}
if ( ! $partners_desc ) {
	$partners_desc = __( 'Trusted by industry leaders and innovative organizations worldwide', 'axyz' );
}
?>

<section class="partnerships">
	<div class="partnerships__container">
		<div class="partnerships__header">
			<?php if ( $partners_heading ) : ?>
				<h2 class="partnerships__title"><?php echo esc_html( $partners_heading ); ?></h2>
			<?php endif; ?>
			<?php if ( $partners_desc ) : ?>
				<p class="partnerships__desc"><?php echo esc_html( $partners_desc ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( $partners_logos ) : ?>
		<div class="partnerships__slider-wrapper">
			<div class="partnerships__slider" id="partnerships-slider">
				<div class="partnerships__track">
					<?php
					$total_logos = count( $partners_logos );
					foreach ( $partners_logos as $logo ) :
						$logo_url = is_array( $logo ) ? $logo['url'] : $logo;
					?>
						<div class="partnerships__slide">
							<img src="<?php echo esc_url( $logo_url ); ?>" alt="" class="partnerships__logo" />
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<?php if ( $total_logos > 4 ) : ?>
			<div class="partnerships__pagination" id="partnerships-pagination">
				<?php
				$total_slides = ceil( $total_logos / 4 );
				for ( $i = 0; $i < $total_slides; $i++ ) :
				?>
					<button type="button" class="partnerships__dot <?php echo $i === 0 ? 'active' : ''; ?>" data-slide="<?php echo $i; ?>"></button>
				<?php endfor; ?>
			</div>
			<?php endif; ?>
		</div>
		<?php endif; ?>
	</div>
</section>
