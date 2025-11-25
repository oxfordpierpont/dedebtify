<?php
/**
 * Plaid Integration Class
 *
 * Handles all Plaid API interactions for financial data syncing
 *
 * @since      1.0.0
 * @package    Dedebtify
 * @subpackage Dedebtify/includes
 */

class Dedebtify_Plaid {

    /**
     * Plaid API base URLs
     */
    const SANDBOX_URL = 'https://sandbox.plaid.com';
    const DEVELOPMENT_URL = 'https://development.plaid.com';
    const PRODUCTION_URL = 'https://production.plaid.com';

    /**
     * Get Plaid API URL based on environment
     *
     * @return string
     */
    private static function get_api_url() {
        $environment = get_option( 'dedebtify_plaid_environment', 'sandbox' );

        switch ( $environment ) {
            case 'production':
                return self::PRODUCTION_URL;
            case 'development':
                return self::DEVELOPMENT_URL;
            default:
                return self::SANDBOX_URL;
        }
    }

    /**
     * Get Plaid credentials
     *
     * @return array|false
     */
    private static function get_credentials() {
        $client_id = get_option( 'dedebtify_plaid_client_id', '' );
        $secret = get_option( 'dedebtify_plaid_secret', '' );

        if ( empty( $client_id ) || empty( $secret ) ) {
            return false;
        }

        return array(
            'client_id' => $client_id,
            'secret' => $secret
        );
    }

    /**
     * Create Plaid Link token for user
     *
     * @param int $user_id User ID
     * @return array|WP_Error
     */
    public static function create_link_token( $user_id ) {
        $credentials = self::get_credentials();
        if ( ! $credentials ) {
            return new WP_Error( 'plaid_not_configured', __( 'Plaid is not configured', 'dedebtify' ) );
        }

        $user = get_userdata( $user_id );

        $body = array(
            'client_id' => $credentials['client_id'],
            'secret' => $credentials['secret'],
            'user' => array(
                'client_user_id' => (string) $user_id
            ),
            'client_name' => get_bloginfo( 'name' ) . ' - DeDebtify',
            'products' => array( 'liabilities', 'transactions' ),
            'country_codes' => array( 'US' ),
            'language' => 'en'
        );

        $response = wp_remote_post( self::get_api_url() . '/link/token/create', array(
            'headers' => array(
                'Content-Type' => 'application/json'
            ),
            'body' => json_encode( $body ),
            'timeout' => 30
        ) );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $data = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( isset( $data['error_code'] ) ) {
            return new WP_Error( 'plaid_api_error', $data['error_message'] );
        }

        return $data;
    }

    /**
     * Exchange public token for access token
     *
     * @param string $public_token Public token from Plaid Link
     * @param int $user_id User ID
     * @return array|WP_Error
     */
    public static function exchange_public_token( $public_token, $user_id ) {
        $credentials = self::get_credentials();
        if ( ! $credentials ) {
            return new WP_Error( 'plaid_not_configured', __( 'Plaid is not configured', 'dedebtify' ) );
        }

        $body = array(
            'client_id' => $credentials['client_id'],
            'secret' => $credentials['secret'],
            'public_token' => $public_token
        );

        $response = wp_remote_post( self::get_api_url() . '/item/public_token/exchange', array(
            'headers' => array(
                'Content-Type' => 'application/json'
            ),
            'body' => json_encode( $body ),
            'timeout' => 30
        ) );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $data = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( isset( $data['error_code'] ) ) {
            return new WP_Error( 'plaid_api_error', $data['error_message'] );
        }

        // Store access token securely
        if ( isset( $data['access_token'] ) && isset( $data['item_id'] ) ) {
            self::save_linked_account( $user_id, $data['access_token'], $data['item_id'] );
        }

        return $data;
    }

    /**
     * Encrypt sensitive data
     *
     * @param string $data Data to encrypt
     * @return string
     */
    private static function encrypt_token( $data ) {
        if ( ! $data ) {
            return '';
        }

        // Use WordPress secret keys for encryption
        $key = wp_salt( 'auth' );
        $iv = substr( wp_salt( 'secure_auth' ), 0, 16 );

        // Use OpenSSL for encryption if available
        if ( function_exists( 'openssl_encrypt' ) ) {
            return base64_encode( openssl_encrypt( $data, 'AES-256-CBC', $key, 0, $iv ) );
        }

        // Fallback to base64 (not ideal but better than plaintext)
        return base64_encode( $data );
    }

