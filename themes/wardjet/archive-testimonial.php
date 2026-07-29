<?php
/**
 * Testimonials archive (CPT archive, not a page).
 *
 * Locale-aware like the news/blog/webinar archives: shows the current locale's
 * testimonials plus the en-us fallback (deduped so a translated item hides its
 * English original), with item links carried under the current-locale prefix
 * (temp_lang_code). Hero title + "Learn More" label are localized per locale;
 * the testimonial items stay English until translations are created.
 */

get_header();

$wj_locale  = function_exists('lc_get_locale_from_url') ? lc_get_locale_from_url() : 'en-us';
$wj_locales = ($wj_locale !== 'en-us') ? array($wj_locale, 'en-us') : array('en-us');

// Localized UI strings (en-ca / en-uk fall back to English).
$wj_t_i18n = array(
    'es-us' => array('heading' => 'Testimonios de clientes', 'more' => 'Más información'),
    'fr-ca' => array('heading' => 'Témoignages de clients',  'more' => 'En savoir plus'),
    'pl-pl' => array('heading' => 'Referencje klientów',      'more' => 'Dowiedz się więcej'),
);
$wj_t_heading = isset($wj_t_i18n[$wj_locale]['heading']) ? $wj_t_i18n[$wj_locale]['heading'] : 'Customer Testimonials';
$wj_t_more    = isset($wj_t_i18n[$wj_locale]['more'])    ? $wj_t_i18n[$wj_locale]['more']    : 'Learn More';
?>

<section>
<div class="container">
	<h1 class="testimonial-heading heading"><?php echo esc_html($wj_t_heading); ?></h1>
	<div class="row">
<?php
$loop = new WP_Query(
	array( 'post_type' => 'testimonial',
	'posts_per_page' => 10,
	'paged' => $paged,
	'orderby' => 'date',
	'order' => 'DESC',
	// dedupe: hide the EN original when this locale already has a translation
	'post__not_in' => function_exists('wj_localized_archive_exclude') ? wj_localized_archive_exclude('testimonial', $wj_locale) : array(),
	'meta_query' => array(
		array( 'key' => 'region_language_code', 'value' => $wj_locales, 'compare' => 'IN' )
	),
		'ignore_sticky_posts' => 1 ) );

	if ($loop->have_posts()):
		while($loop->have_posts()):

			$loop->the_post();
			// Keep the item link in the current locale (English fallbacks included),
			// same mechanism the switcher uses.
			if ($wj_locale !== 'en-us' && isset($GLOBALS['post'])) {
				$GLOBALS['post']->temp_lang_code = $wj_locale;
			}
		?>
		<div class="col-12 testimonial-section">
				<div class="testimonial-image" style="width:40%;float:left;">
			<?php if (has_post_thumbnail()) : the_post_thumbnail(); endif; ?>
				</div> <div class="testimonial-content" style="width:60%; float:right;">
              <div class="testimonial-body">
			<a class="testimonial-title content-spacing" href="<?=get_permalink()?>"><?=get_the_title()?></a><br>
			<p class="testimonial-excerpt content-spacing"> <?=wp_trim_words( get_the_excerpt(), 25, '' )?></p>
			<a class="testimonial-button content-spacing" href="<?=get_permalink()?>"><?php echo esc_html($wj_t_more); ?></a> </div>
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
