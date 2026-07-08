<?php 
global $count;

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

<section class="content-area">
    <div class="container">
        <div class="row mt-5 pt-5 accesory accesory-single justify-content-end">
            <?php 
            if ($image): 
            ?>
                <div class="col-sm-6 col-12 <?=$img_order?>">
                    <figure class="partial-border position-relative">
                        <img src="<?=$img_url?>"/>
                        <figcaption>&nbsp;</figcaption>                            
                    </figure>
                </div>
            <?php 
            endif;
            ?>
            <div class="col-sm-6 col-12 <?=$content_order?>">
                <h2 class="heading text-left"><?=get_sub_field('heading')?></h2>
                <?=get_sub_field('content')?>
            </div>
        </div>
    </div>
</section>
