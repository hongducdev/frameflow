<?php

use Elementor\Embed;

if (!function_exists('frameflow_get_post_grid')) {
    function frameflow_get_post_grid($posts = [], $settings = [])
    {
        if (empty($posts) || !is_array($posts) || empty($settings) || !is_array($settings)) {
            return false;
        }
        switch ($settings['layout']) {
            case 'post-1':
                frameflow_get_post_grid_layout1($posts, $settings);
                break;

            case 'portfolio-1':
                frameflow_get_portfolio_grid_layout1($posts, $settings);
                break;

            case 'portfolio-2':
                frameflow_get_portfolio_grid_layout2($posts, $settings);
                break;

            default:
                return false;
                break;
        }
    }
}

// Start Post Grid
//--------------------------------------------------
function frameflow_get_post_grid_layout1($posts = [], $settings = [])
{
    extract($settings);

    $images_size = !empty($img_size) ? $img_size : '767x444';

    if (is_array($posts)):
        foreach ($posts as $key => $post):
            $item_class = "pxl-grid-item col-xl-{$col_xl} col-lg-{$col_lg} col-md-{$col_md} col-sm-{$col_sm} col-{$col_xs}";
            if (isset($grid_masonry) && !empty($grid_masonry[$key]) && (count($grid_masonry) > 1)) {
                if ($grid_masonry[$key]['col_xl_m'] == 'col-66') {
                    $col_xl_m = '66-pxl';
                } else {
                    $col_xl_m = 12 / $grid_masonry[$key]['col_xl_m'];
                }
                if ($grid_masonry[$key]['col_lg_m'] == 'col-66') {
                    $col_lg_m = '66-pxl';
                } else {
                    $col_lg_m = 12 / $grid_masonry[$key]['col_lg_m'];
                }
                $col_md_m = 12 / $grid_masonry[$key]['col_md_m'];
                $col_sm_m = 12 / $grid_masonry[$key]['col_sm_m'];
                $col_xs_m = 12 / $grid_masonry[$key]['col_xs_m'];
                $item_class = "pxl-grid-item col-xl-{$col_xl_m} col-lg-{$col_lg_m} col-md-{$col_md_m} col-sm-{$col_sm_m} col-{$col_xs_m}";

                $img_size_m = $grid_masonry[$key]['img_size_m'];
                if (!empty($img_size_m)) {
                    $images_size = $img_size_m;
                }
            } elseif (!empty($img_size)) {
                $images_size = $img_size;
            }
            $author_id = $post->post_author;
            $author = get_user_by('id', $author_id);
            if (!empty($tax))
                $filter_class = pxl_get_term_of_post_to_class($post->ID, array_unique($tax));
            else
                $filter_class = ''; ?>
            <div class="<?php echo esc_attr($item_class . ' ' . $filter_class); ?>">
                <div class="pxl-post--inner <?php echo esc_attr($pxl_animate); ?>" data-wow-duration="1.2s">
                    <div class="pxl-post--hold">
                        <?php if ($show_category == 'true' || $show_date == 'true'): ?>
                            <div class="pxl-post--meta">
                                <?php if ($show_category == 'true'): ?>
                                    <div class="pxl-post--category">
                                        <?php the_terms($post->ID, 'category', '', ' '); ?>
                                    </div>
                                <?php endif; ?>
                                <?php if ($show_date == 'true'): ?>
                                    <div class="post-date d-flex">
                                        <?php echo get_the_date('M j, Y', $post->ID)  ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <h4 class="pxl-post--title title-hover-line"><a href="<?php echo esc_url(get_permalink($post->ID)); ?>"><?php echo pxl_print_html(get_the_title($post->ID)); ?></a></h4>
                        <?php if ($show_excerpt == 'true'): ?>
                            <div class="pxl-post--content">
                                <?php if ($show_excerpt == 'true'): ?>
                                    <?php
                                    echo wp_trim_words($post->post_excerpt, $num_words, null);
                                    ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($show_button == 'true') : ?>
                            <a class="btn--readmore btn btn-2-icons" href="<?php echo esc_url(get_permalink($post->ID)); ?>">
                                <span class="btn--text">
                                    <?php if (!empty($button_text)) {
                                        echo esc_attr($button_text);
                                    } else {
                                        echo esc_html__('Read more', 'frameflow');
                                    } ?>
                                </span>
                                <span class="btn-icon-left">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="13" viewBox="0 0 16 13" fill="none"><path d="M9.6 12.7999C9.39526 12.7999 9.19053 12.7219 9.03432 12.5657C8.7219 12.2532 8.7219 11.7467 9.03432 11.4343L13.2686 7.19999H0.800009C0.358159 7.19999 0 6.8418 0 6.39998C0 5.95813 0.358159 5.59997 0.800009 5.59997H13.2686L9.03432 1.36567C8.7219 1.05326 8.7219 0.546725 9.03432 0.234311C9.3467 -0.0781035 9.8533 -0.0781035 10.1657 0.234311L15.7657 5.8343L15.7674 5.83604C15.7677 5.83632 15.768 5.83667 15.7683 5.83695C15.7686 5.83723 15.7688 5.83751 15.7691 5.83778C15.7695 5.8382 15.7699 5.83862 15.7703 5.83904C15.7705 5.83918 15.7706 5.83932 15.7708 5.83949C15.7713 5.84005 15.7718 5.84057 15.7724 5.84109L15.7724 5.84116C15.8444 5.91483 15.8992 5.9989 15.937 6.08847C15.9371 6.08872 15.9372 6.089 15.9373 6.08924C15.9374 6.08952 15.9376 6.08983 15.9377 6.09011C15.9778 6.18543 15.9999 6.29015 15.9999 6.40002C15.9999 6.50989 15.9778 6.61461 15.9377 6.70993C15.9376 6.71017 15.9374 6.71052 15.9373 6.7108C15.9372 6.71104 15.9371 6.71128 15.937 6.71153C15.8992 6.80114 15.8444 6.88521 15.7724 6.95888L15.7724 6.95891C15.7718 6.95947 15.7713 6.95999 15.7708 6.96051C15.7707 6.96065 15.7705 6.96079 15.7703 6.96096C15.7699 6.96142 15.7695 6.9618 15.7691 6.96225C15.7688 6.9625 15.7686 6.96281 15.7683 6.96305C15.768 6.96333 15.7677 6.96368 15.7674 6.96396C15.7668 6.96455 15.7662 6.96514 15.7657 6.9657L10.1657 12.5657C10.0095 12.7219 9.80474 12.7999 9.6 12.7999Z" fill="#fff"></path></svg>
                                </span> 
                            </a>
                        <?php endif; ?>
                    </div>  
                    <?php if (has_post_thumbnail($post->ID) && wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), false)):
                        $img_id = get_post_thumbnail_id($post->ID);
                        $img          = pxl_get_image_by_size(array(
                            'attach_id'  => $img_id,
                            'thumb_size' => $images_size
                        ));
                        $thumbnail    = $img['thumbnail'];
                    ?>
                        <div class="pxl-post--featured hover-imge-ripple" data-image-url="<?php echo esc_url($img['url']); ?>">
                            <a href="<?php echo esc_url(get_permalink($post->ID)); ?>">
                                <?php echo wp_kses_post($thumbnail); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php
        endforeach;
    endif;
}

