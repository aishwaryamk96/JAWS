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
    header('Location: ../index.php');
    die();
}

    require_once(__DIR__ . '/paynimo.php');

	function response_parse($paylink_info) {
        
        $response_token = explode("|",$_POST['msg']);
        $response_token = $response_token[5] ?? false; // using tpsl_txn_id
        if (empty($response_token)) {
            activity_create("critical", "paynimo", "pynm.complete-failure", "paylink_id", $paylink_info["paylink_id"], "user_id", $paylink_info["user_id"], "Paynimo payment_response_parse capture Failed: Empty Token", "logged");
        }

		$api = new Paynimo("", ""); // they dont use api key and secret!!!! huhhh!!!!

		$payment = $api->confirm($paylink_info['currency'], date('d-m-Y'), $response_token);

        $status = $payment['paymentMethod']['paymentTransaction']['statusCode'] ?? false;
        
        if ( !empty( $status ) ) {
            if ( $status === '0396') { //aborted
                activity_create("critical", "paynimo", "pynm.aborted", "paylink_id", $paylink_info["paylink_id"], "user_id", $paylink_info["user_id"], "Paynimo payment_response_parse capture Failed", "logged");
            } else if ( $status === '0398') { //initiated
                activity_create("critical", "paynimo", "pynm.initiated", "paylink_id", $paylink_info["paylink_id"], "user_id", $paylink_info["user_id"], "Paynimo payment_response_parse capture Failed", "logged");
            } else if ( $status === '0399') { // failed
                activity_create("critical", "paynimo", "pynm.failed", "paylink_id", $paylink_info["paylink_id"], "user_id", $paylink_info["user_id"], "Paynimo payment_response_parse capture Failed", "logged");
            }
        } else {
            activity_create("critical", "paynimo", "pynm.complete-failure", "paylink_id", $paylink_info["paylink_id"], "user_id", $paylink_info["user_id"], "Paynimo payment_response_parse capture Failed", "logged");
        }

		$return = array(
			'status' => $status === '0300',
			'reference_id' => $response_token,
			'channel_type' => 'PYNM',
			'channel_info' => 'Paynimo',
			'meta' => json_encode($payment)
		);

		return $return;

    }
    