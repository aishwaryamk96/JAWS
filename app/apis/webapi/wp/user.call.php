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

	// Check Auth
	if (!auth_api("subs.create")) {
		die(header("HTTP/1.1 400"));
	}

	if (empty($_POST["user"]["mobile"]) || empty($_POST["user"]["country_code"])) {
		die(json_encode(["status" => false]));
	}

	unset($_POST["auth"]);

	$data = db_sanitize(json_encode($_POST));
	db_exec("INSERT INTO system_log (source, data) VALUES ('user.call', $data);");

	$phone = $_POST["user"]["phone"];
	$_POST["user"]["phone"] = $_POST["user"]["mobile"];

	load_module("ws_forms");
	ws_forms_log($_POST["form"], $_POST["user"], $_POST["tracking"]);

	if ($_POST["user"]["country_code"] == "+91") {

		load_plugin("exotel");
		connect_call_mcube($_POST["agent_number"], $_POST["user"]["mobile"]);

	}
	else {

		$email = db_sanitize($_POST["user"]["email"]);
		$name = db_sanitize($_POST["user"]["name"]);
		$phone = db_sanitize($phone);
		$meta = db_sanitize(json_encode(["form_name" => $_POST["form"]]));
		$__tr = db_sanitize($_POST["__tr"]);
        //JA-127 START
        $status = 0;
        if(empty($name) && empty($email) && empty($phone))
            $status = 6;
		db_exec("INSERT INTO user_leads_basic (name, email, phone, capture_trigger, meta, __tr,status) VALUES ($name, $email, $phone, 'formsubmit', $meta, $__tr,$status);");
		//JA-127
	}

	die(json_encode(["status" => true]));

?>