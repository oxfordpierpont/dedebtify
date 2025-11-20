<?php
/**
 * Mortgages Manager Template
 *
 * This template displays and manages user's mortgage.
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
    echo '<p>' . __( 'Please log in to manage your mortgage.', 'dedebtify' ) . '</p>';
    return;
}

$user_id = get_current_user_id();
$edit_id = isset( $_GET['edit'] ) ? intval( $_GET['edit'] ) : 0;
$action = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : 'list';
?>

<div class="dedebtify-dashboard dedebtify-mortgages-manager">

    <div class="dedebtify-dashboard-header">
        <h1><?php _e( 'Mortgage Manager', 'dedebtify' ); ?></h1>
        <p><?php _e( 'Track your home mortgage with detailed payment projections', 'dedebtify' ); ?></p>
    </div>

    <?php if ( $action === 'add' || $action === 'edit' ) : ?>
        <!-- Add/Edit Form -->
        <div class="dedebtify-form-container">
            <div class="dedebtify-form-header">
                <h2><?php echo $action === 'edit' ? __( 'Edit Mortgage', 'dedebtify' ) : __( 'Add Mortgage', 'dedebtify' ); ?></h2>
                <a href="?action=list" class="dedebtify-btn dedebtify-btn-secondary"><?php _e( 'Back to List', 'dedebtify' ); ?></a>
            </div>

            <form id="dedebtify-mortgage-form" class="dedebtify-form" data-post-id="<?php echo $edit_id; ?>">
                <?php wp_nonce_field( 'dedebtify_mortgage_form', 'dedebtify_nonce' ); ?>

                <div class="dedebtify-form-row">
                    <div class="dedebtify-form-group">
                        <label for="mortgage_name" class="dedebtify-form-label"><?php _e( 'Property Name', 'dedebtify' ); ?> <span class="required">*</span></label>
                        <input type="text" id="mortgage_name" name="mortgage_name" class="dedebtify-form-input" required placeholder="e.g., Primary Residence">
                        <span class="dedebtify-form-help"><?php _e( 'Enter a descriptive name for this mortgage', 'dedebtify' ); ?></span>
                    </div>
                </div>

                <div class="dedebtify-form-row">
                    <div class="dedebtify-form-group">
                        <label for="property_address" class="dedebtify-form-label"><?php _e( 'Property Address', 'dedebtify' ); ?></label>
                        <input type="text" id="property_address" name="property_address" class="dedebtify-form-input" placeholder="123 Main Street, City, State 12345">
                    </div>
                </div>

                <div class="dedebtify-form-row dedebtify-form-row-2col">
                    <div class="dedebtify-form-group">
                        <label for="loan_amount" class="dedebtify-form-label"><?php _e( 'Original Loan Amount ($)', 'dedebtify' ); ?> <span class="required">*</span></label>
                        <input type="number" step="0.01" id="loan_amount" name="loan_amount" class="dedebtify-form-input" required placeholder="300000.00">
                    </div>

                    <div class="dedebtify-form-group">
                        <label for="current_balance" class="dedebtify-form-label"><?php _e( 'Current Balance ($)', 'dedebtify' ); ?> <span class="required">*</span></label>
                        <input type="number" step="0.01" id="current_balance" name="current_balance" class="dedebtify-form-input" required placeholder="285000.00">
                    </div>
                </div>

                <div class="dedebtify-form-row dedebtify-form-row-2col">
                    <div class="dedebtify-form-group">
                        <label for="interest_rate" class="dedebtify-form-label"><?php _e( 'Interest Rate (APR %)', 'dedebtify' ); ?> <span class="required">*</span></label>
                        <input type="number" step="0.01" id="interest_rate" name="interest_rate" class="dedebtify-form-input" required placeholder="3.75">
                    </div>

                    <div class="dedebtify-form-group">
                        <label for="term_years" class="dedebtify-form-label"><?php _e( 'Term (Years)', 'dedebtify' ); ?> <span class="required">*</span></label>
                        <select id="term_years" name="term_years" class="dedebtify-form-select" required>
                            <option value=""><?php _e( 'Select Term', 'dedebtify' ); ?></option>
                            <option value="15">15 <?php _e( 'Years', 'dedebtify' ); ?></option>
                            <option value="20">20 <?php _e( 'Years', 'dedebtify' ); ?></option>
                            <option value="30">30 <?php _e( 'Years', 'dedebtify' ); ?></option>
                        </select>
                    </div>
                </div>

                <div class="dedebtify-form-row dedebtify-form-row-2col">
                    <div class="dedebtify-form-group">
                        <label for="start_date" class="dedebtify-form-label"><?php _e( 'Start Date', 'dedebtify' ); ?> <span class="required">*</span></label>
                        <input type="date" id="start_date" name="start_date" class="dedebtify-form-input" required>
                    </div>

                    <div class="dedebtify-form-group">
                        <label for="monthly_payment" class="dedebtify-form-label"><?php _e( 'Monthly P&I Payment ($)', 'dedebtify' ); ?> <span class="required">*</span></label>
                        <input type="number" step="0.01" id="monthly_payment" name="monthly_payment" class="dedebtify-form-input" required placeholder="1350.00">
                        <button type="button" id="calculate-mortgage-payment" class="dedebtify-btn dedebtify-btn-small dedebtify-btn-secondary" style="margin-top: 10px;">
                            <?php _e( 'Auto-Calculate', 'dedebtify' ); ?>
                        </button>
                    </div>
                </div>

                <div class="dedebtify-form-row dedebtify-form-row-2col">
                    <div class="dedebtify-form-group">
                        <label for="extra_payment" class="dedebtify-form-label"><?php _e( 'Extra Principal Payment ($)', 'dedebtify' ); ?></label>
                        <input type="number" step="0.01" id="extra_payment" name="extra_payment" class="dedebtify-form-input" placeholder="100.00">
                        <span class="dedebtify-form-help"><?php _e( 'Additional amount towards principal each month', 'dedebtify' ); ?></span>
                    </div>

                    <div class="dedebtify-form-group">
                        <label for="property_tax" class="dedebtify-form-label"><?php _e( 'Annual Property Tax ($)', 'dedebtify' ); ?></label>
                        <input type="number" step="0.01" id="property_tax" name="property_tax" class="dedebtify-form-input" placeholder="3600.00">
                        <span class="dedebtify-form-help"><?php _e( 'Estimated annual property taxes', 'dedebtify' ); ?></span>
                    </div>
                </div>

                <div class="dedebtify-form-row dedebtify-form-row-2col">
                    <div class="dedebtify-form-group">
                        <label for="homeowners_insurance" class="dedebtify-form-label"><?php _e( 'Annual Home Insurance ($)', 'dedebtify' ); ?></label>
                        <input type="number" step="0.01" id="homeowners_insurance" name="homeowners_insurance" class="dedebtify-form-input" placeholder="1200.00">
                        <span class="dedebtify-form-help"><?php _e( 'Annual homeowners insurance premium', 'dedebtify' ); ?></span>
                    </div>

                    <div class="dedebtify-form-group">
                        <label for="pmi" class="dedebtify-form-label"><?php _e( 'Monthly PMI ($)', 'dedebtify' ); ?></label>
                        <input type="number" step="0.01" id="pmi" name="pmi" class="dedebtify-form-input" placeholder="75.00">
                        <span class="dedebtify-form-help"><?php _e( 'Private Mortgage Insurance (if applicable)', 'dedebtify' ); ?></span>
                    </div>
                </div>

                <!-- Payoff Projection -->
                <div id="dedebtify-mortgage-payoff-preview" class="dedebtify-payoff-preview" style="display: none;">
                    <h3><?php _e( 'Mortgage Summary', 'dedebtify' ); ?></h3>
                    <div class="dedebtify-stats-grid">
                        <div class="dedebtify-stat-card">
                            <div class="dedebtify-stat-label"><?php _e( 'Total Monthly Payment', 'dedebtify' ); ?></div>
                            <div class="dedebtify-stat-value" id="preview-total-payment">$0</div>
                            <div class="dedebtify-stat-subtext"><?php _e( 'P&I + Taxes + Insurance + PMI', 'dedebtify' ); ?></div>
                        </div>
                        <div class="dedebtify-stat-card">
                            <div class="dedebtify-stat-label"><?php _e( 'Years Remaining', 'dedebtify' ); ?></div>
                            <div class="dedebtify-stat-value" id="preview-years">0</div>
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
                        <?php echo $action === 'edit' ? __( 'Update Mortgage', 'dedebtify' ) : __( 'Add Mortgage', 'dedebtify' ); ?>
                    </button>
                    <a href="?action=list" class="dedebtify-btn dedebtify-btn-secondary"><?php _e( 'Cancel', 'dedebtify' ); ?></a>
                </div>
            </form>
        </div>

    <?php else : ?>
        <!-- List View -->
        <div class="dedebtify-manager-header">
            <div class="dedebtify-manager-stats" id="dedebtify-mortgage-stats">
                <div class="dedebtify-stat-summary">
                    <span class="stat-label"><?php _e( 'Mortgage Balance:', 'dedebtify' ); ?></span>
                    <span class="stat-value" id="mortgage-total-debt">$0.00</span>
                </div>
                <div class="dedebtify-stat-summary">
                    <span class="stat-label"><?php _e( 'Monthly Payment:', 'dedebtify' ); ?></span>
                    <span class="stat-value" id="mortgage-monthly-payment">$0.00</span>
                </div>
            </div>
            <a href="?action=add" class="dedebtify-btn dedebtify-btn-success"><?php _e( '+ Add Mortgage', 'dedebtify' ); ?></a>
        </div>

        <div id="dedebtify-mortgages-list" class="dedebtify-items-list">
            <div class="dedebtify-loading">
                <div class="dedebtify-spinner"></div>
                <p><?php _e( 'Loading mortgage...', 'dedebtify' ); ?></p>
            </div>
        </div>

        <div class="dedebtify-empty-state" id="mortgages-empty-state" style="display: none;">
            <h3><?php _e( 'No Mortgage Found', 'dedebtify' ); ?></h3>
            <p><?php _e( 'Add your mortgage to track your home loan and see detailed payoff projections.', 'dedebtify' ); ?></p>
            <a href="?action=add" class="dedebtify-btn dedebtify-btn-success"><?php _e( '+ Add Your Mortgage', 'dedebtify' ); ?></a>
        </div>
    <?php endif; ?>

</div>