    /**
     * Decrypt sensitive data
     *
     * @param string $data Encrypted data
     * @return string
     */
    private static function decrypt_token( $data ) {
        if ( ! $data ) {
            return '';
        }

        // Use WordPress secret keys for decryption
        $key = wp_salt( 'auth' );
        $iv = substr( wp_salt( 'secure_auth' ), 0, 16 );

        // Use OpenSSL for decryption if available
        if ( function_exists( 'openssl_decrypt' ) ) {
            return openssl_decrypt( base64_decode( $data ), 'AES-256-CBC', $key, 0, $iv );
        }

        // Fallback to base64 decode
        return base64_decode( $data );
    }

    /**
     * Save linked Plaid account for user
     *
     * @param int $user_id User ID
     * @param string $access_token Plaid access token
     * @param string $item_id Plaid item ID
     */
    private static function save_linked_account( $user_id, $access_token, $item_id ) {
        $linked_accounts = get_user_meta( $user_id, 'dd_plaid_accounts', true );
        if ( ! is_array( $linked_accounts ) ) {
            $linked_accounts = array();
        }

        // Encrypt access token before storing
        $linked_accounts[] = array(
            'access_token' => self::encrypt_token( $access_token ),
            'item_id' => sanitize_text_field( $item_id ),
            'connected_at' => current_time( 'mysql' ),
            'last_sync' => null
        );

        update_user_meta( $user_id, 'dd_plaid_accounts', $linked_accounts );
    }

    /**
     * Get linked accounts for user
     *
     * @param int $user_id User ID
     * @return array
     */
    public static function get_linked_accounts( $user_id ) {
        $linked_accounts = get_user_meta( $user_id, 'dd_plaid_accounts', true );
        return is_array( $linked_accounts ) ? $linked_accounts : array();
    }

    /**
     * Disconnect a Plaid account
     *
     * @param int $user_id User ID
     * @param string $item_id Item ID to disconnect
     * @return bool
     */
    public static function disconnect_account( $user_id, $item_id ) {
        $linked_accounts = self::get_linked_accounts( $user_id );

        $updated_accounts = array_filter( $linked_accounts, function( $account ) use ( $item_id ) {
            return $account['item_id'] !== $item_id;
        } );

        update_user_meta( $user_id, 'dd_plaid_accounts', array_values( $updated_accounts ) );
        return true;
    }

    /**
     * Get account balances
     *
     * @param string $access_token Plaid access token
     * @return array|WP_Error
     */
    public static function get_balances( $access_token ) {
        $credentials = self::get_credentials();
        if ( ! $credentials ) {
            return new WP_Error( 'plaid_not_configured', __( 'Plaid is not configured', 'dedebtify' ) );
        }

        $body = array(
            'client_id' => $credentials['client_id'],
            'secret' => $credentials['secret'],
            'access_token' => $access_token
        );

        $response = wp_remote_post( self::get_api_url() . '/accounts/balance/get', array(
            'headers' => array(
                'Content-Type' => 'application/json'
            ),
            'body' => json_encode( $body ),
            'timeout' => 30
        ) );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $data = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( isset( $data['error_code'] ) ) {
            return new WP_Error( 'plaid_api_error', $data['error_message'] );
        }

        return $data;
    }

    /**
     * Get liabilities (loans, credit cards, mortgages)
     *
     * @param string $access_token Plaid access token
     * @return array|WP_Error
     */
    public static function get_liabilities( $access_token ) {
        $credentials = self::get_credentials();
        if ( ! $credentials ) {
            return new WP_Error( 'plaid_not_configured', __( 'Plaid is not configured', 'dedebtify' ) );
        }

        $body = array(
            'client_id' => $credentials['client_id'],
            'secret' => $credentials['secret'],
            'access_token' => $access_token
        );

        $response = wp_remote_post( self::get_api_url() . '/liabilities/get', array(
            'headers' => array(
                'Content-Type' => 'application/json'
            ),
            'body' => json_encode( $body ),
            'timeout' => 30
        ) );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $data = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( isset( $data['error_code'] ) ) {
            return new WP_Error( 'plaid_api_error', $data['error_message'] );
        }

        return $data;
    }

