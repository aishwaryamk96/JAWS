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
	if (!isset($_GET['pay'])) {
		ui_render_msg_front(array(
			"type" => "error",
			"title" => "A problem ran into you :(",
			"header" => "Oops !",
			"text" => "Sorry, but this link seems to be invalid!<br /><br />Please contact our support team for assistance."
		));
		die();
	}

	// Parse Activity
	$act_id = intval($_GET["pay"]);
	$act = db_query('SELECT content FROM system_activity WHERE act_type="epba.paylink" AND activity="use" AND status="pending" AND act_id='.$act_id.' LIMIT 1;');

	// Check Activity
	if (!isset($act[0])) {
		activity_create("low", "epba.paylink.parse", "fail", "act_id", $act_id, "", '', "Activity Record Not Found", "logged");
		ui_render_msg_front(array(
			"type" => "error",
			"title" => "A problem ran into you :(",
			"header" => "Oops !",
			"text" => "Sorry, but this link seems to be invalid!<br /><br />Either it has been used or it has expired. Please contact our support team for assistance."
		));
		exit();
	}
	$data = json_decode($act[0]['content'], true);


	// Prep Payment Transaction Data
	$transaction_info['sum'] = 498;
	$transaction_info['currency'] = 'INR';
	$transaction_info['extra']['desc'] = 'EPBA_INR_2017';

	// Prep more
	$transaction_info['invoice_id'] = $act_id;
	$transaction_info['return_url'] = JAWS_PATH_WEB."/epba/pay/success?pay=".$act_id;
	$transaction_info['extra']['web_id'] = $act_id;

	session_start();
	$_SESSION['user']['name'] = $data['name'];
	$_SESSION['user']['email'] = $data['email'];
	$_SESSION['user']['phone'] = $data['phone'];
	$_SESSION['user']['city'] = $data['city'];

	// Transact
	payment_transact($transaction_info, "epba.paylink", $act_id, "epba.paylink.confirm");

?>
