<?php
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Dedebtify
 * @subpackage Dedebtify/includes
 */

class Dedebtify_Activator {

    /**
     * Plugin activation tasks.
     *
     * - Flush rewrite rules to register CPTs
     * - Create default options
     * - Check for required PHP version and WordPress version
     *
     * @since    1.0.0
     */
    public static function activate() {
        // Check PHP version
        if ( version_compare( PHP_VERSION, '8.0', '<' ) ) {
            deactivate_plugins( DEDEBTIFY_PLUGIN_BASENAME );
            wp_die(
                __( 'DeDebtify requires PHP 8.0 or higher. Your server is running PHP ' . PHP_VERSION, 'dedebtify' ),
                __( 'Plugin Activation Error', 'dedebtify' ),
                array( 'back_link' => true )
            );
        }

        // Check WordPress version
        global $wp_version;
        if ( version_compare( $wp_version, '6.0', '<' ) ) {
            deactivate_plugins( DEDEBTIFY_PLUGIN_BASENAME );
            wp_die(
                __( 'DeDebtify requires WordPress 6.0 or higher. You are running version ' . $wp_version, 'dedebtify' ),
                __( 'Plugin Activation Error', 'dedebtify' ),
                array( 'back_link' => true )
            );
        }

        // Register CPTs temporarily for flush_rewrite_rules()
        require_once DEDEBTIFY_PLUGIN_DIR . 'includes/class-dedebtify-cpt.php';
        $cpt = new Dedebtify_CPT();
        $cpt->register_post_types();

        // Flush rewrite rules
        flush_rewrite_rules();

        // Set default options
        self::set_default_options();

        // Create plugin pages
        self::create_plugin_pages();

        // Add activation timestamp
        update_option( 'dedebtify_activated_time', current_time( 'timestamp' ) );
    }

    /**
     * Set default plugin options.
     *
     * @since    1.0.0
     * @access   private
     */
    private static function set_default_options() {
        // Default plugin settings
        $defaults = array(
            'version' => DEDEBTIFY_VERSION,
            'currency' => 'USD',
            'date_format' => 'F j, Y',
            'enable_notifications' => false,
            'default_payoff_method' => 'avalanche',
        );

        add_option( 'dedebtify_settings', $defaults );

        // Additional individual settings
        add_option( 'dedebtify_currency_symbol', '$' );
        add_option( 'dedebtify_default_interest_rate', 18.0 );
        add_option( 'dedebtify_notifications_enabled', 0 );
        add_option( 'dedebtify_notification_email', get_option( 'admin_email' ) );
        add_option( 'dedebtify_snapshot_frequency', 'monthly' );
        add_option( 'dedebtify_default_payoff_strategy', 'avalanche' );
    }

    /**
     * Create plugin pages.
     *
     * @since    1.0.0
     * @access   private
     */
    private static function create_plugin_pages() {
        // Check if pages already exist
        $pages_created = get_option( 'dedebtify_pages_created', false );
        if ( $pages_created ) {
            return;
        }

        $pages = array(
            'dashboard' => array(
                'title' => __( 'My Debt Dashboard', 'dedebtify' ),
                'content' => '[dedebtify_dashboard]',
                'slug' => 'debt-dashboard',
            ),
            'credit_cards' => array(
                'title' => __( 'Credit Cards', 'dedebtify' ),
                'content' => '[dedebtify_credit_cards]',
                'slug' => 'credit-cards',
            ),
            'loans' => array(
                'title' => __( 'Loans', 'dedebtify' ),
                'content' => '[dedebtify_loans]',
                'slug' => 'loans',
            ),
            'bills' => array(
                'title' => __( 'Bills & Expenses', 'dedebtify' ),
                'content' => '[dedebtify_bills]',
                'slug' => 'bills',
            ),
            'goals' => array(
                'title' => __( 'Financial Goals', 'dedebtify' ),
                'content' => '[dedebtify_goals]',
                'slug' => 'financial-goals',
            ),
            'action_plan' => array(
                'title' => __( 'Debt Action Plan', 'dedebtify' ),
                'content' => '[dedebtify_action_plan]',
                'slug' => 'debt-action-plan',
            ),
            'snapshots' => array(
                'title' => __( 'Progress Tracking', 'dedebtify' ),
                'content' => '[dedebtify_snapshots]',
                'slug' => 'progress-tracking',
            ),
        );

        $page_ids = array();

        foreach ( $pages as $key => $page_data ) {
            // Check if page already exists
            $existing_page = get_page_by_path( $page_data['slug'] );

            if ( ! $existing_page ) {
                // Create the page
                $page_id = wp_insert_post( array(
                    'post_title' => $page_data['title'],
                    'post_content' => $page_data['content'],
                    'post_name' => $page_data['slug'],
                    'post_status' => 'publish',
                    'post_type' => 'page',
                    'comment_status' => 'closed',
                    'ping_status' => 'closed',
                ) );

                if ( $page_id && ! is_wp_error( $page_id ) ) {
                    $page_ids[$key] = $page_id;
                }
            } else {
                $page_ids[$key] = $existing_page->ID;
            }
        }

        // Save page IDs for future reference
        update_option( 'dedebtify_page_ids', $page_ids );
        update_option( 'dedebtify_pages_created', true );

        // Set the dashboard as the main page
        if ( isset( $page_ids['dashboard'] ) ) {
            update_option( 'dedebtify_dashboard_page_id', $page_ids['dashboard'] );
        }
    }
}
