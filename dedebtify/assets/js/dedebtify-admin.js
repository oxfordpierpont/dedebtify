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
            loadDashboardStats();
        }

        /**
         * Load dashboard statistics
         */
        function loadDashboardStats() {
            const $container = $('.dedebtify-admin-dashboard');
            if (!$container.length) return;

            $.ajax({
                url: dedebtifyAdmin.restUrl + 'dashboard',
                method: 'GET',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', dedebtifyAdmin.restNonce);
                },
                success: function(response) {
                    updateDashboardUI(response);
                },
                error: function(xhr, status, error) {
                    console.error('Failed to load dashboard data:', error);
                    showNotice('Failed to load dashboard data', 'error');
                }
            });
        }

        /**
         * Update dashboard UI with data
         */
        function updateDashboardUI(data) {
            // Update total debt
            $('#dedebtify-total-debt').text(formatCurrency(data.total_debt));

            // Update monthly payments
            $('#dedebtify-monthly-payments').text(formatCurrency(data.monthly_payments));

            // Update DTI
            $('#dedebtify-dti').text(formatPercentage(data.dti_ratio));

            // Update credit utilization
            $('#dedebtify-credit-util').text(formatPercentage(data.credit_utilization));

            // Apply color coding
            applyCreditUtilizationColor(data.credit_utilization);
            applyDTIColor(data.dti_ratio);
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
