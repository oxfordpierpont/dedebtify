/**
 * DeDebtify Action Plan & Snapshots
 *
 * Handles debt payoff planning and financial progress tracking
 *
 * @since      1.0.0
 * @package    Dedebtify
 */

(function($) {
    'use strict';

    $(document).ready(function() {

        /**
         * =========================================
         * ACTION PLAN FUNCTIONS
         * =========================================
         */

        function initActionPlan() {
            if ($('.dedebtify-action-plan').length) {
                initActionPlanForm();
            }
        }

        function initActionPlanForm() {
            const $form = $('#dedebtify-action-plan-form');
            if (!$form.length) return;

            // Strategy help text
            $('#payoff_strategy').on('change', function() {
                const strategy = $(this).val();
                const helpText = strategy === 'avalanche'
                    ? 'Avalanche method pays off highest interest debts first, saving you the most money on interest.'
                    : 'Snowball method pays off smallest balances first, giving you quick wins to stay motivated.';
                $('#strategy-help').text(helpText);
            });

            // Form submission
            $form.on('submit', function(e) {
                e.preventDefault();
                generateActionPlan();
            });

            // Print button
            $('#print-plan').on('click', function() {
                window.print();
            });

            // Regenerate button
            $('#regenerate-plan').on('click', function() {
                $('#dedebtify-plan-summary, #dedebtify-payoff-timeline, #dedebtify-payment-schedule, #dedebtify-action-items, #dedebtify-plan-actions').hide();
                $('html, body').animate({ scrollTop: 0 }, 500);
            });

            // Toggle schedule
            $('#toggle-schedule').on('click', function() {
                const $details = $('#dedebtify-schedule-details');
                if ($details.is(':visible')) {
                    $details.slideUp();
                    $(this).text('Show Details');
                } else {
                    $details.slideDown();
                    $(this).text('Hide Details');
                }
            });
        }

        function generateActionPlan() {
            const strategy = $('#payoff_strategy').val();
            const extraPayment = parseFloat($('#extra_payment').val()) || 0;

            $('#plan-loading').show();
            $('#plan-empty-state').hide();

            // Fetch all debts
            Promise.all([
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
                })
            ]).then(function(results) {
                const creditCards = results[0];
                const loans = results[1];

                const allDebts = [];

                // Add credit cards
                creditCards.forEach(function(card) {
                    if (card.status === 'active' && parseFloat(card.balance) > 0) {
                        allDebts.push({
                            id: card.id,
                            type: 'credit_card',
                            name: card.name,
                            balance: parseFloat(card.balance),
                            rate: parseFloat(card.interest_rate),
                            payment: parseFloat(card.minimum_payment) + parseFloat(card.extra_payment || 0)
                        });
                    }
                });

                // Add loans
                loans.forEach(function(loan) {
                    if (parseFloat(loan.current_balance) > 0) {
                        allDebts.push({
                            id: loan.id,
                            type: 'loan',
                            name: loan.name,
                            balance: parseFloat(loan.current_balance),
                            rate: parseFloat(loan.interest_rate),
                            payment: parseFloat(loan.monthly_payment) + parseFloat(loan.extra_payment || 0)
                        });
                    }
                });

                if (allDebts.length === 0) {
                    $('#plan-loading').hide();
                    $('#plan-empty-state').show();
                    return;
                }

                // Calculate both methods
                const avalancheResult = calculatePayoffPlan(allDebts, 'avalanche', extraPayment);
                const snowballResult = calculatePayoffPlan(allDebts, 'snowball', extraPayment);
                const chosenResult = strategy === 'avalanche' ? avalancheResult : snowballResult;

                // Display results
                displayActionPlan(chosenResult, avalancheResult, snowballResult, strategy);

                $('#plan-loading').hide();
                $('#dedebtify-plan-summary, #dedebtify-payoff-timeline, #dedebtify-payment-schedule, #dedebtify-action-items, #dedebtify-plan-actions').fadeIn();

            }).catch(function(error) {
                $('#plan-loading').hide();
                console.error('Failed to load debt data:', error);
                showMessage('Failed to load debt data', 'error');
            });
        }

        function calculatePayoffPlan(debts, strategy, extraPayment) {
            // Clone debts to avoid mutation
            let debtsCopy = JSON.parse(JSON.stringify(debts));

            // Sort debts based on strategy
            if (strategy === 'avalanche') {
                debtsCopy.sort((a, b) => b.rate - a.rate);
            } else {
                debtsCopy.sort((a, b) => a.balance - b.balance);
            }

            let month = 0;
            let totalInterest = 0;
            const timeline = [];
            const schedule = [];
            let availableExtra = extraPayment;

            while (debtsCopy.some(d => d.balance > 0)) {
                month++;
                if (month > 600) break; // Safety limit

                let currentDebtIndex = debtsCopy.findIndex(d => d.balance > 0);
                if (currentDebtIndex === -1) break;

                debtsCopy.forEach(function(debt, index) {
                    if (debt.balance <= 0) return;

                    const monthlyRate = (debt.rate / 100) / 12;
                    const interest = debt.balance * monthlyRate;
                    let payment = debt.payment;

                    // Apply extra payment to first unpaid debt
                    if (index === currentDebtIndex) {
                        payment += availableExtra;
                    }

                    const principal = Math.min(payment - interest, debt.balance);
                    const actualPayment = principal + interest;

                    debt.balance -= principal;
                    totalInterest += interest;

                    if (debt.balance <= 0.01) {
                        debt.balance = 0;
                        debt.paidOffMonth = month;
                        // Roll over payment
                        availableExtra += debt.payment;
                    }

                    schedule.push({
                        month: month,
                        debt: debt.name,
                        payment: actualPayment,
                        principal: principal,
                        interest: interest,
                        remaining: Math.max(0, debt.balance)
                    });
                });
            }

            // Build timeline
            debtsCopy.forEach(function(debt) {
                if (debt.paidOffMonth) {
                    const originalDebt = debts.find(d => d.id === debt.id);
                    timeline.push({
                        name: debt.name,
                        month: debt.paidOffMonth,
                        balance: originalDebt ? originalDebt.balance : 0
                    });
                }
            });

            timeline.sort((a, b) => a.month - b.month);

            const totalDebt = debts.reduce((sum, d) => sum + d.balance, 0);
            const debtFreeDate = new Date();
            debtFreeDate.setMonth(debtFreeDate.getMonth() + month);

            return {
                months: month,
                totalInterest: totalInterest,
                totalDebt: totalDebt,
                debtFreeDate: debtFreeDate,
                timeline: timeline,
                schedule: schedule,
                order: debtsCopy
            };
        }

        function displayActionPlan(plan, avalanche, snowball, strategy) {
            // Summary
            $('#plan-total-debt').text(formatCurrency(plan.totalDebt));
            $('#plan-total-interest').text(formatCurrency(plan.totalInterest));
            $('#plan-time-to-freedom').text(plan.months + ' months');
            $('#plan-freedom-date').text(plan.debtFreeDate.toLocaleDateString('en-US', { year: 'numeric', month: 'long' }));

            // Strategy comparison
            const avalancheSavings = snowball.totalInterest - avalanche.totalInterest;
            const snowballMotivation = avalanche.months - snowball.months;

            $('#avalanche-summary').html(
                '<strong>' + avalanche.months + ' months</strong>, ' + formatCurrency(avalanche.totalInterest) + ' in interest' +
                (avalancheSavings > 0 ? '<br><span class="success">Saves ' + formatCurrency(avalancheSavings) + ' vs snowball</span>' : '')
            );
            $('#snowball-summary').html(
                '<strong>' + snowball.months + ' months</strong>, ' + formatCurrency(snowball.totalInterest) + ' in interest' +
                (snowballMotivation !== 0 ? '<br><span class="info">Provides quicker wins for motivation</span>' : '')
            );

            // Timeline
            let timelineHtml = '';
            plan.timeline.forEach(function(item, index) {
                const date = new Date();
                date.setMonth(date.getMonth() + item.month);
                timelineHtml += '<div class="dedebtify-timeline-item">';
                timelineHtml += '  <div class="timeline-marker">' + (index + 1) + '</div>';
                timelineHtml += '  <div class="timeline-content">';
                timelineHtml += '    <h4>' + escapeHtml(item.name) + '</h4>';
                timelineHtml += '    <p><strong>Month ' + item.month + '</strong> (' + date.toLocaleDateString('en-US', { year: 'numeric', month: 'short' }) + ')';
                timelineHtml += '    <br>Starting Balance: ' + formatCurrency(item.balance) + '</p>';
                timelineHtml += '  </div>';
                timelineHtml += '</div>';
            });
            $('#dedebtify-timeline-items').html(timelineHtml);

            // Update action focus text
            const firstDebt = plan.order.find(d => d.balance > 0);
            if (firstDebt) {
                $('#action-focus-text').text(
                    'Apply all extra payments to ' + firstDebt.name + ' (' +
                    (strategy === 'avalanche' ? 'highest interest rate' : 'lowest balance') + ').'
                );
            }
        }

        function formatCurrency(amount) {
            return '$' + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }

        function formatPercentage(value) {
            return parseFloat(value).toFixed(2) + '%';
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
         * SNAPSHOTS FUNCTIONS
         * =========================================
         */

        let snapshotsData = [];

        function initSnapshots() {
            if ($('.dedebtify-snapshots').length) {
                loadSnapshots();
                initSnapshotsControls();
            }
        }

        function initSnapshotsControls() {
            $('#create-snapshot, #create-first-snapshot').on('click', function() {
                createSnapshot();
            });

            $('#compare-snapshots').on('click', function() {
                compareSnapshots();
            });

            $('#clear-comparison').on('click', function() {
                $('#comparison-results').slideUp();
                $('#snapshot-select-1, #snapshot-select-2').val('');
                $('#compare-snapshots').prop('disabled', true);
            });

            $('#snapshot-select-1, #snapshot-select-2').on('change', function() {
                const val1 = $('#snapshot-select-1').val();
                const val2 = $('#snapshot-select-2').val();
                $('#compare-snapshots').prop('disabled', !val1 || !val2);
            });
        }

        function loadSnapshots() {
            $.ajax({
                url: dedebtify.restUrl + 'snapshots',
                method: 'GET',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', dedebtify.restNonce);
                },
                success: function(response) {
                    snapshotsData = response;
                    if (response.length === 0) {
                        $('#snapshots-empty-state').show();
                        $('#dedebtify-snapshot-list, #dedebtify-progress-overview, #dedebtify-snapshot-comparison').hide();
                    } else {
                        $('#snapshots-empty-state').hide();
                        renderSnapshots(response);
                        populateSnapshotSelects(response);
                        calculateProgress(response);
                        $('#dedebtify-snapshot-list, #dedebtify-snapshot-comparison').show();
                    }
                },
                error: function() {
                    showMessage('Failed to load snapshots', 'error');
                }
            });
        }

        function createSnapshot() {
            if (!confirm('Create a new financial snapshot? This will capture your current financial state for comparison.')) {
                return;
            }

            // Get current data
            Promise.all([
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
                    url: dedebtify.restUrl + 'bills',
                    method: 'GET',
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-WP-Nonce', dedebtify.restNonce);
                    }
                })
            ]).then(function(results) {
                const cards = results[0];
                const loans = results[1];
                const bills = results[2];

                // Calculate totals
                const totalCreditCardDebt = cards.reduce((sum, c) => sum + parseFloat(c.balance || 0), 0);
                const totalLoanDebt = loans.reduce((sum, l) => sum + parseFloat(l.current_balance || 0), 0);
                const totalDebt = totalCreditCardDebt + totalLoanDebt;

                const totalCreditLimit = cards.reduce((sum, c) => sum + parseFloat(c.credit_limit || 0), 0);
                const creditUtilization = totalCreditLimit > 0 ? (totalCreditCardDebt / totalCreditLimit * 100) : 0;

                const totalPayments = cards.reduce((sum, c) => sum + parseFloat(c.minimum_payment || 0) + parseFloat(c.extra_payment || 0), 0) +
                                     loans.reduce((sum, l) => sum + parseFloat(l.monthly_payment || 0) + parseFloat(l.extra_payment || 0), 0);

                const totalBills = bills.reduce((sum, b) => sum + parseFloat(b.monthly_equivalent || 0), 0);

                // Create snapshot
                const snapshotData = {
                    snapshot_date: new Date().toISOString().split('T')[0],
                    total_debt: totalDebt,
                    total_credit_card_debt: totalCreditCardDebt,
                    total_loan_debt: totalLoanDebt,
                    total_mortgage_debt: 0,
                    total_monthly_payments: totalPayments,
                    total_monthly_bills: totalBills,
                    credit_utilization: creditUtilization,
                    credit_card_count: cards.length,
                    loan_count: loans.length
                };

                $.ajax({
                    url: dedebtify.restUrl + 'snapshots',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(snapshotData),
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-WP-Nonce', dedebtify.restNonce);
                    },
                    success: function() {
                        showMessage('Snapshot created successfully!', 'success');
                        loadSnapshots();
                    },
                    error: function() {
                        showMessage('Failed to create snapshot', 'error');
                    }
                });
            });
        }

        function renderSnapshots(snapshots) {
            let html = '<div class="dedebtify-snapshots-grid">';

            snapshots.forEach(function(snapshot) {
                html += '<div class="dd-card">';
                html += '  <div class="dd-card-header">';
                html += '    <h4 class="dd-card-title" style="font-size: 1rem;">' + new Date(snapshot.snapshot_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) + '</h4>';
                html += '  </div>';
                html += '  <div class="dd-card-content">';
                html += '    <div class="dedebtify-snapshot-metrics">';
                html += '      <div class="dedebtify-metric">';
                html += '        <span class="metric-label">Total Debt</span>';
                html += '        <span class="metric-value">' + formatCurrency(snapshot.total_debt) + '</span>';
                html += '      </div>';
                html += '      <div class="dedebtify-metric">';
                html += '        <span class="metric-label">Credit Utilization</span>';
                html += '        <span class="metric-value">' + formatPercentage(snapshot.credit_utilization) + '</span>';
                html += '      </div>';
                html += '    </div>';
                html += '  </div>';
                html += '</div>';
            });

            html += '</div>';
            $('#snapshots-list-container').html(html);
        }

        function populateSnapshotSelects(snapshots) {
            let options = '<option value="">Select a snapshot...</option>';
            snapshots.forEach(function(snapshot) {
                const date = new Date(snapshot.snapshot_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
                options += '<option value="' + snapshot.id + '">' + date + ' - ' + formatCurrency(snapshot.total_debt) + '</option>';
            });
            $('#snapshot-select-1, #snapshot-select-2').html(options);
        }

        function compareSnapshots() {
            const id1 = $('#snapshot-select-1').val();
            const id2 = $('#snapshot-select-2').val();

            const snap1 = snapshotsData.find(s => s.id == id1);
            const snap2 = snapshotsData.find(s => s.id == id2);

            if (!snap1 || !snap2) return;

            // Populate snapshot 1
            $('#snapshot1-date').text(new Date(snap1.snapshot_date).toLocaleDateString());
            $('#snapshot1-debt').text(formatCurrency(snap1.total_debt));
            $('#snapshot1-payments').text(formatCurrency(snap1.total_monthly_payments));
            $('#snapshot1-dti').text(formatPercentage(snap1.debt_to_income_ratio || 0));
            $('#snapshot1-util').text(formatPercentage(snap1.credit_utilization));
            $('#snapshot1-cards').text(snap1.credit_card_count);
            $('#snapshot1-loans').text(snap1.loan_count);

            // Populate snapshot 2
            $('#snapshot2-date').text(new Date(snap2.snapshot_date).toLocaleDateString());
            $('#snapshot2-debt').text(formatCurrency(snap2.total_debt));
            $('#snapshot2-payments').text(formatCurrency(snap2.total_monthly_payments));
            $('#snapshot2-dti').text(formatPercentage(snap2.debt_to_income_ratio || 0));
            $('#snapshot2-util').text(formatPercentage(snap2.credit_utilization));
            $('#snapshot2-cards').text(snap2.credit_card_count);
            $('#snapshot2-loans').text(snap2.loan_count);

            // Calculate changes
            const debtChange = snap2.total_debt - snap1.total_debt;
            const paymentChange = snap2.total_monthly_payments - snap1.total_monthly_payments;
            const dtiChange = (snap2.debt_to_income_ratio || 0) - (snap1.debt_to_income_ratio || 0);
            const utilChange = snap2.credit_utilization - snap1.credit_utilization;

            displayChange('#change-debt', debtChange, true);
            displayChange('#change-payments', paymentChange, true);
            displayChange('#change-dti', dtiChange, false);
            displayChange('#change-util', utilChange, false);

            // Summary text
            const summaryText = debtChange < 0
                ? '<strong>Great job!</strong> You reduced your debt by ' + formatCurrency(Math.abs(debtChange)) + '!'
                : debtChange > 0
                ? 'Your debt increased by ' + formatCurrency(debtChange) + '. Stay focused on your plan.'
                : 'Your debt remained the same. Keep pushing forward!';

            const summaryClass = debtChange < 0 ? 'success' : debtChange > 0 ? 'warning' : 'info';

            $('#comparison-summary-text').html('<p class="' + summaryClass + '">' + summaryText + '</p>');

            $('#comparison-results').slideDown();
        }

        function displayChange(selector, value, isCurrency) {
            const $el = $(selector);
            const formatted = isCurrency ? formatCurrency(Math.abs(value)) : formatPercentage(Math.abs(value));
            const sign = value > 0 ? '+' : value < 0 ? '-' : '';
            const className = value < 0 ? 'positive' : value > 0 ? 'negative' : 'neutral';

            $el.find('.change-value').text(sign + formatted).removeClass('positive negative neutral').addClass(className);
            $el.find('.change-icon').html(value < 0 ? '↓' : value > 0 ? '↑' : '→');
        }

        function calculateProgress(snapshots) {
            if (snapshots.length < 2) {
                $('#dedebtify-progress-overview').hide();
                return;
            }

            const first = snapshots[0];
            const latest = snapshots[snapshots.length - 1];

            const debtReduced = first.total_debt - latest.total_debt;
            const percentReduced = first.total_debt > 0 ? (debtReduced / first.total_debt * 100) : 0;
            const dtiChange = (first.debt_to_income_ratio || 0) - (latest.debt_to_income_ratio || 0);
            const monthsTracked = snapshots.length;
            const avgMonthly = debtReduced / monthsTracked;

            $('#progress-debt-reduced').text(formatCurrency(debtReduced));
            $('#progress-debt-percent').text(formatPercentage(percentReduced) + ' reduction');
            $('#progress-dti-change').text(formatPercentage(dtiChange));
            $('#progress-months').text(monthsTracked);
            $('#progress-date-range').text(
                new Date(first.snapshot_date).toLocaleDateString('en-US', { month: 'short', year: 'numeric' }) + ' - ' +
                new Date(latest.snapshot_date).toLocaleDateString('en-US', { month: 'short', year: 'numeric' })
            );
            $('#progress-avg-monthly').text(formatCurrency(avgMonthly));

            $('#dedebtify-progress-overview').show();
        }

        // Initialize
        initActionPlan();
        initSnapshots();

    });

})(jQuery);
