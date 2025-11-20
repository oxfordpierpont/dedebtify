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

    // Save styling options
    update_option( 'dedebtify_primary_color', sanitize_hex_color( $_POST['primary_color'] ) );
    update_option( 'dedebtify_success_color', sanitize_hex_color( $_POST['success_color'] ) );
    update_option( 'dedebtify_warning_color', sanitize_hex_color( $_POST['warning_color'] ) );
    update_option( 'dedebtify_danger_color', sanitize_hex_color( $_POST['danger_color'] ) );
    update_option( 'dedebtify_font_family', sanitize_text_field( $_POST['font_family'] ) );
    update_option( 'dedebtify_border_radius', intval( $_POST['border_radius'] ) );

    // Save AI settings
    update_option( 'dedebtify_ai_provider', sanitize_text_field( $_POST['ai_provider'] ) );
    update_option( 'dedebtify_ai_api_key', sanitize_text_field( $_POST['ai_api_key'] ) );
    update_option( 'dedebtify_ai_model', sanitize_text_field( $_POST['ai_model'] ) );

    echo '<div class="notice notice-success is-dismissible"><p>' . __( 'Settings saved successfully!', 'dedebtify' ) . '</p></div>';
}

// Handle dummy data generation
if ( isset( $_POST['dedebtify_generate_dummy_data'] ) && check_admin_referer( 'dedebtify_dummy_data_nonce' ) ) {
    require_once DEDEBTIFY_PLUGIN_DIR . 'includes/class-dedebtify-dummy-data.php';
    Dedebtify_Dummy_Data::generate_all( get_current_user_id() );
    echo '<div class="notice notice-success is-dismissible"><p>' . __( 'Dummy data generated successfully!', 'dedebtify' ) . '</p></div>';
}

// Handle dummy data wipe
if ( isset( $_POST['dedebtify_wipe_dummy_data'] ) && check_admin_referer( 'dedebtify_dummy_data_nonce' ) ) {
    require_once DEDEBTIFY_PLUGIN_DIR . 'includes/class-dedebtify-dummy-data.php';
    Dedebtify_Dummy_Data::delete_all( get_current_user_id() );
    echo '<div class="notice notice-success is-dismissible"><p>' . __( 'Dummy data wiped successfully!', 'dedebtify' ) . '</p></div>';
}

// Get current settings
$currency_symbol = get_option( 'dedebtify_currency_symbol', '$' );
$default_interest_rate = get_option( 'dedebtify_default_interest_rate', 18.0 );
$notifications_enabled = get_option( 'dedebtify_notifications_enabled', 0 );
$notification_email = get_option( 'dedebtify_notification_email', get_option( 'admin_email' ) );
$snapshot_frequency = get_option( 'dedebtify_snapshot_frequency', 'monthly' );
$default_payoff_strategy = get_option( 'dedebtify_default_payoff_strategy', 'avalanche' );

// Get styling settings
$primary_color = get_option( 'dedebtify_primary_color', '#3b82f6' );
$success_color = get_option( 'dedebtify_success_color', '#10b981' );
$warning_color = get_option( 'dedebtify_warning_color', '#f59e0b' );
$danger_color = get_option( 'dedebtify_danger_color', '#ef4444' );
$font_family = get_option( 'dedebtify_font_family', 'System Default' );
$border_radius = get_option( 'dedebtify_border_radius', 8 );

// Get AI settings
$ai_provider = get_option( 'dedebtify_ai_provider', 'openai' );
$ai_api_key = get_option( 'dedebtify_ai_api_key', '' );
$ai_model = get_option( 'dedebtify_ai_model', 'gpt-4o' );

