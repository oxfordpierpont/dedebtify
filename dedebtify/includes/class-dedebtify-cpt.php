<?php
/**
 * Custom Post Types Registration
 *
 * Registers all DeDebtify custom post types for financial data
 *
 * @since      1.0.0
 * @package    Dedebtify
 * @subpackage Dedebtify/includes
 */

class Dedebtify_CPT {

    /**
     * Register all custom post types
     */
    public static function register_post_types() {
        self::register_credit_card_cpt();
        self::register_loan_cpt();
        self::register_mortgage_cpt();
        self::register_bill_cpt();
        self::register_goal_cpt();
    }

    /**
     * Register Credit Card CPT
     */
    private static function register_credit_card_cpt() {
        $labels = array(
            'name' => __( 'Credit Cards', 'dedebtify' ),
            'singular_name' => __( 'Credit Card', 'dedebtify' ),
            'add_new' => __( 'Add New', 'dedebtify' ),
            'add_new_item' => __( 'Add New Credit Card', 'dedebtify' ),
            'edit_item' => __( 'Edit Credit Card', 'dedebtify' ),
            'new_item' => __( 'New Credit Card', 'dedebtify' ),
            'view_item' => __( 'View Credit Card', 'dedebtify' ),
            'search_items' => __( 'Search Credit Cards', 'dedebtify' ),
            'not_found' => __( 'No credit cards found', 'dedebtify' ),
            'not_found_in_trash' => __( 'No credit cards found in trash', 'dedebtify' )
        );

        $args = array(
            'labels' => $labels,
            'public' => false,
            'show_ui' => false,
            'capability_type' => 'post',
            'hierarchical' => false,
            'supports' => array( 'title', 'author' ),
            'has_archive' => false,
            'rewrite' => false,
            'query_var' => false,
            'can_export' => true,
            'delete_with_user' => true
        );

        register_post_type( 'dd_credit_card', $args );
    }

    /**
     * Register Loan CPT
     */
    private static function register_loan_cpt() {
        $labels = array(
            'name' => __( 'Loans', 'dedebtify' ),
            'singular_name' => __( 'Loan', 'dedebtify' ),
            'add_new' => __( 'Add New', 'dedebtify' ),
            'add_new_item' => __( 'Add New Loan', 'dedebtify' ),
            'edit_item' => __( 'Edit Loan', 'dedebtify' ),
            'new_item' => __( 'New Loan', 'dedebtify' ),
            'view_item' => __( 'View Loan', 'dedebtify' ),
            'search_items' => __( 'Search Loans', 'dedebtify' ),
            'not_found' => __( 'No loans found', 'dedebtify' ),
            'not_found_in_trash' => __( 'No loans found in trash', 'dedebtify' )
        );

        $args = array(
            'labels' => $labels,
            'public' => false,
            'show_ui' => false,
            'capability_type' => 'post',
            'hierarchical' => false,
            'supports' => array( 'title', 'author' ),
            'has_archive' => false,
            'rewrite' => false,
            'query_var' => false,
            'can_export' => true,
            'delete_with_user' => true
        );

        register_post_type( 'dd_loan', $args );
    }

    /**
     * Register Mortgage CPT
     */
    private static function register_mortgage_cpt() {
        $labels = array(
            'name' => __( 'Mortgages', 'dedebtify' ),
            'singular_name' => __( 'Mortgage', 'dedebtify' ),
            'add_new' => __( 'Add New', 'dedebtify' ),
            'add_new_item' => __( 'Add New Mortgage', 'dedebtify' ),
            'edit_item' => __( 'Edit Mortgage', 'dedebtify' ),
            'new_item' => __( 'New Mortgage', 'dedebtify' ),
            'view_item' => __( 'View Mortgage', 'dedebtify' ),
            'search_items' => __( 'Search Mortgages', 'dedebtify' ),
            'not_found' => __( 'No mortgages found', 'dedebtify' ),
            'not_found_in_trash' => __( 'No mortgages found in trash', 'dedebtify' )
        );

        $args = array(
            'labels' => $labels,
            'public' => false,
            'show_ui' => false,
            'capability_type' => 'post',
            'hierarchical' => false,
            'supports' => array( 'title', 'author' ),
            'has_archive' => false,
            'rewrite' => false,
            'query_var' => false,
            'can_export' => true,
            'delete_with_user' => true
        );

        register_post_type( 'dd_mortgage', $args );
    }

    /**
     * Register Bill CPT
     */
    private static function register_bill_cpt() {
        $labels = array(
            'name' => __( 'Bills', 'dedebtify' ),
            'singular_name' => __( 'Bill', 'dedebtify' ),
            'add_new' => __( 'Add New', 'dedebtify' ),
            'add_new_item' => __( 'Add New Bill', 'dedebtify' ),
            'edit_item' => __( 'Edit Bill', 'dedebtify' ),
            'new_item' => __( 'New Bill', 'dedebtify' ),
            'view_item' => __( 'View Bill', 'dedebtify' ),
            'search_items' => __( 'Search Bills', 'dedebtify' ),
            'not_found' => __( 'No bills found', 'dedebtify' ),
            'not_found_in_trash' => __( 'No bills found in trash', 'dedebtify' )
        );

        $args = array(
            'labels' => $labels,
            'public' => false,
            'show_ui' => false,
            'capability_type' => 'post',
            'hierarchical' => false,
            'supports' => array( 'title', 'author' ),
            'has_archive' => false,
            'rewrite' => false,
            'query_var' => false,
            'can_export' => true,
            'delete_with_user' => true
        );

        register_post_type( 'dd_bill', $args );
    }

    /**
     * Register Goal CPT
     */
    private static function register_goal_cpt() {
        $labels = array(
            'name' => __( 'Goals', 'dedebtify' ),
            'singular_name' => __( 'Goal', 'dedebtify' ),
            'add_new' => __( 'Add New', 'dedebtify' ),
            'add_new_item' => __( 'Add New Goal', 'dedebtify' ),
            'edit_item' => __( 'Edit Goal', 'dedebtify' ),
            'new_item' => __( 'New Goal', 'dedebtify' ),
            'view_item' => __( 'View Goal', 'dedebtify' ),
            'search_items' => __( 'Search Goals', 'dedebtify' ),
            'not_found' => __( 'No goals found', 'dedebtify' ),
            'not_found_in_trash' => __( 'No goals found in trash', 'dedebtify' )
        );

        $args = array(
            'labels' => $labels,
            'public' => false,
            'show_ui' => false,
            'capability_type' => 'post',
            'hierarchical' => false,
            'supports' => array( 'title', 'author' ),
            'has_archive' => false,
            'rewrite' => false,
            'query_var' => false,
            'can_export' => true,
            'delete_with_user' => true
        );

        register_post_type( 'dd_goal', $args );
    }
}

// Register CPTs on init
add_action( 'init', array( 'Dedebtify_CPT', 'register_post_types' ) );
