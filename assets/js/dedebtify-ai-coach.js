/**
 * DeDebtify AI Coach JavaScript
 *
 * Handles the chat interface and AI interactions
 *
 * @since      1.0.0
 * @package    Dedebtify
 */

(function($) {
    'use strict';

    let chatHistory = [];
    let includeContext = false;
    let financialContext = null;

    $(document).ready(function() {
        if ($('.dedebtify-ai-coach').length) {
            initAICoach();
        }
    });

    /**
     * Initialize AI Coach
     */
    function initAICoach() {
        loadChatHistory();
        initChatInput();
        initSuggestedPrompts();
        initContextToggle();
        initClearChat();
        loadFinancialContext();
    }

    /**
     * Initialize chat input and form submission
     */
    function initChatInput() {
        const $input = $('#dd-chat-input');
        const $submit = $('#dd-chat-submit');
        const $form = $('#dd-chat-form');

        // Auto-resize textarea
        $input.on('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';

            // Enable/disable submit button
            $submit.prop('disabled', !this.value.trim());
        });

        // Submit on Enter (Shift+Enter for new line)
        $input.on('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                if ($(this).val().trim()) {
                    $form.trigger('submit');
                }
            }
        });

        // Form submission
        $form.on('submit', function(e) {
            e.preventDefault();
            const message = $input.val().trim();
            if (message) {
                sendMessage(message);
                $input.val('').css('height', 'auto');
                $submit.prop('disabled', true);
            }
        });
    }

    /**
     * Initialize suggested prompts
     */
    function initSuggestedPrompts() {
        $('.dd-suggested-prompt').on('click', function() {
            const prompt = $(this).data('prompt');
            sendMessage(prompt);
        });
    }

    /**
     * Initialize context toggle
     */
    function initContextToggle() {
        $('#dd-attach-context').on('click', function() {
            includeContext = !includeContext;
            $(this).toggleClass('active');
            $('.dd-context-indicator').toggle(includeContext);

            if (includeContext && !financialContext) {
                loadFinancialContext();
            }
        });
    }

    /**
     * Initialize clear chat
     */
    function initClearChat() {
        $('#dd-clear-chat').on('click', function() {
            if (confirm('Are you sure you want to clear your chat history? This cannot be undone.')) {
                clearChatHistory();
            }
        });
    }

    /**
     * Load financial context
     */
    function loadFinancialContext() {
        Promise.all([
            $.ajax({
                url: dedebtify.restUrl + 'dashboard',
                method: 'GET',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', dedebtify.restNonce);
                }
            }),
            $.ajax({
                url: dedebtify.restUrl + 'credit-cards',
                method: 'GET',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', dedebtify.restNonce);
                }
            }),
            $.ajax({
                url: dedebtify.restUrl + 'loans',
                method: 'GET',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', dedebtify.restNonce);
                }
            }),
            $.ajax({
                url: dedebtify.restUrl + 'mortgages',
                method: 'GET',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', dedebtify.restNonce);
                }
            })
        ]).then(function(results) {
            const [dashboard, cards, loans, mortgages] = results;

            financialContext = {
                dashboard: dashboard,
                credit_cards: cards,
                loans: loans,
                mortgages: mortgages,
                summary: {
                    total_debt: parseFloat(dashboard.total_debt) || 0,
                    monthly_payments: parseFloat(dashboard.monthly_payments) || 0,
                    dti_ratio: parseFloat(dashboard.dti_ratio) || 0,
                    credit_utilization: parseFloat(dashboard.credit_utilization) || 0,
                    num_cards: cards.length,
                    num_loans: loans.length,
                    num_mortgages: mortgages.length
                }
            };
        }).catch(function(error) {
            console.error('Failed to load financial context:', error);
        });
    }

    /**
     * Send message to AI
     */
    function sendMessage(userMessage) {
        // Hide welcome, show messages
        $('#dd-chat-welcome').hide();
        $('#dd-chat-messages').show();

        // Add user message to chat
        addMessage(userMessage, 'user');

        // Show loading indicator
        $('#dd-chat-loading').show();
        scrollToBottom();

        // Prepare request data
        const requestData = {
            message: userMessage,
            include_context: includeContext,
            context: includeContext ? financialContext : null,
            history: chatHistory.slice(-10) // Last 10 messages for context
        };

        // Send to backend
        $.ajax({
            url: dedebtify.restUrl + 'ai-coach',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', dedebtify.restNonce);
            },
            success: function(response) {
                $('#dd-chat-loading').hide();
                addMessage(response.message, 'assistant');
                scrollToBottom();
            },
            error: function(xhr) {
                $('#dd-chat-loading').hide();
                let errorMessage = 'I apologize, but I encountered an error processing your request. Please try again.';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                addMessage(errorMessage, 'assistant');
                scrollToBottom();
            }
        });
    }

    /**
     * Add message to chat
     */
    function addMessage(message, role) {
        const timestamp = new Date().toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit'
        });

        const avatarIcon = role === 'user' ? 'admin-users' : 'welcome-learn-more';

        const $message = $('<div>')
            .addClass('dd-chat-message')
            .addClass('dd-chat-message-' + role);

        const $avatar = $('<div>')
            .addClass('dd-chat-avatar')
            .html('<span class="dashicons dashicons-' + avatarIcon + '"></span>');

        const $bubble = $('<div>')
            .addClass('dd-chat-bubble')
            .html(formatMessage(message));

        $message.append($avatar).append($bubble);

        $('#dd-chat-messages').append($message);

        // Add to chat history
        chatHistory.push({
            role: role,
            content: message,
            timestamp: Date.now()
        });

        saveChatHistory();
    }

    /**
     * Format message (support markdown-like formatting)
     */
    function formatMessage(text) {
        // Convert line breaks
        let formatted = text.replace(/\n/g, '<br>');

        // Bold text (**text** or __text__)
        formatted = formatted.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        formatted = formatted.replace(/__(.*?)__/g, '<strong>$1</strong>');

        // Italic text (*text* or _text_)
        formatted = formatted.replace(/\*(.*?)\*/g, '<em>$1</em>');
        formatted = formatted.replace(/_(.*?)_/g, '<em>$1</em>');

        // Lists (simple conversion)
        formatted = formatted.replace(/^- (.+)$/gm, '<li>$1</li>');
        formatted = formatted.replace(/^(\d+)\. (.+)$/gm, '<li>$2</li>');

        // Wrap consecutive <li> in <ul>
        formatted = formatted.replace(/(<li>.*<\/li>)/gs, function(match) {
            return '<ul>' + match + '</ul>';
        });

        // Code blocks (```code```)
        formatted = formatted.replace(/```([\s\S]*?)```/g, '<pre><code>$1</code></pre>');

        // Inline code (`code`)
        formatted = formatted.replace(/`([^`]+)`/g, '<code>$1</code>');

        return formatted;
    }

    /**
     * Scroll to bottom of chat
     */
    function scrollToBottom() {
        const $messages = $('#dd-chat-messages');
        $messages.scrollTop($messages[0].scrollHeight);
    }

    /**
     * Save chat history to localStorage
     */
    function saveChatHistory() {
        try {
            localStorage.setItem('dedebtify_chat_history', JSON.stringify(chatHistory));
        } catch (e) {
            console.error('Failed to save chat history:', e);
        }
    }

    /**
     * Load chat history from localStorage
     */
    function loadChatHistory() {
        try {
            const saved = localStorage.getItem('dedebtify_chat_history');
            if (saved) {
                chatHistory = JSON.parse(saved);

                if (chatHistory.length > 0) {
                    $('#dd-chat-welcome').hide();
                    $('#dd-chat-messages').show();

                    // Render saved messages
                    chatHistory.forEach(function(msg) {
                        const avatarIcon = msg.role === 'user' ? 'admin-users' : 'welcome-learn-more';

                        const $message = $('<div>')
                            .addClass('dd-chat-message')
                            .addClass('dd-chat-message-' + msg.role);

                        const $avatar = $('<div>')
                            .addClass('dd-chat-avatar')
                            .html('<span class="dashicons dashicons-' + avatarIcon + '"></span>');

                        const $bubble = $('<div>')
                            .addClass('dd-chat-bubble')
                            .html(formatMessage(msg.content));

                        $message.append($avatar).append($bubble);
                        $('#dd-chat-messages').append($message);
                    });

                    scrollToBottom();
                }
            }
        } catch (e) {
            console.error('Failed to load chat history:', e);
        }
    }

    /**
     * Clear chat history
     */
    function clearChatHistory() {
        chatHistory = [];
        localStorage.removeItem('dedebtify_chat_history');
        $('#dd-chat-messages').empty().hide();
        $('#dd-chat-welcome').show();
    }

    /**
     * Export chat conversation
     */
    $('#dd-export-chat').on('click', function() {
        if (chatHistory.length === 0) {
            alert('No conversation to export.');
            return;
        }

        let exportText = 'DeDebtify AI Coach Conversation\n';
        exportText += '================================\n\n';

        chatHistory.forEach(function(msg) {
            const role = msg.role === 'user' ? 'You' : 'AI Coach';
            const date = new Date(msg.timestamp).toLocaleString();
            exportText += `[${date}] ${role}:\n${msg.content}\n\n`;
        });

        const blob = new Blob([exportText], { type: 'text/plain' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'dedebtify-ai-conversation-' + Date.now() + '.txt';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    });

})(jQuery);
