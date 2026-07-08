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
$count++;            

$image = $block['image'];
if (is_array($block['gallery']))
{
    $bgs = $block['gallery'][0]['gallery_item'];
}
else
{
    $bgs = [];
}
?>

<section class="content-area pt-5 pb-5">
    <div class="container">
        <div class="row accesory accesory-single justify-content-end">
            <?php 
            $noimg = count($bgs)==0;
            if (count($bgs) > 0): 
            ?>
                <div class="col-sm-6 col-12 <?=$img_order?>">
                    <?php get_template_part('template-parts/accesory-gallery', '', ['bgs'=>$bgs, 'content_type'=>'single', 'full_width' => true]);?>
                </div>
            <?php 
            endif;
            ?>
            <div class="<?=$noimg?'col-sm-12':'col-sm-6'?> col-12 <?=$content_order?>">
                <h2 class="heading text-left"><?=$block['heading']?></h2>
                <?=$block['content']?>
            </div>
        </div>
    </div>
</section>
