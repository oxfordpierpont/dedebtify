<?php
/**
 * Template Name: DeDebtify Dashboard
 * Description: Main dashboard template for DeDebtify
 */

get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <?php echo do_shortcode('[dedebtify_dashboard]'); ?>
    </main>
</div>

<?php
get_footer();
