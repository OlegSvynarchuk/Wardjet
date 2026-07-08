<?php
get_header(); ?>
<div id="about">
    <?php get_template_part('template-parts/wardjet-secondary-hero', null, ['title_align'=>'center', 'show_overlay'=>true]);?>

    <?php 
    $count = 0;

    $blocks = get_field('content_blocks');

    foreach($blocks as $block):
        $bgs=$block["gallery"][0]['gallery_item'];
        $content_type = $block['content_type'];
        $is_dark = $block['dark_background'];

        if ($is_dark){
            get_template_part('template-parts/accesory-cb-dark', '', ['block'=>$block]);
        } elseif($content_type == 'single') {
            get_template_part('template-parts/accesory-cb-single', '', ['block'=>$block]);

        } elseif($content_type == 'multiple') {
            get_template_part('template-parts/accesory-cb-multi-2-columns','', ['block'=>$block]);

        } elseif($content_type == 'multi_content_blocks') {
            get_template_part('template-parts/accesory-cb-multi-3-columns', '', ['block'=>$block]);
        } else {
            if(file_exists(get_template_directory().'/template-parts/section-'.$content_type.'.php')) {
                get_template_part('template-parts/section', $content_type, ['block'=>$block]);
            }
        }
    endforeach;
    
    
    ?>

    <?php 
    if (get_field('comparison_table')):
    ?>
    <section>
        <div class="container bottom-border mt-5 mb-5 pb-4">
            <div class="row justify-content-center">
                <div class="col-sm-10 col-12">
            <?php 
            get_template_part('template-parts/acf-table', null, ['field_name' => 'comparison_table']);
            ?>
                </div>
            </div>
        </div>
    </section>
    <?php 
    endif;
    ?>
<?php
if (get_field('hotspot_background')):
    ?>
    <?php get_template_part('template-parts/image-hotspots');?>
<?php
endif;
?>
    <?php get_template_part('template-parts/agg-contact');?>
</div>
<?php
get_footer();