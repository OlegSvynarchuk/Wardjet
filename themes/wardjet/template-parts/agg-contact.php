<?php
// Contact block – locale-aware with merged page overrides and tolerant ACF keys.

if (!function_exists('wj_get_current_locale_code')) {
    function wj_get_current_locale_code() {
        if (function_exists('get_current_lang_from_url')) {
            $c = strtolower(trim(get_current_lang_from_url()));
            if ($c) return $c; // e.g. es-us
        }
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        if (preg_match('#^/([a-z]{2})/([a-z]{2})(/|$)#i', $path, $m)) {
            return strtolower($m[2] . '-' . $m[1]); // lang-region
        }
        return 'en-us';
    }
}

$locale   = wj_get_current_locale_code();
$fallback = 'en-us';
$is_numeric_form = function($v){ return is_string($v) && preg_match('/^\d+$/', trim($v)); };

$normalize_code = function($codeRaw) {
    $codeRaw = strtolower(trim((string)$codeRaw));
    if (strpos($codeRaw, '/') !== false) { // us/en -> en-us
        $p = explode('/', $codeRaw);
        if (count($p) === 2) return $p[1].'-'.$p[0];
    }
    return $codeRaw;
};

/** Read localized defaults from options (supports repeater OR array; supports alt subfield names). */
function wj_get_locale_defaults($locale, $fallback, $normalize_code) {
    $try_fields = ['contact_localized', 'contact_localized_defaults'];

    // helpers to fetch sub/array with fallback keys
    $get_sub = function(array $keys) {
        foreach ($keys as $k) {
            $v = get_sub_field($k);
            if ($v !== null && $v !== '' && !(is_array($v) && empty(array_filter($v)))) return $v;
        }
        return null;
    };
    $get_arr = function(array $src, array $keys) {
        foreach ($keys as $k) {
            if (array_key_exists($k, $src)) {
                $v = $src[$k];
                if ($v !== null && $v !== '' && !(is_array($v) && empty(array_filter($v)))) return $v;
            }
        }
        return null;
    };

    $extract = function($src = null, $mode = 'sub') use ($get_sub, $get_arr, $normalize_code) {
        $val = function($keys) use ($src, $mode, $get_sub, $get_arr) {
            return $mode === 'sub' ? $get_sub((array)$keys) : $get_arr((array)$src, (array)$keys);
        };

        $code = $val(['region_language_code','region_lang_code','locale','lang_region']);
        $code = $normalize_code($code);

        $form = $val(['contact_form','form','form_id','form_url','form_embed','form_js']);

        return [
            'code'          => $code,
            'left_heading'  => $val(['contact_left_heading','left_title']),
            'left_copy'     => $val(['left_copy','contact_left_copy','left_text']),
            'form_value'    => $form,
            'right_heading' => $val(['contact_right_heading','right_title']),
            'right_copy'    => $val(['contact_right_copy','right_text']),
            'right_cta'     => $val(['contact_right_cta']),
            'show_left'     => ($v = $val(['display_left_column','show_left','display_left']))  !== null ? (bool)$v : true,
            'show_right'    => ($v = $val(['display_right_column','show_right','display_right'])) !== null ? (bool)$v : true,
            '__source'      => '',
        ];
    };

    $picked = null; $fallbackRow = null;

    foreach ($try_fields as $fname) {
        // Repeater path
        if (function_exists('have_rows') && have_rows($fname, 'option')) {
            while (have_rows($fname, 'option')) { the_row();
                $row = $extract(null, 'sub');
                if (!$row['code']) continue;
                if ($row['code'] === $locale)   { $row['__source'] = $fname.'(repeater)'; return $row; }
                if ($row['code'] === $fallback) { $fallbackRow = $row; }
            }
            if ($fallbackRow) { $fallbackRow['__source'] = $fname.'(repeater-fallback)'; return $fallbackRow; }
        }

        // Raw array path (ACF Free exports repeaters as arrays)
        $arr = get_field($fname, 'option');
        if (is_array($arr) && !empty($arr)) {
            foreach ($arr as $r) {
                $row = $extract($r, 'array');
                if (!$row['code']) continue;
                if ($row['code'] === $locale)   { $row['__source'] = $fname.'(array)'; return $row; }
                if ($row['code'] === $fallback && !$fallbackRow) { $fallbackRow = $row; }
            }
            if ($fallbackRow) { $fallbackRow['__source'] = $fname.'(array-fallback)'; return $fallbackRow; }
        }
    }

    // Global options fallback
    return [
        'code'          => $fallback,
        'left_heading'  => get_field('contact_left_heading', 'option'),
        'left_copy'     => get_field('contact_left_copy',    'option'),
        'form_value'    => get_field('contact_form',         'option'),
        'right_heading' => get_field('contact_right_heading','option'),
        'right_copy'    => get_field('contact_right_copy',   'option'),
        'right_cta'     => get_field('contact_right_cta',    'option'),
        'show_left'     => true,
        'show_right'    => true,
        '__source'      => 'global_option',
    ];
}

