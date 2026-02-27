<?php

/**
 * Actions Hook for the theme
 *
 * @package Case-Themes
 */
add_action('after_setup_theme', 'frameflow_setup');
function frameflow_setup()
{

    //Set the content width in pixels, based on the theme's design and stylesheet.
    $GLOBALS['content_width'] = apply_filters('frameflow_content_width', 1200);

    // Make theme available for translation.
    load_theme_textdomain('frameflow', get_template_directory() . '/languages');

    // Custom Header
    add_theme_support('custom-header');

    // Add default posts and comments RSS feed links to head.
    add_theme_support('automatic-feed-links');

    // Let WordPress manage the document title.
    add_theme_support('title-tag');

    // Enable support for Post Thumbnails on posts and pages.
    add_theme_support('post-thumbnails');

    set_post_thumbnail_size(1170, 710);

    // This theme uses wp_nav_menu() in one location.
    register_nav_menus(array(
        'primary' => esc_html__('Primary', 'frameflow'),
    ));

    // Add theme support for selective refresh for widgets.
    add_theme_support('customize-selective-refresh-widgets');

    // Add support for core custom logo.
    add_theme_support('custom-logo', array(
        'height'      => 250,
        'width'       => 250,
        'flex-width'  => true,
        'flex-height' => true,
    ));
    add_theme_support('post-formats', array(
        'video',
        'audio',
        'quote',
        'link',
    ));

    // Enable support for Post Thumbnails on posts and pages.
    add_theme_support('post-thumbnails');
    add_image_size('frameflow-thumb-small', 80, 70, true);
    add_image_size('frameflow-thumb-xs', 120, 104, true);
    add_image_size('frameflow-large', 952, 333, true);
    add_image_size('frameflow-thumb-related', 767, 444, true);
    add_image_size('frameflow-portfolio', 600, 600, true);

    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
    remove_theme_support('widgets-block-editor');
}

/**
 * Register Widgets Position.
 */
add_action('widgets_init', 'frameflow_widgets_position');
function frameflow_widgets_position()
{
    register_sidebar(array(
        'name'          => esc_html__('Blog Sidebar', 'frameflow'),
        'id'            => 'sidebar-blog',
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title"><span>',
        'after_title'   => '</span></h2><div class="widget-content">',
    ));

    if (class_exists('ReduxFramework')) {
        register_sidebar(array(
            'name'          => esc_html__('Page Sidebar', 'frameflow'),
            'id'            => 'sidebar-page',
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div></section>',
            'before_title'  => '<h2 class="widget-title"><span>',
            'after_title'   => '</span></h2><div class="widget-content">',
        ));
    }

    if (class_exists('Woocommerce')) {
        register_sidebar(array(
            'name'          => esc_html__('Shop Sidebar', 'frameflow'),
            'id'            => 'sidebar-shop',
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title"><span>',
            'after_title'   => '</span></h2><div class="widget-content">',
        ));
    }

    register_sidebar(array(
        'name'          => esc_html__('Event Sidebar', 'frameflow'),
        'id'            => 'sidebar-event',
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title"><span>',
        'after_title'   => '</span></h2><div class="widget-content">',
    ));
}

/**
 * Enqueue Styles Scripts : Front-End
 */
