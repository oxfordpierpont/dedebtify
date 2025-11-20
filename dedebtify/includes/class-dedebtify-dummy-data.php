<?php
/**
 * Dummy Data Generator
 *
 * Creates sample data for testing
 *
 * @since      1.0.0
 * @package    Dedebtify
 * @subpackage Dedebtify/includes
 */

class Dedebtify_Dummy_Data {

    /**
     * Generate all dummy data
     */
    public static function generate_all( $user_id = 0 ) {
        if ( ! $user_id ) {
            $user_id = get_current_user_id();
        }

        // Mark that dummy data has been created
        update_user_meta( $user_id, 'dd_has_dummy_data', true );

        self::create_credit_cards( $user_id );
        self::create_loans( $user_id );
        self::create_mortgages( $user_id );
        self::create_bills( $user_id );
        self::create_goals( $user_id );
        self::create_snapshots( $user_id );

        // Set monthly income
        update_user_meta( $user_id, 'dd_monthly_income', 6500 );
    }

    /**
     * Delete all dummy data
     */
    public static function delete_all( $user_id = 0 ) {
        if ( ! $user_id ) {
            $user_id = get_current_user_id();
        }

        // Delete all user's posts
        $post_types = array( 'dd_credit_card', 'dd_loan', 'dd_mortgage', 'dd_bill', 'dd_goal', 'dd_snapshot' );

        foreach ( $post_types as $post_type ) {
            $posts = get_posts( array(
                'post_type' => $post_type,
                'author' => $user_id,
                'posts_per_page' => -1,
                'post_status' => 'any',
            ) );

            foreach ( $posts as $post ) {
                wp_delete_post( $post->ID, true );
            }
        }

        // Remove dummy data flag
        delete_user_meta( $user_id, 'dd_has_dummy_data' );
        delete_user_meta( $user_id, 'dd_monthly_income' );
    }

    /**
     * Create dummy credit cards
     */
    private static function create_credit_cards( $user_id ) {
        $cards = array(
            array(
                'name' => 'Chase Freedom Unlimited',
                'balance' => 3250.00,
                'credit_limit' => 10000.00,
                'interest_rate' => 18.99,
                'minimum_payment' => 97.50,
                'extra_payment' => 150.00,
                'due_date' => 15,
                'auto_pay' => '1',
                'status' => 'active',
            ),
            array(
                'name' => 'Discover It Cash Back',
                'balance' => 1875.50,
                'credit_limit' => 5000.00,
                'interest_rate' => 16.49,
                'minimum_payment' => 56.25,
                'extra_payment' => 100.00,
                'due_date' => 5,
                'auto_pay' => '1',
                'status' => 'active',
            ),
            array(
                'name' => 'Capital One Quicksilver',
                'balance' => 4500.00,
                'credit_limit' => 8000.00,
                'interest_rate' => 22.99,
                'minimum_payment' => 135.00,
                'extra_payment' => 75.00,
                'due_date' => 20,
                'auto_pay' => '0',
                'status' => 'active',
            ),
        );

        foreach ( $cards as $card ) {
            $post_id = wp_insert_post( array(
                'post_title' => $card['name'],
                'post_type' => 'dd_credit_card',
                'post_status' => 'publish',
                'post_author' => $user_id,
            ) );

            if ( $post_id ) {
                unset( $card['name'] );
                foreach ( $card as $key => $value ) {
                    update_post_meta( $post_id, $key, $value );
                }
            }
        }
    }

