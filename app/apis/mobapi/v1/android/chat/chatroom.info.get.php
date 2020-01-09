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

	if (!auth_api("mobapp.chat") || !isset($_POST["chatroom_id"]))
		die ("You do not have required priviledges to use this feature.");

	load_module("user_enrollment");
	load_module("user");

	$section = section_get_by_id($_POST["chatroom_id"]);
	if ($section === false)
		die(json_encode(["error" => 1, "error_desc" => "Chatroom not found"]));

	$admin_arr = array();
	$admins = explode(";", $section["admin"]);
	foreach ($admins as $admin)
	{
		$user = user_get_by_id($admin);
		$admin_arr[] = ["id" => $user["user_id"], "name" => $user["name"], "photo_url" => $user["photo_url"]];
	}

	$response["chatroom_id"] = $section["section_id"];
	$response["chatroom_name"] = section_name_get($section);
	$response["admins"] = $admin_arr;
	$response["user_count"] = count($admins) + count(explode(";", $section["users"]));

	die(json_encode($response));

?>