/**
 * DeDebtify Mortgage Manager
 *
 * Handles mortgage CRUD operations and calculations
 *
 * @since      1.0.0
 * @package    Dedebtify
 */

(function($) {
    'use strict';

    $(document).ready(function() {

        /**
         * Initialize Mortgage Manager
         */
        function initMortgageManager() {
            if ($('.dedebtify-mortgages-manager').length) {
                loadMortgages();
                initMortgageForm();
            }
        }

        /**
         * Load all mortgages
         */
        function loadMortgages() {
            $.ajax({
                url: dedebtify.restUrl + 'mortgages',
                method: 'GET',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', dedebtify.restNonce);
                },
                success: function(response) {
                    if (response.length === 0) {
                        $('#mortgages-empty-state').show();
                        $('#dedebtify-mortgages-list').hide();
                    } else {
                        $('#mortgages-empty-state').hide();
                        renderMortgages(response);
                        updateMortgageStats(response);
                    }
                },
                error: function() {
                    showMessage('Failed to load mortgages', 'error');
                }
            });
        }

        /**
         * Render mortgages list
         */
        function renderMortgages(mortgages) {
            let html = '';

            mortgages.forEach(function(mortgage) {
                const totalMonthly = parseFloat(mortgage.total_monthly_payment);
                const balance = parseFloat(mortgage.balance);
                const monthsRemaining = mortgage.months_to_payoff;
                const yearsRemaining = (monthsRemaining / 12).toFixed(1);

                html += '<div class="dedebtify-card-item" data-id="' + mortgage.id + '">';
                html += '  <div class="dedebtify-card-header">';
                html += '    <h3 class="dedebtify-card-name">' + escapeHtml(mortgage.name) + '</h3>';
                html += '    <div class="dedebtify-card-actions">';
                html += '      <button class="dedebtify-btn-icon dedebtify-btn-edit" data-id="' + mortgage.id + '" title="Edit">';
                html += '        <span class="dashicons dashicons-edit"></span>';
                html += '      </button>';
                html += '      <button class="dedebtify-btn-icon dedebtify-btn-delete" data-id="' + mortgage.id + '" title="Delete">';
                html += '        <span class="dashicons dashicons-trash"></span>';
                html += '      </button>';
                html += '    </div>';
                html += '  </div>';

                if (mortgage.property_address) {
                    html += '  <div style="margin-bottom: 1rem; color: hsl(var(--dd-muted-foreground));">';
                    html += '    <span class="dashicons dashicons-location" style="font-size: 16px; margin-right: 5px;"></span>';
                    html += escapeHtml(mortgage.property_address);
                    html += '  </div>';
                }

                html += '  <div class="dedebtify-card-details">';
                html += '    <div class="dedebtify-card-detail">';
                html += '      <span class="dedebtify-card-detail-label">Current Balance</span>';
                html += '      <span class="dedebtify-card-detail-value">' + formatCurrency(balance) + '</span>';
                html += '    </div>';
                html += '    <div class="dedebtify-card-detail">';
                html += '      <span class="dedebtify-card-detail-label">Interest Rate</span>';
                html += '      <span class="dedebtify-card-detail-value">' + parseFloat(mortgage.interest_rate).toFixed(2) + '%</span>';
                html += '    </div>';
                html += '    <div class="dedebtify-card-detail">';
                html += '      <span class="dedebtify-card-detail-label">Total Monthly</span>';
                html += '      <span class="dedebtify-card-detail-value">' + formatCurrency(totalMonthly) + '</span>';
                html += '    </div>';
                html += '    <div class="dedebtify-card-detail">';
                html += '      <span class="dedebtify-card-detail-label">Years Remaining</span>';
                html += '      <span class="dedebtify-card-detail-value">' + yearsRemaining + '</span>';
                html += '    </div>';
                html += '  </div>';

                // Payment breakdown
                html += '  <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid hsl(var(--dd-border));">';
                html += '    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; font-size: 0.875rem;">';
                html += '      <div style="color: hsl(var(--dd-muted-foreground));">P&I Payment:</div>';
                html += '      <div style="text-align: right; font-weight: 600;">' + formatCurrency(mortgage.monthly_payment) + '</div>';

                if (parseFloat(mortgage.property_tax) > 0) {
                    html += '      <div style="color: hsl(var(--dd-muted-foreground));">Property Tax:</div>';
                    html += '      <div style="text-align: right; font-weight: 600;">' + formatCurrency(mortgage.property_tax / 12) + '/mo</div>';
                }

                if (parseFloat(mortgage.homeowners_insurance) > 0) {
                    html += '      <div style="color: hsl(var(--dd-muted-foreground));">Insurance:</div>';
                    html += '      <div style="text-align: right; font-weight: 600;">' + formatCurrency(mortgage.homeowners_insurance / 12) + '/mo</div>';
                }

                if (parseFloat(mortgage.pmi) > 0) {
                    html += '      <div style="color: hsl(var(--dd-muted-foreground));">PMI:</div>';
                    html += '      <div style="text-align: right; font-weight: 600;">' + formatCurrency(mortgage.pmi) + '</div>';
                }

                if (parseFloat(mortgage.extra_payment) > 0) {
                    html += '      <div style="color: hsl(var(--dd-success));">Extra Principal:</div>';
                    html += '      <div style="text-align: right; font-weight: 600; color: hsl(var(--dd-success));">' + formatCurrency(mortgage.extra_payment) + '</div>';
                }

                html += '    </div>';
                html += '  </div>';

                // Payoff date
                if (mortgage.payoff_date) {
                    const payoffDate = new Date(mortgage.payoff_date);
                    html += '  <div style="margin-top: 1rem; padding: 0.75rem; background: hsl(var(--dd-muted) / 0.3); border-radius: var(--dd-radius); text-align: center;">';
                    html += '    <div style="font-size: 0.75rem; color: hsl(var(--dd-muted-foreground)); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">Estimated Payoff</div>';
                    html += '    <div style="font-size: 1.125rem; font-weight: 700; color: hsl(var(--dd-primary));">' + payoffDate.toLocaleDateString('en-US', { year: 'numeric', month: 'long' }) + '</div>';
                    html += '  </div>';
                }

                html += '</div>';
            });

            $('#dedebtify-mortgages-list').html(html).show();

            // Attach event handlers
            $('.dedebtify-btn-edit').on('click', function() {
                const id = $(this).data('id');
                window.location.href = '?action=edit&edit=' + id;
            });

            $('.dedebtify-btn-delete').on('click', function() {
                const id = $(this).data('id');
                deleteMortgage(id);
            });
        }

        /**
         * Update mortgage statistics
         */
        function updateMortgageStats(mortgages) {
            let totalBalance = 0;
            let totalMonthlyPayment = 0;

            mortgages.forEach(function(mortgage) {
                totalBalance += parseFloat(mortgage.balance);
                totalMonthlyPayment += parseFloat(mortgage.total_monthly_payment);
            });

            $('#mortgage-total-debt').text(formatCurrency(totalBalance));
            $('#mortgage-monthly-payment').text(formatCurrency(totalMonthlyPayment));
        }

        /**
         * Initialize mortgage form
         */
        function initMortgageForm() {
            const $form = $('#dedebtify-mortgage-form');
            if (!$form.length) return;

            const postId = $form.data('post-id');

            // Load mortgage data if editing
            if (postId) {
                loadMortgageData(postId);
            }

            // Auto-calculate monthly payment
            $('#calculate-mortgage-payment').on('click', function() {
                calculateMortgagePayment();
            });

            // Update preview on field changes
            $('#loan_amount, #interest_rate, #term_years, #property_tax, #homeowners_insurance, #pmi, #extra_payment, #monthly_payment').on('change', function() {
                updateMortgagePreview();
            });

            // Form submission
            $form.on('submit', function(e) {
                e.preventDefault();
                saveMortgage();
            });
        }

        /**
         * Calculate monthly mortgage payment (P&I only)
         */
        function calculateMortgagePayment() {
            const loanAmount = parseFloat($('#loan_amount').val()) || 0;
            const interestRate = parseFloat($('#interest_rate').val()) || 0;
            const termYears = parseInt($('#term_years').val()) || 30;

            if (loanAmount === 0 || interestRate === 0) {
                showMessage('Please enter loan amount and interest rate', 'error');
                return;
            }

            const monthlyRate = (interestRate / 100) / 12;
            const numPayments = termYears * 12;

            // Calculate monthly P&I using standard mortgage formula
            const monthlyPayment = loanAmount * (monthlyRate * Math.pow(1 + monthlyRate, numPayments)) /
                                   (Math.pow(1 + monthlyRate, numPayments) - 1);

            $('#monthly_payment').val(monthlyPayment.toFixed(2));
            updateMortgagePreview();
            showMessage('Monthly payment calculated successfully', 'success');
        }

        /**
         * Update mortgage preview
         */
        function updateMortgagePreview() {
            const monthlyPayment = parseFloat($('#monthly_payment').val()) || 0;
            const propertyTax = parseFloat($('#property_tax').val()) || 0;
            const insurance = parseFloat($('#homeowners_insurance').val()) || 0;
            const pmi = parseFloat($('#pmi').val()) || 0;
            const extraPayment = parseFloat($('#extra_payment').val()) || 0;
            const currentBalance = parseFloat($('#current_balance').val()) || 0;
            const interestRate = parseFloat($('#interest_rate').val()) || 0;

            if (monthlyPayment === 0 || currentBalance === 0) {
                $('#dedebtify-mortgage-payoff-preview').hide();
                return;
            }

            // Calculate total monthly payment
            const totalMonthly = monthlyPayment + (propertyTax / 12) + (insurance / 12) + pmi;

            // Calculate payoff time
            const totalPayment = monthlyPayment + extraPayment;
            const monthlyRate = (interestRate / 100) / 12;

            let months = 0;
            let balance = currentBalance;
            let totalInterest = 0;

            while (balance > 0 && months < 600) {
                const interest = balance * monthlyRate;
                const principal = Math.min(totalPayment - interest, balance);
                balance -= principal;
                totalInterest += interest;
                months++;
            }

            const years = (months / 12).toFixed(1);
            const payoffDate = new Date();
            payoffDate.setMonth(payoffDate.getMonth() + months);

            // Update preview
            $('#preview-total-payment').text(formatCurrency(totalMonthly));
            $('#preview-years').text(years);
            $('#preview-interest').text(formatCurrency(totalInterest));
            $('#preview-date').text(payoffDate.toLocaleDateString('en-US', { year: 'numeric', month: 'long' }));

            $('#dedebtify-mortgage-payoff-preview').fadeIn();
        }

        /**
         * Load mortgage data for editing
         */
        function loadMortgageData(postId) {
            $.ajax({
                url: dedebtify.restUrl + 'mortgages',
                method: 'GET',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', dedebtify.restNonce);
                },
                success: function(response) {
                    const mortgage = response.find(m => m.id == postId);
                    if (mortgage) {
                        populateMortgageForm(mortgage);
                    }
                },
                error: function() {
                    showMessage('Failed to load mortgage data', 'error');
                }
            });
        }

        /**
         * Populate form with mortgage data
         */
        function populateMortgageForm(mortgage) {
            $('#mortgage_name').val(mortgage.name);
            $('#property_address').val(mortgage.property_address);
            $('#loan_amount').val(mortgage.loan_amount);
            $('#current_balance').val(mortgage.balance);
            $('#interest_rate').val(mortgage.interest_rate);
            $('#term_years').val(mortgage.term_years);
            $('#monthly_payment').val(mortgage.monthly_payment);
            $('#start_date').val(mortgage.start_date);
            $('#extra_payment').val(mortgage.extra_payment || '');
            $('#property_tax').val(mortgage.property_tax || '');
            $('#homeowners_insurance').val(mortgage.homeowners_insurance || '');
            $('#pmi').val(mortgage.pmi || '');

            updateMortgagePreview();
        }

        /**
         * Save mortgage
         */
        function saveMortgage() {
            const $form = $('#dedebtify-mortgage-form');
            const postId = $form.data('post-id');
            const isEdit = postId > 0;

            const mortgageData = {
                title: $('#mortgage_name').val(),
                status: 'publish',
                meta: {
                    property_address: $('#property_address').val(),
                    loan_amount: $('#loan_amount').val(),
                    current_balance: $('#current_balance').val(),
                    interest_rate: $('#interest_rate').val(),
                    term_years: $('#term_years').val(),
                    monthly_payment: $('#monthly_payment').val(),
                    start_date: $('#start_date').val(),
                    extra_payment: $('#extra_payment').val() || 0,
                    property_tax: $('#property_tax').val() || 0,
                    homeowners_insurance: $('#homeowners_insurance').val() || 0,
                    pmi: $('#pmi').val() || 0
                }
            };

            const url = isEdit
                ? dedebtify.restUrl + '../wp/v2/dd_mortgage/' + postId
                : dedebtify.restUrl + '../wp/v2/dd_mortgage';

            $.ajax({
                url: url,
                method: isEdit ? 'PUT' : 'POST',
                contentType: 'application/json',
                data: JSON.stringify(mortgageData),
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', dedebtify.restNonce);
                },
                success: function() {
                    showMessage(isEdit ? 'Mortgage updated successfully!' : 'Mortgage added successfully!', 'success');
                    setTimeout(function() {
                        window.location.href = '?action=list';
                    }, 1000);
                },
                error: function(xhr) {
                    showMessage('Failed to save mortgage: ' + (xhr.responseJSON?.message || 'Unknown error'), 'error');
                }
            });
        }

        /**
         * Delete mortgage
         */
        function deleteMortgage(id) {
            if (!confirm('Are you sure you want to delete this mortgage? This action cannot be undone.')) {
                return;
            }

            $.ajax({
                url: dedebtify.restUrl + '../wp/v2/dd_mortgage/' + id,
                method: 'DELETE',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', dedebtify.restNonce);
                },
                success: function() {
                    showMessage('Mortgage deleted successfully', 'success');
                    loadMortgages();
                },
                error: function() {
                    showMessage('Failed to delete mortgage', 'error');
                }
            });
        }

        /**
         * Helper: Format currency
         */
        function formatCurrency(amount) {
            return '$' + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }

        /**
         * Helper: Escape HTML
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

        /**
         * Helper: Show message
         */
        function showMessage(message, type) {
            const $message = $('<div class="dedebtify-message ' + type + '">' + message + '</div>');
            $('.dedebtify-mortgages-manager').prepend($message);

            setTimeout(function() {
                $message.fadeOut(function() {
                    $(this).remove();
                });
            }, 5000);
        }

        // Initialize
        initMortgageManager();

    });

})(jQuery);
