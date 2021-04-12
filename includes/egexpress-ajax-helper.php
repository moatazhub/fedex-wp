<?php // EgExpress - Core Functionality

function egexpress_return_response($status , $message) {
    $response = array(
        'status' => $status ,
        'message' => $message
    );
   
    echo json_encode($response);
//    return 1;
}

