<?php

// disable direct file access
if ( ! defined( 'ABSPATH' ) ) {

    exit;

}




add_filter( 'woocommerce_shipping_calculator_enable_postcode', '__return_false' );
add_filter( 'woocommerce_shipping_calculator_enable_state', '__return_false' );
//add_filter( 'woocommerce_shipping_calculator_enable_city', '__return_true' );

add_filter( 'woocommerce_checkout_fields' , 'egexpress_custom_override_checkout_fields' );

function egexpress_custom_override_checkout_fields( $fields ) {
//    unset($fields['billing']['billing_first_name']);
//    unset($fields['billing']['billing_last_name']);
//    unset($fields['billing']['billing_company']);
//    unset($fields['billing']['billing_address_1']);
//    unset($fields['billing']['billing_address_2']);
//    unset($fields['billing']['billing_city']);
//    unset($fields['billing']['billing_postcode']);
//    unset($fields['billing']['billing_country']);
//    unset($fields['billing']['billing_state']);
//    unset($fields['billing']['billing_phone']);
//    unset($fields['order']['order_comments']);
//    unset($fields['billing']['billing_email']);
//    unset($fields['account']['account_username']);
//    unset($fields['account']['account_password']);
//    unset($fields['account']['account_password-2']);

    // custom ones
//    unset($fields['billing']['billing_postcode']);
    unset($fields['billing']['billing_state']);
    unset($fields['shipping']['billing_state']);

    $city_args = wp_parse_args( array(
        'type' => 'select',
        'options' => egexpress_shipping_cities_value_only()
    ,
    ), $fields['shipping']['shipping_city'] );
    $fields['shipping']['shipping_city'] = $city_args;
    $fields['billing']['billing_city'] = $city_args; // Also change for billing field

    $fields['shipping']['shipping_country'] = array(
        'type'      => 'select',
        'label'     => __('Country', 'egexpress'),
        'options'   => array('EG' => __('Egypt' , 'egexpress'))
    );

    $fields['billing']['billing_country'] = array(
        'type'      => 'select',
        'label'     => __('Country', 'egexpress'),
        'options'   => array('EG' => __('Egypt' , 'egexpress'))
    );
    return $fields;
}
