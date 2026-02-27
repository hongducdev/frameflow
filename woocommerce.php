<?php
get_header();

if (is_singular('product')) {
    $frameflow_sidebar = frameflow()->get_sidebar_args(['type' => 'product', 'content_col' => '12']);
} else {
    $frameflow_sidebar = frameflow()->get_sidebar_args(['type' => 'shop', 'content_col' => '9']);
} ?>
<div class="container">
    <div class="row <?php echo esc_attr($frameflow_sidebar['wrap_class']) ?>">
        <div id="pxl-content-area" class="<?php echo esc_attr($frameflow_sidebar['content_class']) ?>">
            <main id="pxl-content-main">
                <?php woocommerce_content(); ?>
            </main>
        </div>

        <?php if ($frameflow_sidebar['sidebar_class'] && !is_singular('product')) : ?>
            <aside id="pxl-sidebar-area" class="<?php echo esc_attr($frameflow_sidebar['sidebar_class']) ?>">
                <div class="pxl-sidebar-sticky">
                    <?php get_sidebar(); ?>
                </div>
            </aside>
        <?php endif; ?>
    </div>
</div>
<?php get_footer();