// End Post Grid
//--------------------------------------------------


// Start Portfolio Grid
//--------------------------------------------------
function frameflow_get_portfolio_grid_layout1($posts = [], $settings = [])
{
    extract($settings);
    $images_size = !empty($img_size) ? $img_size : '767x668';
    if (is_array($posts)):
        foreach ($posts as $key => $post):
            $item_class = "pxl-grid-item col-xl-{$col_xl} col-lg-{$col_lg} col-md-{$col_md} col-sm-{$col_sm} col-{$col_xs}";
            if (isset($grid_masonry) && !empty($grid_masonry[$key]) && (count($grid_masonry) > 1)) {
                if ($grid_masonry[$key]['col_xl_m'] == 'col-66') {
                    $col_xl_m = '66-pxl';
                } else {
                    $col_xl_m = 12 / $grid_masonry[$key]['col_xl_m'];
                }
                if ($grid_masonry[$key]['col_lg_m'] == 'col-66') {
                    $col_lg_m = '66-pxl';
                } else {
                    $col_lg_m = 12 / $grid_masonry[$key]['col_lg_m'];
                }
                $text_masonry= $grid_masonry[$key]['text_align_m'];
                $col_md_m = 12 / $grid_masonry[$key]['col_md_m'];
                $col_sm_m = 12 / $grid_masonry[$key]['col_sm_m'];
                $col_xs_m = 12 / $grid_masonry[$key]['col_xs_m'];
                $item_class = "pxl-grid-item col-xl-{$col_xl_m} col-lg-{$col_lg_m} col-md-{$col_md_m} col-sm-{$col_sm_m} col-{$col_xs_m} text-{$text_masonry}";

                $img_size_m = $grid_masonry[$key]['img_size_m'];
                if (!empty($img_size_m)) {
                    $images_size = $img_size_m;
                }
            } elseif (!empty($img_size)) {
                $images_size = $img_size;
            }

            if (!empty($tax))
                $filter_class = pxl_get_term_of_post_to_class($post->ID, array_unique($tax));
            else
                $filter_class = '';
            $img_id = get_post_thumbnail_id($post->ID);
            $portfolio_excerpt = get_post_meta($post->ID, 'portfolio_excerpt', true);
            $portfolio_external_link = get_post_meta($post->ID, 'portfolio_external_link', true);
            if ($img_id) {
                $img = pxl_get_image_by_size(array(
                    'attach_id'  => $img_id,
                    'thumb_size' => $images_size,
                    'class' => 'no-lazyload',
                ));
                $thumbnail = $img['thumbnail'];
                $thumbnail_url = $img['url'] ?? '';
            } else {
                $thumbnail = get_the_post_thumbnail($post->ID, $images_size);
                $thumbnail_url = get_the_post_thumbnail_url($post->ID, $images_size);
            }  

            $term_list_markup = '';
            if ($show_category == 'true') {
                $terms_list = get_the_term_list($post->ID, 'portfolio-category', '', '');
                if ($terms_list && !is_wp_error($terms_list)) {
                    $term_list_markup = $terms_list;
                }
            } ?>
            <div class="<?php echo esc_attr($item_class . ' ' . $filter_class); ?>">
                <div class="pxl-post--inner <?php echo esc_attr($pxl_animate); ?>" data-wow-duration="1s">
                    <?php if (! empty($thumbnail_url)) : ?>
                        <div class="pxl-post--featured hover-imge-ripple" data-image-url="<?php echo esc_url($thumbnail_url); ?>" style="background-image: url(<?php echo esc_url($thumbnail_url); ?>);">
                            <a href="<?php echo esc_url(get_permalink($post->ID)); ?>">
                                <?php echo wp_kses_post($thumbnail); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    <?php if ($show_category == 'true' || $show_title == 'true'): ?>
                        <div class="pxl-post--meta">
                            <?php if ($show_title == 'true'): ?>
                                <h6 class="pxl-post--title-s">
                                    <a href="<?php echo esc_url(get_permalink($post->ID)); ?>">
                                        <?php echo esc_html(get_the_title($post->ID)); ?>
                                    </a>
                                </h6>   
                            <?php endif; ?> 
                            <?php if ($show_category == 'true' && !empty($term_list_markup)): ?>
                                <div class="pxl-post--category">
                                    <?php echo wp_kses_post($term_list_markup); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach;
    endif;
}

