<?php
/**
 * Financial calculations engine.
 *
 * This class handles all financial calculations including debt payoff,
 * interest calculations, and financial metrics.
 *
 * @since      1.0.0
 * @package    Dedebtify
 * @subpackage Dedebtify/includes
 */

class Dedebtify_Calculations {

    /**
     * Calculate months to pay off a credit card.
     *
     * @since    1.0.0
     * @param    float    $balance           Current balance
     * @param    float    $interest_rate     Annual interest rate (percentage)
     * @param    float    $monthly_payment   Monthly payment amount
     * @return   int|float                   Months to payoff, or INF if payment doesn't cover interest
     */
    public static function calculate_months_to_payoff( $balance, $interest_rate, $monthly_payment ) {
        // Convert annual rate to monthly decimal
        $monthly_rate = ( $interest_rate / 100 ) / 12;

        // If payment doesn't cover interest, return infinity
        if ( $monthly_payment <= $balance * $monthly_rate ) {
            return INF;
        }

        // Calculate months using logarithmic formula
        // n = -log(1 - (B * r / P)) / log(1 + r)
        // Where: B = balance, r = monthly rate, P = monthly payment
        $months = -log( 1 - ( $balance * $monthly_rate / $monthly_payment ) ) / log( 1 + $monthly_rate );

        return ceil( $months );
    }

    /**
     * Calculate total interest paid over the life of debt.
     *
     * @since    1.0.0
     * @param    float    $balance           Current balance
     * @param    float    $monthly_payment   Monthly payment amount
     * @param    int      $months            Number of months to pay off
     * @return   float                       Total interest paid
     */
    public static function calculate_total_interest( $balance, $monthly_payment, $months ) {
        $total_paid = $monthly_payment * $months;
        $total_interest = $total_paid - $balance;
        return max( 0, $total_interest );
    }

    /**
     * Calculate loan payment using amortization formula.
     *
     * @since    1.0.0
     * @param    float    $principal         Loan principal amount
     * @param    float    $annual_rate       Annual interest rate (percentage)
     * @param    int      $term_months       Term in months
     * @return   float                       Monthly payment amount
     */
    public static function calculate_loan_payment( $principal, $annual_rate, $term_months ) {
        $monthly_rate = ( $annual_rate / 100 ) / 12;

        if ( $monthly_rate == 0 ) {
            return $principal / $term_months;
        }

        $payment = $principal *
                   ( $monthly_rate * pow( 1 + $monthly_rate, $term_months ) ) /
                   ( pow( 1 + $monthly_rate, $term_months ) - 1 );

        return round( $payment, 2 );
    }

    /**
     * Calculate credit utilization percentage.
     *
     * @since    1.0.0
     * @param    float    $balance           Current balance
     * @param    float    $credit_limit      Credit limit
     * @return   float                       Utilization percentage
     */
    public static function calculate_utilization( $balance, $credit_limit ) {
        if ( $credit_limit <= 0 ) {
            return 0;
        }

        return round( ( $balance / $credit_limit ) * 100, 2 );
    }

    /**
     * Calculate debt-to-income ratio.
     *
     * @since    1.0.0
     * @param    float    $monthly_debt      Total monthly debt payments
     * @param    float    $monthly_income    Monthly income
     * @return   float                       DTI percentage
     */
    public static function calculate_dti( $monthly_debt, $monthly_income ) {
        if ( $monthly_income <= 0 ) {
            return 0;
        }

        return round( ( $monthly_debt / $monthly_income ) * 100, 2 );
    }

    /**
     * Convert bill frequency to monthly equivalent.
     *
     * @since    1.0.0
     * @param    float    $amount       Bill amount
     * @param    string   $frequency    Frequency (weekly, bi-weekly, monthly, quarterly, annually)
     * @return   float                  Monthly equivalent amount
     */
    public static function convert_to_monthly( $amount, $frequency ) {
        switch ( $frequency ) {
            case 'weekly':
                return $amount * 52 / 12;
            case 'bi-weekly':
                return $amount * 26 / 12;
            case 'monthly':
                return $amount;
            case 'quarterly':
                return $amount / 3;
            case 'annually':
                return $amount / 12;
            default:
                return $amount;
        }
    }

    /**
     * Calculate payoff date from current date.
     *
     * @since    1.0.0
     * @param    int      $months       Number of months from now
     * @return   string                 Formatted date
     */
    public static function calculate_payoff_date( $months ) {
        if ( $months === INF ) {
            return __( 'Never (payment too low)', 'dedebtify' );
        }

        $date = new DateTime();
        $date->modify( "+{$months} months" );
        return $date->format( 'F Y' );
    }

