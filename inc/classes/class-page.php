<?php

if (!class_exists('Frameflow_Page')) {

    class Frameflow_Page
    {
        public function get_site_loader()
        {

            $loader_style = frameflow()->get_opt('loader_style', 'style-text');
            $loading_text = frameflow()->get_opt('loading_text', '');
            $site_loader = frameflow()->get_opt('site_loader', false);
            $loader_logo = frameflow()->get_opt('loader_logo');
            $loader_logo_height = frameflow()->get_opt('loader_logo_height');
            $percentage_intro = frameflow()->get_opt('percentage_intro', '');
            if ($site_loader) { ?>
                <?php switch ($loader_style) {
                    case 'style-logo': ?>
                        <div id="pxl-loadding" class="pxl-loader">
                            <div class="loader-circle">
                                <div class="loader-line-mask">
                                    <div class="loader-line"></div>
                                </div>
                                <div class="loader-logo"><img src="<?php echo esc_url($loader_logo['url']); ?>" /></div>
                            </div>
                        </div>
                    <?php break;
                    case 'style-text': ?>
                        <div class="preloader-wrap" data-secondline="Relaxed" data-firstline="Stay">
                            <div class="outer">
                                <div class="inner">
                                    <div class="trackbar">
                                        <div class="preloader-intro">
                                            <span><?php echo esc_html($loading_text); ?></span>
                                        </div>
                                        <div class="loadbar"></div>
                                        <div class="percentage-wrapper">
                                            <div class="percentage" id="precent">0</div>
                                        </div>
                                    </div>
                                    <div class="percentage-intro"><?php echo esc_html($percentage_intro); ?></div>
                                </div>
                            </div>
                        </div>
                <?php break;
                } ?>
            <?php }
        }

        public function get_link_pages()
        {
            wp_link_pages(array(
                'before' => '<div class="page-links">',
                'after' => '</div>',
                'link_before' => '<span>',
                'link_after' => '</span>',
            ));
        }

        public function get_page_title()
        {
            $titles = $this->get_title();
            $pt_mode = frameflow()->get_theme_opt('pt_mode');
            $ptitle_layout = frameflow()->get_theme_opt('ptitle_layout');
            $ptitle_scroll_opacity = frameflow()->get_opt('ptitle_scroll_opacity');
            $custom_main_title = frameflow()->get_opt('custom_main_title');

            if (is_singular('event')) {
                $event_pt_mode = frameflow()->get_theme_opt('event_pt_mode', '-1');
                if ($event_pt_mode != '-1') {
                    $pt_mode = $event_pt_mode;
                }
                $event_ptitle_layout = frameflow()->get_theme_opt('event_ptitle_layout', '-1');
                if ($event_ptitle_layout != '-1') {
                    $ptitle_layout = $event_ptitle_layout;
                }
            }

            $pt_mode_page = frameflow()->get_page_opt('pt_mode', '-1');
            if ($pt_mode_page != '-1') {
                $pt_mode = $pt_mode_page;
            }
            $ptitle_layout_page = frameflow()->get_page_opt('ptitle_layout', '-1');
            if ($ptitle_layout_page != '-1') {
                $ptitle_layout = $ptitle_layout_page;
            }
            $ptitle_layout = (int)$ptitle_layout;

            if ($pt_mode == 'none') return;
            if ($pt_mode == 'bd' && $ptitle_layout > 0 && class_exists('Pxltheme_Core') && is_callable('Elementor\Plugin::instance')) {
                $previous_flag = isset($GLOBALS['frameflow_rendering_page_title']) ? $GLOBALS['frameflow_rendering_page_title'] : false;
                $GLOBALS['frameflow_rendering_page_title'] = true;
            ?>
                <div id="pxl-page-title-elementor" class="<?php if ($ptitle_scroll_opacity == true) {
                                                                echo 'pxl-scroll-opacity';
                                                            } ?>">
                    <?php echo Elementor\Plugin::$instance->frontend->get_builder_content_for_display($ptitle_layout); ?>
                </div>
            <?php
                $GLOBALS['frameflow_rendering_page_title'] = $previous_flag;
            } else {
                $ptitle_breadcrumb_on = frameflow()->get_opt('ptitle_breadcrumb_on', '1');
                wp_enqueue_script('stellar-parallax'); ?>
                <div id="pxl-page-title-default" class="pxl--parallax" data-stellar-background-ratio="0.79">
                    <div class="container">
                        <h2 class="pxl-page-title"><?php if (!empty($custom_main_title)) {
                                                        echo wp_kses_post($custom_main_title);
                                                    } else {
                                                        echo wp_kses_post($titles['title']);
                                                    } ?></h2>
                    </div>
                    <div class="ptitle-col-right col-12">
                        <?php if ($ptitle_breadcrumb_on == '1') : ?>
                            <?php $this->get_breadcrumb(); ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php }
        }

        public function get_title()
        {
            $title = '';
            // Default titles
            if (! is_archive()) {
                // Posts page view
                if (is_home()) {
                    // Only available if posts page is set.
                    if (! is_front_page() && $page_for_posts = get_option('page_for_posts')) {
                        $title = get_post_meta($page_for_posts, 'custom_title', true);
                        if (empty($title)) {
                            $title = get_the_title($page_for_posts);
                        }
                    }
                    if (is_front_page()) {
                        $title = esc_html__('Blog', 'frameflow');
                    }
                } // Single page view
                elseif (is_page()) {
                    $title = get_post_meta(get_the_ID(), 'custom_title', true);
                    if (! $title) {
                        $title = get_the_title();
                    }
                } elseif (is_404()) {
                    $title = esc_html__('404 Error', 'frameflow');
                } elseif (is_search()) {
                    $title = esc_html__('Search results', 'frameflow');
                } elseif (is_singular('lp_course')) {
                    $title = esc_html__('Course', 'frameflow');
                } else {
                    $title = get_post_meta(get_the_ID(), 'custom_title', true);
                    if (! $title) {
                        $title = get_the_title();
                    }
                }
            } else {
                $title = get_the_archive_title();
                if ((class_exists('WooCommerce') && is_shop())) {
                    $title = get_post_meta(wc_get_page_id('shop'), 'custom_title', true);
                    if (!$title) {
                        $title = get_the_title(get_option('woocommerce_shop_page_id'));
                    }
                }
            }

            return array(
                'title' => $title,
            );
        }

        public function get_breadcrumb()
        {

            if (! class_exists('CASE_Breadcrumb')) {
                return;
            }

            $breadcrumb = new CASE_Breadcrumb();
            $entries = $breadcrumb->get_entries();

            if (empty($entries)) {
                return;
            }

            ob_start();

            foreach ($entries as $entry) {
                $entry = wp_parse_args($entry, array(
                    'label' => '',
                    'url'   => ''
                ));

                $entry_label = $entry['label'];

                if (!empty($_GET['blog_title'])) {
                    $blog_title = $_GET['blog_title'];
                    $custom_title = explode('_', $blog_title);
                    foreach ($custom_title as $index => $value) {
                        $arr_str_b[$index] = $value;
                    }
                    $str = implode(' ', $arr_str_b);
                    $entry_label = $str;
                }

                if (empty($entry_label)) {
                    continue;
                }

                echo '<li>';

                if (! empty($entry['url'])) {
                    printf(
                        '<a class="breadcrumb-hidden" href="%1$s">%2$s</a>',
                        esc_url($entry['url']),
                        esc_attr($entry_label)
                    );
                } else {
                    $sg_post_title = frameflow()->get_theme_opt('sg_post_title', 'default');
                    $sg_post_title_text = frameflow()->get_theme_opt('sg_post_title_text');
                    if (is_singular('post') && $sg_post_title == 'custom_text' && !empty($sg_post_title_text)) {
                        $entry_label = $sg_post_title_text;
                    }
                    $sg_product_ptitle = frameflow()->get_theme_opt('sg_product_ptitle', 'default');
                    $sg_product_ptitle_text = frameflow()->get_theme_opt('sg_product_ptitle_text');
                    if (is_singular('product') && $sg_product_ptitle == 'custom_text' && !empty($sg_product_ptitle_text)) {
                        $entry_label = $sg_product_ptitle_text;
                    }
                    printf('<span class="breadcrumb-entry" >%s</span>', esc_html($entry_label));
                }

                echo '</li>';
            }

            $output = ob_get_clean();

            if ($output) {
                printf('<ul class="pxl-breadcrumb">%s</ul>', wp_kses_post($output));
            }
        }

        public function get_pagination($query = null, $ajax = false)
        {

            if ($ajax) {
                add_filter('paginate_links', 'frameflow_ajax_paginate_links');
            }

            $classes = array();

            if (empty($query)) {
                $query = $GLOBALS['wp_query'];
            }

            if (empty($query->max_num_pages) || ! is_numeric($query->max_num_pages) || $query->max_num_pages < 2) {
                return;
            }

            $paged = $query->get('paged', '');

            if (! $paged && is_front_page() && ! is_home()) {
                $paged = $query->get('page', '');
            }

            $paged = $paged ? intval($paged) : 1;

            $pagenum_link = html_entity_decode(get_pagenum_link());
            $query_args   = array();
            $url_parts    = explode('?', $pagenum_link);

            if (isset($url_parts[1])) {
                wp_parse_str($url_parts[1], $query_args);
            }

            $pagenum_link = remove_query_arg(array_keys($query_args), $pagenum_link);
            $pagenum_link = trailingslashit($pagenum_link) . '%_%';
            $paginate_links_args = array(
                'base'     => $pagenum_link,
                'total'    => $query->max_num_pages,
                'current'  => $paged,
                'mid_size' => 1,
                'add_args' => array_map('urlencode', $query_args),
                'prev_text' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12.2197 3.96967C12.5125 3.67678 12.9874 3.67678 13.2803 3.96967L17.7803 8.46968C18.0732 8.76255 18.0732 9.23745 17.7803 9.53033L13.2803 14.0303C12.9874 14.3232 12.5125 14.3232 12.2197 14.0303C11.9268 13.7375 11.9268 13.2626 12.2197 12.9697L16.1893 9L12.2197 5.03033C11.9268 4.73743 11.9268 4.26257 12.2197 3.96967Z" fill="#1A1A1A"/>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M0 9C0 8.58578 0.335786 8.25 0.75 8.25H17.25C17.6642 8.25 18 8.58578 18 9C18 9.41422 17.6642 9.75 17.25 9.75H0.75C0.335786 9.75 0 9.41422 0 9Z" fill="#1A1A1A"/>
                </svg>',
                'next_text' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12.2197 3.96967C12.5125 3.67678 12.9874 3.67678 13.2803 3.96967L17.7803 8.46968C18.0732 8.76255 18.0732 9.23745 17.7803 9.53033L13.2803 14.0303C12.9874 14.3232 12.5125 14.3232 12.2197 14.0303C11.9268 13.7375 11.9268 13.2626 12.2197 12.9697L16.1893 9L12.2197 5.03033C11.9268 4.73743 11.9268 4.26257 12.2197 3.96967Z" fill="#1A1A1A"/>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M0 9C0 8.58578 0.335786 8.25 0.75 8.25H17.25C17.6642 8.25 18 8.58578 18 9C18 9.41422 17.6642 9.75 17.25 9.75H0.75C0.335786 9.75 0 9.41422 0 9Z" fill="#1A1A1A"/>
                </svg>',
            );
            if ($ajax) {
                $paginate_links_args['format'] = '?page=%#%';
            }
            $links = paginate_links($paginate_links_args);
            if ($links):
            ?>
                <nav class="pxl-pagination-wrap <?php echo esc_attr($ajax ? 'ajax' : ''); ?>">
                    <div class="pxl-pagination-links">
                        <?php
                        echo '' . $links;
                        ?>
                    </div>
                </nav>
<?php
            endif;
        }
    }
}
