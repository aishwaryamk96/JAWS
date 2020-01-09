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

	if (!auth_api("jlc.referral")) {
		die ("You do not have required priviledges to use this feature.");
	}

	if (!isset($_POST["email"])) {
		die ("You do not have required priviledges to use this feature.");
	}

	load_module("user");
	load_module("activity");

	$user = user_get_by_email($_POST["email"]);
	if (!$user) {

		$res_act = db_query("SELECT * FROM system_activity WHERE act_type='jlc.user.not_found' AND activity=".db_sanitize($_POST["email"]));
		if (!isset($res_act[0])) {
			activity_create("high", "jlc.user.not_found", $_POST["email"], "", "", "", "", json_encode(["email" => $_POST["email"], "name" => $_POST["name"]]));
		}
		$res_act = db_query("SELECT * FROM system_activity WHERE act_type='jlc.user.not_found' AND activity=".db_sanitize($_POST["email"]));
		die(json_encode(array("token" => psk_generate("system_activity", $res_act[0]["act_id"], "jlc.refer.get"))));

	}

	die(json_encode(array("token" => psk_generate("user", $user["user_id"], "jlc.refer.get"))));

?>