    /**
     * Get all user's credit cards.
     *
     * @since    1.0.0
     * @param    int      $user_id      User ID
     * @return   array                  Array of credit card posts
     */
    public static function get_user_credit_cards( $user_id = 0 ) {
        if ( ! $user_id ) {
            $user_id = get_current_user_id();
        }

        $args = array(
            'post_type' => 'dd_credit_card',
            'author' => $user_id,
            'posts_per_page' => -1,
            'post_status' => 'publish',
        );

        return get_posts( $args );
    }

    /**
     * Get all user's loans.
     *
     * @since    1.0.0
     * @param    int      $user_id      User ID
     * @return   array                  Array of loan posts
     */
    public static function get_user_loans( $user_id = 0 ) {
        if ( ! $user_id ) {
            $user_id = get_current_user_id();
        }

        $args = array(
            'post_type' => 'dd_loan',
            'author' => $user_id,
            'posts_per_page' => -1,
            'post_status' => 'publish',
        );

        return get_posts( $args );
    }

    /**
     * Get user's mortgage.
     *
     * @since    1.0.0
     * @param    int      $user_id      User ID
     * @return   object|null            Mortgage post or null
     */
    public static function get_user_mortgage( $user_id = 0 ) {
        if ( ! $user_id ) {
            $user_id = get_current_user_id();
        }

        $args = array(
            'post_type' => 'dd_mortgage',
            'author' => $user_id,
            'posts_per_page' => 1,
            'post_status' => 'publish',
        );

        $mortgages = get_posts( $args );
        return ! empty( $mortgages ) ? $mortgages[0] : null;
    }

    /**
     * Get all user's bills.
     *
     * @since    1.0.0
     * @param    int      $user_id      User ID
     * @return   array                  Array of bill posts
     */
    public static function get_user_bills( $user_id = 0 ) {
        if ( ! $user_id ) {
            $user_id = get_current_user_id();
        }

        $args = array(
            'post_type' => 'dd_bill',
            'author' => $user_id,
            'posts_per_page' => -1,
            'post_status' => 'publish',
        );

        return get_posts( $args );
    }

    /**
     * Get all user's goals.
     *
     * @since    1.0.0
     * @param    int      $user_id      User ID
     * @return   array                  Array of goal posts
     */
    public static function get_user_goals( $user_id = 0 ) {
        if ( ! $user_id ) {
            $user_id = get_current_user_id();
        }

        $args = array(
            'post_type' => 'dd_goal',
            'author' => $user_id,
            'posts_per_page' => -1,
            'post_status' => 'publish',
        );

        return get_posts( $args );
    }

    /**
     * Calculate total credit card debt for user.
     *
     * @since    1.0.0
     * @param    int      $user_id      User ID
     * @return   float                  Total credit card debt
     */
    public static function get_total_credit_card_debt( $user_id = 0 ) {
        $cards = self::get_user_credit_cards( $user_id );
        $total = 0;

        foreach ( $cards as $card ) {
            $status = get_post_meta( $card->ID, 'status', true );
            if ( $status !== 'paid_off' && $status !== 'closed' ) {
                $balance = get_post_meta( $card->ID, 'balance', true );
                $total += floatval( $balance );
            }
        }

        return $total;
    }

    /**
     * Calculate total loan debt for user.
     *
     * @since    1.0.0
     * @param    int      $user_id      User ID
     * @return   float                  Total loan debt
     */
    public static function get_total_loan_debt( $user_id = 0 ) {
        $loans = self::get_user_loans( $user_id );
        $total = 0;

        foreach ( $loans as $loan ) {
            $balance = get_post_meta( $loan->ID, 'current_balance', true );
            $total += floatval( $balance );
        }

        return $total;
    }

    /**
     * Calculate total mortgage debt for user.
     *
     * @since    1.0.0
     * @param    int      $user_id      User ID
     * @return   float                  Total mortgage debt
     */
    public static function get_total_mortgage_debt( $user_id = 0 ) {
        $mortgage = self::get_user_mortgage( $user_id );

        if ( $mortgage ) {
            return floatval( get_post_meta( $mortgage->ID, 'current_balance', true ) );
        }

        return 0;
    }

    /**
     * Calculate total debt for user.
     *
     * @since    1.0.0
     * @param    int      $user_id      User ID
     * @return   float                  Total debt
     */
    public static function get_total_debt( $user_id = 0 ) {
        $cc_debt = self::get_total_credit_card_debt( $user_id );
        $loan_debt = self::get_total_loan_debt( $user_id );
        $mortgage_debt = self::get_total_mortgage_debt( $user_id );

        return $cc_debt + $loan_debt + $mortgage_debt;
    }

