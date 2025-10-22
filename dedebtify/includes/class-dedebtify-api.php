<?php
/**
 * REST API endpoints for DeDebtify.
 *
 * This class handles all REST API endpoints for AJAX requests.
 *
 * @since      1.0.0
 * @package    Dedebtify
 * @subpackage Dedebtify/includes
 */

class Dedebtify_API {

    /**
     * Register REST API routes.
     *
     * @since    1.0.0
     */
    public function register_routes() {
        $namespace = 'dedebtify/v1';

        // Dashboard data endpoint
        register_rest_route( $namespace, '/dashboard', array(
            'methods' => 'GET',
            'callback' => array( $this, 'get_dashboard_data' ),
            'permission_callback' => array( $this, 'check_user_permission' ),
        ));

        // Create snapshot endpoint
        register_rest_route( $namespace, '/snapshot', array(
            'methods' => 'POST',
            'callback' => array( $this, 'create_snapshot' ),
            'permission_callback' => array( $this, 'check_user_permission' ),
        ));

        // Calculate payoff endpoint
        register_rest_route( $namespace, '/calculate-payoff', array(
            'methods' => 'POST',
            'callback' => array( $this, 'calculate_payoff' ),
            'permission_callback' => array( $this, 'check_user_permission' ),
            'args' => array(
                'balance' => array(
                    'required' => true,
                    'type' => 'number',
                ),
                'interest_rate' => array(
                    'required' => true,
                    'type' => 'number',
                ),
                'monthly_payment' => array(
                    'required' => true,
                    'type' => 'number',
                ),
            ),
        ));

        // Get debt payoff order endpoint
        register_rest_route( $namespace, '/payoff-order/(?P<method>avalanche|snowball)', array(
            'methods' => 'GET',
            'callback' => array( $this, 'get_payoff_order' ),
            'permission_callback' => array( $this, 'check_user_permission' ),
        ));

        // Get user statistics
        register_rest_route( $namespace, '/statistics', array(
            'methods' => 'GET',
            'callback' => array( $this, 'get_user_statistics' ),
            'permission_callback' => array( $this, 'check_user_permission' ),
        ));

        // Get all credit cards
        register_rest_route( $namespace, '/credit-cards', array(
            'methods' => 'GET',
            'callback' => array( $this, 'get_credit_cards' ),
            'permission_callback' => array( $this, 'check_user_permission' ),
        ));

        // Get all loans
        register_rest_route( $namespace, '/loans', array(
            'methods' => 'GET',
            'callback' => array( $this, 'get_loans' ),
            'permission_callback' => array( $this, 'check_user_permission' ),
        ));

        // Get all bills
        register_rest_route( $namespace, '/bills', array(
            'methods' => 'GET',
            'callback' => array( $this, 'get_bills' ),
            'permission_callback' => array( $this, 'check_user_permission' ),
        ));

        // Get all goals
        register_rest_route( $namespace, '/goals', array(
            'methods' => 'GET',
            'callback' => array( $this, 'get_goals' ),
            'permission_callback' => array( $this, 'check_user_permission' ),
        ));

        // Get snapshots history
        register_rest_route( $namespace, '/snapshots', array(
            'methods' => 'GET',
            'callback' => array( $this, 'get_snapshots' ),
            'permission_callback' => array( $this, 'check_user_permission' ),
        ));

        // Admin stats endpoint
        register_rest_route( $namespace, '/stats', array(
            'methods' => 'GET',
            'callback' => array( $this, 'get_admin_stats' ),
            'permission_callback' => array( $this, 'check_admin_permission' ),
        ));

        // Recent activity endpoint
        register_rest_route( $namespace, '/activity', array(
            'methods' => 'GET',
            'callback' => array( $this, 'get_recent_activity' ),
            'permission_callback' => array( $this, 'check_admin_permission' ),
        ));
    }

