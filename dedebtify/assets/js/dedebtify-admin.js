/**
 * DeDebtify Admin JavaScript
 *
 * @since      1.0.0
 * @package    Dedebtify
 */

(function($) {
    'use strict';

    $(document).ready(function() {

        /**
         * Initialize admin dashboard
         */
        function initAdminDashboard() {
            if ($('.dedebtify-admin-dashboard').length) {
                loadDashboardStats();
                loadRecentActivity();

                // Refresh button
                $('#refresh-activity').on('click', function(e) {
                    e.preventDefault();
                    loadRecentActivity();
                });
            }
        }

        /**
         * Load dashboard statistics
         */
        function loadDashboardStats() {
            const $container = $('.dedebtify-admin-dashboard');
            if (!$container.length) return;

            $.ajax({
                url: dedebtifyAdmin.restUrl + 'stats',
                method: 'GET',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', dedebtifyAdmin.restNonce);
                },
                success: function(response) {
                    updateDashboardUI(response);
                },
                error: function(xhr, status, error) {
                    console.error('Failed to load dashboard data:', error);
                }
            });
        }

        /**
         * Update dashboard UI with data
         */
        function updateDashboardUI(data) {
            // Update widgets
            $('#total-users').text(data.total_users || 0);
            $('#total-debt').text(formatCurrency(data.total_debt || 0));
            $('#total-cards').text(data.total_credit_cards || 0);
            $('#total-loans').text(data.total_loans || 0);
            $('#total-goals').text(data.total_goals || 0);
            $('#total-snapshots').text(data.total_snapshots || 0);

            // Update system statistics
            $('#stat-credit-cards').text(data.total_credit_cards || 0);
            $('#stat-loans').text(data.total_loans || 0);
            $('#stat-bills').text(data.total_bills || 0);
            $('#stat-goals').text(data.total_goals || 0);
            $('#stat-snapshots').text(data.total_snapshots || 0);

            const totalItems = (data.total_credit_cards || 0) +
                              (data.total_loans || 0) +
                              (data.total_bills || 0) +
                              (data.total_goals || 0) +
                              (data.total_snapshots || 0);
            $('#stat-total-items').text(totalItems);
        }

        /**
         * Load recent activity
         */
        function loadRecentActivity() {
            const $container = $('#recent-activity-container');

            $container.html('<div class="dedebtify-loading"><span class="spinner is-active"></span><p>Loading recent activity...</p></div>');

            $.ajax({
                url: dedebtifyAdmin.restUrl + 'activity',
                method: 'GET',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', dedebtifyAdmin.restNonce);
                },
                success: function(response) {
                    renderRecentActivity(response);
                },
                error: function(xhr, status, error) {
                    console.error('Failed to load recent activity:', error);
                    $container.html('<p class="description">Failed to load recent activity</p>');
                }
            });
        }

        /**
         * Render recent activity
         */
        function renderRecentActivity(activities) {
            const $container = $('#recent-activity-container');

            if (!activities || activities.length === 0) {
                $container.html('<p class="description">No recent activity</p>');
                return;
            }

            let html = '<table class="wp-list-table widefat fixed striped">';
            html += '<thead><tr>';
            html += '<th>Type</th>';
            html += '<th>Item</th>';
            html += '<th>User</th>';
            html += '<th>Action</th>';
            html += '<th>Date</th>';
            html += '</tr></thead>';
            html += '<tbody>';

            activities.forEach(function(activity) {
                html += '<tr>';
                html += '<td><span class="dashicons dashicons-' + getActivityIcon(activity.type) + '"></span> ' + activity.type + '</td>';
                html += '<td>' + escapeHtml(activity.title) + '</td>';
                html += '<td>' + escapeHtml(activity.author) + '</td>';
                html += '<td>' + activity.action + '</td>';
                html += '<td>' + formatDate(activity.date) + '</td>';
                html += '</tr>';
            });

            html += '</tbody></table>';
            $container.html(html);
        }

        /**
         * Get icon for activity type
         */
        function getActivityIcon(type) {
            const icons = {
                'credit_card': 'admin-page',
                'loan': 'money-alt',
                'bill': 'clipboard',
                'goal': 'flag',
                'snapshot': 'camera'
            };
            return icons[type] || 'marker';
        }

        /**
         * Format date
         */
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
        }

        /**
         * Escape HTML
         */
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        /**
         * Apply color coding to credit utilization
         */
        function applyCreditUtilizationColor(util) {
            const $card = $('#dedebtify-credit-util').closest('.dedebtify-stat-card');
            $card.removeClass('success warning danger');

            if (util < 30) {
                $card.addClass('success');
            } else if (util < 50) {
                $card.addClass('warning');
            } else {
                $card.addClass('danger');
            }
        }

        /**
         * Apply color coding to DTI
         */
        function applyDTIColor(dti) {
            const $card = $('#dedebtify-dti').closest('.dedebtify-stat-card');
            $card.removeClass('success warning danger');

            if (dti < 36) {
                $card.addClass('success');
            } else if (dti < 43) {
                $card.addClass('warning');
            } else {
                $card.addClass('danger');
            }
        }

        /**
         * Create snapshot
         */
        $(document).on('click', '#dedebtify-create-snapshot', function(e) {
            e.preventDefault();

            const $btn = $(this);
            const originalText = $btn.text();

            $btn.prop('disabled', true).text('Creating...');

            $.ajax({
                url: dedebtifyAdmin.restUrl + 'snapshot',
                method: 'POST',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', dedebtifyAdmin.restNonce);
                },
                success: function(response) {
                    showNotice(response.message, 'success');
                    $btn.prop('disabled', false).text(originalText);

                    // Reload stats
                    loadDashboardStats();
                },
                error: function(xhr, status, error) {
                    showNotice('Failed to create snapshot', 'error');
                    $btn.prop('disabled', false).text(originalText);
                }
            });
        });

        /**
         * Format currency
         */
        function formatCurrency(amount) {
            return '$' + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }

        /**
         * Format percentage
         */
        function formatPercentage(value) {
            return parseFloat(value).toFixed(1) + '%';
        }

        /**
         * Show notice
         */
        function showNotice(message, type = 'info') {
            const $notice = $('<div class="dedebtify-notice ' + type + '">' + message + '</div>');
            $('.dedebtify-admin-dashboard').prepend($notice);

            setTimeout(function() {
                $notice.fadeOut(function() {
                    $(this).remove();
                });
            }, 5000);
        }

        /**
         * Form validation
         */
        $('.dedebtify-meta-box form').on('submit', function(e) {
            const $form = $(this);
            let isValid = true;

            // Validate required fields
            $form.find('[required]').each(function() {
                if (!$(this).val()) {
                    isValid = false;
                    $(this).addClass('error');
                } else {
                    $(this).removeClass('error');
                }
            });

            // Validate number fields
            $form.find('input[type="number"]').each(function() {
                const val = parseFloat($(this).val());
                const min = parseFloat($(this).attr('min'));
                const max = parseFloat($(this).attr('max'));

                if (min !== undefined && val < min) {
                    isValid = false;
                    $(this).addClass('error');
                }

                if (max !== undefined && val > max) {
                    isValid = false;
                    $(this).addClass('error');
                }
            });

            if (!isValid) {
                e.preventDefault();
                showNotice('Please fill in all required fields correctly', 'error');
            }
        });

        /**
         * Calculate loan payment automatically
         */
        $('#dd_principal, #dd_interest_rate, #dd_term_months').on('change', function() {
            const principal = parseFloat($('#dd_principal').val()) || 0;
            const annualRate = parseFloat($('#dd_interest_rate').val()) || 0;
            const termMonths = parseInt($('#dd_term_months').val()) || 0;

            if (principal > 0 && termMonths > 0) {
                const monthlyPayment = calculateLoanPayment(principal, annualRate, termMonths);
                $('#dd_monthly_payment').val(monthlyPayment.toFixed(2));
            }
        });

        /**
         * Calculate loan payment
         */
        function calculateLoanPayment(principal, annualRate, termMonths) {
            const monthlyRate = (annualRate / 100) / 12;

            if (monthlyRate === 0) {
                return principal / termMonths;
            }

            const payment = principal *
                           (monthlyRate * Math.pow(1 + monthlyRate, termMonths)) /
                           (Math.pow(1 + monthlyRate, termMonths) - 1);

            return payment;
        }

        // Initialize
        initAdminDashboard();
    });

})(jQuery);