    /**
     * Create dummy loans
     */
    private static function create_loans( $user_id ) {
        $loans = array(
            array(
                'name' => 'Honda Civic Auto Loan',
                'loan_type' => 'auto',
                'principal' => 28000.00,
                'current_balance' => 18500.00,
                'interest_rate' => 4.99,
                'term_months' => 60,
                'monthly_payment' => 525.00,
                'start_date' => date( 'Y-m-d', strtotime( '-18 months' ) ),
                'extra_payment' => 50.00,
            ),
            array(
                'name' => 'Federal Student Loan',
                'loan_type' => 'student',
                'principal' => 35000.00,
                'current_balance' => 32750.00,
                'interest_rate' => 5.50,
                'term_months' => 120,
                'monthly_payment' => 380.00,
                'start_date' => date( 'Y-m-d', strtotime( '-12 months' ) ),
                'extra_payment' => 20.00,
            ),
        );

        foreach ( $loans as $loan ) {
            $post_id = wp_insert_post( array(
                'post_title' => $loan['name'],
                'post_type' => 'dd_loan',
                'post_status' => 'publish',
                'post_author' => $user_id,
            ) );

            if ( $post_id ) {
                unset( $loan['name'] );
                foreach ( $loan as $key => $value ) {
                    update_post_meta( $post_id, $key, $value );
                }
            }
        }
    }

    /**
     * Create dummy mortgage
     */
    private static function create_mortgages( $user_id ) {
        $post_id = wp_insert_post( array(
            'post_title' => 'Primary Residence',
            'post_type' => 'dd_mortgage',
            'post_status' => 'publish',
            'post_author' => $user_id,
        ) );

        if ( $post_id ) {
            update_post_meta( $post_id, 'property_address', '123 Main Street, Springfield, IL 62701' );
            update_post_meta( $post_id, 'loan_amount', 250000.00 );
            update_post_meta( $post_id, 'current_balance', 238500.00 );
            update_post_meta( $post_id, 'interest_rate', 3.75 );
            update_post_meta( $post_id, 'term_years', 30 );
            update_post_meta( $post_id, 'monthly_payment', 1157.79 );
            update_post_meta( $post_id, 'start_date', date( 'Y-m-d', strtotime( '-24 months' ) ) );
            update_post_meta( $post_id, 'extra_payment', 200.00 );
            update_post_meta( $post_id, 'property_tax', 3600.00 );
            update_post_meta( $post_id, 'homeowners_insurance', 1200.00 );
            update_post_meta( $post_id, 'pmi', 125.00 );
        }
    }

    /**
     * Create dummy bills
     */
    private static function create_bills( $user_id ) {
        $bills = array(
            array(
                'name' => 'Electric Bill',
                'category' => 'utilities',
                'amount' => 125.00,
                'frequency' => 'monthly',
                'due_date' => 10,
                'auto_pay' => '1',
                'is_essential' => '1',
            ),
            array(
                'name' => 'Internet Service',
                'category' => 'utilities',
                'amount' => 79.99,
                'frequency' => 'monthly',
                'due_date' => 1,
                'auto_pay' => '1',
                'is_essential' => '1',
            ),
            array(
                'name' => 'Car Insurance',
                'category' => 'insurance',
                'amount' => 145.00,
                'frequency' => 'monthly',
                'due_date' => 15,
                'auto_pay' => '1',
                'is_essential' => '1',
            ),
            array(
                'name' => 'Gym Membership',
                'category' => 'entertainment',
                'amount' => 35.00,
                'frequency' => 'monthly',
                'due_date' => 5,
                'auto_pay' => '1',
                'is_essential' => '0',
            ),
            array(
                'name' => 'Netflix Subscription',
                'category' => 'subscriptions',
                'amount' => 15.99,
                'frequency' => 'monthly',
                'due_date' => 8,
                'auto_pay' => '1',
                'is_essential' => '0',
            ),
            array(
                'name' => 'Cell Phone',
                'category' => 'utilities',
                'amount' => 85.00,
                'frequency' => 'monthly',
                'due_date' => 12,
                'auto_pay' => '1',
                'is_essential' => '1',
            ),
        );

        foreach ( $bills as $bill ) {
            $post_id = wp_insert_post( array(
                'post_title' => $bill['name'],
                'post_type' => 'dd_bill',
                'post_status' => 'publish',
                'post_author' => $user_id,
            ) );

            if ( $post_id ) {
                unset( $bill['name'] );
                foreach ( $bill as $key => $value ) {
                    update_post_meta( $post_id, $key, $value );
                }
            }
        }
    }

