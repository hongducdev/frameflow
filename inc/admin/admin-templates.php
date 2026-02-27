<?php

if (!defined('ABSPATH'))
	exit;

class Frameflow_Admin_Templates extends Frameflow_Base
{

	public function __construct()
	{
		$this->add_action('admin_menu', 'register_page', 20);
	}

	public function register_page()
	{
		add_submenu_page(
			'pxlart',
			esc_html__('Templates', 'frameflow'),
			esc_html__('Templates', 'frameflow'),
			'manage_options',
			'edit.php?post_type=pxl-template',
			false
		);
	}
}
new Frameflow_Admin_Templates;