$defaults = wj_get_locale_defaults($locale, $fallback, $normalize_code);

// Page-level overrides (merge only non-empty)
$page = [
    'left_heading'   => get_field('contact_left_heading'),
    'left_copy'      => get_field('contact_left_copy'),
    'form_value'     => get_field('contact_form'),
    'right_heading'  => get_field('contact_right_heading'),
    'right_copy'     => get_field('contact_right_copy'),
    'right_cta'      => get_field('contact_right_cta'),
    'show_left'      => get_field('display_left_column'),
    'show_right'     => get_field('display_right_column'),
];

$merge = $defaults; $overridden = [];
$maybe_override = function($key) use (&$merge, $page, &$overridden) {
    $v = $page[$key] ?? null;
    $empty_link = is_array($v) && empty(array_filter($v));
    if ($v !== null && $v !== '' && !$empty_link) {
        $merge[$key] = $v; $overridden[] = $key;
    }
};
foreach (['left_heading','left_copy','form_value','right_heading','right_copy','right_cta'] as $k) {
    $maybe_override($k);
}
if ($page['show_left']  !== null) { $merge['show_left']  = (bool)$page['show_left'];  $overridden[]='show_left'; }
if ($page['show_right'] !== null) { $merge['show_right'] = (bool)$page['show_right']; $overridden[]='show_right'; }

$data = $merge;

// form_kind determined after repeater loop reads contact_form_type

// ── New fields – read from the active repeater row (get_sub_field) ────────────
// These are injected as sub-fields into contact_localized via acf/load_field.
// $defaults was built from the active repeater row, so sub-field context is set.
// We re-open the correct row to safely call get_sub_field().
$location_title   = null;
$location_company = null;
$location_address = null;
$location_phone   = null;
$global_heading   = null;
$global_copy      = null;
$global_regions   = null;
$email_us         = null;
$email_us_label   = null;
$call_us          = null;
$call_us_label    = null;
$contact_form_type     = null;
$contact_gravity_id    = null;
$matched_row_index     = null;

$try_repeaters = ['contact_localized', 'contact_localized_defaults'];
foreach ( $try_repeaters as $repeater_name ) {
    if ( ! function_exists('have_rows') ) continue;
    // Reset rows to ensure cursor is fresh after the defaults function iterated
    if ( function_exists('reset_rows') ) reset_rows();
    if ( ! have_rows( $repeater_name, 'option' ) ) continue;
    $repeater_row_i = 0;
    while ( have_rows( $repeater_name, 'option' ) ) {
        the_row();
        $repeater_row_i++;
        $row_code = $normalize_code( get_sub_field('region_language_code') ?: get_sub_field('region_lang_code') ?: get_sub_field('locale') ?: '' );
        if ( $row_code !== $locale && $row_code !== $fallback ) continue;
        // Use this row's sub-fields; prefer locale match over fallback
        $location_title   = get_sub_field('contact_location_title');
        $location_company = get_sub_field('contact_location_company');
        $location_address = get_sub_field('contact_location_address');
        $location_phone   = get_sub_field('contact_location_phone');
        $global_heading   = get_sub_field('contact_global_heading');
        $global_copy      = get_sub_field('contact_global_copy');
        $global_regions   = get_sub_field('contact_global_regions');
        $email_us         = get_sub_field('contact_email_us');
        $email_us_label   = get_sub_field('contact_email_us_label');
        $call_us          = get_sub_field('contact_call_us');
        $call_us_label    = get_sub_field('contact_call_us_label');
        $contact_form_type  = get_sub_field('contact_form_type');
        $contact_gravity_id = get_sub_field('contact_gravity_form_id');
        $matched_row_index  = $repeater_row_i;
        if ( $row_code === $locale ) break 2; // exact match found, stop
    }
    break;
}