    /**
     * Sync all accounts for a user
     *
     * @param int $user_id User ID
     * @return array Results of sync operation
     */
    public static function sync_user_accounts( $user_id ) {
        $linked_accounts = self::get_linked_accounts( $user_id );
        $results = array(
            'success' => 0,
            'errors' => 0,
            'synced_items' => array()
        );

        foreach ( $linked_accounts as $index => $account ) {
            // Decrypt access token before use
            $access_token = self::decrypt_token( $account['access_token'] );

            if ( empty( $access_token ) ) {
                $results['errors']++;
                continue;
            }

            // Get liabilities
            $liabilities_data = self::get_liabilities( $access_token );

            if ( is_wp_error( $liabilities_data ) ) {
                $results['errors']++;
                continue;
            }

            // Sync liabilities to DeDebtify
            $synced = self::sync_liabilities_to_dedebtify( $user_id, $liabilities_data );

            if ( $synced ) {
                $results['success']++;
                $results['synced_items'][] = sanitize_text_field( $account['item_id'] );

                // Update last sync time
                $linked_accounts[$index]['last_sync'] = current_time( 'mysql' );
            } else {
                $results['errors']++;
            }
        }

        // Update linked accounts with new sync times
        update_user_meta( $user_id, 'dd_plaid_accounts', $linked_accounts );

        return $results;
    }

    /**
     * Sync Plaid liabilities to DeDebtify custom post types
     *
     * @param int $user_id User ID
     * @param array $liabilities_data Liabilities data from Plaid
     * @return bool
     */
    private static function sync_liabilities_to_dedebtify( $user_id, $liabilities_data ) {
        if ( ! isset( $liabilities_data['liabilities'] ) ) {
            return false;
        }

        $liabilities = $liabilities_data['liabilities'];

        // Sync credit cards
        if ( isset( $liabilities['credit'] ) && is_array( $liabilities['credit'] ) ) {
            foreach ( $liabilities['credit'] as $card ) {
                self::sync_credit_card( $user_id, $card );
            }
        }

        // Sync student loans
        if ( isset( $liabilities['student'] ) && is_array( $liabilities['student'] ) ) {
            foreach ( $liabilities['student'] as $loan ) {
                self::sync_loan( $user_id, $loan, 'student' );
            }
        }

        // Sync mortgages
        if ( isset( $liabilities['mortgage'] ) && is_array( $liabilities['mortgage'] ) ) {
            foreach ( $liabilities['mortgage'] as $mortgage ) {
                self::sync_mortgage( $user_id, $mortgage );
            }
        }

        return true;
    }

    /**
     * Sync a credit card from Plaid
     *
     * @param int $user_id User ID
     * @param array $card_data Credit card data from Plaid
     */
    private static function sync_credit_card( $user_id, $card_data ) {
        $plaid_account_id = $card_data['account_id'];

        // Check if this card already exists (by Plaid account ID)
        $existing = get_posts( array(
            'post_type' => 'dd_credit_card',
            'author' => $user_id,
            'meta_key' => 'plaid_account_id',
            'meta_value' => $plaid_account_id,
            'posts_per_page' => 1
        ) );

        $balance = isset( $card_data['balances']['current'] ) ? abs( $card_data['balances']['current'] ) : 0;
        $credit_limit = isset( $card_data['balances']['limit'] ) ? $card_data['balances']['limit'] : 0;
        $apr = isset( $card_data['aprs'] ) && is_array( $card_data['aprs'] ) && count( $card_data['aprs'] ) > 0
            ? $card_data['aprs'][0]['apr_percentage']
            : 0;

        $post_data = array(
            'post_type' => 'dd_credit_card',
            'post_title' => $card_data['name'] ?? 'Credit Card',
            'post_status' => 'publish',
            'post_author' => $user_id
        );

        $meta_data = array(
            'balance' => $balance,
            'credit_limit' => $credit_limit,
            'interest_rate' => $apr,
            'minimum_payment' => max( 25, $balance * 0.02 ), // Estimate 2% or $25
            'extra_payment' => 0,
            'due_date' => date( 'Y-m-d', strtotime( '+30 days' ) ),
            'status' => 'active',
            'plaid_account_id' => $plaid_account_id,
            'plaid_synced' => 1
        );

        if ( ! empty( $existing ) ) {
            // Update existing card
            $post_id = $existing[0]->ID;
            wp_update_post( array_merge( $post_data, array( 'ID' => $post_id ) ) );
        } else {
            // Create new card
            $post_id = wp_insert_post( $post_data );
        }

        if ( $post_id ) {
            foreach ( $meta_data as $key => $value ) {
                update_post_meta( $post_id, $key, $value );
            }
        }
    }

