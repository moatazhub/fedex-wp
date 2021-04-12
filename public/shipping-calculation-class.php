<?php

if ( ! defined( 'WPINC' ) ){
    die('security by preventing any direct access to your plugin file');
}
function egypt_express_shipping_method()
{
    if (!class_exists('Egypt_Express_Shipping_Method')) {
        class Egypt_Express_Shipping_Method extends WC_Shipping_Method
        {
            public function __construct()
            {
                $this->id = 'egexpress';
                $this->method_title = __('Egypt Express Shipping', 'egexpress');
                $this->method_description = __('Custom Shipping Method for Egypt Express Shipping', 'egexpress');
                $this->title =  __('Egypt Express Shipping', 'egexpress');
                $this->init();
                $this->enabled = $this->get_option( 'enabled' );
                $this->shipping_city = $this->get_option('shipping_city');
                $this->fallback_price = $this->get_option('fallback_price');
                add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));

            }
            /**
            Load the settings API
             */
            function init()
            {
                $this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings
                $this->init_settings();
            }

            public function init_form_fields() {
                $this->form_fields = array(
                    'enabled' => array(
                        'title'     => __( 'Enable/Disable', 'egexpress' ),
                        'type'       => 'checkbox',
                        'default'     => 'yes'
                    ),
                    'shipping_city' => array(
                        'title'         => __( 'Shipping City', 'egexpress' ),
                        'type'          => 'select',
                        'description'   => __( 'Choose the city you will ship your goods from', 'woocommerce' ),
//                        'default'       => __( 'Custom Shipping Method', 'woocommerce' ),
                        'options'       => egexpress_shipping_cities(),
                        'desc_tip'      => true,
                    ),

                    'fallback_price' => array(
                        'title'         => __( 'Fallback Price', 'egexpress' ),
                        'type'          => 'number',
                        'description'   => __( 'a fallback price in case of any errors happen during calculations or connection errors with api.', 'egexpress' ),
                        'default'       => 50,
                        'desc_tip'      => true,
                    ),
                );
            }


            public function calculate_shipping($package = array())
            { 
                $width = 0;
                $height = 0;
                $length = 0;
                $dimensions = 0;

                $cities = array_flip(egexpress_shipping_cities());



                $weight = 0;
                $cost = 0;
                $country = $package["destination"]["country"];
                foreach ($package['contents'] as $item_id => $values) {
                    $_product = $values['data'];
                    $weight = $weight + $_product->get_weight() * $values['quantity'];


                    //  $length = $length + $_product->get_length() * $values['quantity'];
                    // $width  = $width  + $_product->get_width()  * $values['quantity'];
                    //  $height = $height + $_product->get_heigt()  * $values['quantity'];



                }



                $weight = wc_get_weight($weight, 'kg');
                $response = wp_remote_post(EGYPT_EXPRESS_SHIPPING_CALCULATOR_URI, array('body' => array(
                    'source' => $this->shipping_city,
                    'destination' => $cities[$package["destination"]["city"]],
                    'weight_unit' => 2,
                    'weight' => $weight
                    //'width' => $width,
                    //'height' => $height,
                    //'length' => $length
                    )
                ));
                if (is_wp_error($response)) {
                    $error_message = $response->get_error_message();
                    $rate = array(
                        'id' => $this->id,
                        'label' => $this->title,
                        'cost' => $this->fallback_price
                    );
                } else {
                    $body = json_decode(wp_remote_retrieve_body($response), true);

                    if($body['response_code'] == 200) {
                        $rate = array(
                            'id' => $this->id,
                            'label' => $this->title,
                            'cost' => $body['total_price']
                        );
                    } else {
                        $rate = array(
                            'id' => $this->id,
                            'label' => $this->title,
                            'cost' => $this->fallback_price
                        );
                    }
                }


               


//               $customPrice = 0;
//               if (!$cities[$package["destination"]["city"]] )
//                   $customPrice = 0;
//               else
//                   $customPrice = 80;
//
//
//
//                $rate = array(
//                    'id' => $this->id,
//                    'label' => $this->title,
//                    'cost' => $customPrice
//                );


                $this->add_rate($rate);
            }
        }
    }
}
add_action('woocommerce_shipping_init', 'egypt_express_shipping_method');
function add_egypt_express_shipping_method($methods)
{
    $methods[] = 'Egypt_Express_Shipping_Method';
    return $methods;
}
add_filter('woocommerce_shipping_methods', 'add_egypt_express_shipping_method');

/**
 * Filter the cart template path to use our cart.php template instead of the theme's
 */
function egexpress_locate_template( $template, $template_name, $template_path ) {
    $basename = basename( $template );

    if( $basename == 'shipping-calculator.php' ) {
        $template = trailingslashit( plugin_dir_path( __FILE__ ) ) . 'templates/shipping-calculator.php';
    }
    return $template;
}
add_filter( 'woocommerce_locate_template', 'egexpress_locate_template', 10, 3 );