add_action('wp_enqueue_scripts', 'frameflow_scripts');
function frameflow_scripts()
{
    $frameflow_version = wp_get_theme(get_template());
    /* Popup Libs */
    wp_enqueue_style('magnific-popup', get_template_directory_uri() . '/assets/css/libs/magnific-popup.css', array(), '1.1.0');
    wp_enqueue_script('magnific-popup', get_template_directory_uri() . '/assets/js/libs/magnific-popup.min.js', array('jquery'), '1.1.0', true);
    /* Wow Libs */
    wp_enqueue_style('wow-animate', get_template_directory_uri() . '/assets/css/libs/animate.min.css', array(), '1.1.0');
    wp_enqueue_script('wow-animate', get_template_directory_uri() . '/assets/js/libs/wow.min.js', array('jquery'), '1.0.0', true);

    /* Parallax Libs */
    wp_register_script('stellar-parallax', get_template_directory_uri() . '/assets/js/libs/stellar-parallax.min.js', array('jquery'), '0.6.2', true);

    /* Nice Select */
    wp_enqueue_script('nice-select', get_template_directory_uri() . '/assets/js/libs/nice-select.min.js', array('jquery'), 'all', true);

    /* Divider Move on Menu */
    wp_enqueue_script('modernizr', get_template_directory_uri() . '/assets/js/libs/modernizr.min.js', array('jquery'), 'all', true);

    /* Counter Effect */
    wp_register_script('pxl-counter-slide', get_template_directory_uri() . '/assets/js/libs/counter-slide.min.js', array('jquery'), '1.0.0', true);

    /* Scroll Effect */
    wp_register_script('pxl-scroll', get_template_directory_uri() . '/assets/js/libs/scroll.min.js', array('jquery'), '0.6.0', true);

    /* Parallax Scroll */
    wp_enqueue_script('pxl-parallax-background', get_template_directory_uri() . '/assets/js/libs/parallax-background.js', array('jquery'), $frameflow_version->get('Version'), true);
    wp_enqueue_script('pxl-parallax-scroll', get_template_directory_uri() . '/assets/js/libs/parallax-scroll.js', array('jquery'), $frameflow_version->get('Version'), true);
    wp_register_script('pxl-easing', get_template_directory_uri() . '/assets/js/libs/easing.js', array('jquery'), '1.3.0', true);

    /* Tweenmax */
    wp_register_script('pxl-tweenmax', get_template_directory_uri() . '/assets/js/libs/tweenmax.min.js', array('jquery'), '2.1.2', true);

    /* Parallax Move Mouse */
    wp_register_script('pxl-parallax-move-mouse', get_template_directory_uri() . '/assets/js/libs/parallax-move-mouse.js', array('jquery'), '1.0.0', true);

    /* Particles Background Libs */
    wp_register_script('particles-background', get_template_directory_uri() . '/assets/js/libs/particles.min.js', array('jquery'), '1.1.0', true);


    /* Woocommerce - chỉ load trên trang WooCommerce */
    if (class_exists('WooCommerce')) {
        wp_enqueue_style('pxl-dual-cart', get_template_directory_uri() . '/assets/css/pxl-dual-cart.css', array(), $frameflow_version->get('Version'));
        if (is_cart()) {
            wp_enqueue_script('pxl-dual-cart', get_template_directory_uri() . '/assets/js/pxl-dual-cart.js', array('jquery'), $frameflow_version->get('Version'), true);
        }
        if (function_exists('is_woocommerce') && (is_woocommerce() || is_cart() || is_checkout() || is_account_page())) {
            wp_enqueue_script('pxl-woocommerce', get_template_directory_uri() . '/woocommerce/js/woocommerce.js', array('jquery'), $frameflow_version->get('Version'), true);
        }
    }

    /* Icon */
    wp_enqueue_style('bootstrap-icons', get_template_directory_uri() . '/assets/fonts/bootstrap-icons/css/bootstrap-icons.css');
    /* Cookie */
    wp_register_script('pxl-cookie', get_template_directory_uri() . '/assets/js/libs/cookie.js', array('jquery'), '1.4.1', true);
    /* Leaflet map - chỉ load khi cần map (trang Contact / trang dùng template contact) */
    if (is_page() && (is_page('contact') || is_page_template('template-contact.php'))) {
        wp_enqueue_script('pxl-leaflet', get_template_directory_uri() . '/assets/js/libs/leaflet.js', array('jquery'), '1.4.1', true);
    }

    /* smooth scroll */
    $smooth_scroll = frameflow()->get_theme_opt('smooth_scroll', 'off');
    if ($smooth_scroll == 'on') {
        wp_enqueue_script('gsap');
        wp_enqueue_script('pxl-scroll-trigger');
        wp_enqueue_script('pxl-bundled-lenis');
    }

    /* Smooth Scroll */
    wp_enqueue_script('pxl-jarallax', get_template_directory_uri() . '/assets/js/libs/jarallax.min.js', ['jquery'], '2.2.1');
    wp_enqueue_style('pxl-grid', get_template_directory_uri() . '/assets/css/grid.css', array(), $frameflow_version->get('Version'));
    wp_enqueue_style('pxl-style', get_template_directory_uri() . '/assets/css/style.css', array(), $frameflow_version->get('Version'));
    // Enqueued via style.css SCSS
    wp_add_inline_style('pxl-style', frameflow_inline_styles());
    wp_enqueue_style('pxl-base', get_template_directory_uri() . '/style.css', array(), $frameflow_version->get('Version'));
    wp_enqueue_style('pxl-google-fonts', frameflow_fonts_url(), array(), null);
    wp_enqueue_script('pxl-main', get_template_directory_uri() . '/assets/js/theme.js', array('jquery'), $frameflow_version->get('Version'), true);
    wp_localize_script('pxl-main', 'main_data', array('ajax_url' => admin_url('admin-ajax.php')));
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
    do_action('frameflow_scripts');
}

