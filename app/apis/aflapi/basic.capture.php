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

	// This will capture a basic lead either directly by user_id or a token
	// Note: persistence is yet to be implemented on this API !!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	// -------------------

	// Auth check
	if (!auth_api ("leads.basic")) {
		die ("You do not have sufficient privileges to perform this operation");
	}

	// Load stuff
	load_module("leads");

	// Check
	if (!isset($_POST["lead_params"])) {
		die();
	}
	if (!isset($_POST["mode"])) {
		die();
	}

	// Parse
	$id_or_token = "";
	if (strcmp($_POST["mode"], "id") == 0) {

		if (!isset($_POST["user_id"])) {
			die();
		}

		$id_or_token = $_POST["user_id"];

	}
	else if (strcmp($_POST["mode"], "token") == 0) {

		if (isset($_POST["token"])) {
			$id_or_token = $_POST["token"];
		}

	}
	else {
		die();
	}

	// post data check
	if(empty($_POST["post_data"])) {
		$_POST["post_data"] = array();
	}

	// Exec
	echo json_encode(leads_basic_capture($_POST["lead_params"], $_POST["mode"], $id_or_token, $_POST["post_data"]));

	exit();

?>

