<?php
/**
 * Bills Manager Template
 *
 * This template displays and manages user's recurring bills.
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
    echo '<p>' . __( 'Please log in to manage your bills.', 'dedebtify' ) . '</p>';
    return;
}

$user_id = get_current_user_id();
$edit_id = isset( $_GET['edit'] ) ? intval( $_GET['edit'] ) : 0;
$action = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : 'list';
?>

<!-- Navigation -->
<?php Dedebtify_Helpers::render_navigation( 'bills' ); ?>

<div class="dedebtify-dashboard dedebtify-bills-manager">

    <div class="dedebtify-dashboard-header">
        <h1><?php _e( 'Bills Manager', 'dedebtify' ); ?></h1>
        <p><?php _e( 'Track all your recurring monthly expenses and subscriptions', 'dedebtify' ); ?></p>
    </div>

    <?php if ( $action === 'add' || $action === 'edit' ) : ?>
        <!-- Add/Edit Form -->
        <div class="dedebtify-form-container">
            <div class="dedebtify-form-header">
                <h2><?php echo $action === 'edit' ? __( 'Edit Bill', 'dedebtify' ) : __( 'Add New Bill', 'dedebtify' ); ?></h2>
                <a href="?action=list" class="dedebtify-btn dedebtify-btn-secondary"><?php _e( 'Back to List', 'dedebtify' ); ?></a>
            </div>

            <form id="dedebtify-bill-form" class="dedebtify-form" data-post-id="<?php echo $edit_id; ?>">
                <?php wp_nonce_field( 'dedebtify_bill_form', 'dedebtify_nonce' ); ?>

                <div class="dedebtify-form-row">
                    <div class="dedebtify-form-group">
                        <label for="bill_name" class="dedebtify-form-label"><?php _e( 'Bill Name', 'dedebtify' ); ?> <span class="required">*</span></label>
                        <input type="text" id="bill_name" name="bill_name" class="dedebtify-form-input" required placeholder="e.g., Netflix Subscription">
                    </div>
                </div>

                <div class="dedebtify-form-row dedebtify-form-row-2col">
                    <div class="dedebtify-form-group">
                        <label for="category" class="dedebtify-form-label"><?php _e( 'Category', 'dedebtify' ); ?> <span class="required">*</span></label>
                        <select id="category" name="category" class="dedebtify-form-select" required>
                            <option value=""><?php _e( 'Select Category', 'dedebtify' ); ?></option>
                            <option value="housing"><?php _e( 'Housing', 'dedebtify' ); ?></option>
                            <option value="transportation"><?php _e( 'Transportation', 'dedebtify' ); ?></option>
                            <option value="utilities"><?php _e( 'Utilities', 'dedebtify' ); ?></option>
                            <option value="food"><?php _e( 'Food', 'dedebtify' ); ?></option>
                            <option value="healthcare"><?php _e( 'Healthcare', 'dedebtify' ); ?></option>
                            <option value="insurance"><?php _e( 'Insurance', 'dedebtify' ); ?></option>
                            <option value="entertainment"><?php _e( 'Entertainment', 'dedebtify' ); ?></option>
                            <option value="subscriptions"><?php _e( 'Subscriptions', 'dedebtify' ); ?></option>
                            <option value="other"><?php _e( 'Other', 'dedebtify' ); ?></option>
                        </select>
                    </div>

                    <div class="dedebtify-form-group">
                        <label for="amount" class="dedebtify-form-label"><?php _e( 'Amount ($)', 'dedebtify' ); ?> <span class="required">*</span></label>
                        <input type="number" step="0.01" id="amount" name="amount" class="dedebtify-form-input" required placeholder="15.99">
                    </div>
                </div>

                <div class="dedebtify-form-row dedebtify-form-row-2col">
                    <div class="dedebtify-form-group">
                        <label for="frequency" class="dedebtify-form-label"><?php _e( 'Frequency', 'dedebtify' ); ?> <span class="required">*</span></label>
                        <select id="frequency" name="frequency" class="dedebtify-form-select" required>
                            <option value="monthly"><?php _e( 'Monthly', 'dedebtify' ); ?></option>
                            <option value="weekly"><?php _e( 'Weekly', 'dedebtify' ); ?></option>
                            <option value="bi-weekly"><?php _e( 'Bi-weekly', 'dedebtify' ); ?></option>
                            <option value="quarterly"><?php _e( 'Quarterly', 'dedebtify' ); ?></option>
                            <option value="annually"><?php _e( 'Annually', 'dedebtify' ); ?></option>
                        </select>
                    </div>

                    <div class="dedebtify-form-group">
                        <label for="due_date" class="dedebtify-form-label"><?php _e( 'Due Date (Day of Month)', 'dedebtify' ); ?></label>
                        <input type="number" min="1" max="31" id="due_date" name="due_date" class="dedebtify-form-input" placeholder="15">
                        <span class="dedebtify-form-help"><?php _e( 'Optional: Day when bill is due', 'dedebtify' ); ?></span>
                    </div>
                </div>

                <div class="dedebtify-form-row dedebtify-form-row-2col">
                    <div class="dedebtify-form-group">
                        <label for="auto_pay" class="dedebtify-form-label">
                            <input type="checkbox" id="auto_pay" name="auto_pay" value="1">
                            <?php _e( 'Auto-Pay Enabled', 'dedebtify' ); ?>
                        </label>
                    </div>

                    <div class="dedebtify-form-group">
                        <label for="is_essential" class="dedebtify-form-label">
                            <input type="checkbox" id="is_essential" name="is_essential" value="1">
                            <?php _e( 'Essential Bill', 'dedebtify' ); ?>
                        </label>
                        <span class="dedebtify-form-help"><?php _e( 'Mark if this is a necessary expense', 'dedebtify' ); ?></span>
                    </div>
                </div>

                <!-- Monthly Equivalent Preview -->
                <div id="dedebtify-bill-preview" class="dedebtify-payoff-preview" style="display: none;">
                    <h3><?php _e( 'Monthly Equivalent', 'dedebtify' ); ?></h3>
                    <div class="dedebtify-stats-grid">
                        <div class="dedebtify-stat-card">
                            <div class="dedebtify-stat-label"><?php _e( 'Monthly Cost', 'dedebtify' ); ?></div>
                            <div class="dedebtify-stat-value" id="preview-monthly">$0.00</div>
                            <div class="dedebtify-stat-subtext" id="preview-calculation"></div>
                        </div>
                        <div class="dedebtify-stat-card">
                            <div class="dedebtify-stat-label"><?php _e( 'Annual Cost', 'dedebtify' ); ?></div>
                            <div class="dedebtify-stat-value" id="preview-annual">$0.00</div>
                        </div>
                    </div>
                </div>

                <div class="dedebtify-form-actions">
                    <button type="submit" class="dedebtify-btn dedebtify-btn-success">
                        <?php echo $action === 'edit' ? __( 'Update Bill', 'dedebtify' ) : __( 'Add Bill', 'dedebtify' ); ?>
                    </button>
                    <a href="?action=list" class="dedebtify-btn dedebtify-btn-secondary"><?php _e( 'Cancel', 'dedebtify' ); ?></a>
                </div>
            </form>
        </div>

    <?php else : ?>
        <!-- List View -->
        <div class="dedebtify-manager-header">
            <div class="dedebtify-manager-stats" id="dedebtify-bill-stats">
                <div class="dedebtify-stat-summary">
                    <span class="stat-label"><?php _e( 'Total Monthly Bills:', 'dedebtify' ); ?></span>
                    <span class="stat-value" id="bill-total-monthly">$0.00</span>
                </div>
                <div class="dedebtify-stat-summary">
                    <span class="stat-label"><?php _e( 'Essential:', 'dedebtify' ); ?></span>
                    <span class="stat-value" id="bill-essential">$0.00</span>
                </div>
                <div class="dedebtify-stat-summary">
                    <span class="stat-label"><?php _e( 'Discretionary:', 'dedebtify' ); ?></span>
                    <span class="stat-value" id="bill-discretionary">$0.00</span>
                </div>
            </div>
            <a href="?action=add" class="dedebtify-btn dedebtify-btn-success"><?php _e( '+ Add Bill', 'dedebtify' ); ?></a>
        </div>

        <div class="dedebtify-manager-controls">
            <div class="dedebtify-filter-group">
                <label for="filter-category"><?php _e( 'Category:', 'dedebtify' ); ?></label>
                <select id="filter-category" class="dedebtify-form-select">
                    <option value="all"><?php _e( 'All Categories', 'dedebtify' ); ?></option>
                    <option value="housing"><?php _e( 'Housing', 'dedebtify' ); ?></option>
                    <option value="transportation"><?php _e( 'Transportation', 'dedebtify' ); ?></option>
                    <option value="utilities"><?php _e( 'Utilities', 'dedebtify' ); ?></option>
                    <option value="food"><?php _e( 'Food', 'dedebtify' ); ?></option>
                    <option value="healthcare"><?php _e( 'Healthcare', 'dedebtify' ); ?></option>
                    <option value="insurance"><?php _e( 'Insurance', 'dedebtify' ); ?></option>
                    <option value="entertainment"><?php _e( 'Entertainment', 'dedebtify' ); ?></option>
                    <option value="subscriptions"><?php _e( 'Subscriptions', 'dedebtify' ); ?></option>
                    <option value="other"><?php _e( 'Other', 'dedebtify' ); ?></option>
                </select>
            </div>

            <div class="dedebtify-filter-group">
                <label for="filter-essential"><?php _e( 'Type:', 'dedebtify' ); ?></label>
                <select id="filter-essential" class="dedebtify-form-select">
                    <option value="all"><?php _e( 'All', 'dedebtify' ); ?></option>
                    <option value="essential"><?php _e( 'Essential', 'dedebtify' ); ?></option>
                    <option value="discretionary"><?php _e( 'Discretionary', 'dedebtify' ); ?></option>
                </select>
            </div>

            <div class="dedebtify-filter-group">
                <label for="sort-bills-by"><?php _e( 'Sort by:', 'dedebtify' ); ?></label>
                <select id="sort-bills-by" class="dedebtify-form-select">
                    <option value="amount-high"><?php _e( 'Amount (High to Low)', 'dedebtify' ); ?></option>
                    <option value="amount-low"><?php _e( 'Amount (Low to High)', 'dedebtify' ); ?></option>
                    <option value="due-date"><?php _e( 'Due Date', 'dedebtify' ); ?></option>
                    <option value="name"><?php _e( 'Name', 'dedebtify' ); ?></option>
                </select>
            </div>
        </div>

        <div id="dedebtify-bills-list" class="dedebtify-items-list">
            <div class="dedebtify-loading">
                <div class="dedebtify-spinner"></div>
                <p><?php _e( 'Loading bills...', 'dedebtify' ); ?></p>
            </div>
        </div>

        <div class="dedebtify-empty-state" id="bills-empty-state" style="display: none;">
            <h3><?php _e( 'No Bills Found', 'dedebtify' ); ?></h3>
            <p><?php _e( 'Add your first bill to start tracking your monthly expenses and see where your money goes.', 'dedebtify' ); ?></p>
            <a href="?action=add" class="dedebtify-btn dedebtify-btn-success"><?php _e( '+ Add Your First Bill', 'dedebtify' ); ?></a>
        </div>
    <?php endif; ?>

</div>