/**
 * Enqueue Styles Scripts : Back-End
 */
add_action('admin_enqueue_scripts', 'frameflow_admin_style');
function frameflow_admin_style()
{
    $theme = wp_get_theme(get_template());
    wp_enqueue_style('frameflow-admin-style', get_template_directory_uri() . '/assets/css/admin.css', array(), $theme->get('Version'));
    wp_enqueue_style('bootstrap-icons', get_template_directory_uri() . '/assets/fonts/bootstrap-icons/css/bootstrap-icons.css');
}

add_action('elementor/editor/before_enqueue_scripts', function () {
    wp_enqueue_style('frameflow-admin-style', get_template_directory_uri() . '/assets/css/admin.css');
    wp_enqueue_style('admin-bootstrap-icons', get_template_directory_uri() . '/assets/fonts/bootstrap-icons/css/bootstrap-icons.css');
});

/* Favicon */
add_action('wp_head', 'frameflow_site_favicon');
function frameflow_site_favicon()
{
    $favicon = frameflow()->get_theme_opt('favicon');
    if (!empty($favicon['url'])) {
        $favicon_sm  = pxl_get_image_by_size(array(
            'attach_id'  => $favicon['id'],
            'thumb_size' => '32x32',
        ));
        $favicon_sm_url    = $favicon_sm['url'];

        $favicon_xs  = pxl_get_image_by_size(array(
            'attach_id'  => $favicon['id'],
            'thumb_size' => '16x16',
        ));
        $favicon_xs_url    = $favicon_xs['url'];

        echo '<link rel="icon" type="image/png" sizes="32x32" href="' . esc_url($favicon_sm_url) . '"/>';
        echo '<link rel="icon" type="image/png" sizes="16x16" href="' . esc_url($favicon_xs_url) . '"/>';
    }
}

/**
 * Add a pingback url auto-discovery header for singularly identifiable articles.
 */
add_action('wp_head', 'frameflow_pingback_header');
function frameflow_pingback_header()
{
    if (is_singular() && pings_open()) {
        echo '<link rel="pingback" href="', esc_url(get_bloginfo('pingback_url')), '">';
    }
}

/* Hidden Panel */
add_action('pxl_anchor_target', 'frameflow_hook_anchor_templates_hidden_panel');
function frameflow_hook_anchor_templates_hidden_panel()
{

    $hidden_templates = frameflow_get_templates_slug('hidden-panel');
    if (empty($hidden_templates)) return;

    foreach ($hidden_templates as $slug => $values) {
        $args = [
            'slug' => $slug,
            'post_id' => $values['post_id']
        ];
        if (did_action('pxl_anchor_target_hidden_panel_' . $values['post_id']) <= 0) {
            do_action('pxl_anchor_target_hidden_panel_' . $values['post_id'], $args);
        }
    }
}
if (!function_exists('frameflow_hook_anchor_hidden_panel')) {
    function frameflow_hook_anchor_hidden_panel($args)
    {
        $hidden_panel_position = get_post_meta($args['post_id'], 'hidden_panel_position', true);
        $hidden_panel_boxcolor = get_post_meta($args['post_id'], 'hidden_panel_boxcolor', true);
        $hidden_panel_height = get_post_meta($args['post_id'], 'hidden_panel_height', true); ?>
        <div class="pxl-hidden-panel-popup pxl-hidden-template-<?php echo esc_attr($args['post_id']) ?> pxl-pos-<?php echo esc_attr($hidden_panel_position); ?>">
            <div class="pxl-popup--overlay pxl-cursor--cta"></div>
            <div class="pxl-popup--conent" style="height:<?php echo esc_attr($hidden_panel_height) . 'px'; ?>; background-color:<?php echo esc_attr($hidden_panel_boxcolor); ?>;">
                <div class="pxl-conent-elementor">
                    <?php echo Elementor\Plugin::$instance->frontend->get_builder_content_for_display((int)$args['post_id']); ?>
                </div>
            </div>
        </div>
    <?php }
}

