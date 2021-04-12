<?php

// disable direct file access
if ( ! defined( 'ABSPATH' ) ) {

    exit;

}


function egexpress_calculate_create_dwb_hash_and_auth_params($options , $params) {

    $request_body = [];
    //Account No. To be provided by Fedex
    $request_body['accountNo'] = $options['accountNo'];


//Password To be provided by Fedex
    $request_body['password'] = md5($options['password']);

// Security Key To be provided by Fedex
    $security_key = $options['hashkey'];

//Create the 1st part of the hash key
    $keyPart1 = $params['no_of_pieces'] . $params['weight'] . $params['dimensions'];

//Create the 2nd part of the hash key
    $keyPart2 = strrev(md5($security_key));

//Generate the hashKey needed for the transaction.
    $request_body['hashkey'] = trim(sha1($keyPart1 . $keyPart2));
    

    return $request_body;
}
function egexpress_create_dwb_page(){

    // check if user is allowed access
    if ( ! current_user_can( 'manage_options' ) ) return;
    // check on order id exists
    if(! $_GET['order_id'])   die("<h3>" . __('INVALID ORDER ID' , 'egexpress') . "</h3>");
    // check if order id is valid
    $order = new WC_Order($_GET['order_id']);
    if(!$order->get_id()) die("<h3>" . __('INVALID ORDER ID' , 'egexpress') . "</h3>");
    // check if dwb created for this order
    $q = egexpress_get_row($_GET['order_id']);
    if($q) die("<h3>" . __('DWB Already Created for this order' , 'egexpress') . "</h3>");
    ?>

    <div class="wrap">

        <h1><?php echo __('Create DWB' , 'egexpress'); ?></h1>
        <?php  $order = egexpress_create_dwb_form_values($_GET['order_id']); ?>
        <?php echo egexpress_create_dwb_form($order); ?>

    </div>

    <?php

}

function handleForm(array $args) {

}

