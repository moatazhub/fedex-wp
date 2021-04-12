<?php // EgExpress - Settings Page



// disable direct file access
if ( ! defined( 'ABSPATH' ) ) {

    exit;

}

add_filter( 'manage_edit-shop_order_columns', 'egexpress_add_new_order_admin_list_column' );

function egexpress_add_new_order_admin_list_column( $columns ) {
$columns['egexpress_dwb_serial'] = __('DWB serial' , 'egexpress');
$columns['egexpress_dwb_status'] = __('DWB status' , 'egexpress');
return $columns;
}

add_action( 'manage_shop_order_posts_custom_column', 'egexpress_add_new_order_admin_list_column_content' );

function egexpress_add_new_order_admin_list_column_content( $column ) {

    global $post;
    $dwb = egexpress_get_row($post->ID);
    if ( 'egexpress_dwb_serial' === $column ) {
        if(isset($dwb->dwb_sn)) {
            echo "<h4><a class='print_dwb'>$dwb->dwb_sn</a></h4>";
        } else {
            echo "<a href='".admin_url("admin.php?page=egexpress_create_dwb&order_id=$post->ID") ."'>" . __('Create Shipping DWB' ,'egexpress') . "</a>";
        }
    } else if ('egexpress_dwb_status' === $column) {
        if(isset($dwb->dwb_sn)){
            $body = egexpress_get_dwb_status($dwb->dwb_sn);
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
}

add_action( 'woocommerce_admin_order_actions_end', 'egexpress_orders_page_scripts');

function egexpress_orders_page_scripts() {
    // define script url
    $script_url = plugins_url( '/egexpress-orders-page.js', __FILE__ );
    // enqueue script
    wp_enqueue_script( 'egexpress-orders-page', $script_url, array( 'jquery' ) );

    // enqueue script bulk
    $script_url_bulk = plugins_url( '/egexpress-orders-bulk.js', __FILE__ );
    wp_enqueue_script( 'egexpress-orders-bulk', $script_url_bulk, array( 'jquery' ) );

    // create nonce
    $nonce = wp_create_nonce( 'egexpress-orders-page' );

    // create nonce bulk
    $nonce2 = wp_create_nonce( 'egexpress-orders-bulk' );

    // define script
    $script = array( 'nonce' => $nonce );

    // define script bulk
    $script2 = array( 'nonce' => $nonce2 );

    // localize script
    wp_localize_script( 'egexpress-orders-page', 'egexpress_orders_page', $script );
    wp_localize_script( 'egexpress-orders-bulk', 'egexpress_order_bulk', $script2 );
}
function egexpress_get_dwb_status($sn) {

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

function egexpress_orders_page_admin_ajax_handler(){


    check_ajax_referer( 'egexpress-orders-page', 'nonce' );

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
add_action('wp_ajax_egexpress_orders_page_admin_handler' , 'egexpress_orders_page_admin_ajax_handler');

// print bulk AWB
function egexpress_orders_bulk_admin_ajax_handler(){

    $response = array();
    $bulkBody = '';

    check_ajax_referer( 'egexpress-orders-bulk', 'nonce' );


    $orderIds = ($_POST['orderIds']);

    foreach ($orderIds as $orderId){

        // check if dwb created for this order
        $q = egexpress_get_row((int)$orderId);


        if($q) {

            $options = get_option('egexpress_options');
            //Account No. To be provided by Fedex
            $request_body['accountNo'] = $options['accountNo'];

            //Password To be provided by Fedex
            $request_body['password'] = md5($options['password']);

            // Security Key To be provided by Fedex
            $security_key = $options['hashkey'];

            //encrypt security key
            $keyEncrypted = strrev(md5($security_key));

            $request_body['SN'] = $q->dwb_sn;

            //hash key
            $request_body['hashkey'] = trim(sha1($request_body['SN'] . $keyEncrypted));
           // custom_logs($request_body);


            $response = wp_remote_post(EGYPT_EXPRESS_GET_DWB_HTML_URI, array('body' => $request_body));
            $body = wp_remote_retrieve_body($response);

            // insert new <p> element to the document
            $dom = new DomDocument();
            @ $dom->loadHTML($body);

            $ps = $dom->getElementsByTagName('body');
            $first_para = $ps->item(0);

            $html_to_add = '<p>Egypt Express</p>';
            $dom_to_add = new DOMDocument();
            @ $dom_to_add->loadHTML($html_to_add);
            $new_element = $dom_to_add->documentElement;

            $imported_element = $dom->importNode($new_element, true);
            $first_para->parentNode->insertBefore($imported_element, $first_para->nextSibling);

            $body = @ $dom->saveHTML();
           // echo $output;

            ////////////////////////////

            $bulkBody  .= $body ;

            /*

            if (is_wp_error($response)) {
                $error_message = $response->get_error_message();
                $response['status'] = false;
                $response['message'] = $error_message;
                echo json_encode($response);
               // egexpress_return_response(false , $error_message);
            } else {

                $body = wp_remote_retrieve_body($response);

                if (isset($body['response_code'])) {

                    $response['status'] = false;
                    $response['message'] = $body['response_message'];
                    echo json_encode($response);
                   // egexpress_return_response(false , $body['response_message']);

                } else {
                    $bulkBody  .= $body ;
                   // $response['status'] = true;
                  //  $response['message'] = $body;
                  //  echo json_encode($response);
                   // egexpress_return_response(true , $body);
                }


            }
            */
   // die();


       }


    }

    // insert new <p> element to the document
    $dom = new DomDocument();
    @ $dom->loadHTML($bulkBody);

    $ps = $dom->getElementsByTagName('head');
    $first_para = $ps->item(0);

    $html_to_add = '<style type="text/css"> p{page-break-after: always;} </style>';
    $dom_to_add = new DOMDocument();
    @ $dom_to_add->loadHTML($html_to_add);
    $new_element = $dom_to_add->documentElement;

    $imported_element = $dom->importNode($new_element, true);
    $first_para->parentNode->insertBefore($imported_element, $first_para->nextSibling);

    $bulkBody = @ $dom->saveHTML();
    // echo $output;

    ////////////////////////////

    // add bulk message to be print
      $response['status'] = true;
      $response['message'] = $bulkBody;
      echo json_encode($response);
    die();
}
add_action('wp_ajax_egexpress_orders_bulk_admin_handler' , 'egexpress_orders_bulk_admin_ajax_handler');






