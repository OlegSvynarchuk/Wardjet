<?php
$block = $args['block'];
$images = array();

if (!empty($block['gallery'])) {
    $images = $block['gallery'];
} elseif (!empty($block['series_slider'])) {
    $images = $block['series_slider'];
}

if (empty($images)) return;

// Desktop/tablet: 2 images per slide. Mobile: 1 per slide.
// Both carousels render; CSS media query shows only the active one.
$pairs   = array_chunk($images, 2);
$singles = array_chunk($images, 1);

$render_carousel = function ($id, $slides) {
    $total = count($slides);
    ?>
    <div id="<?php echo esc_attr($id); ?>" class="carousel slide" data-ride="carousel" data-interval="5000" data-pause="hover" data-touch="true">
        <div class="carousel-inner">
            <?php foreach ($slides as $i => $slide) : ?>
                <div class="carousel-item <?php echo $i === 0 ? 'active' : ''; ?>">
                    <div class="series-gallery__pair">
                        <?php foreach ($slide as $image_id) :
                            $img_url = wp_get_attachment_image_url($image_id, 'large');
                            $img_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
                        ?>
                            <div class="series-gallery__image">
                                <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($img_alt); ?>" />
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($total > 1) : ?>
        <ol class="carousel-indicators series-gallery__dots">
            <?php for ($i = 0; $i < $total; $i++) : ?>
                <li data-target="#<?php echo esc_attr($id); ?>" data-slide-to="<?php echo $i; ?>" class="<?php echo $i === 0 ? 'active' : ''; ?>"></li>
            <?php endfor; ?>
        </ol>
        <?php endif; ?>
    </div>
    <?php
};
?>
<section class="series-gallery">
    <div class="series-gallery__inner">
        <div class="series-gallery-carousel--desktop">
            <?php $render_carousel('seriesGalleryCarousel', $pairs); ?>
        </div>
        <div class="series-gallery-carousel--mobile">
            <?php $render_carousel('seriesGalleryCarouselMobile', $singles); ?>
        </div>
    </div>
</section>
