<?php
/**
 * Plugin Name: DeDebtify
 * Plugin URI: https://dedebtify.com
 * Description: WordPress plugin for comprehensive debt management, financial tracking, and AI-powered coaching
 * Version: 1.0.0
 * Author: DeDebtify Team
 * Author URI: https://dedebtify.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: dedebtify
 * Domain Path: /languages
 *
 * @package    Dedebtify
 * @author     DeDebtify Team
 * @since      1.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Plugin version
 */
define( 'DEDEBTIFY_VERSION', '1.0.0' );
define( 'DEDEBTIFY_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'DEDEBTIFY_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Load plugin core classes
 */
require_once DEDEBTIFY_PLUGIN_DIR . 'includes/class-dedebtify-helpers.php';
require_once DEDEBTIFY_PLUGIN_DIR . 'includes/class-dedebtify-cpt.php';

/**
 * Load Plaid integration
 */
require_once DEDEBTIFY_PLUGIN_DIR . 'includes/plaid-loader.php';

/**
 * Activation hook
 */
function dedebtify_activate() {
    // Set default options
    add_option( 'dedebtify_ai_provider', 'openrouter' );
    add_option( 'dedebtify_ai_model', 'minimax/minimax-m2' );
    add_option( 'dedebtify_ai_temperature', 0.7 );
    add_option( 'dedebtify_ai_max_tokens', 2000 );
    add_option( 'dedebtify_plaid_enabled', 0 );
    add_option( 'dedebtify_plaid_environment', 'sandbox' );
    add_option( 'dedebtify_plaid_sync_frequency', 'daily' );

    // Flush rewrite rules
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'dedebtify_activate' );

/**
 * Deactivation hook
 */
function dedebtify_deactivate() {
    // Clean up cron jobs
    $timestamp = wp_next_scheduled( 'dedebtify_plaid_auto_sync' );
    if ( $timestamp ) {
        wp_unschedule_event( $timestamp, 'dedebtify_plaid_auto_sync' );
    }

    // Flush rewrite rules
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'dedebtify_deactivate' );

/**
 * Load admin settings page
 */
function dedebtify_admin_menu() {
    add_menu_page(
        __( 'DeDebtify Settings', 'dedebtify' ),
        __( 'DeDebtify', 'dedebtify' ),
        'manage_options',
        'dedebtify-settings',
        'dedebtify_settings_page',
        'dashicons-money-alt',
        30
    );
}
add_action( 'admin_menu', 'dedebtify_admin_menu' );

/**
 * Render settings page
 */
function dedebtify_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( __( 'You do not have sufficient permissions to access this page.', 'dedebtify' ) );
    }

    require_once DEDEBTIFY_PLUGIN_DIR . 'admin/settings-page.php';
}

/**
 * Enqueue admin styles
 */
function dedebtify_admin_styles() {
    wp_enqueue_style( 'dashicons' );
}
add_action( 'admin_enqueue_scripts', 'dedebtify_admin_styles' );

/**
 * Enqueue public styles and scripts
 */
function dedebtify_enqueue_scripts() {
    // Enqueue design system CSS
    wp_enqueue_style(
        'dedebtify-design-system',
        DEDEBTIFY_PLUGIN_URL . 'assets/css/dedebtify-design-system.css',
        array(),
        DEDEBTIFY_VERSION
    );

    // Enqueue mobile app CSS
    wp_enqueue_style(
        'dedebtify-mobile-app',
        DEDEBTIFY_PLUGIN_URL . 'assets/css/dedebtify-mobile-app.css',
        array( 'dedebtify-design-system' ),
        DEDEBTIFY_VERSION
    );

    // Enqueue managers JavaScript
    wp_enqueue_script(
        'dedebtify-managers',
        DEDEBTIFY_PLUGIN_URL . 'assets/js/dedebtify-managers.js',
        array( 'jquery' ),
        DEDEBTIFY_VERSION,
        true
    );

    // Localize script with REST API data
    wp_localize_script( 'dedebtify-managers', 'dedebtify', array(
        'restUrl' => rest_url( 'dedebtify/v1/' ),
        'restNonce' => wp_create_nonce( 'wp_rest' ),
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'nonce' => wp_create_nonce( 'dedebtify_nonce' )
    ) );
}
add_action( 'wp_enqueue_scripts', 'dedebtify_enqueue_scripts' );

/**
 * Load text domain for translations
 */
function dedebtify_load_textdomain() {
    load_plugin_textdomain(
        'dedebtify',
        false,
        dirname( plugin_basename( __FILE__ ) ) . '/languages'
    );
}
add_action( 'plugins_loaded', 'dedebtify_load_textdomain' );
