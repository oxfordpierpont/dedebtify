<?php
/**
 * Custom Post Types Registration
 *
 * Registers all DeDebtify custom post types for financial data
 * Custom Post Types registration and management.
 *
 * This class handles the registration of all Custom Post Types and their meta boxes.
 *
 * @since      1.0.0
 * @package    Dedebtify
 * @subpackage Dedebtify/includes
 */

class Dedebtify_CPT {

    /**
     * Register all custom post types.
     *
     * @since    1.0.0
     */

    public function register_post_types() {
        // Only register if JetEngine is not handling CPTs
        $this->register_credit_card_cpt();
        $this->register_loan_cpt();
        $this->register_mortgage_cpt();
        $this->register_bill_cpt();
        $this->register_goal_cpt();
        $this->register_snapshot_cpt();
    }

    /**
     * Register Credit Card CPT.
     *
     * @since    1.0.0
     */
    private function register_credit_card_cpt() {
        $labels = array(
            'name'                  => _x( 'Credit Cards', 'Post Type General Name', 'dedebtify' ),
            'singular_name'         => _x( 'Credit Card', 'Post Type Singular Name', 'dedebtify' ),
            'menu_name'             => __( 'Credit Cards', 'dedebtify' ),
            'name_admin_bar'        => __( 'Credit Card', 'dedebtify' ),
            'archives'              => __( 'Credit Card Archives', 'dedebtify' ),
            'attributes'            => __( 'Credit Card Attributes', 'dedebtify' ),
            'parent_item_colon'     => __( 'Parent Credit Card:', 'dedebtify' ),
            'all_items'             => __( 'All Credit Cards', 'dedebtify' ),
            'add_new_item'          => __( 'Add New Credit Card', 'dedebtify' ),
            'add_new'               => __( 'Add New', 'dedebtify' ),
            'new_item'              => __( 'New Credit Card', 'dedebtify' ),
            'edit_item'             => __( 'Edit Credit Card', 'dedebtify' ),
            'update_item'           => __( 'Update Credit Card', 'dedebtify' ),
            'view_item'             => __( 'View Credit Card', 'dedebtify' ),
            'view_items'            => __( 'View Credit Cards', 'dedebtify' ),
            'search_items'          => __( 'Search Credit Card', 'dedebtify' ),
        );

        $args = array(
            'label'                 => __( 'Credit Card', 'dedebtify' ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'author' ),
            'hierarchical'          => false,
            'public'                => false,
            'show_ui'               => true,
            'show_in_menu'          => 'dedebtify',
            'menu_position'         => 30,
            'show_in_admin_bar'     => false,
            'show_in_nav_menus'     => false,
            'can_export'            => true,
            'has_archive'           => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => false,
            'capability_type'       => 'post',
            'show_in_rest'          => true,
        );

        register_post_type( 'dd_credit_card', $args );
    }


    /**
     * Register Loan CPT.
     *
     * @since    1.0.0
     */
    private function register_loan_cpt() {
        $labels = array(
            'name'                  => _x( 'Loans', 'Post Type General Name', 'dedebtify' ),
            'singular_name'         => _x( 'Loan', 'Post Type Singular Name', 'dedebtify' ),
            'menu_name'             => __( 'Loans', 'dedebtify' ),
            'name_admin_bar'        => __( 'Loan', 'dedebtify' ),
            'add_new_item'          => __( 'Add New Loan', 'dedebtify' ),
            'edit_item'             => __( 'Edit Loan', 'dedebtify' ),
        );

        $args = array(
            'label'                 => __( 'Loan', 'dedebtify' ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'author' ),
            'hierarchical'          => false,
            'public'                => false,
            'show_ui'               => true,
            'show_in_menu'          => 'dedebtify',
            'show_in_admin_bar'     => false,
            'can_export'            => true,
            'exclude_from_search'   => true,
            'publicly_queryable'    => false,
            'capability_type'       => 'post',
            'show_in_rest'          => true,
        );

        register_post_type( 'dd_loan', $args );
    }


    /**
     * Register Mortgage CPT.
     *
     * @since    1.0.0
     */
    private function register_mortgage_cpt() {
        $labels = array(
            'name'                  => _x( 'Mortgages', 'Post Type General Name', 'dedebtify' ),
            'singular_name'         => _x( 'Mortgage', 'Post Type Singular Name', 'dedebtify' ),
            'menu_name'             => __( 'Mortgages', 'dedebtify' ),
            'add_new_item'          => __( 'Add New Mortgage', 'dedebtify' ),
            'edit_item'             => __( 'Edit Mortgage', 'dedebtify' ),
        );

        $args = array(
            'label'                 => __( 'Mortgage', 'dedebtify' ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'author' ),
            'hierarchical'          => false,
            'public'                => false,
            'show_ui'               => true,
            'show_in_menu'          => 'dedebtify',
            'show_in_admin_bar'     => false,
            'can_export'            => true,
            'exclude_from_search'   => true,
            'publicly_queryable'    => false,
            'capability_type'       => 'post',
            'show_in_rest'          => true,
        );

        register_post_type( 'dd_mortgage', $args );
    }


