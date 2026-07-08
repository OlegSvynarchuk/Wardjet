<?php
global $count;

$bgs = $args['bgs'];
$content_type = $args['content_type'];

if ($bgs && count($bgs)>0):
    $slide_id = 'accesoryGallery-'.$count;
    if ($content_type=='multiple' && count($bgs)<=3):
        //display all items (less than 3) items across and that's it
    ?>
         <div class="row justify-content-between">
            <?php 
            for($i=0;$i<count($bgs);$i++):
            ?>
                <div class="col-sm col-4">
                    <?php 
                    get_template_part('template-parts/accesory-media','', ['bg'=>$bgs[$i]]);
                    ?>
                </div>
            <?php
            endfor;
            ?>
        </div>
    <?php

    elseif ($content_type == 'single' && count($bgs) == 1):
        if ($bgs[0]['acf_fc_layout'] == 'image'):

            $img_url = isset($bgs[0]['image']['url'])?$bgs[0]['image']['url']:$bgs[0]['image'];
    ?>

                    <figure class="position-relative">
                        <img src="<?=$img_url?>" class="<?=$args['full_width']?'img-full-width':''?>"/>
                        <figcaption>&nbsp;</figcaption>                            
                    </figure>
    <?php 
        else:
            get_template_part('template-parts/accesory-media','', ['bg'=>$bgs[0]]);
        endif;
    else:
    ?>
        <div id="<?=$slide_id?>" class="accesory-gallery carousel slide" data-ride="ride">
            <div class="carousel-inner">
                <?php 
                foreach($bgs as $idx => $bg):
                ?>
                    <div class="carousel-item <?=($idx==0)?' active':''?>">
                        <?php 
                        get_template_part('template-parts/accesory-media','', ['bg'=>$bg]);
                        ?>                    
                    </div>
                <?php
                endforeach;
                ?>
            </div>
            <?php 
            if (count($bgs)>1):
            ?>
            <ul class="carousel-indicators position-relative mt-3">
                <?php 
                for($i=0; $i<count($bgs); $i++): 
                ?>
                    <li data-target="#<?=$slide_id?>" data-slide-to="<?=$i;?>" class="<?($i==0)?'active':''?>"></li>
                <?php 
                 endfor; 
                 ?>
            </ul>
            <?php 
            endif;
            ?>
        </div>
<?php 
    endif;
endif;
?>
