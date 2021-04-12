<?php

// disable direct file access
if ( ! defined( 'ABSPATH' ) ) {

    exit;

}

define('EGYPT_EXPRESS_SHIPPING_CITIES_URI' , 'http://82.129.197.84:8080/api/shippingCities');
define('EGYPT_EXPRESS_SHIPPING_CALCULATOR_URI' , 'http://82.129.197.84:8080/api/shippingCalculator');
define('EGYPT_EXPRESS_CREATE_DWB_URI' , 'http://82.129.197.84:8080/api/AWBcreate');
define('EGYPT_EXPRESS_GET_DWB_PDF_FILE_URI' , 'http://82.129.197.84:8080/api/AWBpdf');
define('EGYPT_EXPRESS_GET_DWB_HTML_URI' , 'http://82.129.197.84:8080/api/AWBhtml');
define('EGYPT_EXPRESS_GET_DWB_STATUS_URI' , 'http://82.129.197.84:8080/api/AWBstatus');

//define('' , '');