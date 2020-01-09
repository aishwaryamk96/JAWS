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

	if (!auth_api("mobapp.chat"))
		die ("You do not have required priviledges to use this feature.");

	load_module("user");
	load_module("user_enrollment");

	$convo_id = $_POST["other_id"];
	$convo_pic = "";
	$convo_name = "";
	if (strcmp(strtolower($_POST["is_batch"]), "y") == 0) {

		$section = section_get_by_sis_id($_POST["other_id"]);

		$res_enr = db_query("SELECT section_id FROM user_enrollment WHERE section_id=".$section["id"]." AND user_id=".$_POST["self_id"]);
		if (!isset($res_enr[0]))
			die (json_encode(["error" => 1, "error_desc" => "Student is not enrolled in the section"]));

		$users = explode(";", $section["users"]);
		$users[] = $_POST["self_id"];
		$section["users"] = trim(implode(";", $users), ";");
		section_update($section);

		$chatrooms = user_content_get($_POST["self_id"], "chatrooms");
		$chatrooms = explode(";", $chatrooms);
		$chatrooms[] = $section["id"];
		$chatrooms = trim(implode(";", $chatrooms), ";");
		user_content_set($_POST["self_id"], "chatrooms", $chatrooms);

		$convo_id = $section["id"];
		$convo_name = db_query("SELECT name FROM course WHERE course_id=".$section["course_id"])[0]["name"];

	}
	else {
		$user = user_get_by_id($_POST["other_id"]);
		$convo_name = $user["name"];
		$convo_pic = $user["photo_url"];
	}

	die (json_encode(["Items" => [["conversation_id" => $convo_id, "conversation_pic" => $convo_pic, "conversation_name" => $convo_name, "conversation_self_id" => $_POST["self_id"], "conversation_other_id" => $_POST["other_id"], "conversation_is_batch" => $_POST["is_batch"], "conversation_msg" => "H r u", "conversation_timestamp" => (new DateTime("now"))->format("Y-m-d H:i:s")]], "count" => 1, "ScannedCount" => 1]));

?>