function frameflow_get_portfolio_grid_layout2($posts = [], $settings = [])
{
    extract($settings);
    $images_size = !empty($img_size) ? $img_size : 'full';
    if (is_array($posts)):
        foreach ($posts as $key => $post):
            $item_class = "pxl-grid-item col-xl-{$col_xl} col-lg-{$col_lg} col-md-{$col_md} col-sm-{$col_sm} col-{$col_xs}";
            if (isset($grid_masonry) && !empty($grid_masonry[$key]) && (count($grid_masonry) > 1)) {
                if ($grid_masonry[$key]['col_xl_m'] == 'col-66') {
                    $col_xl_m = '66-pxl';
                } else {
                    $col_xl_m = 12 / $grid_masonry[$key]['col_xl_m'];
                }
                if ($grid_masonry[$key]['col_lg_m'] == 'col-66') {
                    $col_lg_m = '66-pxl';
                } else {
                    $col_lg_m = 12 / $grid_masonry[$key]['col_lg_m'];
                }
                $text_masonry= $grid_masonry[$key]['text_align_m'];
                $col_md_m = 12 / $grid_masonry[$key]['col_md_m'];
                $col_sm_m = 12 / $grid_masonry[$key]['col_sm_m'];
                $col_xs_m = 12 / $grid_masonry[$key]['col_xs_m'];
                $item_class = "pxl-grid-item col-xl-{$col_xl_m} col-lg-{$col_lg_m} col-md-{$col_md_m} col-sm-{$col_sm_m} col-{$col_xs_m} text-{$text_masonry}";

                $img_size_m = $grid_masonry[$key]['img_size_m'];
                if (!empty($img_size_m)) {
                    $images_size = $img_size_m;
                }
            } elseif (!empty($img_size)) {
                $images_size = $img_size;
            }

            if (!empty($tax))
                $filter_class = pxl_get_term_of_post_to_class($post->ID, array_unique($tax));
            else
                $filter_class = '';
            $img_id = get_post_thumbnail_id($post->ID);
            $portfolio_excerpt = get_post_meta($post->ID, 'portfolio_excerpt', true);
            $portfolio_external_link = get_post_meta($post->ID, 'portfolio_external_link', true);
            if ($img_id) {
                $img = pxl_get_image_by_size(array(
                    'attach_id'  => $img_id,
                    'thumb_size' => $images_size,
                    'class' => 'no-lazyload',
                ));
                $thumbnail = $img['thumbnail'];
                $thumbnail_url = $img['url'] ?? '';
            } else {
                $thumbnail = get_the_post_thumbnail($post->ID, $images_size);
                $thumbnail_url = get_the_post_thumbnail_url($post->ID, $images_size);
            }  

            $term_list_markup = '';
            if ($show_category == 'true') {
                $terms_list = get_the_term_list($post->ID, 'portfolio-category', '', '');
                if ($terms_list && !is_wp_error($terms_list)) {
                    $term_list_markup = $terms_list;
                }
            } ?>
            <div class="<?php echo esc_attr($item_class . ' ' . $filter_class); ?>">
                <div class="pxl-post--inner <?php echo esc_attr($pxl_animate); ?>" data-wow-duration="1s">
                    <?php if (! empty($thumbnail_url)) : ?>
                        <div class="pxl-post--featured hover-imge-ripple" data-image-url="<?php echo esc_url($thumbnail_url); ?>" style="background-image: url(<?php echo esc_url($thumbnail_url); ?>);">
                            <a href="<?php echo esc_url(get_permalink($post->ID)); ?>">
                                <?php echo wp_kses_post($thumbnail); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    <?php if ($show_category == 'true' || $show_title == 'true'): ?>
                        <div class="pxl-post--meta">
                            <?php if ($show_category == 'true' && !empty($term_list_markup)): ?>
                                <div class="pxl-post--category">
                                    <?php echo wp_kses_post($term_list_markup); ?>
                                </div>
                            <?php endif; ?>
                            <?php if ($show_title == 'true'): ?>
                                <h3 class="pxl-post--title-s">
                                    <a href="<?php echo esc_url(get_permalink($post->ID)); ?>">
                                        <?php echo esc_html(get_the_title($post->ID)); ?>
                                    </a>
                                </h3>   
                            <?php endif; ?> 
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach;
    endif;
}

