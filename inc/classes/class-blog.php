<?php
if (!class_exists('Frameflow_Blog')) {
    class Frameflow_Blog
    {

        public function get_archive_meta($post_id = 0)
        {
            $post_date_on = frameflow()->get_theme_opt('post_date_on', true);
            $post_comments_on = frameflow()->get_theme_opt('post_comments_on', true);
            if ($post_date_on || $post_comments_on) : ?>
                <div class="post-metas">
                    <div class="meta-inner align-items-center">
                        <?php if ($post_date_on) : ?>
                            <span class="pxl-item--date">
                                <?php echo get_the_date('M d Y'); ?>
                            </span>
                        <?php endif; ?>

                        <?php if ($post_comments_on) : ?>
                            <span class="post-comments  align-items-center">
                                <a href="<?php comments_link(); ?>">
                                    <span><?php comments_number(esc_html__('No Comments', 'frameflow'), esc_html__(' 1 Comment', 'frameflow'), esc_html__('%  Comments', 'frameflow')); ?></span>
                                </a>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif;
        }

        public function get_all_categories_list()
        {
            $categories = get_categories();
            if (!empty($categories)) {
                echo '<div class="category-carousel">';
                foreach ($categories as $category) {
                    $bg_category = get_term_meta($category->term_id, 'bg_category', true);
                    $bg_url = !empty($bg_category['url']) ? esc_url($bg_category['url']) : '';
                    $category_link = get_category_link($category->term_id);

                    echo '<div class="category-item">';
                    if ($bg_url) {
                        echo '<div class="category-banner">
                        <a href="' . esc_url($category_link) . '">
                        <img src="' . $bg_url . '" alt="' . esc_attr($category->name) . '">
                        </a>
                        </div>';
                    }
                    echo '<a href="' . esc_url($category_link) . '" class="category-title">' . esc_html($category->name) . '</a> 
                    <span class="post-count">' . sprintf(__('%d posts', 'frameflow'), $category->count) . '</span>';
                    echo '</div>';
                }
                echo '</div>';
            }
        }

        public function get_archive_meta_2($post_id = 0)
        { ?>
            <div class="post-metas-2">
                <div class="meta-inner ">
                    <span class="post-date-category">
                        <span class="post-date-post"><?php echo get_the_date('d M'); ?> </span>
                        <span><?php the_terms($post_id, 'category', '', ', ', ''); ?></span>
                    </span>
                </div>
            </div>
        <?php }

        public function get_post_title()
        {
            $post_title_on = frameflow()->get_theme_opt('post_title_on', '0');
            if ($post_title_on == '0') return;
        ?>
            <h5 class="post-title">
                <a href="<?php echo esc_url(get_permalink()); ?>" title="<?php the_title_attribute(); ?>">
                    <?php the_title(); ?>
                </a>
            </h5>
        <?php
        }

        public function get_excerpt()
        {
            $archive_excerpt_length = frameflow()->get_theme_opt('archive_excerpt_length', '20');
            $frameflow_the_excerpt = get_the_excerpt();
            if (!empty($frameflow_the_excerpt)) {
                echo wp_trim_words($frameflow_the_excerpt, $archive_excerpt_length, $more = null);
            } else {
                echo wp_kses_post($this->get_excerpt_more($archive_excerpt_length));
            }
        }

        public function get_excerpt_more($length = 55, $post = null)
        {
            $post = get_post($post);

            if (empty($post) || 0 >= $length) {
                return '';
            }

            if (post_password_required($post)) {
                return esc_html__('Post password required.', 'frameflow');
            }

            $content = apply_filters('the_content', strip_shortcodes($post->post_content));
            $content = str_replace(']]>', ']]&gt;', $content);

            $excerpt_more = apply_filters('frameflow_excerpt_more', '&hellip;');
            $excerpt      = wp_trim_words($content, $length, $excerpt_more);

            return $excerpt;
        }

        public function get_post_metas()
        {
            $post_date_on   = (bool) frameflow()->get_theme_opt('post_date_on', true);   
            $post_categories_on    = (bool) frameflow()->get_theme_opt('post_categories_on', true); 

            if (! $post_date_on && ! $post_categories_on) {
                return;
            }

            $post_id = get_the_ID();
            if (! $post_id) {
                return;
            }

            $categories_html = '';
            if ($post_categories_on) {
                $categories_html = get_the_category_list('', ', ', '', $post_id);
            }
        ?>
            <div class="post-metas">
                <div class="meta-inner align-items-center">

                    <?php if ($post_categories_on && $categories_html) : ?>
                        <span class="post-categories align-items-center">
                            <?php echo wp_kses_post($categories_html); ?>
                        </span>
                    <?php endif; ?>

                    <?php if ($post_date_on) : ?>
                        <span class="pxl-item--date">
                            <?php echo esc_html(get_the_date('M d, Y', $post_id)); ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        <?php
        }

        public function frameflow_set_post_views($postID)
        {
            $countKey = 'post_views_count';
            $count    = get_post_meta($postID, $countKey, true);
            if ($count == '') {
                $count = 0;
                delete_post_meta($postID, $countKey);
                add_post_meta($postID, $countKey, '0');
            } else {
                $count++;
                update_post_meta($postID, $countKey, $count);
            }
        }

        public function get_post_tags($taxonomy = 'post_tag')
        {
            if ($taxonomy === 'post_tag') {
                $post_tag = frameflow()->get_theme_opt('post_tag', true);
                if ($post_tag != '1') return;
            }
            $tags_list = get_the_term_list(get_the_ID(), $taxonomy, '', ' ');
            if ($tags_list && !is_wp_error($tags_list)) {
                echo '<div class="post-tags ">';
                printf('%2$s', '', $tags_list);
                echo '</div>';
            }
        }

        public function get_post_category($post_id = 0)
        {
            $archive_category = frameflow()->get_theme_opt('archive_category', true);

            $post_category = $archive_category && has_category('', $post_id);
            $post_date = true;

            echo '<ul class="pxl-item--meta">';

            if ($post_category) {
                echo '<li class="item--category">';
                echo get_the_term_list($post_id, 'category', '', '');
                echo '</li>';
            }

            echo '</ul>';
        }

        public function get_post_share($post_id = 0)
        {
            $post_id = $post_id ? $post_id : get_the_ID();
            $post_type = get_post_type($post_id);
            $opt_prefix = 'post_';
            if ($post_type === 'event') {
                $opt_prefix = 'event_';
            }
            
            $post_social_share = frameflow()->get_theme_opt($opt_prefix . 'social_share', false);
            $share_icons = frameflow()->get_theme_opt('post_social_share_icon', []);
            $social_facebook = frameflow()->get_theme_opt('social_facebook', []);
            $social_twitter = frameflow()->get_theme_opt('social_twitter', []);
            $social_pinterest = frameflow()->get_theme_opt('social_pinterest', []);
            $social_linkedin = frameflow()->get_theme_opt('social_linkedin', []);
            if (empty($post_social_share)) return;
            $post = get_post($post_id);
        ?>
            <div class="post-shares align-items-center">
                <span class="label"><?php echo esc_html__('Follow:', 'frameflow'); ?>
                </span>
                <div class="social-share">
                    <div class="social">
                        <?php if ($social_facebook): ?>
                            <a class="pxl-icon " title="<?php echo esc_attr__('Facebook', 'frameflow'); ?>" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_the_permalink($post_id)); ?>">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                        <?php endif; ?>
                        <?php if ($social_twitter): ?>
                            <a class="pxl-icon " title="<?php echo esc_attr__('Twitter', 'frameflow'); ?>" target="_blank" href="https://twitter.com/intent/tweet?original_referer=<?php echo urldecode(home_url('/')); ?>&url=<?php echo urlencode(get_the_permalink($post_id)); ?>&text=<?php the_title(); ?>%20">
                                <span class="bi-twitter"></span>
                            </a>
                        <?php endif; ?>
                        <?php if ($social_pinterest): ?>
                            <a class="pxl-icon " title="<?php echo esc_attr__('Pinterest', 'frameflow'); ?>" target="_blank" href="https://pinterest.com/pin/create/button/?url=<?php echo urlencode(get_the_post_thumbnail_url($post_id, 'full')); ?>&media=&description=<?php echo urlencode(the_title_attribute(array('echo' => false, 'post' => $post))); ?>">
                                <i class="bi-pinterest"></i>
                            </a>
                        <?php endif; ?>
                        <?php if ($social_linkedin): ?>
                            <a class="pxl-icon " title="<?php echo esc_attr__('Linkedin', 'frameflow'); ?>" target="_blank" href="https://www.linkedin.com/cws/share?url=<?php echo urlencode(get_the_permalink($post_id)); ?>">
                                <i class="bi-linkedin"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php
        }

        public function get_post_nav()
        {
            $post_navigation = frameflow()->get_theme_opt('post_navigation', false);
            if ($post_navigation != '1') return;
            global $post;
            
            if ( ! $post instanceof WP_Post ) {
                return;
            }
            $previous = (is_attachment()) ? get_post($post->post_parent) : get_adjacent_post(false, '', true);
            $next     = get_adjacent_post(false, '', false);

            if (! $next && ! $previous)
                return;
        ?>
            <?php
            $next_post = get_next_post();
            $previous_post = get_previous_post();
            if (empty($previous_post) && empty($next_post)) return;

            ?>
            <div class="single-next-prev-nav row gx-0 justify-content-between align-items-center">
                <?php if (!empty($previous_post)): ?>
                    <div class="nav-next-prev prev col relative text-start">
                        <div class="nav-inner">
                            <?php previous_post_link('%link', ''); ?>
                            <div class="nav-label-wrap justify-content-center align-items-center">
                                <i class="bootstrap-icons bi-arrow-left"></i>
                            </div>
                            <div class="nav-title-wrap d-none d-sm-flex">
                                <span class="nav-label"><?php echo esc_html__('Previous Post', 'frameflow'); ?></span>
                                <div class="nav-title"><?php echo wp_trim_words(get_the_title($previous_post->ID), 5, '...'); ?></div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="grid-archive">
                    <a href="<?php echo get_post_type_archive_link('post'); ?>">
                        <div class="nav-archive-button">
                            <div class="archive-btn-square square-1"></div>
                            <div class="archive-btn-square square-2"></div>
                            <div class="archive-btn-square square-3"></div>
                        </div>
                    </a>
                </div>
                <?php if (!empty($next_post)) : ?>
                    <div class="nav-next-prev next col relative text-end justify-content-end">
                        <div class="nav-inner">
                            <?php next_post_link('%link', ''); ?>
                            <div class="nav-label-wrap justify-content-center align-items-center">
                                <i class="bootstrap-icons bi-arrow-right"></i>
                            </div>
                            <div class="nav-title-wrap  align-items-end d-none d-sm-flex">
                                <span class="nav-label"><?php echo esc_html__('Newer Post', 'frameflow'); ?></span>
                                <div class="nav-title"><?php echo wp_trim_words(get_the_title($next_post->ID), 5, '...'); ?></div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php
        }
        public function get_project_nav()
        {
            global $post;
            if ( ! $post instanceof WP_Post ) {
                return;
            }
            $previous = (is_attachment()) ? get_post($post->post_parent) : get_adjacent_post(false, '', true);
            $next     = get_adjacent_post(false, '', false);
            $link_grid = frameflow()->get_theme_opt('link_grid', '');
            if (! $next && ! $previous)
                return;
        ?>
            <?php
            $next_post = get_next_post();
            $previous_post = get_previous_post();

            if (!empty($next_post) || !empty($previous_post)) {
            ?>
                <div class="pxl-project--navigation">
                    <div class="pxl--items">
                        <div class="pxl--item pxl--item-prev">
                            <?php if (is_a($previous_post, 'WP_Post') && get_the_title($previous_post->ID) != '') {
                            ?>
                                <a href="<?php echo esc_url(get_permalink($previous_post->ID)); ?>"><i class="far fa-arrow-left"></i>Prev Project</a>
                            <?php } ?>
                        </div>
                        <div class="pxl--item pxl--item-grid">
                            <?php if (!empty($link_grid)) { ?>
                                <a href="<?php echo esc_url($link_grid); ?>">
                                    <span class="bl bl1"></span>
                                    <span class="bl bl2"></span>
                                    <span class="bl bl3"></span>
                                    <span class="bl bl4"></span>
                                </a>
                            <?php } ?>
                        </div>
                        <div class="pxl--item pxl--item-next">
                            <?php if (is_a($next_post, 'WP_Post') && get_the_title($next_post->ID) != '') {
                            ?>
                                <a href="<?php echo esc_url(get_permalink($next_post->ID)); ?>">Next Project <i class="far fa-arrow-right"></i> </a>
                            <?php } ?>
                        </div>
                    </div><!-- .nav-links -->
                </div>
                <?php }
        }
        public function get_related_post()
        {
            $post_related_on = frameflow()->get_theme_opt('post_related_on', true);

            if ($post_related_on) {
                global $post;
                if (!$post) return;
                $current_id = $post->ID;
                $posttags = get_the_category($post->ID);
                if (empty($posttags)) return;

                $tags = array();

                foreach ($posttags as $tag) {

                    $tags[] = $tag->term_id;
                }
                $post_number = '6';
                $query_similar = new WP_Query(array('posts_per_page' => $post_number, 'post_type' => 'post', 'post_status' => 'publish', 'category__in' => $tags, 'post__not_in'   => [$current_id]));

                if (count($query_similar->posts) > 1) {
                    wp_enqueue_script('swiper');
                    wp_enqueue_script('pxl-swiper');
                    $opts = [
                        'slide_direction'               => 'horizontal',
                        'slide_percolumn'               => '1',
                        'slide_mode'                    => 'slide',
                        'slides_to_show'                => 3,
                        'slides_to_show_lg'             => 3,
                        'slides_to_show_md'             => 2,
                        'slides_to_show_sm'             => 2,
                        'slides_to_show_xs'             => 1,
                        'slides_to_scroll'              => 1,
                        'slides_gutter'                 => 30,
                        'arrow'                         => true,
                        'dots'                          => false,
                        'dots_style'                    => 'bullets'
                    ];
                    $data_settings = wp_json_encode($opts);
                    $dir           = is_rtl() ? 'rtl' : 'ltr';

                    $author_id = $post->post_author;
                    $author = get_user_by('id', $author_id);

                ?>
                    <div class="pxl-related-post">
                        <div class="pxl-related-post-top">
                            <h3 class="widget-title"><?php echo esc_html__('Related Posts', 'frameflow'); ?></h3>
                            <div class="pxl-swiper-arrow-wrap style-3">
                                <div class="pxl-swiper-arrow pxl-swiper-arrow-prev">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M5.78034 3.96967C5.48747 3.67678 5.01257 3.67678 4.71969 3.96967L0.219694 8.46968C-0.0731812 8.76255 -0.0731812 9.23745 0.219694 9.53033L4.71969 14.0303C5.01257 14.3232 5.48747 14.3232 5.78034 14.0303C6.07322 13.7375 6.07322 13.2626 5.78034 12.9697L1.81067 9L5.78034 5.03033C6.07322 4.73743 6.07322 4.26257 5.78034 3.96967Z" fill="#1A1A1A"/>
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M18 9C18 8.58578 17.6642 8.25 17.25 8.25H0.75C0.335775 8.25 0 8.58578 0 9C0 9.41422 0.335775 9.75 0.75 9.75H17.25C17.6642 9.75 18 9.41422 18 9Z" fill="#1A1A1A"/>
                                    </svg>
                                </div>
                                <div class="pxl-swiper-arrow pxl-swiper-arrow-next">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M12.2197 3.96967C12.5125 3.67678 12.9874 3.67678 13.2803 3.96967L17.7803 8.46968C18.0732 8.76255 18.0732 9.23745 17.7803 9.53033L13.2803 14.0303C12.9874 14.3232 12.5125 14.3232 12.2197 14.0303C11.9268 13.7375 11.9268 13.2626 12.2197 12.9697L16.1893 9L12.2197 5.03033C11.9268 4.73743 11.9268 4.26257 12.2197 3.96967Z" fill="#1A1A1A"/>
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M0 9C0 8.58578 0.335786 8.25 0.75 8.25H17.25C17.6642 8.25 18 8.58578 18 9C18 9.41422 17.6642 9.75 17.25 9.75H0.75C0.335786 9.75 0 9.41422 0 9Z" fill="#1A1A1A"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="pxl-swiper-container pxl-mouse-wheel" data-settings="<?php echo esc_attr($data_settings) ?>" data-rtl="<?php echo esc_attr($dir) ?>">
                            <div class="pxl-related-post-inner pxl-swiper-wrapper swiper-wrapper wow fadeIn" data-wow-delay="300ms" data-wow-duration="1.2s">
                                <?php foreach ($query_similar->posts as $post):
                                    $thumbnail_url = '';
                                    if (has_post_thumbnail(get_the_ID()) && wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), false)) :
                                        $thumbnail_url = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'frameflow-thumb-related', false);
                                    endif;
                                    if ($post->ID !== $current_id) : ?>
                                        <div class="pxl-swiper-slide swiper-slide grid-item">
                                            <div class="pxl-post--inner">
                                                <div class="pxl-post--holder">
                                                    <div class="pxl-post--meta">
                                                        <div class="pxl-post--category">
                                                            <?php echo get_the_category_list($post->ID, ', ', ''); ?>
                                                        </div>  
                                                        <div class="pxl-item--date">
                                                            <?php echo get_the_date('Y/m/d', $post->ID); ?>
                                                        </div>
                                                    </div>
                                                    <h6 class="pxl-post--title">
                                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                                    </h6>
                                                    <div class="pxl-post--button">
                                                        <a class="btn--readmore btn btn-2-icons" href="<?php echo esc_url(get_permalink($post->ID)); ?>">
                                                            <span class="btn--text">
                                                                <?php echo esc_html__('Read More', 'frameflow'); ?>
                                                            </span>
                                                            <span class="btn-icon-left">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="13" viewBox="0 0 16 13" fill="none"><path d="M9.6 12.7999C9.39526 12.7999 9.19053 12.7219 9.03432 12.5657C8.7219 12.2532 8.7219 11.7467 9.03432 11.4343L13.2686 7.19999H0.800009C0.358159 7.19999 0 6.8418 0 6.39998C0 5.95813 0.358159 5.59997 0.800009 5.59997H13.2686L9.03432 1.36567C8.7219 1.05326 8.7219 0.546725 9.03432 0.234311C9.3467 -0.0781035 9.8533 -0.0781035 10.1657 0.234311L15.7657 5.8343L15.7674 5.83604C15.7677 5.83632 15.768 5.83667 15.7683 5.83695C15.7686 5.83723 15.7688 5.83751 15.7691 5.83778C15.7695 5.8382 15.7699 5.83862 15.7703 5.83904C15.7705 5.83918 15.7706 5.83932 15.7708 5.83949C15.7713 5.84005 15.7718 5.84057 15.7724 5.84109L15.7724 5.84116C15.8444 5.91483 15.8992 5.9989 15.937 6.08847C15.9371 6.08872 15.9372 6.089 15.9373 6.08924C15.9374 6.08952 15.9376 6.08983 15.9377 6.09011C15.9778 6.18543 15.9999 6.29015 15.9999 6.40002C15.9999 6.50989 15.9778 6.61461 15.9377 6.70993C15.9376 6.71017 15.9374 6.71052 15.9373 6.7108C15.9372 6.71104 15.9371 6.71128 15.937 6.71153C15.8992 6.80114 15.8444 6.88521 15.7724 6.95888L15.7724 6.95891C15.7718 6.95947 15.7713 6.95999 15.7708 6.96051C15.7707 6.96065 15.7705 6.96079 15.7703 6.96096C15.7699 6.96142 15.7695 6.9618 15.7691 6.96225C15.7688 6.9625 15.7686 6.96281 15.7683 6.96305C15.768 6.96333 15.7677 6.96368 15.7674 6.96396C15.7668 6.96455 15.7662 6.96514 15.7657 6.9657L10.1657 12.5657C10.0095 12.7219 9.80474 12.7999 9.6 12.7999Z" fill="#1A1A1A"></path></svg>
                                                            </span>
                                                        </a>
                                                    </div>
                                                </div>
                                                <?php if (has_post_thumbnail()) { ?>
                                                    <div class="pxl-post--featured">
                                                        <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('frameflow-thumb-related'); ?></a>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                <?php endif;
                                endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php }
            }

            wp_reset_postdata();
        }

        public function get_related_event()
        {
            $event_related_on = frameflow()->get_theme_opt('event_related_on', true);

            if ($event_related_on) {
                global $post;
                if (!$post instanceof WP_Post) return;
                $current_id = $post->ID;
                $event_tags = get_the_terms($post->ID, 'event_tag');
                if (empty($event_tags) || is_wp_error($event_tags)) return;

                $tags = array();

                foreach ($event_tags as $tag) {
                    $tags[] = $tag->term_id;
                }
                $post_number = '6';
                $query_similar = new WP_Query(array(
                    'posts_per_page' => $post_number, 
                    'post_type'      => 'event', 
                    'post_status'    => 'publish', 
                    'post__not_in'   => [$current_id],
                    'tax_query'      => array(
                        array(
                            'taxonomy' => 'event_tag',
                            'field'    => 'term_id',
                            'terms'    => $tags,
                        ),
                    ),
                ));

                if (count($query_similar->posts) > 1) {
                    wp_enqueue_script('swiper');
                    wp_enqueue_script('pxl-swiper');
                    $opts = [
                        'slide_direction'               => 'horizontal',
                        'slide_percolumn'               => '1',
                        'slide_mode'                    => 'slide',
                        'slides_to_show'                => 3,
                        'slides_to_show_lg'             => 3,
                        'slides_to_show_md'             => 2,
                        'slides_to_show_sm'             => 2,
                        'slides_to_show_xs'             => 1,
                        'slides_to_scroll'              => 1,
                        'slides_gutter'                 => 20,
                        'arrow'                         => false,
                        'dots'                          => false,
                        'dots_style'                    => 'bullets'
                    ];
                    $data_settings = wp_json_encode($opts);
                    $dir           = is_rtl() ? 'rtl' : 'ltr';

                ?>
                    <div class="pxl-related-event">
                        <h3 class="widget-title"><?php echo esc_html__('Related Events', 'frameflow'); ?></h3>
                        <div class="pxl-swiper-container pxl-mouse-wheel" data-settings="<?php echo esc_attr($data_settings) ?>" data-rtl="<?php echo esc_attr($dir) ?>">
                            <div class="pxl-related-event-inner pxl-swiper-wrapper swiper-wrapper">
                                <?php foreach ($query_similar->posts as $post):
                                    $event_id = $post->ID;
                                    if ($event_id !== $current_id) : 
                                        $start    = \MyEventsWooCommerce\Core\Utils::get_event_meta($event_id, 'start', '');
                                        $end      = \MyEventsWooCommerce\Core\Utils::get_event_meta($event_id, 'end', '');
                                        $location = \MyEventsWooCommerce\Core\Utils::get_event_meta($event_id, 'location', '');
                                        $ticket_products = \MyEventsWooCommerce\Core\Utils::get_event_meta($event_id, 'ticket_products', array());
                                        ?>
                                        <div class="pxl-swiper-slide swiper-slide grid-item me-event-item">
                                            <div class="me-event-content">
                                                <h4 class="me-event-title">
                                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                                </h4>
                                                
                                                <div class="me-event-description">
                                                    <?php
                                                    $excerpt = get_the_excerpt($post->ID);
                                                    if (empty($excerpt)) {
                                                        $excerpt = wp_trim_words($post->post_content, 25, '...');
                                                    }
                                                    echo esc_html($excerpt);
                                                    ?>
                                                </div>

                                                <div class="me-event-meta">
                                                    <?php if ($start) :
                                                        $start_time = strtotime($start);
                                                        $end_time = $end ? strtotime($end) : false;
                                                        $time_format = 'g:i A';
                                                        $date_format = 'M d, Y';
                                                    ?>
                                                        <div class="me-event-meta-item">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                                <path d="M9.6 0C14.9025 0 19.2 4.2975 19.2 9.6C19.2 14.9025 14.9025 19.2 9.6 19.2C4.2975 19.2 0 14.9025 0 9.6C0 4.2975 4.2975 0 9.6 0ZM8.7 4.5V9.6C8.7 9.9 8.85 10.1813 9.10125 10.35L12.7013 12.75C13.1138 13.0275 13.6725 12.915 13.95 12.4988C14.2275 12.0825 14.115 11.5275 13.6988 11.25L10.5 9.12V4.5C10.5 4.00125 10.0988 3.6 9.6 3.6C9.10125 3.6 8.7 4.00125 8.7 4.5Z" fill="#1A1A1A" />
                                                            </svg>
                                                            <strong>
                                                                <?php 
                                                                echo esc_html(date($time_format, $start_time)); 
                                                                if ($end_time) {
                                                                    echo ' - ' . esc_html(date($time_format, $end_time));
                                                                }
                                                                ?>
                                                            </strong>
                                                        </div>
                                                        <div class="me-event-meta-item">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="18" viewBox="0 0 17 18" fill="none">
                                                                <path d="M4.8 0C4.13625 0 3.6 0.53625 3.6 1.2V2.4H2.4C1.07625 2.4 0 3.47625 0 4.8V6.6H16.8V4.8C16.8 3.47625 15.7237 2.4 14.4 2.4H13.2V1.2C13.2 0.53625 12.6637 0 12 0C11.3362 0 10.8 0.53625 10.8 1.2V2.4H6V1.2C6 0.53625 5.46375 0 4.8 0ZM0 8.4V15.6C0 16.9238 1.07625 18 2.4 18H14.4C15.7237 18 16.8 16.9238 16.8 15.6V8.4H0Z" fill="#1A1A1A" />
                                                            </svg>
                                                            <strong><?php echo esc_html(strtoupper(date('M d, Y', $start_time))); ?></strong>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if ($location) : ?>
                                                        <div class="me-event-meta-item">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="20" viewBox="0 0 15 20" fill="none">
                                                                <path d="M0.00374994 7.0725C0.00374994 3.165 3.22875 0 7.20375 0C11.1787 0 14.4037 3.165 14.4037 7.0725C14.4037 11.5463 9.89625 16.9088 8.01375 18.9525C7.57125 19.4325 6.8325 19.4325 6.39 18.9525C4.5075 16.9088 0 11.5463 0 7.0725H0.00374994ZM7.20375 9.6C8.5275 9.6 9.60375 8.52375 9.60375 7.2C9.60375 5.87625 8.5275 4.8 7.20375 4.8C5.88 4.8 4.80375 5.87625 4.80375 7.2C4.80375 8.52375 5.88 9.6 7.20375 9.6Z" fill="black" />
                                                            </svg>
                                                            <strong><?php echo esc_html(strtoupper($location)); ?></strong>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if (! empty($ticket_products) && is_array($ticket_products) && class_exists('WooCommerce')) :
                                                        $pricing_parts = array();
                                                        foreach ($ticket_products as $type => $product_id) {
                                                            $product = wc_get_product($product_id);
                                                            if ($product && $product->is_in_stock()) {
                                                                $price = $product->get_price();
                                                                $type_label = strtoupper($type);
                                                                $pricing_parts[] = $type_label . ' $' . number_format($price, 0, '.', '');
                                                            }
                                                        }
                                                        if (! empty($pricing_parts)) :
                                                    ?>
                                                            <div class="me-event-pricing">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="15" viewBox="0 0 22 15" fill="none">
                                                                    <path d="M2.4 0C1.07625 0 0 1.07625 0 2.4V4.8C0 5.13 0.2775 5.38875 0.58875 5.4975C1.29375 5.74125 1.8 6.4125 1.8 7.2C1.8 7.9875 1.29375 8.65875 0.58875 8.9025C0.2775 9.01125 0 9.27 0 9.6V12C0 13.3238 1.07625 14.4 2.4 14.4H19.2C20.5237 14.4 21.6 13.3238 21.6 12V9.6C21.6 9.27 21.3225 9.01125 21.0112 8.9025C20.3062 8.65875 19.8 7.9875 19.8 7.2C19.8 6.4125 20.3062 5.74125 21.0112 5.4975C21.3225 5.38875 21.6 5.13 21.6 4.8V2.4C21.6 1.07625 20.5237 0 19.2 0H2.4ZM15.6 10.2V4.2H6V10.2H15.6ZM4.2 3.6C4.2 2.93625 4.73625 2.4 5.4 2.4H16.2C16.8637 2.4 17.4 2.93625 17.4 3.6V10.8C17.4 11.4638 16.8637 12 16.2 12H5.4C4.73625 12 4.2 11.4638 4.2 10.8V3.6Z" fill="#1A1A1A" />
                                                                </svg>
                                                                <span class="me-event-pricing-text"><?php echo esc_html(implode(' / ', $pricing_parts)); ?></span>
                                                            </div>
                                                    <?php
                                                        endif;
                                                    endif;
                                                    ?>
                                                </div>

                                                <div class="me-event-footer">
                                                    <a href="<?php the_permalink(); ?>" class="me-event-details-btn btn btn-text-nanuk btn-2-icons">
                                                        <span class="pxl--btn-text" data-text="<?php
                                                            $button_text = get_option('me_event_details_button_text', __('EVENT DETAILS', 'frameflow'));
                                                            echo esc_attr($button_text); ?>">
                                                            <?php
                                                            $chars = preg_split('//u', $button_text, -1, PREG_SPLIT_NO_EMPTY);
                                                            foreach ($chars as $char) {
                                                                if ($char == ' ') {
                                                                    echo '<span class="spacer">&nbsp;</span>';
                                                                } else {
                                                                    echo '<span>' . esc_html($char) . '</span>';
                                                                }
                                                            }
                                                            ?>
                                                        </span>
                                                        <span class="btn-icon-left">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="13" viewBox="0 0 16 13" fill="none">
                                                                <path d="M9.6 12.7999C9.39526 12.7999 9.19053 12.7219 9.03432 12.5657C8.7219 12.2532 8.7219 11.7467 9.03432 11.4343L13.2686 7.19999H0.800009C0.358159 7.19999 0 6.8418 0 6.39998C0 5.95813 0.358159 5.59997 0.800009 5.59997H13.2686L9.03432 1.36567C8.7219 1.05326 8.7219 0.546725 9.03432 0.234311C9.3467 -0.0781035 9.8533 -0.0781035 10.1657 0.234311L15.7657 5.8343L15.7674 5.83604C15.7677 5.83632 15.768 5.83667 15.7683 5.83695C15.7686 5.83723 15.7688 5.83751 15.7691 5.83778C15.7695 5.8382 15.7699 5.83862 15.7703 5.83904C15.7705 5.83918 15.7706 5.83932 15.7708 5.83949C15.7713 5.84005 15.7718 5.84057 15.7724 5.84109L15.7724 5.84116C15.8444 5.91483 15.8992 5.9989 15.937 6.08847C15.9371 6.08872 15.9372 6.089 15.9373 6.08924C15.9374 6.08952 15.9376 6.08983 15.9377 6.09011C15.9778 6.18543 15.9999 6.29015 15.9999 6.40002C15.9999 6.50989 15.9778 6.61461 15.9377 6.70993C15.9376 6.71017 15.9374 6.71052 15.9373 6.7108C15.9372 6.71104 15.9371 6.71128 15.937 6.71153C15.8992 6.80114 15.8444 6.88521 15.7724 6.95888L15.7724 6.95891C15.7718 6.95947 15.7713 6.95999 15.7708 6.96051C15.7707 6.96065 15.7705 6.96079 15.7703 6.96096C15.7699 6.96142 15.7695 6.9618 15.7691 6.96225C15.7688 6.9625 15.7686 6.96281 15.7683 6.96305C15.768 6.96333 15.7677 6.96368 15.7674 6.96396C15.7668 6.96455 15.7662 6.96514 15.7657 6.9657L10.1657 12.5657C10.0095 12.7219 9.80474 12.7999 9.6 12.7999Z" fill="#fff"></path>
</svg>
                                                        </span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                <?php endif;
                                endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php }
            }
            wp_reset_postdata();
        }
    }
}
