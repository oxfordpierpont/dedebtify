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
        // Financial data endpoints
        register_rest_route( 'dedebtify/v1', '/credit-cards', array(
            'methods' => 'GET',
            'callback' => array( __CLASS__, 'get_credit_cards' ),
            'permission_callback' => array( __CLASS__, 'check_user_permission' )
        ) );

        register_rest_route( 'dedebtify/v1', '/loans', array(
            'methods' => 'GET',
            'callback' => array( __CLASS__, 'get_loans' ),
            'permission_callback' => array( __CLASS__, 'check_user_permission' )
        ) );

        register_rest_route( 'dedebtify/v1', '/bills', array(
            'methods' => 'GET',
            'callback' => array( __CLASS__, 'get_bills' ),
            'permission_callback' => array( __CLASS__, 'check_user_permission' )
        ) );

        register_rest_route( 'dedebtify/v1', '/goals', array(
            'methods' => 'GET',
            'callback' => array( __CLASS__, 'get_goals' ),
            'permission_callback' => array( __CLASS__, 'check_user_permission' )
        ) );

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
     * Get user's credit cards
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public static function get_credit_cards( $request ) {
        $user_id = get_current_user_id();

        $args = array(
            'post_type' => 'dd_credit_card',
            'author' => $user_id,
            'posts_per_page' => -1,
            'post_status' => 'publish'
        );

        $posts = get_posts( $args );
        $cards = array();

        foreach ( $posts as $post ) {
            $cards[] = array(
                'id' => $post->ID,
                'name' => $post->post_title,
                'balance' => (float) get_post_meta( $post->ID, 'balance', true ),
                'credit_limit' => (float) get_post_meta( $post->ID, 'credit_limit', true ),
                'interest_rate' => (float) get_post_meta( $post->ID, 'interest_rate', true ),
                'minimum_payment' => (float) get_post_meta( $post->ID, 'minimum_payment', true ),
                'extra_payment' => (float) get_post_meta( $post->ID, 'extra_payment', true ),
                'status' => get_post_meta( $post->ID, 'status', true ),
                'utilization' => self::calculate_utilization(
                    get_post_meta( $post->ID, 'balance', true ),
                    get_post_meta( $post->ID, 'credit_limit', true )
                )
            );
        }

        return new WP_REST_Response( $cards, 200 );
    }

    /**
     * Get user's loans
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public static function get_loans( $request ) {
        $user_id = get_current_user_id();

        $args = array(
            'post_type' => 'dd_loan',
            'author' => $user_id,
            'posts_per_page' => -1,
            'post_status' => 'publish'
        );

        $posts = get_posts( $args );
        $loans = array();

        foreach ( $posts as $post ) {
            $loans[] = array(
                'id' => $post->ID,
                'name' => $post->post_title,
                'type' => get_post_meta( $post->ID, 'type', true ),
                'principal' => (float) get_post_meta( $post->ID, 'principal', true ),
                'current_balance' => (float) get_post_meta( $post->ID, 'current_balance', true ),
                'interest_rate' => (float) get_post_meta( $post->ID, 'interest_rate', true ),
                'monthly_payment' => (float) get_post_meta( $post->ID, 'monthly_payment', true ),
                'extra_payment' => (float) get_post_meta( $post->ID, 'extra_payment', true ),
                'start_date' => get_post_meta( $post->ID, 'start_date', true )
            );
        }

        return new WP_REST_Response( $loans, 200 );
    }

    /**
     * Get user's bills
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public static function get_bills( $request ) {
        $user_id = get_current_user_id();

        $args = array(
            'post_type' => 'dd_bill',
            'author' => $user_id,
            'posts_per_page' => -1,
            'post_status' => 'publish'
        );

        $posts = get_posts( $args );
        $bills = array();

        foreach ( $posts as $post ) {
            $bills[] = array(
                'id' => $post->ID,
                'name' => $post->post_title,
                'category' => get_post_meta( $post->ID, 'category', true ),
                'amount' => (float) get_post_meta( $post->ID, 'amount', true ),
                'due_date' => get_post_meta( $post->ID, 'due_date', true ),
                'frequency' => get_post_meta( $post->ID, 'frequency', true ),
                'status' => get_post_meta( $post->ID, 'status', true )
            );
        }

        return new WP_REST_Response( $bills, 200 );
    }

    /**
     * Get user's goals
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public static function get_goals( $request ) {
        $user_id = get_current_user_id();

        $args = array(
            'post_type' => 'dd_goal',
            'author' => $user_id,
            'posts_per_page' => -1,
            'post_status' => 'publish'
        );

        $posts = get_posts( $args );
        $goals = array();

        foreach ( $posts as $post ) {
            $goals[] = array(
                'id' => $post->ID,
                'name' => $post->post_title,
                'type' => get_post_meta( $post->ID, 'type', true ),
                'target_amount' => (float) get_post_meta( $post->ID, 'target_amount', true ),
                'current_amount' => (float) get_post_meta( $post->ID, 'current_amount', true ),
                'target_date' => get_post_meta( $post->ID, 'target_date', true ),
                'status' => get_post_meta( $post->ID, 'status', true )
            );
        }

        return new WP_REST_Response( $goals, 200 );
    }

    /**
     * Calculate credit utilization
     *
     * @param float $balance
     * @param float $limit
     * @return float
     */
    private static function calculate_utilization( $balance, $limit ) {
        if ( empty( $limit ) || $limit == 0 ) {
            return 0;
        }
        return ( $balance / $limit ) * 100;
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