// End Portfolio Grid
//--------------------------------------------------

add_action('wp_ajax_frameflow_load_more_post_grid', 'frameflow_load_more_post_grid');
add_action('wp_ajax_nopriv_frameflow_load_more_post_grid', 'frameflow_load_more_post_grid');
add_action('wp_ajax_frameflow_load_more_team_grid', 'frameflow_load_more_team_grid');
add_action('wp_ajax_nopriv_frameflow_load_more_team_grid', 'frameflow_load_more_team_grid');

function frameflow_get_team_grid_items($team_data = [], $settings = [])
{
    if (empty($team_data) || !is_array($team_data)) {
        return;
    }

    extract($settings);

    // Helper functions
    if (!function_exists('_pxl_int_between')) {
        function _pxl_int_between($v, $min = 1, $max = 12) {
            $i = intval($v);
            return max($min, min($max, $i));
        }
    }

    if (!function_exists('_pxl_col_class')) {
        function _pxl_col_class($grid) {
            $cols = _pxl_int_between($grid, 1, 12);
            $w = (int) floor(12 / $cols);
            return max(1, min(12, $w));
        }
    }

    if (!function_exists('_pxl_dep_slug')) {
        function _pxl_dep_slug($text) {
            return function_exists('sanitize_title') ? sanitize_title($text) : strtolower(trim(preg_replace('/[^a-z0-9\-]/', '', str_replace(' ', '-', $text))));
        }
    }

    if (!function_exists('_pxl_parse_departments')) {
        function _pxl_parse_departments($text) {
            $departments = array();
            if (empty($text)) return $departments;
            $parts = is_array($text) ? $text : preg_split('/[,|;]/', $text);
            foreach ($parts as $part) {
                $label = trim((string)$part);
                if ($label !== '' && ($slug = _pxl_dep_slug($label)) !== '') {
                    $departments[] = ['label' => $label, 'slug' => $slug];
                }
            }
            return $departments;
        }
    }

    $col_settings = ['col_xs' => 1, 'col_sm' => 2, 'col_md' => 2, 'col_lg' => 3, 'col_xl' => 3, 'col_xxl' => 4];
    $grid_classes = [];
    foreach ($col_settings as $key => $default) {
        $val = $settings[$key] ?? $default;
        $w = _pxl_col_class($val);
        $breakpoint = str_replace('col_', '', $key);
        $grid_classes[] = ($breakpoint === 'xs') ? "col-{$w}" : "col-{$breakpoint}-{$w}";
    }
    $item_class = "pxl-grid-item " . implode(' ', $grid_classes);

    foreach ($team_data as $key => $value):
        $item_settings = array_merge($settings, [
            'item_class' => $item_class,
            'img_size' => $img_size ?? 'full',
        ]);

        include get_template_directory() . '/elements/templates/pxl_team_grid/team-item.php';
    endforeach;
}

