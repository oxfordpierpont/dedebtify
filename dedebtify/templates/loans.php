<?php
/**
 * Loans Manager Template
 *
 * This template displays and manages user's loans.
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
    echo '<p>' . __( 'Please log in to manage your loans.', 'dedebtify' ) . '</p>';
    return;
}

$user_id = get_current_user_id();
$edit_id = isset( $_GET['edit'] ) ? intval( $_GET['edit'] ) : 0;
$action = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : 'list';
?>

<!-- Navigation -->
<?php Dedebtify_Helpers::render_navigation( 'loans' ); ?>

<div class="dedebtify-dashboard dedebtify-loans-manager">

    <div class="dedebtify-dashboard-header">
        <h1><?php _e( 'Loan Manager', 'dedebtify' ); ?></h1>
        <p><?php _e( 'Track personal loans, auto loans, student loans, and more', 'dedebtify' ); ?></p>
    </div>

    <?php if ( $action === 'add' || $action === 'edit' ) : ?>
        <!-- Add/Edit Form -->
        <div class="dedebtify-form-container">
            <div class="dedebtify-form-header">
                <h2><?php echo $action === 'edit' ? __( 'Edit Loan', 'dedebtify' ) : __( 'Add New Loan', 'dedebtify' ); ?></h2>
                <a href="?action=list" class="dedebtify-btn dedebtify-btn-secondary"><?php _e( 'Back to List', 'dedebtify' ); ?></a>
            </div>

            <form id="dedebtify-loan-form" class="dedebtify-form" data-post-id="<?php echo $edit_id; ?>">
                <?php wp_nonce_field( 'dedebtify_loan_form', 'dedebtify_nonce' ); ?>

                <div class="dedebtify-form-row">
                    <div class="dedebtify-form-group">
                        <label for="loan_name" class="dedebtify-form-label"><?php _e( 'Loan Name', 'dedebtify' ); ?> <span class="required">*</span></label>
                        <input type="text" id="loan_name" name="loan_name" class="dedebtify-form-input" required placeholder="e.g., Auto Loan - Honda Civic">
                        <span class="dedebtify-form-help"><?php _e( 'Enter a descriptive name for this loan', 'dedebtify' ); ?></span>
                    </div>
                </div>

                <div class="dedebtify-form-row dedebtify-form-row-2col">
                    <div class="dedebtify-form-group">
                        <label for="loan_type" class="dedebtify-form-label"><?php _e( 'Loan Type', 'dedebtify' ); ?> <span class="required">*</span></label>
                        <select id="loan_type" name="loan_type" class="dedebtify-form-select" required>
                            <option value=""><?php _e( 'Select Type', 'dedebtify' ); ?></option>
                            <option value="personal"><?php _e( 'Personal Loan', 'dedebtify' ); ?></option>
                            <option value="auto"><?php _e( 'Auto Loan', 'dedebtify' ); ?></option>
                            <option value="student"><?php _e( 'Student Loan', 'dedebtify' ); ?></option>
                            <option value="other"><?php _e( 'Other', 'dedebtify' ); ?></option>
                        </select>
                    </div>

                    <div class="dedebtify-form-group">
                        <label for="start_date" class="dedebtify-form-label"><?php _e( 'Start Date', 'dedebtify' ); ?> <span class="required">*</span></label>
                        <input type="date" id="start_date" name="start_date" class="dedebtify-form-input" required>
                    </div>
                </div>

                <div class="dedebtify-form-row dedebtify-form-row-2col">
                    <div class="dedebtify-form-group">
                        <label for="principal" class="dedebtify-form-label"><?php _e( 'Original Loan Amount ($)', 'dedebtify' ); ?> <span class="required">*</span></label>
                        <input type="number" step="0.01" id="principal" name="principal" class="dedebtify-form-input" required placeholder="25000.00">
                    </div>

                    <div class="dedebtify-form-group">
                        <label for="current_balance" class="dedebtify-form-label"><?php _e( 'Current Balance ($)', 'dedebtify' ); ?> <span class="required">*</span></label>
                        <input type="number" step="0.01" id="current_balance" name="current_balance" class="dedebtify-form-input" required placeholder="18500.00">
                    </div>
                </div>

                <div class="dedebtify-form-row dedebtify-form-row-2col">
                    <div class="dedebtify-form-group">
                        <label for="interest_rate" class="dedebtify-form-label"><?php _e( 'Interest Rate (APR %)', 'dedebtify' ); ?> <span class="required">*</span></label>
                        <input type="number" step="0.01" id="interest_rate" name="interest_rate" class="dedebtify-form-input" required placeholder="5.75">
                    </div>

                    <div class="dedebtify-form-group">
                        <label for="term_months" class="dedebtify-form-label"><?php _e( 'Original Term (Months)', 'dedebtify' ); ?> <span class="required">*</span></label>
                        <input type="number" id="term_months" name="term_months" class="dedebtify-form-input" required placeholder="60">
                    </div>
                </div>

                <div class="dedebtify-form-row dedebtify-form-row-2col">
                    <div class="dedebtify-form-group">
                        <label for="monthly_payment" class="dedebtify-form-label"><?php _e( 'Monthly Payment ($)', 'dedebtify' ); ?> <span class="required">*</span></label>
                        <input type="number" step="0.01" id="monthly_payment" name="monthly_payment" class="dedebtify-form-input" required placeholder="480.00">
                        <button type="button" id="calculate-loan-payment" class="dedebtify-btn dedebtify-btn-small dedebtify-btn-secondary" style="margin-top: 10px;">
                            <?php _e( 'Auto-Calculate', 'dedebtify' ); ?>
                        </button>
                    </div>

                    <div class="dedebtify-form-group">
                        <label for="extra_payment" class="dedebtify-form-label"><?php _e( 'Extra Payment ($)', 'dedebtify' ); ?></label>
                        <input type="number" step="0.01" id="extra_payment" name="extra_payment" class="dedebtify-form-input" placeholder="100.00">
                        <span class="dedebtify-form-help"><?php _e( 'Additional amount you plan to pay each month', 'dedebtify' ); ?></span>
                    </div>
                </div>

                <!-- Payoff Projection -->
                <div id="dedebtify-loan-payoff-preview" class="dedebtify-payoff-preview" style="display: none;">
                    <h3><?php _e( 'Payoff Projection', 'dedebtify' ); ?></h3>
                    <div class="dedebtify-stats-grid">
                        <div class="dedebtify-stat-card">
                            <div class="dedebtify-stat-label"><?php _e( 'Months Remaining', 'dedebtify' ); ?></div>
                            <div class="dedebtify-stat-value" id="preview-months">0</div>
                        </div>
                        <div class="dedebtify-stat-card">
                            <div class="dedebtify-stat-label"><?php _e( 'Payoff Date', 'dedebtify' ); ?></div>
                            <div class="dedebtify-stat-value" id="preview-date" style="font-size: 1.2rem;">—</div>
                        </div>
                        <div class="dedebtify-stat-card">
                            <div class="dedebtify-stat-label"><?php _e( 'Total Interest', 'dedebtify' ); ?></div>
                            <div class="dedebtify-stat-value" id="preview-interest">$0</div>
                        </div>
                        <div class="dedebtify-stat-card">
                            <div class="dedebtify-stat-label"><?php _e( 'Total Paid', 'dedebtify' ); ?></div>
                            <div class="dedebtify-stat-value" id="preview-total">$0</div>
                        </div>
                    </div>
                </div>

                <div class="dedebtify-form-actions">
                    <button type="submit" class="dedebtify-btn dedebtify-btn-success">
                        <?php echo $action === 'edit' ? __( 'Update Loan', 'dedebtify' ) : __( 'Add Loan', 'dedebtify' ); ?>
                    </button>
                    <a href="?action=list" class="dedebtify-btn dedebtify-btn-secondary"><?php _e( 'Cancel', 'dedebtify' ); ?></a>
                </div>
            </form>
        </div>

    <?php else : ?>
        <!-- List View -->
        <div class="dedebtify-manager-header">
            <div class="dedebtify-manager-stats" id="dedebtify-loan-stats">
                <div class="dedebtify-stat-summary">
                    <span class="stat-label"><?php _e( 'Total Loan Debt:', 'dedebtify' ); ?></span>
                    <span class="stat-value" id="loan-total-debt">$0.00</span>
                </div>
                <div class="dedebtify-stat-summary">
                    <span class="stat-label"><?php _e( 'Monthly Payments:', 'dedebtify' ); ?></span>
                    <span class="stat-value" id="loan-monthly-payment">$0.00</span>
                </div>
                <div class="dedebtify-stat-summary">
                    <span class="stat-label"><?php _e( 'Active Loans:', 'dedebtify' ); ?></span>
                    <span class="stat-value" id="loan-count">0</span>
                </div>
            </div>
            <a href="?action=add" class="dedebtify-btn dedebtify-btn-success"><?php _e( '+ Add Loan', 'dedebtify' ); ?></a>
        </div>

        <div class="dedebtify-manager-controls">
            <div class="dedebtify-filter-group">
                <label for="filter-loan-type"><?php _e( 'Type:', 'dedebtify' ); ?></label>
                <select id="filter-loan-type" class="dedebtify-form-select">
                    <option value="all"><?php _e( 'All Types', 'dedebtify' ); ?></option>
                    <option value="personal"><?php _e( 'Personal', 'dedebtify' ); ?></option>
                    <option value="auto"><?php _e( 'Auto', 'dedebtify' ); ?></option>
                    <option value="student"><?php _e( 'Student', 'dedebtify' ); ?></option>
                    <option value="other"><?php _e( 'Other', 'dedebtify' ); ?></option>
                </select>
            </div>

            <div class="dedebtify-filter-group">
                <label for="sort-loans-by"><?php _e( 'Sort by:', 'dedebtify' ); ?></label>
                <select id="sort-loans-by" class="dedebtify-form-select">
                    <option value="balance-high"><?php _e( 'Balance (High to Low)', 'dedebtify' ); ?></option>
                    <option value="balance-low"><?php _e( 'Balance (Low to High)', 'dedebtify' ); ?></option>
                    <option value="rate-high"><?php _e( 'Interest Rate (High to Low)', 'dedebtify' ); ?></option>
                    <option value="rate-low"><?php _e( 'Interest Rate (Low to High)', 'dedebtify' ); ?></option>
                </select>
            </div>
        </div>

        <div id="dedebtify-loans-list" class="dedebtify-items-list">
            <div class="dedebtify-loading">
                <div class="dedebtify-spinner"></div>
                <p><?php _e( 'Loading loans...', 'dedebtify' ); ?></p>
            </div>
        </div>

        <div class="dedebtify-empty-state" id="loans-empty-state" style="display: none;">
            <h3><?php _e( 'No Loans Found', 'dedebtify' ); ?></h3>
            <p><?php _e( 'Add your first loan to start tracking your debt and see payoff projections.', 'dedebtify' ); ?></p>
            <a href="?action=add" class="dedebtify-btn dedebtify-btn-success"><?php _e( '+ Add Your First Loan', 'dedebtify' ); ?></a>
        </div>
    <?php endif; ?>

</div>
