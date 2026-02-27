<?php

/**
 * Filters hook for the theme
 *
 * @package Case-Themes
 */

/* Custom Classs - Body */
function frameflow_body_classes($classes)
{

	$classes[] = '';
	if (class_exists('ReduxFramework')) {
		$classes[] = ' pxl-redux-page';

		$footer_fixed = frameflow()->get_theme_opt('footer_fixed');
		$p_footer_fixed = frameflow()->get_page_opt('p_footer_fixed');

		if ($p_footer_fixed != false && $p_footer_fixed != 'inherit') {
			$footer_fixed = $p_footer_fixed;
		}

		if (isset($footer_fixed) && $footer_fixed == 'on') {
			$classes[] = ' pxl-footer-fixed';
		}

		$pxl_body_typography = frameflow()->get_theme_opt('pxl_body_typography');
		if ($pxl_body_typography != 'google-font') {
			$classes[] = ' body-' . $pxl_body_typography . ' ';
		}

		$pxl_heading_typography = frameflow()->get_theme_opt('pxl_heading_typography');
		if ($pxl_heading_typography != 'google-font') {
			$classes[] = ' heading-' . $pxl_heading_typography . ' ';
		}

		$theme_default = frameflow()->get_theme_opt('theme_default');
		if (isset($theme_default['font-family']) && $theme_default['font-family'] == false && $pxl_body_typography == 'google-font') {
			$classes[] = ' pxl-font-default';
		}

		$header_layout = frameflow()->get_opt('header_layout');
		if (isset($header_layout) && $header_layout) {
			$post_header = get_post($header_layout);
			$header_type = get_post_meta($post_header->ID, 'header_type', true);
			if (isset($header_type)) {
				$classes[] = ' bd-' . $header_type . '';
			}
		}

		// $get_gradient_color = frameflow()->get_opt('gradient_color');
		// if($get_gradient_color['from'] == $get_gradient_color['to'] ) {
		//     $classes[] = ' site-color-normal ';
		// } else {
		// 	$classes[] = ' site-color-gradient ';
		// }

		$shop_layout = frameflow()->get_theme_opt('shop_layout', 'grid');
		if (isset($_GET['shop-layout'])) {
			$shop_layout = $_GET['shop-layout'];
		}
		$classes[] = ' woocommerce-layout-' . $shop_layout;

		$body_custom_class = frameflow()->get_page_opt('body_custom_class');
		if (!empty($body_custom_class)) {
			$classes[] = $body_custom_class;
		}
	}
	return $classes;
}
add_filter('body_class', 'frameflow_body_classes');

/* Post Type Support */
function frameflow_add_cpt_support()
{
	$cpt_support = get_option('elementor_cpt_support');

	if (! $cpt_support) {
		$cpt_support = ['page', 'post', 'portfolio', 'footer', 'pxl-template'];
		update_option('elementor_cpt_support', $cpt_support);
	} else if (! in_array('portfolio', $cpt_support)) {
		$cpt_support[] = 'portfolio';
		update_option('elementor_cpt_support', $cpt_support);
	} else if (! in_array('footer', $cpt_support)) {
		$cpt_support[] = 'footer';
		update_option('elementor_cpt_support', $cpt_support);
	} else if (! in_array('pxl-template', $cpt_support)) {
		$cpt_support[] = 'pxl-template';
		update_option('elementor_cpt_support', $cpt_support);
	}
}
add_action('after_switch_theme', 'frameflow_add_cpt_support');

add_filter('pxl_support_default_cpt', 'frameflow_support_default_cpt');
function frameflow_support_default_cpt($postypes)
{
	return $postypes; // pxl-template
}

