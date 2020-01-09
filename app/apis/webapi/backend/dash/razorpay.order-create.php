<?php

/*
           8 8888       .8. `8.`888b                 ,8' d888888o.
           8 8888      .888. `8.`888b               ,8'.`8888:' `88.
           8 8888     :88888. `8.`888b             ,8' 8.`8888.   Y8
           8 8888    . `88888. `8.`888b     .b    ,8'  `8.`8888.
           8 8888   .8. `88888. `8.`888b    88b  ,8'    `8.`8888.
           8 8888  .8`8. `88888. `8.`888b .`888b,8'      `8.`8888.
88.        8 8888 .8' `8. `88888. `8.`888b8.`8888'        `8.`8888.
`88.       8 888'.8'   `8. `88888. `8.`888`8.`88'     8b   `8.`8888.
  `88o.    8 88'.888888888. `88888. `8.`8' `8,`'      `8b.  ;8.`8888
    `Y888888 ' .8'       `8. `88888. `8.`   `8'        `Y8888P ,88P'

    JIGSAW ACADEMY WORKFLOW SYSTEM v2
    ---------------------------------
*/

	// Prevent exclusive access
	if (!defined("JAWS")) {
		header("HTTP/1.1 401 Unauthorized.");
		die();
	}

	// Init Session
    auth_session_init();

	// Auth Check - Expecting Session Only !
	if ( !auth_session_is_logged() ) {
		header("HTTP/1.1 401 Unauthorized..");
		die();
	}

	load_plugin("razorpay");
    
    if(empty($_POST)){
        $_POST = json_decode(file_get_contents('php://input'),true);
    }

    $data = array(
        "amount" => $_POST['amount'],
        "currency" => $_POST['currency'],
        "receipt" => $_POST['receipt'],
    );

    $return = createOrder($data);

    if( isset($return["status"]) && $return["status"] == false){
        // order didn't get created
        echo json_encode($return);
    }

    db_query("UPDATE payment_instl SET meta = " . db_sanitize(json_encode($return)) . " WHERE paylink_id = " . db_sanitize($_POST['receipt']) . ";");

    echo json_encode($return);

	exit();

?>