<?php
/**
*/


get_header(); ?>
<div id="about">
    <?php 
    get_template_part('template-parts/wardjet-secondary-hero', null, ['title_align'=>'center', 'show_overlay'=>true]);
    
    $blocks = get_field('content');
    foreach($blocks as $block): 
        get_template_part('template-parts/section', $block['acf_fc_layout'], ['block'=>$block] );
    endforeach; 
    ?>
</div>
<?php
get_footer();