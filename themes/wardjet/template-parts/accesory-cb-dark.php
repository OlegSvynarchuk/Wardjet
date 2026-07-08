<?php 
global $count;

$block = $args['block'];

if ($count % 2 == 0){
    $img_order = "order-sm-1";
    $content_order = "order-sm-2";
}
else
{
    $img_order = "order-sm-2";
    $content_order = "order-sm-1";
}


$img_url = '';
$image = $block['image'];
if ($image) {
    $img_url = wp_get_attachment_image_src($image, 'full');
    if(!empty($img_url[0])) {
        $bg_txt = 'background: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(23,60,109,1) 40%, rgba(23,60,109,1) 100%), url('.$img_url[0].') no-repeat top left';
    }
    else
    {
        //no background image, so we're showing split content
        //increase counter
        $bg_txt = 'background-color: rgba(23,60,109,1)';
    }
}

$bgs = $block['gallery'][0]['gallery_item'];
?>


            
<section class="content-area dark accesory-dark pt-5 pb-5" style="<?=$bg_txt?>">
    <div class="container">
        <div class="row accesory accesory-single justify-content-end">
            <?php 
            if (is_array($bgs) && count($bgs) > 0): 
            ?>
                <div class="col-sm-6 col-12 <?=$img_order?>">
                    <?php get_template_part('template-parts/accesory-gallery', '', ['bgs'=>$bgs, 'content_type'=>'single']);?>
                </div>
            <?php 
                //increase global counter
                $count++;
            endif;
            ?>

            <div class="col-sm-6 col-12 <?=$content_order?>">
                <h2 class="heading text-left"><?=$block['heading']?></h2>
                <?=$block['content']?>
            </div>
        </div>
    </div>
</section>