// Check if user has dummy data
$has_dummy_data = get_user_meta( get_current_user_id(), 'dd_has_dummy_data', true );
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

        <!-- Styling Settings -->
        <div class="dedebtify-settings-section">
            <h3><?php _e( 'Appearance Settings', 'dedebtify' ); ?></h3>

            <div class="dedebtify-settings-row">
                <div class="dedebtify-settings-label">
                    <label for="primary_color"><?php _e( 'Primary Color', 'dedebtify' ); ?></label>
                </div>
                <div class="dedebtify-settings-field">
                    <input type="color" id="primary_color" name="primary_color" value="<?php echo esc_attr( $primary_color ); ?>">
                    <span class="description"><?php _e( 'Main accent color for buttons and highlights', 'dedebtify' ); ?></span>
                </div>
            </div>

            <div class="dedebtify-settings-row">
                <div class="dedebtify-settings-label">
                    <label for="success_color"><?php _e( 'Success Color', 'dedebtify' ); ?></label>
                </div>
                <div class="dedebtify-settings-field">
                    <input type="color" id="success_color" name="success_color" value="<?php echo esc_attr( $success_color ); ?>">
                    <span class="description"><?php _e( 'Color for positive changes and success messages', 'dedebtify' ); ?></span>
                </div>
            </div>

            <div class="dedebtify-settings-row">
                <div class="dedebtify-settings-label">
                    <label for="warning_color"><?php _e( 'Warning Color', 'dedebtify' ); ?></label>
                </div>
                <div class="dedebtify-settings-field">
                    <input type="color" id="warning_color" name="warning_color" value="<?php echo esc_attr( $warning_color ); ?>">
                    <span class="description"><?php _e( 'Color for warnings and moderate alerts', 'dedebtify' ); ?></span>
                </div>
            </div>

            <div class="dedebtify-settings-row">
                <div class="dedebtify-settings-label">
                    <label for="danger_color"><?php _e( 'Danger Color', 'dedebtify' ); ?></label>
                </div>
                <div class="dedebtify-settings-field">
                    <input type="color" id="danger_color" name="danger_color" value="<?php echo esc_attr( $danger_color ); ?>">
                    <span class="description"><?php _e( 'Color for errors and urgent issues', 'dedebtify' ); ?></span>
                </div>
            </div>

            <div class="dedebtify-settings-row">
                <div class="dedebtify-settings-label">
                    <label for="font_family"><?php _e( 'Font Family', 'dedebtify' ); ?></label>
                </div>
                <div class="dedebtify-settings-field">
                    <select id="font_family" name="font_family" class="regular-text">
                        <option value="System Default" <?php selected( $font_family, 'System Default' ); ?>><?php _e( 'System Default', 'dedebtify' ); ?></option>
                        <option value="Arial, sans-serif" <?php selected( $font_family, 'Arial, sans-serif' ); ?>>Arial</option>
                        <option value="'Helvetica Neue', Helvetica, sans-serif" <?php selected( $font_family, "'Helvetica Neue', Helvetica, sans-serif" ); ?>>Helvetica</option>
                        <option value="'Segoe UI', Tahoma, sans-serif" <?php selected( $font_family, "'Segoe UI', Tahoma, sans-serif" ); ?>>Segoe UI</option>
                        <option value="'Roboto', sans-serif" <?php selected( $font_family, "'Roboto', sans-serif" ); ?>>Roboto</option>
                        <option value="'Open Sans', sans-serif" <?php selected( $font_family, "'Open Sans', sans-serif" ); ?>>Open Sans</option>
                        <option value="Georgia, serif" <?php selected( $font_family, 'Georgia, serif' ); ?>>Georgia</option>
                    </select>
                    <span class="description"><?php _e( 'Font family for all text', 'dedebtify' ); ?></span>
                </div>
            </div>

            <div class="dedebtify-settings-row">
                <div class="dedebtify-settings-label">
                    <label for="border_radius"><?php _e( 'Border Radius (px)', 'dedebtify' ); ?></label>
                </div>
                <div class="dedebtify-settings-field">
                    <input type="number" id="border_radius" name="border_radius" value="<?php echo esc_attr( $border_radius ); ?>" min="0" max="50" class="small-text">
                    <span class="description"><?php _e( 'Roundness of corners (0 = square, higher = rounder)', 'dedebtify' ); ?></span>
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

    <!-- Dummy Data Management -->
    <div class="dedebtify-settings-section" style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-top: 30px;">
        <h3><?php _e( 'Dummy Data Management', 'dedebtify' ); ?></h3>
        <p class="description"><?php _e( 'Use dummy data to test the plugin or demo it to potential users. All dummy data is user-specific and can be completely removed.', 'dedebtify' ); ?></p>

        <?php if ( $has_dummy_data ) : ?>
            <div class="notice notice-info inline">
                <p><strong><?php _e( 'You currently have dummy data installed.', 'dedebtify' ); ?></strong></p>
                <p><?php _e( 'Dummy data includes: 3 credit cards, 2 loans, 1 mortgage, 6 bills, 3 goals, and 3 historical snapshots.', 'dedebtify' ); ?></p>
            </div>

            <form method="post" action="" style="margin-top: 20px;">
                <?php wp_nonce_field( 'dedebtify_dummy_data_nonce' ); ?>
                <button type="submit" name="dedebtify_wipe_dummy_data" class="button button-secondary" onclick="return confirm('<?php _e( 'Are you sure you want to delete all dummy data? This cannot be undone.', 'dedebtify' ); ?>');">
                    <?php _e( 'Wipe All Dummy Data', 'dedebtify' ); ?>
                </button>
            </form>
        <?php else : ?>
            <div class="notice notice-warning inline">
                <p><?php _e( 'No dummy data found. Generate sample data to see how the plugin works.', 'dedebtify' ); ?></p>
            </div>

            <form method="post" action="" style="margin-top: 20px;">
                <?php wp_nonce_field( 'dedebtify_dummy_data_nonce' ); ?>
                <button type="submit" name="dedebtify_generate_dummy_data" class="button button-primary">
                    <?php _e( 'Generate Dummy Data', 'dedebtify' ); ?>
                </button>
                <p class="description" style="margin-top: 10px;">
                    <?php _e( 'This will create realistic sample data including credit cards, loans, mortgage, bills, goals, and historical snapshots.', 'dedebtify' ); ?>
                </p>
            </form>
        <?php endif; ?>
    </div>

    <!-- AI Coach Settings -->
    <div class="dedebtify-settings-section" id="ai-coach">
        <h3><?php _e( 'AI Financial Coach Settings', 'dedebtify' ); ?></h3>
        <p class="description">
            <?php _e( 'Configure your AI provider to enable the AI Financial Coach feature. The AI Coach provides personalized financial advice and insights based on your data.', 'dedebtify' ); ?>
        </p>

        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="ai_provider"><?php _e( 'AI Provider', 'dedebtify' ); ?></label>
                </th>
                <td>
                    <select name="ai_provider" id="ai_provider" class="regular-text">
                        <option value="openai" <?php selected( $ai_provider, 'openai' ); ?>>OpenAI (ChatGPT)</option>
                        <option value="anthropic" <?php selected( $ai_provider, 'anthropic' ); ?>>Anthropic (Claude)</option>
                        <option value="openrouter" <?php selected( $ai_provider, 'openrouter' ); ?>>OpenRouter (Multi-Provider)</option>
                    </select>
                    <p class="description">
                        <?php _e( 'Choose your preferred AI provider. OpenRouter provides access to multiple AI models through a single API key.', 'dedebtify' ); ?>
                    </p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="ai_api_key"><?php _e( 'API Key', 'dedebtify' ); ?></label>
                </th>
                <td>
                    <input type="password" id="ai_api_key" name="ai_api_key" value="<?php echo esc_attr( $ai_api_key ); ?>" class="regular-text" autocomplete="off">
                    <p class="description">
                        <?php _e( 'Get your API key from:', 'dedebtify' ); ?><br>
                        <strong>OpenAI:</strong> <a href="https://platform.openai.com/api-keys" target="_blank">platform.openai.com/api-keys</a><br>
                        <strong>Anthropic:</strong> <a href="https://console.anthropic.com" target="_blank">console.anthropic.com</a><br>
                        <strong>OpenRouter:</strong> <a href="https://openrouter.ai/keys" target="_blank">openrouter.ai/keys</a>
                    </p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="ai_model"><?php _e( 'AI Model', 'dedebtify' ); ?></label>
                </th>
                <td>
                    <select name="ai_model" id="ai_model" class="regular-text">
                        <!-- OpenAI Models -->
                        <optgroup label="OpenAI Models" id="openai-models">
                            <option value="gpt-4o" <?php selected( $ai_model, 'gpt-4o' ); ?>>GPT-4o (Recommended)</option>
                            <option value="gpt-4o-mini" <?php selected( $ai_model, 'gpt-4o-mini' ); ?>>GPT-4o Mini (Faster & Cheaper)</option>
                            <option value="gpt-4-turbo" <?php selected( $ai_model, 'gpt-4-turbo' ); ?>>GPT-4 Turbo</option>
                        </optgroup>
                        <!-- Anthropic Models -->
                        <optgroup label="Anthropic Models" id="anthropic-models">
                            <option value="claude-3-5-sonnet-20241022" <?php selected( $ai_model, 'claude-3-5-sonnet-20241022' ); ?>>Claude 3.5 Sonnet (Recommended)</option>
                            <option value="claude-3-5-haiku-20241022" <?php selected( $ai_model, 'claude-3-5-haiku-20241022' ); ?>>Claude 3.5 Haiku (Faster & Cheaper)</option>
                            <option value="claude-3-opus-20240229" <?php selected( $ai_model, 'claude-3-opus-20240229' ); ?>>Claude 3 Opus</option>
                        </optgroup>
                        <!-- OpenRouter Models -->
                        <optgroup label="OpenRouter - Claude" id="openrouter-anthropic-models">
                            <option value="anthropic/claude-3.5-sonnet" <?php selected( $ai_model, 'anthropic/claude-3.5-sonnet' ); ?>>Claude 3.5 Sonnet (Recommended)</option>
                            <option value="anthropic/claude-3.5-haiku" <?php selected( $ai_model, 'anthropic/claude-3.5-haiku' ); ?>>Claude 3.5 Haiku</option>
                            <option value="anthropic/claude-3-opus" <?php selected( $ai_model, 'anthropic/claude-3-opus' ); ?>>Claude 3 Opus</option>
                        </optgroup>
                        <optgroup label="OpenRouter - OpenAI" id="openrouter-openai-models">
                            <option value="openai/gpt-4o" <?php selected( $ai_model, 'openai/gpt-4o' ); ?>>GPT-4o</option>
                            <option value="openai/gpt-4o-mini" <?php selected( $ai_model, 'openai/gpt-4o-mini' ); ?>>GPT-4o Mini (Faster & Cheaper)</option>
                            <option value="openai/gpt-4-turbo" <?php selected( $ai_model, 'openai/gpt-4-turbo' ); ?>>GPT-4 Turbo</option>
                        </optgroup>
                        <optgroup label="OpenRouter - Google" id="openrouter-google-models">
                            <option value="google/gemini-pro-1.5" <?php selected( $ai_model, 'google/gemini-pro-1.5' ); ?>>Gemini 1.5 Pro</option>
                            <option value="google/gemini-flash-1.5" <?php selected( $ai_model, 'google/gemini-flash-1.5' ); ?>>Gemini 1.5 Flash (Faster)</option>
                        </optgroup>
                        <optgroup label="OpenRouter - Meta" id="openrouter-meta-models">
                            <option value="meta-llama/llama-3.1-70b-instruct" <?php selected( $ai_model, 'meta-llama/llama-3.1-70b-instruct' ); ?>>Llama 3.1 70B</option>
                            <option value="meta-llama/llama-3.1-405b-instruct" <?php selected( $ai_model, 'meta-llama/llama-3.1-405b-instruct' ); ?>>Llama 3.1 405B</option>
                        </optgroup>
                    </select>
                    <p class="description">
                        <?php _e( 'Select which AI model to use. OpenRouter provides access to models from multiple providers through a single API key. More advanced models provide better responses but may cost more.', 'dedebtify' ); ?>
                    </p>
                </td>
            </tr>
        </table>

        <div class="dedebtify-ai-status" style="background: #f0f0f1; padding: 15px; margin: 20px 0; border-radius: 4px;">
            <h4 style="margin-top: 0;"><?php _e( 'AI Coach Status', 'dedebtify' ); ?></h4>
            <?php if ( !empty( $ai_api_key ) ) : ?>
                <p>
                    <span class="dashicons dashicons-yes-alt" style="color: #00a32a;"></span>
                    <?php _e( 'API key configured. AI Coach is ready to help your users!', 'dedebtify' ); ?>
                </p>
            <?php else : ?>
                <p>
                    <span class="dashicons dashicons-warning" style="color: #dba617;"></span>
                    <?php _e( 'No API key configured. Users will see setup instructions when they visit the AI Coach page.', 'dedebtify' ); ?>
                </p>
            <?php endif; ?>
        </div>

        <div class="dedebtify-info-box" style="background: #e7f5fe; border-left: 4px solid #2271b1; padding: 12px; margin-top: 20px;">
            <p style="margin: 0;">
                <strong><?php _e( 'Privacy Note:', 'dedebtify' ); ?></strong>
                <?php _e( 'When users attach their financial data to conversations, it is sent to the selected AI provider for analysis. Ensure your privacy policy reflects this. No data is stored by DeDebtify beyond the user\'s browser.', 'dedebtify' ); ?>
            </p>
        </div>
    </div>

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
