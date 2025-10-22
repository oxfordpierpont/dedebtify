<?php
/**
 * Settings Page
 *
 * @since      1.0.0
 * @package    Dedebtify
 * @subpackage Dedebtify/admin
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Handle form submission
if ( isset( $_POST['dedebtify_settings_submit'] ) && check_admin_referer( 'dedebtify_settings_nonce' ) ) {
    // Save settings
    update_option( 'dedebtify_currency_symbol', sanitize_text_field( $_POST['currency_symbol'] ) );
    update_option( 'dedebtify_default_interest_rate', floatval( $_POST['default_interest_rate'] ) );
    update_option( 'dedebtify_notifications_enabled', isset( $_POST['notifications_enabled'] ) ? 1 : 0 );
    update_option( 'dedebtify_notification_email', sanitize_email( $_POST['notification_email'] ) );
    update_option( 'dedebtify_snapshot_frequency', sanitize_text_field( $_POST['snapshot_frequency'] ) );
    update_option( 'dedebtify_default_payoff_strategy', sanitize_text_field( $_POST['default_payoff_strategy'] ) );

    echo '<div class="notice notice-success is-dismissible"><p>' . __( 'Settings saved successfully!', 'dedebtify' ) . '</p></div>';
}

// Get current settings
$currency_symbol = get_option( 'dedebtify_currency_symbol', '$' );
$default_interest_rate = get_option( 'dedebtify_default_interest_rate', 18.0 );
$notifications_enabled = get_option( 'dedebtify_notifications_enabled', 0 );
$notification_email = get_option( 'dedebtify_notification_email', get_option( 'admin_email' ) );
$snapshot_frequency = get_option( 'dedebtify_snapshot_frequency', 'monthly' );
$default_payoff_strategy = get_option( 'dedebtify_default_payoff_strategy', 'avalanche' );
?>

<div class="wrap dedebtify-settings-page">
    <h1><?php _e( 'DeDebtify Settings', 'dedebtify' ); ?></h1>
    <p class="description"><?php _e( 'Configure your debt management system settings', 'dedebtify' ); ?></p>

    <form method="post" action="">
        <?php wp_nonce_field( 'dedebtify_settings_nonce' ); ?>

        <!-- General Settings -->
        <div class="dedebtify-settings-section">
            <h3><?php _e( 'General Settings', 'dedebtify' ); ?></h3>

            <div class="dedebtify-settings-row">
                <div class="dedebtify-settings-label">
                    <label for="currency_symbol"><?php _e( 'Currency Symbol', 'dedebtify' ); ?></label>
                </div>
                <div class="dedebtify-settings-field">
                    <input type="text" id="currency_symbol" name="currency_symbol" value="<?php echo esc_attr( $currency_symbol ); ?>" class="regular-text">
                    <span class="description"><?php _e( 'The currency symbol to display (e.g., $, €, £)', 'dedebtify' ); ?></span>
                </div>
            </div>

            <div class="dedebtify-settings-row">
                <div class="dedebtify-settings-label">
                    <label for="default_interest_rate"><?php _e( 'Default Interest Rate', 'dedebtify' ); ?></label>
                </div>
                <div class="dedebtify-settings-field">
                    <input type="number" id="default_interest_rate" name="default_interest_rate" value="<?php echo esc_attr( $default_interest_rate ); ?>" step="0.01" min="0" max="100" class="regular-text">
                    <span class="description"><?php _e( 'Default annual interest rate (%) for new credit cards', 'dedebtify' ); ?></span>
                </div>
            </div>

            <div class="dedebtify-settings-row">
                <div class="dedebtify-settings-label">
                    <label for="default_payoff_strategy"><?php _e( 'Default Payoff Strategy', 'dedebtify' ); ?></label>
                </div>
                <div class="dedebtify-settings-field">
                    <select id="default_payoff_strategy" name="default_payoff_strategy" class="regular-text">
                        <option value="avalanche" <?php selected( $default_payoff_strategy, 'avalanche' ); ?>><?php _e( 'Avalanche (Highest Interest First)', 'dedebtify' ); ?></option>
                        <option value="snowball" <?php selected( $default_payoff_strategy, 'snowball' ); ?>><?php _e( 'Snowball (Lowest Balance First)', 'dedebtify' ); ?></option>
                    </select>
                    <span class="description"><?php _e( 'Default debt payoff strategy for new users', 'dedebtify' ); ?></span>
                </div>
            </div>
        </div>

        <!-- Snapshot Settings -->
        <div class="dedebtify-settings-section">
            <h3><?php _e( 'Snapshot Settings', 'dedebtify' ); ?></h3>

            <div class="dedebtify-settings-row">
                <div class="dedebtify-settings-label">
                    <label for="snapshot_frequency"><?php _e( 'Snapshot Reminder Frequency', 'dedebtify' ); ?></label>
                </div>
                <div class="dedebtify-settings-field">
                    <select id="snapshot_frequency" name="snapshot_frequency" class="regular-text">
                        <option value="weekly" <?php selected( $snapshot_frequency, 'weekly' ); ?>><?php _e( 'Weekly', 'dedebtify' ); ?></option>
                        <option value="monthly" <?php selected( $snapshot_frequency, 'monthly' ); ?>><?php _e( 'Monthly', 'dedebtify' ); ?></option>
                        <option value="quarterly" <?php selected( $snapshot_frequency, 'quarterly' ); ?>><?php _e( 'Quarterly', 'dedebtify' ); ?></option>
                    </select>
                    <span class="description"><?php _e( 'How often to remind users to create snapshots', 'dedebtify' ); ?></span>
                </div>
            </div>
        </div>

        <!-- Notification Settings -->
        <div class="dedebtify-settings-section">
            <h3><?php _e( 'Notification Settings', 'dedebtify' ); ?></h3>

            <div class="dedebtify-settings-row">
                <div class="dedebtify-settings-label">
                    <label for="notifications_enabled"><?php _e( 'Enable Notifications', 'dedebtify' ); ?></label>
                </div>
                <div class="dedebtify-settings-field">
                    <label>
                        <input type="checkbox" id="notifications_enabled" name="notifications_enabled" value="1" <?php checked( $notifications_enabled, 1 ); ?>>
                        <?php _e( 'Enable email notifications', 'dedebtify' ); ?>
                    </label>
                    <span class="description"><?php _e( 'Send email notifications for important events', 'dedebtify' ); ?></span>
                </div>
            </div>

            <div class="dedebtify-settings-row">
                <div class="dedebtify-settings-label">
                    <label for="notification_email"><?php _e( 'Notification Email', 'dedebtify' ); ?></label>
                </div>
                <div class="dedebtify-settings-field">
                    <input type="email" id="notification_email" name="notification_email" value="<?php echo esc_attr( $notification_email ); ?>" class="regular-text">
                    <span class="description"><?php _e( 'Email address for admin notifications', 'dedebtify' ); ?></span>
                </div>
            </div>
        </div>

        <!-- Shortcodes Reference -->
        <div class="dedebtify-settings-section">
            <h3><?php _e( 'Available Shortcodes', 'dedebtify' ); ?></h3>
            <p><?php _e( 'Use these shortcodes in your pages to display various components:', 'dedebtify' ); ?></p>

            <table class="widefat" style="max-width: 800px;">
                <thead>
                    <tr>
                        <th><?php _e( 'Shortcode', 'dedebtify' ); ?></th>
                        <th><?php _e( 'Description', 'dedebtify' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>[dedebtify_dashboard]</code></td>
                        <td><?php _e( 'Display the complete user dashboard', 'dedebtify' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>[dedebtify_credit_cards]</code></td>
                        <td><?php _e( 'Display credit card manager interface', 'dedebtify' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>[dedebtify_loans]</code></td>
                        <td><?php _e( 'Display loans manager interface', 'dedebtify' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>[dedebtify_bills]</code></td>
                        <td><?php _e( 'Display bills manager interface', 'dedebtify' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>[dedebtify_goals]</code></td>
                        <td><?php _e( 'Display financial goals manager interface', 'dedebtify' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>[dedebtify_action_plan]</code></td>
                        <td><?php _e( 'Display debt action plan generator', 'dedebtify' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>[dedebtify_snapshots]</code></td>
                        <td><?php _e( 'Display financial snapshots and progress tracking', 'dedebtify' ); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Save Button -->
        <p class="submit">
            <button type="submit" name="dedebtify_settings_submit" class="button button-primary">
                <?php _e( 'Save Settings', 'dedebtify' ); ?>
            </button>
        </p>
    </form>

    <!-- System Information -->
    <div class="dedebtify-settings-section">
        <h3><?php _e( 'System Information', 'dedebtify' ); ?></h3>

        <table class="widefat" style="max-width: 600px;">
            <tr>
                <th style="width: 250px;"><?php _e( 'Plugin Version', 'dedebtify' ); ?></th>
                <td><?php echo DEDEBTIFY_VERSION; ?></td>
            </tr>
            <tr>
                <th><?php _e( 'WordPress Version', 'dedebtify' ); ?></th>
                <td><?php echo get_bloginfo( 'version' ); ?></td>
            </tr>
            <tr>
                <th><?php _e( 'PHP Version', 'dedebtify' ); ?></th>
                <td><?php echo PHP_VERSION; ?></td>
            </tr>
            <tr>
                <th><?php _e( 'Database Version', 'dedebtify' ); ?></th>
                <td><?php global $wpdb; echo $wpdb->db_version(); ?></td>
            </tr>
            <tr>
                <th><?php _e( 'Server Software', 'dedebtify' ); ?></th>
                <td><?php echo $_SERVER['SERVER_SOFTWARE']; ?></td>
            </tr>
        </table>
    </div>

</div>
