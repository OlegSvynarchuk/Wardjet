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

$bgs = $bgs=$block["gallery"][0]['gallery_item'];;
?>

<section class="content-area">
    <div class="container">
        <div class="row mt-5 pt-5 accesory accesory-single justify-content-end">
            <div class="col-12">
            <?php
                get_template_part('template-parts/accesory-gallery', '', ['bgs'=>$bgs, 'content_type'=>'multiple']);
            ?>
            </div>

            <?php
                $rows = $block['key_features'];
                $break = floor(count($rows)/2);
             ?>
                <div class="col-sm-6 col-12">
                <?php 
                    for($i=0; $i<count($rows); $i++):
                ?>
                        <h3><?=$rows[$i]['heading']?></h3>
                        <p><?=$rows[$i]['content']?>
                        </p>
                <?php
                        if ($i == $break):
                            //reset column
                            echo '</div>
                            <div class="col-sm-6 col-12">';
                        endif;
                ?>                    
                <?php 
                    endfor;
                ?>
                </div>
            </div>  
        </div>
    </div>
</section>
