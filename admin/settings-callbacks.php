<?php // EgExpress - Settings Callbacks



// disable direct file access
if ( ! defined( 'ABSPATH' ) ) {
	
	exit;
	
}



// callback: login section
function egexpress_callback_settings_section() {
	
	echo '<p>'. esc_html__('You can change your api credentials from here.', 'egexpress') .'</p>';
	
}




// callback: text field
function egexpress_callback_field_text( $args ) {
	
	$options = get_option( 'egexpress_options', egexpress_options_default() );
	
	$id    = isset( $args['id'] )    ? $args['id']    : '';
	$label = isset( $args['label'] ) ? $args['label'] : '';
	
	$value = isset( $options[$id] ) ? sanitize_text_field( $options[$id] ) : '';
	
	echo '<input id="egexpress_options_'. $id .'" name="egexpress_options['. $id .']" type="text" size="40" value="'. $value .'"><br />';
	echo '<label for="egexpress_options_'. $id .'">'. $label .'</label>';
	
}



// radio field options
function egexpress_options_radio() {
	
	return array(
		
		'enable'  => esc_html__('Enable custom styles', 'egexpress'),
		'disable' => esc_html__('Disable custom styles', 'egexpress')
		
	);
	
}



// callback: radio field
function egexpress_callback_field_radio( $args ) {
	
	$options = get_option( 'egexpress_options', egexpress_options_default() );
	
	$id    = isset( $args['id'] )    ? $args['id']    : '';
	$label = isset( $args['label'] ) ? $args['label'] : '';
	
	$selected_option = isset( $options[$id] ) ? sanitize_text_field( $options[$id] ) : '';
	
	$radio_options = egexpress_options_radio();
	
	foreach ( $radio_options as $value => $label ) {
		
		$checked = checked( $selected_option === $value, true, false );
		
		echo '<label><input name="egexpress_options['. $id .']" type="radio" value="'. $value .'"'. $checked .'> ';
		echo '<span>'. $label .'</span></label><br />';
		
	}
	
}



// callback: textarea field
function egexpress_callback_field_textarea( $args ) {
	
	$options = get_option( 'egexpress_options', egexpress_options_default() );
	
	$id    = isset( $args['id'] )    ? $args['id']    : '';
	$label = isset( $args['label'] ) ? $args['label'] : '';
	
	$allowed_tags = wp_kses_allowed_html( 'post' );
	
	$value = isset( $options[$id] ) ? wp_kses( stripslashes_deep( $options[$id] ), $allowed_tags ) : '';
	
	echo '<textarea id="egexpress_options_'. $id .'" name="egexpress_options['. $id .']" rows="5" cols="50">'. $value .'</textarea><br />';
	echo '<label for="egexpress_options_'. $id .'">'. $label .'</label>';
	
}



// callback: checkbox field
function egexpress_callback_field_checkbox( $args ) {
	
	$options = get_option( 'egexpress_options', egexpress_options_default() );
	
	$id    = isset( $args['id'] )    ? $args['id']    : '';
	$label = isset( $args['label'] ) ? $args['label'] : '';
	
	$checked = isset( $options[$id] ) ? checked( $options[$id], 1, false ) : '';
	
	echo '<input id="egexpress_options_'. $id .'" name="egexpress_options['. $id .']" type="checkbox" value="1"'. $checked .'> ';
	echo '<label for="egexpress_options_'. $id .'">'. $label .'</label>';
	
}

// callback: select field
function egexpress_shipping_city_select( $args ) {
	
	$options = get_option( 'egexpress_options', egexpress_options_default() );

	$id    = isset( $args['id'] )    ? $args['id']    : '';
	$label = isset( $args['label'] ) ? $args['label'] : '';
	
	$selected_option = isset( $options[$id] ) ? sanitize_text_field( $options[$id] ) : '';
	
	//$select_options = egexpress_shipping_cities();
    $select_options = egexpress_shipping_cities_code_value();
	
	echo '<select id="egexpress_options_'. $id .'" name="egexpress_options['. $id .']">';
	
	foreach ( $select_options as $value => $option ) {
		
		$selected = selected( $selected_option == $value, true, false );
		
		echo '<option value="'. $value .'"'. $selected .'>'. $option .'</option>';
		
	}
	
	echo '</select> <label for="egexpress_options_'. $id .'">'. $label .'</label>';
	
}


