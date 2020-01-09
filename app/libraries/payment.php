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

    JIGSAW ACADEMY WORKFLOW SYSTEM v1
    ---------------------------------
*/

    // Prevent exclusive access
    if (!defined("JAWS")) {
      	header('Location: https://www.jigsawacademy.com');
      	die();
    }

    // This will initiate a payment flow using the payment gateway plugin set in config
    // It also generates a pre-shared key to validate the payment
    function payment_transact($transaction_info, $entity_type, $entity_id, $action, $payment_gateway = "default") {

		$token = psk_generate($entity_type, $entity_id, $action, strval(time()), "", "", false);
		if ($payment_gateway == "default") {
			load_plugin(JAWS_PAYMENT_GATEWAY_CURRENT);
		}
		else {
			load_plugin($payment_gateway);
		}

		transact($transaction_info, $token);
		/*if (!transact($transaction_info, $token)) {

		// Fallback

		}*/

    }

    // This will check the returned pre-shared key to validate the payment return URL after transaction
    // Note : This will expire the pre-shared key associated with the given entity/action
    function payment_validate($entity_type, $entity_id, $action) {

		if (!isset($_GET["validate"])) {
			return false;
		}

		$token = psk_get($entity_type, $entity_id, $action);
		if (!$token) {
			return false;
		}

		psk_expire($entity_type, $entity_id, $action);

		return ((strcmp($_GET["validate"], $token)) == 0);

    }

    // This will parse & return a standardized response from the selected gateway
    function payment_response_parse($payment_gateway_details = [], $paylink_info = []) {

		if ($payment_gateway_details['pg'] == "default") {
			load_plugin(JAWS_PAYMENT_GATEWAY_CURRENT);
		} else {
			load_plugin($payment_gateway_details['pg']);
        }
        
        $response = response_parse($paylink_info);

		// Log gateway response
		if ($response !== false) {
			activity_create("ignore", "gateway.response", "response", $payment_gateway_details['pg'], 0, "reference_id", $response["reference_id"], json_encode($response), "logged");
		}

		$response["pg"] = $payment_gateway_details['pg'];

		return $response;

    }




?>