<?php
/**
 * New KPIs Section
 * Displays company statistics: Years, Countries, Team Members
 */
?>

<section class="new-kpis">
	<div class="new-kpis__container">
		<?php
		// Check page-level kpis first, then options, then defaults
		if ( have_rows( 'kpis' ) ) :
			while ( have_rows( 'kpis' ) ) :
				the_row();
		?>
			<div class="new-kpis__card">
				<span class="new-kpis__number"><?php echo esc_html( get_sub_field( 'number' ) ); ?>+</span>
				<span class="new-kpis__label"><?php echo esc_html( get_sub_field( 'description' ) ); ?></span>
			</div>
		<?php
			endwhile;
		elseif ( have_rows( 'stats_counters', 'option' ) ) :
			while ( have_rows( 'stats_counters', 'option' ) ) :
				the_row();
		?>
			<div class="new-kpis__card">
				<span class="new-kpis__number"><?php echo esc_html( get_sub_field( 'number' ) ); ?></span>
				<span class="new-kpis__label"><?php echo esc_html( get_sub_field( 'label' ) ); ?></span>
			</div>
		<?php
			endwhile;
		else :
			// Default stats
			$stats = array(
				array( 'number' => '30+', 'label' => __( 'Years of Excellence', 'wardjet' ) ),
				array( 'number' => '180+', 'label' => __( 'Countries Worldwide', 'wardjet' ) ),
				array( 'number' => '100+', 'label' => __( 'Dedicated Team Members', 'wardjet' ) ),
			);
			foreach ( $stats as $stat ) :
		?>
			<div class="new-kpis__card">
				<span class="new-kpis__number"><?php echo esc_html( $stat['number'] ); ?></span>
				<span class="new-kpis__label"><?php echo esc_html( $stat['label'] ); ?></span>
			</div>
		<?php
			endforeach;
		endif;
		?>
	</div>
</section>