/* Elementor Popup */
add_action('pxl_anchor_target', 'frameflow_hook_anchor_templates_popup');
function frameflow_hook_anchor_templates_popup()
{

    $popup_templates = frameflow_get_templates_slug('popup');
    if (empty($popup_templates)) return;

    foreach ($popup_templates as $slug => $values) {
        $args = [
            'slug' => $slug,
            'post_id' => $values['post_id']
        ];
        if (did_action('pxl_anchor_target_popup_' . $values['post_id']) <= 0) {
            do_action('pxl_anchor_target_popup_' . $values['post_id'], $args);
        }
    }
}
/* Search Popup */
if (!function_exists('frameflow_hook_anchor_search')) {
    function frameflow_hook_anchor_search()
    {
        $logo_s_p = frameflow()->get_theme_opt('logo_s_p', ['url' => get_template_directory_uri() . '/assets/img/logo.png', 'id' => '']);
        $placeholder_search_pu = frameflow()->get_theme_opt('placeholder_search_pu', 'Search...');
    ?>
        <div id="pxl-search-popup">
            <div class="pxl-item--overlay"></div>
            <div class="pxl-item--logo">
                <?php
                printf(
                    '<a href="%1$s" title="%2$s" rel="home">
                    <img src="%3$s" alt="%2$s" class="logo-light"/>
                    </a>',
                    esc_url(home_url('/')),
                    esc_attr(get_bloginfo('name')),
                    esc_url($logo_s_p['url'])
                );
                ?>
            </div>
            <div class="pxl-item--conent">
                <div class="pxl-item--close pxl-close"></div>
                <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                    <input type="text" required placeholder="<?php echo esc_attr($placeholder_search_pu); ?>" name="s" class="search-field" />
                    <button type="submit" class="search-submit rm-style-default"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <g clip-path="url(#clip0_243_485)">
                                <path d="M10.5 18C14.6421 18 18 14.6421 18 10.5C18 6.35786 14.6421 3 10.5 3C6.35786 3 3 6.35786 3 10.5C3 14.6421 6.35786 18 10.5 18Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M15.8047 15.8037L21.0012 21.0003" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </g>
                            <defs>
                                <clipPath id="clip0_243_485">
                                    <rect width="24" height="24" fill="black" />
                                </clipPath>
                            </defs>
                        </svg></button>
                    <div class="pxl--search-divider"></div>
                </form>
            </div>
        </div>
    <?php }
}
if (!function_exists('frameflow_hook_anchor_popup')) {
    function frameflow_hook_anchor_popup($args)
    { ?>
        <div id="pxl-popup-elementor" class="pxl-popup-elementor-wrap">
            <div class="pxl-item--overlay pxl-cursor--cta">
                <div class="pxl-item--flip pxl-item--flip1"></div>
                <div class="pxl-item--flip pxl-item--flip2"></div>
                <div class="pxl-item--flip pxl-item--flip3"></div>
                <div class="pxl-item--flip pxl-item--flip4"></div>
                <div class="pxl-item--flip pxl-item--flip5"></div>
            </div>
            <div class="pxl-item--close pxl-close pxl-cursor--cta"></div>
            <div class="pxl-item--conent">
                <div class="pxl-conent-elementor">
                    <?php echo Elementor\Plugin::$instance->frontend->get_builder_content_for_display((int)$args['post_id']); ?>
                </div>
            </div>
        </div>
    <?php }
}

/* Page Popup */
add_action('pxl_anchor_target', 'frameflow_hook_anchor_templates_page_popup');
function frameflow_hook_anchor_templates_page_popup()
{

    $page_templates = frameflow_get_templates_slug('popup');
    if (empty($page_templates)) return;

    foreach ($page_templates as $slug => $values) {
        $args = [
            'slug' => $slug,
            'post_id' => $values['post_id']
        ];
        if (did_action('pxl_anchor_target_page_popup_' . $values['post_id']) <= 0) {
            do_action('pxl_anchor_target_page_popup_' . $values['post_id'], $args);
        }
    }
}
if (!function_exists('frameflow_hook_anchor_page_popup')) {
    function frameflow_hook_anchor_page_popup($args)
    { ?>
        <div class="pxl-page-popup pxl-page-popup-template-<?php echo esc_attr($args['post_id']) ?>">
            <div class="pxl-close-popup  pxl-cursor--cta ">x</div>
            <div class="pxl-popup--conent">
                <div class="pxl-conent-elementor">
                    <?php
                    $content_page = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display((int)$args['post_id']);
                    pxl_print_html($content_page);
                    ?>
                </div>
            </div>
        </div>
    <?php }
}

