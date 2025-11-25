<?php
/**
 * AI Financial Coach Template
 *
 * This template displays the AI-powered financial coaching chat interface.
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
    echo '<p>' . __( 'Please log in to chat with your AI Financial Coach.', 'dedebtify' ) . '</p>';
    return;
}

$user_id = get_current_user_id();
$user_info = get_userdata( $user_id );
?>

<!-- Navigation -->
<?php Dedebtify_Helpers::render_navigation( 'ai_coach' ); ?>

<div class="dedebtify-dashboard dedebtify-ai-coach">

    <div class="dedebtify-dashboard-header">
        <h1><?php _e( 'AI Financial Coach', 'dedebtify' ); ?></h1>
        <p><?php _e( 'Get personalized financial advice and coaching powered by AI', 'dedebtify' ); ?></p>
    </div>

    <!-- Chat Container -->
    <div class="dd-chat-container">

        <!-- Welcome Message (shown when chat is empty) -->
        <div id="dd-chat-welcome" class="dd-chat-welcome">
            <div class="dd-chat-welcome-content">
                <div class="dd-chat-welcome-icon">
                    <span class="dashicons dashicons-welcome-learn-more"></span>
                </div>
                <h2><?php printf( __( 'Hi %s! 👋', 'dedebtify' ), esc_html( $user_info->first_name ?: $user_info->display_name ) ); ?></h2>
                <p><?php _e( 'I\'m your AI Financial Coach. I can help you with:', 'dedebtify' ); ?></p>

                <div class="dd-coach-capabilities">
                    <div class="dd-capability-card">
                        <span class="dashicons dashicons-chart-line"></span>
                        <h3><?php _e( 'Financial Analysis', 'dedebtify' ); ?></h3>
                        <p><?php _e( 'Review your debt profile and provide insights', 'dedebtify' ); ?></p>
                    </div>
                    <div class="dd-capability-card">
                        <span class="dashicons dashicons-lightbulb"></span>
                        <h3><?php _e( 'Personalized Advice', 'dedebtify' ); ?></h3>
                        <p><?php _e( 'Get recommendations tailored to your situation', 'dedebtify' ); ?></p>
                    </div>
                    <div class="dd-capability-card">
                        <span class="dashicons dashicons-book"></span>
                        <h3><?php _e( 'Financial Education', 'dedebtify' ); ?></h3>
                        <p><?php _e( 'Learn about budgeting, investing, and more', 'dedebtify' ); ?></p>
                    </div>
                    <div class="dd-capability-card">
                        <span class="dashicons dashicons-calendar-alt"></span>
                        <h3><?php _e( 'Action Plans', 'dedebtify' ); ?></h3>
                        <p><?php _e( 'Create strategies to achieve your goals', 'dedebtify' ); ?></p>
                    </div>
                </div>

                <div class="dd-suggested-prompts">
                    <p class="dd-suggested-label"><?php _e( 'Try asking:', 'dedebtify' ); ?></p>
                    <button class="dd-suggested-prompt" data-prompt="<?php esc_attr_e( 'Analyze my current financial situation', 'dedebtify' ); ?>">
                        <?php _e( 'Analyze my current financial situation', 'dedebtify' ); ?>
                    </button>
                    <button class="dd-suggested-prompt" data-prompt="<?php esc_attr_e( 'What should I pay off first?', 'dedebtify' ); ?>">
                        <?php _e( 'What should I pay off first?', 'dedebtify' ); ?>
                    </button>
                    <button class="dd-suggested-prompt" data-prompt="<?php esc_attr_e( 'How can I improve my credit score?', 'dedebtify' ); ?>">
                        <?php _e( 'How can I improve my credit score?', 'dedebtify' ); ?>
                    </button>
                    <button class="dd-suggested-prompt" data-prompt="<?php esc_attr_e( 'Help me create a budget', 'dedebtify' ); ?>">
                        <?php _e( 'Help me create a budget', 'dedebtify' ); ?>
                    </button>
                </div>
            </div>
        </div>

        <!-- Messages Area -->
        <div id="dd-chat-messages" class="dd-chat-messages" style="display: none;">
            <!-- Messages will be dynamically inserted here -->
        </div>

        <!-- Loading Indicator -->
        <div id="dd-chat-loading" class="dd-chat-loading" style="display: none;">
            <div class="dd-chat-message dd-chat-message-assistant">
                <div class="dd-chat-avatar">
                    <span class="dashicons dashicons-welcome-learn-more"></span>
                </div>
                <div class="dd-chat-bubble">
                    <div class="dd-typing-indicator">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat Input -->
        <div class="dd-chat-input-container">
            <form id="dd-chat-form" class="dd-chat-form">
                <button type="button" id="dd-attach-context" class="dd-chat-attach-btn" title="<?php esc_attr_e( 'Include financial data', 'dedebtify' ); ?>">
                    <span class="dashicons dashicons-paperclip"></span>
                    <span class="dd-context-indicator" style="display: none;"><?php _e( 'Data attached', 'dedebtify' ); ?></span>
                </button>
                <div class="dd-chat-input-wrapper">
                    <textarea
                        id="dd-chat-input"
                        class="dd-chat-input"
                        placeholder="<?php esc_attr_e( 'Ask me anything about personal finance...', 'dedebtify' ); ?>"
                        rows="1"
                    ></textarea>
                    <button type="submit" id="dd-chat-submit" class="dd-chat-submit" disabled>
                        <span class="dashicons dashicons-arrow-up-alt2"></span>
                    </button>
                </div>
            </form>
            <div class="dd-chat-disclaimer">
                <span class="dashicons dashicons-info"></span>
                <?php _e( 'AI-generated advice is for educational purposes. Always consult a financial professional for personalized guidance.', 'dedebtify' ); ?>
            </div>
        </div>

    </div>

    <!-- Settings Panel (Optional) -->
    <div class="dd-chat-settings" style="display: none;">
        <button id="dd-clear-chat" class="dedebtify-btn dedebtify-btn-secondary">
            <span class="dashicons dashicons-trash"></span>
            <?php _e( 'Clear Chat History', 'dedebtify' ); ?>
        </button>
        <button id="dd-export-chat" class="dedebtify-btn dedebtify-btn-secondary">
            <span class="dashicons dashicons-download"></span>
            <?php _e( 'Export Conversation', 'dedebtify' ); ?>
        </button>
    </div>

</div>
