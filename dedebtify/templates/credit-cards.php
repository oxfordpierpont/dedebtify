<?php
/**
 * Credit Cards Manager Template
 *
 * This template displays and manages user's credit cards.
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
    echo '<p>' . __( 'Please log in to manage your credit cards.', 'dedebtify' ) . '</p>';
    return;
}

$user_id = get_current_user_id();
$edit_id = isset( $_GET['edit'] ) ? intval( $_GET['edit'] ) : 0;
$action = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : 'list';
?>

<div class="dedebtify-dashboard dedebtify-credit-cards-manager">

    <div class="dedebtify-dashboard-header">
        <h1><?php _e( 'Credit Card Manager', 'dedebtify' ); ?></h1>
        <p><?php _e( 'Track and manage your credit cards with payoff projections', 'dedebtify' ); ?></p>
    </div>

    <?php if ( $action === 'add' || $action === 'edit' ) : ?>
        <!-- Add/Edit Form -->
        <div class="dedebtify-form-container">
            <div class="dedebtify-form-header">
                <h2><?php echo $action === 'edit' ? __( 'Edit Credit Card', 'dedebtify' ) : __( 'Add New Credit Card', 'dedebtify' ); ?></h2>
                <a href="?action=list" class="dedebtify-btn dedebtify-btn-secondary"><?php _e( 'Back to List', 'dedebtify' ); ?></a>
            </div>

            <form id="dedebtify-credit-card-form" class="dedebtify-form" data-post-id="<?php echo $edit_id; ?>">
                <?php wp_nonce_field( 'dedebtify_credit_card_form', 'dedebtify_nonce' ); ?>

                <div class="dedebtify-form-row">
                    <div class="dedebtify-form-group">
                        <label for="card_name" class="dedebtify-form-label"><?php _e( 'Card Name', 'dedebtify' ); ?> <span class="required">*</span></label>
                        <input type="text" id="card_name" name="card_name" class="dedebtify-form-input" required placeholder="e.g., Chase Freedom">
                        <span class="dedebtify-form-help"><?php _e( 'Enter a name to identify this card', 'dedebtify' ); ?></span>
                    </div>
                </div>

                <div class="dedebtify-form-row dedebtify-form-row-2col">
                    <div class="dedebtify-form-group">
                        <label for="balance" class="dedebtify-form-label"><?php _e( 'Current Balance ($)', 'dedebtify' ); ?> <span class="required">*</span></label>
                        <input type="number" step="0.01" id="balance" name="balance" class="dedebtify-form-input" required placeholder="3500.00">
                    </div>

                    <div class="dedebtify-form-group">
                        <label for="credit_limit" class="dedebtify-form-label"><?php _e( 'Credit Limit ($)', 'dedebtify' ); ?> <span class="required">*</span></label>
                        <input type="number" step="0.01" id="credit_limit" name="credit_limit" class="dedebtify-form-input" required placeholder="5000.00">
                    </div>
                </div>

                <div class="dedebtify-form-row dedebtify-form-row-2col">
                    <div class="dedebtify-form-group">
                        <label for="interest_rate" class="dedebtify-form-label"><?php _e( 'Interest Rate (APR %)', 'dedebtify' ); ?> <span class="required">*</span></label>
                        <input type="number" step="0.01" id="interest_rate" name="interest_rate" class="dedebtify-form-input" required placeholder="18.99">
                    </div>

                    <div class="dedebtify-form-group">
                        <label for="minimum_payment" class="dedebtify-form-label"><?php _e( 'Minimum Payment ($)', 'dedebtify' ); ?> <span class="required">*</span></label>
                        <input type="number" step="0.01" id="minimum_payment" name="minimum_payment" class="dedebtify-form-input" required placeholder="75.00">
                    </div>
                </div>

                <div class="dedebtify-form-row dedebtify-form-row-2col">
                    <div class="dedebtify-form-group">
                        <label for="extra_payment" class="dedebtify-form-label"><?php _e( 'Extra Payment ($)', 'dedebtify' ); ?></label>
                        <input type="number" step="0.01" id="extra_payment" name="extra_payment" class="dedebtify-form-input" placeholder="200.00">
                        <span class="dedebtify-form-help"><?php _e( 'Additional amount you plan to pay each month', 'dedebtify' ); ?></span>
                    </div>

                    <div class="dedebtify-form-group">
                        <label for="due_date" class="dedebtify-form-label"><?php _e( 'Due Date (Day of Month)', 'dedebtify' ); ?></label>
                        <input type="number" min="1" max="31" id="due_date" name="due_date" class="dedebtify-form-input" placeholder="15">
                    </div>
                </div>

                <div class="dedebtify-form-row dedebtify-form-row-2col">
                    <div class="dedebtify-form-group">
                        <label for="status" class="dedebtify-form-label"><?php _e( 'Status', 'dedebtify' ); ?></label>
                        <select id="status" name="status" class="dedebtify-form-select">
                            <option value="active"><?php _e( 'Active', 'dedebtify' ); ?></option>
                            <option value="paid_off"><?php _e( 'Paid Off', 'dedebtify' ); ?></option>
                            <option value="closed"><?php _e( 'Closed', 'dedebtify' ); ?></option>
                        </select>
                    </div>

                    <div class="dedebtify-form-group">
                        <label for="auto_pay" class="dedebtify-form-label">
                            <input type="checkbox" id="auto_pay" name="auto_pay" value="1">
                            <?php _e( 'Auto-Pay Enabled', 'dedebtify' ); ?>
                        </label>
                    </div>
                </div>

                <!-- Payoff Projection (shown after entering data) -->
                <div id="dedebtify-payoff-preview" class="dedebtify-payoff-preview" style="display: none;">
                    <h3><?php _e( 'Payoff Projection', 'dedebtify' ); ?></h3>
                    <div class="dedebtify-stats-grid">
                        <div class="dedebtify-stat-card">
                            <div class="dedebtify-stat-label"><?php _e( 'Utilization', 'dedebtify' ); ?></div>
                            <div class="dedebtify-stat-value" id="preview-utilization">0%</div>
                        </div>
                        <div class="dedebtify-stat-card">
                            <div class="dedebtify-stat-label"><?php _e( 'Months to Payoff', 'dedebtify' ); ?></div>
                            <div class="dedebtify-stat-value" id="preview-months">0</div>
                        </div>
                        <div class="dedebtify-stat-card">
                            <div class="dedebtify-stat-label"><?php _e( 'Total Interest', 'dedebtify' ); ?></div>
                            <div class="dedebtify-stat-value" id="preview-interest">$0</div>
                        </div>
                        <div class="dedebtify-stat-card">
                            <div class="dedebtify-stat-label"><?php _e( 'Payoff Date', 'dedebtify' ); ?></div>
                            <div class="dedebtify-stat-value" id="preview-date" style="font-size: 1.2rem;">—</div>
                        </div>
                    </div>
                </div>

                <div class="dedebtify-form-actions">
                    <button type="submit" class="dedebtify-btn dedebtify-btn-success">
                        <?php echo $action === 'edit' ? __( 'Update Card', 'dedebtify' ) : __( 'Add Card', 'dedebtify' ); ?>
                    </button>
                    <a href="?action=list" class="dedebtify-btn dedebtify-btn-secondary"><?php _e( 'Cancel', 'dedebtify' ); ?></a>
                </div>
            </form>
        </div>

    <?php else : ?>
        <!-- List View -->
        <div class="dedebtify-manager-header">
            <div class="dedebtify-manager-stats" id="dedebtify-cc-stats">
                <div class="dedebtify-stat-summary">
                    <span class="stat-label"><?php _e( 'Total Credit Card Debt:', 'dedebtify' ); ?></span>
                    <span class="stat-value" id="cc-total-debt">$0.00</span>
                </div>
                <div class="dedebtify-stat-summary">
                    <span class="stat-label"><?php _e( 'Overall Utilization:', 'dedebtify' ); ?></span>
                    <span class="stat-value" id="cc-utilization">0%</span>
                </div>
                <div class="dedebtify-stat-summary">
                    <span class="stat-label"><?php _e( 'Monthly Payments:', 'dedebtify' ); ?></span>
                    <span class="stat-value" id="cc-monthly-payment">$0.00</span>
                </div>
            </div>
            <a href="?action=add" class="dedebtify-btn dedebtify-btn-success"><?php _e( '+ Add Credit Card', 'dedebtify' ); ?></a>
        </div>

        <div class="dedebtify-manager-controls">
            <div class="dedebtify-filter-group">
                <label for="sort-by"><?php _e( 'Sort by:', 'dedebtify' ); ?></label>
                <select id="sort-by" class="dedebtify-form-select">
                    <option value="balance-high"><?php _e( 'Balance (High to Low)', 'dedebtify' ); ?></option>
                    <option value="balance-low"><?php _e( 'Balance (Low to High)', 'dedebtify' ); ?></option>
                    <option value="rate-high"><?php _e( 'Interest Rate (High to Low)', 'dedebtify' ); ?></option>
                    <option value="rate-low"><?php _e( 'Interest Rate (Low to High)', 'dedebtify' ); ?></option>
                    <option value="utilization-high"><?php _e( 'Utilization (High to Low)', 'dedebtify' ); ?></option>
                </select>
            </div>

            <div class="dedebtify-filter-group">
                <label for="filter-status"><?php _e( 'Status:', 'dedebtify' ); ?></label>
                <select id="filter-status" class="dedebtify-form-select">
                    <option value="all"><?php _e( 'All', 'dedebtify' ); ?></option>
                    <option value="active"><?php _e( 'Active', 'dedebtify' ); ?></option>
                    <option value="paid_off"><?php _e( 'Paid Off', 'dedebtify' ); ?></option>
                    <option value="closed"><?php _e( 'Closed', 'dedebtify' ); ?></option>
                </select>
            </div>
        </div>

        <div id="dedebtify-credit-cards-list" class="dedebtify-items-list">
            <div class="dedebtify-loading">
                <div class="dedebtify-spinner"></div>
                <p><?php _e( 'Loading credit cards...', 'dedebtify' ); ?></p>
            </div>
        </div>

        <div class="dedebtify-empty-state" id="empty-state" style="display: none;">
            <h3><?php _e( 'No Credit Cards Found', 'dedebtify' ); ?></h3>
            <p><?php _e( 'Add your first credit card to start tracking your debt and see payoff projections.', 'dedebtify' ); ?></p>
            <a href="?action=add" class="dedebtify-btn dedebtify-btn-success"><?php _e( '+ Add Your First Credit Card', 'dedebtify' ); ?></a>
        </div>
    <?php endif; ?>

</div>
