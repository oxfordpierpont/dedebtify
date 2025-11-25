<?php
/**
 * Setup/Welcome Page
 *
 * @since      1.0.0
 * @package    Dedebtify
 * @subpackage Dedebtify/admin
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Get page IDs
$page_ids = get_option( 'dedebtify_page_ids', array() );
$dashboard_page_id = get_option( 'dedebtify_dashboard_page_id', 0 );
?>

<div class="wrap">
    <h1><?php _e( 'Welcome to DeDebtify!', 'dedebtify' ); ?></h1>
    <p class="about-text"><?php _e( 'Thank you for installing DeDebtify! Your debt management system is ready to use.', 'dedebtify' ); ?></p>

    <div class="dedebtify-admin-section">
        <h2><?php _e( 'Setup Complete!', 'dedebtify' ); ?></h2>

        <?php if ( ! empty( $page_ids ) ) : ?>
            <p><?php _e( 'The following pages have been created automatically:', 'dedebtify' ); ?></p>

            <table class="widefat">
                <thead>
                    <tr>
                        <th><?php _e( 'Page', 'dedebtify' ); ?></th>
                        <th><?php _e( 'URL', 'dedebtify' ); ?></th>
                        <th><?php _e( 'Actions', 'dedebtify' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $page_ids as $key => $page_id ) : ?>
                        <?php $page = get_post( $page_id ); ?>
                        <?php if ( $page ) : ?>
                            <tr>
                                <td><strong><?php echo esc_html( $page->post_title ); ?></strong></td>
                                <td><code><?php echo esc_html( get_permalink( $page_id ) ); ?></code></td>
                                <td>
                                    <a href="<?php echo esc_url( get_permalink( $page_id ) ); ?>" class="button" target="_blank"><?php _e( 'View', 'dedebtify' ); ?></a>
                                    <a href="<?php echo esc_url( get_edit_post_link( $page_id ) ); ?>" class="button"><?php _e( 'Edit', 'dedebtify' ); ?></a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <div class="notice notice-warning inline">
                <p><?php _e( 'No pages were created. Please deactivate and reactivate the plugin to create the pages.', 'dedebtify' ); ?></p>
            </div>
        <?php endif; ?>
    </div>

    <div class="dedebtify-admin-section">
        <h2><?php _e( 'Next Steps', 'dedebtify' ); ?></h2>

        <div class="dedebtify-setup-steps">
            <div class="dedebtify-step">
                <span class="dedebtify-step-number">1</span>
                <div class="dedebtify-step-content">
                    <h3><?php _e( 'Create a Navigation Menu', 'dedebtify' ); ?></h3>
                    <p><?php _e( 'Add the DeDebtify pages to your WordPress menu so users can navigate between them.', 'dedebtify' ); ?></p>
                    <a href="<?php echo admin_url( 'nav-menus.php' ); ?>" class="button button-primary"><?php _e( 'Go to Menus', 'dedebtify' ); ?></a>
                </div>
            </div>

            <div class="dedebtify-step">
                <span class="dedebtify-step-number">2</span>
                <div class="dedebtify-step-content">
                    <h3><?php _e( 'Configure Settings', 'dedebtify' ); ?></h3>
                    <p><?php _e( 'Set your currency, default interest rates, and notification preferences.', 'dedebtify' ); ?></p>
                    <a href="<?php echo admin_url( 'admin.php?page=dedebtify-settings' ); ?>" class="button button-primary"><?php _e( 'Go to Settings', 'dedebtify' ); ?></a>
                </div>
            </div>

            <div class="dedebtify-step">
                <span class="dedebtify-step-number">3</span>
                <div class="dedebtify-step-content">
                    <h3><?php _e( 'Test the User Experience', 'dedebtify' ); ?></h3>
                    <p><?php _e( 'Visit the dashboard page and start adding your financial data.', 'dedebtify' ); ?></p>
                    <?php if ( $dashboard_page_id ) : ?>
                        <a href="<?php echo get_permalink( $dashboard_page_id ); ?>" class="button button-primary" target="_blank"><?php _e( 'View Dashboard', 'dedebtify' ); ?></a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="dedebtify-step">
                <span class="dedebtify-step-number">4</span>
                <div class="dedebtify-step-content">
                    <h3><?php _e( 'Restrict Access (Optional)', 'dedebtify' ); ?></h3>
                    <p><?php _e( 'Consider using a membership plugin to restrict pages to logged-in users only.', 'dedebtify' ); ?></p>
                    <p class="description"><?php _e( 'Recommended plugins: MemberPress, Restrict Content Pro, or WP Members.', 'dedebtify' ); ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="dedebtify-admin-section">
        <h2><?php _e( 'Available Shortcodes', 'dedebtify' ); ?></h2>
        <p><?php _e( 'You can use these shortcodes in any page or post:', 'dedebtify' ); ?></p>

        <table class="widefat">
            <thead>
                <tr>
                    <th><?php _e( 'Shortcode', 'dedebtify' ); ?></th>
                    <th><?php _e( 'Description', 'dedebtify' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><code>[dedebtify_dashboard]</code></td>
                    <td><?php _e( 'Display the complete user dashboard with financial overview', 'dedebtify' ); ?></td>
                </tr>
                <tr>
                    <td><code>[dedebtify_credit_cards]</code></td>
                    <td><?php _e( 'Credit card management interface', 'dedebtify' ); ?></td>
                </tr>
                <tr>
                    <td><code>[dedebtify_loans]</code></td>
                    <td><?php _e( 'Loans management interface', 'dedebtify' ); ?></td>
                </tr>
                <tr>
                    <td><code>[dedebtify_mortgages]</code></td>
                    <td><?php _e( 'Mortgage management with payoff projections', 'dedebtify' ); ?></td>
                </tr>
                <tr>
                    <td><code>[dedebtify_bills]</code></td>
                    <td><?php _e( 'Bills and recurring expenses tracking', 'dedebtify' ); ?></td>
                </tr>
                <tr>
                    <td><code>[dedebtify_goals]</code></td>
                    <td><?php _e( 'Financial goals management', 'dedebtify' ); ?></td>
                </tr>
                <tr>
                    <td><code>[dedebtify_action_plan]</code></td>
                    <td><?php _e( 'Debt payoff action plan generator', 'dedebtify' ); ?></td>
                </tr>
                <tr>
                    <td><code>[dedebtify_snapshots]</code></td>
                    <td><?php _e( 'Progress tracking and snapshot comparison', 'dedebtify' ); ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="dedebtify-admin-section">
        <h2><?php _e( 'Need Help?', 'dedebtify' ); ?></h2>
        <div class="dedebtify-quick-actions">
            <a href="<?php echo admin_url( 'admin.php?page=dedebtify' ); ?>" class="dedebtify-action-card">
                <span class="dashicons dashicons-dashboard"></span>
                <h3><?php _e( 'Admin Dashboard', 'dedebtify' ); ?></h3>
                <p><?php _e( 'View system statistics', 'dedebtify' ); ?></p>
            </a>

            <a href="<?php echo admin_url( 'admin.php?page=dedebtify-reports' ); ?>" class="dedebtify-action-card">
                <span class="dashicons dashicons-chart-bar"></span>
                <h3><?php _e( 'Reports', 'dedebtify' ); ?></h3>
                <p><?php _e( 'View analytics and reports', 'dedebtify' ); ?></p>
            </a>

            <a href="<?php echo admin_url( 'admin.php?page=dedebtify-settings' ); ?>" class="dedebtify-action-card">
                <span class="dashicons dashicons-admin-settings"></span>
                <h3><?php _e( 'Settings', 'dedebtify' ); ?></h3>
                <p><?php _e( 'Configure plugin options', 'dedebtify' ); ?></p>
            </a>
        </div>
    </div>

    <div class="dedebtify-admin-section">
        <h2><?php _e( 'Hide This Page', 'dedebtify' ); ?></h2>
        <p><?php _e( 'Once you\'ve completed the setup, you can hide this welcome page.', 'dedebtify' ); ?></p>
        <form method="post">
            <?php wp_nonce_field( 'dedebtify_hide_setup' ); ?>
            <button type="submit" name="dedebtify_hide_setup" class="button"><?php _e( 'Hide Setup Page', 'dedebtify' ); ?></button>
        </form>
    </div>
</div>

<style>
.dedebtify-setup-steps {
    display: grid;
    gap: 20px;
    margin-top: 20px;
}

.dedebtify-step {
    display: flex;
    gap: 20px;
    padding: 20px;
    background: #fff;
    border: 1px solid #dcdcde;
    border-radius: 4px;
}

.dedebtify-step-number {
    flex-shrink: 0;
    width: 40px;
    height: 40px;
    background: #2271b1;
    color: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    font-weight: bold;
}

.dedebtify-step-content {
    flex: 1;
}

.dedebtify-step-content h3 {
    margin: 0 0 10px 0;
    font-size: 16px;
}

.dedebtify-step-content p {
    margin: 0 0 15px 0;
}
</style>

<?php
// Handle hide setup page
if ( isset( $_POST['dedebtify_hide_setup'] ) && check_admin_referer( 'dedebtify_hide_setup' ) ) {
    update_option( 'dedebtify_hide_setup_page', true );
    wp_redirect( admin_url( 'admin.php?page=dedebtify' ) );
    exit;
}
?>
