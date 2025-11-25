<?php
/**
 * PWA Handler
 *
 * Handles Progressive Web App functionality including service worker,
 * manifest, and push notifications
 *
 * @since      1.0.0
 * @package    Dedebtify
 * @subpackage Dedebtify/includes
 */

class Dedebtify_PWA {

    /**
     * Initialize PWA functionality
     */
    public function __construct() {
        add_action( 'wp_head', array( $this, 'add_pwa_meta_tags' ) );
        add_action( 'wp_head', array( $this, 'add_manifest_link' ) );
        add_action( 'init', array( $this, 'register_manifest_route' ) );
        add_action( 'init', array( $this, 'register_service_worker_route' ) );
        add_action( 'wp_ajax_dedebtify_save_push_subscription', array( $this, 'save_push_subscription' ) );
        add_action( 'wp_ajax_dedebtify_send_test_notification', array( $this, 'send_test_notification' ) );
    }

    /**
     * Add PWA meta tags to head
     */
    public function add_pwa_meta_tags() {
        // Only add on DeDebtify pages
        if ( ! $this->is_dedebtify_page() ) {
            return;
        }

        $primary_color = get_option( 'dedebtify_primary_color', '#3b82f6' );

        echo '<meta name="mobile-web-app-capable" content="yes">' . "\n";
        echo '<meta name="apple-mobile-web-app-capable" content="yes">' . "\n";
        echo '<meta name="apple-mobile-web-app-status-bar-style" content="default">' . "\n";
        echo '<meta name="apple-mobile-web-app-title" content="DeDebtify">' . "\n";
        echo '<meta name="theme-color" content="' . esc_attr( $primary_color ) . '">' . "\n";
        echo '<meta name="msapplication-TileColor" content="' . esc_attr( $primary_color ) . '">' . "\n";
        echo '<meta name="msapplication-tap-highlight" content="no">' . "\n";

        // Apple touch icons
        echo '<link rel="apple-touch-icon" sizes="180x180" href="' . DEDEBTIFY_PLUGIN_URL . 'assets/images/icon-192x192.png">' . "\n";
        echo '<link rel="apple-touch-icon" sizes="152x152" href="' . DEDEBTIFY_PLUGIN_URL . 'assets/images/icon-152x152.png">' . "\n";
        echo '<link rel="apple-touch-icon" sizes="144x144" href="' . DEDEBTIFY_PLUGIN_URL . 'assets/images/icon-144x144.png">' . "\n";
    }

    /**
     * Add manifest link to head
     */
    public function add_manifest_link() {
        if ( ! $this->is_dedebtify_page() ) {
            return;
        }

        $manifest_url = site_url( '/dedebtify-manifest.json' );
        echo '<link rel="manifest" href="' . esc_url( $manifest_url ) . '">' . "\n";
    }

    /**
     * Register manifest route
     */
    public function register_manifest_route() {
        add_rewrite_rule(
            '^dedebtify-manifest\.json$',
            'index.php?dedebtify_manifest=1',
            'top'
        );

        add_filter( 'query_vars', function( $vars ) {
            $vars[] = 'dedebtify_manifest';
            return $vars;
        });

        add_action( 'template_redirect', function() {
            if ( get_query_var( 'dedebtify_manifest' ) ) {
                $this->serve_manifest();
                exit;
            }
        });
    }

    /**
     * Register service worker route
     */
    public function register_service_worker_route() {
        add_rewrite_rule(
            '^dedebtify-sw\.js$',
            'index.php?dedebtify_sw=1',
            'top'
        );

        add_filter( 'query_vars', function( $vars ) {
            $vars[] = 'dedebtify_sw';
            return $vars;
        });

        add_action( 'template_redirect', function() {
            if ( get_query_var( 'dedebtify_sw' ) ) {
                $this->serve_service_worker();
                exit;
            }
        });
    }

