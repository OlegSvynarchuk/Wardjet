<?php
//if the page has a left contact defined we'll use that, otherwise we'll use the default one

if (get_field('contact_left_heading'))
{
    $left_heading = get_field('contact_left_heading');
    $left_copy = get_field('contact_left_copy');
    $form_id = get_field('contact_form');
}
else
{
    $left_heading = get_field('contact_left_heading', 'option');
    $left_copy = get_field('contact_left_copy', 'option');
    $form_id = get_field('contact_form', 'option');
}



if (get_field('contact_right_heading'))
{
    $right_heading = get_field('contact_right_heading');
    $right_copy = get_field('contact_right_copy');
    $right_cta = get_field('contact_right_cta');
}
else
{
    $right_heading = get_field('contact_right_heading', 'option');
    $right_copy = get_field('contact_right_copy', 'option');
    $right_cta = get_field('contact_right_cta', 'option');

}

$display_left = get_field('display_left_column') || get_field('display_left_column')===NULL;
$display_right = get_field('display_right_column') || get_field('display_right_column')===NULL;

$two_column = ($display_left && $display_right);

$left_column = '<h3 class="heading">'.$left_heading.'</h3>';
$left_column .= PHP_EOL;
$left_column .= $left_copy;
$left_column .= PHP_EOL;
$left_column .= gravity_form( $form_id, false, false, false, '', true, 12, false);


$right_column = '<h3 class="heading">'.$right_heading.'</h3>';
$right_column .= PHP_EOL;
$right_column .= $right_copy;
$right_column .= PHP_EOL;
if ($right_cta)
{
    $right_column .= '<p class="cta">'.build_link($right_cta).'</p>';
}


?>
<section id="" class="content-area contact">
    <div id="" class="site-main" role="main">
        <div class="container">
            <div class="row">

                <?php
                if ($display_left):
                ?>
                    <div class="<?=$two_column?'col-sm-6':'col-sm-12 frm-2-col'?>">
                        <h3 class="heading"><?=$left_heading?></h3>
                        <script type="text/javascript" src="<?=$form_id?>"></script>
                    </div>
                <?php 
                    endif;
                ?>

                <?php
                if ($display_right):
                ?>
                    <div class="<?=$two_column?'col-sm-5 offset-sm-1':'col-sm-12'?>">
                        <?=$right_column?>
                    </div>
                <?php 
                    endif;
                ?>

            </div>
        </div>
    </div><!-- #main -->
</section><!-- #primary -->