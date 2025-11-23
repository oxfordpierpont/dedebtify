<?php
/**
 * REST API Class
 *
 * Handles all REST API endpoints for DeDebtify
 *
 * @since      1.0.0
 * @package    Dedebtify
 * @subpackage Dedebtify/includes
 */

class Dedebtify_REST_API {

    /**
     * Register REST API routes
     */
    public static function register_routes() {
        // Plaid endpoints
        register_rest_route( 'dedebtify/v1', '/plaid/create-link-token', array(
            'methods' => 'POST',
            'callback' => array( __CLASS__, 'create_link_token' ),
            'permission_callback' => array( __CLASS__, 'check_user_permission' )
        ) );

        register_rest_route( 'dedebtify/v1', '/plaid/exchange-token', array(
            'methods' => 'POST',
            'callback' => array( __CLASS__, 'exchange_public_token' ),
            'permission_callback' => array( __CLASS__, 'check_user_permission' )
        ) );

        register_rest_route( 'dedebtify/v1', '/plaid/linked-accounts', array(
            'methods' => 'GET',
            'callback' => array( __CLASS__, 'get_linked_accounts' ),
            'permission_callback' => array( __CLASS__, 'check_user_permission' )
        ) );

        register_rest_route( 'dedebtify/v1', '/plaid/sync', array(
            'methods' => 'POST',
            'callback' => array( __CLASS__, 'sync_accounts' ),
            'permission_callback' => array( __CLASS__, 'check_user_permission' )
        ) );

        register_rest_route( 'dedebtify/v1', '/plaid/disconnect', array(
            'methods' => 'POST',
            'callback' => array( __CLASS__, 'disconnect_account' ),
            'permission_callback' => array( __CLASS__, 'check_user_permission' )
        ) );
    }

    /**
     * Check if user is logged in
     *
     * @return bool
     */
    public static function check_user_permission() {
        return is_user_logged_in();
    }

    /**
     * Create Plaid Link token
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public static function create_link_token( $request ) {
        // Check if Plaid is enabled
        if ( ! get_option( 'dedebtify_plaid_enabled', 0 ) ) {
            return new WP_Error(
                'plaid_disabled',
                __( 'Plaid integration is not enabled', 'dedebtify' ),
                array( 'status' => 403 )
            );
        }

        $user_id = get_current_user_id();
        $result = Dedebtify_Plaid::create_link_token( $user_id );

        if ( is_wp_error( $result ) ) {
            return $result;
        }

        return new WP_REST_Response( $result, 200 );
    }

    /**
     * Exchange public token for access token
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public static function exchange_public_token( $request ) {
        $public_token = $request->get_param( 'public_token' );

        // Validate and sanitize input
        if ( empty( $public_token ) ) {
            return new WP_Error(
                'missing_token',
                __( 'Public token is required', 'dedebtify' ),
                array( 'status' => 400 )
            );
        }

        // Sanitize token
        $public_token = sanitize_text_field( $public_token );

        // Validate token format (Plaid tokens start with public- or access-)
        if ( ! preg_match( '/^public-[a-z0-9-]+$/i', $public_token ) ) {
            return new WP_Error(
                'invalid_token',
                __( 'Invalid token format', 'dedebtify' ),
                array( 'status' => 400 )
            );
        }

        $user_id = get_current_user_id();
        $result = Dedebtify_Plaid::exchange_public_token( $public_token, $user_id );

        if ( is_wp_error( $result ) ) {
            return $result;
        }

        // Trigger initial sync in background
        wp_schedule_single_event( time() + 10, 'dedebtify_plaid_initial_sync', array( $user_id ) );

        return new WP_REST_Response( array(
            'success' => true,
            'message' => __( 'Account linked successfully and data is syncing', 'dedebtify' )
        ), 200 );
    }

    /**
     * Get linked Plaid accounts
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public static function get_linked_accounts( $request ) {
        $user_id = get_current_user_id();
        $accounts = Dedebtify_Plaid::get_linked_accounts( $user_id );

        // Remove sensitive data before sending
        $safe_accounts = array_map( function( $account ) {
            return array(
                'item_id' => $account['item_id'],
                'connected_at' => $account['connected_at'],
                'last_sync' => $account['last_sync']
            );
        }, $accounts );

        return new WP_REST_Response( $safe_accounts, 200 );
    }

    /**
     * Sync Plaid accounts
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public static function sync_accounts( $request ) {
        $user_id = get_current_user_id();
        $results = Dedebtify_Plaid::sync_user_accounts( $user_id );

        return new WP_REST_Response( array(
            'success' => true,
            'message' => sprintf(
                __( 'Synced %d accounts. %d errors occurred.', 'dedebtify' ),
                $results['success'],
                $results['errors']
            ),
            'results' => $results
        ), 200 );
    }

    /**
     * Disconnect a Plaid account
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public static function disconnect_account( $request ) {
        $item_id = $request->get_param( 'item_id' );

        // Validate input
        if ( empty( $item_id ) ) {
            return new WP_Error(
                'missing_item_id',
                __( 'Item ID is required', 'dedebtify' ),
                array( 'status' => 400 )
            );
        }

        // Sanitize item ID
        $item_id = sanitize_text_field( $item_id );

        // Verify user owns this item
        $user_id = get_current_user_id();
        $linked_accounts = Dedebtify_Plaid::get_linked_accounts( $user_id );
        $item_exists = false;

        foreach ( $linked_accounts as $account ) {
            if ( $account['item_id'] === $item_id ) {
                $item_exists = true;
                break;
            }
        }

        if ( ! $item_exists ) {
            return new WP_Error(
                'invalid_item',
                __( 'Account not found or you do not have permission to disconnect it', 'dedebtify' ),
                array( 'status' => 403 )
            );
        }

        Dedebtify_Plaid::disconnect_account( $user_id, $item_id );

        return new WP_REST_Response( array(
            'success' => true,
            'message' => __( 'Account disconnected successfully', 'dedebtify' )
        ), 200 );
    }
}

// Register initial sync action
add_action( 'dedebtify_plaid_initial_sync', function( $user_id ) {
    Dedebtify_Plaid::sync_user_accounts( $user_id );
} );

// Register routes on rest_api_init
add_action( 'rest_api_init', array( 'Dedebtify_REST_API', 'register_routes' ) );
