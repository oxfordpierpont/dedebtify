<?php
/**
 * Template Name: DeDebtify Snapshots
 * Description: Financial snapshots template
 */

get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <?php echo do_shortcode('[dedebtify_snapshots]'); ?>
    </main>
</div>

<?php
get_footer();