    /**
     * Check if user has permission to access endpoint.
     *
     * @since    1.0.0
     * @return   bool
     */
    public function check_user_permission() {
        return is_user_logged_in();
    }

    /**
     * Check if user has admin permission.
     *
     * @since    1.0.0
     * @return   bool
     */
    public function check_admin_permission() {
        return current_user_can( 'manage_options' );
    }

    /**
     * Get dashboard data.
     *
     * @since    1.0.0
     * @param    WP_REST_Request    $request
     * @return   WP_REST_Response
     */
    public function get_dashboard_data( $request ) {
        $user_id = get_current_user_id();

        $data = array(
            'total_debt' => Dedebtify_Calculations::get_total_debt( $user_id ),
            'credit_card_debt' => Dedebtify_Calculations::get_total_credit_card_debt( $user_id ),
            'loan_debt' => Dedebtify_Calculations::get_total_loan_debt( $user_id ),
            'mortgage_debt' => Dedebtify_Calculations::get_total_mortgage_debt( $user_id ),
            'monthly_payments' => Dedebtify_Calculations::get_total_monthly_payments( $user_id ),
            'monthly_bills' => Dedebtify_Calculations::get_total_monthly_bills( $user_id ),
            'dti_ratio' => Dedebtify_Calculations::get_user_dti( $user_id ),
            'credit_utilization' => Dedebtify_Calculations::get_overall_credit_utilization( $user_id ),
            'monthly_income' => get_user_meta( $user_id, 'dd_monthly_income', true ),
        );

        return rest_ensure_response( $data );
    }

    /**
     * Create financial snapshot.
     *
     * @since    1.0.0
     * @param    WP_REST_Request    $request
     * @return   WP_REST_Response
     */
    public function create_snapshot( $request ) {
        $user_id = get_current_user_id();
        $post_id = Dedebtify_Calculations::create_snapshot( $user_id );

        if ( is_wp_error( $post_id ) ) {
            return new WP_Error(
                'snapshot_creation_failed',
                __( 'Failed to create snapshot', 'dedebtify' ),
                array( 'status' => 500 )
            );
        }

        return rest_ensure_response( array(
            'success' => true,
            'message' => __( 'Snapshot created successfully', 'dedebtify' ),
            'post_id' => $post_id,
        ));
    }

    /**
     * Calculate debt payoff.
     *
     * @since    1.0.0
     * @param    WP_REST_Request    $request
     * @return   WP_REST_Response
     */
    public function calculate_payoff( $request ) {
        $balance = floatval( $request->get_param( 'balance' ) );
        $interest_rate = floatval( $request->get_param( 'interest_rate' ) );
        $monthly_payment = floatval( $request->get_param( 'monthly_payment' ) );

        $months = Dedebtify_Calculations::calculate_months_to_payoff( $balance, $interest_rate, $monthly_payment );
        $total_interest = Dedebtify_Calculations::calculate_total_interest( $balance, $monthly_payment, $months );
        $payoff_date = Dedebtify_Calculations::calculate_payoff_date( $months );

        $data = array(
            'months_to_payoff' => $months,
            'total_interest' => $total_interest,
            'payoff_date' => $payoff_date,
            'total_paid' => $monthly_payment * $months,
        );

        return rest_ensure_response( $data );
    }

    /**
     * Get debt payoff order.
     *
     * @since    1.0.0
     * @param    WP_REST_Request    $request
     * @return   WP_REST_Response
     */
    public function get_payoff_order( $request ) {
        $user_id = get_current_user_id();
        $method = $request->get_param( 'method' );

        if ( $method === 'avalanche' ) {
            $order = Dedebtify_Calculations::get_avalanche_order( $user_id );
        } else {
            $order = Dedebtify_Calculations::get_snowball_order( $user_id );
        }

        return rest_ensure_response( $order );
    }

