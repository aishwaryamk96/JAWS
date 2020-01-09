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
		header("HTTP/1.1 401 Unauthorized");
		die();
	}

	// Init Session
	auth_session_init();

	// Auth Check - Expecting Session Only !
	if ((!auth_session_is_logged()) || (!auth_session_is_allowed("dash"))) {
		header("HTTP/1.1 401 Unauthorized");
		die();
	}

	// Prep
	load_library("setting");


	//get data
	$agent_default_discount_max = setting_get('payment.discount_max.default');
	$instl_criteria = setting_get("payment.instl.criteria");
	$tax_rate = json_decode(setting_get("payment.tax.percentage"),true);
	$instalment_fees = json_decode(setting_get("payment.instl.fee"),true);
	$instalment_date = setting_get("payment.instl.due.days");
	$allow_paymode = auth_session_is_allowed("payment.pay_mode.set");

	// Output
	echo json_encode(
		[
			"agent_default_discount_max" => $agent_default_discount_max,
			"instl_criteria" => $instl_criteria,
			"tax_rate" => $tax_rate,
			"instalment_fees" => $instalment_fees,
			"instalment_date" => $instalment_date,
			"allow_paymode" => $allow_paymode,
			"max_due_date" => auth_session_is_allowed("pm") ? 90 : 45,
			"superuser" => auth_session_is_allowed("pm")
		]
	);

	// Done
	exit();

?>
