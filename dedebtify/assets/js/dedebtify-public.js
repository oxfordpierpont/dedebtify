/**
 * DeDebtify Public JavaScript
 *
 * @since      1.0.0
 * @package    Dedebtify
 */

(function($) {
    'use strict';

    $(document).ready(function() {

        // State management
        let creditCardsData = [];
        let currentSort = 'balance-high';
        let currentFilter = 'all';

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
         * Initialize Credit Card Manager
         */
        function initCreditCardManager() {
            if ($('.dedebtify-credit-cards-manager').length) {
                loadCreditCardsList();
                initCreditCardForm();
                initCreditCardControls();
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

        /**
         * =========================================
         * CREDIT CARD MANAGER FUNCTIONS
         * =========================================
         */

        /**
         * Load credit cards list
         */
        function loadCreditCardsList() {
            const $container = $('#dedebtify-credit-cards-list');
            if (!$container.length) return;

            showLoading($container);

            $.ajax({
                url: dedebtify.restUrl + 'credit-cards',
                method: 'GET',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', dedebtify.restNonce);
                },
                success: function(response) {
                    creditCardsData = response;
                    updateCreditCardStats(response);
                    renderCreditCardsList();
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
         * Update credit card statistics
         */
        function updateCreditCardStats(cards) {
            let totalDebt = 0;
            let totalLimit = 0;
            let totalPayment = 0;

            cards.forEach(function(card) {
                if (card.status === 'active') {
                    totalDebt += parseFloat(card.balance) || 0;
                    totalLimit += parseFloat(card.credit_limit) || 0;
                    totalPayment += (parseFloat(card.minimum_payment) || 0) + (parseFloat(card.extra_payment) || 0);
                }
            });

            const utilization = totalLimit > 0 ? (totalDebt / totalLimit * 100) : 0;

            $('#cc-total-debt').text(formatCurrency(totalDebt));
            $('#cc-utilization').text(formatPercentage(utilization));
            $('#cc-monthly-payment').text(formatCurrency(totalPayment));
        }

        /**
         * Render credit cards list
         */
        function renderCreditCardsList() {
            const $container = $('#dedebtify-credit-cards-list');
            const $emptyState = $('#empty-state');

            // Filter and sort
            let filteredCards = creditCardsData.filter(function(card) {
                if (currentFilter === 'all') return true;
                return card.status === currentFilter;
            });

            // Sort
            filteredCards.sort(function(a, b) {
                switch(currentSort) {
                    case 'balance-high':
                        return parseFloat(b.balance) - parseFloat(a.balance);
                    case 'balance-low':
                        return parseFloat(a.balance) - parseFloat(b.balance);
                    case 'rate-high':
                        return parseFloat(b.interest_rate) - parseFloat(a.interest_rate);
                    case 'rate-low':
                        return parseFloat(a.interest_rate) - parseFloat(b.interest_rate);
                    case 'utilization-high':
                        return parseFloat(b.utilization) - parseFloat(a.utilization);
                    default:
                        return 0;
                }
            });

            if (filteredCards.length === 0) {
                $container.hide();
                $emptyState.show();
                return;
            }

            $container.show();
            $emptyState.hide();

            let html = '';
            filteredCards.forEach(function(card) {
                html += renderCreditCardItem(card);
            });

            $container.html(html);
        }

        /**
         * Render single credit card item
         */
        function renderCreditCardItem(card) {
            const utilization = parseFloat(card.utilization) || 0;
            const utilizationClass = getUtilizationClass(utilization);
            const statusBadgeClass = card.status === 'active' ? 'success' : (card.status === 'paid_off' ? 'info' : 'secondary');

            let html = '<div class="dedebtify-card-item" data-card-id="' + card.id + '">';

            // Header
            html += '  <div class="dedebtify-card-header">';
            html += '    <h3 class="dedebtify-card-name">' + escapeHtml(card.name) + '</h3>';
            html += '    <div class="dedebtify-card-actions">';
            html += '      <span class="dedebtify-badge dedebtify-badge-' + statusBadgeClass + '">' + card.status.replace('_', ' ').toUpperCase() + '</span>';
            html += '      <button class="dedebtify-btn-icon dedebtify-btn-edit" data-card-id="' + card.id + '" title="Edit"><span class="dashicons dashicons-edit"></span></button>';
            html += '      <button class="dedebtify-btn-icon dedebtify-btn-delete" data-card-id="' + card.id + '" title="Delete"><span class="dashicons dashicons-trash"></span></button>';
            html += '    </div>';
            html += '  </div>';

            // Details grid
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
            html += '      <div class="dedebtify-card-detail-label">Interest Rate</div>';
            html += '      <div class="dedebtify-card-detail-value">' + formatPercentage(card.interest_rate) + '</div>';
            html += '    </div>';
            html += '    <div class="dedebtify-card-detail">';
            html += '      <div class="dedebtify-card-detail-label">Monthly Payment</div>';
            html += '      <div class="dedebtify-card-detail-value">' + formatCurrency((parseFloat(card.minimum_payment) || 0) + (parseFloat(card.extra_payment) || 0)) + '</div>';
            html += '    </div>';
            html += '  </div>';

            // Utilization progress bar
            html += '  <div class="dedebtify-card-utilization">';
            html += '    <div class="dedebtify-card-utilization-label">Utilization: ' + formatPercentage(utilization) + '</div>';
            html += '    <div class="dedebtify-progress">';
            html += '      <div class="dedebtify-progress-bar dedebtify-progress-' + utilizationClass + '" style="width: ' + Math.min(utilization, 100) + '%"></div>';
            html += '    </div>';
            html += '  </div>';

            // Payoff scenarios
            if (card.status === 'active' && parseFloat(card.balance) > 0) {
                html += '  <div class="dedebtify-card-payoff">';
                html += '    <h4>Payoff Scenarios:</h4>';
                html += '    <div class="dedebtify-payoff-scenarios">';

                // Scenario 1: Minimum only
                if (card.payoff_minimum) {
                    html += '      <div class="dedebtify-payoff-scenario">';
                    html += '        <div class="scenario-label">Minimum Only:</div>';
                    html += '        <div class="scenario-value">' + card.payoff_minimum.months + ' months | Interest: ' + formatCurrency(card.payoff_minimum.interest) + '</div>';
                    html += '      </div>';
                }

                // Scenario 2: Minimum + Extra
                if (card.payoff_with_extra) {
                    html += '      <div class="dedebtify-payoff-scenario">';
                    html += '        <div class="scenario-label">With Extra Payment:</div>';
                    html += '        <div class="scenario-value">' + card.payoff_with_extra.months + ' months | Interest: ' + formatCurrency(card.payoff_with_extra.interest) + '</div>';
                    html += '      </div>';
                }

                // Savings
                if (card.payoff_minimum && card.payoff_with_extra) {
                    const savings = parseFloat(card.payoff_minimum.interest) - parseFloat(card.payoff_with_extra.interest);
                    if (savings > 0) {
                        html += '      <div class="dedebtify-payoff-savings">';
                        html += '        <strong>You save ' + formatCurrency(savings) + ' in interest!</strong>';
                        html += '      </div>';
                    }
                }

                html += '    </div>';
                html += '  </div>';
            }

            html += '</div>';
            return html;
        }

        /**
         * Initialize credit card form
         */
        function initCreditCardForm() {
            const $form = $('#dedebtify-credit-card-form');
            if (!$form.length) return;

            // Load existing card data if editing
            const postId = $form.data('post-id');
            if (postId && postId > 0) {
                loadCreditCardData(postId);
            }

            // Real-time payoff preview
            $form.find('input[name="balance"], input[name="interest_rate"], input[name="minimum_payment"], input[name="extra_payment"], input[name="credit_limit"]').on('input', function() {
                updatePayoffPreview();
            });

            // Form submission
            $form.on('submit', function(e) {
                e.preventDefault();
                saveCreditCard();
            });
        }

        /**
         * Load credit card data for editing
         */
        function loadCreditCardData(postId) {
            $.ajax({
                url: dedebtify.restUrl + 'credit-cards/' + postId,
                method: 'GET',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', dedebtify.restNonce);
                },
                success: function(card) {
                    // Populate form fields
                    $('#card_name').val(card.name);
                    $('#balance').val(card.balance);
                    $('#credit_limit').val(card.credit_limit);
                    $('#interest_rate').val(card.interest_rate);
                    $('#minimum_payment').val(card.minimum_payment);
                    $('#extra_payment').val(card.extra_payment || '');
                    $('#due_date').val(card.due_date || '');
                    $('#status').val(card.status);
                    $('#auto_pay').prop('checked', card.auto_pay == '1');

                    // Update preview
                    updatePayoffPreview();
                },
                error: function() {
                    showMessage('Failed to load credit card data', 'error');
                }
            });
        }

        /**
         * Update payoff preview in real-time
         */
        function updatePayoffPreview() {
            const balance = parseFloat($('#balance').val()) || 0;
            const creditLimit = parseFloat($('#credit_limit').val()) || 0;
            const interestRate = parseFloat($('#interest_rate').val()) || 0;
            const minimumPayment = parseFloat($('#minimum_payment').val()) || 0;
            const extraPayment = parseFloat($('#extra_payment').val()) || 0;

            if (balance <= 0 || interestRate <= 0 || minimumPayment <= 0) {
                $('#dedebtify-payoff-preview').hide();
                return;
            }

            // Calculate utilization
            const utilization = creditLimit > 0 ? (balance / creditLimit * 100) : 0;

            // Calculate months to payoff
            const monthlyPayment = minimumPayment + extraPayment;
            const monthlyRate = (interestRate / 100) / 12;
            const monthlyInterest = balance * monthlyRate;

            let months = 'Never';
            let totalInterest = 0;
            let payoffDate = '—';

            if (monthlyPayment > monthlyInterest) {
                const numerator = Math.log(1 - (balance * monthlyRate / monthlyPayment));
                const denominator = Math.log(1 + monthlyRate);
                months = Math.ceil(-numerator / denominator);
                totalInterest = (monthlyPayment * months) - balance;

                // Calculate payoff date
                const date = new Date();
                date.setMonth(date.getMonth() + months);
                payoffDate = date.toLocaleDateString('en-US', { year: 'numeric', month: 'short' });
            }

            // Update preview
            $('#preview-utilization').text(formatPercentage(utilization));
            $('#preview-months').text(months === 'Never' ? months : months + ' months');
            $('#preview-interest').text(formatCurrency(totalInterest));
            $('#preview-date').text(payoffDate);

            $('#dedebtify-payoff-preview').show();
        }

        /**
         * Save credit card (create or update)
         */
        function saveCreditCard() {
            const $form = $('#dedebtify-credit-card-form');
            const postId = $form.data('post-id');
            const isEdit = postId && postId > 0;

            const formData = {
                name: $('#card_name').val(),
                balance: parseFloat($('#balance').val()) || 0,
                credit_limit: parseFloat($('#credit_limit').val()) || 0,
                interest_rate: parseFloat($('#interest_rate').val()) || 0,
                minimum_payment: parseFloat($('#minimum_payment').val()) || 0,
                extra_payment: parseFloat($('#extra_payment').val()) || 0,
                due_date: parseInt($('#due_date').val()) || 0,
                status: $('#status').val(),
                auto_pay: $('#auto_pay').is(':checked') ? 1 : 0
            };

            // Client-side validation
            if (!formData.name) {
                showMessage('Please enter a card name', 'error');
                return;
            }
            if (formData.balance < 0) {
                showMessage('Balance cannot be negative', 'error');
                return;
            }
            if (formData.credit_limit <= 0) {
                showMessage('Credit limit must be greater than 0', 'error');
                return;
            }

            const $submitBtn = $form.find('button[type="submit"]');
            const originalText = $submitBtn.text();
            $submitBtn.prop('disabled', true).text('Saving...');

            const url = isEdit ? dedebtify.restUrl + 'credit-cards/' + postId : dedebtify.restUrl + 'credit-cards';
            const method = isEdit ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                method: method,
                contentType: 'application/json',
                data: JSON.stringify(formData),
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', dedebtify.restNonce);
                },
                success: function(response) {
                    showMessage(isEdit ? 'Credit card updated successfully!' : 'Credit card added successfully!', 'success');

                    // Redirect to list view
                    setTimeout(function() {
                        window.location.href = window.location.pathname + '?action=list';
                    }, 1500);
                },
                error: function(xhr) {
                    const error = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to save credit card';
                    showMessage(error, 'error');
                },
                complete: function() {
                    $submitBtn.prop('disabled', false).text(originalText);
                }
            });
        }

        /**
         * Initialize credit card controls (sort/filter)
         */
        function initCreditCardControls() {
            // Sort
            $('#sort-by').on('change', function() {
                currentSort = $(this).val();
                renderCreditCardsList();
            });

            // Filter
            $('#filter-status').on('change', function() {
                currentFilter = $(this).val();
                renderCreditCardsList();
            });
        }

        /**
         * Handle edit button click
         */
        $(document).on('click', '.dedebtify-btn-edit', function(e) {
            e.preventDefault();
            const cardId = $(this).data('card-id');
            window.location.href = window.location.pathname + '?action=edit&edit=' + cardId;
        });

        /**
         * Handle delete button click
         */
        $(document).on('click', '.dedebtify-btn-delete', function(e) {
            e.preventDefault();

            const cardId = $(this).data('card-id');
            const $cardItem = $(this).closest('.dedebtify-card-item');
            const cardName = $cardItem.find('.dedebtify-card-name').text();

            if (!confirm('Are you sure you want to delete "' + cardName + '"? This action cannot be undone.')) {
                return;
            }

            const $btn = $(this);
            $btn.prop('disabled', true);

            $.ajax({
                url: dedebtify.restUrl + 'credit-cards/' + cardId,
                method: 'DELETE',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', dedebtify.restNonce);
                },
                success: function(response) {
                    showMessage('Credit card deleted successfully', 'success');

                    // Remove from UI with animation
                    $cardItem.fadeOut(400, function() {
                        $(this).remove();
                        // Reload data to update stats
                        loadCreditCardsList();
                    });
                },
                error: function(xhr) {
                    const error = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to delete credit card';
                    showMessage(error, 'error');
                    $btn.prop('disabled', false);
                }
            });
        });

        // Initialize
        initDashboard();
        initCreditCardManager();

    });

})(jQuery);
