<?php
/**
 * AI Financial Coach Service
 *
 * Handles AI-powered financial coaching and advice
 *
 * @since      1.0.0
 * @package    Dedebtify
 * @subpackage Dedebtify/includes
 */

class Dedebtify_AI_Coach {

    /**
     * AI Provider API Key
     */
    private $api_key;

    /**
     * AI Provider endpoint
     */
    private $api_endpoint;

    /**
     * AI Model to use
     */
    private $model;

    /**
     * System prompt for the AI coach
     */
    private $system_prompt;

    /**
     * Initialize the AI Coach
     */
    public function __construct() {
        // Get settings from options
        $this->api_key = get_option( 'dedebtify_ai_api_key', '' );
        $provider = get_option( 'dedebtify_ai_provider', 'openai' );

        // Set provider-specific settings
        switch ( $provider ) {
            case 'anthropic':
                $this->api_endpoint = 'https://api.anthropic.com/v1/messages';
                $this->model = get_option( 'dedebtify_ai_model', 'claude-3-5-sonnet-20241022' );
                break;

            case 'openrouter':
                $this->api_endpoint = 'https://openrouter.ai/api/v1/chat/completions';
                $this->model = get_option( 'dedebtify_ai_model', 'anthropic/claude-3.5-sonnet' );
                break;

            case 'openai':
            default:
                $this->api_endpoint = 'https://api.openai.com/v1/chat/completions';
                $this->model = get_option( 'dedebtify_ai_model', 'gpt-4o' );
                break;
        }

        $this->system_prompt = $this->get_system_prompt();
    }

    /**
     * Get the system prompt for the AI coach
     */
    private function get_system_prompt() {
        return "You are an expert AI Financial Coach integrated into DeDebtify, a debt management WordPress plugin. Your role is to provide personalized, empathetic, and actionable financial advice to users.

Key Responsibilities:
1. Analyze users' financial situations when they share their data
2. Provide clear, actionable recommendations for debt reduction
3. Educate users about personal finance concepts in simple terms
4. Create strategic debt payoff plans (avalanche, snowball methods)
5. Offer encouragement and motivation for financial goals
6. Explain credit scores, budgeting, investing, and saving strategies
7. Help users make informed financial decisions

Guidelines:
- Be empathetic and non-judgmental about users' financial situations
- Provide specific, actionable advice rather than vague suggestions
- Use simple language and avoid excessive financial jargon
- When analyzing financial data, provide clear breakdowns and insights
- Always emphasize that your advice is educational and users should consult certified financial professionals for personalized legal/tax advice
- Focus on sustainable, realistic strategies
- Celebrate progress and encourage small wins
- Be honest about challenges while remaining optimistic

When users share their financial data:
- Analyze total debt, monthly payments, DTI ratio, and credit utilization
- Identify high-interest debts that should be prioritized
- Suggest realistic payoff strategies based on their situation
- Point out potential red flags (high utilization, high DTI)
- Recommend specific next steps

Format your responses with:
- Clear headings and bullet points when appropriate
- Numbered steps for action plans
- Bold text for key points
- Keep responses conversational but professional";
    }

    /**
     * Process a chat message and get AI response
     *
     * @param string $message User's message
     * @param array $context User's financial context (optional)
     * @param array $history Chat history for context (optional)
     * @return string AI's response
     */
    public function get_response( $message, $context = null, $history = array() ) {
        // Check if API key is set
        if ( empty( $this->api_key ) ) {
            return $this->get_setup_message();
        }

        // Build the messages array
        $messages = $this->build_messages( $message, $context, $history );

        // Get provider
        $provider = get_option( 'dedebtify_ai_provider', 'openai' );

        // Make API request
        if ( $provider === 'anthropic' ) {
            return $this->call_anthropic_api( $messages );
        } elseif ( $provider === 'openrouter' ) {
            return $this->call_openrouter_api( $messages );
        } else {
            return $this->call_openai_api( $messages );
        }
    }

