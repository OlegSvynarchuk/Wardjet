<?php
get_header();
$bg_url = get_field('hero_background_image');
?>
<section>
    <div class="container text-center pb-5">
        <h1><?=get_the_title()?></h1>
        <?=get_field('video')?>
        <?php the_content()?>

    </div>
</section>

<?php
get_footer();
?>