/* Cart Sidebar */
if (!function_exists('frameflow_hook_anchor_cart')) {
    function frameflow_hook_anchor_cart()
    {
        global $woocommerce; ?>
        <div id="pxl-cart-sidebar" class="pxl-popup-wrap">
            <div class="pxl-popup--overlay pxl-cursor--cta"></div>
            <div class="pxl-popup--conent pxl-widget-cart-sidebar">
                <div class="widget_shopping_cart">
                    <div class="widget_shopping_head">
                        <div class="pxl-item--close pxl-close pxl-cursor--cta"></div>
                        <div class="widget_shopping_title">
                            <?php echo esc_html__('Cart', 'frameflow'); ?> <span class="widget_cart_counter">(<?php echo sprintf(_n('%d item', '%d items', WC()->cart->cart_contents_count, 'frameflow'), WC()->cart->cart_contents_count); ?>)</span>
                        </div>
                    </div>
                    <div class="widget_shopping_cart_content">
                        <?php 
                        if (function_exists('frameflow_render_mini_cart_grouped_items')) {
                            frameflow_render_mini_cart_grouped_items();
                        } else {
                            // Fallback if the function is not available
                            wc_get_template('cart/mini-cart.php');
                        }
                        ?>
                    </div>
                    <?php if (!WC()->cart->is_empty()) : ?>
                        <div class="widget_shopping_cart_footer">
                            <p class="total"><strong><?php esc_html_e('Total', 'frameflow'); ?>:</strong> <?php echo WC()->cart->get_cart_subtotal(); ?></p>
                            <?php do_action('woocommerce_widget_shopping_cart_before_buttons'); ?>
                            <p class="buttons">
                                <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="btn btn-shop wc-forward btn-2-icons"><?php esc_html_e('View Cart', 'frameflow'); ?><span class="btn-icon-left">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="13" viewBox="0 0 16 13" fill="none"><path d="M9.6 12.7999C9.39526 12.7999 9.19053 12.7219 9.03432 12.5657C8.7219 12.2532 8.7219 11.7467 9.03432 11.4343L13.2686 7.19999H0.800009C0.358159 7.19999 0 6.8418 0 6.39998C0 5.95813 0.358159 5.59997 0.800009 5.59997H13.2686L9.03432 1.36567C8.7219 1.05326 8.7219 0.546725 9.03432 0.234311C9.3467 -0.0781035 9.8533 -0.0781035 10.1657 0.234311L15.7657 5.8343L15.7674 5.83604C15.7677 5.83632 15.768 5.83667 15.7683 5.83695C15.7686 5.83723 15.7688 5.83751 15.7691 5.83778C15.7695 5.8382 15.7699 5.83862 15.7703 5.83904C15.7705 5.83918 15.7706 5.83932 15.7708 5.83949C15.7713 5.84005 15.7718 5.84057 15.7724 5.84109L15.7724 5.84116C15.8444 5.91483 15.8992 5.9989 15.937 6.08847C15.9371 6.08872 15.9372 6.089 15.9373 6.08924C15.9374 6.08952 15.9376 6.08983 15.9377 6.09011C15.9778 6.18543 15.9999 6.29015 15.9999 6.40002C15.9999 6.50989 15.9778 6.61461 15.9377 6.70993C15.9376 6.71017 15.9374 6.71052 15.9373 6.7108C15.9372 6.71104 15.9371 6.71128 15.937 6.71153C15.8992 6.80114 15.8444 6.88521 15.7724 6.95888L15.7724 6.95891C15.7718 6.95947 15.7713 6.95999 15.7708 6.96051C15.7707 6.96065 15.7705 6.96079 15.7703 6.96096C15.7699 6.96142 15.7695 6.9618 15.7691 6.96225C15.7688 6.9625 15.7686 6.96281 15.7683 6.96305C15.768 6.96333 15.7677 6.96368 15.7674 6.96396C15.7668 6.96455 15.7662 6.96514 15.7657 6.9657L10.1657 12.5657C10.0095 12.7219 9.80474 12.7999 9.6 12.7999Z" fill="#1A1A1A"></path></svg>            </span></a>
                                <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="btn checkout wc-forward btn-2-icons"><?php esc_html_e('Checkout', 'frameflow'); ?><span class="btn-icon-left">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="13" viewBox="0 0 16 13" fill="none"><path d="M9.6 12.7999C9.39526 12.7999 9.19053 12.7219 9.03432 12.5657C8.7219 12.2532 8.7219 11.7467 9.03432 11.4343L13.2686 7.19999H0.800009C0.358159 7.19999 0 6.8418 0 6.39998C0 5.95813 0.358159 5.59997 0.800009 5.59997H13.2686L9.03432 1.36567C8.7219 1.05326 8.7219 0.546725 9.03432 0.234311C9.3467 -0.0781035 9.8533 -0.0781035 10.1657 0.234311L15.7657 5.8343L15.7674 5.83604C15.7677 5.83632 15.768 5.83667 15.7683 5.83695C15.7686 5.83723 15.7688 5.83751 15.7691 5.83778C15.7695 5.8382 15.7699 5.83862 15.7703 5.83904C15.7705 5.83918 15.7706 5.83932 15.7708 5.83949C15.7713 5.84005 15.7718 5.84057 15.7724 5.84109L15.7724 5.84116C15.8444 5.91483 15.8992 5.9989 15.937 6.08847C15.9371 6.08872 15.9372 6.089 15.9373 6.08924C15.9374 6.08952 15.9376 6.08983 15.9377 6.09011C15.9778 6.18543 15.9999 6.29015 15.9999 6.40002C15.9999 6.50989 15.9778 6.61461 15.9377 6.70993C15.9376 6.71017 15.9374 6.71052 15.9373 6.7108C15.9372 6.71104 15.9371 6.71128 15.937 6.71153C15.8992 6.80114 15.8444 6.88521 15.7724 6.95888L15.7724 6.95891C15.7718 6.95947 15.7713 6.95999 15.7708 6.96051C15.7707 6.96065 15.7705 6.96079 15.7703 6.96096C15.7699 6.96142 15.7695 6.9618 15.7691 6.96225C15.7688 6.9625 15.7686 6.96281 15.7683 6.96305C15.768 6.96333 15.7677 6.96368 15.7674 6.96396C15.7668 6.96455 15.7662 6.96514 15.7657 6.9657L10.1657 12.5657C10.0095 12.7219 9.80474 12.7999 9.6 12.7999Z" fill="#1A1A1A"></path></svg>            </span></a>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php }
}

