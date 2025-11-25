<?php
/**
 * Page Templates Handler
 *
 * Registers custom page templates
 *
 * @since      1.0.0
 * @package    Dedebtify
 * @subpackage Dedebtify/includes
 */

class Dedebtify_Page_Templates {

    /**
     * Templates array
     *
     * @var array
     */
    protected $templates;

    /**
     * Initialize the class
     */
    public function __construct() {
        $this->templates = array(
            'page-templates/template-dashboard.php'     => 'DeDebtify Dashboard',
            'page-templates/template-credit-cards.php'  => 'DeDebtify Credit Cards',
            'page-templates/template-loans.php'         => 'DeDebtify Loans',
            'page-templates/template-mortgages.php'     => 'DeDebtify Mortgages',
            'page-templates/template-bills.php'         => 'DeDebtify Bills',
            'page-templates/template-goals.php'         => 'DeDebtify Goals',
            'page-templates/template-action-plan.php'   => 'DeDebtify Action Plan',
            'page-templates/template-snapshots.php'     => 'DeDebtify Snapshots',
        );

        // Add filters
        add_filter( 'theme_page_templates', array( $this, 'add_page_templates' ) );
        add_filter( 'template_include', array( $this, 'load_page_template' ) );
    }

    /**
     * Add custom templates to the page template dropdown
     *
     * @param array $templates
     * @return array
     */
    public function add_page_templates( $templates ) {
        $templates = array_merge( $templates, $this->templates );
        return $templates;
    }

    /**
     * Load the custom page template
     *
     * @param string $template
     * @return string
     */
    public function load_page_template( $template ) {
        global $post;

        if ( ! $post ) {
            return $template;
        }

        $page_template = get_post_meta( $post->ID, '_wp_page_template', true );

        if ( ! isset( $this->templates[ $page_template ] ) ) {
            return $template;
        }

        $file = DEDEBTIFY_PLUGIN_DIR . $page_template;

        if ( file_exists( $file ) ) {
            return $file;
        }

        return $template;
    }
}
