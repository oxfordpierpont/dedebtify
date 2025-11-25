<?php
/**
 * Plaid Integration Loader
 *
 * Include this file in your main plugin file to enable Plaid integration
 *
 * @since      1.0.0
 * @package    Dedebtify
 * @subpackage Dedebtify/includes
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Load Plaid Integration Classes
 */
function dedebtify_load_plaid() {
    // Load Plaid integration class
    require_once plugin_dir_path( __FILE__ ) . 'class-dedebtify-plaid.php';

    // Load REST API class
    require_once plugin_dir_path( __FILE__ ) . 'class-dedebtify-rest-api.php';
}
add_action( 'plugins_loaded', 'dedebtify_load_plaid' );

/**
 * Enqueue Plaid scripts and styles
 */
function dedebtify_enqueue_plaid_scripts() {
    if ( ! is_user_logged_in() ) {
        return;
    }

    // Only load on account sync page
    if ( is_page() ) {
        global $post;
        if ( $post && has_shortcode( $post->post_content, 'dedebtify_account_sync' ) ) {
            wp_enqueue_script(
                'dedebtify-plaid',
                plugin_dir_url( dirname( __FILE__ ) ) . 'assets/js/dedebtify-plaid.js',
                array( 'jquery' ),
                '1.0.0',
                true
            );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'dedebtify_enqueue_plaid_scripts' );

/**
 * Register shortcode for account sync page
 */
function dedebtify_account_sync_shortcode() {
    ob_start();
    include plugin_dir_path( dirname( __FILE__ ) ) . 'templates/account-sync.php';
    return ob_get_clean();
}
add_shortcode( 'dedebtify_account_sync', 'dedebtify_account_sync_shortcode' );

/**
 * Setup cron job for automatic syncing
 */
function dedebtify_setup_plaid_cron() {
    if ( ! wp_next_scheduled( 'dedebtify_plaid_auto_sync' ) ) {
        $frequency = get_option( 'dedebtify_plaid_sync_frequency', 'daily' );
        wp_schedule_event( time(), $frequency, 'dedebtify_plaid_auto_sync' );
    }
}
add_action( 'wp', 'dedebtify_setup_plaid_cron' );

/**
 * Cron job to auto-sync all users' Plaid accounts
 */
function dedebtify_auto_sync_all_users() {
    $auto_sync = get_option( 'dedebtify_plaid_auto_sync', 0 );

    if ( ! $auto_sync ) {
        return;
    }

    // Get all users who have linked Plaid accounts
    $users = get_users( array(
        'meta_key' => 'dd_plaid_accounts',
        'meta_compare' => 'EXISTS'
    ) );

    foreach ( $users as $user ) {
        Dedebtify_Plaid::sync_user_accounts( $user->ID );
    }
}
add_action( 'dedebtify_plaid_auto_sync', 'dedebtify_auto_sync_all_users' );

/**
 * Add custom cron schedules
 */
function dedebtify_add_cron_schedules( $schedules ) {
    $schedules['hourly'] = array(
        'interval' => 3600,
        'display' => __( 'Every Hour', 'dedebtify' )
    );

    return $schedules;
}
add_filter( 'cron_schedules', 'dedebtify_add_cron_schedules' );

/**
 * Clean up cron job on plugin deactivation
 */
function dedebtify_deactivate_plaid_cron() {
    $timestamp = wp_next_scheduled( 'dedebtify_plaid_auto_sync' );
    if ( $timestamp ) {
        wp_unschedule_event( $timestamp, 'dedebtify_plaid_auto_sync' );
    }
}
register_deactivation_hook( __FILE__, 'dedebtify_deactivate_plaid_cron' );
