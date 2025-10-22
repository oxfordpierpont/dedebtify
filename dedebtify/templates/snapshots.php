<?php
/**
 * Snapshots Comparison Template
 *
 * This template displays financial snapshots and allows comparison over time.
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
    echo '<p>' . __( 'Please log in to view your financial snapshots.', 'dedebtify' ) . '</p>';
    return;
}

$user_id = get_current_user_id();
?>

<div class="dedebtify-dashboard dedebtify-snapshots">

    <div class="dedebtify-dashboard-header">
        <h1><?php _e( 'Financial Snapshots', 'dedebtify' ); ?></h1>
        <p><?php _e( 'Track your progress over time and compare snapshots to see how far you\'ve come', 'dedebtify' ); ?></p>
    </div>

    <!-- Create Snapshot Action -->
    <div class="dedebtify-snapshot-actions">
        <button id="create-snapshot" class="dedebtify-btn dedebtify-btn-success">
            <?php _e( 'Create New Snapshot', 'dedebtify' ); ?>
        </button>
        <p class="dedebtify-help-text"><?php _e( 'Snapshots capture your current financial state for future comparison', 'dedebtify' ); ?></p>
    </div>

    <!-- Progress Overview -->
    <div id="dedebtify-progress-overview" class="dedebtify-section" style="display: none;">
        <h2><?php _e( 'Your Progress', 'dedebtify' ); ?></h2>

        <div class="dedebtify-stats-grid">
            <div class="dedebtify-stat-card">
                <div class="dedebtify-stat-label"><?php _e( 'Total Debt Reduced', 'dedebtify' ); ?></div>
                <div class="dedebtify-stat-value success" id="progress-debt-reduced">$0</div>
                <div class="dedebtify-stat-subtext" id="progress-debt-percent">0% reduction</div>
            </div>
            <div class="dedebtify-stat-card">
                <div class="dedebtify-stat-label"><?php _e( 'DTI Improvement', 'dedebtify' ); ?></div>
                <div class="dedebtify-stat-value" id="progress-dti-change">0%</div>
                <div class="dedebtify-stat-subtext"><?php _e( 'Since first snapshot', 'dedebtify' ); ?></div>
            </div>
            <div class="dedebtify-stat-card">
                <div class="dedebtify-stat-label"><?php _e( 'Months Tracked', 'dedebtify' ); ?></div>
                <div class="dedebtify-stat-value" id="progress-months">0</div>
                <div class="dedebtify-stat-subtext" id="progress-date-range">—</div>
            </div>
            <div class="dedebtify-stat-card">
                <div class="dedebtify-stat-label"><?php _e( 'Average Monthly Reduction', 'dedebtify' ); ?></div>
                <div class="dedebtify-stat-value" id="progress-avg-monthly">$0</div>
                <div class="dedebtify-stat-subtext"><?php _e( 'Debt paydown rate', 'dedebtify' ); ?></div>
            </div>
        </div>

        <!-- Debt Progress Chart -->
        <div class="dedebtify-chart-container">
            <h3><?php _e( 'Debt Over Time', 'dedebtify' ); ?></h3>
            <div id="debt-progress-chart" class="dedebtify-chart">
                <!-- Chart will be rendered here -->
            </div>
        </div>
    </div>

    <!-- Snapshot Comparison -->
    <div id="dedebtify-snapshot-comparison" class="dedebtify-section">
        <h2><?php _e( 'Compare Snapshots', 'dedebtify' ); ?></h2>

        <div class="dedebtify-comparison-selector">
            <div class="dedebtify-form-group">
                <label for="snapshot-select-1" class="dedebtify-form-label"><?php _e( 'First Snapshot', 'dedebtify' ); ?></label>
                <select id="snapshot-select-1" class="dedebtify-form-select">
                    <option value=""><?php _e( 'Select a snapshot...', 'dedebtify' ); ?></option>
                </select>
            </div>

            <div class="dedebtify-comparison-vs">
                <span>VS</span>
            </div>

            <div class="dedebtify-form-group">
                <label for="snapshot-select-2" class="dedebtify-form-label"><?php _e( 'Second Snapshot', 'dedebtify' ); ?></label>
                <select id="snapshot-select-2" class="dedebtify-form-select">
                    <option value=""><?php _e( 'Select a snapshot...', 'dedebtify' ); ?></option>
                </select>
            </div>

            <button id="compare-snapshots" class="dedebtify-btn dedebtify-btn-primary" disabled>
                <?php _e( 'Compare', 'dedebtify' ); ?>
            </button>
        </div>

        <!-- Comparison Results -->
        <div id="comparison-results" style="display: none;">
            <div class="dedebtify-comparison-header">
                <h3><?php _e( 'Comparison Results', 'dedebtify' ); ?></h3>
                <button id="clear-comparison" class="dedebtify-btn dedebtify-btn-secondary dedebtify-btn-small">
                    <?php _e( 'Clear', 'dedebtify' ); ?>
                </button>
            </div>

            <div class="dedebtify-comparison-grid">
                <!-- Snapshot 1 -->
                <div class="dedebtify-snapshot-card">
                    <div class="dedebtify-snapshot-header">
                        <h4 id="snapshot1-title"><?php _e( 'Snapshot 1', 'dedebtify' ); ?></h4>
                        <span class="dedebtify-snapshot-date" id="snapshot1-date">—</span>
                    </div>
                    <div class="dedebtify-snapshot-metrics">
                        <div class="dedebtify-metric">
                            <span class="metric-label"><?php _e( 'Total Debt', 'dedebtify' ); ?></span>
                            <span class="metric-value" id="snapshot1-debt">$0</span>
                        </div>
                        <div class="dedebtify-metric">
                            <span class="metric-label"><?php _e( 'Monthly Payments', 'dedebtify' ); ?></span>
                            <span class="metric-value" id="snapshot1-payments">$0</span>
                        </div>
                        <div class="dedebtify-metric">
                            <span class="metric-label"><?php _e( 'DTI Ratio', 'dedebtify' ); ?></span>
                            <span class="metric-value" id="snapshot1-dti">0%</span>
                        </div>
                        <div class="dedebtify-metric">
                            <span class="metric-label"><?php _e( 'Credit Utilization', 'dedebtify' ); ?></span>
                            <span class="metric-value" id="snapshot1-util">0%</span>
                        </div>
                        <div class="dedebtify-metric">
                            <span class="metric-label"><?php _e( 'Credit Cards', 'dedebtify' ); ?></span>
                            <span class="metric-value" id="snapshot1-cards">0</span>
                        </div>
                        <div class="dedebtify-metric">
                            <span class="metric-label"><?php _e( 'Loans', 'dedebtify' ); ?></span>
                            <span class="metric-value" id="snapshot1-loans">0</span>
                        </div>
                    </div>
                </div>

                <!-- Change Indicators -->
                <div class="dedebtify-snapshot-changes">
                    <div class="dedebtify-change-item" id="change-debt">
                        <span class="change-label"><?php _e( 'Debt Change', 'dedebtify' ); ?></span>
                        <span class="change-value">—</span>
                        <span class="change-icon">→</span>
                    </div>
                    <div class="dedebtify-change-item" id="change-payments">
                        <span class="change-label"><?php _e( 'Payment Change', 'dedebtify' ); ?></span>
                        <span class="change-value">—</span>
                        <span class="change-icon">→</span>
                    </div>
                    <div class="dedebtify-change-item" id="change-dti">
                        <span class="change-label"><?php _e( 'DTI Change', 'dedebtify' ); ?></span>
                        <span class="change-value">—</span>
                        <span class="change-icon">→</span>
                    </div>
                    <div class="dedebtify-change-item" id="change-util">
                        <span class="change-label"><?php _e( 'Utilization Change', 'dedebtify' ); ?></span>
                        <span class="change-value">—</span>
                        <span class="change-icon">→</span>
                    </div>
                </div>

                <!-- Snapshot 2 -->
                <div class="dedebtify-snapshot-card">
                    <div class="dedebtify-snapshot-header">
                        <h4 id="snapshot2-title"><?php _e( 'Snapshot 2', 'dedebtify' ); ?></h4>
                        <span class="dedebtify-snapshot-date" id="snapshot2-date">—</span>
                    </div>
                    <div class="dedebtify-snapshot-metrics">
                        <div class="dedebtify-metric">
                            <span class="metric-label"><?php _e( 'Total Debt', 'dedebtify' ); ?></span>
                            <span class="metric-value" id="snapshot2-debt">$0</span>
                        </div>
                        <div class="dedebtify-metric">
                            <span class="metric-label"><?php _e( 'Monthly Payments', 'dedebtify' ); ?></span>
                            <span class="metric-value" id="snapshot2-payments">$0</span>
                        </div>
                        <div class="dedebtify-metric">
                            <span class="metric-label"><?php _e( 'DTI Ratio', 'dedebtify' ); ?></span>
                            <span class="metric-value" id="snapshot2-dti">0%</span>
                        </div>
                        <div class="dedebtify-metric">
                            <span class="metric-label"><?php _e( 'Credit Utilization', 'dedebtify' ); ?></span>
                            <span class="metric-value" id="snapshot2-util">0%</span>
                        </div>
                        <div class="dedebtify-metric">
                            <span class="metric-label"><?php _e( 'Credit Cards', 'dedebtify' ); ?></span>
                            <span class="metric-value" id="snapshot2-cards">0</span>
                        </div>
                        <div class="dedebtify-metric">
                            <span class="metric-label"><?php _e( 'Loans', 'dedebtify' ); ?></span>
                            <span class="metric-value" id="snapshot2-loans">0</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary -->
            <div class="dedebtify-comparison-summary">
                <h4><?php _e( 'Summary', 'dedebtify' ); ?></h4>
                <div id="comparison-summary-text">
                    <?php _e( 'Select snapshots to see comparison', 'dedebtify' ); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Snapshot History List -->
    <div id="dedebtify-snapshot-list" class="dedebtify-section">
        <h2><?php _e( 'Snapshot History', 'dedebtify' ); ?></h2>

        <div id="snapshots-list-container">
            <div class="dedebtify-loading">
                <div class="dedebtify-spinner"></div>
                <p><?php _e( 'Loading snapshots...', 'dedebtify' ); ?></p>
            </div>
        </div>
    </div>

    <!-- Empty State -->
    <div class="dedebtify-empty-state" id="snapshots-empty-state" style="display: none;">
        <h3><?php _e( 'No Snapshots Yet', 'dedebtify' ); ?></h3>
        <p><?php _e( 'Create your first financial snapshot to start tracking your debt payoff progress over time.', 'dedebtify' ); ?></p>
        <button id="create-first-snapshot" class="dedebtify-btn dedebtify-btn-success">
            <?php _e( 'Create Your First Snapshot', 'dedebtify' ); ?>
        </button>
    </div>

</div>
