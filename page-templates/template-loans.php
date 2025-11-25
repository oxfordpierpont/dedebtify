<?php
/**
 * Template Name: DeDebtify Loans
 * Description: Loans management template
 */

get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <?php echo do_shortcode('[dedebtify_loans]'); ?>
    </main>
</div>

<?php
get_footer();
