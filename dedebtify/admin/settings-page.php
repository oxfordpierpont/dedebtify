<?php
/**
 * Admin Settings and Dashboard Page
 *
 * @since      1.0.0
 * @package    Dedebtify
 * @subpackage Dedebtify/admin
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Get current page
$current_page = isset( $_GET['page'] ) ? $_GET['page'] : 'dedebtify';

?>

<div class="wrap dedebtify-admin-dashboard">

    <div class="dedebtify-admin-header">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <p><?php _e( 'Manage your debt tracking plugin settings and view system overview.', 'dedebtify' ); ?></p>
    </div>

    <?php if ( $current_page === 'dedebtify' ) : ?>
        <!-- Dashboard View -->
        <div id="dedebtify-dashboard-stats">
            <div class="dedebtify-stats-grid">
                <div class="dedebtify-stat-card">
                    <h3><?php _e( 'Total Users', 'dedebtify' ); ?></h3>
                    <div class="dedebtify-stat-value"><?php echo count_users()['total_users']; ?></div>
                    <p><?php _e( 'Registered users', 'dedebtify' ); ?></p>
                </div>

                <div class="dedebtify-stat-card">
                    <h3><?php _e( 'Credit Cards', 'dedebtify' ); ?></h3>
                    <div class="dedebtify-stat-value"><?php echo wp_count_posts( 'dd_credit_card' )->publish; ?></div>
                    <p><?php _e( 'Total tracked', 'dedebtify' ); ?></p>
                </div>

                <div class="dedebtify-stat-card">
                    <h3><?php _e( 'Loans', 'dedebtify' ); ?></h3>
                    <div class="dedebtify-stat-value"><?php echo wp_count_posts( 'dd_loan' )->publish; ?></div>
                    <p><?php _e( 'Total tracked', 'dedebtify' ); ?></p>
                </div>

                <div class="dedebtify-stat-card">
                    <h3><?php _e( 'Bills', 'dedebtify' ); ?></h3>
                    <div class="dedebtify-stat-value"><?php echo wp_count_posts( 'dd_bill' )->publish; ?></div>
                    <p><?php _e( 'Total tracked', 'dedebtify' ); ?></p>
                </div>

                <div class="dedebtify-stat-card">
                    <h3><?php _e( 'Goals', 'dedebtify' ); ?></h3>
                    <div class="dedebtify-stat-value"><?php echo wp_count_posts( 'dd_goal' )->publish; ?></div>
                    <p><?php _e( 'Total tracked', 'dedebtify' ); ?></p>
                </div>

                <div class="dedebtify-stat-card">
                    <h3><?php _e( 'Snapshots', 'dedebtify' ); ?></h3>
                    <div class="dedebtify-stat-value"><?php echo wp_count_posts( 'dd_snapshot' )->publish; ?></div>
                    <p><?php _e( 'Total created', 'dedebtify' ); ?></p>
                </div>
            </div>
        </div>

        <div class="dedebtify-quick-actions">
            <h2><?php _e( 'Quick Actions', 'dedebtify' ); ?></h2>
            <div class="dedebtify-actions-grid">
                <a href="<?php echo admin_url( 'post-new.php?post_type=dd_credit_card' ); ?>" class="dedebtify-action-btn">
                    <?php _e( 'Add Credit Card', 'dedebtify' ); ?>
                </a>
                <a href="<?php echo admin_url( 'post-new.php?post_type=dd_loan' ); ?>" class="dedebtify-action-btn">
                    <?php _e( 'Add Loan', 'dedebtify' ); ?>
                </a>
                <a href="<?php echo admin_url( 'post-new.php?post_type=dd_bill' ); ?>" class="dedebtify-action-btn">
                    <?php _e( 'Add Bill', 'dedebtify' ); ?>
                </a>
                <a href="<?php echo admin_url( 'post-new.php?post_type=dd_goal' ); ?>" class="dedebtify-action-btn">
                    <?php _e( 'Add Goal', 'dedebtify' ); ?>
                </a>
                <a href="#" id="dedebtify-create-snapshot" class="dedebtify-action-btn">
                    <?php _e( 'Create Snapshot', 'dedebtify' ); ?>
                </a>
                <a href="<?php echo admin_url( 'admin.php?page=dedebtify-settings' ); ?>" class="dedebtify-action-btn">
                    <?php _e( 'Settings', 'dedebtify' ); ?>
                </a>
            </div>
        </div>

        <div class="dedebtify-info-box">
            <h2><?php _e( 'Plugin Information', 'dedebtify' ); ?></h2>
            <table class="widefat">
                <tbody>
                    <tr>
                        <td><strong><?php _e( 'Version:', 'dedebtify' ); ?></strong></td>
                        <td><?php echo DEDEBTIFY_VERSION; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php _e( 'PHP Version:', 'dedebtify' ); ?></strong></td>
                        <td><?php echo PHP_VERSION; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php _e( 'WordPress Version:', 'dedebtify' ); ?></strong></td>
                        <td><?php echo get_bloginfo( 'version' ); ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php _e( 'Elementor:', 'dedebtify' ); ?></strong></td>
                        <td><?php echo defined( 'ELEMENTOR_VERSION' ) ? ELEMENTOR_VERSION : __( 'Not installed', 'dedebtify' ); ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php _e( 'JetEngine:', 'dedebtify' ); ?></strong></td>
                        <td><?php echo defined( 'JET_ENGINE_VERSION' ) ? JET_ENGINE_VERSION : __( 'Not installed', 'dedebtify' ); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

    <?php elseif ( $current_page === 'dedebtify-settings' ) : ?>
        <!-- Settings View -->
        <form method="post" action="options.php">
            <?php
            settings_fields( 'dedebtify_settings_group' );
            do_settings_sections( 'dedebtify_settings_group' );

            $settings = get_option( 'dedebtify_settings', array() );
            ?>

            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="dedebtify_currency"><?php _e( 'Currency', 'dedebtify' ); ?></label>
                    </th>
                    <td>
                        <select id="dedebtify_currency" name="dedebtify_settings[currency]" class="regular-text">
                            <option value="USD" <?php selected( $settings['currency'] ?? 'USD', 'USD' ); ?>>USD ($)</option>
                            <option value="EUR" <?php selected( $settings['currency'] ?? 'USD', 'EUR' ); ?>>EUR (€)</option>
                            <option value="GBP" <?php selected( $settings['currency'] ?? 'USD', 'GBP' ); ?>>GBP (£)</option>
                            <option value="CAD" <?php selected( $settings['currency'] ?? 'USD', 'CAD' ); ?>>CAD ($)</option>
                            <option value="AUD" <?php selected( $settings['currency'] ?? 'USD', 'AUD' ); ?>>AUD ($)</option>
                        </select>
                        <p class="description"><?php _e( 'Select your preferred currency', 'dedebtify' ); ?></p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="dedebtify_date_format"><?php _e( 'Date Format', 'dedebtify' ); ?></label>
                    </th>
                    <td>
                        <input type="text" id="dedebtify_date_format" name="dedebtify_settings[date_format]" value="<?php echo esc_attr( $settings['date_format'] ?? 'F j, Y' ); ?>" class="regular-text">
                        <p class="description"><?php _e( 'PHP date format for displaying dates', 'dedebtify' ); ?></p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="dedebtify_payoff_method"><?php _e( 'Default Payoff Method', 'dedebtify' ); ?></label>
                    </th>
                    <td>
                        <select id="dedebtify_payoff_method" name="dedebtify_settings[default_payoff_method]" class="regular-text">
                            <option value="avalanche" <?php selected( $settings['default_payoff_method'] ?? 'avalanche', 'avalanche' ); ?>><?php _e( 'Avalanche (Highest Interest First)', 'dedebtify' ); ?></option>
                            <option value="snowball" <?php selected( $settings['default_payoff_method'] ?? 'avalanche', 'snowball' ); ?>><?php _e( 'Snowball (Smallest Balance First)', 'dedebtify' ); ?></option>
                        </select>
                        <p class="description"><?php _e( 'Default debt payoff strategy for new users', 'dedebtify' ); ?></p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="dedebtify_notifications"><?php _e( 'Enable Notifications', 'dedebtify' ); ?></label>
                    </th>
                    <td>
                        <input type="checkbox" id="dedebtify_notifications" name="dedebtify_settings[enable_notifications]" value="1" <?php checked( $settings['enable_notifications'] ?? false, 1 ); ?>>
                        <label for="dedebtify_notifications"><?php _e( 'Enable push notifications (requires OneSignal)', 'dedebtify' ); ?></label>
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>
        </form>

        <hr>

        <h2><?php _e( 'Shortcodes', 'dedebtify' ); ?></h2>
        <p><?php _e( 'Use these shortcodes in your pages:', 'dedebtify' ); ?></p>
        <ul>
            <li><code>[dedebtify_dashboard]</code> - <?php _e( 'Display the full dashboard', 'dedebtify' ); ?></li>
        </ul>

        <hr>

        <h2><?php _e( 'Documentation', 'dedebtify' ); ?></h2>
        <p><?php _e( 'For detailed documentation, visit:', 'dedebtify' ); ?> <a href="https://yoursite.com/docs" target="_blank">https://yoursite.com/docs</a></p>

    <?php endif; ?>

</div>
