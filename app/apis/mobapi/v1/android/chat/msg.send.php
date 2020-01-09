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
		header('Location: http://www.jigsawacademy.com');
		die();
	}

	load_plugin("mobile_app");

	// Authenticate the call and check if all the desired params are available or not
	if (!auth_api("mobapp.chat") || !isset($_POST["chat_room_id"]) || !isset($_POST["self_id"]) || !isset($_POST["is_batch"]) || !isset($_POST["other_id"]) || !isset($_POST["message"]))
		die("You do not have required privileges to use this feature.");

	// Check if the receiver is course_section or an individual user
	if (strtolower($_POST["is_batch"]) == "y") {

		// Check if the user is enrolled in the section or not
		$res_enr = db_query("SELECT * FROM user_enrollment WHERE user_id=".$_POST["self_id"]." AND section_id=".$_POST["chat_room_id"]);
		if (!isset($res_enr[0])) {

			$res_admins = db_query("SELECT admin FROM course_section WHERE id=".$_POST["chat_room_id"])[0];
			if (!in_array($_POST["self_id"], explode(";", $res_admins["admin"]))) {
				die(json_encode(["error" => 1, "error_desc" => "Student is not enrolled in the section"]));
			}

		}

		$to_type = "course_section";

	}
	else {
		$to_type = "user";
	}

	$to = $_POST["chat_room_id"];

	if (!isset($_POST["msg_reply_id"])) {
		$_POST["msg_reply_id"] = "";
	}

	// From the plugin
	$mobileApp = new MobileApp();
	$objects = $mobileApp->msg_send($to_type, $to, $_POST["self_id"], $_POST["message"], $_POST["msg_reply_id"]);

	die(json_encode(["Items" => [$objects["android"]->getMessage()], "Count" => 1, "ScannedCount" => 1]));

?>