function egexpress_create_dwb_form($order) {
    ?>
        <style>
            .form-group {
                margin-bottom: 1rem;
            }
            .form-control {
                display: block;
                width: 100%;
                padding: .375rem .75rem;
                font-size: 1rem;
                line-height: 1.5;
                color: #495057;
                background-color: #fff;
                background-clip: padding-box;
                border: 1px solid #ced4da;
                border-radius: .25rem;
                transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
            }
            .bd-form {
                padding: 0.5rem;
                margin-right: 0;
                margin-left: 0;
                border-width: .2rem;
                width: 23%;
                float: left;
            }
            .btn:not(:disabled):not(.disabled) {
                cursor: pointer;
            }
            .btn-primary {
                color: #fff;
                background-color: #007bff;
                border-color: #007bff;
            }
            .btn-warning {
                color: #fff;
                background-color: red;
                border-color: red;
            }
            .btn {
                display: inline-block;
                font-weight: 400;
                text-align: center;
                white-space: nowrap;
                vertical-align: middle;
                -webkit-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                user-select: none;
                border: 1px solid transparent;
                padding: .375rem .75rem;
                font-size: 1rem;
                line-height: 1.5;
                border-radius: .25rem;
                transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
            }
            .btn-xs {
                display: inline-block;
                font-weight: 100;
                text-align: center;
                white-space: nowrap;
                vertical-align: middle;
                -webkit-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                user-select: none;
                border: 1px solid transparent;
                padding: .375rem .75rem;
                font-size: 1rem;
                line-height: 1.5;
                border-radius: .25rem;
                transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
            }
            .egexpress-create-dwb-ajax-response {
                float: left;
            }
            .form-print {
                float: left;
                padding: 10px;
                width: 100%;
            }
            /* 1. Ensure this sits above everything when visible */
            .modal {
                position: absolute;
                z-index: 10000; /* 1 */
                top: 0;
                left: 0;
                visibility: hidden;
                width: 100%;
                height: 100%;
            }

            .modal.is-visible {
                visibility: visible;
            }

            .modal-overlay {
                position: fixed;
                z-index: 10;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: hsla(0, 0%, 0%, 0.5);
                visibility: hidden;
                opacity: 0;
                transition: visibility 0s linear 0.3s, opacity 0.3s;
            }

            .modal.is-visible .modal-overlay {
                opacity: 1;
                visibility: visible;
                transition-delay: 0s;
            }

            .modal-wrapper {
                position: absolute;
                z-index: 9999;
                top: 30%;
                left: 50%;
                width: 32em;
                margin-left: -16em;
                background-color: #fff;
                box-shadow: 0 0 1.5em hsla(0, 0%, 0%, 0.35);
            }

            .modal-transition {
                transition: all 0.3s 0.12s;
                transform: translateY(-10%);
                opacity: 0;
            }

            .modal.is-visible .modal-transition {
                transform: translateY(0);
                opacity: 1;
            }

            .modal-header,
            .modal-content {
                padding: 1em;
            }

            .modal-header {
                position: relative;
                background-color: #fff;
                box-shadow: 0 1px 2px hsla(0, 0%, 0%, 0.06);
                border-bottom: 1px solid #e8e8e8;
            }

            .modal-close {
                position: absolute;
                top: 0;
                right: 0;
                padding: 1em;
                color: #aaa;
                background: none;
                border: 0;
            }

            .modal-close:hover {
                color: #777;
            }

            .modal-heading {
                font-size: 1.125em;
                margin: 0;
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
            }

            .modal-content > *:first-child {
                margin-top: 0;
            }

            .modal-content > *:last-child {
                margin-bottom: 0;
            }
            @media print{
                body{
                    visibility:hidden;
                }
                .egexpress-create-dwb-ajax-form {
                    visibility: visible;
                    width: 100%;
                    margin-left: -15% ;
                }
            }
        </style>
        <form  method="post" class="egexpress-create-dwb-ajax-form">

            <?php
            $egexpress_options = get_option('egexpress_options');

          //  echo $egexpress_options['shipperCity'];

          //  print_r(egexpress_shipping_cities_code_value());
          //  print_r($order->get_items() );


            ?>

            <div class="bd-form">
                <input type="hidden" name="order_id" id="order_id" value="<?php echo $_GET['order_id'] ;?>">
                <h3><?php echo __('Shipper Data' , 'egexpress');?></h3>
                <div class="form-group">
                    <label for="shipper_name"><?php echo __('Shipper name' , 'egexpress'); ?></label>
                    <input type="text" name="shipper_name" required id="shipper_name" class="form-control" placeholder="<?php echo __('Shipper name' , 'egexpress'); ?>" value="<?php echo $egexpress_options['shipperName'];?>"  >
                </div>
                <div class="form-group">
                    <label for="shipper_phone"><?php echo __('Shipper Phone' , 'egexpress'); ?></label>
                    <input type="text" name="shipper_phone" required id="shipper_phone" class="form-control" placeholder="<?php echo __('Shipper Phone' , 'egexpress'); ?>" value="<?php echo $egexpress_options['shipperPhone'];?>" >
                </div>
                <div class="form-group">
                    <label for="shipper_city"><?php echo __('Shipper City' , 'egexpress'); ?></label>
                    <select name="shipper_city" required id="shipper_city" class="form-control">

                        <?php foreach ($order['cities'] as $key => $city) :?>
                                <option value="<?php echo $key;?>" <?php echo $key == $egexpress_options['shipperCity'] ? "selected" : ""; ?> > <?php echo $city; ?> </option>
                            <?php endforeach;?>



                    </select>
                </div>
                <div class="form-group">
                    <label for="shipper_address1"><?php echo __('Shipper Address 1' , 'egexpress'); ?></label>
                    <input type="text" name="shipper_address1" required id="shipper_address1" class="form-control" placeholder="<?php echo __('Shipper Address 1' , 'egexpress'); ?>" value="<?php echo $egexpress_options['shipperAddress1'];?>" >
                </div>
                <div class="form-group">
                    <label for="shipper_address2"><?php echo __('Shipper Address 2' , 'egexpress'); ?></label>
                    <input type="text" name="shipper_address2"  id="shipper_address2" class="form-control" placeholder="<?php echo __('Shipper Address 2' , 'egexpress'); ?>" value="<?php echo $egexpress_options['shipperAddress2'];?>" >
                </div>
            </div>
            <div class="bd-form">
                <h3><?php echo __('Recipient Data' , 'egexpress');?></h3>
                <div class="form-group">
                    <label for="recipient_name"><?php echo __('Recipient Name' , 'egexpress'); ?></label>
                    <input type="text" name="recipient_name" required id="recipient_name" class="form-control" placeholder="<?php echo __('Recipient Name' , 'egexpress'); ?>" value="<?php echo $order['recipient_name'];?>" >
                </div>
                <div class="form-group">
                    <label for="recipient_phone"><?php echo __('Recipient Phone' , 'egexpress'); ?></label>
                    <input type="text" name="recipient_phone" required id="recipient_phone" class="form-control" placeholder="<?php echo __('Recipient Phone' , 'egexpress'); ?>" value="<?php echo $order['recipient_phone']?>">
                </div>
                <div class="form-group">
                    <label for="recipient_city"><?php echo __('Recipient City' , 'egexpress'); ?></label>
                    <select name="recipient_city" id="recipient_city" required class="form-control">
                        <?php foreach ($order['cities'] as $key => $city) :?>
                            <option value="<?php echo $key;?>" <?php echo $order['recipient_city'] == $city ? "selected" : ""; ?> > <?php echo $city; ?> </option>
                        <?php endforeach;?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="recipient_address1"><?php echo __('Recipient Address1' , 'egexpress'); ?></label>
                    <input type="text" name="recipient_address1" required id="recipient_address1" class="form-control" placeholder="<?php echo __('Recipient Address1' , 'egexpress'); ?>" value="<?php echo $order['recipient_address1']; ?>">
                </div>
                <div class="form-group">
                    <label for="recipient_address2"><?php echo __('Recipient Address2' , 'egexpress'); ?></label>
                    <input type="text" name="recipient_address2"  id="recipient_address2" class="form-control" placeholder="<?php echo __('Recipient Address2' , 'egexpress'); ?>" >
                </div>

            </div>
            <div class="bd-form">
                <h3><?php echo __('Goods Data' , 'egexpress');?></h3>
                <div class="form-group">
                    <label for="no_of_pieces"><?php echo __('no of pieces' , 'egexpress'); ?></label>
                    <input type="text" name="no_of_pieces" required id="no_of_pieces" class="form-control" placeholder="<?php echo __('no of pieces' , 'egexpress'); ?>" value="<?php echo $order['no_of_pieces'];?>">
                </div>
                <div class="form-group">
                    <label for="weight"><?php echo __('Weight' , 'egexpress'); ?></label>
                    <input type="text" name="weight" required id="weight" class="form-control" placeholder="<?php echo __('Weight' , 'egexpress'); ?>" value="<?php echo $order['weight'] ?>">
                </div>
                <div class="form-group">
                    <label for="dimensions"><?php echo __('Dimensions' , 'egexpress'); ?></label>
                    <input type="text" name="dimensions"  required id="dimensions" class="form-control" placeholder="<?php echo __('Dimensions' , 'egexpress'); ?>" value="<?php echo $order['dimensions'] ?>">
                </div>
                <div class="form-group">
                    <label for="goods_description"><?php echo __('Goods Description' , 'egexpress'); ?></label>
                    <input type="text" name="goods_description" required id="goods_description" class="form-control" placeholder="<?php echo __('Goods Description' , 'egexpress'); ?>" value="<?php echo $egexpress_options['goodsDescription']; ?>" >
                </div>
                <div class="form-group">
                    <label for="goods_origin_country"><?php echo __('goods origin country' , 'egexpress'); ?></label>
                    <input type="text" name="goods_origin_country" required id="goods_origin_country" class="form-control" placeholder="<?php echo __('goods origin country' , 'egexpress'); ?>" value="<?php echo $egexpress_options['goodsOriginCountry']; ?>" >
                </div>
            </div>
            <div class="bd-form">
                <h3><?php echo __('Payment Details' , 'egexpress');?></h3>
                <div class="form-group">
                    <label for="payment_method"><?php echo __('Payment Method' , 'egexpress'); ?></label>
                    <select name="payment_method" id="payment_method" required placeholder="<?php echo __('Payment Method' , 'egexpress'); ?>" class="form-control">
                            <option value="COD" <?php echo $order['payment_method'] == 'cod' ? "selected" : ""; ?> >  <?php echo __('Cash on delivery' , 'egexpress') ?> </option>
                            <option value="prepaid" > <?php echo __('other' , 'egexpress') ?> </option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="COD_amount"><?php echo __('COD Amount' , 'egexpress'); ?></label>
                    <input type="text" name="COD_amount" <?php echo $order['payment_method'] == 'cod' ? "required" : ""; ?> id="COD_amount" class="form-control" placeholder="<?php echo __('COD Amount' , 'egexpress'); ?>" value="<?php echo $order['payment_method'] == 'cod' ? $order['COD_amount'] : "0"; ?>">
                </div>
                <div class="form-group">
                    <button class="modal-toggle btn btn-primary"><?php echo __('Submit' , 'egexpress'); ?></button>
                </div>




            </div>
            <div class="modal">
                <div class="modal-overlay modal-toggle"></div>
                <div class="modal-wrapper modal-transition">
                    <div class="modal-header">
                        <button class="modal-close modal-toggle"><svg class="icon-close icon" viewBox="0 0 32 32"><use xlink:href="#icon-close"></use></svg></button>
                        <h2 class="modal-heading"><?php echo __('Warning' , 'egexpress') ?></h2>
                    </div>

                    <div class="modal-body">
                        <div class="modal-content">
                            <p><?php echo __('By clicking Submit you won\'t be able to edit any if these data', 'egexpress');?></p>
                            <input type="submit" class="btn-xs btn-primary" placeholder="<?php echo __('Submit' , 'egexpress'); ?>" >
                            <button  class="modal-toggle btn-xs btn-warning"><?php echo __('Close' , 'egexpress')?></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    <div class="form-print hidden">
        <a href="" class="btn btn-primary form-print-btn"><?php echo __('Print' , 'egexpress'); ?></a>
    </div>
    <div class="egexpress-create-dwb-ajax-response"></div>

    <?php
}

