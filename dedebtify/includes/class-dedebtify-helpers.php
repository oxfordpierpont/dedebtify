<?php
/**
 * Helper Functions
 *
 * Utility functions for the plugin
 *
 * @since      1.0.0
 * @package    Dedebtify
 * @subpackage Dedebtify/includes
 */

class Dedebtify_Helpers {

    /**
     * Get page URL by key
     *
     * @param string $key Page key (dashboard, credit_cards, loans, etc.)
     * @return string Page URL or empty string
     */
    public static function get_page_url( $key ) {
        $page_ids = get_option( 'dedebtify_page_ids', array() );

        if ( isset( $page_ids[ $key ] ) && $page_ids[ $key ] > 0 ) {
            return get_permalink( $page_ids[ $key ] );
        }

        return '';
    }

    /**
     * Get all DeDebtify page URLs
     *
     * @return array Associative array of page keys and URLs
     */
    public static function get_all_page_urls() {
        $page_ids = get_option( 'dedebtify_page_ids', array() );
        $urls = array();

        foreach ( $page_ids as $key => $page_id ) {
            if ( $page_id > 0 ) {
                $urls[ $key ] = get_permalink( $page_id );
            }
        }

        return $urls;
    }

    /**
     * Render navigation menu
     *
     * @param string $current_page Current page key
     */
    public static function render_navigation( $current_page = '' ) {
        $page_ids = get_option( 'dedebtify_page_ids', array() );

        $nav_items = array(
            'dashboard' => array(
                'title' => __( 'Dashboard', 'dedebtify' ),
                'icon' => 'dashboard'
            ),
            'ai_coach' => array(
                'title' => __( 'AI Coach', 'dedebtify' ),
                'icon' => 'welcome-learn-more'
            ),
            'credit_cards' => array(
                'title' => __( 'Credit Cards', 'dedebtify' ),
                'icon' => 'money-alt'
                'icon' => 'credit-card'
            ),
            'loans' => array(
                'title' => __( 'Loans', 'dedebtify' ),
                'icon' => 'money-alt'
            ),
            'mortgages' => array(
                'title' => __( 'Mortgage', 'dedebtify' ),
                'icon' => 'admin-home'
            ),
            'bills' => array(
                'title' => __( 'Bills', 'dedebtify' ),
                'icon' => 'list-view'
            ),
            'goals' => array(
                'title' => __( 'Goals', 'dedebtify' ),
                'icon' => 'star-filled'
            ),
            'action_plan' => array(
                'title' => __( 'Action Plan', 'dedebtify' ),
                'icon' => 'calendar-alt'
            ),
            'snapshots' => array(
                'title' => __( 'Progress', 'dedebtify' ),
                'icon' => 'chart-line'
            ),
            'account_sync' => array(
                'title' => __( 'Account Sync', 'dedebtify' ),
                'icon' => 'update'
            ),
        );

        ?>
        <nav class="dd-nav">
            <div class="dd-nav-container">
                <?php foreach ( $nav_items as $key => $item ) : ?>
                    <?php if ( isset( $page_ids[ $key ] ) && $page_ids[ $key ] > 0 ) : ?>
                        <?php
                        $url = get_permalink( $page_ids[ $key ] );
                        $is_current = ( $current_page === $key );
                        ?>
                        <a href="<?php echo esc_url( $url ); ?>"
                           class="dd-nav-item <?php echo $is_current ? 'active' : ''; ?>">
                            <span class="dashicons dashicons-<?php echo esc_attr( $item['icon'] ); ?>"></span>
                            <span class="dd-nav-label"><?php echo esc_html( $item['title'] ); ?></span>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </nav>
        <?php
    }

    /**
     * Check if user has dummy data
     *
     * @param int $user_id User ID (default: current user)
     * @return bool
     */
    public static function has_dummy_data( $user_id = 0 ) {
        if ( ! $user_id ) {
            $user_id = get_current_user_id();
        }

        return (bool) get_user_meta( $user_id, 'dd_has_dummy_data', true );
    }
}
