<?php
get_header();

$blocks = get_field('content');
?>
<div id="product">
    <?php 
    if (is_array($blocks)):
        foreach($blocks as $block):
            get_template_part('template-parts/section', $block['acf_fc_layout'], ['block'=>$block]);
        endforeach;
    endif;
    get_template_part('template-parts/agg-contact');
    
get_footer();
?>