function egexpress_create_dwb_form_values($order_id = 0){
    $cities = egexpress_shipping_cities_code_value();

    $order_array['cities'] = $cities;
    if($order_id) {
        $order = new WC_Order($order_id);

        $order_array['recipient_address1'] = $order->get_shipping_address_1();
        $order_array['payment_method'] = $order->get_payment_method();
        if($order_array['payment_method'] == 'cod') {
            $order_array['COD_amount'] = $order->get_total();
        }
        $order_array['recipient_name'] = $order->get_formatted_shipping_full_name();
        $order_array['recipient_phone'] = $order->get_billing_phone();

        $order_array['payment_method'] = $order->get_payment_method();
        $order_array['no_of_pieces'] = $order->get_item_count();
        $order_array['recipient_city'] = $order->get_shipping_city();
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




//        print_r($order->get_items() );

        return $order_array;
    } else {
        return $order_array;
    }

}


//------- AJAX ------- //

// enqueue scripts
function egexpress_create_dwb_scripts( $hook ) {
    // check if our page
    if ( 'admin_page_egexpress_create_dwb' !== $hook ) return;



    // define script url
    $script_url = plugins_url( '/egexpress-create-dwb.js', __FILE__ );
    // enqueue script
    wp_enqueue_script( 'egexpress-create-dwb', $script_url, array( 'jquery' ) );



    // create nonce
    $nonce = wp_create_nonce( 'egexpress-create-dwb' );

    // define script
    $script = array( 'nonce' => $nonce );

    // localize script
    wp_localize_script( 'egexpress-create-dwb', 'egexpress_create_dwb', $script );

}
add_action( 'admin_enqueue_scripts', 'egexpress_create_dwb_scripts' );










