<?php
/**
 * Public Dashboard Template - Modern Design
 *
 * @since      1.0.0
 * @package    Dedebtify
 */

if (!defined('WPINC')) {
    die;
}

if (!is_user_logged_in()) {
    echo '<p>' . __('Please log in to view your dashboard.', 'dedebtify') . '</p>';
    return;
}

$user_id = get_current_user_id();
$user_info = get_userdata($user_id);
$display_name = $user_info->display_name;
$user_email = $user_info->user_email;
$initials = strtoupper(substr($display_name, 0, 1));
if (strpos($display_name, ' ') !== false) {
    $names = explode(' ', $display_name);
    $initials = strtoupper(substr($names[0], 0, 1) . substr(end($names), 0, 1));
}
?>

<!-- Sidebar Navigation -->
<div class="dd-sidebar">
    <div class="dd-sidebar-header">
        <div class="dd-sidebar-profile">
            <div class="dd-sidebar-avatar">
                <?php echo get_avatar($user_id, 56); ?>
            </div>
            <div class="dd-sidebar-user-info">
                <h3 class="dd-sidebar-user-name"><?php echo esc_html($display_name); ?></h3>
                <p class="dd-sidebar-user-email"><?php echo esc_html($user_email); ?></p>
            </div>
        </div>
    </div>
    
    <nav class="dd-sidebar-nav">
        <a href="<?php echo esc_url(Dedebtify_Helpers::get_page_url('dashboard')); ?>" class="dd-sidebar-nav-item active">
            <span class="dashicons dashicons-dashboard"></span>
            <?php _e('Dashboard', 'dedebtify'); ?>
        </a>
        <a href="<?php echo esc_url(Dedebtify_Helpers::get_page_url('credit_cards')); ?>" class="dd-sidebar-nav-item">
            <span class="dashicons dashicons-money-alt"></span>
            <?php _e('Credit Cards', 'dedebtify'); ?>
        </a>
        <a href="<?php echo esc_url(Dedebtify_Helpers::get_page_url('loans')); ?>" class="dd-sidebar-nav-item">
            <span class="dashicons dashicons-admin-site-alt3"></span>
            <?php _e('Loans', 'dedebtify'); ?>
        </a>
        <a href="<?php echo esc_url(Dedebtify_Helpers::get_page_url('mortgages')); ?>" class="dd-sidebar-nav-item">
            <span class="dashicons dashicons-admin-home"></span>
            <?php _e('Mortgage', 'dedebtify'); ?>
        </a>
        <a href="<?php echo esc_url(Dedebtify_Helpers::get_page_url('bills')); ?>" class="dd-sidebar-nav-item">
            <span class="dashicons dashicons-list-view"></span>
            <?php _e('Bills', 'dedebtify'); ?>
        </a>
        <a href="<?php echo esc_url(Dedebtify_Helpers::get_page_url('goals')); ?>" class="dd-sidebar-nav-item">
            <span class="dashicons dashicons-star-filled"></span>
            <?php _e('Goals', 'dedebtify'); ?>
        </a>
        <a href="<?php echo esc_url(Dedebtify_Helpers::get_page_url('snapshots')); ?>" class="dd-sidebar-nav-item">
            <span class="dashicons dashicons-chart-line"></span>
            <?php _e('Progress', 'dedebtify'); ?>
        </a>
        <a href="<?php echo esc_url(Dedebtify_Helpers::get_page_url('ai_coach')); ?>" class="dd-sidebar-nav-item">
            <span class="dashicons dashicons-welcome-learn-more"></span>
            <?php _e('AI Coach', 'dedebtify'); ?>
        </a>
        <a href="#" class="dd-sidebar-nav-item">
            <span class="dashicons dashicons-admin-generic"></span>
            <?php _e('Settings', 'dedebtify'); ?>
        </a>
    </nav>
</div>

