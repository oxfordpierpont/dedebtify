/**
 * DeDebtify Public JavaScript
 *
 * @since      1.0.0
 * @package    Dedebtify
 */

(function($) {
    'use strict';

    $(document).ready(function() {

        /**
         * Initialize dashboard
         */
        function initDashboard() {
            if ($('.dedebtify-dashboard').length) {
                loadDashboardData();
                loadCreditCards();
                loadLoans();
                loadBills();
                loadGoals();
            }
        }

        /**
         * Load dashboard data
         */
        function loadDashboardData() {
            showLoading($('#dedebtify-dashboard-stats'));

            $.ajax({
                url: dedebtify.restUrl + 'dashboard',
                method: 'GET',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', dedebtify.restNonce);
                },
                success: function(response) {
                    updateDashboardStats(response);
                },
                error: function(xhr, status, error) {
                    console.error('Failed to load dashboard data:', error);
                    showMessage('Failed to load dashboard data', 'error');
                },
                complete: function() {
                    hideLoading($('#dedebtify-dashboard-stats'));
                }
            });
        }

        /**
         * Update dashboard statistics
         */
        function updateDashboardStats(data) {
            $('#dd-total-debt').text(formatCurrency(data.total_debt));
            $('#dd-monthly-payments').text(formatCurrency(data.monthly_payments));
            $('#dd-monthly-bills').text(formatCurrency(data.monthly_bills));
            $('#dd-dti-ratio').text(formatPercentage(data.dti_ratio));
            $('#dd-credit-utilization').text(formatPercentage(data.credit_utilization));

            // Update progress bars if they exist
            updateProgressBar($('#dd-dti-progress'), data.dti_ratio);
            updateProgressBar($('#dd-credit-util-progress'), data.credit_utilization);

            // Apply color coding
            applyCreditUtilizationColor(data.credit_utilization);
            applyDTIColor(data.dti_ratio);
        }

        /**
         * Load credit cards
         */
        function loadCreditCards() {
            const $container = $('#dedebtify-credit-cards');
            if (!$container.length) return;

            showLoading($container);

            $.ajax({
                url: dedebtify.restUrl + 'credit-cards',
                method: 'GET',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', dedebtify.restNonce);
                },
                success: function(response) {
                    renderCreditCards(response, $container);
                },
                error: function(xhr, status, error) {
                    console.error('Failed to load credit cards:', error);
                    $container.html('<p class="dedebtify-message error">Failed to load credit cards</p>');
                },
                complete: function() {
                    hideLoading($container);
                }
            });
        }

        /**
         * Render credit cards
         */
        function renderCreditCards(cards, $container) {
            if (cards.length === 0) {
                $container.html('<p>No credit cards found. <a href="#">Add your first card</a></p>');
                return;
            }

            let html = '';
            cards.forEach(function(card) {
                html += '<div class="dedebtify-card-item">';
                html += '  <div class="dedebtify-card-header">';
                html += '    <h3 class="dedebtify-card-name">' + escapeHtml(card.name) + '</h3>';
                html += '    <span class="dedebtify-card-badge ' + card.status + '">' + card.status.replace('_', ' ') + '</span>';
                html += '  </div>';
                html += '  <div class="dedebtify-card-details">';
                html += '    <div class="dedebtify-card-detail">';
                html += '      <div class="dedebtify-card-detail-label">Balance</div>';
                html += '      <div class="dedebtify-card-detail-value">' + formatCurrency(card.balance) + '</div>';
                html += '    </div>';
                html += '    <div class="dedebtify-card-detail">';
                html += '      <div class="dedebtify-card-detail-label">Limit</div>';
                html += '      <div class="dedebtify-card-detail-value">' + formatCurrency(card.credit_limit) + '</div>';
                html += '    </div>';
                html += '    <div class="dedebtify-card-detail">';
                html += '      <div class="dedebtify-card-detail-label">Utilization</div>';
                html += '      <div class="dedebtify-card-detail-value">' + formatPercentage(card.utilization) + '</div>';
                html += '    </div>';
                html += '    <div class="dedebtify-card-detail">';
                html += '      <div class="dedebtify-card-detail-label">APR</div>';
                html += '      <div class="dedebtify-card-detail-value">' + formatPercentage(card.interest_rate) + '</div>';
                html += '    </div>';
                html += '  </div>';

                // Progress bar
                html += '  <div class="dedebtify-progress">';
                html += '    <div class="dedebtify-progress-bar ' + getUtilizationClass(card.utilization) + '" style="width: ' + card.utilization + '%"></div>';
                html += '  </div>';

                // Payoff info
                if (card.months_to_payoff !== Infinity) {
                    html += '  <div class="dedebtify-card-payoff">';
                    html += '    <p><strong>Payoff:</strong> ' + card.months_to_payoff + ' months (' + escapeHtml(card.payoff_date) + ')</p>';
                    html += '    <p><strong>Interest:</strong> ' + formatCurrency(card.total_interest) + '</p>';
                    html += '  </div>';
                }

                html += '</div>';
            });

            $container.html(html);
        }

        /**
         * Load loans
         */
        function loadLoans() {
            const $container = $('#dedebtify-loans');
            if (!$container.length) return;

            showLoading($container);

            $.ajax({
                url: dedebtify.restUrl + 'loans',
                method: 'GET',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', dedebtify.restNonce);
                },
                success: function(response) {
                    renderLoans(response, $container);
                },
                error: function(xhr, status, error) {
                    console.error('Failed to load loans:', error);
                    $container.html('<p class="dedebtify-message error">Failed to load loans</p>');
                },
                complete: function() {
                    hideLoading($container);
                }
            });
        }

        /**
         * Render loans
         */
        function renderLoans(loans, $container) {
            if (loans.length === 0) {
                $container.html('<p>No loans found. <a href="#">Add your first loan</a></p>');
                return;
            }

            let html = '<div class="dedebtify-card-list">';
            loans.forEach(function(loan) {
                html += '<div class="dedebtify-card-item">';
                html += '  <div class="dedebtify-card-header">';
                html += '    <h3 class="dedebtify-card-name">' + escapeHtml(loan.name) + '</h3>';
                html += '    <span class="dedebtify-card-badge">' + loan.type + '</span>';
                html += '  </div>';
                html += '  <div class="dedebtify-card-details">';
                html += '    <div class="dedebtify-card-detail">';
                html += '      <div class="dedebtify-card-detail-label">Balance</div>';
                html += '      <div class="dedebtify-card-detail-value">' + formatCurrency(loan.balance) + '</div>';
                html += '    </div>';
                html += '    <div class="dedebtify-card-detail">';
                html += '      <div class="dedebtify-card-detail-label">Payment</div>';
                html += '      <div class="dedebtify-card-detail-value">' + formatCurrency(loan.monthly_payment) + '</div>';
                html += '    </div>';
                html += '    <div class="dedebtify-card-detail">';
                html += '      <div class="dedebtify-card-detail-label">Rate</div>';
                html += '      <div class="dedebtify-card-detail-value">' + formatPercentage(loan.interest_rate) + '</div>';
                html += '    </div>';
                html += '    <div class="dedebtify-card-detail">';
                html += '      <div class="dedebtify-card-detail-label">Payoff</div>';
                html += '      <div class="dedebtify-card-detail-value">' + escapeHtml(loan.payoff_date) + '</div>';
                html += '    </div>';
                html += '  </div>';
                html += '</div>';
            });
            html += '</div>';

            $container.html(html);
        }

        /**
         * Load bills
         */
        function loadBills() {
            const $container = $('#dedebtify-bills');
            if (!$container.length) return;

            showLoading($container);

            $.ajax({
                url: dedebtify.restUrl + 'bills',
                method: 'GET',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', dedebtify.restNonce);
                },
                success: function(response) {
                    renderBills(response, $container);
                },
                error: function(xhr, status, error) {
                    console.error('Failed to load bills:', error);
                },
                complete: function() {
                    hideLoading($container);
                }
            });
        }

        /**
         * Render bills
         */
        function renderBills(bills, $container) {
            if (bills.length === 0) {
                $container.html('<p>No bills found. <a href="#">Add your first bill</a></p>');
                return;
            }

            let html = '<div class="dedebtify-card-list">';
            bills.forEach(function(bill) {
                html += '<div class="dedebtify-card-item">';
                html += '  <h3 class="dedebtify-card-name">' + escapeHtml(bill.name) + '</h3>';
                html += '  <p><strong>Category:</strong> ' + bill.category + ' | <strong>Amount:</strong> ' + formatCurrency(bill.amount) + ' (' + bill.frequency + ')</p>';
                html += '  <p><strong>Monthly:</strong> ' + formatCurrency(bill.monthly_equivalent) + '</p>';
                html += '</div>';
            });
            html += '</div>';

            $container.html(html);
        }

        /**
         * Load goals
         */
        function loadGoals() {
            const $container = $('#dedebtify-goals');
            if (!$container.length) return;

            showLoading($container);

            $.ajax({
                url: dedebtify.restUrl + 'goals',
                method: 'GET',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', dedebtify.restNonce);
                },
                success: function(response) {
                    renderGoals(response, $container);
                },
                error: function(xhr, status, error) {
                    console.error('Failed to load goals:', error);
                },
                complete: function() {
                    hideLoading($container);
                }
            });
        }

        /**
         * Render goals
         */
        function renderGoals(goals, $container) {
            if (goals.length === 0) {
                $container.html('<p>No goals found. <a href="#">Add your first goal</a></p>');
                return;
            }

            let html = '<div class="dedebtify-card-list">';
            goals.forEach(function(goal) {
                html += '<div class="dedebtify-card-item">';
                html += '  <h3 class="dedebtify-card-name">' + escapeHtml(goal.name) + '</h3>';
                html += '  <div class="dedebtify-progress">';
                html += '    <div class="dedebtify-progress-bar success" style="width: ' + goal.progress_percentage + '%"></div>';
                html += '  </div>';
                html += '  <p>' + formatPercentage(goal.progress_percentage) + ' (' + formatCurrency(goal.current_amount) + ' / ' + formatCurrency(goal.target_amount) + ')</p>';
                html += '  <p><strong>Monthly:</strong> ' + formatCurrency(goal.monthly_contribution) + ' | <strong>Remaining:</strong> ' + goal.months_to_goal + ' months</p>';
                html += '</div>';
            });
            html += '</div>';

            $container.html(html);
        }

        /**
         * Create snapshot
         */
        $(document).on('click', '.dedebtify-create-snapshot', function(e) {
            e.preventDefault();

            const $btn = $(this);
            const originalText = $btn.text();

            $btn.prop('disabled', true).text('Creating...');

            $.ajax({
                url: dedebtify.restUrl + 'snapshot',
                method: 'POST',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', dedebtify.restNonce);
                },
                success: function(response) {
                    showMessage(response.message, 'success');
                    loadDashboardData();
                },
                error: function(xhr, status, error) {
                    showMessage('Failed to create snapshot', 'error');
                },
                complete: function() {
                    $btn.prop('disabled', false).text(originalText);
                }
            });
        });

        /**
         * Helper Functions
         */

        function showLoading($element) {
            $element.html('<div class="dedebtify-loading"><div class="dedebtify-spinner"></div></div>');
        }

        function hideLoading($element) {
            $element.find('.dedebtify-loading').remove();
        }

        function formatCurrency(amount) {
            return '$' + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }

        function formatPercentage(value) {
            return parseFloat(value).toFixed(1) + '%';
        }

        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }

        function updateProgressBar($bar, value) {
            if ($bar.length) {
                $bar.css('width', value + '%');
            }
        }

        function getUtilizationClass(util) {
            if (util < 30) return 'success';
            if (util < 50) return 'warning';
            return 'danger';
        }

        function applyCreditUtilizationColor(util) {
            const $card = $('#dd-credit-utilization').closest('.dedebtify-stat-card');
            $card.removeClass('success warning danger');
            $card.addClass(getUtilizationClass(util));
        }

        function applyDTIColor(dti) {
            const $card = $('#dd-dti-ratio').closest('.dedebtify-stat-card');
            $card.removeClass('success warning danger');

            if (dti < 36) {
                $card.addClass('success');
            } else if (dti < 43) {
                $card.addClass('warning');
            } else {
                $card.addClass('danger');
            }
        }

        function showMessage(message, type) {
            const $message = $('<div class="dedebtify-message ' + type + '">' + message + '</div>');
            $('.dedebtify-dashboard').prepend($message);

            setTimeout(function() {
                $message.fadeOut(function() {
                    $(this).remove();
                });
            }, 5000);
        }

        // Initialize
        initDashboard();

    });

})(jQuery);