    /**
     * Calculate total monthly debt payments for user.
     *
     * @since    1.0.0
     * @param    int      $user_id      User ID
     * @return   float                  Total monthly payments
     */
    public static function get_total_monthly_payments( $user_id = 0 ) {
        $total = 0;

        // Credit cards
        $cards = self::get_user_credit_cards( $user_id );
        foreach ( $cards as $card ) {
            $status = get_post_meta( $card->ID, 'status', true );
            if ( $status !== 'paid_off' && $status !== 'closed' ) {
                $min_payment = get_post_meta( $card->ID, 'minimum_payment', true );
                $extra_payment = get_post_meta( $card->ID, 'extra_payment', true );
                $total += floatval( $min_payment ) + floatval( $extra_payment );
            }
        }

        // Loans
        $loans = self::get_user_loans( $user_id );
        foreach ( $loans as $loan ) {
            $monthly_payment = get_post_meta( $loan->ID, 'monthly_payment', true );
            $extra_payment = get_post_meta( $loan->ID, 'extra_payment', true );
            $total += floatval( $monthly_payment ) + floatval( $extra_payment );
        }

        // Mortgage
        $mortgage = self::get_user_mortgage( $user_id );
        if ( $mortgage ) {
            $monthly_payment = get_post_meta( $mortgage->ID, 'monthly_payment', true );
            $extra_payment = get_post_meta( $mortgage->ID, 'extra_payment', true );
            $property_tax = get_post_meta( $mortgage->ID, 'property_tax', true );
            $insurance = get_post_meta( $mortgage->ID, 'homeowners_insurance', true );
            $pmi = get_post_meta( $mortgage->ID, 'pmi', true );

            $total += floatval( $monthly_payment );
            $total += floatval( $extra_payment );
            $total += floatval( $property_tax ) / 12;
            $total += floatval( $insurance ) / 12;
            $total += floatval( $pmi );
        }

        return $total;
    }

    /**
     * Calculate total monthly bills for user.
     *
     * @since    1.0.0
     * @param    int      $user_id      User ID
     * @return   float                  Total monthly bills
     */
    public static function get_total_monthly_bills( $user_id = 0 ) {
        $bills = self::get_user_bills( $user_id );
        $total = 0;

        foreach ( $bills as $bill ) {
            $amount = get_post_meta( $bill->ID, 'amount', true );
            $frequency = get_post_meta( $bill->ID, 'frequency', true );
            $total += self::convert_to_monthly( floatval( $amount ), $frequency );
        }

        return $total;
    }

    /**
     * Calculate overall credit utilization for user.
     *
     * @since    1.0.0
     * @param    int      $user_id      User ID
     * @return   float                  Credit utilization percentage
     */
    public static function get_overall_credit_utilization( $user_id = 0 ) {
        $cards = self::get_user_credit_cards( $user_id );
        $total_balance = 0;
        $total_limit = 0;

        foreach ( $cards as $card ) {
            $status = get_post_meta( $card->ID, 'status', true );
            if ( $status !== 'closed' ) {
                $balance = get_post_meta( $card->ID, 'balance', true );
                $limit = get_post_meta( $card->ID, 'credit_limit', true );
                $total_balance += floatval( $balance );
                $total_limit += floatval( $limit );
            }
        }

        return self::calculate_utilization( $total_balance, $total_limit );
    }

    /**
     * Calculate user's debt-to-income ratio.
     *
     * @since    1.0.0
     * @param    int      $user_id      User ID
     * @return   float                  DTI percentage
     */
    public static function get_user_dti( $user_id = 0 ) {
        if ( ! $user_id ) {
            $user_id = get_current_user_id();
        }

        $monthly_payments = self::get_total_monthly_payments( $user_id );
        $monthly_income = get_user_meta( $user_id, 'dd_monthly_income', true );

        return self::calculate_dti( $monthly_payments, floatval( $monthly_income ) );
    }

    /**
     * Generate debt avalanche payoff order.
     * Sorts debts by highest interest rate first.
     *
     * @since    1.0.0
     * @param    int      $user_id      User ID
     * @return   array                  Ordered array of debts
     */
    public static function get_avalanche_order( $user_id = 0 ) {
        $debts = array();

        // Get all credit cards
        $cards = self::get_user_credit_cards( $user_id );
        foreach ( $cards as $card ) {
            $status = get_post_meta( $card->ID, 'status', true );
            if ( $status !== 'paid_off' && $status !== 'closed' ) {
                $debts[] = array(
                    'id' => $card->ID,
                    'type' => 'credit_card',
                    'name' => $card->post_title,
                    'balance' => floatval( get_post_meta( $card->ID, 'balance', true ) ),
                    'interest_rate' => floatval( get_post_meta( $card->ID, 'interest_rate', true ) ),
                    'minimum_payment' => floatval( get_post_meta( $card->ID, 'minimum_payment', true ) ),
                );
            }
        }

        // Get all loans
        $loans = self::get_user_loans( $user_id );
        foreach ( $loans as $loan ) {
            $debts[] = array(
                'id' => $loan->ID,
                'type' => 'loan',
                'name' => $loan->post_title,
                'balance' => floatval( get_post_meta( $loan->ID, 'current_balance', true ) ),
                'interest_rate' => floatval( get_post_meta( $loan->ID, 'interest_rate', true ) ),
                'minimum_payment' => floatval( get_post_meta( $loan->ID, 'monthly_payment', true ) ),
            );
        }

        // Sort by interest rate (highest first)
        usort( $debts, function( $a, $b ) {
            return $b['interest_rate'] <=> $a['interest_rate'];
        });

        return $debts;
    }