function frameflow_load_more_post_grid()
{
    try {
        if (!isset($_POST['settings'])) {
            throw new Exception(__('Something went wrong while requesting. Please try again!', 'frameflow'));
        }

        $settings = isset($_POST['settings']) ? $_POST['settings'] : null;

        $source = isset($settings['source']) ? $settings['source'] : '';
        $term_slug = isset($settings['term_slug']) ? $settings['term_slug'] : '';

        if (!empty($term_slug) && $term_slug != '*') {
            $term_slug = str_replace('.', '', $term_slug);
            $source = [$term_slug . '|' . $settings['tax'][0]];
        }

        if (isset($_POST['handler_click']) && sanitize_text_field(wp_unslash($_POST['handler_click'])) == 'filter') {
            set_query_var('paged', 1);
            $settings['paged'] = 1;
        } elseif (isset($_POST['handler_click']) && sanitize_text_field(wp_unslash($_POST['handler_click'])) == 'select_orderby') {
            set_query_var('paged', 1);
            $settings['paged'] = 1;
        } else {
            set_query_var('paged', (int)$settings['paged']);
        }

        extract(pxl_get_posts_of_grid($settings['post_type'], [
            'source'      => $source,
            'orderby'     => isset($settings['orderby']) ? $settings['orderby'] : 'date',
            'order'       => isset($settings['order']) ? ($settings['orderby'] == 'title' ? 'asc' : sanitize_text_field($settings['order'])) : 'desc',
            'limit'       => isset($settings['limit']) ? $settings['limit'] : '6',
            'post_ids'    => isset($settings['post_ids']) ? $settings['post_ids'] : [],
            'post_not_in' => isset($settings['post_not_in']) ? $settings['post_not_in'] : [],
        ], $settings['tax']));

        // Calculate already displayed items for load more functionality (before template call)
        $already_displayed = 0;
        if (isset($settings['pagination_type']) && $settings['pagination_type'] == 'loadmore') {
            // For load more, already_displayed should be the items shown before this page
            // Page 1: already_displayed = 0, Page 2: already_displayed = limit, Page 3: already_displayed = 2*limit, etc.
            $already_displayed = ((int)$settings['paged'] - 1) * (int)$settings['limit'];
        }
        // Add already_displayed to settings so templates can use it for numbering
        $settings['already_displayed'] = $already_displayed;

        ob_start();
        if (isset($settings['wg_type']) && $settings['wg_type'] == 'post-list') {
            frameflow_get_post_list($posts, $settings);
        } else {
            frameflow_get_post_grid($posts, $settings);
        }
        $html = ob_get_clean();

        $pagin_html = '';
        if (isset($settings['pagination_type']) && $settings['pagination_type'] == 'pagination') {
            ob_start();
            frameflow()->page->get_pagination($query, true);
            $pagin_html = ob_get_clean();
        }

        $result_count = '';
        if (isset($settings['show_toolbar']) && $settings['show_toolbar'] == 'show') {
            ob_start();
            if ((int)$settings['paged'] == 0) {
                $limit_start = 1;
                $limit_end = ((int)$settings['limit'] >= $total) ? $total : (int)$settings['limit'];
            } else {
                $limit_start = (((int)$settings['paged'] - 1) * (int)$settings['limit']) + 1;
                $limit_end = (int)$settings['paged'] * (int)$settings['limit'];
                $limit_end = ($limit_end >= $total) ? $total : $limit_end;
            }

            if (isset($settings['pagination_type']) && $settings['pagination_type'] == 'loadmore') {
                printf(
                    '<span class="result-count">%1$s %2$s %3$s %4$s %5$s</span>',
                    esc_html__('Showing', 'frameflow'),
                    '1-' . $limit_end,
                    esc_html__('of', 'frameflow'),
                    $total,
                    esc_html__('results', 'frameflow')
                );
            } else {
                printf(
                    '<span class="result-count">%1$s %2$s %3$s %4$s %5$s</span>',
                    esc_html__('Showing', 'frameflow'),
                    $limit_start . '-' . $limit_end,
                    esc_html__('of', 'frameflow'),
                    $total,
                    esc_html__('results', 'frameflow')
                );
            }

            $result_count = ob_get_clean();
        }

        // Recalculate already_displayed for response (after getting total)
        $already_displayed = 0;
        if (isset($settings['pagination_type']) && $settings['pagination_type'] == 'loadmore') {
            $already_displayed = ((int)$settings['paged'] - 1) * (int)$settings['limit'];
            // Make sure we don't exceed the total
            if ($already_displayed > $total) {
                $already_displayed = $total;
            }
        }

        wp_send_json(array(
            'status' => true,
            'message' => esc_attr__('Load Successfully!', 'frameflow'),
            'data' => array(
                'html' => $html,
                'pagin_html' => $pagin_html,
                'paged' => $settings['paged'],
                'posts' => $posts,
                'max' => $max,
                'total' => $total,
                'already_displayed' => $already_displayed,
                'result_count' => $result_count,
            ),
        ));
    } catch (Exception $e) {
        wp_send_json(array('status' => false, 'message' => $e->getMessage()));
    }
    die;
}

