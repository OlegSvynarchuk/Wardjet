<?php
/**
 * Template part for Locations page sections
 *
 * Renders title/subtitle, primary location groups, and other locations from ACF fields.
 *
 * @package AXYZ
 */

if (!defined('ABSPATH')) {
    exit;
}

$locations_title         = get_field('locations_title');
$locations_subtitle      = get_field('locations_subtitle');
$region_title            = get_field('primary_region_title');
$primary_location_groups = get_field('primary_location_groups');
$other_locations_title   = get_field('other_locations_title');
$other_locations_regions = get_field('other_locations_regions');
?>

<?php if ($locations_title || $locations_subtitle) : ?>
<section class="locations-hero">
    <div class="locations-hero__text">
        <?php if ($locations_title) : ?>
            <h1 class="locations-hero__title"><?php echo esc_html($locations_title); ?></h1>
        <?php endif; ?>
        <?php if ($locations_subtitle) : ?>
            <p class="locations-hero__subtitle"><?php echo esc_html($locations_subtitle); ?></p>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<?php if ($region_title || $primary_location_groups) : ?>
    <?php if ($primary_location_groups) : ?>
        <?php foreach ($primary_location_groups as $group_index => $group) :
            $is_reverse = (!empty($group['layout']) && $group['layout'] === 'reverse');
            $modifier = $is_reverse ? ' locations-region--reverse' : '';
        ?>
        <section class="locations-primary<?php echo esc_attr($modifier); ?>">
            <div class="locations-primary__inner">
                <div class="locations-primary__content">
                    <?php if ($group_index === 0 && $region_title) : ?>
                        <h2 class="locations-primary__region-title"><?php echo esc_html($region_title); ?></h2>
                    <?php endif; ?>

                    <?php if (!empty($group['locations'])) : ?>
                        <?php foreach ($group['locations'] as $location) : ?>
                            <div class="locations-card">
                                <div class="locations-card__body">
                                    <div class="locations-card__icon">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12 2C8.13 2 5 5.13 5 9C5 14.25 12 22 12 22C12 22 19 14.25 19 9C19 5.13 15.87 2 12 2Z" stroke="#FFFFFF" stroke-width="2" fill="none"/>
                                            <circle cx="12" cy="9" r="3" stroke="#FFFFFF" stroke-width="2" fill="none"/>
                                        </svg>
                                    </div>
                                    <div class="locations-card__details">
                                        <?php if (!empty($location['location_name'])) : ?>
                                            <h3 class="locations-card__name"><?php echo esc_html($location['location_name']); ?></h3>
                                        <?php endif; ?>
                                        <?php if (!empty($location['company_name'])) : ?>
                                            <span class="locations-card__company"><?php echo esc_html($location['company_name']); ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($location['address'])) :
                                            // Collapse multiple <br> into a single one
                                            $clean_address = preg_replace('#(<br\s*/?>\s*){2,}#i', '<br />', $location['address']);
                                        ?>
                                            <div class="locations-card__address"><?php echo wp_kses_post($clean_address); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php if (!empty($location['phones'])) : ?>
                                    <div class="locations-card__phones">
                                        <img class="locations-card__phone-icon" width="20" height="20" src="<?php echo esc_url(get_template_directory_uri() . '/inc/assets/images/phone.png'); ?>" alt="" />
                                        <?php foreach ($location['phones'] as $phone) : ?>
                                            <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $phone['phone_number'])); ?>" class="locations-card__phone-link">
                                                <?php echo esc_html($phone['phone_number']); ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <?php if (!empty($group['group_image'])) : ?>
                    <div class="locations-primary__image">
                        <img src="<?php echo esc_url($group['group_image']['url']); ?>" alt="<?php echo esc_attr($group['group_image']['alt']); ?>" />
                    </div>
                <?php endif; ?>
            </div>
        </section>
        <?php endforeach; ?>
    <?php endif; ?>
<?php endif; ?>

<?php if ($other_locations_title) : ?>
<section class="locations-other-title">
    <div class="locations-other-title__inner">
        <h2 class="locations-other-title__heading"><?php echo esc_html($other_locations_title); ?></h2>
    </div>
</section>
<?php endif; ?>

<?php if ($other_locations_regions) : ?>
    <?php foreach ($other_locations_regions as $index => $region) :
        $is_reverse = (!empty($region['layout']) && $region['layout'] === 'reverse');
        $modifier = $is_reverse ? ' locations-region--reverse' : '';
    ?>
    <section class="locations-region<?php echo esc_attr($modifier); ?>">
        <div class="locations-region__inner">
            <div class="locations-region__content">
                <?php if (!empty($region['region_title'])) : ?>
                    <h2 class="locations-region__title"><?php echo esc_html($region['region_title']); ?></h2>
                <?php endif; ?>

                <?php if (!empty($region['locations'])) : ?>
                    <?php foreach ($region['locations'] as $location) : ?>
                        <div class="locations-card">
                            <div class="locations-card__body">
                                <div class="locations-card__icon">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 2C8.13 2 5 5.13 5 9C5 14.25 12 22 12 22C12 22 19 14.25 19 9C19 5.13 15.87 2 12 2Z" stroke="#FFFFFF" stroke-width="2" fill="none"/>
                                        <circle cx="12" cy="9" r="3" stroke="#FFFFFF" stroke-width="2" fill="none"/>
                                    </svg>
                                </div>
                                <div class="locations-card__details">
                                    <?php if (!empty($location['location_name'])) : ?>
                                        <h3 class="locations-card__name"><?php echo esc_html($location['location_name']); ?></h3>
                                    <?php endif; ?>
                                    <?php if (!empty($location['company_name'])) : ?>
                                        <span class="locations-card__company"><?php echo esc_html($location['company_name']); ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($location['address'])) :
                                        $clean_address = preg_replace('#(<br\s*/?>\s*){2,}#i', '<br />', $location['address']);
                                    ?>
                                        <div class="locations-card__address"><?php echo wp_kses_post($clean_address); ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if (!empty($location['phones'])) : ?>
                                <div class="locations-card__phones">
                                    <img class="locations-card__phone-icon" width="20" height="20" src="<?php echo esc_url(get_template_directory_uri() . '/inc/assets/images/phone.png'); ?>" alt="" />
                                    <?php foreach ($location['phones'] as $phone) : ?>
                                        <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $phone['phone_number'])); ?>" class="locations-card__phone-link">
                                            <?php echo esc_html($phone['phone_number']); ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?php if (!empty($region['region_image'])) : ?>
                <div class="locations-region__image">
                    <img src="<?php echo esc_url($region['region_image']['url']); ?>" alt="<?php echo esc_attr($region['region_image']['alt']); ?>" />
                </div>
            <?php endif; ?>
        </div>
    </section>
    <?php endforeach; ?>
<?php endif; ?>
