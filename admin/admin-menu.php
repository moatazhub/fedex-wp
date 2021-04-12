<?php // EgExpress - Admin Menu



// disable direct file access
if ( ! defined( 'ABSPATH' ) ) {
	
	exit;
	
}

// add top-level administrative menu
function egexpress_add_toplevel_menu() {
	
	/* 
	
	add_menu_page(
		string   $page_title, 
		string   $menu_title, 
		string   $capability, 
		string   $menu_slug, 
		callable $function = '', 
		string   $icon_url = '', 
		int      $position = null 
	)
	
	*/
	
	add_menu_page(
		esc_html__('Egypt Express', 'egexpress'),
		esc_html__('Egypt Express', 'egexpress'),
		'manage_options',
		'egexpress_parent',
		'egexpress_display_settings_page',
		'dashicons-admin-generic',
		null
	);
    add_submenu_page(
         null,
        esc_html__('Create DWB', 'egexpress'),
        esc_html__('Create DWB', 'egexpress'),
        'manage_options',
        'egexpress_create_dwb',
        'egexpress_create_dwb_page'
    );
}
 add_action( 'admin_menu', 'egexpress_add_toplevel_menu' );


