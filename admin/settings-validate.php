<?php // EgExpress - Validate Settings



// disable direct file access
if ( ! defined( 'ABSPATH' ) ) {
	
	exit;
	
}



// callback: validate options
function egexpress_callback_validate_options( $input ) {
	

	do_action("woocommerce_shipping_init");
	return $input;
	
}


