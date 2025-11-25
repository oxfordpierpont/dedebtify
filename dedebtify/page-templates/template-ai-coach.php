<?php
/**
 * Template Name: DeDebtify AI Coach
 *
 * Template for displaying the AI Financial Coach interface.
 *
 * @since      1.0.0
 * @package    Dedebtify
 * @subpackage Dedebtify/page-templates
 */

get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <?php echo do_shortcode('[dedebtify_ai_coach]'); ?>
    </main>
</div>

<?php
get_footer();
