<?php
/**
 * ACF Fields for Hero Video Carousel
 *
 * Registers the video carousel field group for the homepage.
 *
 * @package AXYZ
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register ACF fields for video carousel
 */
function axyz_register_hero_video_carousel_fields() {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_hero_video_carousel',
        'title' => __('Hero Video Carousel', 'axyz'),
        'fields' => array(
            array(
                'key' => 'field_video_carousel',
                'label' => __('Video Slides', 'axyz'),
                'name' => 'video_carousel',
                'type' => 'repeater',
                'instructions' => __('Add 3-5 video slides for the homepage hero carousel.', 'axyz'),
                'required' => 0,
                'min' => 1,
                'max' => 5,
                'layout' => 'block',
                'button_label' => __('Add Video Slide', 'axyz'),
                'sub_fields' => array(
                    array(
                        'key' => 'field_video_file',
                        'label' => __('Video File (Desktop)', 'axyz'),
                        'name' => 'video_file',
                        'type' => 'file',
                        'instructions' => __('Upload an MP4 video file for desktop. Recommended size: 1920x500px, max 10MB.', 'axyz'),
                        'required' => 1,
                        'return_format' => 'array',
                        'mime_types' => 'mp4',
                        'library' => 'all',
                    ),
                    array(
                        'key' => 'field_video_file_mobile',
                        'label' => __('Video File (Mobile)', 'axyz'),
                        'name' => 'video_file_mobile',
                        'type' => 'file',
                        'instructions' => __('Optional MP4 video for mobile devices. Recommended: portrait/square format, smaller file size. If empty, desktop video is used.', 'axyz'),
                        'required' => 0,
                        'return_format' => 'array',
                        'mime_types' => 'mp4',
                        'library' => 'all',
                    ),
                    array(
                        'key' => 'field_video_title',
                        'label' => __('Overlay Title', 'axyz'),
                        'name' => 'video_title',
                        'type' => 'text',
                        'instructions' => __('Text displayed as overlay on the video (e.g., "PROMO VIDEO").', 'axyz'),
                        'required' => 0,
                        'placeholder' => __('PROMO VIDEO', 'axyz'),
                    ),
                    array(
                        'key' => 'field_video_poster',
                        'label' => __('Poster Image', 'axyz'),
                        'name' => 'video_poster',
                        'type' => 'image',
                        'instructions' => __('Optional fallback image shown before video loads.', 'axyz'),
                        'required' => 0,
                        'return_format' => 'array',
                        'preview_size' => 'large',
                        'library' => 'all',
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'page_template',
                    'operator' => '==',
                    'value' => 'wardjet-homepage.php',
                ),
            ),
            array(
                array(
                    'param' => 'page_template',
                    'operator' => '==',
                    'value' => 'wardjet-our-products.php',
                ),
            ),
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'products',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => __('Video carousel slides for the homepage hero section.', 'axyz'),
    ));
}

// Hook to register fields after ACF is loaded
add_action('acf/init', 'axyz_register_hero_video_carousel_fields');
