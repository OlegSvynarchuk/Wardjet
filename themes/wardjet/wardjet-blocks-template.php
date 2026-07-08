<?php
/**
 * Template Name: Blocks Template
 */
get_header();
$blocks = get_field('content');
?>
<div id="main">
    <?php
    foreach($blocks as $block):
    get_template_part('template-parts/section', $block['acf_fc_layout'], ['block'=>$block]);
    endforeach;
    
	get_template_part('template-parts/agg-contact');
	?>
<?php
get_footer();