// Fix dynamically-injected sub-fields: read directly from options if get_sub_field returned empty
if (isset($matched_row_index)) {
    $opt_prefix = 'options_contact_localized_' . ($matched_row_index - 1) . '_';
    if (empty($location_title))   $location_title   = get_option($opt_prefix . 'contact_location_title', '');
    if (empty($location_company)) $location_company = get_option($opt_prefix . 'contact_location_company', '');
    if (empty($location_address)) $location_address = get_option($opt_prefix . 'contact_location_address', '');
    if (empty($location_phone))   $location_phone   = get_option($opt_prefix . 'contact_location_phone', '');
    if (empty($global_heading))   $global_heading   = get_option($opt_prefix . 'contact_global_heading', '');
    if (empty($global_copy))      $global_copy      = get_option($opt_prefix . 'contact_global_copy', '');
    if (empty($contact_form_type))  $contact_form_type  = get_option($opt_prefix . 'contact_form_type', '');
    if (empty($contact_gravity_id)) $contact_gravity_id = get_option($opt_prefix . 'contact_gravity_form_id', '');
    if (empty($email_us_label))     $email_us_label     = get_option($opt_prefix . 'contact_email_us_label', '');
    if (empty($call_us_label))      $call_us_label      = get_option($opt_prefix . 'contact_call_us_label', '');

    // Fix link fields: get_sub_field may return serialized string instead of array
    $fix_link = function($val, $opt_key) {
        // Already a proper link array with real URL
        if (is_array($val) && !empty($val['url']) && substr($val['url'], 0, 2) !== 'a:') return $val;
        // URL field contains a serialized string — unserialize it
        if (is_array($val) && !empty($val['url']) && substr($val['url'], 0, 2) === 'a:') {
            $decoded = maybe_unserialize($val['url']);
            if (is_array($decoded) && !empty($decoded['url'])) return $decoded;
        }
        // Try reading raw option directly
        $raw = get_option($opt_key, '');
        if (is_string($raw) && substr($raw, 0, 2) === 'a:') {
            $raw = maybe_unserialize($raw);
        }
        return is_array($raw) && !empty($raw['url']) ? $raw : $val;
    };
    $email_us = $fix_link($email_us, $opt_prefix . 'contact_email_us');
    $call_us  = $fix_link($call_us,  $opt_prefix . 'contact_call_us');
}

// Fix nested repeater: read regions directly from options if ACF sub-field context fails
if (!empty($global_regions) && isset($matched_row_index)) {
    $has_empty_keys = false;
    foreach ($global_regions as $r) {
        if (isset($r['']) || (!isset($r['region_name']) && !isset($r['region_countries']))) {
            $has_empty_keys = true;
            break;
        }
    }
    if ($has_empty_keys) {
        $row_idx = $matched_row_index - 1; // 0-based for options
        $count = intval(get_option('options_contact_localized_' . $row_idx . '_contact_global_regions'));
        if ($count > 0) {
            $global_regions = array();
            for ($ri = 0; $ri < $count; $ri++) {
                $prefix = 'options_contact_localized_' . $row_idx . '_contact_global_regions_' . $ri;
                $global_regions[] = array(
                    'region_name'      => get_option($prefix . '_region_name', ''),
                    'region_countries' => get_option($prefix . '_region_countries', ''),
                );
            }
        }
    }
}

