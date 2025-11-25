<?php
/**
 * Dashboard Template - Exact Design from React Source
 * Converted from App.tsx to WordPress PHP
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

// Get financial data from WordPress (would be fetched from plugin's custom post types)
$total_debt = 299375.50;
$credit_cards = get_posts(array('post_type' => 'dd_credit_card', 'author' => $user_id, 'posts_per_page' => -1));
$loans = get_posts(array('post_type' => 'dd_loan', 'author' => $user_id, 'posts_per_page' => -1));
?>

<div class="dd-app-container">
    <!-- Sidebar -->
    <aside class="dd-sidebar-exact">
        <!-- User Profile -->
        <div class="dd-sidebar-profile">
            <div class="dd-profile-image-wrapper">
                <?php echo get_avatar($user_id, 64, '', '', array('class' => 'dd-profile-image')); ?>
                <span class="dd-profile-badge">4</span>
            </div>
            <h2 class="dd-profile-name"><?php echo esc_html($display_name); ?></h2>
            <p class="dd-profile-email"><?php echo esc_html($user_info->user_email); ?></p>
        </div>

        <!-- Navigation -->
        <nav class="dd-sidebar-nav">
            <a href="<?php echo esc_url(Dedebtify_Helpers::get_page_url('dashboard')); ?>" class="dd-nav-item active">
                <div class="dd-nav-item-icon">
                    <span class="dashicons dashicons-dashboard"></span>
                </div>
                <span class="dd-nav-item-label"><?php _e('Dashboard', 'dedebtify'); ?></span>
                <div class="dd-nav-active-indicator"></div>
            </a>
            <a href="<?php echo esc_url(Dedebtify_Helpers::get_page_url('credit_cards')); ?>" class="dd-nav-item">
                <div class="dd-nav-item-icon">
                    <span class="dashicons dashicons-money-alt"></span>
                </div>
                <span class="dd-nav-item-label"><?php _e('Credit Cards', 'dedebtify'); ?></span>
            </a>
            <a href="<?php echo esc_url(Dedebtify_Helpers::get_page_url('loans')); ?>" class="dd-nav-item">
                <div class="dd-nav-item-icon">
                    <span class="dashicons dashicons-admin-site-alt3"></span>
                </div>
                <span class="dd-nav-item-label"><?php _e('Loans', 'dedebtify'); ?></span>
            </a>
            <a href="<?php echo esc_url(Dedebtify_Helpers::get_page_url('mortgages')); ?>" class="dd-nav-item">
                <div class="dd-nav-item-icon">
                    <span class="dashicons dashicons-admin-home"></span>
                </div>
                <span class="dd-nav-item-label"><?php _e('Mortgage', 'dedebtify'); ?></span>
            </a>
            <a href="<?php echo esc_url(Dedebtify_Helpers::get_page_url('bills')); ?>" class="dd-nav-item">
                <div class="dd-nav-item-icon">
                    <span class="dashicons dashicons-list-view"></span>
                </div>
                <span class="dd-nav-item-label"><?php _e('Bills', 'dedebtify'); ?></span>
            </a>
            <a href="<?php echo esc_url(Dedebtify_Helpers::get_page_url('goals')); ?>" class="dd-nav-item">
                <div class="dd-nav-item-icon">
                    <span class="dashicons dashicons-star-filled"></span>
                </div>
                <span class="dd-nav-item-label"><?php _e('Goals', 'dedebtify'); ?></span>
            </a>
            <a href="<?php echo esc_url(Dedebtify_Helpers::get_page_url('snapshots')); ?>" class="dd-nav-item">
                <div class="dd-nav-item-icon">
                    <span class="dashicons dashicons-chart-line"></span>
                </div>
                <span class="dd-nav-item-label"><?php _e('Progress', 'dedebtify'); ?></span>
            </a>
            <a href="<?php echo esc_url(Dedebtify_Helpers::get_page_url('ai_coach')); ?>" class="dd-nav-item">
                <div class="dd-nav-item-icon">
                    <span class="dashicons dashicons-welcome-learn-more"></span>
                </div>
                <span class="dd-nav-item-label"><?php _e('AI Coach', 'dedebtify'); ?></span>
            </a>
            <a href="#" class="dd-nav-item">
                <div class="dd-nav-item-icon">
                    <span class="dashicons dashicons-admin-generic"></span>
                </div>
                <span class="dd-nav-item-label"><?php _e('Settings', 'dedebtify'); ?></span>
            </a>
        </nav>

        <!-- Logout -->
        <div class="dd-sidebar-logout">
            <a href="<?php echo wp_logout_url(home_url()); ?>" class="dd-logout-btn">
                <span class="dashicons dashicons-exit"></span>
                <span class="dd-nav-item-label"><?php _e('Logout', 'dedebtify'); ?></span>
            </a>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <main class="dd-main-wrapper">
        
        <!-- Left/Middle Column (Main Dashboard) -->
        <div class="dd-main-content-area">
            
            <!-- Header -->
            <header class="dd-header">
                <div class="dd-header-top">
                    <div>
                        <h1 class="dd-header-title"><?php printf(__('Welcome back, %s!', 'dedebtify'), esc_html($display_name)); ?></h1>
                        <p class="dd-header-subtitle"><?php _e("Here's your current financial overview", 'dedebtify'); ?></p>
                    </div>
                    <div class="dd-header-actions">
                        <button class="dd-header-icon-btn">
                            <span class="dashicons dashicons-search"></span>
                        </button>
                        <button class="dd-header-icon-btn">
                            <span class="dashicons dashicons-bell"></span>
                            <span class="dd-notification-dot"></span>
                        </button>
                    </div>
                </div>

                <!-- Quick Actions Toolbar -->
                <div class="dd-toolbar">
                    <a href="<?php echo esc_url(Dedebtify_Helpers::get_page_url('snapshots')); ?>" class="dd-btn-primary-exact">
                        <span class="dashicons dashicons-plus"></span>
                        <?php _e('Create Snapshot', 'dedebtify'); ?>
                    </a>
                    <a href="<?php echo esc_url(Dedebtify_Helpers::get_page_url('credit_cards')); ?>" class="dd-btn-secondary-exact"><?php _e('View Cards', 'dedebtify'); ?></a>
                    <a href="<?php echo esc_url(Dedebtify_Helpers::get_page_url('loans')); ?>" class="dd-btn-secondary-exact"><?php _e('View Loans', 'dedebtify'); ?></a>
                    <a href="<?php echo esc_url(Dedebtify_Helpers::get_page_url('bills')); ?>" class="dd-btn-secondary-exact"><?php _e('View Bills', 'dedebtify'); ?></a>
                    <a href="<?php echo esc_url(Dedebtify_Helpers::get_page_url('goals')); ?>" class="dd-btn-secondary-exact"><?php _e('View Goals', 'dedebtify'); ?></a>
                </div>
            </header>

            <div class="dd-content-padding">
                <!-- Debt Breakdown Section -->
                <section class="dd-section-card">
                    <div class="dd-section-header">
                        <h3 class="dd-section-title"><?php _e('Debt Breakdown', 'dedebtify'); ?></h3>
                        <button class="dd-header-icon-btn">
                            <span class="dashicons dashicons-filter"></span>
                        </button>
                    </div>
                    
                    <!-- Stacked Bar Chart -->
                    <div class="dd-debt-chart-bar">
                        <div class="dd-debt-segment" style="width: 3.2%; background: #EF4444;">
                            <!-- Red: Credit Cards -->
                        </div>
                        <div class="dd-debt-segment" style="width: 17.1%; background: #F59E0B;">
                            <div class="dd-debt-segment-label">17%</div>
                        </div>
                        <div class="dd-debt-segment" style="width: 79.7%; background: #3B82F6;">
                            <div class="dd-debt-segment-label">79.7%</div>
                        </div>
                    </div>

                    <!-- Legend -->
                    <div class="dd-debt-legend">
                        <div class="dd-legend-item">
                            <div class="dd-legend-dot" style="background: #EF4444;"></div>
                            <div>
                                <p class="dd-legend-category"><?php _e('Credit Cards', 'dedebtify'); ?></p>
                                <div>
                                    <span class="dd-legend-amount">$9,625</span>
                                    <span class="dd-legend-percentage">(3.2%)</span>
                                </div>
                            </div>
                        </div>
                        <div class="dd-legend-item">
                            <div class="dd-legend-dot" style="background: #F59E0B;"></div>
                            <div>
                                <p class="dd-legend-category"><?php _e('Loans', 'dedebtify'); ?></p>
                                <div>
                                    <span class="dd-legend-amount">$51,250</span>
                                    <span class="dd-legend-percentage">(17.1%)</span>
                                </div>
                            </div>
                        </div>
                        <div class="dd-legend-item">
                            <div class="dd-legend-dot" style="background: #3B82F6;"></div>
                            <div>
                                <p class="dd-legend-category"><?php _e('Mortgage', 'dedebtify'); ?></p>
                                <div>
                                    <span class="dd-legend-amount">$238,500</span>
                                    <span class="dd-legend-percentage">(79.7%)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Two Column Grid -->
                <div class="dd-two-column-grid">
                    
                    <!-- Credit Cards Column -->
                    <section>
                        <div class="dd-section-header">
                            <h3 class="dd-section-title"><?php _e('Active Credit Cards', 'dedebtify'); ?></h3>
                            <span class="dd-badge dd-badge-blue">3 Active</span>
                        </div>
                        
                        <!-- Credit Card Example 1: Capital One -->
                        <div class="dd-credit-card">
                            <div class="dd-card-accent" style="background: #ef4444;"></div>
                            
                            <div class="dd-card-header">
                                <div class="dd-card-info">
                                    <div class="dd-card-icon blue">
                                        <span class="dashicons dashicons-money-alt"></span>
                                    </div>
                                    <div>
                                        <h4 class="dd-card-name">Capital One Quicksilver</h4>
                                        <div class="dd-card-meta">
                                            <span class="dd-card-apr">APR: 23.0%</span>
                                            <span>•</span>
                                            <span class="dd-card-status">Active</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="dd-card-balance">
                                    <div class="dd-card-balance-amount">$4,500</div>
                                    <div class="dd-card-balance-limit">Limit: $8,000</div>
                                </div>
                            </div>

                            <div class="dd-card-utilization">
                                <div class="dd-util-header">
                                    <span class="dd-util-label">Utilization</span>
                                    <span class="dd-util-value high">56.3%</span>
                                </div>
                                <div class="dd-progress-bar">
                                    <div class="dd-progress-fill red" style="width: 56.3%;"></div>
                                </div>
                            </div>

                            <div class="dd-card-details">
                                <div class="dd-card-detail-item">
                                    <div class="dd-detail-icon blue">
                                        <span class="dashicons dashicons-calendar-alt"></span>
                                    </div>
                                    <div>
                                        <p class="dd-detail-label">Payoff Date</p>
                                        <p class="dd-detail-value">March 2028</p>
                                    </div>
                                </div>
                                <div class="dd-detail-divider"></div>
                                <div class="dd-card-detail-item">
                                    <div class="dd-detail-icon red">
                                        <span class="dashicons dashicons-money"></span>
                                    </div>
                                    <div>
                                        <p class="dd-detail-label">Est. Interest</p>
                                        <p class="dd-detail-value">$1,380</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Credit Card Example 2: Discover -->
                        <div class="dd-credit-card">
                            <div class="dd-card-accent" style="background: #f59e0b;"></div>
                            
                            <div class="dd-card-header">
                                <div class="dd-card-info">
                                    <div class="dd-card-icon orange">
                                        <span class="dashicons dashicons-money-alt"></span>
                                    </div>
                                    <div>
                                        <h4 class="dd-card-name">Discover It Cash Back</h4>
                                        <div class="dd-card-meta">
                                            <span class="dd-card-apr">APR: 16.5%</span>
                                            <span>•</span>
                                            <span class="dd-card-status">Active</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="dd-card-balance">
                                    <div class="dd-card-balance-amount">$1,876</div>
                                    <div class="dd-card-balance-limit">Limit: $5,000</div>
                                </div>
                            </div>

                            <div class="dd-card-utilization">
                                <div class="dd-util-header">
                                    <span class="dd-util-label">Utilization</span>
                                    <span class="dd-util-value normal">37.5%</span>
                                </div>
                                <div class="dd-progress-bar">
                                    <div class="dd-progress-fill yellow" style="width: 37.5%;"></div>
                                </div>
                            </div>

                            <div class="dd-card-details">
                                <div class="dd-card-detail-item">
                                    <div class="dd-detail-icon blue">
                                        <span class="dashicons dashicons-calendar-alt"></span>
                                    </div>
                                    <div>
                                        <p class="dd-detail-label">Payoff Date</p>
                                        <p class="dd-detail-value">Jan 2027</p>
                                    </div>
                                </div>
                                <div class="dd-detail-divider"></div>
                                <div class="dd-card-detail-item">
                                    <div class="dd-detail-icon red">
                                        <span class="dashicons dashicons-money"></span>
                                    </div>
                                    <div>
                                        <p class="dd-detail-label">Est. Interest</p>
                                        <p class="dd-detail-value">$312</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Loans Column -->
                    <section>
                        <div class="dd-section-header">
                            <h3 class="dd-section-title"><?php _e('Loans', 'dedebtify'); ?></h3>
                            <span class="dd-badge dd-badge-orange">2 Active</span>
                        </div>
                        
                        <div id="dd-loans-list">
                            <!-- Loans will be loaded via JavaScript or rendered here -->
                            <p style="text-align: center; color: #6b7280; padding: 40px;"><?php _e('Loans section coming soon', 'dedebtify'); ?></p>
                        </div>
                    </section>

                </div>
            </div>
        </div>

        <!-- Right Sidebar (Goals, Bills, Promo) -->
        <div class="dd-right-sidebar">
            <h3 class="dd-section-title" style="margin-bottom: 24px;"><?php _e('Goals & Bills', 'dedebtify'); ?></h3>
            <p style="text-align: center; color: #6b7280; padding: 40px 20px;"><?php _e('Right sidebar widgets coming soon', 'dedebtify'); ?></p>
        </div>

    </main>
</div>
