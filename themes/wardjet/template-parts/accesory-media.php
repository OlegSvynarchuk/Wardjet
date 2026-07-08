<?php
$bg = $args['bg'];
if ($bg['acf_fc_layout'] == 'image'):
    echo '<img src="'.$bg['image'].'" class="img-fluid">';
else:
    if (substr($bg['video_url'], -3) == 'mp4'):
        echo '<video class="embed-responsive embed-responsive-16by" autoplay loop controls>';
        echo '<source src="'.$bg['video_url'].'" type="video/mp4">';
        echo '</video>'; 
    else:
    echo '<div class="embed-responsive embed-responsive-16by9">
        <iframe src="'.$bg['video_url'].'" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>';
    endif;
endif;
?>