/**
 * DeDebtify Managers JavaScript
 *
 * Handles all CRUD operations for financial items
 *
 * @since      1.0.0
 * @package    Dedebtify
 */

(function($) {
    'use strict';

    $(document).ready(function() {

        // ===========================
        // CREDIT CARD MANAGER
        // ===========================

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
         * Load credit cards list
         */
        function loadCreditCardsList() {
            const $list = $('#dedebtify-credit-cards-list');
            if (!$list.length) return;

            $.ajax({
                url: dedebtify.restUrl + 'credit-cards',
                method: 'GET',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', dedebtify.restNonce);
                },
                success: function(response) {
                    renderCreditCardsList(response);
                    updateCreditCardStats(response);
                },
                error: function(xhr, status, error) {
                    console.error('Failed to load credit cards:', error);
                    $list.html('<div class="dedebtify-message error">Failed to load credit cards</div>');
                }
            });
        }

        /**
         * Render credit cards list
         */
        function renderCreditCardsList(cards) {
            const $list = $('#dedebtify-credit-cards-list');
            const $emptyState = $('#empty-state');

            if (cards.length === 0) {
                $list.hide();
                $emptyState.show();
                return;
            }

            $emptyState.hide();
            $list.show();

            let html = '';
            cards.forEach(function(card) {
                const utilClass = getUtilizationClass(card.utilization);
                const statusClass = card.status.replace('_', '-');

                html += '<div class="dedebtify-card-item" data-id="' + card.id + '" data-status="' + card.status + '">';
                html += '  <div class="dedebtify-card-header">';
                html += '    <div>';
                html += '      <h3 class="dedebtify-card-name">' + escapeHtml(card.name) + '</h3>';
                html += '      <span class="dedebtify-card-badge ' + statusClass + '">' + card.status.replace('_', ' ') + '</span>';
                html += '    </div>';
                html += '    <div class="dedebtify-card-actions">';
                html += '      <button class="dedebtify-btn dedebtify-btn-small" onclick="window.location=\'?action=edit&edit=' + card.id + '\'">' + dedebtifyL10n.edit + '</button>';
                html += '      <button class="dedebtify-btn dedebtify-btn-small dedebtify-btn-danger dedebtify-delete-card" data-id="' + card.id + '">' + dedebtifyL10n.delete + '</button>';
                html += '    </div>';
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
                html += '      <div class="dedebtify-card-detail-value ' + utilClass + '">' + formatPercentage(card.utilization) + '</div>';
                html += '    </div>';
                html += '    <div class="dedebtify-card-detail">';
                html += '      <div class="dedebtify-card-detail-label">APR</div>';
                html += '      <div class="dedebtify-card-detail-value">' + formatPercentage(card.interest_rate) + '</div>';
                html += '    </div>';
                html += '    <div class="dedebtify-card-detail">';
                html += '      <div class="dedebtify-card-detail-label">Monthly Payment</div>';
                html += '      <div class="dedebtify-card-detail-value">' + formatCurrency(card.minimum_payment + card.extra_payment) + '</div>';
                html += '    </div>';
                html += '  </div>';

                // Utilization progress bar
                html += '  <div class="dedebtify-progress">';
                html += '    <div class="dedebtify-progress-bar ' + utilClass + '" style="width: ' + Math.min(card.utilization, 100) + '%"></div>';
                html += '  </div>';

                // Payoff info
                if (card.status === 'active' && card.months_to_payoff !== Infinity) {
                    html += '  <div class="dedebtify-card-payoff">';
                    html += '    <div class="dedebtify-payoff-info">';
                    html += '      <strong>Payoff Timeline:</strong> ' + card.months_to_payoff + ' months (' + escapeHtml(card.payoff_date) + ')';
                    html += '    </div>';
                    html += '    <div class="dedebtify-payoff-info">';
                    html += '      <strong>Total Interest:</strong> ' + formatCurrency(card.total_interest);
                    html += '    </div>';
                    html += '  </div>';
                } else if (card.months_to_payoff === Infinity) {
                    html += '  <div class="dedebtify-card-payoff warning">';
                    html += '    <strong>⚠️ Warning:</strong> Payment doesn\'t cover interest. Increase your payment.';
                    html += '  </div>';
                }

                html += '</div>';
            });

            $list.html(html);
        }

        /**
         * Update credit card stats
         */
        function updateCreditCardStats(cards) {
            let totalDebt = 0;
            let totalBalance = 0;
            let totalLimit = 0;
            let totalPayment = 0;

            cards.forEach(function(card) {
                if (card.status === 'active') {
                    totalDebt += parseFloat(card.balance);
                    totalBalance += parseFloat(card.balance);
                    totalLimit += parseFloat(card.credit_limit);
                    totalPayment += parseFloat(card.minimum_payment) + parseFloat(card.extra_payment);
                }
            });

            const utilization = totalLimit > 0 ? (totalBalance / totalLimit) * 100 : 0;

            $('#cc-total-debt').text(formatCurrency(totalDebt));
            $('#cc-utilization').text(formatPercentage(utilization));
            $('#cc-monthly-payment').text(formatCurrency(totalPayment));
        }

        /**
         * Initialize credit card form
         */
        function initCreditCardForm() {
            const $form = $('#dedebtify-credit-card-form');
            if (!$form.length) return;

            // Load existing data if editing
            const postId = $form.data('post-id');
            if (postId > 0) {
                loadCreditCardData(postId);
            }

            // Real-time payoff calculation
            $('#balance, #interest_rate, #minimum_payment, #extra_payment, #credit_limit').on('input', function() {
                calculatePayoffPreview();
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
                url: dedebtify.restUrl + 'credit-cards',
                method: 'GET',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', dedebtify.restNonce);
                },
                success: function(response) {
                    const card = response.find(c => c.id === postId);
                    if (card) {
                        populateCreditCardForm(card);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to load card data:', error);
                    showMessage('Failed to load card data', 'error');
                }
            });
        }

        /**
         * Populate form with card data
         */
        function populateCreditCardForm(card) {
            $('#card_name').val(card.name);
            $('#balance').val(card.balance);
            $('#credit_limit').val(card.credit_limit);
            $('#interest_rate').val(card.interest_rate);
            $('#minimum_payment').val(card.minimum_payment);
            $('#extra_payment').val(card.extra_payment);
            $('#due_date').val(card.due_date);
            $('#status').val(card.status);
            $('#auto_pay').prop('checked', card.auto_pay == 1);

            calculatePayoffPreview();
        }

        /**
         * Calculate and display payoff preview
         */
        function calculatePayoffPreview() {
            const balance = parseFloat($('#balance').val()) || 0;
            const creditLimit = parseFloat($('#credit_limit').val()) || 0;
            const interestRate = parseFloat($('#interest_rate').val()) || 0;
            const minPayment = parseFloat($('#minimum_payment').val()) || 0;
            const extraPayment = parseFloat($('#extra_payment').val()) || 0;
            const totalPayment = minPayment + extraPayment;

            if (balance > 0 && totalPayment > 0) {
                // Calculate utilization
                const utilization = DedebtifyCalculator.calculateUtilization(balance, creditLimit);

                // Calculate payoff
                const months = DedebtifyCalculator.calculateMonthsToPayoff(balance, interestRate, totalPayment);
                const totalInterest = DedebtifyCalculator.calculateTotalInterest(balance, totalPayment, months);
                const payoffDate = DedebtifyCalculator.calculatePayoffDate(months);

                // Update preview
                $('#preview-utilization').text(DedebtifyCalculator.formatPercentage(utilization));
                $('#preview-months').text(months === Infinity ? '∞' : months);
                $('#preview-interest').text(DedebtifyCalculator.formatCurrency(totalInterest));
                $('#preview-date').text(payoffDate);

                $('#dedebtify-payoff-preview').slideDown();
            } else {
                $('#dedebtify-payoff-preview').slideUp();
            }
        }

        /**
         * Save credit card
         */
        function saveCreditCard() {
            const $form = $('#dedebtify-credit-card-form');
            const $submitBtn = $form.find('button[type="submit"]');
            const originalText = $submitBtn.text();
            const postId = $form.data('post-id');

            // Validate
            if (!$form[0].checkValidity()) {
                $form[0].reportValidity();
                return;
            }

            // Get form data
            const formData = {
                title: $('#card_name').val(),
                status: 'publish',
                author: dedebtify.userId,
                meta: {
                    balance: $('#balance').val(),
                    credit_limit: $('#credit_limit').val(),
                    interest_rate: $('#interest_rate').val(),
                    minimum_payment: $('#minimum_payment').val(),
                    extra_payment: $('#extra_payment').val() || 0,
                    due_date: $('#due_date').val() || '',
                    status: $('#status').val(),
                    auto_pay: $('#auto_pay').is(':checked') ? '1' : '0'
                }
            };

            $submitBtn.prop('disabled', true).text('Saving...');

            // Determine method and URL
            const method = postId > 0 ? 'PUT' : 'POST';
            const url = dedebtify.restUrl.replace('/dedebtify/v1/', '/wp/v2/') + 'dd_credit_card' + (postId > 0 ? '/' + postId : '');

            $.ajax({
                url: url,
                method: method,
                data: JSON.stringify(formData),
                contentType: 'application/json',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', dedebtify.restNonce);
                },
                success: function(response) {
                    showMessage(postId > 0 ? 'Card updated successfully!' : 'Card added successfully!', 'success');
                    setTimeout(function() {
                        window.location.href = '?action=list';
                    }, 1500);
                },
                error: function(xhr, status, error) {
                    console.error('Failed to save card:', error);
                    showMessage('Failed to save card. Please try again.', 'error');
                    $submitBtn.prop('disabled', false).text(originalText);
                }
            });
        }

        /**
         * Initialize credit card controls (sort, filter)
         */
        function initCreditCardControls() {
            $('#sort-by, #filter-status').on('change', function() {
                filterAndSortCards();
            });
        }

        /**
         * Filter and sort cards
         */
        function filterAndSortCards() {
            const sortBy = $('#sort-by').val();
            const filterStatus = $('#filter-status').val();
            const $cards = $('.dedebtify-card-item');

            // Filter
            $cards.each(function() {
                const status = $(this).data('status');
                if (filterStatus === 'all' || filterStatus === status) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });

            // Sort
            const $list = $('#dedebtify-credit-cards-list');
            const $visibleCards = $cards.filter(':visible');

            $visibleCards.sort(function(a, b) {
                const $a = $(a);
                const $b = $(b);

                // Extract values based on sort criteria
                let aVal, bVal;

                if (sortBy.includes('balance')) {
                    aVal = parseFloat($a.find('.dedebtify-card-detail-value').first().text().replace(/[^0-9.-]+/g,""));
                    bVal = parseFloat($b.find('.dedebtify-card-detail-value').first().text().replace(/[^0-9.-]+/g,""));
                }

                if (sortBy.includes('high')) {
                    return bVal - aVal;
                } else {
                    return aVal - bVal;
                }
            });

            $list.html($visibleCards);
        }

        /**
         * Delete credit card
         */
        $(document).on('click', '.dedebtify-delete-card', function(e) {
            e.preventDefault();

            if (!confirm('Are you sure you want to delete this credit card? This action cannot be undone.')) {
                return;
            }

            const cardId = $(this).data('id');
            const $cardItem = $(this).closest('.dedebtify-card-item');

            $.ajax({
                url: dedebtify.restUrl.replace('/dedebtify/v1/', '/wp/v2/') + 'dd_credit_card/' + cardId,
                method: 'DELETE',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', dedebtify.restNonce);
                },
                success: function(response) {
                    $cardItem.fadeOut(function() {
                        $(this).remove();
                        // Check if list is empty
                        if ($('.dedebtify-card-item').length === 0) {
                            $('#dedebtify-credit-cards-list').hide();
                            $('#empty-state').show();
                        }
                    });
                    showMessage('Credit card deleted successfully', 'success');
                },
                error: function(xhr, status, error) {
                    console.error('Failed to delete card:', error);
                    showMessage('Failed to delete card. Please try again.', 'error');
                }
            });
        });

        // ===========================
        // HELPER FUNCTIONS
        // ===========================

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
            return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
        }

        function getUtilizationClass(util) {
            if (util < 30) return 'success';
            if (util < 50) return 'warning';
            return 'danger';
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
        initCreditCardManager();

    });

})(jQuery);

