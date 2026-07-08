<?php
/**
* Template Name: Support Landing page
*/

get_header(); ?>
<div id="support">
    <?php get_template_part('template-parts/wardjet-secondary-hero', null, ['title_align'=>'center', 'show_overlay'=>true]);?>

    <section class="dark pt-2 pb-2">
        <div class="container">
            <?php 
            if (get_field('support_icon')):
            ?>
                <p class="icon-tag text-center"><img src="<?=get_field('support_icon')['url']?>"/> <span><?=get_field('support_icon_label')?></span></p>
            <?php 
            endif;
            ?> 
        </div>
    </section>

    <?php 
    $count = 0;

    while(have_rows('content_blocks')):
        the_row();
        $is_dark = get_sub_field('dark_bg');
        $img_order_class = ($count%2==0)?'order-sm-1':'order-sm-2';
        $copy_order_class = ($count%2==0)?'order-sm-2':'order-sm-1';
        $link = get_sub_field('cta');
        $is_dark = get_sub_field('dark_bg');
        $img_url = get_sub_field('image')['url'];
        if ($is_dark):
        ?>
            <section class="about-items dark" style="background: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(23,60,109,1) 40%, rgba(23,60,109,1) 100%), url(<?=$img_url?>) no-repeat top left; ">
        <?php 
        else:
            $count++;
        ?>
            <section class="about-items extra-space">
        <?php 
        endif;
        ?>
            <div class="container">
                <div class="row justify-content-end align-items-center support-item">
                    <?php 
                    if (!$is_dark):
                    ?>
                        <div class="col-sm-6 <?=$img_order_class?>">
                            <figure class="partial-border position-relative">
                                <img src="<?=$img_url?>">
                                <figcaption>&nbsp;</figcaption>
                            </figure>
                        </div>
                    <?php 
                    endif;
                    ?>
                    <div class="col-sm-6 <?=$copy_order_class?>">
                        <?php 
                        if (get_sub_field('icon')):
                        ?>
                            <p class="icon-tag"><img src="<?=get_sub_field('icon')['url']?>"/> <span><?=get_sub_field('icon_text')?></span></p>
                        <?php 
                        endif;
                        ?>                        
                        <h2 class="heading"><?=get_sub_field('sub_heading')?></h2>
                        <?=get_sub_field('content')?>
                        <?php 
                        if ($link):
                        ?>
                           <p class="cta"><?=build_link($link);?></p>
                        <?php 
                        endif;
                        ?>
                    </div>
                </div>
            </div>
        </section>
    <?php 
    endwhile;    
    ?>

    <section class="extra-space">
        <div class="container">
            <div class="row">
                <?php
                while(have_rows('widgets')):
                    the_row();
                ?>
                    <div class="col-sm col-12 support-widget">
                        <img src="<?=get_sub_field('image')['url']?>"/>
                        <h3><?=get_sub_field('header')?></h3>
                        <?=get_sub_field('content')?>
                        <?php 
                        if (get_sub_field('cta')):
                        ?>
                           <p class="cta"><?=build_link(get_sub_field('cta'));?></p>
                        <?php 
                        endif;
                        ?>
                    </div>
                <?php
                endwhile;
                ?>
            </div>
        </div>
    </section>

    <?php get_template_part('template-parts/agg-contact');?>
</div>
<?php
get_footer();
?>