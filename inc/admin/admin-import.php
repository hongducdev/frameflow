<?php

/**
 * The Frameflow_Admin_Import class
 */

if (!defined('ABSPATH'))
	exit; // Exit if accessed directly

class Frameflow_Admin_Import extends Frameflow_Admin_Page
{
	protected $id = null;
	protected $page_title = null;
	protected $menu_title = null;
	public $parent = null;
	public function __construct()
	{

		$this->id = 'pxlart-import-demos';
		$this->page_title = esc_html__('Import Demos', 'frameflow');
		$this->menu_title = esc_html__('Import Demos', 'frameflow');
		$this->parent = 'pxlart';
		//$this->position = '10';

		parent::__construct();
	}

	public function display()
	{
		include_once(get_template_directory() . '/inc/admin/views/admin-demos.php');
	}


	public function save() {}
}
new Frameflow_Admin_Import;
