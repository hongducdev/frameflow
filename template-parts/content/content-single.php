<?php

/**
 * Template part for displaying posts in loop
 *
 * @package Case-Themes
 */

if (has_post_thumbnail()) {
    $content_inner_cls = 'single-post-inner has-post-thumbnail';
    $meta_class    = '';
} else {
    $content_inner_cls = 'single-post-inner  no-post-thumbnail';
    $meta_class = '';
}

if (class_exists('\Elementor\Plugin') && \Elementor\Plugin::$instance->documents->get($id)->is_built_with_elementor()) {
    $post_content_classes = 'single-elementor-content';
} else {
    $post_content_classes = '';
}
$sg_featured_img_size = frameflow()->get_theme_opt('sg_featured_img_size', '1300x700');
$feature_image_display = frameflow()->get_theme_opt('feature_image_display', 'hide');
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('pxl-single-post'); ?>>
    <div class="<?php echo esc_attr($content_inner_cls); ?>">
        <?php if (has_post_thumbnail() && ($feature_image_display == 'show')) {
            $img  = pxl_get_image_by_size(array(
                'attach_id'  => get_post_thumbnail_id($post->ID),
                'thumb_size' => $sg_featured_img_size,
            ));
            $thumbnail    = $img['thumbnail']; ?>
            <div class="pxl-item--image">
                <?php echo wp_kses_post($thumbnail); ?>
            </div>
        <?php } ?>
        <div class="pxl-post--top">
            <?php frameflow()->blog->get_post_metas(); ?>
            <?php frameflow()->blog->get_post_title(); ?>
        </div>
        <div class="post-content overflow-hidden">
            <div class="content-inner clearfix <?php echo esc_attr($post_content_classes); ?>">
                <?php
                the_content();
                ?></div>
            <div class="<?php echo trim(implode(' ', ['navigation page-links clearfix empty-none'])); ?>">
                <?php
                wp_link_pages();
                ?></div>
        </div>
        <?php
        $post_tag = frameflow()->get_theme_opt('post_tag', true);
        $post_social_share = frameflow()->get_theme_opt('post_social_share', false);

        $tags_html = '';
        if ($post_tag == '1') {
            ob_start();
            frameflow()->blog->get_post_tags();
            $tags_html = trim(ob_get_clean());
        }

        if (!empty($tags_html) || $post_social_share == '1') :
        ?>
            <div class="post-tags-share d-flex">

                <?php if (!empty($tags_html)) : ?>
                    <div class="post-tags-wrap">
                        <span class="post-label"><?php echo esc_html__('Tag:', 'frameflow'); ?></span>
                        <?php echo $tags_html; ?>
                    </div>
                <?php endif; ?>

                <?php if ($post_social_share == '1') : ?>
                    <div class="post-share-wrap">
                        <?php frameflow()->blog->get_post_share(); ?>
                    </div>
                <?php endif; ?>

            </div>

            <div class="pxl-el-divider"></div>
        <?php endif; ?>
    </div>

    <?php frameflow()->blog->get_post_nav(); ?>
</article>