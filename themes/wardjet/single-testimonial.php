<?php 
get_header();
?>

<section>
<div class="container">
	<div class="row">
		<h1 class="heading"><?=get_the_title()?></h1>
		<?php 
		the_content();
		?>
	</div>
</div>
</section>

<?php 
get_template_part('template-parts/agg-contact');
?>
<?php 
get_footer();
?>