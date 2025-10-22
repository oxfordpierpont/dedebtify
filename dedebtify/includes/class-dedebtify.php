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
        // Load CPT registration class
        require_once DEDEBTIFY_PLUGIN_DIR . 'includes/class-dedebtify-cpt.php';

        // Load calculations class
        require_once DEDEBTIFY_PLUGIN_DIR . 'includes/class-dedebtify-calculations.php';

        // Load REST API class
        require_once DEDEBTIFY_PLUGIN_DIR . 'includes/class-dedebtify-api.php';

        // Load Elementor integration class (if Elementor is active)
        if ( did_action( 'elementor/loaded' ) ) {
            require_once DEDEBTIFY_PLUGIN_DIR . 'includes/class-dedebtify-elementor.php';
        }
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

        // Add shortcode support
        add_shortcode( 'dedebtify_dashboard', array( $this, 'render_dashboard_shortcode' ) );
        add_shortcode( 'dedebtify_credit_cards', array( $this, 'render_credit_cards_shortcode' ) );
        add_shortcode( 'dedebtify_loans', array( $this, 'render_loans_shortcode' ) );
        add_shortcode( 'dedebtify_bills', array( $this, 'render_bills_shortcode' ) );
        add_shortcode( 'dedebtify_goals', array( $this, 'render_goals_shortcode' ) );
        add_shortcode( 'dedebtify_action_plan', array( $this, 'render_action_plan_shortcode' ) );
        add_shortcode( 'dedebtify_snapshots', array( $this, 'render_snapshots_shortcode' ) );
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
        wp_enqueue_style(
            $this->plugin_name . '-admin',
            DEDEBTIFY_PLUGIN_URL . 'assets/css/dedebtify-admin.css',
            array(),
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
        wp_enqueue_style(
            $this->plugin_name . '-public',
            DEDEBTIFY_PLUGIN_URL . 'assets/css/dedebtify-public.css',
            array(),
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
        require_once DEDEBTIFY_PLUGIN_DIR . 'admin/settings-page.php';
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
}
