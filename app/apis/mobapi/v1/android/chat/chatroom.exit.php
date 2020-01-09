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

	if (!auth_api("mobapp.chat") || !isset($_POST["self_id"]) || !isset($_POST["chat_room_id"]) || !isset($_POST["other_id"]))
		die ("You do not have required priviledges to use this feature.");

	load_module("user");
	load_module("user_enrollment");

	// We are talking about sectional chats
	if ($_POST["chat_room_id"] != $_POST["other_id"])
	{

		// Remove from user_content table
		$chatrooms = user_content_get($_POST["self_id"], "chatrooms");

		if ($chatrooms !== false)
		{
			$chatrooms = explode(";", $chatrooms);
			$chatrooms_new = array();
			foreach ($chatrooms as $chatroom)
			{
				if ($chatroom != $_POST["chat_room_id"])
					$chatrooms_new[] = $chatroom;
			}

			user_content_set($_POST["self_id"], "chatrooms", implode(";", $chatrooms_new));
		}

		// Remove from course_section table
		$course_section = section_get_by_id($_POST["chat_room_id"]);
		$users = explode(";", $course_section["users"]);
		$users_new = array();
		foreach ($users as $user)
		{
			if ($user != $_POST["self_id"])
				$users_new[] = $user;
		}
		$course_section["users"] = $users_new;
		section_update($course_section);
	}
	// We are talking about individual chats
	else
	{
		db_query("UPDATE user_msg SET status='removed', expire_date=".db_sanitize(date("Y-m-d H:i:s"))." WHERE from_type='user' AND to_type='user' AND (from_id=".$_POST["self_id"]." AND to_id=".$_POST["chat_room_id"].") OR (from_id=".$_POST["chat_room_id"]." AND to_id=".$_POST["self_id"].");");
	}

	die(json_encode(["Items" => [["exit_id" => $_POST["chat_room_id"], "self_id" => $_POST["self_id"], "other_id" => $_POST["other_id"], "exit_timestamp" => date("Y-m-d H:i:s")]], "Count" => 1, "ScannedCount" => 1]));

?>