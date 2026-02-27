<?php

add_action('pxl_post_metabox_register', 'frameflow_page_options_register');
function frameflow_page_options_register($metabox)
{

	$panels = [
		'post' => [
			'opt_name'            => 'post_option',
			'display_name'        => esc_html__('Post Settings', 'frameflow'),
			'show_options_object' => false,
			'context'  => 'advanced',
			'priority' => 'default',
			'sections'  => [
				'post_settings' => [
					'title'  => esc_html__('Post Settings', 'frameflow'),
					'icon'   => 'el el-refresh',
					'fields' => array_merge(
						frameflow_sidebar_pos_opts(['prefix' => 'post_', 'default' => true, 'default_value' => '-1']),
						frameflow_page_title_opts([
							'default'         => true,
							'default_value'   => '-1'
						]),
						array(
							array(
								'id'          => 'featured-video-url',
								'type'        => 'text',
								'title'       => esc_html__('Video URL', 'frameflow'),
								'description' => esc_html__('Video will show when set post format is video', 'frameflow'),
								'validate'    => 'url',
								'msg'         => 'Url error!',
							),
							array(
								'id'          => 'featured-audio-url',
								'type'        => 'text',
								'title'       => esc_html__('Audio URL', 'frameflow'),
								'description' => esc_html__('Audio that will show when set post format is audio', 'frameflow'),
								'validate'    => 'url',
								'msg'         => 'Url error!',
							),
							array(
								'id' => 'featured-quote-text',
								'type' => 'textarea',
								'title' => esc_html__('Quote Text', 'frameflow'),
								'default' => '',
							),
							array(
								'id'          => 'featured-quote-cite',
								'type'        => 'text',
								'title'       => esc_html__('Quote Cite', 'frameflow'),
								'description' => esc_html__('Quote will show when set post format is quote', 'frameflow'),
							),
							array(
								'id'       => 'featured-link-url',
								'type'     => 'text',
								'title'    => esc_html__('Format Link URL', 'frameflow'),
								'description' => esc_html__('Link will show when set post format is link', 'frameflow'),
							),
							array(
								'id'          => 'featured-link-text',
								'type'        => 'text',
								'title'       => esc_html__('Format Link Text', 'frameflow'),
							),
						)
					)
				]
			]
		],
		'page' => [
			'opt_name'            => 'pxl_page_options',
			'display_name'        => esc_html__('Page Options', 'frameflow'),
			'show_options_object' => false,
			'context'  => 'advanced',
			'priority' => 'default',
			'sections'  => [
				'header' => [
					'title'  => esc_html__('Header', 'frameflow'),
					'icon'   => 'el-icon-website',
					'fields' => array_merge(
						frameflow_header_opts([
							'default'         => true,
							'default_value'   => '-1'
						]),
						frameflow_header_mobile_opts([
							'default'         => true,
							'default_value'   => '-1'
						]),
						array(
							array(
								'id'       => 'header_display',
								'type'     => 'button_set',
								'title'    => esc_html__('Header Display', 'frameflow'),
								'options'  => array(
									'show' => esc_html__('Show', 'frameflow'),
									'hide'  => esc_html__('Hide', 'frameflow'),
								),
								'default'  => 'show',
							),
							array(
								'id'       => 'p_menu',
								'type'     => 'select',
								'title'    => esc_html__('Menu', 'frameflow'),
								'options'  => frameflow_get_nav_menu_slug(),
								'default' => '',
							),
						),
						array(
							array(
								'id'       => 'sticky_scroll',
								'type'     => 'button_set',
								'title'    => esc_html__('Sticky Scroll', 'frameflow'),
								'options'  => array(
									'-1' => esc_html__('Inherit', 'frameflow'),
									'pxl-sticky-stt' => esc_html__('Scroll To Top', 'frameflow'),
									'pxl-sticky-stb'  => esc_html__('Scroll To Bottom', 'frameflow'),
								),
								'default'  => '-1',
							),
						)
					)

				],
				'page_title' => [
					'title'  => esc_html__('Page Title', 'frameflow'),
					'icon'   => 'el el-indent-left',
					'fields' => array_merge(
						frameflow_page_title_opts([
							'default'         => true,
							'default_value'   => '-1'
						]),
						array(
							array(
								'id'      => 'sg_page_title_text',
								'type'    => 'text',
								'title'   => esc_html__('Page Title Text', 'frameflow'),
								'default' => '',
							),	
						)
					)
				],
				'content' => [
					'title'  => esc_html__('Content', 'frameflow'),
					'icon'   => 'el-icon-pencil',
					'fields' => array_merge(
						frameflow_sidebar_pos_opts(['prefix' => 'page_', 'default' => true, 'default_value' => '-1']),
						array(
							array(
								'id'             => 'content_spacing',
								'type'           => 'spacing',
								'output'         => array('#pxl-wapper #pxl-main'),
								'right'          => false,
								'left'           => false,
								'mode'           => 'padding',
								'units'          => array('px'),
								'units_extended' => 'false',
								'title'          => esc_html__('Spacing Top/Bottom', 'frameflow'),
								'default'        => array(
									'padding-top'    => '',
									'padding-bottom' => '',
									'units'          => 'px',
								)
							),
						),
						array(
							array(
								'id'    => 'loader_style',
								'type'  => 'select',
								'title' => esc_html__('Loader Style', 'frameflow'),
								'options' => [
									'style-text'           => esc_html__('Text', 'frameflow'),
									'style-logo'     => esc_html__('Logo', 'frameflow'),
								],
							),
							array(
								'id'             => 'loading_text',
								'type'           => 'text',
								'title'          => esc_html__('Loading Text', 'frameflow'),
								'default'        => '',
								'desc'           => esc_html__('Enter the text displayed on load.', 'frameflow'),
								'force_output'   => true,
								'required'       => array(0 => 'loader_style', 1 => 'equals', 2 => array('style-text')),
							),
							array(
								'id'       => 'percentage_intro',
								'type'     => 'text',
								'title'    => esc_html__('Percentage Intro', 'frameflow'),
								'default'  => esc_html__('Please wait, content is loading...', 'frameflow'),
								'desc'     => esc_html__('Enter the text displayed on load.', 'frameflow'),
								'force_output' => true,
								'required' => array(0 => 'loader_style', 1 => 'equals', 2 => array('style-text')),
							),
							array(
								'id'       => 'loader_logo',
								'type'     => 'media',
								'title'    => esc_html__('Logo', 'frameflow'),
								'url'      => false,
								'required' => array(0 => 'loader_style', 1 => 'equals', 2 => array('style-logo')),
							),
							array(
								'id'       => 'loader_logo_height',
								'type'     => 'dimensions',
								'title'    => esc_html__('Logo Height', 'frameflow'),
								'width'    => false,
								'unit'     => 'px',
								'output'    => array('.pxl-loader .loader-logo img'),
								'required' => array(0 => 'loader_style', 1 => 'equals', 2 => array('style-logo')),
							),
						)
					)
				],
				'footer' => [
					'title'  => esc_html__('Footer', 'frameflow'),
					'icon'   => 'el el-website',
					'fields' => array_merge(
						frameflow_footer_opts([
							'default'         => true,
							'default_value'   => '-1'
						]),
						array(
							array(
								'id'       => 'footer_display',
								'type'     => 'button_set',
								'title'    => esc_html__('Footer Display', 'frameflow'),
								'options'  => array(
									'show' => esc_html__('Show', 'frameflow'),
									'hide'  => esc_html__('Hide', 'frameflow'),
								),
								'default'  => 'show',
							),
							array(
								'id'       => 'p_footer_fixed',
								'type'     => 'button_set',
								'title'    => esc_html__('Footer Fixed', 'frameflow'),
								'options'  => array(
									'inherit' => esc_html__('Inherit', 'frameflow'),
									'on' => esc_html__('On', 'frameflow'),
									'off' => esc_html__('Off', 'frameflow'),
								),
								'default'  => 'inherit',
							),
							array(
								'id'          => 'body_bg_color_ct',
								'type'        => 'background',
								'title'       => esc_html__('Body Background Color Custom', 'frameflow'),
								'transparent' => false,
								'output' => [
									'.pxl-footer-fixed #pxl-main',
								],
								'required' => array(0 => 'p_footer_fixed', 1 => 'equals', 2 => 'on'),
								'url'      => false
							),
							array(
								'id'       => 'back_top_top_style',
								'type'     => 'button_set',
								'title'    => esc_html__('Back to Top Style', 'frameflow'),
								'options'  => array(
									'style-default' => esc_html__('Default', 'frameflow'),
									'style-round' => esc_html__('Round', 'frameflow'),
								),
								'default'  => 'style-default',
							),
						)
					)
				],
				'colors' => [
					'title'  => esc_html__('Colors', 'frameflow'),
					'icon'   => 'el el-website',
					'fields' => array_merge(
						array(
							array(
								'id'       => 'body_bg_color',
								'type'     => 'color',
								'title'    => esc_html__('Body Background Color', 'frameflow'),
								'transparent' => false,
								'default'     => ''
							),
							array(
								'id'          => 'primary_color',
								'type'        => 'color',
								'title'       => esc_html__('Primary Color', 'frameflow'),
								'transparent' => false,
								'default'     => ''
							),
							array(
								'id'          => 'third_color',
								'type'        => 'color',
								'title'       => esc_html__('Third Color', 'frameflow'),
								'transparent' => false,
								'default'     => ''
							),
							array(
								'id'          => 'four_color',
								'type'        => 'color',
								'title'       => esc_html__('Four Color', 'frameflow'),
								'transparent' => false,
								'default'     => ''
							),
							array(
								'id'          => 'gradient_color',
								'type'        => 'color_gradient',
								'title'       => esc_html__('Gradient Color One', 'frameflow'),
								'transparent' => false,
								'default'  => array(
									'from' => '',
									'to'   => '',
								),
							),
							array(
								'id'          => 'gradient_color_center',
								'type'        => 'color',
								'title'       => esc_html__('Gradient Color Center', 'frameflow'),
								'transparent' => false,
								'default'     => ''
							),
							array(
								'id'          => 'gradient_color_two',
								'type'        => 'color_gradient',
								'title'       => esc_html__('Gradient Color Two', 'frameflow'),
								'transparent' => false,
								'default'  => array(
									'from' => '',
									'to'   => '',
								),
							),
							array(
								'id'          => 'gradient_color_two_center',
								'type'        => 'color',
								'title'       => esc_html__('Gradient Color Two Center', 'frameflow'),
								'transparent' => false,
								'default'     => ''
							),
						)
					)
				],
				'extra' => [
					'title'  => esc_html__('Extra', 'frameflow'),
					'icon'   => 'el el-website',
					'fields' => array_merge(
						array(
							array(
								'id' => 'body_custom_class',
								'type' => 'text',
								'title' => esc_html__('Body Custom Class', 'frameflow'),
							),
						)
					)
				]
			]
		],
		'portfolio' => [
			'opt_name'            => 'pxl_portfolio_options',
			'display_name'        => esc_html__('Portfolio Options', 'frameflow'),
			'show_options_object' => false,
			'context'  => 'advanced',
			'priority' => 'default',
			'sections'  => [
				'header1' => [
					'title'  => esc_html__('Header', 'frameflow'),
					'icon'   => 'el-icon-website',
					'fields' => array_merge(
						frameflow_header_opts([
							'default'         => true,
							'default_value'   => '-1'
						]),
						frameflow_header_mobile_opts([
							'default'         => true,
							'default_value'   => '-1'
						]),
						array(
							array(
								'id'       => 'header_display',
								'type'     => 'button_set',
								'title'    => esc_html__('Header Display', 'frameflow'),
								'options'  => array(
									'show' => esc_html__('Show', 'frameflow'),
									'hide'  => esc_html__('Hide', 'frameflow'),
								),
								'default'  => 'show',
							),
							array(
								'id'       => 'p_menu',
								'type'     => 'select',
								'title'    => esc_html__('Menu', 'frameflow'),
								'options'  => frameflow_get_nav_menu_slug(),
								'default' => '',
							),
						),
						array(
							array(
								'id'       => 'sticky_scroll',
								'type'     => 'button_set',
								'title'    => esc_html__('Sticky Scroll', 'frameflow'),
								'options'  => array(
									'-1' => esc_html__('Inherit', 'frameflow'),
									'pxl-sticky-stt' => esc_html__('Scroll To Top', 'frameflow'),
									'pxl-sticky-stb'  => esc_html__('Scroll To Bottom', 'frameflow'),
								),
								'default'  => '-1',
							),
						)
					)

				],
				'page_title' => [
					'title'  => esc_html__('Page Title', 'frameflow'),
					'icon'   => 'el el-indent-left',
					'fields' => array_merge(
						frameflow_page_title_opts([
							'default'         => true,
							'default_value'   => '-1'
						]),
					)
				],
				'content' => [
					'title'  => esc_html__('Content', 'frameflow'),
					'icon'   => 'el-icon-pencil',
					'fields' => array_merge(
						frameflow_sidebar_pos_opts(['prefix' => 'page_', 'default' => true, 'default_value' => '-1']),
						array(
							array(
								'id'             => 'content_spacing',
								'type'           => 'spacing',
								'output'         => array('#pxl-wapper #pxl-main'),
								'right'          => false,
								'left'           => false,
								'mode'           => 'padding',
								'units'          => array('px'),
								'units_extended' => 'false',
								'title'          => esc_html__('Spacing Top/Bottom', 'frameflow'),
								'default'        => array(
									'padding-top'    => '',
									'padding-bottom' => '',
									'units'          => 'px',
								)
							),
							array(
								'id' => 'location',
								'type' => 'text',
								'title'    => esc_html('Location', 'frameflow'),
							),
						),
					)
				],
				'footer' => [
					'title'  => esc_html__('Footer', 'frameflow'),
					'icon'   => 'el el-website',
					'fields' => array_merge(
						frameflow_footer_opts([
							'default'         => true,
							'default_value'   => '-1'
						]),
						array(
							array(
								'id'       => 'footer_display',
								'type'     => 'button_set',
								'title'    => esc_html__('Footer Display', 'frameflow'),
								'options'  => array(
									'show' => esc_html__('Show', 'frameflow'),
									'hide'  => esc_html__('Hide', 'frameflow'),
								),
								'default'  => 'show',
							),
							array(
								'id'       => 'p_footer_fixed',
								'type'     => 'button_set',
								'title'    => esc_html__('Footer Fixed', 'frameflow'),
								'options'  => array(
									'inherit' => esc_html__('Inherit', 'frameflow'),
									'on' => esc_html__('On', 'frameflow'),
									'off' => esc_html__('Off', 'frameflow'),
								),
								'default'  => 'inherit',
							),
							array(
								'id'       => 'back_top_top_style',
								'type'     => 'button_set',
								'title'    => esc_html__('Back to Top Style', 'frameflow'),
								'options'  => array(
									'style-default' => esc_html__('Default', 'frameflow'),
									'style-round' => esc_html__('Round', 'frameflow'),
								),
								'default'  => 'style-default',
							),
						)
					)
				],
			]
		],
		'product' => [
			'opt_name'            => 'pxl_product_options',
			'display_name'        => esc_html__('Product Options', 'frameflow'),
			'show_options_object' => false,
			'context'  => 'advanced',
			'priority' => 'default',
			'sections'  => [
				'header1' => [
					'title'  => esc_html__('Header', 'frameflow'),
					'icon'   => 'el-icon-website',
					'fields' => array_merge(
						frameflow_header_opts([
							'default'         => true,
							'default_value'   => '-1'
						]),
						frameflow_header_mobile_opts([
							'default'         => true,
							'default_value'   => '-1'
						]),
						array(
							array(
								'id'       => 'header_display',
								'type'     => 'button_set',
								'title'    => esc_html__('Header Display', 'frameflow'),
								'options'  => array(
									'show' => esc_html__('Show', 'frameflow'),
									'hide'  => esc_html__('Hide', 'frameflow'),
								),
								'default'  => 'show',
							),
							array(
								'id'       => 'p_menu',
								'type'     => 'select',
								'title'    => esc_html__('Menu', 'frameflow'),
								'options'  => frameflow_get_nav_menu_slug(),
								'default' => '',
							),
						),
						array(
							array(
								'id'       => 'sticky_scroll',
								'type'     => 'button_set',
								'title'    => esc_html__('Sticky Scroll', 'frameflow'),
								'options'  => array(
									'-1' => esc_html__('Inherit', 'frameflow'),
									'pxl-sticky-stt' => esc_html__('Scroll To Top', 'frameflow'),
									'pxl-sticky-stb'  => esc_html__('Scroll To Bottom', 'frameflow'),
								),
								'default'  => '-1',
							),
						),
					)

				],
				'page_title' => [
					'title'  => esc_html__('Page Title', 'frameflow'),
					'icon'   => 'el el-indent-left',
					'fields' => array_merge(
						frameflow_page_title_opts([
							'default'         => true,
							'default_value'   => '-1'
						]),
					)
				],
				'content' => [
					'title'  => esc_html__('Content', 'frameflow'),
					'icon'   => 'el-icon-pencil',
					'fields' => array_merge(
						frameflow_sidebar_pos_opts(['prefix' => 'page_', 'default' => false, 'default_value' => '0']),
						array(
							array(
								'id'             => 'content_spacing',
								'type'           => 'spacing',
								'output'         => array('#pxl-wapper #pxl-main'),
								'right'          => false,
								'left'           => false,
								'mode'           => 'padding',
								'units'          => array('px'),
								'units_extended' => 'false',
								'title'          => esc_html__('Spacing Top/Bottom', 'frameflow'),
								'default'        => array(
									'padding-top'    => '',
									'padding-bottom' => '',
									'units'          => 'px',
								)
							),
						),
					)
				],
				'footer' => [
					'title'  => esc_html__('Footer', 'frameflow'),
					'icon'   => 'el el-website',
					'fields' => array_merge(
						frameflow_footer_opts([
							'default'         => true,
							'default_value'   => '-1'
						]),
					)
				],
			]
		],

		'pxl-template' => [ //post_type
			'opt_name'            => 'pxl_hidden_template_options',
			'display_name'        => esc_html__('Template Options', 'frameflow'),
			'show_options_object' => false,
			'context'  => 'advanced',
			'priority' => 'default',
			'sections'  => [
				'header' => [
					'title'  => esc_html__('General', 'frameflow'),
					'icon'   => 'el-icon-website',
					'fields' => array(
						array(
							'id'    => 'template_type',
							'type'  => 'select',
							'title' => esc_html__('Type', 'frameflow'),
							'options' => [
								'df'       	   => esc_html__('Select Type', 'frameflow'),
								'header'       => esc_html__('Header Desktop', 'frameflow'),
								'header-mobile'       => esc_html__('Header Mobile', 'frameflow'),
								'footer'       => esc_html__('Footer', 'frameflow'),
								'mega-menu'    => esc_html__('Mega Menu', 'frameflow'),
								'page-title'   => esc_html__('Page Title', 'frameflow'),
								'tab' => esc_html__('Tab', 'frameflow'),
								'hidden-panel' => esc_html__('Hidden Panel', 'frameflow'),
								'popup' => esc_html__('Popup', 'frameflow'),
								'widget' => esc_html__('Widget Sidebar', 'frameflow'),
								'page' => esc_html__('Page', 'frameflow'),
								'slider' => esc_html__('Slider', 'frameflow'),
							],
							'default' => 'df',
						),
						array(
							'id'    => 'header_type',
							'type'  => 'select',
							'title' => esc_html__('Header Type', 'frameflow'),
							'options' => [
								'px-header--default'       	   => esc_html__('Default', 'frameflow'),
								'px-header--transparent'       => esc_html__('Transparent', 'frameflow'),
								'px-header--left_sidebar'       => esc_html__('Left Sidebar', 'frameflow'),
							],
							'default' => 'px-header--default',
							'indent' => true,
							'required' => array(0 => 'template_type', 1 => 'equals', 2 => 'header'),
						),

						array(
							'id'    => 'header_mobile_type',
							'type'  => 'select',
							'title' => esc_html__('Header Type', 'frameflow'),
							'options' => [
								'px-header--default'       	   => esc_html__('Default', 'frameflow'),
								'px-header--transparent'       => esc_html__('Transparent', 'frameflow'),
							],
							'default' => 'px-header--default',
							'indent' => true,
							'required' => array(0 => 'template_type', 1 => 'equals', 2 => 'header-mobile'),
						),

						array(
							'id'    => 'hidden_panel_position',
							'type'  => 'select',
							'title' => esc_html__('Hidden Panel Position', 'frameflow'),
							'options' => [
								'top'       	   => esc_html__('Top', 'frameflow'),
								'right'       	   => esc_html__('Right', 'frameflow'),
							],
							'default' => 'right',
							'required' => array(0 => 'template_type', 1 => 'equals', 2 => 'hidden-panel'),
						),
						array(
							'id'          => 'hidden_panel_height',
							'type'        => 'text',
							'title'       => esc_html__('Hidden Panel Height', 'frameflow'),
							'subtitle'       => esc_html__('Enter number.', 'frameflow'),
							'transparent' => false,
							'default'     => '',
							'force_output' => true,
							'required' => array(0 => 'hidden_panel_position', 1 => 'equals', 2 => 'top'),
						),
						array(
							'id'          => 'hidden_panel_boxcolor',
							'type'        => 'color',
							'title'       => esc_html__('Box Color', 'frameflow'),
							'transparent' => false,
							'default'     => '',
							'required' => array(0 => 'template_type', 1 => 'equals', 2 => 'hidden-panel'),
						),

						array(
							'id'          => 'header_sidebar_width',
							'type'        => 'slider',
							'title'       => esc_html__('Header Sidebar Width', 'frameflow'),
							"default"   => 300,
							"min"       => 50,
							"step"      => 1,
							"max"       => 900,
							'force_output' => true,
							'required' => array(0 => 'header_type', 1 => 'equals', 2 => 'px-header--left_sidebar'),
						),

						array(
							'id'          => 'header_sidebar_border',
							'type'        => 'border',
							'title'       => esc_html__('Header Sidebar Border', 'frameflow'),
							'force_output' => true,
							'required' => array(0 => 'header_type', 1 => 'equals', 2 => 'px-header--left_sidebar'),
							'default' => '',
						),
					),

				],
			]
		],
	];

	$metabox->add_meta_data($panels);
}
