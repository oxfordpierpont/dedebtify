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
    }
}