add_filter('pxl_extra_post_types', 'frameflow_add_post_type');
function frameflow_add_post_type($postypes)
{
	$theme_options = get_option(frameflow()->get_option_name(), []);

	$portfolio_display = frameflow()->get_theme_opt('portfolio_display', true);
	$portfolio_slug = !empty($theme_options['portfolio_slug']) ? $theme_options['portfolio_slug'] : 'portfolio';
	$portfolio_name = !empty($theme_options['portfolio_name']) ? $theme_options['portfolio_name'] : esc_html__('Portfolio', 'frameflow');

	if ($portfolio_display) {
		$portfolio_status = true;
	} else {
		$portfolio_status = false;
	}

	$postypes['portfolio'] = array(
		'status' => $portfolio_status,
		'item_name'  => $portfolio_name,
		'items_name' => $portfolio_name,
		'args'       => array(
			'rewrite'             => array(
				'slug'       => $portfolio_slug,
			),
		),
	);

	return $postypes;
}

/* Custom Archive Post Type Link */

function frameflow_custom_archive_portfolio_link()
{
	if (is_post_type_archive('portfolio')) {
		$archive_portfolio_link = frameflow()->get_theme_opt('archive_portfolio_link');
		wp_redirect(get_permalink($archive_portfolio_link), 301);
		exit();
	}
}
add_action('template_redirect', 'frameflow_custom_archive_portfolio_link');

add_filter('pxl_extra_taxonomies', 'frameflow_add_tax');
function frameflow_add_tax($taxonomies)
{

	$taxonomies['portfolio-category'] = array(
		'status'     => true,
		'post_type'  => array('portfolio'),
		'taxonomy'   => 'Portfolio Categories',
		'taxonomies' => 'Portfolio Categories',
		'args'       => array(
			'rewrite'             => array(
				'slug'       => 'portfolio-category'
			),
		),
		'labels'     => array()
	);

	return $taxonomies;
}

add_filter('pxl_theme_builder_post_types', 'frameflow_theme_builder_post_type');
function frameflow_theme_builder_post_type($postypes)
{
	//default are header, footer, mega-menu
	return $postypes;
}

add_filter('pxl_theme_builder_layout_ids', 'frameflow_theme_builder_layout_id');
function frameflow_theme_builder_layout_id($layout_ids)
{
	//default [], 
	$header_layout        = (int)frameflow()->get_opt('header_layout');
	$header_sticky_layout = (int)frameflow()->get_opt('header_sticky_layout');
	$footer_layout        = (int)frameflow()->get_opt('footer_layout');
	$ptitle_layout        = (int)frameflow()->get_opt('ptitle_layout');
	$product_bottom_content        = (int)frameflow()->get_opt('product_bottom_content');
	if ($header_layout > 0)
		$layout_ids[] = $header_layout;
	if ($header_sticky_layout > 0)
		$layout_ids[] = $header_sticky_layout;
	if ($footer_layout > 0)
		$layout_ids[] = $footer_layout;
	if ($ptitle_layout > 0)
		$layout_ids[] = $ptitle_layout;
	if ($product_bottom_content > 0)
		$layout_ids[] = $product_bottom_content;

	$slider_template = frameflow_get_templates_option('slider');
	if (count($slider_template) > 0) {
		foreach ($slider_template as $key => $value) {
			$layout_ids[] = $key;
		}
	}

	$tab_template = frameflow_get_templates_option('tab');
	if (count($tab_template) > 0) {
		foreach ($tab_template as $key => $value) {
			$layout_ids[] = $key;
		}
	}

	$mega_menu_id = frameflow_get_mega_menu_builder_id();
	if (!empty($mega_menu_id))
		$layout_ids = array_merge($layout_ids, $mega_menu_id);

	$page_popup_id = frameflow_get_page_popup_builder_id();
	if (!empty($page_popup_id))
		$layout_ids = array_merge($layout_ids, $page_popup_id);

	return $layout_ids;
}

add_filter('pxl_wg_get_source_id_builder', 'frameflow_wg_get_source_builder');
function frameflow_wg_get_source_builder($wg_datas)
{
	$wg_datas['tabs'] = ['control_name' => 'tabs', 'source_name' => 'content_template'];
	$wg_datas['slides'] = ['control_name' => 'slides', 'source_name' => 'slide_template'];
	return $wg_datas;
}