<!-- Main Content -->
<div class="dd-layout-with-sidebar">
    <div class="dd-main-content">
        
        <!-- Welcome Header -->
        <h1 style="font-size: 32px; font-weight: 700; color: #1f2937; margin: 0 0 8px 0;">
            <?php printf(__('Welcome back, %s!', 'dedebtify'), esc_html($display_name)); ?>
        </h1>
        <p style="font-size: 15px; color: #6b7280; margin: 0 0 32px 0;">
            <?php _e('Here\'s your financial overview', 'dedebtify'); ?>
        </p>

        <!-- Stats Cards -->
        <div class="dd-stats-row">
            <div class="dd-stat-card-modern">
                <div class="dd-stat-icon-top red">
                    <span class="dashicons dashicons-money-alt"></span>
                </div>
                <div class="dd-stat-label-modern"><?php _e('TOTAL DEBT', 'dedebtify'); ?></div>
                <div class="dd-stat-value-modern" id="dd-total-debt">$0.00</div>
                <div class="dd-stat-change positive">↓ 2.5% <?php _e('this month', 'dedebtify'); ?></div>
            </div>

            <div class="dd-stat-card-modern">
                <div class="dd-stat-icon-top orange">
                    <span class="dashicons dashicons-calendar-alt"></span>
                </div>
                <div class="dd-stat-label-modern"><?php _e('MONTHLY PAYMENTS', 'dedebtify'); ?></div>
                <div class="dd-stat-value-modern" id="dd-monthly-payments">$0.00</div>
                <div class="dd-stat-change"><?php _e('Due soon', 'dedebtify'); ?></div>
            </div>

            <div class="dd-stat-card-modern">
                <div class="dd-stat-icon-top purple">
                    <span class="dashicons dashicons-chart-line"></span>
                </div>
                <div class="dd-stat-label-modern"><?php _e('CREDIT UTILIZATION', 'dedebtify'); ?></div>
                <div class="dd-stat-value-modern" id="dd-credit-utilization">0%</div>
                <div class="dd-stat-change positive">↓ 5% <?php _e('improved', 'dedebtify'); ?></div>
            </div>

            <div class="dd-stat-card-modern">
                <div class="dd-stat-icon-top teal">
                    <span class="dashicons dashicons-yes-alt"></span>
                </div>
                <div class="dd-stat-label-modern"><?php _e('DEBT-FREE DATE', 'dedebtify'); ?></div>
                <div class="dd-stat-value-modern" id="dd-debt-free-date" style="font-size: 18px;">—</div>
                <div class="dd-stat-change"><?php _e('Projected', 'dedebtify'); ?></div>
            </div>
        </div>

        <!-- Debt Breakdown Chart -->
        <div class="dd-chart-card" id="debt-breakdown-section" style="display: none;">
            <div class="dd-chart-header">
                <div>
                    <h2 class="dd-chart-title"><?php _e('Debt Breakdown', 'dedebtify'); ?></h2>
                    <p class="dd-chart-subtitle"><?php _e('Distribution across categories', 'dedebtify'); ?></p>
                </div>
            </div>
            <div class="debt-breakdown-chart">
                <div class="debt-breakdown-bars" id="debt-breakdown-bars"></div>
            </div>
            <div class="debt-breakdown-legend" id="debt-breakdown-legend"></div>
        </div>

        <!-- Recent Items -->
        <div class="dd-chart-card">
            <div class="dd-chart-header">
                <h2 class="dd-chart-title"><?php _e('Recent Activity', 'dedebtify'); ?></h2>
                <button class="dedebtify-btn-small dedebtify-btn-secondary"><?php _e('View All', 'dedebtify'); ?></button>
            </div>
            <div id="dedebtify-dashboard-stats"></div>
        </div>

        <!-- Quick Actions -->
        <div class="dedebtify-btn-group" style="margin-top: 32px;">
            <button class="dd-btn-primary">
                <span class="dashicons dashicons-plus-alt"></span>
                <?php _e('ADD DEBT', 'dedebtify'); ?>
            </button>
            <button class="dd-btn-secondary">
                <span class="dashicons dashicons-camera"></span>
                <?php _e('CREATE SNAPSHOT', 'dedebtify'); ?>
            </button>
        </div>

    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Mobile sidebar toggle
    if ($(window).width() < 768) {
        $('.dd-layout-with-sidebar').prepend('<button class="dd-sidebar-toggle"><span class="dashicons dashicons-menu"></span></button>');
        $('.dd-sidebar-toggle').on('click', function() {
            $('.dd-sidebar').toggleClass('open');
        });
    }
});
</script>