// Determine final form kind — check toggle first, fallback to legacy detection
$form_kind = '';
if ($contact_form_type === 'gravity' && !empty($contact_gravity_id)) {
    $form_kind = 'gravityforms';
    $data['form_value'] = $contact_gravity_id;
} elseif (!empty($data['form_value'])) {
    if ($is_numeric_form($data['form_value'])) {
        $form_kind = 'gravityforms';
    } elseif (stripos($data['form_value'], 'jotform.com/jsform') !== false) {
        $form_kind = 'jotform';
    } else {
        $form_kind = 'url';
    }
}

// Section heading/subheading: prefer left_heading/left_copy as the section title/subtitle
$section_heading  = !empty($data['left_heading']) ? $data['left_heading'] : __('Get in Touch', 'wardjet');
$section_subhead  = !empty($data['left_copy'])    ? $data['left_copy']    : '';

// Fallback: read left_copy directly from options if ACF sub-field was empty
if (empty($section_subhead) && isset($matched_row_index)) {
    $section_subhead = get_option('options_contact_localized_' . ($matched_row_index - 1) . '_left_copy', '');
}
if (empty($section_subhead)) {
    $section_subhead = __('Ready to transform your manufacturing capabilities? Contact our team to discuss your CNC routing needs.', 'wardjet');
}

// Hide location card on locations pages (HQ shown on the page itself)
$locations_page_ids = array(17382, 18227, 18344, 19927, 19928, 19929);
$is_locations_page = is_page() && in_array(get_the_ID(), $locations_page_ids, true);

$has_right_col = $location_title || $location_address || $global_heading || $email_us || $call_us;

