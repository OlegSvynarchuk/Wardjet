<?php
/**
 * Plugin Name: WJ Multilingual
 * Description: Custom multilingual system for AXYZ — locale routing, permalinks, language switcher, term/section labels. Replaces WPML.
 * Version: 0.1.0
 * Author: Pixels2Pixels
 * Requires PHP: 7.4
 *
 * Phased migration from theme functions.php → this plugin. Phase 0 ships
 * memoized locale helpers; later phases move routing, menu, permalinks,
 * canonical, term-labels, taxonomy section labels.
 */

if (!defined('ABSPATH')) exit;

define('WJ_MULTILINGUAL_VERSION', '0.1.0');
define('WJ_MULTILINGUAL_DIR', plugin_dir_path(__FILE__));

require_once WJ_MULTILINGUAL_DIR . 'includes/locale.php';
require_once WJ_MULTILINGUAL_DIR . 'includes/routing.php';
require_once WJ_MULTILINGUAL_DIR . 'includes/permalinks.php';
require_once WJ_MULTILINGUAL_DIR . 'includes/menu.php';
require_once WJ_MULTILINGUAL_DIR . 'includes/canonical.php';
require_once WJ_MULTILINGUAL_DIR . 'includes/term-labels.php';
require_once WJ_MULTILINGUAL_DIR . 'includes/acf-term-labels.php';
require_once WJ_MULTILINGUAL_DIR . 'includes/acf-tax-section-labels.php';
require_once WJ_MULTILINGUAL_DIR . 'includes/seo.php';
