<?php
/**
 * Admin Dashboard Page
 *
 * @since      1.0.0
 * @package    Dedebtify
 * @subpackage Dedebtify/admin
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
?>

<div class="wrap dedebtify-admin-dashboard">
    <h1><?php _e( 'DeDebtify Dashboard', 'dedebtify' ); ?></h1>

    <div class="dedebtify-admin-header">
        <p class="description"><?php _e( 'Overview of your debt management system', 'dedebtify' ); ?></p>
    </div>

    <!-- Dashboard Widgets Grid -->
    <div class="dedebtify-dashboard-widgets">

        <!-- Total Users Widget -->
        <div class="dedebtify-widget">
            <div class="dedebtify-widget-header">
                <h2><?php _e( 'Total Users', 'dedebtify' ); ?></h2>
                <span class="dashicons dashicons-groups"></span>
            </div>
            <div class="dedebtify-widget-content">
                <div class="dedebtify-widget-stat">
                    <span class="stat-value" id="total-users">0</span>
                    <span class="stat-label"><?php _e( 'Active Users', 'dedebtify' ); ?></span>
                </div>
            </div>
        </div>

        <!-- Total Debt Tracked Widget -->
        <div class="dedebtify-widget">
            <div class="dedebtify-widget-header">
                <h2><?php _e( 'Total Debt Tracked', 'dedebtify' ); ?></h2>
                <span class="dashicons dashicons-chart-line"></span>
            </div>
            <div class="dedebtify-widget-content">
                <div class="dedebtify-widget-stat">
                    <span class="stat-value" id="total-debt">$0</span>
                    <span class="stat-label"><?php _e( 'Across All Users', 'dedebtify' ); ?></span>
                </div>
            </div>
        </div>

        <!-- Credit Cards Widget -->
        <div class="dedebtify-widget">
            <div class="dedebtify-widget-header">
                <h2><?php _e( 'Credit Cards', 'dedebtify' ); ?></h2>
                <span class="dashicons dashicons-admin-page"></span>
            </div>
            <div class="dedebtify-widget-content">
                <div class="dedebtify-widget-stat">
                    <span class="stat-value" id="total-cards">0</span>
                    <span class="stat-label"><?php _e( 'Total Cards', 'dedebtify' ); ?></span>
                </div>
            </div>
        </div>

        <!-- Loans Widget -->
        <div class="dedebtify-widget">
            <div class="dedebtify-widget-header">
                <h2><?php _e( 'Loans', 'dedebtify' ); ?></h2>
                <span class="dashicons dashicons-money-alt"></span>
            </div>
            <div class="dedebtify-widget-content">
                <div class="dedebtify-widget-stat">
                    <span class="stat-value" id="total-loans">0</span>
                    <span class="stat-label"><?php _e( 'Total Loans', 'dedebtify' ); ?></span>
                </div>
            </div>
        </div>

        <!-- Goals Widget -->
        <div class="dedebtify-widget">
            <div class="dedebtify-widget-header">
                <h2><?php _e( 'Financial Goals', 'dedebtify' ); ?></h2>
                <span class="dashicons dashicons-flag"></span>
            </div>
            <div class="dedebtify-widget-content">
                <div class="dedebtify-widget-stat">
                    <span class="stat-value" id="total-goals">0</span>
                    <span class="stat-label"><?php _e( 'Active Goals', 'dedebtify' ); ?></span>
                </div>
            </div>
        </div>

        <!-- Snapshots Widget -->
        <div class="dedebtify-widget">
            <div class="dedebtify-widget-header">
                <h2><?php _e( 'Snapshots', 'dedebtify' ); ?></h2>
                <span class="dashicons dashicons-camera"></span>
            </div>
            <div class="dedebtify-widget-content">
                <div class="dedebtify-widget-stat">
                    <span class="stat-value" id="total-snapshots">0</span>
                    <span class="stat-label"><?php _e( 'Total Snapshots', 'dedebtify' ); ?></span>
                </div>
            </div>
        </div>

    </div>

    <!-- Recent Activity Section -->
    <div class="dedebtify-admin-section">
        <div class="dedebtify-section-header">
            <h2><?php _e( 'Recent Activity', 'dedebtify' ); ?></h2>
            <button id="refresh-activity" class="button button-secondary">
                <span class="dashicons dashicons-update"></span> <?php _e( 'Refresh', 'dedebtify' ); ?>
            </button>
        </div>

        <div id="recent-activity-container">
            <div class="dedebtify-loading">
                <span class="spinner is-active"></span>
                <p><?php _e( 'Loading recent activity...', 'dedebtify' ); ?></p>
            </div>
        </div>
    </div>

    <!-- System Statistics -->
    <div class="dedebtify-admin-section">
        <h2><?php _e( 'System Statistics', 'dedebtify' ); ?></h2>

        <div class="dedebtify-stats-grid">
            <div class="dedebtify-stat-item">
                <div class="stat-icon">
                    <span class="dashicons dashicons-admin-page"></span>
                </div>
                <div class="stat-info">
                    <div class="stat-value" id="stat-credit-cards">0</div>
                    <div class="stat-label"><?php _e( 'Total Credit Cards', 'dedebtify' ); ?></div>
                </div>
            </div>

            <div class="dedebtify-stat-item">
                <div class="stat-icon">
                    <span class="dashicons dashicons-money-alt"></span>
                </div>
                <div class="stat-info">
                    <div class="stat-value" id="stat-loans">0</div>
                    <div class="stat-label"><?php _e( 'Total Loans', 'dedebtify' ); ?></div>
                </div>
            </div>

            <div class="dedebtify-stat-item">
                <div class="stat-icon">
                    <span class="dashicons dashicons-clipboard"></span>
                </div>
                <div class="stat-info">
                    <div class="stat-value" id="stat-bills">0</div>
                    <div class="stat-label"><?php _e( 'Total Bills', 'dedebtify' ); ?></div>
                </div>
            </div>

            <div class="dedebtify-stat-item">
                <div class="stat-icon">
                    <span class="dashicons dashicons-flag"></span>
                </div>
                <div class="stat-info">
                    <div class="stat-value" id="stat-goals">0</div>
                    <div class="stat-label"><?php _e( 'Total Goals', 'dedebtify' ); ?></div>
                </div>
            </div>

            <div class="dedebtify-stat-item">
                <div class="stat-icon">
                    <span class="dashicons dashicons-camera"></span>
                </div>
                <div class="stat-info">
                    <div class="stat-value" id="stat-snapshots">0</div>
                    <div class="stat-label"><?php _e( 'Total Snapshots', 'dedebtify' ); ?></div>
                </div>
            </div>

            <div class="dedebtify-stat-item">
                <div class="stat-icon">
                    <span class="dashicons dashicons-database"></span>
                </div>
                <div class="stat-info">
                    <div class="stat-value" id="stat-total-items">0</div>
                    <div class="stat-label"><?php _e( 'Total Items', 'dedebtify' ); ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="dedebtify-admin-section">
        <h2><?php _e( 'Quick Actions', 'dedebtify' ); ?></h2>

        <div class="dedebtify-quick-actions">
            <a href="<?php echo admin_url( 'admin.php?page=dedebtify-settings' ); ?>" class="dedebtify-action-card">
                <span class="dashicons dashicons-admin-settings"></span>
                <h3><?php _e( 'Settings', 'dedebtify' ); ?></h3>
                <p><?php _e( 'Configure plugin settings', 'dedebtify' ); ?></p>
            </a>

            <a href="<?php echo admin_url( 'edit.php?post_type=dd_credit_card' ); ?>" class="dedebtify-action-card">
                <span class="dashicons dashicons-admin-page"></span>
                <h3><?php _e( 'View Credit Cards', 'dedebtify' ); ?></h3>
                <p><?php _e( 'Manage all credit cards', 'dedebtify' ); ?></p>
            </a>

            <a href="<?php echo admin_url( 'edit.php?post_type=dd_loan' ); ?>" class="dedebtify-action-card">
                <span class="dashicons dashicons-money-alt"></span>
                <h3><?php _e( 'View Loans', 'dedebtify' ); ?></h3>
                <p><?php _e( 'Manage all loans', 'dedebtify' ); ?></p>
            </a>

            <a href="<?php echo admin_url( 'admin.php?page=dedebtify-reports' ); ?>" class="dedebtify-action-card">
                <span class="dashicons dashicons-chart-bar"></span>
                <h3><?php _e( 'Reports', 'dedebtify' ); ?></h3>
                <p><?php _e( 'View analytics and reports', 'dedebtify' ); ?></p>
            </a>
        </div>
    </div>

</div>