    /**
     * Generate debt snowball payoff order.
     * Sorts debts by smallest balance first.
     *
     * @since    1.0.0
     * @param    int      $user_id      User ID
     * @return   array                  Ordered array of debts
     */
    public static function get_snowball_order( $user_id = 0 ) {
        $debts = array();

        // Get all credit cards
        $cards = self::get_user_credit_cards( $user_id );
        foreach ( $cards as $card ) {
            $status = get_post_meta( $card->ID, 'status', true );
            if ( $status !== 'paid_off' && $status !== 'closed' ) {
                $debts[] = array(
                    'id' => $card->ID,
                    'type' => 'credit_card',
                    'name' => $card->post_title,
                    'balance' => floatval( get_post_meta( $card->ID, 'balance', true ) ),
                    'interest_rate' => floatval( get_post_meta( $card->ID, 'interest_rate', true ) ),
                    'minimum_payment' => floatval( get_post_meta( $card->ID, 'minimum_payment', true ) ),
                );
            }
        }

        // Get all loans
        $loans = self::get_user_loans( $user_id );
        foreach ( $loans as $loan ) {
            $debts[] = array(
                'id' => $loan->ID,
                'type' => 'loan',
                'name' => $loan->post_title,
                'balance' => floatval( get_post_meta( $loan->ID, 'current_balance', true ) ),
                'interest_rate' => floatval( get_post_meta( $loan->ID, 'interest_rate', true ) ),
                'minimum_payment' => floatval( get_post_meta( $loan->ID, 'monthly_payment', true ) ),
            );
        }

        // Sort by balance (smallest first)
        usort( $debts, function( $a, $b ) {
            return $a['balance'] <=> $b['balance'];
        });

        return $debts;
    }

    /**
     * Create a financial snapshot for user.
     *
     * @since    1.0.0
     * @param    int      $user_id      User ID
     * @return   int|WP_Error           Post ID on success, WP_Error on failure
     */
    public static function create_snapshot( $user_id = 0 ) {
        if ( ! $user_id ) {
            $user_id = get_current_user_id();
        }

        // Calculate all values
        $total_debt = self::get_total_debt( $user_id );
        $cc_debt = self::get_total_credit_card_debt( $user_id );
        $loan_debt = self::get_total_loan_debt( $user_id );
        $mortgage_debt = self::get_total_mortgage_debt( $user_id );
        $monthly_payments = self::get_total_monthly_payments( $user_id );
        $monthly_bills = self::get_total_monthly_bills( $user_id );
        $monthly_income = get_user_meta( $user_id, 'dd_monthly_income', true );
        $dti = self::get_user_dti( $user_id );
        $credit_util = self::get_overall_credit_utilization( $user_id );

        // Create snapshot post
        $post_data = array(
            'post_type' => 'dd_snapshot',
            'post_title' => 'Snapshot - ' . date( 'F j, Y' ),
            'post_status' => 'publish',
            'post_author' => $user_id,
        );

        $post_id = wp_insert_post( $post_data );

        if ( is_wp_error( $post_id ) ) {
            return $post_id;
        }

        // Save all meta data
        update_post_meta( $post_id, 'snapshot_date', date( 'Y-m-d' ) );
        update_post_meta( $post_id, 'total_debt', $total_debt );
        update_post_meta( $post_id, 'total_credit_card_debt', $cc_debt );
        update_post_meta( $post_id, 'total_loan_debt', $loan_debt );
        update_post_meta( $post_id, 'total_mortgage_debt', $mortgage_debt );
        update_post_meta( $post_id, 'total_monthly_payments', $monthly_payments );
        update_post_meta( $post_id, 'total_monthly_bills', $monthly_bills );
        update_post_meta( $post_id, 'monthly_income', $monthly_income );
        update_post_meta( $post_id, 'debt_to_income_ratio', $dti );
        update_post_meta( $post_id, 'credit_utilization', $credit_util );

        return $post_id;
    }
}