    /**
     * Register Bill CPT.
     *
     * @since    1.0.0
     */
    private function register_bill_cpt() {
        $labels = array(
            'name'                  => _x( 'Bills', 'Post Type General Name', 'dedebtify' ),
            'singular_name'         => _x( 'Bill', 'Post Type Singular Name', 'dedebtify' ),
            'menu_name'             => __( 'Bills', 'dedebtify' ),
            'add_new_item'          => __( 'Add New Bill', 'dedebtify' ),
            'edit_item'             => __( 'Edit Bill', 'dedebtify' ),
        );

        $args = array(
            'label'                 => __( 'Bill', 'dedebtify' ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'author' ),
            'hierarchical'          => false,
            'public'                => false,
            'show_ui'               => true,
            'show_in_menu'          => 'dedebtify',
            'show_in_admin_bar'     => false,
            'can_export'            => true,
            'exclude_from_search'   => true,
            'publicly_queryable'    => false,
            'capability_type'       => 'post',
            'show_in_rest'          => true,
        );

        register_post_type( 'dd_bill', $args );
    }


    /**
     * Register Goal CPT.
     *
     * @since    1.0.0
     */
    private function register_goal_cpt() {
        $labels = array(
            'name'                  => _x( 'Goals', 'Post Type General Name', 'dedebtify' ),
            'singular_name'         => _x( 'Goal', 'Post Type Singular Name', 'dedebtify' ),
            'menu_name'             => __( 'Goals', 'dedebtify' ),
            'add_new_item'          => __( 'Add New Goal', 'dedebtify' ),
            'edit_item'             => __( 'Edit Goal', 'dedebtify' ),
        );

        $args = array(
            'label'                 => __( 'Goal', 'dedebtify' ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'author' ),
            'hierarchical'          => false,
            'public'                => false,
            'show_ui'               => true,
            'show_in_menu'          => 'dedebtify',
            'show_in_admin_bar'     => false,
            'can_export'            => true,
            'exclude_from_search'   => true,
            'publicly_queryable'    => false,
            'capability_type'       => 'post',
            'show_in_rest'          => true,
        );

        register_post_type( 'dd_goal', $args );
    }


    /**
     * Register Snapshot CPT.
     *
     * @since    1.0.0
     */
    private function register_snapshot_cpt() {
        $labels = array(
            'name'                  => _x( 'Snapshots', 'Post Type General Name', 'dedebtify' ),
            'singular_name'         => _x( 'Snapshot', 'Post Type Singular Name', 'dedebtify' ),
            'menu_name'             => __( 'Snapshots', 'dedebtify' ),
            'add_new_item'          => __( 'Add New Snapshot', 'dedebtify' ),
            'edit_item'             => __( 'Edit Snapshot', 'dedebtify' ),
        );

        $args = array(
            'label'                 => __( 'Snapshot', 'dedebtify' ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'author' ),
            'hierarchical'          => false,
            'public'                => false,
            'show_ui'               => true,
            'show_in_menu'          => 'dedebtify',
            'show_in_admin_bar'     => false,
            'can_export'            => true,
            'exclude_from_search'   => true,
            'publicly_queryable'    => false,
            'capability_type'       => 'post',
            'show_in_rest'          => true,
        );

        register_post_type( 'dd_snapshot', $args );
    }

    /**
     * Add meta boxes for custom post types.
     *
     * @since    1.0.0
     */
    public function add_meta_boxes() {
        // Credit Card meta box
        add_meta_box(
            'dd_credit_card_details',
            __( 'Credit Card Details', 'dedebtify' ),
            array( $this, 'render_credit_card_meta_box' ),
            'dd_credit_card',
            'normal',
            'high'
        );

        // Loan meta box
        add_meta_box(
            'dd_loan_details',
            __( 'Loan Details', 'dedebtify' ),
            array( $this, 'render_loan_meta_box' ),
            'dd_loan',
            'normal',
            'high'
        );

        // Mortgage meta box
        add_meta_box(
            'dd_mortgage_details',
            __( 'Mortgage Details', 'dedebtify' ),
            array( $this, 'render_mortgage_meta_box' ),
            'dd_mortgage',
            'normal',
            'high'
        );

        // Bill meta box
        add_meta_box(
            'dd_bill_details',
            __( 'Bill Details', 'dedebtify' ),
            array( $this, 'render_bill_meta_box' ),
            'dd_bill',
            'normal',
            'high'
        );

        // Goal meta box
        add_meta_box(
            'dd_goal_details',
            __( 'Goal Details', 'dedebtify' ),
            array( $this, 'render_goal_meta_box' ),
            'dd_goal',
            'normal',
            'high'
        );

        // Snapshot meta box
        add_meta_box(
            'dd_snapshot_details',
            __( 'Snapshot Details', 'dedebtify' ),
            array( $this, 'render_snapshot_meta_box' ),
            'dd_snapshot',
            'normal',
            'high'
        );
    }