    /**
     * Sync a loan from Plaid
     *
     * @param int $user_id User ID
     * @param array $loan_data Loan data from Plaid
     * @param string $loan_type Type of loan
     */
    private static function sync_loan( $user_id, $loan_data, $loan_type = 'student' ) {
        $plaid_account_id = $loan_data['account_id'];

        // Check if this loan already exists
        $existing = get_posts( array(
            'post_type' => 'dd_loan',
            'author' => $user_id,
            'meta_key' => 'plaid_account_id',
            'meta_value' => $plaid_account_id,
            'posts_per_page' => 1
        ) );

        $balance = isset( $loan_data['balances']['current'] ) ? abs( $loan_data['balances']['current'] ) : 0;
        $original_amount = isset( $loan_data['origination_principal_amount'] ) ? $loan_data['origination_principal_amount'] : $balance;
        $interest_rate = isset( $loan_data['interest_rate_percentage'] ) ? $loan_data['interest_rate_percentage'] : 0;

        $post_data = array(
            'post_type' => 'dd_loan',
            'post_title' => $loan_data['name'] ?? ucfirst( $loan_type ) . ' Loan',
            'post_status' => 'publish',
            'post_author' => $user_id
        );

        $meta_data = array(
            'principal' => $original_amount,
            'current_balance' => $balance,
            'interest_rate' => $interest_rate,
            'term_months' => 120, // Default, Plaid doesn't always provide this
            'monthly_payment' => isset( $loan_data['minimum_payment_amount'] ) ? $loan_data['minimum_payment_amount'] : 0,
            'extra_payment' => 0,
            'start_date' => isset( $loan_data['origination_date'] ) ? $loan_data['origination_date'] : date( 'Y-m-d' ),
            'type' => $loan_type,
            'plaid_account_id' => $plaid_account_id,
            'plaid_synced' => 1
        );

        if ( ! empty( $existing ) ) {
            // Update existing loan
            $post_id = $existing[0]->ID;
            wp_update_post( array_merge( $post_data, array( 'ID' => $post_id ) ) );
        } else {
            // Create new loan
            $post_id = wp_insert_post( $post_data );
        }

        if ( $post_id ) {
            foreach ( $meta_data as $key => $value ) {
                update_post_meta( $post_id, $key, $value );
            }
        }
    }

    /**
     * Sync a mortgage from Plaid
     *
     * @param int $user_id User ID
     * @param array $mortgage_data Mortgage data from Plaid
     */
    private static function sync_mortgage( $user_id, $mortgage_data ) {
        $plaid_account_id = $mortgage_data['account_id'];

        // Check if this mortgage already exists
        $existing = get_posts( array(
            'post_type' => 'dd_mortgage',
            'author' => $user_id,
            'meta_key' => 'plaid_account_id',
            'meta_value' => $plaid_account_id,
            'posts_per_page' => 1
        ) );

        $balance = isset( $mortgage_data['balances']['current'] ) ? abs( $mortgage_data['balances']['current'] ) : 0;
        $original_amount = isset( $mortgage_data['origination_principal_amount'] ) ? $mortgage_data['origination_principal_amount'] : $balance;
        $interest_rate = isset( $mortgage_data['interest_rate']['percentage'] ) ? $mortgage_data['interest_rate']['percentage'] : 0;

        $post_data = array(
            'post_type' => 'dd_mortgage',
            'post_title' => $mortgage_data['name'] ?? 'Mortgage',
            'post_status' => 'publish',
            'post_author' => $user_id
        );

        $meta_data = array(
            'principal' => $original_amount,
            'current_balance' => $balance,
            'interest_rate' => $interest_rate,
            'term_years' => isset( $mortgage_data['loan_term'] ) ? (int) $mortgage_data['loan_term'] / 12 : 30,
            'monthly_payment' => isset( $mortgage_data['last_payment_amount'] ) ? $mortgage_data['last_payment_amount'] : 0,
            'start_date' => isset( $mortgage_data['origination_date'] ) ? $mortgage_data['origination_date'] : date( 'Y-m-d' ),
            'property_value' => isset( $mortgage_data['property_value'] ) ? $mortgage_data['property_value'] : 0,
            'plaid_account_id' => $plaid_account_id,
            'plaid_synced' => 1
        );

        if ( ! empty( $existing ) ) {
            // Update existing mortgage
            $post_id = $existing[0]->ID;
            wp_update_post( array_merge( $post_data, array( 'ID' => $post_id ) ) );
        } else {
            // Create new mortgage
            $post_id = wp_insert_post( $post_data );
        }

        if ( $post_id ) {
            foreach ( $meta_data as $key => $value ) {
                update_post_meta( $post_id, $key, $value );
            }
        }
    }
}