    /**
     * Build messages array with context
     */
    private function build_messages( $message, $context, $history ) {
        $messages = array();

        // Add chat history if provided
        if ( ! empty( $history ) ) {
            foreach ( $history as $msg ) {
                if ( isset( $msg['role'] ) && isset( $msg['content'] ) ) {
                    $messages[] = array(
                        'role' => $msg['role'] === 'user' ? 'user' : 'assistant',
                        'content' => $msg['content']
                    );
                }
            }
        }

        // Add current message with optional financial context
        $user_message = $message;

        if ( ! empty( $context ) && isset( $context['summary'] ) ) {
            $user_message = $this->format_context( $context ) . "\n\n" . $message;
        }

        $messages[] = array(
            'role' => 'user',
            'content' => $user_message
        );

        return $messages;
    }

    /**
     * Format financial context for AI
     */
    private function format_context( $context ) {
        $summary = $context['summary'];

        $context_text = "[User's Current Financial Data]\n\n";
        $context_text .= "Total Debt: $" . number_format( $summary['total_debt'], 2 ) . "\n";
        $context_text .= "Monthly Debt Payments: $" . number_format( $summary['monthly_payments'], 2 ) . "\n";
        $context_text .= "Debt-to-Income Ratio: " . number_format( $summary['dti_ratio'], 1 ) . "%\n";
        $context_text .= "Credit Utilization: " . number_format( $summary['credit_utilization'], 1 ) . "%\n";
        $context_text .= "Number of Credit Cards: " . $summary['num_cards'] . "\n";
        $context_text .= "Number of Loans: " . $summary['num_loans'] . "\n";
        $context_text .= "Number of Mortgages: " . $summary['num_mortgages'] . "\n\n";

        // Add credit card details if available
        if ( ! empty( $context['credit_cards'] ) && count( $context['credit_cards'] ) > 0 ) {
            $context_text .= "Credit Cards:\n";
            foreach ( $context['credit_cards'] as $card ) {
                $context_text .= "- " . $card['name'] . ": $" . number_format( $card['balance'], 2 );
                $context_text .= " at " . number_format( $card['interest_rate'], 2 ) . "% APR";
                $context_text .= " (Utilization: " . number_format( $card['utilization'], 1 ) . "%)\n";
            }
            $context_text .= "\n";
        }

        // Add loan details if available
        if ( ! empty( $context['loans'] ) && count( $context['loans'] ) > 0 ) {
            $context_text .= "Loans:\n";
            foreach ( $context['loans'] as $loan ) {
                $context_text .= "- " . $loan['name'] . " (" . ucfirst( $loan['loan_type'] ) . "): $";
                $context_text .= number_format( $loan['current_balance'], 2 );
                $context_text .= " at " . number_format( $loan['interest_rate'], 2 ) . "% APR\n";
            }
            $context_text .= "\n";
        }

        $context_text .= "[End of Financial Data]\n\nUser's Question:";

        return $context_text;
    }

    /**
     * Call OpenAI API
     */
    private function call_openai_api( $messages ) {
        $request_body = array(
            'model' => $this->model,
            'messages' => array_merge(
                array(
                    array(
                        'role' => 'system',
                        'content' => $this->system_prompt
                    )
                ),
                $messages
            ),
            'temperature' => 0.7,
            'max_tokens' => 2000
        );

        $response = wp_remote_post( $this->api_endpoint, array(
            'headers' => array(
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->api_key
            ),
            'body' => json_encode( $request_body ),
            'timeout' => 60
        ) );

        if ( is_wp_error( $response ) ) {
            error_log( 'DeDebtify AI Coach Error: ' . $response->get_error_message() );
            return 'I apologize, but I\'m having trouble connecting to my AI service right now. Please try again in a moment.';
        }

        $body = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( isset( $body['error'] ) ) {
            error_log( 'DeDebtify AI Coach API Error: ' . json_encode( $body['error'] ) );
            return 'I encountered an error while processing your request. Please make sure the API key is configured correctly in settings.';
        }

        if ( isset( $body['choices'][0]['message']['content'] ) ) {
            return $body['choices'][0]['message']['content'];
        }

        return 'I apologize, but I couldn\'t generate a response. Please try again.';
    }

