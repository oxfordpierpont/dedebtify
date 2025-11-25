<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * @link       https://yoursite.com
 * @since      1.0.0
 * @package    Dedebtify
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

/**
 * Delete all plugin data
 */
function dedebtify_uninstall() {
    global $wpdb;

    // Delete all custom post types
    $post_types = array(
        'dd_credit_card',
        'dd_loan',
        'dd_mortgage',
        'dd_bill',
        'dd_goal',
        'dd_snapshot'
    );

    foreach ( $post_types as $post_type ) {
        $posts = get_posts( array(
            'post_type' => $post_type,
            'numberposts' => -1,
            'post_status' => 'any'
        ));

        foreach ( $posts as $post ) {
            // Delete all post meta
            $wpdb->delete(
                $wpdb->postmeta,
                array( 'post_id' => $post->ID ),
                array( '%d' )
            );

            // Delete the post
            wp_delete_post( $post->ID, true );
        }
    }

    // Delete all user meta
    $wpdb->delete(
        $wpdb->usermeta,
        array( 'meta_key' => 'dd_monthly_income' ),
        array( '%s' )
    );

    $wpdb->delete(
        $wpdb->usermeta,
        array( 'meta_key' => 'dd_target_debt_free_date' ),
        array( '%s' )
    );

    $wpdb->delete(
        $wpdb->usermeta,
        array( 'meta_key' => 'dd_preferred_payoff_method' ),
        array( '%s' )
    );

    $wpdb->delete(
        $wpdb->usermeta,
        array( 'meta_key' => 'dd_notification_preferences' ),
        array( '%s' )
    );

    $wpdb->delete(
        $wpdb->usermeta,
        array( 'meta_key' => 'dd_currency' ),
        array( '%s' )
    );

    // Delete plugin options
    delete_option( 'dedebtify_settings' );
    delete_option( 'dedebtify_version' );
    delete_option( 'dedebtify_activated_time' );

    // For multisite
    if ( is_multisite() ) {
        $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

        foreach ( $blog_ids as $blog_id ) {
            switch_to_blog( $blog_id );

            // Delete posts and meta for this site
            foreach ( $post_types as $post_type ) {
                $posts = get_posts( array(
                    'post_type' => $post_type,
                    'numberposts' => -1,
                    'post_status' => 'any'
                ));

                foreach ( $posts as $post ) {
                    wp_delete_post( $post->ID, true );
                }
            }

            // Delete options for this site
            delete_option( 'dedebtify_settings' );
            delete_option( 'dedebtify_version' );
            delete_option( 'dedebtify_activated_time' );

            restore_current_blog();
        }
    }

    // Clear any cached data
    wp_cache_flush();
}

// Run uninstall
dedebtify_uninstall();
