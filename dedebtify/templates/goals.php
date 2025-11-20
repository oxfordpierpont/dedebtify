<?php
/**
 * Goals Manager Template
 *
 * This template displays and manages user's financial goals.
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
    echo '<p>' . __( 'Please log in to manage your goals.', 'dedebtify' ) . '</p>';
    return;
}

$user_id = get_current_user_id();
$edit_id = isset( $_GET['edit'] ) ? intval( $_GET['edit'] ) : 0;
$action = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : 'list';
?>

<!-- Navigation -->
<?php Dedebtify_Helpers::render_navigation( 'goals' ); ?>

<div class="dedebtify-dashboard dedebtify-goals-manager">

    <div class="dedebtify-dashboard-header">
        <h1><?php _e( 'Goals Manager', 'dedebtify' ); ?></h1>
        <p><?php _e( 'Set and track your financial goals and savings milestones', 'dedebtify' ); ?></p>
    </div>

    <?php if ( $action === 'add' || $action === 'edit' ) : ?>
        <!-- Add/Edit Form -->
        <div class="dedebtify-form-container">
            <div class="dedebtify-form-header">
                <h2><?php echo $action === 'edit' ? __( 'Edit Goal', 'dedebtify' ) : __( 'Add New Goal', 'dedebtify' ); ?></h2>
                <a href="?action=list" class="dedebtify-btn dedebtify-btn-secondary"><?php _e( 'Back to List', 'dedebtify' ); ?></a>
            </div>

            <form id="dedebtify-goal-form" class="dedebtify-form" data-post-id="<?php echo $edit_id; ?>">
                <?php wp_nonce_field( 'dedebtify_goal_form', 'dedebtify_nonce' ); ?>

                <div class="dedebtify-form-row">
                    <div class="dedebtify-form-group">
                        <label for="goal_name" class="dedebtify-form-label"><?php _e( 'Goal Name', 'dedebtify' ); ?> <span class="required">*</span></label>
                        <input type="text" id="goal_name" name="goal_name" class="dedebtify-form-input" required placeholder="e.g., Emergency Fund">
                    </div>
                </div>

                <div class="dedebtify-form-row dedebtify-form-row-2col">
                    <div class="dedebtify-form-group">
                        <label for="goal_type" class="dedebtify-form-label"><?php _e( 'Goal Type', 'dedebtify' ); ?> <span class="required">*</span></label>
                        <select id="goal_type" name="goal_type" class="dedebtify-form-select" required>
                            <option value=""><?php _e( 'Select Type', 'dedebtify' ); ?></option>
                            <option value="savings"><?php _e( 'Savings', 'dedebtify' ); ?></option>
                            <option value="emergency_fund"><?php _e( 'Emergency Fund', 'dedebtify' ); ?></option>
                            <option value="debt_payoff"><?php _e( 'Debt Payoff', 'dedebtify' ); ?></option>
                            <option value="investment"><?php _e( 'Investment', 'dedebtify' ); ?></option>
                            <option value="purchase"><?php _e( 'Major Purchase', 'dedebtify' ); ?></option>
                            <option value="other"><?php _e( 'Other', 'dedebtify' ); ?></option>
                        </select>
                    </div>

                    <div class="dedebtify-form-group">
                        <label for="priority" class="dedebtify-form-label"><?php _e( 'Priority', 'dedebtify' ); ?></label>
                        <select id="priority" name="priority" class="dedebtify-form-select">
                            <option value="medium"><?php _e( 'Medium', 'dedebtify' ); ?></option>
                            <option value="low"><?php _e( 'Low', 'dedebtify' ); ?></option>
                            <option value="high"><?php _e( 'High', 'dedebtify' ); ?></option>
                        </select>
                    </div>
                </div>

                <div class="dedebtify-form-row dedebtify-form-row-2col">
                    <div class="dedebtify-form-group">
                        <label for="target_amount" class="dedebtify-form-label"><?php _e( 'Target Amount ($)', 'dedebtify' ); ?> <span class="required">*</span></label>
                        <input type="number" step="0.01" id="target_amount" name="target_amount" class="dedebtify-form-input" required placeholder="10000.00">
                    </div>

                    <div class="dedebtify-form-group">
                        <label for="current_amount" class="dedebtify-form-label"><?php _e( 'Current Amount ($)', 'dedebtify' ); ?> <span class="required">*</span></label>
                        <input type="number" step="0.01" id="current_amount" name="current_amount" class="dedebtify-form-input" required placeholder="3500.00">
                    </div>
                </div>

                <div class="dedebtify-form-row dedebtify-form-row-2col">
                    <div class="dedebtify-form-group">
                        <label for="monthly_contribution" class="dedebtify-form-label"><?php _e( 'Monthly Contribution ($)', 'dedebtify' ); ?></label>
                        <input type="number" step="0.01" id="monthly_contribution" name="monthly_contribution" class="dedebtify-form-input" placeholder="250.00">
                        <span class="dedebtify-form-help"><?php _e( 'Amount you plan to save each month', 'dedebtify' ); ?></span>
                    </div>

                    <div class="dedebtify-form-group">
                        <label for="target_date" class="dedebtify-form-label"><?php _e( 'Target Date', 'dedebtify' ); ?></label>
                        <input type="date" id="target_date" name="target_date" class="dedebtify-form-input">
                        <span class="dedebtify-form-help"><?php _e( 'Optional: When you want to reach this goal', 'dedebtify' ); ?></span>
                    </div>
                </div>

                <!-- Goal Progress Preview -->
                <div id="dedebtify-goal-preview" class="dedebtify-payoff-preview" style="display: none;">
                    <h3><?php _e( 'Goal Progress', 'dedebtify' ); ?></h3>

                    <div class="dedebtify-progress-container">
                        <div class="dedebtify-progress" style="height: 30px; margin-bottom: 10px;">
                            <div class="dedebtify-progress-bar success" id="goal-progress-bar" style="width: 0%"></div>
                        </div>
                        <div style="text-align: center; font-size: 1.25rem; font-weight: bold; margin-bottom: 20px;">
                            <span id="goal-progress-percent">0%</span> Complete
                        </div>
                    </div>

                    <div class="dedebtify-stats-grid">
                        <div class="dedebtify-stat-card">
                            <div class="dedebtify-stat-label"><?php _e( 'Remaining', 'dedebtify' ); ?></div>
                            <div class="dedebtify-stat-value" id="preview-remaining">$0</div>
                        </div>
                        <div class="dedebtify-stat-card">
                            <div class="dedebtify-stat-label"><?php _e( 'Months to Goal', 'dedebtify' ); ?></div>
                            <div class="dedebtify-stat-value" id="preview-months">0</div>
                        </div>
                        <div class="dedebtify-stat-card">
                            <div class="dedebtify-stat-label"><?php _e( 'Estimated Date', 'dedebtify' ); ?></div>
                            <div class="dedebtify-stat-value" id="preview-date" style="font-size: 1.2rem;">—</div>
                        </div>
                        <div class="dedebtify-stat-card">
                            <div class="dedebtify-stat-label"><?php _e( 'On Track?', 'dedebtify' ); ?></div>
                            <div class="dedebtify-stat-value" id="preview-status" style="font-size: 1.2rem;">—</div>
                        </div>
                    </div>
                </div>

                <div class="dedebtify-form-actions">
                    <button type="submit" class="dedebtify-btn dedebtify-btn-success">
                        <?php echo $action === 'edit' ? __( 'Update Goal', 'dedebtify' ) : __( 'Add Goal', 'dedebtify' ); ?>
                    </button>
                    <a href="?action=list" class="dedebtify-btn dedebtify-btn-secondary"><?php _e( 'Cancel', 'dedebtify' ); ?></a>
                </div>
            </form>
        </div>

    <?php else : ?>
        <!-- List View -->
        <div class="dedebtify-manager-header">
            <div class="dedebtify-manager-stats" id="dedebtify-goal-stats">
                <div class="dedebtify-stat-summary">
                    <span class="stat-label"><?php _e( 'Total Goal Target:', 'dedebtify' ); ?></span>
                    <span class="stat-value" id="goal-total-target">$0.00</span>
                </div>
                <div class="dedebtify-stat-summary">
                    <span class="stat-label"><?php _e( 'Total Saved:', 'dedebtify' ); ?></span>
                    <span class="stat-value" id="goal-total-saved">$0.00</span>
                </div>
                <div class="dedebtify-stat-summary">
                    <span class="stat-label"><?php _e( 'Overall Progress:', 'dedebtify' ); ?></span>
                    <span class="stat-value" id="goal-overall-progress">0%</span>
                </div>
            </div>
            <a href="?action=add" class="dedebtify-btn dedebtify-btn-success"><?php _e( '+ Add Goal', 'dedebtify' ); ?></a>
        </div>

        <div class="dedebtify-manager-controls">
            <div class="dedebtify-filter-group">
                <label for="filter-goal-type"><?php _e( 'Type:', 'dedebtify' ); ?></label>
                <select id="filter-goal-type" class="dedebtify-form-select">
                    <option value="all"><?php _e( 'All Types', 'dedebtify' ); ?></option>
                    <option value="savings"><?php _e( 'Savings', 'dedebtify' ); ?></option>
                    <option value="emergency_fund"><?php _e( 'Emergency Fund', 'dedebtify' ); ?></option>
                    <option value="debt_payoff"><?php _e( 'Debt Payoff', 'dedebtify' ); ?></option>
                    <option value="investment"><?php _e( 'Investment', 'dedebtify' ); ?></option>
                    <option value="purchase"><?php _e( 'Major Purchase', 'dedebtify' ); ?></option>
                    <option value="other"><?php _e( 'Other', 'dedebtify' ); ?></option>
                </select>
            </div>

            <div class="dedebtify-filter-group">
                <label for="filter-priority"><?php _e( 'Priority:', 'dedebtify' ); ?></label>
                <select id="filter-priority" class="dedebtify-form-select">
                    <option value="all"><?php _e( 'All', 'dedebtify' ); ?></option>
                    <option value="high"><?php _e( 'High', 'dedebtify' ); ?></option>
                    <option value="medium"><?php _e( 'Medium', 'dedebtify' ); ?></option>
                    <option value="low"><?php _e( 'Low', 'dedebtify' ); ?></option>
                </select>
            </div>

            <div class="dedebtify-filter-group">
                <label for="sort-goals-by"><?php _e( 'Sort by:', 'dedebtify' ); ?></label>
                <select id="sort-goals-by" class="dedebtify-form-select">
                    <option value="progress"><?php _e( 'Progress', 'dedebtify' ); ?></option>
                    <option value="target-high"><?php _e( 'Target (High to Low)', 'dedebtify' ); ?></option>
                    <option value="target-low"><?php _e( 'Target (Low to High)', 'dedebtify' ); ?></option>
                    <option value="priority"><?php _e( 'Priority', 'dedebtify' ); ?></option>
                </select>
            </div>
        </div>

        <div id="dedebtify-goals-list" class="dedebtify-items-list">
            <div class="dedebtify-loading">
                <div class="dedebtify-spinner"></div>
                <p><?php _e( 'Loading goals...', 'dedebtify' ); ?></p>
            </div>
        </div>

        <div class="dedebtify-empty-state" id="goals-empty-state" style="display: none;">
            <h3><?php _e( 'No Goals Found', 'dedebtify' ); ?></h3>
            <p><?php _e( 'Set your first financial goal to start tracking your progress towards financial freedom.', 'dedebtify' ); ?></p>
            <a href="?action=add" class="dedebtify-btn dedebtify-btn-success"><?php _e( '+ Add Your First Goal', 'dedebtify' ); ?></a>
        </div>
    <?php endif; ?>

</div>
