<?php
/**
 * Debt Action Plan Template
 *
 * This template displays a comprehensive debt payoff action plan.
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
    echo '<p>' . __( 'Please log in to view your debt action plan.', 'dedebtify' ) . '</p>';
    return;
}

$user_id = get_current_user_id();
?>

<!-- Navigation -->
<?php Dedebtify_Helpers::render_navigation( 'action_plan' ); ?>

<div class="dedebtify-dashboard dedebtify-action-plan">

    <div class="dedebtify-dashboard-header">
        <h1><?php _e( 'Debt Action Plan', 'dedebtify' ); ?></h1>
        <p><?php _e( 'Create a strategic plan to pay off your debts efficiently', 'dedebtify' ); ?></p>
    </div>

    <!-- Strategy Selection -->
    <div class="dedebtify-form-container">
        <div class="dedebtify-form-header">
            <h2><?php _e( 'Configure Your Plan', 'dedebtify' ); ?></h2>
        </div>

        <form id="dedebtify-action-plan-form" class="dedebtify-form">
            <div class="dedebtify-form-row dedebtify-form-row-2col">
                <div class="dedebtify-form-group">
                    <label for="payoff_strategy" class="dedebtify-form-label"><?php _e( 'Payoff Strategy', 'dedebtify' ); ?> <span class="required">*</span></label>
                    <select id="payoff_strategy" name="payoff_strategy" class="dedebtify-form-select" required>
                        <option value="avalanche"><?php _e( 'Avalanche (Highest Interest First)', 'dedebtify' ); ?></option>
                        <option value="snowball"><?php _e( 'Snowball (Lowest Balance First)', 'dedebtify' ); ?></option>
                    </select>
                    <span class="dedebtify-form-help" id="strategy-help">
                        <?php _e( 'Avalanche saves the most on interest. Snowball provides quick wins.', 'dedebtify' ); ?>
                    </span>
                </div>

                <div class="dedebtify-form-group">
                    <label for="extra_payment" class="dedebtify-form-label"><?php _e( 'Extra Monthly Payment ($)', 'dedebtify' ); ?></label>
                    <input type="number" step="0.01" id="extra_payment" name="extra_payment" class="dedebtify-form-input" value="0" placeholder="0.00">
                    <span class="dedebtify-form-help"><?php _e( 'Additional amount you can apply to debt each month', 'dedebtify' ); ?></span>
                </div>
            </div>

            <div class="dedebtify-form-actions">
                <button type="submit" class="dedebtify-btn dedebtify-btn-success">
                    <?php _e( 'Generate Action Plan', 'dedebtify' ); ?>
                </button>
            </div>
        </form>
    </div>

    <!-- Plan Summary -->
    <div id="dedebtify-plan-summary" class="dedebtify-section" style="display: none;">
        <h2><?php _e( 'Plan Summary', 'dedebtify' ); ?></h2>

        <div class="dedebtify-stats-grid">
            <div class="dedebtify-stat-card">
                <div class="dedebtify-stat-label"><?php _e( 'Total Debt', 'dedebtify' ); ?></div>
                <div class="dedebtify-stat-value" id="plan-total-debt">$0</div>
            </div>
            <div class="dedebtify-stat-card">
                <div class="dedebtify-stat-label"><?php _e( 'Total Interest', 'dedebtify' ); ?></div>
                <div class="dedebtify-stat-value" id="plan-total-interest">$0</div>
            </div>
            <div class="dedebtify-stat-card">
                <div class="dedebtify-stat-label"><?php _e( 'Time to Debt Freedom', 'dedebtify' ); ?></div>
                <div class="dedebtify-stat-value" id="plan-time-to-freedom">0 months</div>
            </div>
            <div class="dedebtify-stat-card">
                <div class="dedebtify-stat-label"><?php _e( 'Debt-Free Date', 'dedebtify' ); ?></div>
                <div class="dedebtify-stat-value" id="plan-freedom-date" style="font-size: 1.2rem;">—</div>
            </div>
        </div>

        <!-- Strategy Comparison -->
        <div class="dedebtify-comparison-card">
            <h3><?php _e( 'Strategy Comparison', 'dedebtify' ); ?></h3>
            <div class="dedebtify-comparison-grid">
                <div class="dedebtify-comparison-item">
                    <strong><?php _e( 'Avalanche Method', 'dedebtify' ); ?></strong>
                    <p id="avalanche-summary"><?php _e( 'Calculating...', 'dedebtify' ); ?></p>
                </div>
                <div class="dedebtify-comparison-item">
                    <strong><?php _e( 'Snowball Method', 'dedebtify' ); ?></strong>
                    <p id="snowball-summary"><?php _e( 'Calculating...', 'dedebtify' ); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Payoff Timeline -->
    <div id="dedebtify-payoff-timeline" class="dedebtify-section" style="display: none;">
        <h2><?php _e( 'Payoff Timeline', 'dedebtify' ); ?></h2>
        <p class="dedebtify-section-description"><?php _e( 'Follow this order to maximize your debt payoff efficiency', 'dedebtify' ); ?></p>

        <div id="dedebtify-timeline-items" class="dedebtify-timeline-list">
            <!-- Timeline items will be inserted here by JavaScript -->
        </div>
    </div>

    <!-- Monthly Payment Schedule -->
    <div id="dedebtify-payment-schedule" class="dedebtify-section" style="display: none;">
        <div class="dedebtify-section-header">
            <h2><?php _e( 'Monthly Payment Schedule', 'dedebtify' ); ?></h2>
            <button id="toggle-schedule" class="dedebtify-btn dedebtify-btn-secondary dedebtify-btn-small">
                <?php _e( 'Show Details', 'dedebtify' ); ?>
            </button>
        </div>

        <div id="dedebtify-schedule-details" style="display: none;">
            <div class="dedebtify-table-container">
                <table class="dedebtify-table" id="payment-schedule-table">
                    <thead>
                        <tr>
                            <th><?php _e( 'Month', 'dedebtify' ); ?></th>
                            <th><?php _e( 'Debt', 'dedebtify' ); ?></th>
                            <th><?php _e( 'Payment', 'dedebtify' ); ?></th>
                            <th><?php _e( 'Principal', 'dedebtify' ); ?></th>
                            <th><?php _e( 'Interest', 'dedebtify' ); ?></th>
                            <th><?php _e( 'Remaining', 'dedebtify' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Schedule rows will be inserted here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Action Items -->
    <div id="dedebtify-action-items" class="dedebtify-section" style="display: none;">
        <h2><?php _e( 'Next Steps', 'dedebtify' ); ?></h2>
        <div class="dedebtify-action-list">
            <div class="dedebtify-action-item">
                <span class="dedebtify-action-number">1</span>
                <div class="dedebtify-action-content">
                    <h4><?php _e( 'Make Minimum Payments', 'dedebtify' ); ?></h4>
                    <p><?php _e( 'Continue making minimum payments on all debts to avoid penalties and maintain good credit.', 'dedebtify' ); ?></p>
                </div>
            </div>
            <div class="dedebtify-action-item">
                <span class="dedebtify-action-number">2</span>
                <div class="dedebtify-action-content">
                    <h4><?php _e( 'Focus Extra Payments', 'dedebtify' ); ?></h4>
                    <p id="action-focus-text"><?php _e( 'Apply all extra payments to your target debt according to your chosen strategy.', 'dedebtify' ); ?></p>
                </div>
            </div>
            <div class="dedebtify-action-item">
                <span class="dedebtify-action-number">3</span>
                <div class="dedebtify-action-content">
                    <h4><?php _e( 'Roll Over Payments', 'dedebtify' ); ?></h4>
                    <p><?php _e( 'When you pay off a debt, add that payment amount to the next debt in your plan.', 'dedebtify' ); ?></p>
                </div>
            </div>
            <div class="dedebtify-action-item">
                <span class="dedebtify-action-number">4</span>
                <div class="dedebtify-action-content">
                    <h4><?php _e( 'Track Your Progress', 'dedebtify' ); ?></h4>
                    <p><?php _e( 'Update your balances monthly and celebrate each milestone along the way!', 'dedebtify' ); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Print and Export -->
    <div id="dedebtify-plan-actions" class="dedebtify-section" style="display: none;">
        <div class="dedebtify-form-actions">
            <button id="print-plan" class="dedebtify-btn dedebtify-btn-secondary">
                <?php _e( 'Print Plan', 'dedebtify' ); ?>
            </button>
            <button id="regenerate-plan" class="dedebtify-btn dedebtify-btn-secondary">
                <?php _e( 'Regenerate Plan', 'dedebtify' ); ?>
            </button>
        </div>
    </div>

    <!-- Loading State -->
    <div id="plan-loading" class="dedebtify-loading" style="display: none;">
        <div class="dedebtify-spinner"></div>
        <p><?php _e( 'Generating your action plan...', 'dedebtify' ); ?></p>
    </div>

    <!-- Empty State -->
    <div class="dedebtify-empty-state" id="plan-empty-state" style="display: none;">
        <h3><?php _e( 'No Debts Found', 'dedebtify' ); ?></h3>
        <p><?php _e( 'You need to add credit cards or loans before generating an action plan.', 'dedebtify' ); ?></p>
        <a href="?page=credit-cards&action=add" class="dedebtify-btn dedebtify-btn-success"><?php _e( 'Add Credit Card', 'dedebtify' ); ?></a>
        <a href="?page=loans&action=add" class="dedebtify-btn dedebtify-btn-secondary"><?php _e( 'Add Loan', 'dedebtify' ); ?></a>
    </div>

</div>