// process ajax request
function egexpress_create_dwb_ajax_admin_handler() {

    // check nonce
    check_ajax_referer( 'egexpress-create-dwb', 'nonce' );

    // check user
    if ( ! current_user_can( 'manage_options' ) ) return;

    $row = egexpress_get_row($_POST['order_id']);
    if(isset($row->id)){
        egexpress_return_response(false , egexpress_display_message("This oreder already have dwb" , 'error'));
        die();
    }

    $egexpress_options = get_option('egexpress_options');
    
    $request_body = egexpress_calculate_create_dwb_hash_and_auth_params($egexpress_options , $_POST);
    unset($_POST['nonce']);
    unset($_POST['action']);
    $request_body = array_merge($request_body , $_POST);


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
            egexpress_create_database_row($_POST['order_id'] , $body['SN']);
            egexpress_return_response(true , [
                'message' => egexpress_display_message($body['response_message'] , 'updated') ,
                'html'    => egexpress_get_dwb_html($body['SN'] , $egexpress_options)
            ]);

            // Status without the "wc-" prefix
            $order = wc_get_order( $_POST['order_id'] );
            $order->update_status( 'invoiced2' );

        } else {
             egexpress_return_response(false , egexpress_display_message($body['response_message'] , 'error'));
        }

    }




    // end processing
    wp_die();

}
function egexpress_display_message( $message , $class){
    return "
        <div class=\"$class notice\">
            <p>$message</p>
        </div>
    ";
}

function egexpress_get_dwb_html($sn , $options) {
    $request_body['SN'] = $sn;
    $request_body['accountNo'] = $options['accountNo'];
    $request_body['password'] = md5($options['password']);
        //encrypt security key
    $keyEncrypted = strrev(md5($options['hashkey']));
//hash key
    $request_body['hashkey'] = trim(sha1($sn . $keyEncrypted));
    $response = wp_remote_post(EGYPT_EXPRESS_GET_DWB_HTML_URI, array('body' => $request_body));
//    return json_encode($request_body);
    if (is_wp_error($response)) {
        $error_message = $response->get_error_message();
        return $error_message;
    } else {
        $body = wp_remote_retrieve_body($response);
        return $body;
    }
}
// ajax hook for logged-in users: wp_ajax_{action}
add_action( 'wp_ajax_egexpress_create_dwb_ajax_admin_handler', 'egexpress_create_dwb_ajax_admin_handler' );





