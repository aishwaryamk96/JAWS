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

	// Params Check
	if ((!isset($_GET['name'])) ||
		(!isset($_GET['email'])) ||/*
		(!isset($_GET['phone'])) ||
		(!isset($_GET['empid'])) ||
		(!isset($_GET['office'])) ||
		(!isset($_GET['city'])) ||
		(!isset($_GET['course_code'])) ||*/
		(!isset($_GET['paymode']))) {

		ui_render_msg_front(array(
			"type" => "error",
			"title" => "A problem ran into you :(",
			"header" => "Oops !",
			"text" => "Sorry, but this link seems to be invalid!<br /><br />Please contact our support team for assistance."
		));

		exit();
	}

	$allowed_email_ids = file("external/temp/nokia.csv", FILE_IGNORE_NEW_LINES);
	// $allowed_email_ids = array('test@nokia.com');

	if (!in_array($_GET["email"], $allowed_email_ids)) {

		ui_render_msg_front(array(
			"type" => "error",
			"title" => "A problem ran into you :(",
			"header" => "Oops !",
			"text" => "Sorry, but this link seems to be invalid!<br /><br />Please contact our support team for assistance."
		));

		exit();

	}

	// Prep Payment Transaction Data
	$transaction_info['sum'] = (strtoupper($_GET['paymode']) === 'INR') ? 2950: 40;
	$transaction_info['currency'] = strtoupper($_GET['paymode']);
	$transaction_info['extra']['desc'] = 'NOKIA_'.(strtoupper($_GET['paymode']) === 'INR') ? 'INR_2950': 'USD_40';

	// Create Data to be stored
	$data = [
		"name" => $_GET["name"],
		"email" => $_GET["email"],/*
		"phone" => $_GET["phone"],
		"email_alt" => $_GET["email_alt"],
		"course_code" => $_GET["course_code"],
		"paymode" => strtoupper($_GET["paymode"]),
		"city" => $_GET["city"],
		"country" => $_GET["country"] ?? 'India',
		"office" => $_GET["office"],
		"empid" => $_GET["empid"],*/
		"sum" => $transaction_info['sum']
	];

	// Store in Activity
	$act_id = activity_create('high','nokia.paylink','use','','','','',json_encode($data),'pending');

	// Send Email Here ???

	// Prep more
	$transaction_info['invoice_id'] = $act_id;
	$transaction_info['return_url'] = JAWS_PATH_WEB."/nokia/pay/success?pay=".$act_id;
	$transaction_info['extra']['web_id'] = $act_id;

	session_start();
	$_SESSION['user']['name'] = $data['name'];
	$_SESSION['user']['email'] = $data['email'];
	$_SESSION['user']['phone'] = $data['phone'];
	$_SESSION['user']['city'] = $data['city'];

	// Transact
	payment_transact($transaction_info, "nokia.paylink", $act_id, "nokia.paylink.confirm");

?>