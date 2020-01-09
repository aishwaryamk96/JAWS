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

	if (!auth_api("mobapp.chat") || !isset($_POST["self_id"]))
		die ("You do not have required priviledges to use this feature.");

	load_module("user");
	load_module("user_enrollment");

	// Get chatrooms from the user_content table
	$user_content = user_content_get($_POST["self_id"], "chatrooms");

	// If the record is not present, add all the sections in the chatrooms list
	if ($user_content === false) {

		// Get all section IDs for the user
		$sections = db_query("SELECT section_id FROM user_enrollment WHERE user_id=".$_POST["self_id"]);
		$section_ids = array();
		foreach ($sections as $section) {

			// Add the user to the list of chatroom participants
			$section_ids[] = $section["section_id"];
			$section_info = section_get_by_id($section["section_id"]);
			$section_info["users"] = $section_info["users"].";".$_POST["self_id"];
			// Update the section
			section_update($section_info);

		}
		// Set the chatroom content for the user
		user_content_set($_POST["self_id"], "chatrooms", implode(";", $section_ids));

		// Update the value for future use
		$user_content["value"] = implode(";", $section_ids);

	}

	// Active chatrooms array
	$response = array();

	// Now explode the chatrooms list
	$section_ids = explode(";", $user_content);
	foreach ($section_ids as $section_id) {

		// Fetch everything about the group chat
		$section = db_query("SELECT section.*, course.name, course_meta.content FROM course_section AS section INNER JOIN course ON course.course_id = section.course_id INNER JOIN course_meta ON course_meta.course_id = course.course_id WHERE section.id=".$section_id)[0];

		// Decode the JSON object of course meta content
		$course_meta_content = json_decode($section["content"], true);

		// Fetch the last message in the group chat
		$chat_last_msg = db_query("SELECT content, create_date FROM user_msg WHERE to_type='course_section' AND to_id=".$section_id." ORDER BY msg_id DESC LIMIT 1;");
		// If there is no message in the group, set message and date to blanks
		if (!isset($chat_last_msg[0])) {

			$chat_last_msg["content"] = "";
			$chat_last_msg["create_date"] = (strlen($section["start_date"]) > 0 ? $section["start_date"] : date("Y-m-d H:i:s"));

		}
		else {
			$chat_last_msg = $chat_last_msg[0];
		}

		$res_user_count = db_query("SELECT enr_id FROM user_enrollment WHERE section_id=".$section_id);

		// Get the count of chatroom participants
		//$user_count = count(explode(";", $section["users"])) + count(explode(";", $section["admin"]));
		$user_count = count($res_user_count);

		// Build the response array
		$response[] = array("conversation_id" => $section["id"], "conversation_pic" => $course_meta_content["img_main_small"], "conversation_name" => $section["name"], "conversation_self_id" => 0, "conversation_other_id" => $section["sis_id"], "conversation_is_batch" => "Y", "conversation_msg" => $chat_last_msg["content"], "conversation_timestamp" => $chat_last_msg["create_date"], "user_count" => $user_count);
	
	}

	// Exited chatrooms array
	$exited_chatrooms = array();

	// Get the list of section IDs of the user
	$res_enr = db_query("SELECT section_id FROM user_enrollment WHERE user_id=".$_POST["self_id"]);
	foreach ($res_enr as $enr) {

		// If the section ID is not present in the list of active chatrooms list, it is an exited chatroom
		if (!in_array($enr["section_id"], $section_ids)) {

			// Fetch everything about the group chat
			$section = db_query("SELECT section.*, course.name, course_meta.content FROM course_section AS section INNER JOIN course ON course.course_id = section.course_id INNER JOIN course_meta ON course_meta.course_id = course.course_id WHERE section.id=".$enr["section_id"])[0];

			// Decode the JSON object of course meta content
			$course_meta_content = json_decode($section["content"], true);

			// Fetch the last message in the group chat
			$chat_last_msg = db_query("SELECT content, create_date FROM user_msg WHERE to_type='course_section' AND to_id=".$enr["section_id"]." ORDER BY msg_id DESC LIMIT 1;");
			// If there is no message in the group, set message and date to blanks
			if (!isset($chat_last_msg[0])) {

				$chat_last_msg["content"] = "";
				$chat_last_msg["create_date"] = (strlen($section["start_date"]) > 0 ? $section["start_date"] : date("Y-m-d H:i:s"));

			}
			else {
				$chat_last_msg = $chat_last_msg[0];
			}

			$res_user_count = db_query("SELECT enr_id FROM user_enrollment WHERE section_id=".$enr["section_id"]);

			// Get the count of chatroom participants
			//$user_count = count(explode(";", $section["users"])) + count(explode(";", $section["admin"]));
			$user_count = count($res_user_count);

			// Build the response array
			$exited_chatrooms[] = array("conversation_id" => $section["id"], "conversation_pic" => $course_meta_content["img_main_small"], "conversation_name" => $section["name"], "conversation_self_id" => 0, "conversation_other_id" => $section["sis_id"], "conversation_is_batch" => "Y", "conversation_msg" => $chat_last_msg["content"], "conversation_timestamp" => $chat_last_msg["create_date"], "user_count" => $user_count);
		
		}

	}

	// Start working on individual chats

	// Get the unique to_ids where the from_id is this user, then get the last msg for each.
	$other_msgs = db_query("SELECT DISTINCT to_id, content, create_date FROM user_msg WHERE to_type='user' AND from_type='user' AND from_id=".$_POST["self_id"]." status='visible'");
	foreach ($other_msgs as $other_msg) {

		$user = user_get_by_id($other_msg["to_id"]);
		$response[] = array("conversation_id" => $user["user_id"], "conversation_pic" => $user["photo_url"], "conversation_name" => $user["name"], "conversation_self_id" => $user["user_id"], "conversation_other_id" => $user["user_id"], "conversation_is_batch" => "N", "conversation_msg" => $other_msg["content"], "conversation_timestamp" => $other_msg["create_date"], "user_count" => 2);
	
	}

	// Get the unique from_ids where the to_id is this user, then get the last msg for each.
	$other_msgs_2 = db_query("SELECT DISTINCT from_id, content FROM user_msg WHERE to_type='user' AND from_type='user' AND to_id=".$_POST["self_id"]." status='visible'");
	foreach ($other_msgs_2 as $other_msg) {

		$user = user_get_by_id($other_msg["from_id"]);
		$response[] = array("conversation_id" => $user["user_id"], "conversation_pic" => $user["photo_url"], "conversation_name" => $user["name"], "conversation_self_id" => $user["user_id"], "conversation_other_id" => $user["user_id"], "conversation_is_batch" => "N", "conversation_msg" => $other_msg["content"], "conversation_timestamp" => $other_msg["create_date"], "user_count" => 2);
	
	}

	// Respond
	die (json_encode(["Items" => [["chatrooms" => $response, "exited_chatrooms" => (count($exited_chatrooms) > 0 ? $exited_chatrooms : "NULL")]], "Count" => count($response), "ScannedCount" => count($response)]));

?>