    /**
     * Call Anthropic API
     */
    private function call_anthropic_api( $messages ) {
        $request_body = array(
            'model' => $this->model,
            'max_tokens' => 2000,
            'system' => $this->system_prompt,
            'messages' => $messages
        );

        $response = wp_remote_post( $this->api_endpoint, array(
            'headers' => array(
                'Content-Type' => 'application/json',
                'x-api-key' => $this->api_key,
                'anthropic-version' => '2023-06-01'
            ),
            'body' => json_encode( $request_body ),
            'timeout' => 60
        ) );

        if ( is_wp_error( $response ) ) {
            error_log( 'DeDebtify AI Coach Error: ' . $response->get_error_message() );
            return 'I apologize, but I\'m having trouble connecting to my AI service right now. Please try again in a moment.';
        }

        $body = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( isset( $body['error'] ) ) {
            error_log( 'DeDebtify AI Coach API Error: ' . json_encode( $body['error'] ) );
            return 'I encountered an error while processing your request. Please make sure the API key is configured correctly in settings.';
        }

        if ( isset( $body['content'][0]['text'] ) ) {
            return $body['content'][0]['text'];
        }

        return 'I apologize, but I couldn\'t generate a response. Please try again.';
    }

    /**
     * Call OpenRouter API
     */
    private function call_openrouter_api( $messages ) {
        $request_body = array(
            'model' => $this->model,
            'messages' => array_merge(
                array(
                    array(
                        'role' => 'system',
                        'content' => $this->system_prompt
                    )
                ),
                $messages
            ),
            'temperature' => 0.7,
            'max_tokens' => 2000
        );

        // Get site URL for OpenRouter headers
        $site_url = get_site_url();
        $site_name = get_bloginfo( 'name' );

        $response = wp_remote_post( $this->api_endpoint, array(
            'headers' => array(
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->api_key,
                'HTTP-Referer' => $site_url,
                'X-Title' => $site_name . ' - DeDebtify AI Coach'
            ),
            'body' => json_encode( $request_body ),
            'timeout' => 60
        ) );

        if ( is_wp_error( $response ) ) {
            error_log( 'DeDebtify AI Coach Error: ' . $response->get_error_message() );
            return 'I apologize, but I\'m having trouble connecting to my AI service right now. Please try again in a moment.';
        }

        $body = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( isset( $body['error'] ) ) {
            error_log( 'DeDebtify AI Coach API Error: ' . json_encode( $body['error'] ) );
            return 'I encountered an error while processing your request. Please make sure the API key is configured correctly in settings.';
        }

        if ( isset( $body['choices'][0]['message']['content'] ) ) {
            return $body['choices'][0]['message']['content'];
        }

        return 'I apologize, but I couldn\'t generate a response. Please try again.';
    }

    /**
     * Get setup message when API is not configured
     */
    private function get_setup_message() {
        $settings_url = admin_url( 'admin.php?page=dedebtify-settings#ai-coach' );

        return "👋 Welcome to DeDebtify AI Coach!\n\n" .
               "To start using the AI Financial Coach, you'll need to configure your AI provider settings.\n\n" .
               "**Setup Steps:**\n" .
               "1. Go to DeDebtify Settings > AI Coach\n" .
               "2. Choose your AI provider (OpenAI, Anthropic, or OpenRouter)\n" .
               "3. Enter your API key\n" .
               "4. Select your preferred model\n" .
               "5. Save settings\n\n" .
               "**Need an API key?**\n" .
               "- OpenAI: Visit platform.openai.com/api-keys\n" .
               "- Anthropic: Visit console.anthropic.com\n" .
               "- OpenRouter: Visit openrouter.ai/keys\n\n" .
               "Once configured, I'll be ready to help you with personalized financial advice!";
    }

    /**
     * Test API connection
     */
    public function test_connection() {
        if ( empty( $this->api_key ) ) {
            return array(
                'success' => false,
                'message' => 'No API key configured'
            );
        }

        $test_message = 'Hello! Please respond with "Connection successful" if you can read this.';
        $response = $this->get_response( $test_message );

        if ( strpos( $response, 'trouble connecting' ) !== false ||
             strpos( $response, 'error' ) !== false ) {
            return array(
                'success' => false,
                'message' => $response
            );
        }

        return array(
            'success' => true,
            'message' => 'AI Coach is ready to help!',
            'response' => $response
        );
    }
}