// ── JotForm CSS override via JavaScript MutationObserver ──
// JotForm injects styles asynchronously — we re-append our overrides
// each time it adds a new <style> or <link> to the document.
if ( $form_kind === 'jotform' ) {
    add_action( 'wp_footer', function() {
        ?>
        <script>
        (function() {
            var CSS = [
                '.contact-section .form-all{font-family:"Montserrat",sans-serif!important;font-size:16px!important;color:#314158!important;background:transparent!important;}',
                '.contact-section ul.page-section{padding:0!important;margin:0!important;background:transparent!important;}',
                '.contact-section .form-line{padding:0!important;margin:0 0 25px 0!important;border-radius:0!important;background:transparent!important;width:100%!important;}',
                '.contact-section .form-label,.contact-section .form-label-top{font-family:"Montserrat",sans-serif!important;font-weight:400!important;font-size:14px!important;line-height:20px!important;letter-spacing:.35px!important;text-transform:uppercase!important;color:#314158!important;margin-bottom:8px!important;display:block!important;}',
                '.contact-section .form-sub-label{font-family:"Montserrat",sans-serif!important;font-size:11px!important;text-transform:none!important;letter-spacing:0!important;color:#45556C!important;margin-top:4px!important;}',
                '.contact-section .form-input,.contact-section .form-input-wide,.contact-section .form-dropdown,.contact-section input[type=text],.contact-section input[type=email],.contact-section input[type=tel],.contact-section input[type=number],.contact-section select,.contact-section textarea{font-family:"Montserrat",sans-serif!important;font-size:16px!important;color:rgba(15,23,43,.5)!important;background:#fff!important;border:1px solid #CAD5E2!important;border-radius:10px!important;padding:12px 16px!important;width:100%!important;box-sizing:border-box!important;-webkit-appearance:none!important;appearance:none!important;box-shadow:none!important;height:auto!important;outline:none!important;}',
                '.contact-section .form-input:focus,.contact-section .form-input-wide:focus,.contact-section input[type=text]:focus,.contact-section input[type=email]:focus,.contact-section input[type=tel]:focus,.contact-section select:focus,.contact-section textarea:focus{border-color:#003B71!important;color:#0F172B!important;box-shadow:none!important;}',
                '.contact-section textarea{min-height:120px!important;resize:vertical!important;}',
                '.contact-section .form-submit-button,.contact-section input[type=submit],.contact-section button[type=submit]{font-family:"Montserrat",sans-serif!important;font-weight:600!important;font-size:16px!important;text-transform:uppercase!important;letter-spacing:.5px!important;color:#fff!important;background:#003B71!important;border:none!important;border-radius:10px!important;padding:16px!important;width:100%!important;cursor:pointer!important;height:auto!important;display:block!important;box-shadow:none!important;}',
                '.contact-section .form-submit-button:hover,.contact-section input[type=submit]:hover,.contact-section button[type=submit]:hover{background:#072C52!important;}',
                '.contact-section .form-line-error .form-input,.contact-section .form-line-error .form-input-wide{border-color:#e53e3e!important;}',
                '.contact-section .form-error-message{font-size:12px!important;color:#e53e3e!important;margin-top:4px!important;}'
            ].join('');

            function injectOverrides() {
                var el = document.getElementById('contact-jf-overrides');
                if (!el) {
                    el = document.createElement('style');
                    el.id = 'contact-jf-overrides';
                }
                el.textContent = CSS;
                document.head.appendChild(el); // always re-append to stay last
            }

            // Inject immediately, on load, and watch for JotForm's async injections
            injectOverrides();
            window.addEventListener('load', injectOverrides);

            var timeout;
            var observer = new MutationObserver(function(mutations) {
                for (var i = 0; i < mutations.length; i++) {
                    var nodes = mutations[i].addedNodes;
                    for (var j = 0; j < nodes.length; j++) {
                        var n = nodes[j];
                        if (n.nodeName === 'STYLE' || n.nodeName === 'LINK') {
                            clearTimeout(timeout);
                            timeout = setTimeout(injectOverrides, 50);
                            break;
                        }
                    }
                }
            });
            observer.observe(document.head, { childList: true });

            // Stop observing after 10s (form should be fully loaded by then)
            setTimeout(function() { observer.disconnect(); }, 10000);
        })();
        </script>
        <?php
    }, 99 );
}
?>

