<?php 
$block = $args['block'];

$rows = $block['multi_content_blocks'];

?>
<section class="content-area">
    <div class="container">
        <div class="row pb-5 pt-5 accesory accesory-multi-blocks justify-content-start">
            <?php 
                for($i=0; $i<count($rows); $i++):
                    $row = $rows[$i];

            ?>
                <div class="col-sm-4 col-6 amb-block">
                    <h3><?=$row['heading']?></h3>
                    <?=$row['content']?>   
                    <br/>

                    <?php 
                    if (is_array($rows[$i]['media']) && count($rows[$i]['media']) > 0):
                        get_template_part('template-parts/accesory-gallery', '', ['bgs'=>$row['media'][0]['gallery_item'], 'content_type'=>'single']);
                    
                    else:
                    ?>
                        <img src="<?=$row['item_image']['url']?>" class="mt-2"/>
                    <?php 
                    endif;
                    ?>
                </div>
            <?php 
                endfor;
            ?>
        </div>
    </div>
</section>
