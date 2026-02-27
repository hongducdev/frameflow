<?php

/**
 * Theme functions: init, enqueue scripts and styles, include required files and widgets.
 *
 * @package Case-Themes
 * @since Frameflow 1.0
 */

if (!defined('DEV_MODE')) {
    define('DEV_MODE', true);
}

if (!defined('THEME_DEV_MODE_ELEMENTS') && is_user_logged_in()) {
    define('THEME_DEV_MODE_ELEMENTS', true);
}

require_once get_template_directory() . '/inc/classes/class-main.php';

if (is_admin()) {
    require_once get_template_directory() . '/inc/admin/admin-init.php';
}

/**
 * Theme Require
 */
frameflow()->require_folder('inc');
frameflow()->require_folder('inc/classes');
frameflow()->require_folder('inc/theme-options');
frameflow()->require_folder('template-parts/widgets');
if (class_exists('Woocommerce')) {
    frameflow()->require_folder('woocommerce');
    require_once get_template_directory() . '/woocommerce/wc-pricing-form.php';
}

/**
 * My Events WooCommerce Plugin Activation Code
 * Add this code to activate the My Events WooCommerce plugin
 */
if (!defined('ME_EVENTS_ACTIVATION_CODE')) {
    define('ME_EVENTS_ACTIVATION_CODE', true);
}
