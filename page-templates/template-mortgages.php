<?php
/**
 * Template Name: DeDebtify Mortgages
 * Description: Mortgage management template
 */

get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <?php echo do_shortcode('[dedebtify_mortgages]'); ?>
    </main>
</div>

<?php
get_footer();
