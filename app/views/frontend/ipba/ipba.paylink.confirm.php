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
		header('Location: https://www.jigsawacademy.com');
		die();
	}

	// Load stuff
	load_module("ui");
	load_library('payment');

	// Check
	if (!isset($_GET["pay"])) {
		ui_render_msg_front(array(
			"type" => "error",
			"title" => "A problem ran into you :(",
			"header" => "Oops !",
			"text" => "Sorry, but this link seems to be invalid!<br /><br />Please contact our support team for assistance."
		));
		exit();
	}

	// Parse Activity
	$act = db_query('SELECT content FROM system_activity WHERE act_type="ipba.paylink" AND activity="use" AND status="pending" AND act_id='.intval($_GET["pay"]).' LIMIT 1;');

	// Check Activity
	if (!isset($act[0])) {
		activity_create("low", "ipba.paylink.confirm", "fail", "act_id", $_GET["pay"], "", '', "Activity Record Not Found", "logged");
		ui_render_msg_front(array(
			"type" => "error",
			"title" => "A problem ran into you :(",
			"header" => "Oops !",
			"text" => "Sorry, but this link seems to be invalid!<br /><br />Either it has been used or it has expired. Please contact our support team for assistance."
		));
		exit();
	}
	$data = json_decode($act[0]['content'], true);

	// Parse transaction response
	$transaction_response = payment_response_parse();

	// Transaction - No response
	if ($transaction_response === false) {
		activity_create("low", "ipba.paylink.confirm", "fail", "act_id", $_GET["pay"], "", '', "No Transaction Response", "logged");
		ui_render_msg_front(array(
			"type" => "error",
			"title" => "A problem ran into you :(",
			"header" => "Oops !",
			"text" => "Sorry, but this link seems to be invalid!<br /><br />Either it has been used or it has expired. Please contact our support team for assistance."
		));
		exit();
	}

	// Validate Response
	if (!payment_validate("ipba.paylink", intval($_GET["pay"]), "ipba.paylink.confirm")) {
		activity_create("low", "ipba.paylink.confirm", "fail", "act_id", $_GET["pay"], "", "", "PSK Mismatch", "logged");
		ui_render_msg_front(array(
			"type" => "error",
			"title" => "A problem ran into you :(",
			"header" => "Oops !",
			"text" => "Sorry, but this link seems to be invalid!<br /><br />Either it has been used or it has expired. Please contact our support team for assistance."
		));
		exit();
	}

	// Check response
	if (!$transaction_response["status"]) {
		activity_create("high", "ipba.paylink.confirm", "fail", "act_id", $_GET["pay"], "", '', "Gateway Cancelled/Failed", "logged");
		ui_render_msg_front(array(
			"type" => "error",
			"title" => "A problem ran into you :(",
			"header" => "Oops !",
			"text" => "Sorry, but your payment transaction seems to have failed! Do not worry if you have been charged for this - our support team will help you out."
		));
		exit();
	}

	// Success - Update activity
	db_exec("UPDATE system_activity SET status='executed' WHERE act_id=".intval($_GET["pay"]).";");

	// Email Student
	load_library('email');
	send_email('ipba.success', ['to' => $data['email']], [
    		'fname' => substr($data['name'], 0, ((strpos($data['name'], " ") !== false) ? strpos($data['name'], " ") : strlen($data['name']))),
    		'course' => [
    				'name' => 'IPBA thing'
    				],
    		'payment' => [
    				'sum' => $data['sum'],
    				'currency' => strtoupper($data['paymode'])
    			]
	]);

	// Render MSG
	ui_render_msg_front(array(
		"type" => "info",
		"title" => "Payment Complete",
		"header" => "Success",
		"text" => "We have recieved the payment towards your enrollment.<br/><br/>Your account on the Jigsaw Learning Center is being created. Our corporate support team will contact you within 24 hours."
	));
	exit();

?>