/**
 * Defer non-critical scripts
 */
add_filter('script_loader_tag', 'frameflow_defer_scripts', 10, 2);
function frameflow_defer_scripts($tag, $handle) {
    $defer_handles = array('wow-animate', 'tilt', 'stellar-parallax', 'nice-select', 'pxl-counter-slide', 'slick-lib');
    if (in_array($handle, $defer_handles)) {
        return str_replace(' src', ' defer="defer" src', $tag);
    }
    return $tag;
}

/** Show Cart Sidebar Hidden */
add_action('wp_ajax_nopriv_item_added', 'frameflow_addedtocart_sweet_message');
add_action('wp_ajax_item_added', 'frameflow_addedtocart_sweet_message');
function frameflow_addedtocart_sweet_message() {
    echo isset($_POST['id']) && $_POST['id'] > 0 ? (int) esc_attr($_POST['id']) : false;
    die();
}
add_filter('woocommerce_add_to_cart_fragments', function($fragments) {
    ob_start();
    ?>
    <span class="pxl_cart_counter"><?php echo WC()->cart->cart_contents_count; ?></span>
    <?php
    $fragments['span.pxl_cart_counter'] = ob_get_clean();
    return $fragments;
});
add_action('wp_footer', 'frameflow_cart_hidden_sidebar');
function frameflow_cart_hidden_sidebar() {
    if (class_exists('Woocommerce') && is_checkout()) {
        return;
    }
    ?>
    <script type="text/javascript">
        jQuery( function($) {
            if ( typeof wc_add_to_cart_params === 'undefined' ) {
                return false;
            }

            $(document.body).on( 'added_to_cart', function( event, fragments, cart_hash, $button ) {
                var $pid = $button.data('product_id');

                $.ajax({
                    type: 'POST',
                    url: wc_add_to_cart_params.ajax_url,
                    data: {
                        'action': 'item_added',
                        'id'    : $pid
                    },
                    success: function (response) {
                        $('#pxl-cart-sidebar').addClass('active');
                        $("body").addClass('body-overflow');

                        if (fragments && fragments['span.pxl_cart_counter']) {
                            $('.pxl_cart_counter').html($(fragments['span.pxl_cart_counter']).html());
                        }

                        $("#pxl-cart-sidebar .pxl-item--close").on('click', function () {
                            $('body').removeClass('body-overflow');
                            $('#pxl-cart-sidebar').removeClass('active');
                        });
                    }
                });
            });
        });
    </script>
    <?php
}