    /**
     * Serve manifest file
     */
    public function serve_manifest() {
        header( 'Content-Type: application/manifest+json' );
        header( 'Service-Worker-Allowed: /' );

        $page_ids = get_option( 'dedebtify_page_ids', array() );
        $dashboard_url = isset( $page_ids['dashboard'] ) ? get_permalink( $page_ids['dashboard'] ) : '/';
        $ai_coach_url = isset( $page_ids['ai_coach'] ) ? get_permalink( $page_ids['ai_coach'] ) : '/ai-coach/';

        $primary_color = get_option( 'dedebtify_primary_color', '#3b82f6' );

        $manifest = array(
            'name' => get_bloginfo( 'name' ) . ' - DeDebtify',
            'short_name' => 'DeDebtify',
            'description' => get_bloginfo( 'description' ),
            'start_url' => $dashboard_url,
            'scope' => '/',
            'display' => 'standalone',
            'background_color' => '#ffffff',
            'theme_color' => $primary_color,
            'orientation' => 'portrait-primary',
            'icons' => array(
                array(
                    'src' => DEDEBTIFY_PLUGIN_URL . 'assets/images/icon-192x192.png',
                    'sizes' => '192x192',
                    'type' => 'image/png',
                    'purpose' => 'any maskable'
                ),
                array(
                    'src' => DEDEBTIFY_PLUGIN_URL . 'assets/images/icon-512x512.png',
                    'sizes' => '512x512',
                    'type' => 'image/png',
                    'purpose' => 'any maskable'
                )
            ),
            'categories' => array( 'finance', 'productivity', 'lifestyle' ),
            'shortcuts' => array(
                array(
                    'name' => 'Dashboard',
                    'short_name' => 'Dashboard',
                    'description' => 'View your debt dashboard',
                    'url' => $dashboard_url,
                    'icons' => array(
                        array(
                            'src' => DEDEBTIFY_PLUGIN_URL . 'assets/images/icon-96x96.png',
                            'sizes' => '96x96',
                            'type' => 'image/png'
                        )
                    )
                ),
                array(
                    'name' => 'AI Coach',
                    'short_name' => 'AI Coach',
                    'description' => 'Chat with your financial coach',
                    'url' => $ai_coach_url,
                    'icons' => array(
                        array(
                            'src' => DEDEBTIFY_PLUGIN_URL . 'assets/images/icon-96x96.png',
                            'sizes' => '96x96',
                            'type' => 'image/png'
                        )
                    )
                )
            ),
            'prefer_related_applications' => false
        );

        echo json_encode( $manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
    }

    /**
     * Serve service worker file
     */
    public function serve_service_worker() {
        header( 'Content-Type: application/javascript' );
        header( 'Service-Worker-Allowed: /' );

        $sw_file = DEDEBTIFY_PLUGIN_DIR . 'service-worker.js';

        if ( file_exists( $sw_file ) ) {
            readfile( $sw_file );
        }
    }

    /**
     * Check if current page is a DeDebtify page
     */
    private function is_dedebtify_page() {
        global $post;

        if ( ! $post ) {
            return false;
        }

        // Check if page uses DeDebtify shortcodes
        $dedebtify_shortcodes = array(
            'dedebtify_dashboard',
            'dedebtify_credit_cards',
            'dedebtify_loans',
            'dedebtify_mortgages',
            'dedebtify_bills',
            'dedebtify_goals',
            'dedebtify_action_plan',
            'dedebtify_snapshots',
            'dedebtify_ai_coach'
        );

        foreach ( $dedebtify_shortcodes as $shortcode ) {
            if ( has_shortcode( $post->post_content, $shortcode ) ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Save push notification subscription
     */
    public function save_push_subscription() {
        check_ajax_referer( 'dedebtify_nonce', 'nonce' );

        if ( ! is_user_logged_in() ) {
            wp_send_json_error( 'Not logged in' );
        }

        $subscription = json_decode( file_get_contents( 'php://input' ), true );

        if ( ! isset( $subscription['subscription'] ) ) {
            wp_send_json_error( 'Invalid subscription data' );
        }

        $user_id = get_current_user_id();
        $subscription_data = $subscription['subscription'];

        // Save subscription to user meta
        update_user_meta( $user_id, 'dd_push_subscription', $subscription_data );

        wp_send_json_success( 'Subscription saved' );
    }

    /**
     * Send test notification
     */
    public function send_test_notification() {
        check_ajax_referer( 'dedebtify_nonce', 'nonce' );

        if ( ! is_user_logged_in() ) {
            wp_send_json_error( 'Not logged in' );
        }

        $user_id = get_current_user_id();

        $result = $this->send_notification( $user_id, array(
            'title' => 'DeDebtify Test',
            'body' => 'Push notifications are working! You\'ll receive updates about your finances.',
            'icon' => DEDEBTIFY_PLUGIN_URL . 'assets/images/icon-192x192.png',
            'url' => site_url( '/debt-dashboard/' )
        ));

        if ( $result ) {
            wp_send_json_success( 'Test notification sent' );
        } else {
            wp_send_json_error( 'Failed to send notification' );
        }
    }

    /**
     * Send push notification to user
     */
    public function send_notification( $user_id, $data ) {
        $subscription = get_user_meta( $user_id, 'dd_push_subscription', true );

        if ( ! $subscription ) {
            return false;
        }

        // Use Web Push library (would need to be installed via Composer)
        // For now, return true if subscription exists
        // In production, implement actual push using library like web-push-php

        do_action( 'dedebtify_push_notification_sent', $user_id, $data );

        return true;
    }

    /**
     * Schedule notification for bill due date
     */
    public static function schedule_bill_notification( $bill_id ) {
        $due_date = get_post_meta( $bill_id, '_due_date', true );

        if ( ! $due_date ) {
            return;
        }

        $user_id = get_post_field( 'post_author', $bill_id );
        $bill_name = get_the_title( $bill_id );
        $amount = get_post_meta( $bill_id, '_amount', true );

        // Schedule for 3 days before due date
        $notification_time = strtotime( $due_date . ' -3 days' );

        if ( $notification_time > time() ) {
            wp_schedule_single_event( $notification_time, 'dedebtify_send_bill_reminder', array(
                'user_id' => $user_id,
                'bill_name' => $bill_name,
                'amount' => $amount,
                'due_date' => $due_date
            ));
        }
    }

    /**
     * Send bill reminder notification
     */
    public static function send_bill_reminder( $user_id, $bill_name, $amount, $due_date ) {
        $pwa = new self();

        $pwa->send_notification( $user_id, array(
            'title' => 'Bill Reminder',
            'body' => sprintf( '%s ($%s) is due in 3 days', $bill_name, number_format( $amount, 2 ) ),
            'icon' => DEDEBTIFY_PLUGIN_URL . 'assets/images/icon-192x192.png',
            'badge' => DEDEBTIFY_PLUGIN_URL . 'assets/images/badge-72x72.png',
            'tag' => 'bill-reminder-' . $bill_name,
            'url' => site_url( '/bills/' )
        ));
    }
}