<section class="contact-section">
    <div class="contact-section__container">

        <!-- Section header -->
        <div class="contact-section__header">
            <h2 class="contact-section__heading"><?php echo esc_html($section_heading); ?></h2>
            <?php if ($section_subhead): ?>
                <p class="contact-section__subheading"><?php echo wp_kses_post($section_subhead); ?></p>
            <?php endif; ?>
        </div>

        <!-- Body: form + info -->
        <div class="contact-section__body">

            <!-- Left: Gravity Form -->
            <?php if ($data['show_left'] && !empty($data['form_value'])): ?>
                <div class="contact-section__form-col">
                    <?php
                    if ($form_kind === 'gravityforms') {
                        gravity_form(intval($data['form_value']), false, false, false, '', true, 12, true);
                    } else {
                        echo '<script async type="text/javascript" src="' . esc_url($data['form_value']) . '"></script>';
                    }
                    ?>
                </div>
            <?php endif; ?>

            <!-- Right: location card + global presence + CTA buttons -->
            <?php if ($has_right_col): ?>
                <div class="contact-section__info-col">

                    <!-- Location card -->
                    <?php if (($location_title || $location_address) && !$is_locations_page): ?>
                        <div class="contact-location-card">
                            <div class="contact-location-card__header">
                                <div class="contact-location-card__icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 2C8.134 2 5 5.134 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.866-3.134-7-7-7zm0 9.5a2.5 2.5 0 110-5 2.5 2.5 0 010 5z"/>
                                    </svg>
                                </div>
                                <div class="contact-location-card__title-wrap">
                                    <?php if ($location_title): ?>
                                        <p class="contact-location-card__title"><?php echo esc_html($location_title); ?></p>
                                    <?php endif; ?>
                                    <?php if ($location_company): ?>
                                        <p class="contact-location-card__company"><?php echo esc_html($location_company); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php if ($location_address || $location_phone): ?>
                                <hr class="contact-location-card__divider">
                            <?php endif; ?>

                            <?php if ($location_address): ?>
                                <p class="contact-location-card__address"><?php echo wp_kses_post($location_address); ?></p>
                            <?php endif; ?>

                            <?php if ($location_phone): ?>
                                <a href="tel:<?php echo esc_attr(preg_replace('/[^+\d]/', '', $location_phone)); ?>" class="contact-location-card__phone">
                                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="currentColor">
                                        <path d="M6.62 10.79a15.05 15.05 0 006.59 6.59l2.2-2.2a1 1 0 011.01-.24c1.12.37 2.33.57 3.58.57a1 1 0 011 1V20a1 1 0 01-1 1C9.61 21 3 14.39 3 6a1 1 0 011-1h3.5a1 1 0 011 1c0 1.25.2 2.46.57 3.58a1 1 0 01-.25 1.01l-2.2 2.2z"/>
                                    </svg>
                                    <?php echo esc_html($location_phone); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Global Presence card -->
                    <?php if ($global_heading || $global_copy || $global_regions): ?>
                        <div class="contact-global-card">
                            <?php if ($global_heading): ?>
                                <h3 class="contact-global-card__heading"><?php echo esc_html($global_heading); ?></h3>
                            <?php endif; ?>

                            <?php if ($global_copy): ?>
                                <div class="contact-global-card__copy"><?php echo wp_kses_post($global_copy); ?></div>
                            <?php endif; ?>

                            <?php if (!empty($global_regions)): ?>
                                <div class="contact-global-card__regions">
                                    <?php foreach ($global_regions as $region): ?>
                                        <div class="contact-global-card__region">
                                            <?php if (!empty($region['region_name'])): ?>
                                                <p class="contact-global-card__region-name"><?php echo esc_html($region['region_name']); ?></p>
                                            <?php endif; ?>
                                            <?php if (!empty($region['region_countries'])): ?>
                                                <p class="contact-global-card__region-countries"><?php echo esc_html($region['region_countries']); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Email Us / Call Us buttons -->
                    <?php if ($email_us || $call_us): ?>
                        <div class="contact-section__cta-buttons">
                            <?php if ($email_us && !empty($email_us['url'])): ?>
                                <a href="<?php echo esc_url($email_us['url']); ?>"
                                   class="contact-section__cta-btn"
                                   <?php if (!empty($email_us['target'])) echo 'target="' . esc_attr($email_us['target']) . '"'; ?>>
                                    <img src="<?php echo esc_url(get_template_directory_uri() . '/inc/assets/images/emailicon.svg'); ?>" alt="" width="20" height="20" />
                                    <?php
                                    $email_label_out = !empty($email_us_label)
                                        ? $email_us_label
                                        : (!empty($email_us['title']) ? $email_us['title'] : __('Email Us', 'wardjet'));
                                    echo esc_html($email_label_out);
                                    ?>
                                </a>
                            <?php endif; ?>

                            <?php if ($call_us && !empty($call_us['url'])): ?>
                                <a href="<?php echo esc_url($call_us['url']); ?>"
                                   class="contact-section__cta-btn"
                                   <?php if (!empty($call_us['target'])) echo 'target="' . esc_attr($call_us['target']) . '"'; ?>>
                                    <img src="<?php echo esc_url(get_template_directory_uri() . '/inc/assets/images/phoneicon.svg'); ?>" alt="" width="20" height="20" />
                                    <?php
                                    $call_label_out = !empty($call_us_label)
                                        ? $call_us_label
                                        : (!empty($call_us['title']) ? $call_us['title'] : __('Call Us', 'wardjet'));
                                    echo esc_html($call_label_out);
                                    ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                </div>
            <?php endif; ?>

        </div><!-- .contact-section__body -->

    </div><!-- .contact-section__container -->
</section>