    /**
     * Render Credit Card meta box.
     *
     * @since    1.0.0
     */
    public function render_credit_card_meta_box( $post ) {
        wp_nonce_field( 'dd_credit_card_meta_box', 'dd_credit_card_meta_box_nonce' );

        $balance = get_post_meta( $post->ID, 'balance', true );
        $credit_limit = get_post_meta( $post->ID, 'credit_limit', true );
        $interest_rate = get_post_meta( $post->ID, 'interest_rate', true );
        $minimum_payment = get_post_meta( $post->ID, 'minimum_payment', true );
        $extra_payment = get_post_meta( $post->ID, 'extra_payment', true );
        $due_date = get_post_meta( $post->ID, 'due_date', true );
        $auto_pay = get_post_meta( $post->ID, 'auto_pay', true );
        $status = get_post_meta( $post->ID, 'status', true );
        if ( empty( $status ) ) $status = 'active';

        ?>
        <table class="form-table">
            <tr>
                <th><label for="dd_balance"><?php _e( 'Current Balance ($)', 'dedebtify' ); ?></label></th>
                <td><input type="number" step="0.01" id="dd_balance" name="dd_balance" value="<?php echo esc_attr( $balance ); ?>" class="regular-text" required></td>
            </tr>
            <tr>
                <th><label for="dd_credit_limit"><?php _e( 'Credit Limit ($)', 'dedebtify' ); ?></label></th>
                <td><input type="number" step="0.01" id="dd_credit_limit" name="dd_credit_limit" value="<?php echo esc_attr( $credit_limit ); ?>" class="regular-text" required></td>
            </tr>
            <tr>
                <th><label for="dd_interest_rate"><?php _e( 'Interest Rate (%)', 'dedebtify' ); ?></label></th>
                <td><input type="number" step="0.01" id="dd_interest_rate" name="dd_interest_rate" value="<?php echo esc_attr( $interest_rate ); ?>" class="regular-text" required></td>
            </tr>
            <tr>
                <th><label for="dd_minimum_payment"><?php _e( 'Minimum Payment ($)', 'dedebtify' ); ?></label></th>
                <td><input type="number" step="0.01" id="dd_minimum_payment" name="dd_minimum_payment" value="<?php echo esc_attr( $minimum_payment ); ?>" class="regular-text" required></td>
            </tr>
            <tr>
                <th><label for="dd_extra_payment"><?php _e( 'Extra Payment ($)', 'dedebtify' ); ?></label></th>
                <td><input type="number" step="0.01" id="dd_extra_payment" name="dd_extra_payment" value="<?php echo esc_attr( $extra_payment ); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="dd_due_date"><?php _e( 'Due Date (Day of Month)', 'dedebtify' ); ?></label></th>
                <td><input type="number" min="1" max="31" id="dd_due_date" name="dd_due_date" value="<?php echo esc_attr( $due_date ); ?>" class="small-text"></td>
            </tr>
            <tr>
                <th><label for="dd_auto_pay"><?php _e( 'Auto-Pay Enabled', 'dedebtify' ); ?></label></th>
                <td><input type="checkbox" id="dd_auto_pay" name="dd_auto_pay" value="1" <?php checked( $auto_pay, '1' ); ?>></td>
            </tr>
            <tr>
                <th><label for="dd_status"><?php _e( 'Status', 'dedebtify' ); ?></label></th>
                <td>
                    <select id="dd_status" name="dd_status" class="regular-text">
                        <option value="active" <?php selected( $status, 'active' ); ?>><?php _e( 'Active', 'dedebtify' ); ?></option>
                        <option value="paid_off" <?php selected( $status, 'paid_off' ); ?>><?php _e( 'Paid Off', 'dedebtify' ); ?></option>
                        <option value="closed" <?php selected( $status, 'closed' ); ?>><?php _e( 'Closed', 'dedebtify' ); ?></option>
                    </select>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Render Loan meta box.
     *
     * @since    1.0.0
     */
    public function render_loan_meta_box( $post ) {
        wp_nonce_field( 'dd_loan_meta_box', 'dd_loan_meta_box_nonce' );

        $loan_type = get_post_meta( $post->ID, 'loan_type', true );
        $principal = get_post_meta( $post->ID, 'principal', true );
        $current_balance = get_post_meta( $post->ID, 'current_balance', true );
        $interest_rate = get_post_meta( $post->ID, 'interest_rate', true );
        $term_months = get_post_meta( $post->ID, 'term_months', true );
        $monthly_payment = get_post_meta( $post->ID, 'monthly_payment', true );
        $start_date = get_post_meta( $post->ID, 'start_date', true );
        $extra_payment = get_post_meta( $post->ID, 'extra_payment', true );

        ?>
        <table class="form-table">
            <tr>
                <th><label for="dd_loan_type"><?php _e( 'Loan Type', 'dedebtify' ); ?></label></th>
                <td>
                    <select id="dd_loan_type" name="dd_loan_type" class="regular-text" required>
                        <option value="personal" <?php selected( $loan_type, 'personal' ); ?>><?php _e( 'Personal', 'dedebtify' ); ?></option>
                        <option value="auto" <?php selected( $loan_type, 'auto' ); ?>><?php _e( 'Auto', 'dedebtify' ); ?></option>
                        <option value="student" <?php selected( $loan_type, 'student' ); ?>><?php _e( 'Student', 'dedebtify' ); ?></option>
                        <option value="other" <?php selected( $loan_type, 'other' ); ?>><?php _e( 'Other', 'dedebtify' ); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="dd_principal"><?php _e( 'Original Amount ($)', 'dedebtify' ); ?></label></th>
                <td><input type="number" step="0.01" id="dd_principal" name="dd_principal" value="<?php echo esc_attr( $principal ); ?>" class="regular-text" required></td>
            </tr>
            <tr>
                <th><label for="dd_current_balance"><?php _e( 'Current Balance ($)', 'dedebtify' ); ?></label></th>
                <td><input type="number" step="0.01" id="dd_current_balance" name="dd_current_balance" value="<?php echo esc_attr( $current_balance ); ?>" class="regular-text" required></td>
            </tr>
            <tr>
                <th><label for="dd_interest_rate"><?php _e( 'Interest Rate (%)', 'dedebtify' ); ?></label></th>
                <td><input type="number" step="0.01" id="dd_interest_rate" name="dd_interest_rate" value="<?php echo esc_attr( $interest_rate ); ?>" class="regular-text" required></td>
            </tr>
            <tr>
                <th><label for="dd_term_months"><?php _e( 'Term (Months)', 'dedebtify' ); ?></label></th>
                <td><input type="number" id="dd_term_months" name="dd_term_months" value="<?php echo esc_attr( $term_months ); ?>" class="regular-text" required></td>
            </tr>
            <tr>
                <th><label for="dd_monthly_payment"><?php _e( 'Monthly Payment ($)', 'dedebtify' ); ?></label></th>
                <td><input type="number" step="0.01" id="dd_monthly_payment" name="dd_monthly_payment" value="<?php echo esc_attr( $monthly_payment ); ?>" class="regular-text" required></td>
            </tr>
            <tr>
                <th><label for="dd_start_date"><?php _e( 'Start Date', 'dedebtify' ); ?></label></th>
                <td><input type="date" id="dd_start_date" name="dd_start_date" value="<?php echo esc_attr( $start_date ); ?>" class="regular-text" required></td>
            </tr>
            <tr>
                <th><label for="dd_extra_payment"><?php _e( 'Extra Payment ($)', 'dedebtify' ); ?></label></th>
                <td><input type="number" step="0.01" id="dd_extra_payment" name="dd_extra_payment" value="<?php echo esc_attr( $extra_payment ); ?>" class="regular-text"></td>
            </tr>
        </table>
        <?php
    }

    /**
     * Render Mortgage meta box.
     *
     * @since    1.0.0
     */
    public function render_mortgage_meta_box( $post ) {
        wp_nonce_field( 'dd_mortgage_meta_box', 'dd_mortgage_meta_box_nonce' );

        $property_address = get_post_meta( $post->ID, 'property_address', true );
        $loan_amount = get_post_meta( $post->ID, 'loan_amount', true );
        $current_balance = get_post_meta( $post->ID, 'current_balance', true );
        $interest_rate = get_post_meta( $post->ID, 'interest_rate', true );
        $term_years = get_post_meta( $post->ID, 'term_years', true );
        $monthly_payment = get_post_meta( $post->ID, 'monthly_payment', true );
        $start_date = get_post_meta( $post->ID, 'start_date', true );
        $extra_payment = get_post_meta( $post->ID, 'extra_payment', true );
        $property_tax = get_post_meta( $post->ID, 'property_tax', true );
        $homeowners_insurance = get_post_meta( $post->ID, 'homeowners_insurance', true );
        $pmi = get_post_meta( $post->ID, 'pmi', true );

        ?>
        <table class="form-table">
            <tr>
                <th><label for="dd_property_address"><?php _e( 'Property Address', 'dedebtify' ); ?></label></th>
                <td><input type="text" id="dd_property_address" name="dd_property_address" value="<?php echo esc_attr( $property_address ); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="dd_loan_amount"><?php _e( 'Original Loan Amount ($)', 'dedebtify' ); ?></label></th>
                <td><input type="number" step="0.01" id="dd_loan_amount" name="dd_loan_amount" value="<?php echo esc_attr( $loan_amount ); ?>" class="regular-text" required></td>
            </tr>
            <tr>
                <th><label for="dd_current_balance"><?php _e( 'Current Balance ($)', 'dedebtify' ); ?></label></th>
                <td><input type="number" step="0.01" id="dd_current_balance" name="dd_current_balance" value="<?php echo esc_attr( $current_balance ); ?>" class="regular-text" required></td>
            </tr>
            <tr>
                <th><label for="dd_interest_rate"><?php _e( 'Interest Rate (%)', 'dedebtify' ); ?></label></th>
                <td><input type="number" step="0.01" id="dd_interest_rate" name="dd_interest_rate" value="<?php echo esc_attr( $interest_rate ); ?>" class="regular-text" required></td>
            </tr>
            <tr>
                <th><label for="dd_term_years"><?php _e( 'Term (Years)', 'dedebtify' ); ?></label></th>
                <td><input type="number" id="dd_term_years" name="dd_term_years" value="<?php echo esc_attr( $term_years ); ?>" class="regular-text" required></td>
            </tr>
            <tr>
                <th><label for="dd_monthly_payment"><?php _e( 'Monthly P&I Payment ($)', 'dedebtify' ); ?></label></th>
                <td><input type="number" step="0.01" id="dd_monthly_payment" name="dd_monthly_payment" value="<?php echo esc_attr( $monthly_payment ); ?>" class="regular-text" required></td>
            </tr>
            <tr>
                <th><label for="dd_start_date"><?php _e( 'Start Date', 'dedebtify' ); ?></label></th>
                <td><input type="date" id="dd_start_date" name="dd_start_date" value="<?php echo esc_attr( $start_date ); ?>" class="regular-text" required></td>
            </tr>
            <tr>
                <th><label for="dd_extra_payment"><?php _e( 'Extra Principal Payment ($)', 'dedebtify' ); ?></label></th>
                <td><input type="number" step="0.01" id="dd_extra_payment" name="dd_extra_payment" value="<?php echo esc_attr( $extra_payment ); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="dd_property_tax"><?php _e( 'Annual Property Tax ($)', 'dedebtify' ); ?></label></th>
                <td><input type="number" step="0.01" id="dd_property_tax" name="dd_property_tax" value="<?php echo esc_attr( $property_tax ); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="dd_homeowners_insurance"><?php _e( 'Annual Homeowners Insurance ($)', 'dedebtify' ); ?></label></th>
                <td><input type="number" step="0.01" id="dd_homeowners_insurance" name="dd_homeowners_insurance" value="<?php echo esc_attr( $homeowners_insurance ); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="dd_pmi"><?php _e( 'Monthly PMI ($)', 'dedebtify' ); ?></label></th>
                <td><input type="number" step="0.01" id="dd_pmi" name="dd_pmi" value="<?php echo esc_attr( $pmi ); ?>" class="regular-text"></td>
            </tr>
        </table>
        <?php
    }

    /**
     * Render Bill meta box.
     *
     * @since    1.0.0
     */
    public function render_bill_meta_box( $post ) {
        wp_nonce_field( 'dd_bill_meta_box', 'dd_bill_meta_box_nonce' );

        $category = get_post_meta( $post->ID, 'category', true );
        $amount = get_post_meta( $post->ID, 'amount', true );
        $frequency = get_post_meta( $post->ID, 'frequency', true );
        if ( empty( $frequency ) ) $frequency = 'monthly';
        $due_date = get_post_meta( $post->ID, 'due_date', true );
        $auto_pay = get_post_meta( $post->ID, 'auto_pay', true );
        $is_essential = get_post_meta( $post->ID, 'is_essential', true );

        ?>
        <table class="form-table">
            <tr>
                <th><label for="dd_category"><?php _e( 'Category', 'dedebtify' ); ?></label></th>
                <td>
                    <select id="dd_category" name="dd_category" class="regular-text" required>
                        <option value="housing" <?php selected( $category, 'housing' ); ?>><?php _e( 'Housing', 'dedebtify' ); ?></option>
                        <option value="transportation" <?php selected( $category, 'transportation' ); ?>><?php _e( 'Transportation', 'dedebtify' ); ?></option>
                        <option value="utilities" <?php selected( $category, 'utilities' ); ?>><?php _e( 'Utilities', 'dedebtify' ); ?></option>
                        <option value="food" <?php selected( $category, 'food' ); ?>><?php _e( 'Food', 'dedebtify' ); ?></option>
                        <option value="healthcare" <?php selected( $category, 'healthcare' ); ?>><?php _e( 'Healthcare', 'dedebtify' ); ?></option>
                        <option value="insurance" <?php selected( $category, 'insurance' ); ?>><?php _e( 'Insurance', 'dedebtify' ); ?></option>
                        <option value="entertainment" <?php selected( $category, 'entertainment' ); ?>><?php _e( 'Entertainment', 'dedebtify' ); ?></option>
                        <option value="subscriptions" <?php selected( $category, 'subscriptions' ); ?>><?php _e( 'Subscriptions', 'dedebtify' ); ?></option>
                        <option value="other" <?php selected( $category, 'other' ); ?>><?php _e( 'Other', 'dedebtify' ); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="dd_amount"><?php _e( 'Amount ($)', 'dedebtify' ); ?></label></th>
                <td><input type="number" step="0.01" id="dd_amount" name="dd_amount" value="<?php echo esc_attr( $amount ); ?>" class="regular-text" required></td>
            </tr>
            <tr>
                <th><label for="dd_frequency"><?php _e( 'Frequency', 'dedebtify' ); ?></label></th>
                <td>
                    <select id="dd_frequency" name="dd_frequency" class="regular-text" required>
                        <option value="weekly" <?php selected( $frequency, 'weekly' ); ?>><?php _e( 'Weekly', 'dedebtify' ); ?></option>
                        <option value="bi-weekly" <?php selected( $frequency, 'bi-weekly' ); ?>><?php _e( 'Bi-weekly', 'dedebtify' ); ?></option>
                        <option value="monthly" <?php selected( $frequency, 'monthly' ); ?>><?php _e( 'Monthly', 'dedebtify' ); ?></option>
                        <option value="quarterly" <?php selected( $frequency, 'quarterly' ); ?>><?php _e( 'Quarterly', 'dedebtify' ); ?></option>
                        <option value="annually" <?php selected( $frequency, 'annually' ); ?>><?php _e( 'Annually', 'dedebtify' ); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="dd_due_date"><?php _e( 'Due Date (Day of Month)', 'dedebtify' ); ?></label></th>
                <td><input type="number" min="1" max="31" id="dd_due_date" name="dd_due_date" value="<?php echo esc_attr( $due_date ); ?>" class="small-text"></td>
            </tr>
            <tr>
                <th><label for="dd_auto_pay"><?php _e( 'Auto-Pay Enabled', 'dedebtify' ); ?></label></th>
                <td><input type="checkbox" id="dd_auto_pay" name="dd_auto_pay" value="1" <?php checked( $auto_pay, '1' ); ?>></td>
            </tr>
            <tr>
                <th><label for="dd_is_essential"><?php _e( 'Essential Bill', 'dedebtify' ); ?></label></th>
                <td><input type="checkbox" id="dd_is_essential" name="dd_is_essential" value="1" <?php checked( $is_essential, '1' ); ?>></td>
            </tr>
        </table>
        <?php
    }

    /**
     * Render Goal meta box.
     *
     * @since    1.0.0
     */
    public function render_goal_meta_box( $post ) {
        wp_nonce_field( 'dd_goal_meta_box', 'dd_goal_meta_box_nonce' );

        $goal_type = get_post_meta( $post->ID, 'goal_type', true );
        $target_amount = get_post_meta( $post->ID, 'target_amount', true );
        $current_amount = get_post_meta( $post->ID, 'current_amount', true );
        $monthly_contribution = get_post_meta( $post->ID, 'monthly_contribution', true );
        $target_date = get_post_meta( $post->ID, 'target_date', true );
        $priority = get_post_meta( $post->ID, 'priority', true );
        if ( empty( $priority ) ) $priority = 'medium';

        ?>
        <table class="form-table">
            <tr>
                <th><label for="dd_goal_type"><?php _e( 'Goal Type', 'dedebtify' ); ?></label></th>
                <td>
                    <select id="dd_goal_type" name="dd_goal_type" class="regular-text" required>
                        <option value="savings" <?php selected( $goal_type, 'savings' ); ?>><?php _e( 'Savings', 'dedebtify' ); ?></option>
                        <option value="emergency_fund" <?php selected( $goal_type, 'emergency_fund' ); ?>><?php _e( 'Emergency Fund', 'dedebtify' ); ?></option>
                        <option value="debt_payoff" <?php selected( $goal_type, 'debt_payoff' ); ?>><?php _e( 'Debt Payoff', 'dedebtify' ); ?></option>
                        <option value="investment" <?php selected( $goal_type, 'investment' ); ?>><?php _e( 'Investment', 'dedebtify' ); ?></option>
                        <option value="purchase" <?php selected( $goal_type, 'purchase' ); ?>><?php _e( 'Purchase', 'dedebtify' ); ?></option>
                        <option value="other" <?php selected( $goal_type, 'other' ); ?>><?php _e( 'Other', 'dedebtify' ); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="dd_target_amount"><?php _e( 'Target Amount ($)', 'dedebtify' ); ?></label></th>
                <td><input type="number" step="0.01" id="dd_target_amount" name="dd_target_amount" value="<?php echo esc_attr( $target_amount ); ?>" class="regular-text" required></td>
            </tr>
            <tr>
                <th><label for="dd_current_amount"><?php _e( 'Current Amount ($)', 'dedebtify' ); ?></label></th>
                <td><input type="number" step="0.01" id="dd_current_amount" name="dd_current_amount" value="<?php echo esc_attr( $current_amount ); ?>" class="regular-text" required></td>
            </tr>
            <tr>
                <th><label for="dd_monthly_contribution"><?php _e( 'Monthly Contribution ($)', 'dedebtify' ); ?></label></th>
                <td><input type="number" step="0.01" id="dd_monthly_contribution" name="dd_monthly_contribution" value="<?php echo esc_attr( $monthly_contribution ); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="dd_target_date"><?php _e( 'Target Date', 'dedebtify' ); ?></label></th>
                <td><input type="date" id="dd_target_date" name="dd_target_date" value="<?php echo esc_attr( $target_date ); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="dd_priority"><?php _e( 'Priority', 'dedebtify' ); ?></label></th>
                <td>
                    <select id="dd_priority" name="dd_priority" class="regular-text">
                        <option value="low" <?php selected( $priority, 'low' ); ?>><?php _e( 'Low', 'dedebtify' ); ?></option>
                        <option value="medium" <?php selected( $priority, 'medium' ); ?>><?php _e( 'Medium', 'dedebtify' ); ?></option>
                        <option value="high" <?php selected( $priority, 'high' ); ?>><?php _e( 'High', 'dedebtify' ); ?></option>
                    </select>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Render Snapshot meta box.
     *
     * @since    1.0.0
     */
    public function render_snapshot_meta_box( $post ) {
        wp_nonce_field( 'dd_snapshot_meta_box', 'dd_snapshot_meta_box_nonce' );

        $snapshot_date = get_post_meta( $post->ID, 'snapshot_date', true );
        $total_debt = get_post_meta( $post->ID, 'total_debt', true );
        $total_credit_card_debt = get_post_meta( $post->ID, 'total_credit_card_debt', true );
        $total_loan_debt = get_post_meta( $post->ID, 'total_loan_debt', true );
        $total_mortgage_debt = get_post_meta( $post->ID, 'total_mortgage_debt', true );
        $total_monthly_payments = get_post_meta( $post->ID, 'total_monthly_payments', true );
        $total_monthly_bills = get_post_meta( $post->ID, 'total_monthly_bills', true );
        $monthly_income = get_post_meta( $post->ID, 'monthly_income', true );
        $debt_to_income_ratio = get_post_meta( $post->ID, 'debt_to_income_ratio', true );
        $credit_utilization = get_post_meta( $post->ID, 'credit_utilization', true );

        ?>
        <table class="form-table">
            <tr>
                <th><label for="dd_snapshot_date"><?php _e( 'Snapshot Date', 'dedebtify' ); ?></label></th>
                <td><input type="date" id="dd_snapshot_date" name="dd_snapshot_date" value="<?php echo esc_attr( $snapshot_date ); ?>" class="regular-text" required></td>
            </tr>
            <tr>
                <th><label for="dd_total_debt"><?php _e( 'Total Debt ($)', 'dedebtify' ); ?></label></th>
                <td><input type="number" step="0.01" id="dd_total_debt" name="dd_total_debt" value="<?php echo esc_attr( $total_debt ); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="dd_total_credit_card_debt"><?php _e( 'Total Credit Card Debt ($)', 'dedebtify' ); ?></label></th>
                <td><input type="number" step="0.01" id="dd_total_credit_card_debt" name="dd_total_credit_card_debt" value="<?php echo esc_attr( $total_credit_card_debt ); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="dd_total_loan_debt"><?php _e( 'Total Loan Debt ($)', 'dedebtify' ); ?></label></th>
                <td><input type="number" step="0.01" id="dd_total_loan_debt" name="dd_total_loan_debt" value="<?php echo esc_attr( $total_loan_debt ); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="dd_total_mortgage_debt"><?php _e( 'Total Mortgage Debt ($)', 'dedebtify' ); ?></label></th>
                <td><input type="number" step="0.01" id="dd_total_mortgage_debt" name="dd_total_mortgage_debt" value="<?php echo esc_attr( $total_mortgage_debt ); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="dd_total_monthly_payments"><?php _e( 'Total Monthly Payments ($)', 'dedebtify' ); ?></label></th>
                <td><input type="number" step="0.01" id="dd_total_monthly_payments" name="dd_total_monthly_payments" value="<?php echo esc_attr( $total_monthly_payments ); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="dd_total_monthly_bills"><?php _e( 'Total Monthly Bills ($)', 'dedebtify' ); ?></label></th>
                <td><input type="number" step="0.01" id="dd_total_monthly_bills" name="dd_total_monthly_bills" value="<?php echo esc_attr( $total_monthly_bills ); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="dd_monthly_income"><?php _e( 'Monthly Income ($)', 'dedebtify' ); ?></label></th>
                <td><input type="number" step="0.01" id="dd_monthly_income" name="dd_monthly_income" value="<?php echo esc_attr( $monthly_income ); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="dd_debt_to_income_ratio"><?php _e( 'Debt-to-Income Ratio (%)', 'dedebtify' ); ?></label></th>
                <td><input type="number" step="0.01" id="dd_debt_to_income_ratio" name="dd_debt_to_income_ratio" value="<?php echo esc_attr( $debt_to_income_ratio ); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="dd_credit_utilization"><?php _e( 'Credit Utilization (%)', 'dedebtify' ); ?></label></th>
                <td><input type="number" step="0.01" id="dd_credit_utilization" name="dd_credit_utilization" value="<?php echo esc_attr( $credit_utilization ); ?>" class="regular-text"></td>
            </tr>
        </table>
        <p class="description"><?php _e( 'Note: Snapshots are typically auto-generated. Manual creation is for testing or adjustments.', 'dedebtify' ); ?></p>
        <?php
    }

    /**
     * Save meta data when post is saved.
     *
     * @since    1.0.0
     */
    public function save_meta_data( $post_id, $post ) {
        // Check if nonce is set for each post type
        $nonces = array(
            'dd_credit_card' => 'dd_credit_card_meta_box_nonce',
            'dd_loan' => 'dd_loan_meta_box_nonce',
            'dd_mortgage' => 'dd_mortgage_meta_box_nonce',
            'dd_bill' => 'dd_bill_meta_box_nonce',
            'dd_goal' => 'dd_goal_meta_box_nonce',
            'dd_snapshot' => 'dd_snapshot_meta_box_nonce',
        );

        if ( ! isset( $nonces[ $post->post_type ] ) ) {
            return;
        }

        $nonce_name = $nonces[ $post->post_type ];

        // Verify nonce
        if ( ! isset( $_POST[ $nonce_name ] ) || ! wp_verify_nonce( $_POST[ $nonce_name ], str_replace( '_nonce', '', $nonce_name ) ) ) {
            return;
        }

        // Check autosave
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // Check permissions
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Save meta based on post type
        switch ( $post->post_type ) {
            case 'dd_credit_card':
                $this->save_credit_card_meta( $post_id );
                break;
            case 'dd_loan':
                $this->save_loan_meta( $post_id );
                break;
            case 'dd_mortgage':
                $this->save_mortgage_meta( $post_id );
                break;
            case 'dd_bill':
                $this->save_bill_meta( $post_id );
                break;
            case 'dd_goal':
                $this->save_goal_meta( $post_id );
                break;
            case 'dd_snapshot':
                $this->save_snapshot_meta( $post_id );
                break;
        }
    }

    /**
     * Save Credit Card meta.
     *
     * @since    1.0.0
     */
    private function save_credit_card_meta( $post_id ) {
        $fields = array( 'balance', 'credit_limit', 'interest_rate', 'minimum_payment', 'extra_payment', 'due_date', 'status' );

        foreach ( $fields as $field ) {
            if ( isset( $_POST[ 'dd_' . $field ] ) ) {
                update_post_meta( $post_id, $field, sanitize_text_field( $_POST[ 'dd_' . $field ] ) );
            }
        }

        // Handle checkbox
        update_post_meta( $post_id, 'auto_pay', isset( $_POST['dd_auto_pay'] ) ? '1' : '0' );
    }

    /**
     * Save Loan meta.
     *
     * @since    1.0.0
     */
    private function save_loan_meta( $post_id ) {
        $fields = array( 'loan_type', 'principal', 'current_balance', 'interest_rate', 'term_months', 'monthly_payment', 'start_date', 'extra_payment' );

        foreach ( $fields as $field ) {
            if ( isset( $_POST[ 'dd_' . $field ] ) ) {
                update_post_meta( $post_id, $field, sanitize_text_field( $_POST[ 'dd_' . $field ] ) );
            }
        }
    }

    /**
     * Save Mortgage meta.
     *
     * @since    1.0.0
     */
    private function save_mortgage_meta( $post_id ) {
        $fields = array( 'property_address', 'loan_amount', 'current_balance', 'interest_rate', 'term_years', 'monthly_payment', 'start_date', 'extra_payment', 'property_tax', 'homeowners_insurance', 'pmi' );

        foreach ( $fields as $field ) {
            if ( isset( $_POST[ 'dd_' . $field ] ) ) {
                update_post_meta( $post_id, $field, sanitize_text_field( $_POST[ 'dd_' . $field ] ) );
            }
        }
    }

    /**
     * Save Bill meta.
     *
     * @since    1.0.0
     */
    private function save_bill_meta( $post_id ) {
        $fields = array( 'category', 'amount', 'frequency', 'due_date' );

        foreach ( $fields as $field ) {
            if ( isset( $_POST[ 'dd_' . $field ] ) ) {
                update_post_meta( $post_id, $field, sanitize_text_field( $_POST[ 'dd_' . $field ] ) );
            }
        }

        // Handle checkboxes
        update_post_meta( $post_id, 'auto_pay', isset( $_POST['dd_auto_pay'] ) ? '1' : '0' );
        update_post_meta( $post_id, 'is_essential', isset( $_POST['dd_is_essential'] ) ? '1' : '0' );
    }

    /**
     * Save Goal meta.
     *
     * @since    1.0.0
     */
    private function save_goal_meta( $post_id ) {
        $fields = array( 'goal_type', 'target_amount', 'current_amount', 'monthly_contribution', 'target_date', 'priority' );

        foreach ( $fields as $field ) {
            if ( isset( $_POST[ 'dd_' . $field ] ) ) {
                update_post_meta( $post_id, $field, sanitize_text_field( $_POST[ 'dd_' . $field ] ) );
            }
        }
    }

    /**
     * Save Snapshot meta.
     *
     * @since    1.0.0
     */
    private function save_snapshot_meta( $post_id ) {
        $fields = array( 'snapshot_date', 'total_debt', 'total_credit_card_debt', 'total_loan_debt', 'total_mortgage_debt', 'total_monthly_payments', 'total_monthly_bills', 'monthly_income', 'debt_to_income_ratio', 'credit_utilization' );

        foreach ( $fields as $field ) {
            if ( isset( $_POST[ 'dd_' . $field ] ) ) {
                update_post_meta( $post_id, $field, sanitize_text_field( $_POST[ 'dd_' . $field ] ) );
            }
        }
    }

    /**
     * Set custom columns for CPT list view.
     *
     * @since    1.0.0
     */
    public function set_custom_columns( $columns ) {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'title' => __( 'Card Name', 'dedebtify' ),
            'balance' => __( 'Balance', 'dedebtify' ),
            'credit_limit' => __( 'Limit', 'dedebtify' ),
            'utilization' => __( 'Utilization', 'dedebtify' ),
            'status' => __( 'Status', 'dedebtify' ),
            'date' => __( 'Date', 'dedebtify' )
        );
        return $columns;
    }

    /**
     * Display custom column content.
     *
     * @since    1.0.0
     */
    public function custom_column_content( $column, $post_id ) {
        switch ( $column ) {
            case 'balance':
                $balance = get_post_meta( $post_id, 'balance', true );
                echo '$' . number_format( $balance, 2 );
                break;

            case 'credit_limit':
                $limit = get_post_meta( $post_id, 'credit_limit', true );
                echo '$' . number_format( $limit, 2 );
                break;

            case 'utilization':
                $balance = get_post_meta( $post_id, 'balance', true );
                $limit = get_post_meta( $post_id, 'credit_limit', true );
                if ( $limit > 0 ) {
                    $util = ( $balance / $limit ) * 100;
                    echo number_format( $util, 1 ) . '%';
                }
                break;

            case 'status':
                $status = get_post_meta( $post_id, 'status', true );
                echo ucfirst( str_replace( '_', ' ', $status ) );
                break;
        }
    }
}
