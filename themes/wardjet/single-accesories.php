<?php
get_header(); ?>
<div id="about" class="accesory">
    <?php get_template_part('template-parts/wardjet-secondary-hero', null, ['title_align'=>'center', 'show_overlay'=>true]);?>

    <section class="content-area mt-5 mb-5">
        <div class="container">
            <?php 
            $count = 0;
            while(have_rows('content_blocks')):
                the_row();

                if ($count % 2 == 0){
                    $img_order = "order-sm-1";
                    $content_order = "order-sm-2";
                }
                else
                {
                    $img_order = "order-sm-2";
                    $content_order = "order-sm-1";
                }
                $count++;
            ?>
            <div class="row mt-5 pt-5 accesory">
                <div class="col-sm-5 <?=$img_order?>">

                    <?php 
                    $bgs=get_sub_field("gallery")[0]['gallery_item'];

                    if ($bgs && count($bgs)>0):
                        $slide_id = 'accesoryGallery-'.$count;
                    ?>
                        <div id="<?=$slide_id?>" class="accesory-gallery carousel slide" data-ride="ride">
                            <div class="carousel-inner">
                                <?php 
                                foreach($bgs as $idx => $bg):
                                ?>
                                    <div class="carousel-item <?=($idx==0)?' active':''?>">
                                        <?php
                                        if ($bg['acf_fc_layout'] == 'image'):
                                        ?>
                                            <img src="<?=$bg['image']?>" class="img-fluid">
                                        <?php 
                                        else:
                                        ?>
                                            <div class="embed-responsive embed-responsive-16by9">
                                                <iframe src="<?=$bg['video_url']?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                            </div>
                                        <?php
                                        endif;
                                        ?>
                                    </div>
                                <?php
                                endforeach;
                                ?>
                            </div>

                            <?php 
                            if (count($bgs) > 1):
                            ?>
                                <a href="#<?=$slide_id?>" role="button" data-slide="prev" class="float-slide-nav">
                                    <i class="fas fa-chevron-circle-left"></i>
                                </a>

                                <a href="#<?=$slide_id?>" role="button" data-slide="next" class="float-slide-nav">
                                    <i class="fas fa-chevron-circle-right"></i>
                                </a>
                            <?php 
                            endif;
                            ?>
                        </div>
                    <?php 
                    endif;
                    ?>
                </div>
                <div class="col-sm-7 <?=$content_order?>">
                    <h2 class="heading text-left"><?=get_sub_field('heading')?></h2>
                    <?=get_sub_field('content')?>
                </div>
            </div>

            <?php 
            endwhile;
            ?>
        </div>
    </section>


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


    <?php get_template_part('template-parts/agg-contact');?>
</div>
<?php
get_footer();