<?php $block = $args['block']; ?>
<section class="brochure-section">
    <div class="brochure-section__inner">
        <div class="brochure-section__content">
            <?php if (!empty($block['title'])) : ?>
                <h2 class="brochure-section__title"><?php echo wp_kses_post($block['title']); ?></h2>
            <?php endif; ?>
            <?php if (!empty($block['description'])) : ?>
                <div class="brochure-section__description"><?php echo wp_kses_post($block['description']); ?></div>
            <?php endif; ?>
            <?php $cta = $block['cta']; if ($cta) : ?>
                <a href="<?php echo esc_url($cta['url']); ?>" class="brochure-section__cta" <?php if ($cta['target']) : ?>target="_blank"<?php endif; ?>>
                    <?php echo esc_html($cta['title']); ?>
                </a>
            <?php endif; ?>
        </div>
        <?php if (!empty($block['image'])) : ?>
            <div class="brochure-section__image-wrapper">
                <div class="brochure-section__image">
                    <?php echo wp_get_attachment_image($block['image'], 'large'); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
