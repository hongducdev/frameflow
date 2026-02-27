<?php

/**
 * @package Case-Themes
 */

get_header();
$frameflow_sidebar = frameflow()->get_sidebar_args(['type' => 'blog', 'content_col' => '9', 'sidebar_layout' => 'style1']);
?>
<div class="container">
    <div class="row <?php echo esc_attr($frameflow_sidebar['wrap_class']) ?>">
        <div id="pxl-content-area" class="<?php echo esc_attr($frameflow_sidebar['content_class']) ?>">
            <main id="pxl-content-main">
                <?php if (have_posts()) {
                    while (have_posts()) {
                        the_post();
                        get_template_part('template-parts/content/archive/standard');
                    }
                    frameflow()->page->get_pagination();
                } else {
                    get_template_part('template-parts/content/content', 'none');
                } ?>
            </main>
        </div>
        <?php if ($frameflow_sidebar['sidebar_class']) : ?>
            <div id="pxl-sidebar-area" class="<?php echo esc_attr($frameflow_sidebar['sidebar_class']) ?>">
                <div class="pxl-sidebar-sticky">
                    <?php get_sidebar(); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php get_footer();