/* Update primary color in Editor Builder */
add_action('elementor/preview/enqueue_styles', 'frameflow_add_editor_preview_style');
function frameflow_add_editor_preview_style()
{
	wp_add_inline_style('editor-preview', frameflow_editor_preview_inline_styles());
}
function frameflow_editor_preview_inline_styles()
{
	$theme_colors = frameflow_configs('theme_colors');
	ob_start();
	echo '.elementor-edit-area-active, .elementor-edit-area-active .e-con {';
	foreach ($theme_colors as $color => $value) {
		printf('--%1$s-color: %2$s;', str_replace('#', '', $color),  $value['value']);
	}
	echo '}';
	return ob_get_clean();
}

add_filter('get_the_archive_title', 'frameflow_archive_title_remove_label');
function frameflow_archive_title_remove_label($title)
{
	if (is_category()) {
		$title = single_cat_title('', false);
	} elseif (is_tag()) {
		$title = single_tag_title('', false);
	} elseif (is_author()) {
		$title = get_the_author();
	} elseif (is_post_type_archive()) {
		$title = post_type_archive_title('', false);
	} elseif (is_tax()) {
		$title = single_term_title('', false);
	} elseif (is_home()) {
		$title = single_post_title('', false);
	}

	return $title;
}

add_filter('comment_reply_link', 'frameflow_comment_reply_text');
function frameflow_comment_reply_text($link)
{
	$link = str_replace('Reply', '' . esc_attr__('Reply', 'frameflow') . '', $link);
	return $link;
}
add_filter('pxl_enable_pagepopup', 'frameflow_enable_pagepopup');
function frameflow_enable_pagepopup()
{
	return false;
}
add_filter('pxl_enable_megamenu', 'frameflow_enable_megamenu');
function frameflow_enable_megamenu()
{
	return true;
}
add_filter('pxl_enable_onepage', 'frameflow_enable_onepage');
function frameflow_enable_onepage()
{
	return true;
}

add_filter('pxl_support_awesome_pro', 'frameflow_support_awesome_pro');
function frameflow_support_awesome_pro()
{
	return false;
}

add_filter('redux_pxl_iconpicker_field/get_icons', 'frameflow_add_icons_to_pxl_iconpicker_field');
function frameflow_add_icons_to_pxl_iconpicker_field($icons)
{
	$custom_icons = [];
	$icons = array_merge($custom_icons, $icons);
	return $icons;
}


add_filter("pxl_mega_menu/get_icons", "frameflow_add_icons_to_megamenu");
function frameflow_add_icons_to_megamenu($icons)
{
	$custom_icons = [];
	$icons = array_merge($custom_icons, $icons);
	return $icons;
}


/**
 * Move comment field to bottom
 */
add_filter('comment_form_fields', 'frameflow_comment_field_to_bottom');
function frameflow_comment_field_to_bottom($fields)
{
	$comment_field = $fields['comment'];
	unset($fields['comment']);
	$fields['comment'] = $comment_field;
	return $fields;
}


/* ------Disable Lazy loading---- */
add_filter('wp_lazy_loading_enabled', '__return_false');

/* ------ Export Settings ---- */
add_filter('pxl_export_wp_settings', 'frameflow_export_wp_settings');
function frameflow_export_wp_settings($wp_options)
{
	$wp_options[] = 'mc4wp_default_form_id';
	return $wp_options;
}

/* ------ Theme Info ---- */
add_filter('pxl_server_info', 'frameflow_add_server_info');
function frameflow_add_server_info($infos)
{
	$infos = [
		'api_url' => 'https://api.casethemes.net/',
		'docs_url' => 'https://doc.casethemes.net/frameflow/',
		'plugin_url' => 'https://api.casethemes.net/plugins/',
		'demo_url' => 'https://demo.casethemes.net/frameflow/',
		'support_url' => 'https://casethemes.ticksy.com/',
		'help_url' => 'https://doc.casethemes.net/frameflow',
		'email_support' => 'casethemesagency@gmail.com',
		'video_url' => '#'
	];

	return $infos;
}

