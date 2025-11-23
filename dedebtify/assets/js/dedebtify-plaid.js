/**
 * DeDebtify Plaid Integration JavaScript
 *
 * Handles Plaid Link and account syncing
 *
 * @since      1.0.0
 * @package    Dedebtify
 */

(function($) {
    'use strict';

    let plaidLinkHandler = null;

    $(document).ready(function() {
        if ($('.dedebtify-account-sync').length) {
            initAccountSync();
        }
    });

    /**
     * Initialize Account Sync page
     */
    function initAccountSync() {
        loadLinkedAccounts();
        initLinkButtons();
        loadPlaidScript();
    }

    /**
     * Load Plaid Link script
     */
    function loadPlaidScript() {
        if (typeof Plaid === 'undefined') {
            const script = document.createElement('script');
            script.src = 'https://cdn.plaid.com/link/v2/stable/link-initialize.js';
            script.async = true;
            document.head.appendChild(script);
        }
    }

    /**
     * Initialize link account buttons
     */
    function initLinkButtons() {
        $('#dd-link-account-btn, #dd-link-first-account-btn').on('click', function() {
            openPlaidLink();
        });
    }

    /**
     * Load linked accounts
     */
    function loadLinkedAccounts() {
        $.ajax({
            url: dedebtify.restUrl + 'plaid/linked-accounts',
            method: 'GET',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', dedebtify.restNonce);
            },
            success: function(accounts) {
                renderLinkedAccounts(accounts);
            },
            error: function(xhr, status, error) {
                console.error('Failed to load linked accounts:', error);
                $('#dd-linked-accounts-list').html('<div class="dedebtify-message error">Failed to load accounts</div>');
            }
        });
    }

    /**
     * Render linked accounts
     */
    function renderLinkedAccounts(accounts) {
        const $list = $('#dd-linked-accounts-list');
        const $noAccounts = $('#dd-no-accounts');

        if (accounts.length === 0) {
            $list.hide();
            $noAccounts.show();
            return;
        }

        $noAccounts.hide();
        $list.show();

        let html = '';
        accounts.forEach(function(account) {
            const connectedDate = new Date(account.connected_at).toLocaleDateString();
            const lastSync = account.last_sync ? new Date(account.last_sync).toLocaleString() : 'Never';

            html += '<div class="dd-linked-account-item" data-item-id="' + escapeHtml(account.item_id) + '">';
            html += '  <div class="dd-account-info">';
            html += '    <div class="dd-account-icon">';
            html += '      <span class="dashicons dashicons-bank"></span>';
            html += '    </div>';
            html += '    <div class="dd-account-details">';
            html += '      <h3>Linked Account</h3>';
            html += '      <div class="dd-account-meta">';
            html += '        <span>Connected: ' + escapeHtml(connectedDate) + '</span> • ';
            html += '        <span>Last Synced: ' + escapeHtml(lastSync) + '</span>';
            html += '      </div>';
            html += '    </div>';
            html += '  </div>';
            html += '  <div class="dd-account-actions">';
            html += '    <button class="dedebtify-btn dedebtify-btn-sm dd-sync-account-btn" data-item-id="' + escapeHtml(account.item_id) + '">';
            html += '      <span class="dashicons dashicons-update"></span> Sync Now';
            html += '    </button>';
            html += '    <button class="dedebtify-btn dedebtify-btn-sm dedebtify-btn-secondary dd-disconnect-account-btn" data-item-id="' + escapeHtml(account.item_id) + '">';
            html += '      <span class="dashicons dashicons-no"></span> Disconnect';
            html += '    </button>';
            html += '  </div>';
            html += '</div>';
        });

        $list.html(html);

        // Attach event listeners
        $('.dd-sync-account-btn').on('click', function() {
            syncAccounts();
        });

        $('.dd-disconnect-account-btn').on('click', function() {
            const itemId = $(this).data('item-id');
            disconnectAccount(itemId);
        });
    }

    /**
     * Open Plaid Link
     */
    function openPlaidLink() {
        // Show loading
        showMessage('Initializing secure connection...', 'info');

        // Get link token from backend
        $.ajax({
            url: dedebtify.restUrl + 'plaid/create-link-token',
            method: 'POST',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', dedebtify.restNonce);
            },
            success: function(response) {
                if (response.link_token) {
                    initializePlaidLink(response.link_token);
                } else {
                    showMessage('Failed to initialize Plaid Link', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Failed to create link token:', error);
                let errorMsg = 'Failed to initialize connection';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                showMessage(errorMsg, 'error');
            }
        });
    }

    /**
     * Initialize Plaid Link with token
     */
    function initializePlaidLink(linkToken) {
        if (typeof Plaid === 'undefined') {
            showMessage('Plaid Link is not loaded. Please refresh the page.', 'error');
            return;
        }

        plaidLinkHandler = Plaid.create({
            token: linkToken,
            onSuccess: function(public_token, metadata) {
                console.log('Plaid Link success:', metadata);
                exchangePublicToken(public_token);
            },
            onExit: function(err, metadata) {
                if (err != null) {
                    console.error('Plaid Link error:', err);
                    showMessage('Failed to link account: ' + err.error_message, 'error');
                }
            },
            onEvent: function(eventName, metadata) {
                console.log('Plaid Link event:', eventName, metadata);
            }
        });

        plaidLinkHandler.open();
    }

    /**
     * Exchange public token for access token
     */
    function exchangePublicToken(publicToken) {
        showMessage('Connecting your account...', 'info');

        $.ajax({
            url: dedebtify.restUrl + 'plaid/exchange-token',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                public_token: publicToken
            }),
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', dedebtify.restNonce);
            },
            success: function(response) {
                showMessage('Account linked successfully! Syncing your data...', 'success');
                // Reload accounts after a short delay
                setTimeout(function() {
                    loadLinkedAccounts();
                }, 2000);
            },
            error: function(xhr, status, error) {
                console.error('Failed to exchange token:', error);
                let errorMsg = 'Failed to link account';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                showMessage(errorMsg, 'error');
            }
        });
    }

    /**
     * Sync all accounts
     */
    function syncAccounts() {
        showMessage('Syncing your accounts...', 'info');

        $.ajax({
            url: dedebtify.restUrl + 'plaid/sync',
            method: 'POST',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', dedebtify.restNonce);
            },
            success: function(response) {
                showMessage(response.message, 'success');
                loadLinkedAccounts();
            },
            error: function(xhr, status, error) {
                console.error('Failed to sync accounts:', error);
                showMessage('Failed to sync accounts', 'error');
            }
        });
    }

    /**
     * Disconnect an account
     */
    function disconnectAccount(itemId) {
        if (!confirm('Are you sure you want to disconnect this account? Your data will remain, but automatic syncing will stop.')) {
            return;
        }

        $.ajax({
            url: dedebtify.restUrl + 'plaid/disconnect',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                item_id: itemId
            }),
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', dedebtify.restNonce);
            },
            success: function(response) {
                showMessage('Account disconnected', 'success');
                loadLinkedAccounts();
            },
            error: function(xhr, status, error) {
                console.error('Failed to disconnect account:', error);
                showMessage('Failed to disconnect account', 'error');
            }
        });
    }

    /**
     * Show message to user
     */
    function showMessage(message, type) {
        const $message = $('<div>')
            .addClass('dedebtify-message')
            .addClass(type)
            .html('<p>' + escapeHtml(message) + '</p>');

        $('.dedebtify-dashboard').prepend($message);

        setTimeout(function() {
            $message.fadeOut(function() {
                $(this).remove();
            });
        }, 5000);
    }

    /**
     * Escape HTML
     */
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
    }

})(jQuery);
