<?php

/**
 * @package Frameflow
 */

$archive_readmore_text = frameflow()->get_theme_opt('archive_readmore_text', esc_html__('Read more', 'frameflow'));
$post_social_share = frameflow()->get_theme_opt('post_social_share', false);
$archive_excerpt = frameflow()->get_theme_opt('archive_excerpt', true);
$archive_social = frameflow()->get_theme_opt('archive_social', true);
$featured_video = get_post_meta(get_the_ID(), 'featured-video-url', true);
$audio_url = get_post_meta(get_the_ID(), 'featured-audio-url', true);
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('pxl-archive-post'); ?>>
    <div class="content-inner-post">
        <?php if (has_post_thumbnail()) {
            $archive_date = frameflow()->get_theme_opt('archive_date', true);
        ?>
            <div class="post-featured">
                <?php
                if (has_post_format('quote')) {
                    $quote_text = get_post_meta(get_the_ID(), 'featured-quote-text', true);
                    $quote_cite = get_post_meta(get_the_ID(), 'featured-quote-cite', true);
                ?>
                    <div class="format-wrap">
                        <div class="quote-inner">
                            <div class="content-top">
                                <div class="link-icon">
                                    <a href="<?php echo esc_url(get_permalink()); ?>" title="<?php the_title_attribute(); ?>">
                                        <span>“</span>
                                    </a>
                                </div>
                                <div class="content-right">
                                    <?php frameflow()->blog->get_archive_meta_2(); ?>
                                    <div class="quote-text">
                                        <a href="<?php echo esc_url(get_permalink()); ?>"><?php echo esc_html($quote_text); ?></a>
                                    </div>
                                </div>
                            </div>

                            <?php
                            if (!empty($quote_cite)) {
                            ?>
                                <p class="quote-cite">
                                    <?php echo esc_html($quote_cite); ?>
                                </p>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                <?php
                } elseif (has_post_format('link')) {
                    $link_url = get_post_meta(get_the_ID(), 'featured-link-url', true);
                    $link_text = get_post_meta(get_the_ID(), 'featured-link-text', true);
                ?>
                    <div class="format-wrap">
                        <div class="link-inner">
                            <div class="content-top">
                                <div class="link-icon">
                                    <a href="<?php echo esc_url($link_url); ?>">
                                        <svg version="1.1" id="Glyph" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                            viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
                                            <path d="M192.5,240.5c20.7-21,56-23,79,0h0.2c6.4,6.4,11,14.2,13.8,22.6c6.7-1.1,12.6-4,17.1-8.5l22.1-21.9
                                    c-5-9.6-11.4-18.4-19-26.2c-42-41.1-106.9-40-147.2,0l-80,80c-40.6,40.9-40.6,106.3,0,147.2c40.9,40.6,106.3,40.6,147.2,0l75.4-75.4
                                    c-22,3.6-43.1,1.6-62.7-5.3l-46.7,46.6c-21.1,21.3-57.9,21.3-79.2,0c-21.8-21.8-21.8-57.3,0-79C113.9,318.9,197.8,235.1,192.5,240.5
                                    L192.5,240.5z" />
                                            <path d="M319.5,271.5c-21,21.3-56.3,22.7-79,0c-0.2,0-0.2,0-0.2,0c-6.4-6.4-11-14.2-13.8-22.6c-6.7,1.1-12.6,4-17.1,8.5l-22.1,21.9
                                    c5,9.6,11.4,18.4,19,26.2c42,41.1,106.9,40,147.2,0l80-80c40.6-40.9,40.6-106.3,0-147.2c-40.9-40.6-106.3-40.6-147.2,0L211,153.8
                                    c22-3.6,43.1-1.6,62.7,5.3l46.7-46.6c21.1-21.3,57.9-21.3,79.2,0c21.8,21.8,21.8,57.3,0,79C398.1,193.1,314.2,276.9,319.5,271.5
                                    L319.5,271.5z" />
                                        </svg>
                                    </a>
                                </div>
                                <div class="content-right">
                                    <?php frameflow()->blog->get_archive_meta_2(); ?>
                                    <h4 class="post-title">
                                        <a href="<?php echo esc_url(get_permalink()); ?>" title="<?php the_title_attribute(); ?>">
                                            <?php if (is_sticky()) { ?>
                                                <i class="bi-check"></i>
                                            <?php } ?>
                                            <?php the_title(); ?>
                                        </a>
                                    </h4>
                                </div>
                            </div>

                            <div class="link-text">
                                <a class="link-text" target="_blank" href="<?php echo esc_url($link_url); ?>"><?php echo esc_html($link_text); ?></a>
                            </div>
                        </div>
                    </div>
                    <?php
                } elseif (has_post_format('video')) {
                    if (has_post_thumbnail()) {
                    ?>
                        <div class="format-wrap">
                            <div class="pxl-item--image">
                                <a href="<?php echo esc_url(get_permalink()); ?>"><?php the_post_thumbnail('full'); ?></a>
                                <?php
                                if (!empty($featured_video)) {
                                ?>
                                    <div class="pxl-video-popup">
                                        <div class="content-inner">
                                            <a class="video-play-button pxl-action-popup" href="<?php echo esc_url($featured_video); ?>">
                                                <i class="bi-play-fill"></i>
                                            </a>
                                        </div>
                                    </div>
                                <?php
                                } ?>
                            </div>
                        </div>
                    <?php
                    }
                } elseif (!empty($audio_url) && has_post_format('audio')) {
                    global $wp_embed;
                    pxl_print_html($wp_embed->run_shortcode('[embed]' . $audio_url . '[/embed]'));
                } else {
                    ?>
                    <div class="pxl-item--image hover-imge-ripple" style="background-image: url(<?php echo esc_url(get_the_post_thumbnail_url(get_the_ID(), 'full')); ?>);" data-image-url="<?php echo esc_url(get_the_post_thumbnail_url(get_the_ID(), 'full')); ?>">
                        <a href="<?php echo esc_url(get_permalink()); ?>"><?php the_post_thumbnail('full'); ?></a>
                    </div>
                <?php
                }
                ?>
            </div>
        <?php } ?>
        <?php
        if (!has_post_format('link') && !has_post_format('quote')) {
        ?>
            <div class="post-content">
                <?php frameflow()->blog->get_post_metas(); ?>
                <h3 class="post-title">
                    <a href="<?php echo esc_url(get_permalink()); ?>" title="<?php the_title_attribute(); ?>">
                        <?php if (is_sticky()) { ?>
                            <i class="bi-check"></i>
                        <?php } ?>
                        <?php the_title(); ?>
                    </a>
                </h3>
                <?php if ($archive_excerpt) { ?>
                    <div class="post-excerpt">
                        <?php
                        frameflow()->blog->get_excerpt(60);
                        wp_link_pages(array(
                            'before'      => '<div class="page-links">',
                            'after'       => '</div>',
                        'link_before' => '<span>',
                        'link_after'  => '</span>',
                    ));
                    ?>
                    </div>
                <?php } ?>
                <?php
                if (!empty($archive_readmore_text)) {
                ?>
                    <a class="btn-more btn btn-2-icons" href="<?php echo esc_url(get_permalink()); ?>">
                        <span class="btn--text">
                            <?php echo esc_html($archive_readmore_text); ?> 
                        </span>
                        <span class="btn-icon-left">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="13" viewBox="0 0 16 13" fill="none"><path d="M9.6 12.7999C9.39526 12.7999 9.19053 12.7219 9.03432 12.5657C8.7219 12.2532 8.7219 11.7467 9.03432 11.4343L13.2686 7.19999H0.800009C0.358159 7.19999 0 6.8418 0 6.39998C0 5.95813 0.358159 5.59997 0.800009 5.59997H13.2686L9.03432 1.36567C8.7219 1.05326 8.7219 0.546725 9.03432 0.234311C9.3467 -0.0781035 9.8533 -0.0781035 10.1657 0.234311L15.7657 5.8343L15.7674 5.83604C15.7677 5.83632 15.768 5.83667 15.7683 5.83695C15.7686 5.83723 15.7688 5.83751 15.7691 5.83778C15.7695 5.8382 15.7699 5.83862 15.7703 5.83904C15.7705 5.83918 15.7706 5.83932 15.7708 5.83949C15.7713 5.84005 15.7718 5.84057 15.7724 5.84109L15.7724 5.84116C15.8444 5.91483 15.8992 5.9989 15.937 6.08847C15.9371 6.08872 15.9372 6.089 15.9373 6.08924C15.9374 6.08952 15.9376 6.08983 15.9377 6.09011C15.9778 6.18543 15.9999 6.29015 15.9999 6.40002C15.9999 6.50989 15.9778 6.61461 15.9377 6.70993C15.9376 6.71017 15.9374 6.71052 15.9373 6.7108C15.9372 6.71104 15.9371 6.71128 15.937 6.71153C15.8992 6.80114 15.8444 6.88521 15.7724 6.95888L15.7724 6.95891C15.7718 6.95947 15.7713 6.95999 15.7708 6.96051C15.7707 6.96065 15.7705 6.96079 15.7703 6.96096C15.7699 6.96142 15.7695 6.9618 15.7691 6.96225C15.7688 6.9625 15.7686 6.96281 15.7683 6.96305C15.768 6.96333 15.7677 6.96368 15.7674 6.96396C15.7668 6.96455 15.7662 6.96514 15.7657 6.9657L10.1657 12.5657C10.0095 12.7219 9.80474 12.7999 9.6 12.7999Z" fill="#fff"></path></svg>
                        </span>
                    </a>
                <?php } ?>
            </div>
        <?php
        }
        ?>
    </div>
</article>