function frameflow_load_more_team_grid()
{
    try {
        if (!isset($_POST['settings'])) {
            throw new Exception(__('Something went wrong while requesting. Please try again!', 'frameflow'));
        }

        $settings = isset($_POST['settings']) ? $_POST['settings'] : null;

        if (!isset($settings['widget_type']) || $settings['widget_type'] !== 'team_grid') {
            throw new Exception(__('Invalid widget type!', 'frameflow'));
        }

        $team_data = isset($settings['team_data']) ? $settings['team_data'] : [];

        // Handle team_data passed as JSON string (e.g., via AJAX with slashes)
        if (is_string($team_data)) {
            $decoded = json_decode(wp_unslash($team_data), true);
            if (is_array($decoded)) {
                $team_data = $decoded;
            }
        }

        $items_limit_setting = isset($settings['items_limit']) ? intval($settings['items_limit']) : 0;
        if ($items_limit_setting <= 0) {
            $items_limit = count($team_data);
        } else {
            $items_limit = $items_limit_setting;
        }

        // For load more, we need to calculate how many items are already displayed
        $current_page = isset($settings['paged']) ? intval($settings['paged']) : (isset($settings['current_page']) ? intval($settings['current_page']) : 1);
        $current_page = max(1, $current_page);

        // If this is a filter action, always start from page 1
        $handler_click = isset($_POST['handler_click']) ? sanitize_text_field(wp_unslash($_POST['handler_click'])) : '';
        if ($handler_click === 'filter') {
            $current_page = 1;
        }

        // Calculate how many items are already displayed
        $already_displayed = ($current_page - 1) * $items_limit;

        $term_slug = isset($settings['term_slug']) ? $settings['term_slug'] : '';

        // Filter by department if term_slug is provided
        $filtered_team_data = $team_data;
        if (!empty($term_slug) && $term_slug !== '*') {
            $department_slug = ltrim($term_slug, '.');

            $filtered_team_data = array_filter($team_data, function ($item) use ($department_slug) {
                if (empty($item['department'])) {
                    return false;
                }

                $departments = is_array($item['department'])
                    ? $item['department']
                    : preg_split('/[,|;]/', $item['department']);

                if (!is_array($departments)) {
                    $departments = array($item['department']);
                }

                foreach ($departments as $department_label) {
                    if (!is_string($department_label)) {
                        continue;
                    }
                    $label = trim($department_label);
                    if ($label === '') {
                        continue;
                    }
                    $slug = sanitize_title($label);
                    if ($slug === $department_slug) {
                        return true;
                    }
                }

                return false;
            });
        }

        $total_items = count($filtered_team_data);
        $max_pages = ceil($total_items / $items_limit);

        // For load more, get the next batch of items
        $paged_team_data = array_slice($filtered_team_data, $already_displayed, $items_limit);

        // Prepare settings for template
        $template_settings = array(
            'team'        => $paged_team_data,
            'image_size'  => $settings['image_size'] ?? $settings['img_size'] ?? 'full',
            'img_size'    => $settings['img_size'] ?? $settings['image_size'] ?? 'full',
            'pxl_animate' => $settings['pxl_animate'] ?? '',
            'item_style'  => $settings['item_style'] ?? 'style_1',
            'col_xxl'     => $settings['col_xxl'] ?? 4,
            'col_xl'      => $settings['col_xl'] ?? 3,
            'col_lg'      => $settings['col_lg'] ?? 3,
            'col_md'      => $settings['col_md'] ?? 2,
            'col_sm'      => $settings['col_sm'] ?? 2,
            'col_xs'      => $settings['col_xs'] ?? 1,
        );

        ob_start();
        frameflow_get_team_grid_items($paged_team_data, $template_settings);
        $html = ob_get_clean();

        wp_send_json(array(
            'status' => true,
            'message' => esc_attr__('Load Successfully!', 'frameflow'),
            'data' => array(
                'html' => $html,
                'paged' => $current_page,
                'max' => $max_pages,
                'total' => $total_items,
                'already_displayed' => $already_displayed + count($paged_team_data),
            ),
        ));
    } catch (Exception $e) {
        wp_send_json(array('status' => false, 'message' => $e->getMessage()));
    }
    die;
}
