<?php
/*
Plugin Name:  Egypt Express
Description:  Egypt Express Shipping plugin for woocommerce
Plugin URI :   https://profiles.wordpress.org/specialk
Author:       tqniat lab
Author URI:   https://www.tqniat.com
Version:      1.0
Text Domain:  egexpress
Domain Path:  /languages
License:      GPL v2 or later
License URI:  https://www.gnu.org/licenses/gpl-2.0.txt
*/



// disable direct file access
if ( ! defined( 'ABSPATH' ) ) {

	exit;

}


if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {

// load text domain
    function egexpress_load_textdomain()
    {

        load_plugin_textdomain('egexpress', false, plugin_dir_path(__FILE__) . 'languages/');

    }

    add_action('plugins_loaded', 'egexpress_load_textdomain');

    function egexpress_shipping_cities()
    {
        $response = wp_safe_remote_get(EGYPT_EXPRESS_SHIPPING_CITIES_URI);
        if (is_wp_error($response)) {
            die($response->get_error_message());
        } else {
            $cities = json_decode(wp_remote_retrieve_body($response))->cities;
            $cities_return = array();
            foreach ($cities as $city) {
                $cities_return[$city->id] = $city->city_en;
            }
            return $cities_return;
        }
    }
    function egexpress_shipping_cities_value_only(){
        $response = wp_safe_remote_get(EGYPT_EXPRESS_SHIPPING_CITIES_URI);
        if (is_wp_error($response)) {
            die($response->get_error_message());
        } else {
            $cities = json_decode(wp_remote_retrieve_body($response))->cities;
            $cities_return = array();
            foreach ($cities as $city) {
                $cities_return[$city->city_en] = $city->city_en;
            }
            return $cities_return;
        }
    }
    function egexpress_shipping_cities_js()
    {
        $response = wp_safe_remote_get(EGYPT_EXPRESS_SHIPPING_CITIES_URI);
        if (is_wp_error($response)) {
            die($response->get_error_message());
        } else {
            $cities = json_decode(wp_remote_retrieve_body($response))->cities;
            $cities_return = array();
            foreach ($cities as $city) {
                $cities_return[] = array(
                    'id' => $city->id,
                    'name' => $city->city_en
                );
            }
            return $cities_return;
        }
    }
    function egexpress_shipping_cities_code_value()
    {
        $response = wp_safe_remote_get(EGYPT_EXPRESS_SHIPPING_CITIES_URI);
        if (is_wp_error($response)) {
            die($response->get_error_message());
        } else {
            $cities = json_decode(wp_remote_retrieve_body($response))->cities;
            $cities_return = array();
            foreach ($cities as $city) {
                $cities_return[$city->code] = $city->city_en;
            }
            return $cities_return;
        }
    }

// include plugin dependencies: public
    require_once plugin_dir_path(__FILE__) . 'public/api-endpoints.php';
    require_once plugin_dir_path(__FILE__) . 'public/shipping-calculation-init.php';
    require_once plugin_dir_path(__FILE__) . 'public/shipping-calculation-class.php';
    require_once plugin_dir_path(__FILE__) . 'public/client-orders-page-column.php';



// include plugin dependencies: admin and public
    require_once plugin_dir_path(__FILE__) . 'includes/database.php';
    require_once plugin_dir_path(__FILE__) . 'includes/egexpress-ajax-helper.php';

// include plugin dependencies: admin only
    if (is_admin()) {
        egexpress_database_init();
        require_once plugin_dir_path(__FILE__) . 'admin/admin-menu.php';
        require_once plugin_dir_path(__FILE__) . 'admin/settings-page.php';
        require_once plugin_dir_path(__FILE__) . 'admin/create-dwb-page.php';
        require_once plugin_dir_path(__FILE__) . 'admin/settings-register.php';
        require_once plugin_dir_path(__FILE__) . 'admin/settings-callbacks.php';
        require_once plugin_dir_path(__FILE__) . 'admin/settings-validate.php';
        require_once plugin_dir_path(__FILE__) . 'admin/orders-page-column.php';

    }

// default plugin options
    function egexpress_options_default()
    {
        //TODO::add default values
        return array(

        );

    }


}


