<?php
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * @since      1.0.0
 * @package    Dedebtify
 * @subpackage Dedebtify/includes
 */

class Dedebtify {

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * @since    1.0.0
     */
    public function __construct() {
        $this->version = DEDEBTIFY_VERSION;
        $this->plugin_name = 'dedebtify';

        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->define_cpt_hooks();
        $this->define_api_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {
        // Load helper functions
        require_once DEDEBTIFY_PLUGIN_DIR . 'includes/class-dedebtify-helpers.php';

        // Load CPT registration class
        require_once DEDEBTIFY_PLUGIN_DIR . 'includes/class-dedebtify-cpt.php';

        // Load calculations class
        require_once DEDEBTIFY_PLUGIN_DIR . 'includes/class-dedebtify-calculations.php';

        // Load REST API class
        require_once DEDEBTIFY_PLUGIN_DIR . 'includes/class-dedebtify-api.php';

        // Load page templates class
        require_once DEDEBTIFY_PLUGIN_DIR . 'includes/class-dedebtify-page-templates.php';

        // Load dummy data class
        require_once DEDEBTIFY_PLUGIN_DIR . 'includes/class-dedebtify-dummy-data.php';

        // Load PWA class
        require_once DEDEBTIFY_PLUGIN_DIR . 'includes/class-dedebtify-pwa.php';

        // Load Elementor integration class (if Elementor is active)
        if ( did_action( 'elementor/loaded' ) ) {
            require_once DEDEBTIFY_PLUGIN_DIR . 'includes/class-dedebtify-elementor.php';
        }

        // Initialize page templates
        new Dedebtify_Page_Templates();

        // Initialize PWA
        new Dedebtify_PWA();
    }

    /**
     * Register all of the hooks related to the admin area functionality.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {
        // Enqueue admin styles and scripts
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );

        // Add admin menu pages
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );

        // Add settings link to plugins page
        add_filter( 'plugin_action_links_' . DEDEBTIFY_PLUGIN_BASENAME, array( $this, 'add_settings_link' ) );
    }

    /**
     * Register all of the hooks related to the public-facing functionality.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {
        // Enqueue public styles and scripts
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public_assets' ) );

        // Add dynamic custom CSS
        add_action( 'wp_head', array( $this, 'output_custom_css' ) );

        // Add shortcode support
        add_shortcode( 'dedebtify_dashboard', array( $this, 'render_dashboard_shortcode' ) );
        add_shortcode( 'dedebtify_credit_cards', array( $this, 'render_credit_cards_shortcode' ) );
        add_shortcode( 'dedebtify_loans', array( $this, 'render_loans_shortcode' ) );
        add_shortcode( 'dedebtify_mortgages', array( $this, 'render_mortgages_shortcode' ) );
        add_shortcode( 'dedebtify_bills', array( $this, 'render_bills_shortcode' ) );
        add_shortcode( 'dedebtify_goals', array( $this, 'render_goals_shortcode' ) );
        add_shortcode( 'dedebtify_action_plan', array( $this, 'render_action_plan_shortcode' ) );
        add_shortcode( 'dedebtify_snapshots', array( $this, 'render_snapshots_shortcode' ) );
        add_shortcode( 'dedebtify_ai_coach', array( $this, 'render_ai_coach_shortcode' ) );
    }

    /**
     * Register all hooks related to Custom Post Types.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_cpt_hooks() {
        $cpt = new Dedebtify_CPT();

        // Register CPTs
        add_action( 'init', array( $cpt, 'register_post_types' ) );

        // Add meta boxes
        add_action( 'add_meta_boxes', array( $cpt, 'add_meta_boxes' ) );

        // Save meta data
        add_action( 'save_post', array( $cpt, 'save_meta_data' ), 10, 2 );

        // Modify post type columns
        add_filter( 'manage_dd_credit_card_posts_columns', array( $cpt, 'set_custom_columns' ) );
        add_action( 'manage_dd_credit_card_posts_custom_column', array( $cpt, 'custom_column_content' ), 10, 2 );
    }

    /**
     * Register all hooks related to the REST API.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_api_hooks() {
        $api = new Dedebtify_API();

        // Register REST API endpoints
        add_action( 'rest_api_init', array( $api, 'register_routes' ) );
    }

    /**
     * Run the plugin.
     *
     * @since    1.0.0
     */
    public function run() {
        // Plugin is now running
    }

    /**
     * Enqueue admin-specific styles and scripts.
     *
     * @since    1.0.0
     */
    public function enqueue_admin_assets() {
        // Enqueue design system first (base components)
        wp_enqueue_style(
            $this->plugin_name . '-design-system',
            DEDEBTIFY_PLUGIN_URL . 'assets/css/dedebtify-design-system.css',
            array(),
            $this->version,
            'all'
        );

        // Enqueue admin styles (depends on design system)
        wp_enqueue_style(
            $this->plugin_name . '-admin',
            DEDEBTIFY_PLUGIN_URL . 'assets/css/dedebtify-admin.css',
            array( $this->plugin_name . '-design-system' ),
            $this->version,
            'all'
        );

        wp_enqueue_script(
            $this->plugin_name . '-admin',
            DEDEBTIFY_PLUGIN_URL . 'assets/js/dedebtify-admin.js',
            array( 'jquery' ),
            $this->version,
            true
        );

        // Localize script for AJAX
        wp_localize_script(
            $this->plugin_name . '-admin',
            'dedebtifyAdmin',
            array(
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'nonce' => wp_create_nonce( 'dedebtify_admin_nonce' ),
                'restUrl' => rest_url( 'dedebtify/v1/' ),
                'restNonce' => wp_create_nonce( 'wp_rest' ),
            )
        );
    }

    /**
     * Enqueue public-facing styles and scripts.
     *
     * @since    1.0.0
     */
    public function enqueue_public_assets() {
        // Enqueue design system first (base components)
        wp_enqueue_style(
            $this->plugin_name . '-design-system',
            DEDEBTIFY_PLUGIN_URL . 'assets/css/dedebtify-design-system.css',
            array(),
            $this->version,
            'all'
        );

        // Enqueue public styles (depends on design system)
        wp_enqueue_style(
            $this->plugin_name . '-public',
            DEDEBTIFY_PLUGIN_URL . 'assets/css/dedebtify-public.css',
            array( $this->plugin_name . '-design-system' ),
            $this->version,
            'all'
        );

        // Enqueue enhanced styles (integrates design system with components)
        wp_enqueue_style(
            $this->plugin_name . '-enhanced',
            DEDEBTIFY_PLUGIN_URL . 'assets/css/dedebtify-enhanced.css',
            array( $this->plugin_name . '-design-system', $this->plugin_name . '-public' ),
            $this->version,
            'all'
        );

        // Enqueue mobile app styles (modern Shadcn-inspired UI)
        wp_enqueue_style(
            $this->plugin_name . '-mobile-app',
            DEDEBTIFY_PLUGIN_URL . 'assets/css/dedebtify-mobile-app.css',
            array( $this->plugin_name . '-enhanced' ),
            $this->version,
            'all'
        );

        // Enqueue sidebar navigation styles
        wp_enqueue_style(
            $this->plugin_name . '-sidebar',
            DEDEBTIFY_PLUGIN_URL . 'assets/css/dedebtify-sidebar.css',
            array( $this->plugin_name . '-mobile-app' ),
            $this->version,
            'all'
        );

        // Enqueue AI Coach styles
        wp_enqueue_style(
            $this->plugin_name . '-ai-coach',
            DEDEBTIFY_PLUGIN_URL . 'assets/css/dedebtify-ai-coach.css',
            array( $this->plugin_name . '-sidebar' ),
            $this->version,
            'all'
        );

        // Enqueue PWA styles
        wp_enqueue_style(
            $this->plugin_name . '-pwa',
            DEDEBTIFY_PLUGIN_URL . 'assets/css/dedebtify-pwa.css',
            array( $this->plugin_name . '-mobile-app' ),
            $this->version,
            'all'
        );

        wp_enqueue_script(
            $this->plugin_name . '-public',
            DEDEBTIFY_PLUGIN_URL . 'assets/js/dedebtify-public.js',
            array( 'jquery' ),
            $this->version,
            true
        );

        wp_enqueue_script(
            $this->plugin_name . '-calculator',
            DEDEBTIFY_PLUGIN_URL . 'assets/js/dedebtify-calculator.js',
            array( 'jquery' ),
            $this->version,
            true
        );

        wp_enqueue_script(
            $this->plugin_name . '-managers',
            DEDEBTIFY_PLUGIN_URL . 'assets/js/dedebtify-managers.js',
            array( 'jquery', $this->plugin_name . '-calculator' ),
            $this->version,
            true
        );

        wp_enqueue_script(
            $this->plugin_name . '-action-plan',
            DEDEBTIFY_PLUGIN_URL . 'assets/js/dedebtify-action-plan.js',
            array( 'jquery', $this->plugin_name . '-public' ),
            $this->version,
            true
        );

        wp_enqueue_script(
            $this->plugin_name . '-mortgages',
            DEDEBTIFY_PLUGIN_URL . 'assets/js/dedebtify-mortgages.js',
            array( 'jquery', $this->plugin_name . '-public' ),
            $this->version,
            true
        );

        wp_enqueue_script(
            $this->plugin_name . '-ai-coach',
            DEDEBTIFY_PLUGIN_URL . 'assets/js/dedebtify-ai-coach.js',
            array( 'jquery', $this->plugin_name . '-public' ),
            $this->version,
            true
        );

        wp_enqueue_script(
            $this->plugin_name . '-pwa',
            DEDEBTIFY_PLUGIN_URL . 'assets/js/dedebtify-pwa.js',
            array( 'jquery', $this->plugin_name . '-public' ),
            $this->version,
            true
        );

        // Localize PWA script
        wp_localize_script(
            $this->plugin_name . '-pwa',
            'dedebtifyPWA',
            array(
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'nonce' => wp_create_nonce( 'dedebtify_nonce' ),
                'serviceWorkerUrl' => site_url( '/dedebtify-sw.js' ),
                'pushEnabled' => get_option( 'dedebtify_pwa_push_enabled', false ),
                'vapidPublicKey' => get_option( 'dedebtify_pwa_vapid_public_key', '' ),
            )
        );

        // Localize script for AJAX
        wp_localize_script(
            $this->plugin_name . '-public',
            'dedebtify',
            array(
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'nonce' => wp_create_nonce( 'dedebtify_nonce' ),
                'restUrl' => rest_url( 'dedebtify/v1/' ),
                'restNonce' => wp_create_nonce( 'wp_rest' ),
                'userId' => get_current_user_id(),
            )
        );

        // Localize script for managers with translations
        wp_localize_script(
            $this->plugin_name . '-managers',
            'dedebtifyL10n',
            array(
                'edit' => __( 'Edit', 'dedebtify' ),
                'delete' => __( 'Delete', 'dedebtify' ),
                'confirm_delete' => __( 'Are you sure you want to delete this item?', 'dedebtify' ),
                'saving' => __( 'Saving...', 'dedebtify' ),
                'loading' => __( 'Loading...', 'dedebtify' ),
                'error' => __( 'An error occurred. Please try again.', 'dedebtify' ),
            )
        );
    }

    /**
     * Output custom CSS from settings.
     *
     * @since    1.0.0
     */
    public function output_custom_css() {
        $primary_color = get_option( 'dedebtify_primary_color', '#3b82f6' );
        $success_color = get_option( 'dedebtify_success_color', '#10b981' );
        $warning_color = get_option( 'dedebtify_warning_color', '#f59e0b' );
        $danger_color = get_option( 'dedebtify_danger_color', '#ef4444' );
        $font_family = get_option( 'dedebtify_font_family', 'System Default' );
        $border_radius = get_option( 'dedebtify_border_radius', 8 );

        // Convert hex to HSL for CSS custom properties
        $primary_hsl = $this->hex_to_hsl( $primary_color );
        $success_hsl = $this->hex_to_hsl( $success_color );
        $warning_hsl = $this->hex_to_hsl( $warning_color );
        $danger_hsl = $this->hex_to_hsl( $danger_color );

        $font_css = '';
        if ( $font_family !== 'System Default' ) {
            $font_css = "font-family: {$font_family};";
        }

        echo '<style id="dedebtify-custom-css">';
        echo ':root {';
        echo "--dd-primary: {$primary_hsl};";
        echo "--dd-success: {$success_hsl};";
        echo "--dd-warning: {$warning_hsl};";
        echo "--dd-destructive: {$danger_hsl};";
        echo "--dd-radius: {$border_radius}px;";
        echo '}';
        if ( ! empty( $font_css ) ) {
            echo ".dedebtify-dashboard,";
            echo ".dedebtify-card,";
            echo ".dedebtify-form,";
            echo ".dd-btn,";
            echo ".dedebtify-stat-card {";
            echo $font_css;
            echo '}';
        }
        echo '</style>';
    }

    /**
     * Convert hex color to HSL.
     *
     * @since    1.0.0
     * @param    string    $hex
     * @return   string
     */
    private function hex_to_hsl( $hex ) {
        $hex = str_replace( '#', '', $hex );
        $r = hexdec( substr( $hex, 0, 2 ) ) / 255;
        $g = hexdec( substr( $hex, 2, 2 ) ) / 255;
        $b = hexdec( substr( $hex, 4, 2 ) ) / 255;

        $max = max( $r, $g, $b );
        $min = min( $r, $g, $b );
        $l = ( $max + $min ) / 2;

        if ( $max === $min ) {
            $h = $s = 0;
        } else {
            $d = $max - $min;
            $s = $l > 0.5 ? $d / ( 2 - $max - $min ) : $d / ( $max + $min );

            switch ( $max ) {
                case $r:
                    $h = ( $g - $b ) / $d + ( $g < $b ? 6 : 0 );
                    break;
                case $g:
                    $h = ( $b - $r ) / $d + 2;
                    break;
                case $b:
                    $h = ( $r - $g ) / $d + 4;
                    break;
            }

            $h /= 6;
        }

        $h = round( $h * 360 );
        $s = round( $s * 100 );
        $l = round( $l * 100 );

        return "{$h} {$s}% {$l}%";
    }

    /**
     * Add admin menu pages.
     *
     * @since    1.0.0
     */
    public function add_admin_menu() {
        add_menu_page(
            __( 'DeDebtify', 'dedebtify' ),
            __( 'DeDebtify', 'dedebtify' ),
            'manage_options',
            'dedebtify',
            array( $this, 'render_admin_dashboard' ),
            'dashicons-money-alt',
            30
        );

        add_submenu_page(
            'dedebtify',
            __( 'Dashboard', 'dedebtify' ),
            __( 'Dashboard', 'dedebtify' ),
            'manage_options',
            'dedebtify',
            array( $this, 'render_admin_dashboard' )
        );

        // Show setup page if not hidden
        if ( ! get_option( 'dedebtify_hide_setup_page', false ) ) {
            add_submenu_page(
                'dedebtify',
                __( 'Setup Guide', 'dedebtify' ),
                __( 'Setup Guide', 'dedebtify' ) . ' <span class="dashicons dashicons-star-filled" style="color: #f0b849; font-size: 14px;"></span>',
                'manage_options',
                'dedebtify-setup',
                array( $this, 'render_setup_page' )
            );
        }

        add_submenu_page(
            'dedebtify',
            __( 'Reports', 'dedebtify' ),
            __( 'Reports', 'dedebtify' ),
            'manage_options',
            'dedebtify-reports',
            array( $this, 'render_reports_page' )
        );

        add_submenu_page(
            'dedebtify',
            __( 'Settings', 'dedebtify' ),
            __( 'Settings', 'dedebtify' ),
            'manage_options',
            'dedebtify-settings',
            array( $this, 'render_settings_page' )
        );
    }

    /**
     * Render admin dashboard.
     *
     * @since    1.0.0
     */
    public function render_admin_dashboard() {
        require_once DEDEBTIFY_PLUGIN_DIR . 'admin/dashboard.php';
    }

    /**
     * Render setup guide page.
     *
     * @since    1.0.0
     */
    public function render_setup_page() {
        require_once DEDEBTIFY_PLUGIN_DIR . 'admin/setup-page.php';
    }

    /**
     * Render reports page.
     *
     * @since    1.0.0
     */
    public function render_reports_page() {
        require_once DEDEBTIFY_PLUGIN_DIR . 'admin/reports-page.php';
    }

    /**
     * Render settings page.
     *
     * @since    1.0.0
     */
    public function render_settings_page() {
        require_once DEDEBTIFY_PLUGIN_DIR . 'admin/settings-page.php';
    }

    /**
     * Add settings link to plugins page.
     *
     * @since    1.0.0
     */
    public function add_settings_link( $links ) {
        $settings_link = '<a href="' . admin_url( 'admin.php?page=dedebtify-settings' ) . '">' . __( 'Settings', 'dedebtify' ) . '</a>';
        array_unshift( $links, $settings_link );
        return $links;
    }

    /**
     * Render dashboard shortcode.
     *
     * @since    1.0.0
     */
    public function render_dashboard_shortcode( $atts ) {
        if ( ! is_user_logged_in() ) {
            return '<p>' . __( 'Please log in to view your dashboard.', 'dedebtify' ) . '</p>';
        }

        ob_start();
        require_once DEDEBTIFY_PLUGIN_DIR . 'templates/dashboard.php';
        return ob_get_clean();
    }

    /**
     * Render credit cards manager shortcode.
     *
     * @since    1.0.0
     */
    public function render_credit_cards_shortcode( $atts ) {
        if ( ! is_user_logged_in() ) {
            return '<p>' . __( 'Please log in to manage your credit cards.', 'dedebtify' ) . '</p>';
        }

        ob_start();
        require_once DEDEBTIFY_PLUGIN_DIR . 'templates/credit-cards.php';
        return ob_get_clean();
    }

    /**
     * Render loans manager shortcode.
     *
     * @since    1.0.0
     */
    public function render_loans_shortcode( $atts ) {
        if ( ! is_user_logged_in() ) {
            return '<p>' . __( 'Please log in to manage your loans.', 'dedebtify' ) . '</p>';
        }

        ob_start();
        require_once DEDEBTIFY_PLUGIN_DIR . 'templates/loans.php';
        return ob_get_clean();
    }

    /**
     * Render mortgages manager shortcode.
     *
     * @since    1.0.0
     */
    public function render_mortgages_shortcode( $atts ) {
        if ( ! is_user_logged_in() ) {
            return '<p>' . __( 'Please log in to manage your mortgage.', 'dedebtify' ) . '</p>';
        }

        ob_start();
        require_once DEDEBTIFY_PLUGIN_DIR . 'templates/mortgages.php';
        return ob_get_clean();
    }

    /**
     * Render bills manager shortcode.
     *
     * @since    1.0.0
     */
    public function render_bills_shortcode( $atts ) {
        if ( ! is_user_logged_in() ) {
            return '<p>' . __( 'Please log in to manage your bills.', 'dedebtify' ) . '</p>';
        }

        ob_start();
        require_once DEDEBTIFY_PLUGIN_DIR . 'templates/bills.php';
        return ob_get_clean();
    }

    /**
     * Render goals manager shortcode.
     *
     * @since    1.0.0
     */
    public function render_goals_shortcode( $atts ) {
        if ( ! is_user_logged_in() ) {
            return '<p>' . __( 'Please log in to manage your goals.', 'dedebtify' ) . '</p>';
        }

        ob_start();
        require_once DEDEBTIFY_PLUGIN_DIR . 'templates/goals.php';
        return ob_get_clean();
    }

    /**
     * Render action plan shortcode.
     *
     * @since    1.0.0
     */
    public function render_action_plan_shortcode( $atts ) {
        if ( ! is_user_logged_in() ) {
            return '<p>' . __( 'Please log in to view your debt action plan.', 'dedebtify' ) . '</p>';
        }

        ob_start();
        require_once DEDEBTIFY_PLUGIN_DIR . 'templates/action-plan.php';
        return ob_get_clean();
    }

    /**
     * Render snapshots shortcode.
     *
     * @since    1.0.0
     */
    public function render_snapshots_shortcode( $atts ) {
        if ( ! is_user_logged_in() ) {
            return '<p>' . __( 'Please log in to view your financial snapshots.', 'dedebtify' ) . '</p>';
        }

        ob_start();
        require_once DEDEBTIFY_PLUGIN_DIR . 'templates/snapshots.php';
        return ob_get_clean();
    }

    /**
     * Render AI Coach shortcode
     *
     * @since    1.0.0
     */
    public function render_ai_coach_shortcode( $atts ) {
        if ( ! is_user_logged_in() ) {
            return '<p>' . __( 'Please log in to chat with your AI Financial Coach.', 'dedebtify' ) . '</p>';
        }

        ob_start();
        require_once DEDEBTIFY_PLUGIN_DIR . 'templates/ai-coach.php';
        return ob_get_clean();
    }
}