    /**
     * Get user statistics.
     *
     * @since    1.0.0
     * @param    WP_REST_Request    $request
     * @return   WP_REST_Response
     */
    public function get_user_statistics( $request ) {
        $user_id = get_current_user_id();

        $credit_cards = Dedebtify_Calculations::get_user_credit_cards( $user_id );
        $loans = Dedebtify_Calculations::get_user_loans( $user_id );
        $bills = Dedebtify_Calculations::get_user_bills( $user_id );
        $goals = Dedebtify_Calculations::get_user_goals( $user_id );

        $active_cards = 0;
        foreach ( $credit_cards as $card ) {
            $status = get_post_meta( $card->ID, 'status', true );
            if ( $status === 'active' ) {
                $active_cards++;
            }
        }

        $data = array(
            'total_credit_cards' => count( $credit_cards ),
            'active_credit_cards' => $active_cards,
            'total_loans' => count( $loans ),
            'total_bills' => count( $bills ),
            'total_goals' => count( $goals ),
            'has_mortgage' => Dedebtify_Calculations::get_user_mortgage( $user_id ) !== null,
        );

        return rest_ensure_response( $data );
    }

    /**
     * Get all credit cards with calculated data.
     *
     * @since    1.0.0
     * @param    WP_REST_Request    $request
     * @return   WP_REST_Response
     */
    public function get_credit_cards( $request ) {
        $user_id = get_current_user_id();
        $cards = Dedebtify_Calculations::get_user_credit_cards( $user_id );
        $cards_data = array();

        foreach ( $cards as $card ) {
            $balance = floatval( get_post_meta( $card->ID, 'balance', true ) );
            $credit_limit = floatval( get_post_meta( $card->ID, 'credit_limit', true ) );
            $interest_rate = floatval( get_post_meta( $card->ID, 'interest_rate', true ) );
            $min_payment = floatval( get_post_meta( $card->ID, 'minimum_payment', true ) );
            $extra_payment = floatval( get_post_meta( $card->ID, 'extra_payment', true ) );
            $status = get_post_meta( $card->ID, 'status', true );

            $total_payment = $min_payment + $extra_payment;
            $months_to_payoff = Dedebtify_Calculations::calculate_months_to_payoff( $balance, $interest_rate, $total_payment );
            $total_interest = Dedebtify_Calculations::calculate_total_interest( $balance, $total_payment, $months_to_payoff );
            $utilization = Dedebtify_Calculations::calculate_utilization( $balance, $credit_limit );

            $cards_data[] = array(
                'id' => $card->ID,
                'name' => $card->post_title,
                'balance' => $balance,
                'credit_limit' => $credit_limit,
                'interest_rate' => $interest_rate,
                'minimum_payment' => $min_payment,
                'extra_payment' => $extra_payment,
                'status' => $status,
                'utilization' => $utilization,
                'months_to_payoff' => $months_to_payoff,
                'total_interest' => $total_interest,
                'payoff_date' => Dedebtify_Calculations::calculate_payoff_date( $months_to_payoff ),
            );
        }

        return rest_ensure_response( $cards_data );
    }

    /**
     * Get all loans with calculated data.
     *
     * @since    1.0.0
     * @param    WP_REST_Request    $request
     * @return   WP_REST_Response
     */
    public function get_loans( $request ) {
        $user_id = get_current_user_id();
        $loans = Dedebtify_Calculations::get_user_loans( $user_id );
        $loans_data = array();

        foreach ( $loans as $loan ) {
            $balance = floatval( get_post_meta( $loan->ID, 'current_balance', true ) );
            $interest_rate = floatval( get_post_meta( $loan->ID, 'interest_rate', true ) );
            $monthly_payment = floatval( get_post_meta( $loan->ID, 'monthly_payment', true ) );
            $extra_payment = floatval( get_post_meta( $loan->ID, 'extra_payment', true ) );

            $total_payment = $monthly_payment + $extra_payment;
            $months_to_payoff = Dedebtify_Calculations::calculate_months_to_payoff( $balance, $interest_rate, $total_payment );

            $loans_data[] = array(
                'id' => $loan->ID,
                'name' => $loan->post_title,
                'type' => get_post_meta( $loan->ID, 'loan_type', true ),
                'balance' => $balance,
                'interest_rate' => $interest_rate,
                'monthly_payment' => $monthly_payment,
                'extra_payment' => $extra_payment,
                'months_to_payoff' => $months_to_payoff,
                'payoff_date' => Dedebtify_Calculations::calculate_payoff_date( $months_to_payoff ),
            );
        }

        return rest_ensure_response( $loans_data );
    }

