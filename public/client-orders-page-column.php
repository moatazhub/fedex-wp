<?php // EgExpress - Settings Page



// disable direct file access
if ( ! defined( 'ABSPATH' ) ) {

    exit;

}

add_filter( 'woocommerce_my_account_my_orders_columns', 'egexpress_add_new_order_client_list_column' );

function egexpress_add_new_order_client_list_column( $columns ) {
    $columns['egexpress_dwb_serial'] = __('DWB serial' , 'egexpress');
    $columns['egexpress_dwb_status'] = __('DWB status' , 'egexpress');
    return $columns;
}

add_action( 'woocommerce_my_account_my_orders_column_egexpress_dwb_serial', 'egexpress_my_orders_dwb_serial_column' );
add_action( 'woocommerce_my_account_my_orders_column_egexpress_dwb_status', 'egexpress_my_orders_dwb_status_column' );

function egexpress_my_orders_dwb_serial_column( $order ) {
    $dwb = egexpress_get_row($order->get_id());
    if(isset($dwb->dwb_sn)) {
        echo "<h5><a class='print_dwb'>$dwb->dwb_sn</a></h5>";
    } else {
        echo __("Not Shipped" , 'egexpress');
    }
}
function egexpress_my_orders_dwb_status_column($order){
    $dwb = egexpress_get_row($order->get_id());
    if(isset($dwb->dwb_sn)){
        $body = egexpress_client_get_dwb_status($dwb->dwb_sn);
        $body = json_decode($body);
        if(isset($body->response_code)){
            if($body->response_code == 200) {
                echo $body->status[0]->status;
            }else {
                echo $body->response_message;
            }
        } else {
            echo $body;
        }
    } else {
        echo "N/A";
    }
}
add_action( 'woocommerce_after_account_orders', 'egexpress_client_orders_client_page_scripts');

function egexpress_client_orders_client_page_scripts() {
    // define script url
    $script_url = plugins_url( '/js/client-orders-page-column.js', __FILE__ );
    // enqueue script
    wp_enqueue_script( 'client-orders-page-column', $script_url, array( 'jquery' ) );



    // create nonce
    $nonce = wp_create_nonce( 'client-orders-page-column' );

    // define script
    $script = array(
        'nonce' => $nonce  ,
        'ajaxurl' => admin_url( 'admin-ajax.php' )
        );

    // localize script
    wp_localize_script( 'client-orders-page-column', 'egexpress_client_orders_page', $script );
}
function egexpress_client_get_dwb_status($sn) {

    $options = get_option('egexpress_options');
    //Account No. To be provided by Fedex
    $request_body['accountNo'] = $options['accountNo'];

    //Password To be provided by Fedex
    $request_body['password'] = md5($options['password']);

    $keyEncrypted = strrev(md5($options['hashkey']));

    $request_body['SN'] = $sn;

    //hash key
    $request_body['hashkey'] = trim(sha1($keyEncrypted));


    $response = wp_remote_post(EGYPT_EXPRESS_GET_DWB_STATUS_URI, array('body' => $request_body));

    if (is_wp_error($response)) {
        $error_message = $response->get_error_message();
        return $error_message;
    } else {
        $body = wp_remote_retrieve_body($response);
        return $body;
    }
}
function egexpress_orders_page_client_ajax_handler(){
    check_ajax_referer( 'client-orders-page-column', 'nonce' );
    $options = get_option('egexpress_options');
    //Account No. To be provided by Fedex
    $request_body['accountNo'] = $options['accountNo'];

    //Password To be provided by Fedex
    $request_body['password'] = md5($options['password']);

    // Security Key To be provided by Fedex
    $security_key = $options['hashkey'];

    //DWB Serial Number
    $request_body['SN'] = $_POST['dwb_sn'];
    //encrypt security key
    $keyEncrypted = strrev(md5($security_key));

    //hash key
    $request_body['hashkey'] = trim(sha1($request_body['SN'] . $keyEncrypted));


    $response = wp_remote_post(EGYPT_EXPRESS_GET_DWB_HTML_URI, array('body' => $request_body));
    if (is_wp_error($response)) {
        $error_message = $response->get_error_message();
        egexpress_return_response(false , $error_message);
    } else {
        $body = wp_remote_retrieve_body($response);

        if (isset($body['response_code'])) {
            egexpress_return_response(false , $body['response_message']);
        } else {
            egexpress_return_response(true , $body);
        }
    }
    die();

}
add_action('wp_ajax_egexpress_orders_page_client_handler' , 'egexpress_orders_page_client_ajax_handler');