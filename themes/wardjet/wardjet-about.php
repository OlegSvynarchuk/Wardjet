<?php
/**
* Template Name: About page
*/

get_header(); ?>
<div id="about">
    <?php get_template_part('template-parts/wardjet-secondary-hero', null, ['show_overlay'=>true]);?>

    <?php get_template_part('template-parts/kpis');?>

    <section class="content-area extra-space">
        <div class="container">
            <div class="row">
                <div class="col-sm-6">
                    <figure class="partial-border position-relative">
                        <img src="<?=get_field('about_image')['url']?>">
                        <figcaption>&nbsp;</figcaption>
                    </figure>
                </div>
                <div class="col-sm-6 wow animate__fadeIn">
                    <?=get_field('about_copy')?>
                </div>
            </div>
        </div>
    </section>

    <?php
    $i=0;
    while(have_rows('about_items')): 
        the_row();
        $img_order_class = ($i%2==0)?'order-2 order-sm-1':'order-2 order-sm-2';
        $copy_order_class = ($i%2==0)?'order-1 order-sm-2':'order-1 order-sm-1';
        $link = get_sub_field('cta');
        $is_dark = get_sub_field('dark_bg');

        if ($is_dark):
            $img_url = get_sub_field('image')['url'];
        ?>
            <section class="about-items dark" style="background: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(23,60,109,1) 40%, rgba(23,60,109,1) 100%), url(<?=$img_url?>) no-repeat top left; ">
        <?php 
        else:
            $i++;
        ?>
            <section class="about-items extra-space">
        <?php 
        endif;
        ?>
        <div class="container">                
            <a name="<?=get_sub_field('anchor')?>"></a>

            <div class="row about-item justify-content-end align-items-center">

                <?php 
                if (!$is_dark):
                ?>
                <div class="col-sm-6 <?=$img_order_class?>">
                    <figure class="partial-border position-relative">
                        <img src="<?=get_sub_field('image')['url']?>">
                        <figcaption>&nbsp;</figcaption>
                    </figure>
                </div>
                <?php 
                endif;
                ?>
                <div class="col-sm-6 <?=$copy_order_class?> wow animate__fadeIn">
                    <h2 class="heading"><?=get_sub_field('title')?></h2>
                    <?=get_sub_field('copy')?>

                    
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
    endwhile;?>

<section class="content-area dark pt-5" style="background: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(23,60,109,1) 50%, rgba(23,60,109,1) 100%), url(https://www.wardjet.com/wp-content/uploads/2021/11/World-Class-Service-original.jpg) no-repeat top left; ">
<div class="container">
<div class="row justify-content-end wow animate__fadeIn" style="visibility: visible; animation-name: fadeIn;">
<div class="col-sm-5 col-12">
<p class="icon-tag text-center text-sm-left"><img src="" alt=""> <span></span></p>
<h2 class="heading text-left">Our Corporate Video</h2>
<p>This video showcases our products, services, and most importantly, our team members who work tirelessly to ensure our customers are happy and satisfied. We believe this video will give you a deeper understanding of who we are as a company and what we offer to our customers.</p>
<p class="cta"><a href="<?php echo esc_url( home_url('/support-landing/') ); ?>" target="">Learn More</a></p>
</div>
</div>
</div>
</section>

<section class="content-area video">
		<div class="container">
			<div style="margin-bottom:50px;" class="video-wrapper embed-responsive embed-responsive-16by9">
				<iframe  src="https://www.youtube.com/embed/nqajFBE8Eh8?rel=0&controls=0&autoplay=0" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
			</div>
		</div>
	</section>

    <?php
    get_template_part('template-parts/reviews');
    ?>

    <?php
    get_template_part('template-parts/partnership');
    ?>    

    <?php get_template_part('template-parts/agg-contact');?>
</div>
<?php
get_footer();