function register_my_invoiced_order_status() {
    /*
    register_post_status( 'wc-invoiced', array(
            'label' => _x( 'Print AWB', 'Order Status', 'egexpress' ),
            'public' => true,
            'exclude_from_search' => false,
            'show_in_all_admin_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop( 'Print AWB <span class="count">(%s)</span>', 'Print AWB <span class="count">(%s)</span>', 'egexpress' )
        )
    );
*/
    register_post_status( 'wc-invoiced2', array(
            'label' => _x( 'Fedex Processing', 'Order Status', 'egexpress' ),
            'public' => true,
            'exclude_from_search' => false,
            'show_in_all_admin_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop( 'Fedex Processing <span class="count">(%s)</span>', 'Fedex Processing <span class="count">(%s)</span>', 'egexpress' )
        )
    );
}

add_action( 'init', 'register_my_invoiced_order_status' );

function my_invoiced_order_status( $order_statuses ){
  //  $order_statuses['wc-invoiced'] = _x( 'Print AWB', 'Order Status', 'egexpress' );
    $order_statuses['wc-invoiced2'] = _x( 'Fedex Processing', 'Order Status', 'egexpress' );
    return $order_statuses;

}
add_filter( 'wc_order_statuses', 'my_invoiced_order_status' );



//add_action( 'woocommerce_thankyou', 'bbloomer_thankyou_change_order_status' );

function bbloomer_thankyou_change_order_status( $order_id ){
    if( ! $order_id ) return;
    $order = wc_get_order( $order_id );

    // Status without the "wc-" prefix
    $order->update_status( 'invoiced' );
}



// Adding to admin order list bulk dropdown a custom action 'custom_downloads'
add_filter( 'bulk_actions-edit-shop_order', 'downloads_bulk_actions_edit_product', 20, 1 );
function downloads_bulk_actions_edit_product( $actions ) {
    $actions['write_downloads'] = __( 'Change status to Fedex Processing', 'woocommerce' );
    $actions['print_bulk'] = __( 'Print bulk invoices', 'woocommerce' );
    return $actions;
}

