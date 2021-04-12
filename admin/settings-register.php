<?php // EgExpress - Register Settings



// disable direct file access
if ( ! defined( 'ABSPATH' ) ) {
	
	exit;
	
}



// register plugin settings
function egexpress_register_settings() {
	
	/*
	
	register_setting( 
		string   $option_group, 
		string   $option_name, 
		callable $sanitize_callback = ''
	);
	
	*/
	
	register_setting( 
		'egexpress_options',
		'egexpress_options',
		'egexpress_callback_validate_options'
	); 
	
	/*
	
	add_settings_section( 
		string   $id, 
		string   $title, 
		callable $callback, 
		string   $page
	);
	
	*/
	
	add_settings_section( 
		'egexpress_settings_section',
		esc_html__('API Settings', 'egexpress'),
		'egexpress_callback_settings_section',
		'egexpress'
	);
	
	add_settings_field(
		'accountNo',
		esc_html__('Account Number', 'egexpress'),
		'egexpress_callback_field_text',
		'egexpress',
		'egexpress_settings_section',
		[ 'id' => 'accountNo', 'label' => esc_html__('Add API Account Number Here', 'egexpress') ]
	);
	
	add_settings_field(
		'password',
		esc_html__('Password', 'egexpress'),
		'egexpress_callback_field_text',
		'egexpress',
		'egexpress_settings_section',
		[ 'id' => 'password', 'label' => esc_html__('Add API Password Here', 'egexpress') ]
	);

    add_settings_field(
        'hashkey',
        esc_html__('Hashkey', 'egexpress'),
        'egexpress_callback_field_text',
        'egexpress',
        'egexpress_settings_section',
        [ 'id' => 'hashkey', 'label' => esc_html__('Add API hashkey Here', 'egexpress') ]
    );

    add_settings_field(
        'shipperName',
        esc_html__('Shipper Name', 'egexpress'),
        'egexpress_callback_field_text',
        'egexpress',
        'egexpress_settings_section',
        [ 'id' => 'shipperName', 'label' => esc_html__('Add Shipper Name Here', 'egexpress') ]
    );

    add_settings_field(
        'shipperPhone',
        esc_html__('Shipper Phone', 'egexpress'),
        'egexpress_callback_field_text',
        'egexpress',
        'egexpress_settings_section',
        [ 'id' => 'shipperPhone', 'label' => esc_html__('Add Shipper Phone Here', 'egexpress') ]
    );

    add_settings_field(
        'shipperCity',
        esc_html__('Shipper City', 'egexpress'),
        'egexpress_shipping_city_select',
        'egexpress',
        'egexpress_settings_section',
        [ 'id' => 'shipperCity', 'label' => esc_html__('Add Shipper City Here', 'egexpress') ]
    );

    add_settings_field(
        'shipperAddress1',
        esc_html__('Shipper Address1', 'egexpress'),
        'egexpress_callback_field_text',
        'egexpress',
        'egexpress_settings_section',
        [ 'id' => 'shipperAddress1', 'label' => esc_html__('Add Shipper Address1 Here', 'egexpress') ]
    );

    add_settings_field(
        'shipperAddress2',
        esc_html__('Shipper Address2', 'egexpress'),
        'egexpress_callback_field_text',
        'egexpress',
        'egexpress_settings_section',
        [ 'id' => 'shipperAddress2', 'label' => esc_html__('Add Shipper Address2 Here', 'egexpress') ]
    );

    add_settings_field(
        'goodsDescription',
        esc_html__('Goods Description', 'egexpress'),
        'egexpress_callback_field_text',
        'egexpress',
        'egexpress_settings_section',
        [ 'id' => 'goodsDescription', 'label' => esc_html__('Add Goods Description Here', 'egexpress') ]
    );

    add_settings_field(
        'goodsOriginCountry',
        esc_html__('goods origin country', 'egexpress'),
        'egexpress_callback_field_text',
        'egexpress',
        'egexpress_settings_section',
        [ 'id' => 'goodsOriginCountry', 'label' => esc_html__('Add goods origin country Here', 'egexpress') ]
    );


    
} 
add_action( 'admin_init', 'egexpress_register_settings' );


