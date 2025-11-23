<?php
/**
 * Account Sync Template (Plaid Integration)
 *
 * This template allows users to link and sync their financial accounts
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
    echo '<p>' . __( 'Please log in to connect your accounts.', 'dedebtify' ) . '</p>';
    return;
}

$plaid_enabled = get_option( 'dedebtify_plaid_enabled', 0 );
$plaid_client_id = get_option( 'dedebtify_plaid_client_id', '' );
?>

<!-- Navigation -->
<?php Dedebtify_Helpers::render_navigation( 'account_sync' ); ?>

<div class="dedebtify-dashboard dedebtify-account-sync">

    <div class="dedebtify-dashboard-header">
        <h1><?php _e( 'Account Sync', 'dedebtify' ); ?></h1>
        <p><?php _e( 'Automatically sync your financial accounts to keep your debt information up-to-date', 'dedebtify' ); ?></p>
    </div>

    <?php if ( ! $plaid_enabled || empty( $plaid_client_id ) ) : ?>
        <!-- Plaid Not Configured -->
        <div class="dedebtify-message warning">
            <span class="dashicons dashicons-warning"></span>
            <div>
                <strong><?php _e( 'Account Sync Not Available', 'dedebtify' ); ?></strong>
                <p><?php _e( 'The site administrator has not configured account syncing yet. Please contact support or check back later.', 'dedebtify' ); ?></p>
            </div>
        </div>

    <?php else : ?>
        <!-- Connected Accounts Section -->
        <div class="dedebtify-section">
            <div class="dedebtify-section-header">
                <h2><?php _e( 'Connected Accounts', 'dedebtify' ); ?></h2>
                <button id="dd-link-account-btn" class="dedebtify-btn dedebtify-btn-success">
                    <span class="dashicons dashicons-plus"></span>
                    <?php _e( 'Link New Account', 'dedebtify' ); ?>
                </button>
            </div>

            <div id="dd-linked-accounts-list" class="dd-linked-accounts-list">
                <div class="dedebtify-loading">
                    <div class="dedebtify-spinner"></div>
                    <p><?php _e( 'Loading connected accounts...', 'dedebtify' ); ?></p>
                </div>
            </div>

            <div id="dd-no-accounts" class="dedebtify-empty-state" style="display: none;">
                <span class="dashicons dashicons-bank" style="font-size: 48px; color: #cbd5e0; margin-bottom: 16px;"></span>
                <h3><?php _e( 'No Connected Accounts', 'dedebtify' ); ?></h3>
                <p><?php _e( 'Link your bank, credit card, and loan accounts to automatically sync your balances and transactions.', 'dedebtify' ); ?></p>
                <button id="dd-link-first-account-btn" class="dedebtify-btn dedebtify-btn-success">
                    <span class="dashicons dashicons-plus"></span>
                    <?php _e( 'Link Your First Account', 'dedebtify' ); ?>
                </button>
            </div>
        </div>

        <!-- Sync Info Section -->
        <div class="dedebtify-section">
            <h2><?php _e( 'How Account Sync Works', 'dedebtify' ); ?></h2>

            <div class="dd-info-cards">
                <div class="dd-info-card">
                    <div class="dd-info-icon">
                        <span class="dashicons dashicons-shield"></span>
                    </div>
                    <h3><?php _e( 'Secure Connection', 'dedebtify' ); ?></h3>
                    <p><?php _e( 'We use Plaid, a trusted financial data network, to securely connect your accounts. Your login credentials are never shared with DeDebtify.', 'dedebtify' ); ?></p>
                </div>

                <div class="dd-info-card">
                    <div class="dd-info-icon">
                        <span class="dashicons dashicons-update"></span>
                    </div>
                    <h3><?php _e( 'Automatic Updates', 'dedebtify' ); ?></h3>
                    <p><?php _e( 'Your account balances and transactions are automatically updated daily. You can also manually sync anytime to get the latest data.', 'dedebtify' ); ?></p>
                </div>

                <div class="dd-info-card">
                    <div class="dd-info-icon">
                        <span class="dashicons dashicons-admin-tools"></span>
                    </div>
                    <h3><?php _e( 'Full Control', 'dedebtify' ); ?></h3>
                    <p><?php _e( 'You can disconnect any linked account at any time. Disconnecting removes the connection but keeps your historical data.', 'dedebtify' ); ?></p>
                </div>
            </div>
        </div>

        <!-- Supported Account Types -->
        <div class="dedebtify-section">
            <h2><?php _e( 'Supported Account Types', 'dedebtify' ); ?></h2>
            <div class="dd-supported-types">
                <div class="dd-type-badge">
                    <span class="dashicons dashicons-money-alt"></span>
                    <?php _e( 'Credit Cards', 'dedebtify' ); ?>
                </div>
                <div class="dd-type-badge">
                    <span class="dashicons dashicons-money-alt"></span>
                    <?php _e( 'Personal Loans', 'dedebtify' ); ?>
                </div>
                <div class="dd-type-badge">
                    <span class="dashicons dashicons-money-alt"></span>
                    <?php _e( 'Auto Loans', 'dedebtify' ); ?>
                </div>
                <div class="dd-type-badge">
                    <span class="dashicons dashicons-welcome-learn-more"></span>
                    <?php _e( 'Student Loans', 'dedebtify' ); ?>
                </div>
                <div class="dd-type-badge">
                    <span class="dashicons dashicons-admin-home"></span>
                    <?php _e( 'Mortgages', 'dedebtify' ); ?>
                </div>
            </div>
        </div>

    <?php endif; ?>

</div>

<style>
.dd-linked-accounts-list {
    margin-top: 20px;
}

.dd-linked-account-item {
    background: hsl(var(--dd-card));
    border: 1px solid hsl(var(--dd-border));
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.dd-account-info {
    display: flex;
    align-items: center;
    gap: 16px;
}

.dd-account-icon {
    width: 48px;
    height: 48px;
    background: hsl(var(--dd-accent));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: hsl(var(--dd-primary));
}

.dd-account-details h3 {
    margin: 0 0 4px 0;
    font-size: 16px;
    font-weight: 600;
}

.dd-account-meta {
    font-size: 13px;
    color: hsl(var(--dd-muted-foreground));
}

.dd-account-actions {
    display: flex;
    gap: 8px;
}

.dd-info-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.dd-info-card {
    background: hsl(var(--dd-card));
    border: 1px solid hsl(var(--dd-border));
    border-radius: 12px;
    padding: 24px;
    text-align: center;
}

.dd-info-icon {
    width: 56px;
    height: 56px;
    background: hsl(var(--dd-primary) / 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
    font-size: 28px;
    color: hsl(var(--dd-primary));
}

.dd-info-card h3 {
    font-size: 16px;
    font-weight: 600;
    margin: 0 0 8px 0;
}

.dd-info-card p {
    font-size: 14px;
    color: hsl(var(--dd-muted-foreground));
    margin: 0;
}

.dd-supported-types {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 20px;
}

.dd-type-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    background: hsl(var(--dd-accent));
    border: 1px solid hsl(var(--dd-border));
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
}

.dd-type-badge .dashicons {
    font-size: 18px;
    color: hsl(var(--dd-primary));
}

.dedebtify-section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
}

.dedebtify-section-header h2 {
    margin: 0;
}
</style>