// Localization object (will be populated by wp_localize_script)
var dedebtifyL10n = dedebtifyL10n || {
    edit: 'Edit',
    delete: 'Delete',
    confirm_delete: 'Are you sure you want to delete this item?'
};

        // ===========================
        // LOANS MANAGER
        // ===========================

        function initLoansManager() {
            if ($('.dedebtify-loans-manager').length) {
                loadLoansList();
                initLoanForm();
                initLoanControls();
            }
        }

        function loadLoansList() {
            const $list = $('#dedebtify-loans-list');
            if (!$list.length) return;

            $.ajax({
                url: dedebtify.restUrl + 'loans',
                method: 'GET',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', dedebtify.restNonce);
                },
                success: function(response) {
                    renderLoansList(response);
                    updateLoanStats(response);
                },
                error: function(xhr, status, error) {
                    console.error('Failed to load loans:', error);
                    $list.html('<div class="dedebtify-message error">Failed to load loans</div>');
                }
            });
        }

        function renderLoansList(loans) {
            const $list = $('#dedebtify-loans-list');
            const $emptyState = $('#loans-empty-state');

            if (loans.length === 0) {
                $list.hide();
                $emptyState.show();
                return;
            }

            $emptyState.hide();
            $list.show();

            let html = '';
            loans.forEach(function(loan) {
                html += '<div class="dedebtify-card-item" data-id="' + loan.id + '" data-type="' + loan.type + '">';
                html += '  <div class="dedebtify-card-header">';
                html += '    <div>';
                html += '      <h3 class="dedebtify-card-name">' + escapeHtml(loan.name) + '</h3>';
                html += '      <span class="dedebtify-card-badge">' + loan.type + '</span>';
                html += '    </div>';
                html += '    <div class="dedebtify-card-actions">';
                html += '      <button class="dedebtify-btn dedebtify-btn-small" onclick="window.location=\'?action=edit&edit=' + loan.id + '\'">' + dedebtifyL10n.edit + '</button>';
                html += '      <button class="dedebtify-btn dedebtify-btn-small dedebtify-btn-danger dedebtify-delete-loan" data-id="' + loan.id + '">' + dedebtifyL10n.delete + '</button>';
                html += '    </div>';
                html += '  </div>';

                html += '  <div class="dedebtify-card-details">';
                html += '    <div class="dedebtify-card-detail">';
                html += '      <div class="dedebtify-card-detail-label">Balance</div>';
                html += '      <div class="dedebtify-card-detail-value">' + formatCurrency(loan.balance) + '</div>';
                html += '    </div>';
                html += '    <div class="dedebtify-card-detail">';
                html += '      <div class="dedebtify-card-detail-label">Monthly Payment</div>';
                html += '      <div class="dedebtify-card-detail-value">' + formatCurrency(loan.monthly_payment + loan.extra_payment) + '</div>';
                html += '    </div>';
                html += '    <div class="dedebtify-card-detail">';
                html += '      <div class="dedebtify-card-detail-label">Interest Rate</div>';
                html += '      <div class="dedebtify-card-detail-value">' + formatPercentage(loan.interest_rate) + '</div>';
                html += '    </div>';
                html += '    <div class="dedebtify-card-detail">';
                html += '      <div class="dedebtify-card-detail-label">Payoff Date</div>';
                html += '      <div class="dedebtify-card-detail-value">' + escapeHtml(loan.payoff_date) + '</div>';
                html += '    </div>';
                html += '  </div>';

                if (loan.months_to_payoff !== Infinity) {
                    html += '  <div class="dedebtify-card-payoff">';
                    html += '    <div class="dedebtify-payoff-info">';
                    html += '      <strong>Remaining:</strong> ' + loan.months_to_payoff + ' months';
                    html += '    </div>';
                    html += '  </div>';
                }

                html += '</div>';
            });

            $list.html(html);
        }

        function updateLoanStats(loans) {
            let totalDebt = 0;
            let totalPayment = 0;

            loans.forEach(function(loan) {
                totalDebt += parseFloat(loan.balance);
                totalPayment += parseFloat(loan.monthly_payment) + parseFloat(loan.extra_payment);
            });

            $('#loan-total-debt').text(formatCurrency(totalDebt));
            $('#loan-monthly-payment').text(formatCurrency(totalPayment));
            $('#loan-count').text(loans.length);
        }

        function initLoanForm() {
            const $form = $('#dedebtify-loan-form');
            if (!$form.length) return;

            const postId = $form.data('post-id');
            if (postId > 0) {
                loadLoanData(postId);
            }

            // Auto-calculate payment
            $('#calculate-loan-payment').on('click', function() {
                const principal = parseFloat($('#principal').val()) || 0;
                const rate = parseFloat($('#interest_rate').val()) || 0;
                const termMonths = parseInt($('#term_months').val()) || 0;

                if (principal > 0 && termMonths > 0) {
                    const payment = DedebtifyCalculator.calculateLoanPayment(principal, rate, termMonths);
                    $('#monthly_payment').val(payment.toFixed(2));
                    calculateLoanPayoffPreview();
                }
            });

            $('#current_balance, #interest_rate, #monthly_payment, #extra_payment').on('input', function() {
                calculateLoanPayoffPreview();
            });

            $form.on('submit', function(e) {
                e.preventDefault();
                saveLoan();
            });
        }

        function loadLoanData(postId) {
            $.ajax({
                url: dedebtify.restUrl + 'loans',
                method: 'GET',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', dedebtify.restNonce);
                },
                success: function(response) {
                    const loan = response.find(l => l.id === postId);
                    if (loan) {
                        populateLoanForm(loan);
                    }
                }
            });
        }

        function populateLoanForm(loan) {
            $('#loan_name').val(loan.name);
            $('#loan_type').val(loan.type);
            $('#principal').val(loan.principal || loan.balance);
            $('#current_balance').val(loan.balance);
            $('#interest_rate').val(loan.interest_rate);
            $('#term_months').val(loan.term_months || 60);
            $('#monthly_payment').val(loan.monthly_payment);
            $('#start_date').val(loan.start_date);
            $('#extra_payment').val(loan.extra_payment);

            calculateLoanPayoffPreview();
        }

        function calculateLoanPayoffPreview() {
            const balance = parseFloat($('#current_balance').val()) || 0;
            const interestRate = parseFloat($('#interest_rate').val()) || 0;
            const monthlyPayment = parseFloat($('#monthly_payment').val()) || 0;
            const extraPayment = parseFloat($('#extra_payment').val()) || 0;
            const totalPayment = monthlyPayment + extraPayment;

            if (balance > 0 && totalPayment > 0) {
                const months = DedebtifyCalculator.calculateMonthsToPayoff(balance, interestRate, totalPayment);
                const totalInterest = DedebtifyCalculator.calculateTotalInterest(balance, totalPayment, months);
                const payoffDate = DedebtifyCalculator.calculatePayoffDate(months);
                const totalPaid = totalPayment * months;

                $('#preview-months').text(months === Infinity ? '∞' : months);
                $('#preview-interest').text(DedebtifyCalculator.formatCurrency(totalInterest));
                $('#preview-date').text(payoffDate);
                $('#preview-total').text(DedebtifyCalculator.formatCurrency(totalPaid));

                $('#dedebtify-loan-payoff-preview').slideDown();
            } else {
                $('#dedebtify-loan-payoff-preview').slideUp();
            }
        }

        function saveLoan() {
            const $form = $('#dedebtify-loan-form');
            const $submitBtn = $form.find('button[type="submit"]');
            const originalText = $submitBtn.text();
            const postId = $form.data('post-id');

            if (!$form[0].checkValidity()) {
                $form[0].reportValidity();
                return;
            }

            const formData = {
                title: $('#loan_name').val(),
                status: 'publish',
                author: dedebtify.userId,
                meta: {
                    loan_type: $('#loan_type').val(),
                    principal: $('#principal').val(),
                    current_balance: $('#current_balance').val(),
                    interest_rate: $('#interest_rate').val(),
                    term_months: $('#term_months').val(),
                    monthly_payment: $('#monthly_payment').val(),
                    start_date: $('#start_date').val(),
                    extra_payment: $('#extra_payment').val() || 0
                }
            };

            $submitBtn.prop('disabled', true).text(dedebtifyL10n.saving);

            const method = postId > 0 ? 'PUT' : 'POST';
            const url = dedebtify.restUrl.replace('/dedebtify/v1/', '/wp/v2/') + 'dd_loan' + (postId > 0 ? '/' + postId : '');

            $.ajax({
                url: url,
                method: method,
                data: JSON.stringify(formData),
                contentType: 'application/json',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', dedebtify.restNonce);
                },
                success: function(response) {
                    showMessage(postId > 0 ? 'Loan updated successfully!' : 'Loan added successfully!', 'success');
                    setTimeout(function() {
                        window.location.href = '?action=list';
                    }, 1500);
                },
                error: function(xhr, status, error) {
                    console.error('Failed to save loan:', error);
                    showMessage('Failed to save loan. Please try again.', 'error');
                    $submitBtn.prop('disabled', false).text(originalText);
                }
            });
        }

        function initLoanControls() {
            $('#filter-loan-type, #sort-loans-by').on('change', function() {
                filterAndSortLoans();
            });
        }

        function filterAndSortLoans() {
            // Implementation similar to credit cards
        }

        $(document).on('click', '.dedebtify-delete-loan', function(e) {
            e.preventDefault();
            if (!confirm(dedebtifyL10n.confirm_delete)) return;

            const loanId = $(this).data('id');
            const $loanItem = $(this).closest('.dedebtify-card-item');

            $.ajax({
                url: dedebtify.restUrl.replace('/dedebtify/v1/', '/wp/v2/') + 'dd_loan/' + loanId,
                method: 'DELETE',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', dedebtify.restNonce);
                },
                success: function(response) {
                    $loanItem.fadeOut(function() {
                        $(this).remove();
                        if ($('.dedebtify-card-item').length === 0) {
                            $('#dedebtify-loans-list').hide();
                            $('#loans-empty-state').show();
                        }
                    });
                    showMessage('Loan deleted successfully', 'success');
                },
                error: function(xhr, status, error) {
                    showMessage('Failed to delete loan', 'error');
                }
            });
        });

        // ===========================
        // BILLS MANAGER
        // ===========================

        function initBillsManager() {
            if ($('.dedebtify-bills-manager').length) {
                loadBillsList();
                initBillForm();
                initBillControls();
            }
        }

        function loadBillsList() {
            const $list = $('#dedebtify-bills-list');
            if (!$list.length) return;

            $.ajax({
                url: dedebtify.restUrl + 'bills',
                method: 'GET',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', dedebtify.restNonce);
                },
                success: function(response) {
                    renderBillsList(response);
                    updateBillStats(response);
                },
                error: function(xhr, status, error) {
                    console.error('Failed to load bills:', error);
                    $list.html('<div class="dedebtify-message error">Failed to load bills</div>');
                }
            });
        }

        function renderBillsList(bills) {
            const $list = $('#dedebtify-bills-list');
            const $emptyState = $('#bills-empty-state');

            if (bills.length === 0) {
                $list.hide();
                $emptyState.show();
                return;
            }

            $emptyState.hide();
            $list.show();

            let html = '';
            bills.forEach(function(bill) {
                html += '<div class="dedebtify-card-item" data-id="' + bill.id + '" data-category="' + bill.category + '" data-essential="' + bill.is_essential + '">';
                html += '  <div class="dedebtify-card-header">';
                html += '    <div>';
                html += '      <h3 class="dedebtify-card-name">' + escapeHtml(bill.name) + '</h3>';
                html += '      <span class="dedebtify-card-badge">' + bill.category + '</span>';
                if (bill.is_essential == 1) {
                    html += '      <span class="dedebtify-card-badge success">Essential</span>';
                }
                if (bill.auto_pay == 1) {
                    html += '      <span class="dedebtify-card-badge">Auto-Pay</span>';
                }
                html += '    </div>';
                html += '    <div class="dedebtify-card-actions">';
                html += '      <button class="dedebtify-btn dedebtify-btn-small" onclick="window.location=\'?action=edit&edit=' + bill.id + '\'">' + dedebtifyL10n.edit + '</button>';
                html += '      <button class="dedebtify-btn dedebtify-btn-small dedebtify-btn-danger dedebtify-delete-bill" data-id="' + bill.id + '">' + dedebtifyL10n.delete + '</button>';
                html += '    </div>';
                html += '  </div>';

                html += '  <div class="dedebtify-card-details">';
                html += '    <div class="dedebtify-card-detail">';
                html += '      <div class="dedebtify-card-detail-label">Amount</div>';
                html += '      <div class="dedebtify-card-detail-value">' + formatCurrency(bill.amount) + '</div>';
                html += '    </div>';
                html += '    <div class="dedebtify-card-detail">';
                html += '      <div class="dedebtify-card-detail-label">Frequency</div>';
                html += '      <div class="dedebtify-card-detail-value">' + bill.frequency + '</div>';
                html += '    </div>';
                html += '    <div class="dedebtify-card-detail">';
                html += '      <div class="dedebtify-card-detail-label">Monthly Equivalent</div>';
                html += '      <div class="dedebtify-card-detail-value">' + formatCurrency(bill.monthly_equivalent) + '</div>';
                html += '    </div>';
                if (bill.due_date) {
                    html += '    <div class="dedebtify-card-detail">';
                    html += '      <div class="dedebtify-card-detail-label">Due Date</div>';
                    html += '      <div class="dedebtify-card-detail-value">' + bill.due_date + '</div>';
                    html += '    </div>';
                }
                html += '  </div>';
                html += '</div>';
            });

            $list.html(html);
        }

        function updateBillStats(bills) {
            let totalMonthly = 0;
            let essentialTotal = 0;
            let discretionaryTotal = 0;

            bills.forEach(function(bill) {
                const monthly = parseFloat(bill.monthly_equivalent);
                totalMonthly += monthly;
                if (bill.is_essential == 1) {
                    essentialTotal += monthly;
                } else {
                    discretionaryTotal += monthly;
                }
            });

            $('#bill-total-monthly').text(formatCurrency(totalMonthly));
            $('#bill-essential').text(formatCurrency(essentialTotal));
            $('#bill-discretionary').text(formatCurrency(discretionaryTotal));
        }

        function initBillForm() {
            const $form = $('#dedebtify-bill-form');
            if (!$form.length) return;

            const postId = $form.data('post-id');
            if (postId > 0) {
                loadBillData(postId);
            }

            $('#amount, #frequency').on('input change', function() {
                calculateBillPreview();
            });

            $form.on('submit', function(e) {
                e.preventDefault();
                saveBill();
            });
        }

        function loadBillData(postId) {
            $.ajax({
                url: dedebtify.restUrl + 'bills',
                method: 'GET',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', dedebtify.restNonce);
                },
                success: function(response) {
                    const bill = response.find(b => b.id === postId);
                    if (bill) {
                        populateBillForm(bill);
                    }
                }
            });
        }

        function populateBillForm(bill) {
            $('#bill_name').val(bill.name);
            $('#category').val(bill.category);
            $('#amount').val(bill.amount);
            $('#frequency').val(bill.frequency);
            $('#due_date').val(bill.due_date);
            $('#auto_pay').prop('checked', bill.auto_pay == 1);
            $('#is_essential').prop('checked', bill.is_essential == 1);

            calculateBillPreview();
        }

        function calculateBillPreview() {
            const amount = parseFloat($('#amount').val()) || 0;
            const frequency = $('#frequency').val();

            if (amount > 0) {
                const monthly = DedebtifyCalculator.convertToMonthly(amount, frequency);
                const annual = monthly * 12;

                $('#preview-monthly').text(formatCurrency(monthly));
                $('#preview-annual').text(formatCurrency(annual));

                let calculation = '';
                switch(frequency) {
                    case 'weekly': calculation = amount + ' × 52 ÷ 12'; break;
                    case 'bi-weekly': calculation = amount + ' × 26 ÷ 12'; break;
                    case 'quarterly': calculation = amount + ' ÷ 3'; break;
                    case 'annually': calculation = amount + ' ÷ 12'; break;
                    default: calculation = 'Monthly amount';
                }
                $('#preview-calculation').text(calculation);

                $('#dedebtify-bill-preview').slideDown();
            } else {
                $('#dedebtify-bill-preview').slideUp();
            }
        }

        function saveBill() {
            const $form = $('#dedebtify-bill-form');
            const $submitBtn = $form.find('button[type="submit"]');
            const originalText = $submitBtn.text();
            const postId = $form.data('post-id');

            if (!$form[0].checkValidity()) {
                $form[0].reportValidity();
                return;
            }

            const formData = {
                title: $('#bill_name').val(),
                status: 'publish',
                author: dedebtify.userId,
                meta: {
                    category: $('#category').val(),
                    amount: $('#amount').val(),
                    frequency: $('#frequency').val(),
                    due_date: $('#due_date').val() || '',
                    auto_pay: $('#auto_pay').is(':checked') ? '1' : '0',
                    is_essential: $('#is_essential').is(':checked') ? '1' : '0'
                }
            };

            $submitBtn.prop('disabled', true).text(dedebtifyL10n.saving);

            const method = postId > 0 ? 'PUT' : 'POST';
            const url = dedebtify.restUrl.replace('/dedebtify/v1/', '/wp/v2/') + 'dd_bill' + (postId > 0 ? '/' + postId : '');

            $.ajax({
                url: url,
                method: method,
                data: JSON.stringify(formData),
                contentType: 'application/json',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', dedebtify.restNonce);
                },
                success: function(response) {
                    showMessage(postId > 0 ? 'Bill updated successfully!' : 'Bill added successfully!', 'success');
                    setTimeout(function() {
                        window.location.href = '?action=list';
                    }, 1500);
                },
                error: function(xhr, status, error) {
                    console.error('Failed to save bill:', error);
                    showMessage('Failed to save bill. Please try again.', 'error');
                    $submitBtn.prop('disabled', false).text(originalText);
                }
            });
        }

        function initBillControls() {
            $('#filter-category, #filter-essential, #sort-bills-by').on('change', function() {
                filterAndSortBills();
            });
        }

        function filterAndSortBills() {
            // Implementation for bills filtering
        }

        $(document).on('click', '.dedebtify-delete-bill', function(e) {
            e.preventDefault();
            if (!confirm(dedebtifyL10n.confirm_delete)) return;

            const billId = $(this).data('id');
            const $billItem = $(this).closest('.dedebtify-card-item');

            $.ajax({
                url: dedebtify.restUrl.replace('/dedebtify/v1/', '/wp/v2/') + 'dd_bill/' + billId,
                method: 'DELETE',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', dedebtify.restNonce);
                },
                success: function(response) {
                    $billItem.fadeOut(function() {
                        $(this).remove();
                        if ($('.dedebtify-card-item').length === 0) {
                            $('#dedebtify-bills-list').hide();
                            $('#bills-empty-state').show();
                        }
                    });
                    showMessage('Bill deleted successfully', 'success');
                },
                error: function(xhr, status, error) {
                    showMessage('Failed to delete bill', 'error');
                }
            });
        });

        // ===========================
        // GOALS MANAGER
        // ===========================

        function initGoalsManager() {
            if ($('.dedebtify-goals-manager').length) {
                loadGoalsList();
                initGoalForm();
                initGoalControls();
            }
        }

        function loadGoalsList() {
            const $list = $('#dedebtify-goals-list');
            if (!$list.length) return;

            $.ajax({
                url: dedebtify.restUrl + 'goals',
                method: 'GET',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', dedebtify.restNonce);
                },
                success: function(response) {
                    renderGoalsList(response);
                    updateGoalStats(response);
                },
                error: function(xhr, status, error) {
                    console.error('Failed to load goals:', error);
                    $list.html('<div class="dedebtify-message error">Failed to load goals</div>');
                }
            });
        }

        function renderGoalsList(goals) {
            const $list = $('#dedebtify-goals-list');
            const $emptyState = $('#goals-empty-state');

            if (goals.length === 0) {
                $list.hide();
                $emptyState.show();
                return;
            }

            $emptyState.hide();
            $list.show();

            let html = '';
            goals.forEach(function(goal) {
                const progressClass = goal.progress_percentage >= 75 ? 'success' : (goal.progress_percentage >= 50 ? 'warning' : '');

                html += '<div class="dedebtify-card-item" data-id="' + goal.id + '" data-type="' + goal.type + '" data-priority="' + goal.priority + '">';
                html += '  <div class="dedebtify-card-header">';
                html += '    <div>';
                html += '      <h3 class="dedebtify-card-name">' + escapeHtml(goal.name) + '</h3>';
                html += '      <span class="dedebtify-card-badge">' + goal.type.replace('_', ' ') + '</span>';
                html += '      <span class="dedebtify-card-badge ' + goal.priority + '">' + goal.priority + '</span>';
                html += '    </div>';
                html += '    <div class="dedebtify-card-actions">';
                html += '      <button class="dedebtify-btn dedebtify-btn-small" onclick="window.location=\'?action=edit&edit=' + goal.id + '\'">' + dedebtifyL10n.edit + '</button>';
                html += '      <button class="dedebtify-btn dedebtify-btn-small dedebtify-btn-danger dedebtify-delete-goal" data-id="' + goal.id + '">' + dedebtifyL10n.delete + '</button>';
                html += '    </div>';
                html += '  </div>';

                html += '  <div class="dedebtify-progress" style="height: 25px; margin: 15px 0;">';
                html += '    <div class="dedebtify-progress-bar ' + progressClass + '" style="width: ' + Math.min(goal.progress_percentage, 100) + '%"></div>';
                html += '  </div>';

                html += '  <div class="dedebtify-card-details">';
                html += '    <div class="dedebtify-card-detail">';
                html += '      <div class="dedebtify-card-detail-label">Progress</div>';
                html += '      <div class="dedebtify-card-detail-value ' + progressClass + '">' + formatPercentage(goal.progress_percentage) + '</div>';
                html += '    </div>';
                html += '    <div class="dedebtify-card-detail">';
                html += '      <div class="dedebtify-card-detail-label">Current / Target</div>';
                html += '      <div class="dedebtify-card-detail-value">' + formatCurrency(goal.current_amount) + ' / ' + formatCurrency(goal.target_amount) + '</div>';
                html += '    </div>';
                html += '    <div class="dedebtify-card-detail">';
                html += '      <div class="dedebtify-card-detail-label">Monthly Contribution</div>';
                html += '      <div class="dedebtify-card-detail-value">' + formatCurrency(goal.monthly_contribution) + '</div>';
                html += '    </div>';
                html += '    <div class="dedebtify-card-detail">';
                html += '      <div class="dedebtify-card-detail-label">Months to Goal</div>';
                html += '      <div class="dedebtify-card-detail-value">' + (goal.months_to_goal === Infinity ? '∞' : goal.months_to_goal) + '</div>';
                html += '    </div>';
                html += '  </div>';
                html += '</div>';
            });

            $list.html(html);
        }

        function updateGoalStats(goals) {
            let totalTarget = 0;
            let totalSaved = 0;

            goals.forEach(function(goal) {
                totalTarget += parseFloat(goal.target_amount);
                totalSaved += parseFloat(goal.current_amount);
            });

            const overallProgress = totalTarget > 0 ? (totalSaved / totalTarget) * 100 : 0;

            $('#goal-total-target').text(formatCurrency(totalTarget));
            $('#goal-total-saved').text(formatCurrency(totalSaved));
            $('#goal-overall-progress').text(formatPercentage(overallProgress));
        }

        function initGoalForm() {
            const $form = $('#dedebtify-goal-form');
            if (!$form.length) return;

            const postId = $form.data('post-id');
            if (postId > 0) {
                loadGoalData(postId);
            }

            $('#target_amount, #current_amount, #monthly_contribution, #target_date').on('input change', function() {
                calculateGoalPreview();
            });

            $form.on('submit', function(e) {
                e.preventDefault();
                saveGoal();
            });
        }

        function loadGoalData(postId) {
            $.ajax({
                url: dedebtify.restUrl + 'goals',
                method: 'GET',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', dedebtify.restNonce);
                },
                success: function(response) {
                    const goal = response.find(g => g.id === postId);
                    if (goal) {
                        populateGoalForm(goal);
                    }
                }
            });
        }

        function populateGoalForm(goal) {
            $('#goal_name').val(goal.name);
            $('#goal_type').val(goal.type);
            $('#priority').val(goal.priority);
            $('#target_amount').val(goal.target_amount);
            $('#current_amount').val(goal.current_amount);
            $('#monthly_contribution').val(goal.monthly_contribution);
            $('#target_date').val(goal.target_date);

            calculateGoalPreview();
        }

        function calculateGoalPreview() {
            const targetAmount = parseFloat($('#target_amount').val()) || 0;
            const currentAmount = parseFloat($('#current_amount').val()) || 0;
            const monthlyContribution = parseFloat($('#monthly_contribution').val()) || 0;
            const targetDate = $('#target_date').val();

            if (targetAmount > 0) {
                const remaining = Math.max(0, targetAmount - currentAmount);
                const progress = DedebtifyCalculator.calculateProgress(currentAmount, targetAmount);
                const monthsToGoal = DedebtifyCalculator.calculateMonthsToGoal(currentAmount, targetAmount, monthlyContribution);

                $('#goal-progress-percent').text(formatPercentage(progress));
                $('#goal-progress-bar').css('width', Math.min(progress, 100) + '%');
                $('#preview-remaining').text(formatCurrency(remaining));
                $('#preview-months').text(monthsToGoal === Infinity ? '∞' : monthsToGoal);

                if (monthsToGoal !== Infinity) {
                    const estimatedDate = new Date();
                    estimatedDate.setMonth(estimatedDate.getMonth() + monthsToGoal);
                    $('#preview-date').text(estimatedDate.toLocaleDateString('en-US', { year: 'numeric', month: 'long' }));

                    // Check if on track
                    if (targetDate) {
                        const target = new Date(targetDate);
                        const isOnTrack = estimatedDate <= target;
                        $('#preview-status').text(isOnTrack ? '✓ On Track' : '⚠ Behind').removeClass('success warning danger').addClass(isOnTrack ? 'success' : 'warning');
                    } else {
                        $('#preview-status').text('—');
                    }
                } else {
                    $('#preview-date').text('—');
                    $('#preview-status').text('—');
                }

                $('#dedebtify-goal-preview').slideDown();
            } else {
                $('#dedebtify-goal-preview').slideUp();
            }
        }

        function saveGoal() {
            const $form = $('#dedebtify-goal-form');
            const $submitBtn = $form.find('button[type="submit"]');
            const originalText = $submitBtn.text();
            const postId = $form.data('post-id');

            if (!$form[0].checkValidity()) {
                $form[0].reportValidity();
                return;
            }

            const formData = {
                title: $('#goal_name').val(),
                status: 'publish',
                author: dedebtify.userId,
                meta: {
                    goal_type: $('#goal_type').val(),
                    priority: $('#priority').val(),
                    target_amount: $('#target_amount').val(),
                    current_amount: $('#current_amount').val(),
                    monthly_contribution: $('#monthly_contribution').val() || 0,
                    target_date: $('#target_date').val() || ''
                }
            };

            $submitBtn.prop('disabled', true).text(dedebtifyL10n.saving);

            const method = postId > 0 ? 'PUT' : 'POST';
            const url = dedebtify.restUrl.replace('/dedebtify/v1/', '/wp/v2/') + 'dd_goal' + (postId > 0 ? '/' + postId : '');

            $.ajax({
                url: url,
                method: method,
                data: JSON.stringify(formData),
                contentType: 'application/json',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', dedebtify.restNonce);
                },
                success: function(response) {
                    showMessage(postId > 0 ? 'Goal updated successfully!' : 'Goal added successfully!', 'success');
                    setTimeout(function() {
                        window.location.href = '?action=list';
                    }, 1500);
                },
                error: function(xhr, status, error) {
                    console.error('Failed to save goal:', error);
                    showMessage('Failed to save goal. Please try again.', 'error');
                    $submitBtn.prop('disabled', false).text(originalText);
                }
            });
        }

        function initGoalControls() {
            $('#filter-goal-type, #filter-priority, #sort-goals-by').on('change', function() {
                filterAndSortGoals();
            });
        }

        function filterAndSortGoals() {
            // Implementation for goals filtering
        }

        $(document).on('click', '.dedebtify-delete-goal', function(e) {
            e.preventDefault();
            if (!confirm(dedebtifyL10n.confirm_delete)) return;

            const goalId = $(this).data('id');
            const $goalItem = $(this).closest('.dedebtify-card-item');

            $.ajax({
                url: dedebtify.restUrl.replace('/dedebtify/v1/', '/wp/v2/') + 'dd_goal/' + goalId,
                method: 'DELETE',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', dedebtify.restNonce);
                },
                success: function(response) {
                    $goalItem.fadeOut(function() {
                        $(this).remove();
                        if ($('.dedebtify-card-item').length === 0) {
                            $('#dedebtify-goals-list').hide();
                            $('#goals-empty-state').show();
                        }
                    });
                    showMessage('Goal deleted successfully', 'success');
                },
                error: function(xhr, status, error) {
                    showMessage('Failed to delete goal', 'error');
                }
            });
        });

        // Initialize all managers
        initCreditCardManager();
        initLoansManager();
        initBillsManager();
        initGoalsManager();