    /**
     * Create dummy goals
     */
    private static function create_goals( $user_id ) {
        $goals = array(
            array(
                'name' => 'Emergency Fund',
                'goal_type' => 'emergency_fund',
                'target_amount' => 10000.00,
                'current_amount' => 3500.00,
                'monthly_contribution' => 250.00,
                'target_date' => date( 'Y-m-d', strtotime( '+1 year' ) ),
                'priority' => 'high',
            ),
            array(
                'name' => 'Vacation to Hawaii',
                'goal_type' => 'purchase',
                'target_amount' => 5000.00,
                'current_amount' => 1200.00,
                'monthly_contribution' => 200.00,
                'target_date' => date( 'Y-m-d', strtotime( '+10 months' ) ),
                'priority' => 'medium',
            ),
            array(
                'name' => 'Pay Off Credit Cards',
                'goal_type' => 'debt_payoff',
                'target_amount' => 9625.50,
                'current_amount' => 9625.50,
                'monthly_contribution' => 325.00,
                'target_date' => date( 'Y-m-d', strtotime( '+18 months' ) ),
                'priority' => 'high',
            ),
        );

        foreach ( $goals as $goal ) {
            $post_id = wp_insert_post( array(
                'post_title' => $goal['name'],
                'post_type' => 'dd_goal',
                'post_status' => 'publish',
                'post_author' => $user_id,
            ) );

            if ( $post_id ) {
                unset( $goal['name'] );
                foreach ( $goal as $key => $value ) {
                    update_post_meta( $post_id, $key, $value );
                }
            }
        }
    }

    /**
     * Create dummy snapshots
     */
    private static function create_snapshots( $user_id ) {
        $snapshots = array(
            array(
                'date' => date( 'Y-m-d', strtotime( '-3 months' ) ),
                'total_debt' => 305000.00,
                'credit_card_debt' => 11200.00,
                'loan_debt' => 55000.00,
                'mortgage_debt' => 238800.00,
                'monthly_payments' => 3050.00,
                'monthly_bills' => 485.98,
                'dti_ratio' => 54.39,
                'credit_utilization' => 48.70,
            ),
            array(
                'date' => date( 'Y-m-d', strtotime( '-2 months' ) ),
                'total_debt' => 302500.00,
                'credit_card_debt' => 10500.00,
                'loan_debt' => 53000.00,
                'mortgage_debt' => 239000.00,
                'monthly_payments' => 3000.00,
                'monthly_bills' => 485.98,
                'dti_ratio' => 53.62,
                'credit_utilization' => 45.65,
            ),
            array(
                'date' => date( 'Y-m-d', strtotime( '-1 month' ) ),
                'total_debt' => 299875.50,
                'credit_card_debt' => 9625.50,
                'loan_debt' => 51250.00,
                'mortgage_debt' => 239000.00,
                'monthly_payments' => 2950.00,
                'monthly_bills' => 485.98,
                'dti_ratio' => 52.85,
                'credit_utilization' => 41.85,
            ),
        );

        foreach ( $snapshots as $snapshot ) {
            $post_id = wp_insert_post( array(
                'post_title' => 'Snapshot ' . $snapshot['date'],
                'post_type' => 'dd_snapshot',
                'post_status' => 'publish',
                'post_author' => $user_id,
            ) );

            if ( $post_id ) {
                update_post_meta( $post_id, 'snapshot_date', $snapshot['date'] );
                update_post_meta( $post_id, 'total_debt', $snapshot['total_debt'] );
                update_post_meta( $post_id, 'total_credit_card_debt', $snapshot['credit_card_debt'] );
                update_post_meta( $post_id, 'total_loan_debt', $snapshot['loan_debt'] );
                update_post_meta( $post_id, 'total_mortgage_debt', $snapshot['mortgage_debt'] );
                update_post_meta( $post_id, 'total_monthly_payments', $snapshot['monthly_payments'] );
                update_post_meta( $post_id, 'total_monthly_bills', $snapshot['monthly_bills'] );
                update_post_meta( $post_id, 'debt_to_income_ratio', $snapshot['dti_ratio'] );
                update_post_meta( $post_id, 'credit_utilization', $snapshot['credit_utilization'] );
            }
        }
    }
}