    /**
     * Get all bills with calculated data.
     *
     * @since    1.0.0
     * @param    WP_REST_Request    $request
     * @return   WP_REST_Response
     */
    public function get_bills( $request ) {
        $user_id = get_current_user_id();
        $bills = Dedebtify_Calculations::get_user_bills( $user_id );
        $bills_data = array();

        foreach ( $bills as $bill ) {
            $amount = floatval( get_post_meta( $bill->ID, 'amount', true ) );
            $frequency = get_post_meta( $bill->ID, 'frequency', true );
            $monthly_equivalent = Dedebtify_Calculations::convert_to_monthly( $amount, $frequency );

            $bills_data[] = array(
                'id' => $bill->ID,
                'name' => $bill->post_title,
                'category' => get_post_meta( $bill->ID, 'category', true ),
                'amount' => $amount,
                'frequency' => $frequency,
                'monthly_equivalent' => $monthly_equivalent,
                'due_date' => get_post_meta( $bill->ID, 'due_date', true ),
                'auto_pay' => get_post_meta( $bill->ID, 'auto_pay', true ),
                'is_essential' => get_post_meta( $bill->ID, 'is_essential', true ),
            );
        }

        return rest_ensure_response( $bills_data );
    }

    /**
     * Get all goals with calculated data.
     *
     * @since    1.0.0
     * @param    WP_REST_Request    $request
     * @return   WP_REST_Response
     */
    public function get_goals( $request ) {
        $user_id = get_current_user_id();
        $goals = Dedebtify_Calculations::get_user_goals( $user_id );
        $goals_data = array();

        foreach ( $goals as $goal ) {
            $target_amount = floatval( get_post_meta( $goal->ID, 'target_amount', true ) );
            $current_amount = floatval( get_post_meta( $goal->ID, 'current_amount', true ) );
            $monthly_contribution = floatval( get_post_meta( $goal->ID, 'monthly_contribution', true ) );

            $remaining = $target_amount - $current_amount;
            $progress_percentage = $target_amount > 0 ? ( $current_amount / $target_amount ) * 100 : 0;
            $months_to_goal = $monthly_contribution > 0 ? ceil( $remaining / $monthly_contribution ) : INF;

            $goals_data[] = array(
                'id' => $goal->ID,
                'name' => $goal->post_title,
                'type' => get_post_meta( $goal->ID, 'goal_type', true ),
                'target_amount' => $target_amount,
                'current_amount' => $current_amount,
                'monthly_contribution' => $monthly_contribution,
                'remaining_amount' => $remaining,
                'progress_percentage' => round( $progress_percentage, 2 ),
                'months_to_goal' => $months_to_goal,
                'target_date' => get_post_meta( $goal->ID, 'target_date', true ),
                'priority' => get_post_meta( $goal->ID, 'priority', true ),
            );
        }

        return rest_ensure_response( $goals_data );
    }