// Make the action from selected orders
add_filter( 'handle_bulk_actions-edit-shop_order', 'downloads_handle_bulk_action_edit_shop_order', 10, 3 );
function downloads_handle_bulk_action_edit_shop_order( $redirect_to, $action, $post_ids ) {
   // custom_logs("enter...");
   // custom_logs($action);

    if ( $action !== 'write_downloads')
        return $redirect_to; // Exit
    //custom_logs("enter2...");
    $processed_ids = array();


    foreach ( $post_ids as $order_id ) {

        //validate
        // check if dwb created for this order
        $q = egexpress_get_row($order_id);
        if($q) continue; //die("<h3>" . __('DWB Already Created for this order' , 'egexpress') . "</h3>");


        $order = wc_get_order( $order_id );

        $order_array['recipient_address1'] = $order->get_shipping_address_1();
        $order_array['recipient_address2'] = $order->get_shipping_address_2();
        $order_array['payment_method'] = $order->get_payment_method();
        if($order_array['payment_method'] == 'cod') {
            $order_array['COD_amount'] = $order->get_total();
        }
        $order_array['recipient_name'] = $order->get_formatted_shipping_full_name();
        $order_array['recipient_phone'] = $order->get_billing_phone();

        $order_array['payment_method'] = $order->get_payment_method();
        $order_array['no_of_pieces'] = $order->get_item_count();

        //to get city code
        $cities = egexpress_shipping_cities_code_value();
        $recipient_city = $order->get_shipping_city();
        $cityCode = array_search ($recipient_city, $cities);

        $order_array['recipient_city'] =$cityCode;
        $order_array['weight'] = 0;

        $order_array['width'] = 0;
        $order_array['height'] = 0;
        $order_array['length'] = 0;
        $order_array['dimensions'] = 0;

        foreach ($order->get_items() as $item) {
            $p = wc_get_product($item->get_product_id());
            $order_array['weight'] += $p->get_weight() * $item->get_quantity();

            $order_array['width']  = $p->get_width();
            $order_array['height'] = $p->get_height();
            $order_array['length'] = $p->get_length();
            $order_array['dimensions'] += ($order_array['width']*$order_array['height']*$order_array['length']) * $item->get_quantity();

            //  $order_array['dimensions'] += $order_array['dimensions'] ;


        }
        $order_array['weight'] = wc_get_weight($order_array['weight'], 'kg');

        if($order_array['dimensions'] === 0 ) {
            $order_array['dimensions'] = 1;
        }

        unset($order_array['width']);
        unset($order_array['height']);
        unset($order_array['length']);


        $options = get_option('egexpress_options');

        $request_body = [];
        $request_body['shipper_name'] = $options['shipperName'];
        $request_body['shipper_phone'] = $options['shipperPhone'];
        $request_body['shipper_city'] = $options['shipperCity'];
        $request_body['shipper_address1'] = $options['shipperAddress1'];
        $request_body['shipper_address2'] = $options['shipperAddress2'];
        $request_body['goods_description'] = $options['goodsDescription'];
        $request_body['goods_origin_country'] = $options['goodsOriginCountry'];

        //Account No. To be provided by Fedex
        $request_body['accountNo'] = $options['accountNo'];

        //Password To be provided by Fedex
        $request_body['password'] = md5($options['password']);
        // Security Key To be provided by Fedex
        $security_key = $options['hashkey'];
        //Create the 1st part of the hash key
        $keyPart1 = $order_array['no_of_pieces'] . $order_array['weight'] . $order_array['dimensions'];
        //Create the 2nd part of the hash key
        $keyPart2 = strrev(md5($security_key));
        //Generate the hashKey needed for the transaction.
        $request_body['hashkey'] = trim(sha1($keyPart1 . $keyPart2));

        $request_body = array_merge($order_array, $request_body);
        $request_body['order_id'] =  $order_id ;
        $request_body['payment_method'] =  "COD";

        $response = wp_remote_post(EGYPT_EXPRESS_CREATE_DWB_URI, array('body' => $request_body));

        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();

        } else {
            $body = json_decode(wp_remote_retrieve_body($response), true);

        }

        if ( isset( $error_message ) ) {

            egexpress_return_response(false , egexpress_display_message("something went wrong :  $error_message " , 'error'));
            die();

        } else {

            if($body['response_code'] == 200){
                egexpress_create_database_row($order_id , $body['SN']);
                egexpress_return_response(true , [
                    'message' => egexpress_display_message($body['response_message'] , 'updated') ,
                    'html'    => egexpress_get_dwb_html($body['SN'] , $egexpress_options)
                ]);

                // Status without the "wc-" prefix
                $order->update_status( 'invoiced2' );

            } else {
                egexpress_return_response(false , egexpress_display_message($body['response_message'] , 'error'));
            }

        }



        $processed_ids[] = $order_id;
    }



    return $redirect_to = add_query_arg( array(
        'write_downloads' => '1',
        'processed_count' => count( $processed_ids ),
        'processed_ids' => implode( ',', $processed_ids ),
    ), $redirect_to );

    // end processing
    wp_die();
}



// The results notice from bulk action on orders
add_action( 'admin_notices', 'downloads_bulk_action_admin_notice' );
function downloads_bulk_action_admin_notice() {
    if ( empty( $_REQUEST['write_downloads'] ) ) return; // Exit

    $count = intval( $_REQUEST['processed_count'] );

    printf( '<div id="message" class="updated fade"><p>' .
        _n( 'Processed %s Orders from Ship with Fedex to Fedex processing.',
            'Processed %s Orders from Ship with Fedex to Fedex processing.',
            $count,
            'write_downloads'
        ) . '</p></div>', $count );
}









