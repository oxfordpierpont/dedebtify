/**
 * DeDebtify Calculator JavaScript
 *
 * Financial calculation functions for client-side use
 *
 * @since      1.0.0
 * @package    Dedebtify
 */

(function(window) {
    'use strict';

    /**
     * DeDebtify Calculator Object
     */
    const DedebtifyCalculator = {

        /**
         * Calculate months to pay off debt
         *
         * @param {number} balance - Current balance
         * @param {number} interestRate - Annual interest rate (percentage)
         * @param {number} monthlyPayment - Monthly payment amount
         * @return {number} - Months to payoff
         */
        calculateMonthsToPayoff: function(balance, interestRate, monthlyPayment) {
            // Convert annual rate to monthly decimal
            const monthlyRate = (interestRate / 100) / 12;

            // If payment doesn't cover interest, return infinity
            if (monthlyPayment <= balance * monthlyRate) {
                return Infinity;
            }

            // Calculate months using logarithmic formula
            const months = -Math.log(1 - (balance * monthlyRate / monthlyPayment)) /
                          Math.log(1 + monthlyRate);

            return Math.ceil(months);
        },

        /**
         * Calculate total interest paid
         *
         * @param {number} balance - Current balance
         * @param {number} monthlyPayment - Monthly payment amount
         * @param {number} months - Number of months to pay off
         * @return {number} - Total interest paid
         */
        calculateTotalInterest: function(balance, monthlyPayment, months) {
            const totalPaid = monthlyPayment * months;
            const totalInterest = totalPaid - balance;
            return Math.max(0, totalInterest);
        },

        /**
         * Calculate loan payment using amortization formula
         *
         * @param {number} principal - Loan principal amount
         * @param {number} annualRate - Annual interest rate (percentage)
         * @param {number} termMonths - Term in months
         * @return {number} - Monthly payment amount
         */
        calculateLoanPayment: function(principal, annualRate, termMonths) {
            const monthlyRate = (annualRate / 100) / 12;

            if (monthlyRate === 0) {
                return principal / termMonths;
            }

            const payment = principal *
                           (monthlyRate * Math.pow(1 + monthlyRate, termMonths)) /
                           (Math.pow(1 + monthlyRate, termMonths) - 1);

            return Math.round(payment * 100) / 100;
        },

        /**
         * Calculate credit utilization percentage
         *
         * @param {number} balance - Current balance
         * @param {number} creditLimit - Credit limit
         * @return {number} - Utilization percentage
         */
        calculateUtilization: function(balance, creditLimit) {
            if (creditLimit <= 0) {
                return 0;
            }

            return Math.round((balance / creditLimit) * 100 * 10) / 10;
        },

        /**
         * Calculate debt-to-income ratio
         *
         * @param {number} monthlyDebt - Total monthly debt payments
         * @param {number} monthlyIncome - Monthly income
         * @return {number} - DTI percentage
         */
        calculateDTI: function(monthlyDebt, monthlyIncome) {
            if (monthlyIncome <= 0) {
                return 0;
            }

            return Math.round((monthlyDebt / monthlyIncome) * 100 * 10) / 10;
        },

        /**
         * Convert bill frequency to monthly equivalent
         *
         * @param {number} amount - Bill amount
         * @param {string} frequency - Frequency (weekly, bi-weekly, monthly, quarterly, annually)
         * @return {number} - Monthly equivalent amount
         */
        convertToMonthly: function(amount, frequency) {
            switch (frequency) {
                case 'weekly':
                    return amount * 52 / 12;
                case 'bi-weekly':
                    return amount * 26 / 12;
                case 'monthly':
                    return amount;
                case 'quarterly':
                    return amount / 3;
                case 'annually':
                    return amount / 12;
                default:
                    return amount;
            }
        },

        /**
         * Calculate payoff date from current date
         *
         * @param {number} months - Number of months from now
         * @return {string} - Formatted date
         */
        calculatePayoffDate: function(months) {
            if (months === Infinity) {
                return 'Never (payment too low)';
            }

            const date = new Date();
            date.setMonth(date.getMonth() + months);

            const options = { year: 'numeric', month: 'long' };
            return date.toLocaleDateString('en-US', options);
        },

        /**
         * Generate amortization schedule
         *
         * @param {number} principal - Loan principal
         * @param {number} annualRate - Annual interest rate (percentage)
         * @param {number} monthlyPayment - Monthly payment
         * @param {number} maxMonths - Maximum months to calculate (default 360)
         * @return {Array} - Array of payment objects
         */
        generateAmortizationSchedule: function(principal, annualRate, monthlyPayment, maxMonths = 360) {
            const schedule = [];
            const monthlyRate = (annualRate / 100) / 12;
            let balance = principal;
            let month = 0;

            while (balance > 0 && month < maxMonths) {
                month++;

                const interestPayment = balance * monthlyRate;
                let principalPayment = monthlyPayment - interestPayment;

                // Last payment adjustment
                if (principalPayment > balance) {
                    principalPayment = balance;
                    monthlyPayment = principalPayment + interestPayment;
                }

                balance -= principalPayment;

                schedule.push({
                    month: month,
                    payment: Math.round(monthlyPayment * 100) / 100,
                    principal: Math.round(principalPayment * 100) / 100,
                    interest: Math.round(interestPayment * 100) / 100,
                    balance: Math.round(balance * 100) / 100
                });

                // Prevent infinite loop
                if (principalPayment <= 0) {
                    break;
                }
            }

            return schedule;
        },

        /**
         * Calculate progress percentage
         *
         * @param {number} current - Current amount
         * @param {number} target - Target amount
         * @return {number} - Progress percentage
         */
        calculateProgress: function(current, target) {
            if (target <= 0) {
                return 0;
            }

            return Math.min(100, Math.round((current / target) * 100 * 10) / 10);
        },

        /**
         * Calculate months to reach goal
         *
         * @param {number} currentAmount - Current saved amount
         * @param {number} targetAmount - Target goal amount
         * @param {number} monthlyContribution - Monthly contribution
         * @return {number} - Months to reach goal
         */
        calculateMonthsToGoal: function(currentAmount, targetAmount, monthlyContribution) {
            if (monthlyContribution <= 0) {
                return Infinity;
            }

            const remaining = targetAmount - currentAmount;

            if (remaining <= 0) {
                return 0;
            }

            return Math.ceil(remaining / monthlyContribution);
        },

        /**
         * Compare two payoff scenarios
         *
         * @param {Object} scenario1 - First scenario {balance, rate, payment}
         * @param {Object} scenario2 - Second scenario {balance, rate, payment}
         * @return {Object} - Comparison results
         */
        compareScenarios: function(scenario1, scenario2) {
            const months1 = this.calculateMonthsToPayoff(scenario1.balance, scenario1.rate, scenario1.payment);
            const months2 = this.calculateMonthsToPayoff(scenario2.balance, scenario2.rate, scenario2.payment);

            const interest1 = this.calculateTotalInterest(scenario1.balance, scenario1.payment, months1);
            const interest2 = this.calculateTotalInterest(scenario2.balance, scenario2.payment, months2);

            return {
                scenario1: {
                    months: months1,
                    totalInterest: interest1,
                    totalPaid: scenario1.payment * months1
                },
                scenario2: {
                    months: months2,
                    totalInterest: interest2,
                    totalPaid: scenario2.payment * months2
                },
                savings: {
                    months: months1 - months2,
                    interest: interest1 - interest2,
                    total: (scenario1.payment * months1) - (scenario2.payment * months2)
                }
            };
        },

        /**
         * Format currency
         *
         * @param {number} amount - Amount to format
         * @return {string} - Formatted currency string
         */
        formatCurrency: function(amount) {
            return '$' + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        },

        /**
         * Format percentage
         *
         * @param {number} value - Value to format
         * @return {string} - Formatted percentage string
         */
        formatPercentage: function(value) {
            return parseFloat(value).toFixed(1) + '%';
        }
    };

    // Expose calculator to window object
    window.DedebtifyCalculator = DedebtifyCalculator;

})(window);