    /**
     * Get snapshots history.
     *
     * @since    1.0.0
     * @param    WP_REST_Request    $request
     * @return   WP_REST_Response
     */
    public function get_snapshots( $request ) {
        $user_id = get_current_user_id();

        $args = array(
            'post_type' => 'dd_snapshot',
            'author' => $user_id,
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC',
        );

        $snapshots = get_posts( $args );
        $snapshots_data = array();

        foreach ( $snapshots as $snapshot ) {
            $snapshots_data[] = array(
                'id' => $snapshot->ID,
                'date' => get_post_meta( $snapshot->ID, 'snapshot_date', true ),
                'total_debt' => floatval( get_post_meta( $snapshot->ID, 'total_debt', true ) ),
                'credit_card_debt' => floatval( get_post_meta( $snapshot->ID, 'total_credit_card_debt', true ) ),
                'loan_debt' => floatval( get_post_meta( $snapshot->ID, 'total_loan_debt', true ) ),
                'mortgage_debt' => floatval( get_post_meta( $snapshot->ID, 'total_mortgage_debt', true ) ),
                'monthly_payments' => floatval( get_post_meta( $snapshot->ID, 'total_monthly_payments', true ) ),
                'monthly_bills' => floatval( get_post_meta( $snapshot->ID, 'total_monthly_bills', true ) ),
                'dti_ratio' => floatval( get_post_meta( $snapshot->ID, 'debt_to_income_ratio', true ) ),
                'credit_utilization' => floatval( get_post_meta( $snapshot->ID, 'credit_utilization', true ) ),
            );
        }

        return rest_ensure_response( $snapshots_data );
    }

    /**
     * Get admin statistics.
     *
     * @since    1.0.0
     * @param    WP_REST_Request    $request
     * @return   WP_REST_Response
     */
    public function get_admin_stats( $request ) {
        $stats = array(
            'total_users' => count_users()['total_users'],
            'total_credit_cards' => wp_count_posts('dd_credit_card')->publish,
            'total_loans' => wp_count_posts('dd_loan')->publish,
            'total_bills' => wp_count_posts('dd_bill')->publish,
            'total_goals' => wp_count_posts('dd_goal')->publish,
            'total_snapshots' => wp_count_posts('dd_snapshot')->publish,
        );

        // Calculate total debt across all users
        $total_debt = 0;

        // Get all credit card balances
        $cards = get_posts(array(
            'post_type' => 'dd_credit_card',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ));
        foreach ($cards as $card) {
            $balance = get_post_meta($card->ID, 'balance', true);
            $total_debt += floatval($balance);
        }

        // Get all loan balances
        $loans = get_posts(array(
            'post_type' => 'dd_loan',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ));
        foreach ($loans as $loan) {
            $balance = get_post_meta($loan->ID, 'balance', true);
            $total_debt += floatval($balance);
        }

        $stats['total_debt'] = $total_debt;

        return rest_ensure_response( $stats );
    }

    /**
     * Get recent activity.
     *
     * @since    1.0.0
     * @param    WP_REST_Request    $request
     * @return   WP_REST_Response
     */
    public function get_recent_activity( $request ) {
        $activities = array();
        $post_types = array('dd_credit_card', 'dd_loan', 'dd_bill', 'dd_goal', 'dd_snapshot');

        foreach ($post_types as $post_type) {
            $posts = get_posts(array(
                'post_type' => $post_type,
                'posts_per_page' => 5,
                'post_status' => 'publish',
                'orderby' => 'modified',
                'order' => 'DESC'
            ));

            foreach ($posts as $post) {
                $author = get_userdata($post->post_author);
                $type_label = str_replace('dd_', '', $post_type);
                $type_label = str_replace('_', ' ', $type_label);

                $activities[] = array(
                    'id' => $post->ID,
                    'type' => $type_label,
                    'title' => $post->post_title,
                    'author' => $author ? $author->display_name : 'Unknown',
                    'action' => 'Updated',
                    'date' => $post->post_modified,
                );
            }
        }

        // Sort by date descending
        usort($activities, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        // Return top 20
        $activities = array_slice($activities, 0, 20);

        return rest_ensure_response( $activities );
    }
}
