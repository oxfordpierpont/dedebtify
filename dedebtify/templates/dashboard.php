<?php
/**
 * Public Dashboard Template
 *
 * This template displays the user's financial dashboard.
 *
 * @since      1.0.0
 * @package    Dedebtify
 * @subpackage Dedebtify/templates
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Check if user is logged in
if ( ! is_user_logged_in() ) {
    echo '<p>' . __( 'Please log in to view your dashboard.', 'dedebtify' ) . '</p>';
    return;
}

$user_id = get_current_user_id();
$user_info = get_userdata( $user_id );
?>

<div class="dedebtify-dashboard">

    <div class="dedebtify-dashboard-header">
        <h1><?php printf( __( 'Welcome back, %s!', 'dedebtify' ), esc_html( $user_info->display_name ) ); ?></h1>
        <p><?php _e( 'Here\'s your current financial overview', 'dedebtify' ); ?></p>
    </div>

    <!-- Dashboard Stats -->
    <div id="dedebtify-dashboard-stats">
        <div class="dedebtify-stats-grid">
            <div class="dedebtify-stat-card">
                <div class="dedebtify-stat-label"><?php _e( 'Total Debt', 'dedebtify' ); ?></div>
                <div class="dedebtify-stat-value" id="dd-total-debt">$0.00</div>
                <div class="dedebtify-stat-subtext"><?php _e( 'All debts combined', 'dedebtify' ); ?></div>
            </div>

            <div class="dedebtify-stat-card">
                <div class="dedebtify-stat-label"><?php _e( 'Monthly Payments', 'dedebtify' ); ?></div>
                <div class="dedebtify-stat-value" id="dd-monthly-payments">$0.00</div>
                <div class="dedebtify-stat-subtext"><?php _e( 'Debt payments per month', 'dedebtify' ); ?></div>
            </div>

            <div class="dedebtify-stat-card">
                <div class="dedebtify-stat-label"><?php _e( 'Monthly Bills', 'dedebtify' ); ?></div>
                <div class="dedebtify-stat-value" id="dd-monthly-bills">$0.00</div>
                <div class="dedebtify-stat-subtext"><?php _e( 'Recurring expenses', 'dedebtify' ); ?></div>
            </div>

            <div class="dedebtify-stat-card">
                <div class="dedebtify-stat-label"><?php _e( 'DTI Ratio', 'dedebtify' ); ?></div>
                <div class="dedebtify-stat-value" id="dd-dti-ratio">0%</div>
                <div class="dedebtify-stat-subtext"><?php _e( 'Debt-to-income ratio', 'dedebtify' ); ?></div>
                <div class="dedebtify-progress" id="dd-dti-progress">
                    <div class="dedebtify-progress-bar" style="width: 0%"></div>
                </div>
            </div>

            <div class="dedebtify-stat-card">
                <div class="dedebtify-stat-label"><?php _e( 'Credit Utilization', 'dedebtify' ); ?></div>
                <div class="dedebtify-stat-value" id="dd-credit-utilization">0%</div>
                <div class="dedebtify-stat-subtext"><?php _e( 'Credit usage percentage', 'dedebtify' ); ?></div>
                <div class="dedebtify-progress" id="dd-credit-util-progress">
                    <div class="dedebtify-progress-bar" style="width: 0%"></div>
                </div>
            </div>

            <div class="dedebtify-stat-card">
                <div class="dedebtify-stat-label"><?php _e( 'Debt-Free Date', 'dedebtify' ); ?></div>
                <div class="dedebtify-stat-value" id="dd-debt-free-date" style="font-size: 1.5rem;">—</div>
                <div class="dedebtify-stat-subtext"><?php _e( 'Projected completion', 'dedebtify' ); ?></div>
            </div>
        </div>
    </div>

    <!-- Debt Breakdown Visualization -->
    <div class="dedebtify-section" id="debt-breakdown-section" style="display: none;">
        <h2><?php _e( 'Debt Breakdown', 'dedebtify' ); ?></h2>
        <div class="dd-card">
            <div class="dd-card-content">
                <div class="dedebtify-debt-breakdown">
                    <div class="debt-breakdown-chart">
                        <div class="debt-breakdown-bars" id="debt-breakdown-bars"></div>
                    </div>
                    <div class="debt-breakdown-legend" id="debt-breakdown-legend"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="dedebtify-btn-group">
        <button class="dedebtify-btn dedebtify-create-snapshot">
            <?php _e( 'Create Snapshot', 'dedebtify' ); ?>
        </button>
        <a href="<?php echo get_permalink(); ?>?view=credit-cards" class="dedebtify-btn dedebtify-btn-secondary">
            <?php _e( 'View Credit Cards', 'dedebtify' ); ?>
        </a>
        <a href="<?php echo get_permalink(); ?>?view=loans" class="dedebtify-btn dedebtify-btn-secondary">
            <?php _e( 'View Loans', 'dedebtify' ); ?>
        </a>
        <a href="<?php echo get_permalink(); ?>?view=bills" class="dedebtify-btn dedebtify-btn-secondary">
            <?php _e( 'View Bills', 'dedebtify' ); ?>
        </a>
        <a href="<?php echo get_permalink(); ?>?view=goals" class="dedebtify-btn dedebtify-btn-secondary">
            <?php _e( 'View Goals', 'dedebtify' ); ?>
        </a>
    </div>

    <!-- Credit Cards Section -->
    <div class="dedebtify-section">
        <h2><?php _e( 'Credit Cards', 'dedebtify' ); ?></h2>
        <div id="dedebtify-credit-cards">
            <div class="dedebtify-loading">
                <div class="dedebtify-spinner"></div>
            </div>
        </div>
    </div>

    <!-- Loans Section -->
    <div class="dedebtify-section">
        <h2><?php _e( 'Loans', 'dedebtify' ); ?></h2>
        <div id="dedebtify-loans">
            <div class="dedebtify-loading">
                <div class="dedebtify-spinner"></div>
            </div>
        </div>
    </div>

    <!-- Bills Section -->
    <div class="dedebtify-section">
        <h2><?php _e( 'Bills', 'dedebtify' ); ?></h2>
        <div id="dedebtify-bills">
            <div class="dedebtify-loading">
                <div class="dedebtify-spinner"></div>
            </div>
        </div>
    </div>

    <!-- Goals Section -->
    <div class="dedebtify-section">
        <h2><?php _e( 'Goals', 'dedebtify' ); ?></h2>
        <div id="dedebtify-goals">
            <div class="dedebtify-loading">
                <div class="dedebtify-spinner"></div>
            </div>
        </div>
    </div>

    <!-- Print Button -->
    <div class="dedebtify-btn-group print-hide">
        <button class="dedebtify-btn dedebtify-btn-secondary" onclick="window.print()">
            <?php _e( 'Print Dashboard', 'dedebtify' ); ?>
        </button>
    </div>

</div>
