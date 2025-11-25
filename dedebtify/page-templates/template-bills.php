<?php
/**
 * Template Name: DeDebtify Bills
 * Description: Bills management template
 */

get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <?php echo do_shortcode('[dedebtify_bills]'); ?>
    </main>
</div>

<?php
get_footer();
