<?php 
get_header();
?>

<section>
<div class="container">
	<h1 class="testimonial-heading heading"><?=__('Customer Testimonials','axyz')?></h1>
	<div class="row">
<?php 
$wj_locale  = function_exists('lc_get_locale_from_url') ? lc_get_locale_from_url() : 'en-us';
$wj_locales = ($wj_locale !== 'en-us') ? array($wj_locale, 'en-us') : array('en-us');
$loop = new WP_Query(
	array( 'post_type' => 'testimonial',
	'posts_per_page' => 10,
	'paged' => $paged,
	'orderby' => 'date',
	'order' => 'DESC',
	'meta_query' => array(
		array( 'key' => 'region_language_code', 'value' => $wj_locales, 'compare' => 'IN' )
	),
		'ignore_sticky_posts' => 1 ) );
	
	if ($loop->have_posts()):
		while($loop->have_posts()):
		
			$loop->the_post();
		?>
		<div class="col-12 testimonial-section">
				<div class="testimonial-image" style="width:40%;float:left;">
					
			<?php
			if (get_the_post_thumbnail()):
				the_post_thumbnail();
			else:
				echo '<img src="https://axyz.juancg.ca/wp-content/uploads/2021/08/packaging-cut-with-WARDJet.jpg" class="attachment-post-thumbnail size-post-thumbnail wp-post-image" alt="j-series packaging cut with WARDJet" decoding="async" loading="lazy" srcset="https://axyz.juancg.ca/wp-content/uploads/2021/08/packaging-cut-with-WARDJet.jpg 1920w, https://axyz.juancg.ca/wp-content/uploads/2021/08/packaging-cut-with-WARDJet-300x200.jpg 300w, https://axyz.juancg.ca/wp-content/uploads/2021/08/packaging-cut-with-WARDJet-1024x683.jpg 1024w, https://axyz.juancg.ca/wp-content/uploads/2021/08/packaging-cut-with-WARDJet-768x512.jpg 768w, https://axyz.juancg.ca/wp-content/uploads/2021/08/packaging-cut-with-WARDJet-1536x1024.jpg 1536w" sizes="(max-width: 1920px) 100vw, 1920px" width="1920" height="1280">';
			endif;	
			?></div> <div class="testimonial-content" style="width:60%; float:right;">
              <div class="testimonial-body">
			<a class="testimonial-title content-spacing" href="<?=get_permalink()?>"><?=get_the_title()?></a><br>		
			<p class="testimonial-excerpt content-spacing"> <?=wp_trim_words( get_the_excerpt(), 25, '' )?></p>			
			<a class="testimonial-button content-spacing" href="<?=get_permalink()?>">Learn More</a> </div>
			</div>
		</div>
		<?php
		endwhile;
	endif;
?>

</div>
<?php posts_nav_link(); ?>
</div>
</section>

<?php 
get_template_part('template-parts/agg-contact');
?>
<?php 
get_footer();
?>