/* ------ Template Filter ---- */
add_filter('pxl_template_type_support', 'frameflow_template_type_support');
function frameflow_template_type_support($type)
{
	$extra_type = [
		'header'       => esc_html__('Header Desktop', 'frameflow'),
		'header-mobile'          => esc_html__('Header Mobile', 'frameflow'),
		'widget'          => esc_html__('Widget Sidebar', 'frameflow'),
		'footer'       => esc_html__('Footer', 'frameflow'),
		'mega-menu'    => esc_html__('Mega Menu', 'frameflow'),
		'page-title'          => esc_html__('Page Title', 'frameflow'),
		'hidden-panel'          => esc_html__('Hidden Panel', 'frameflow'),
		'tab'          => esc_html__('Tab', 'frameflow'),
		'popup'          => esc_html__('Popup', 'frameflow'),
		'page'          => esc_html__('Page', 'frameflow'),
		'slider'          => esc_html__('Slider', 'frameflow'),
	];
	return $extra_type;
}

/* Taxonomy Meta Register */
add_action('pxl_taxonomy_meta_register', 'frameflow_tax_options_register');
function frameflow_tax_options_register($metabox)
{

	$panels = [
		'category' => [
			'opt_name'            => 'tax_post_option',
			'display_name'        => esc_html__('Frameflow Settings', 'frameflow'),
			'show_options_object' => false,
			'sections'  => [
				'tax_post_settings' => [
					'title'  => esc_html__('Frameflow Settings', 'frameflow'),
					'icon'   => 'el el-refresh',
					'fields' => array(
						array(
							'id'       => 'bg_category',
							'type'     => 'media',
							'title'    => esc_html__('Select Banner', 'frameflow'),
							'default'  => '',
							'url'      => false,
						),

					)
				]
			]
		],

	];

	$metabox->add_meta_data($panels);
}

/* Switch Swiper Version  */
add_filter('pxl-swiper-version-active', 'frameflow_set_swiper_version_active');
function frameflow_set_swiper_version_active($version)
{
	$version = '8.4.5'; //5.3.6, 8.4.5, 10.1.0
	return $version;
}

/* Search Result  */
function frameflow_custom_post_types_in_search_results($query)
{
	if ($query->is_main_query() && $query->is_search() && ! is_admin()) {
		// If searching specifically for products (WooCommerce), don't override post_type
		if ( isset($_GET['post_type']) && $_GET['post_type'] == 'product' ) {
			return;
		}
		$query->set('post_type', array('post', 'portfolio', 'product'));
	}
}
add_action('pre_get_posts', 'frameflow_custom_post_types_in_search_results');

/* Add Custom Font Face */
add_filter('elementor/fonts/groups', 'frameflow_update_elementor_font_groups_control');
function frameflow_update_elementor_font_groups_control($font_groups)
{
	$pxlfonts_group = array('pxlfonts' => esc_html__('Boldonse Fonts', 'frameflow'));
	$pxlfonts_group = array('pxlfonts' => esc_html__('Geist Fonts', 'frameflow'));

	return array_merge($pxlfonts_group, $font_groups);
}

add_filter('elementor/fonts/additional_fonts', 'frameflow_update_elementor_font_control');
function frameflow_update_elementor_font_control($additional_fonts)
{
	$additional_fonts['Boldonse'] = 'pxlfonts';
	$additional_fonts['Geist'] = 'pxlfonts';
	return $additional_fonts;
}
/**
 * Split menu text into spans
 */
add_filter('nav_menu_item_title', 'frameflow_split_menu_text', 10, 4);
function frameflow_split_menu_text($title, $item, $args, $depth)
{
    if (empty($args->pxl_split_title)) {
        return $title;
    }

    if ($depth > 0 || ($item && isset($item->menu_item_parent) && $item->menu_item_parent != 0)) {
        return $title;
    }

    if (empty($title)) {
        return $title;
    }

    if (function_exists('mb_str_split')) {
        $chars = mb_str_split($title, 1, 'UTF-8');
    } else {
        $chars = preg_split('//u', $title, -1, PREG_SPLIT_NO_EMPTY);
    }

    $split_title = '';
    foreach ($chars as $char) {
        $display_char = ($char === ' ') ? '&nbsp;' : esc_html($char);
        $split_title .= '<span>' . $display_char . '</span>';
    }

    return $split_title;
}
