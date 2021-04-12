<?php // EgExpress - Core Functionality



// disable direct file access
if ( ! defined( 'ABSPATH' ) ) {

    exit;

}

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

function egexpress_database_init(){
    global $wpdb;

    $table_name = $wpdb->prefix . "egexpress";

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
      id bigint(20) NOT NULL AUTO_INCREMENT,
      order_id bigint(20) NOT NULL,
      dwb_sn VARCHAR (100) NOT NULL,
      PRIMARY KEY  (id)
    ) $charset_collate;";

    dbDelta( $sql );
}

function egexpress_create_database_row($order_id , $dwb_sn) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'egexpress';
    return $wpdb->insert(
        $table_name,
        array(
            'order_id' => $order_id,
            'dwb_sn' => $dwb_sn,
        )
    );

}

function egexpress_get_row($order_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'egexpress';
    $q = $wpdb->get_row("SELECT * FROM $table_name
		        WHERE order_id = $order_id
		        LIMIT 1");
    return $q;
}

