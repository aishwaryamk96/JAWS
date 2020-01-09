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

	// This API returns a user only if the user has enrollments

	// Check Auth
	if (!auth_api("user.login")) die("You do not have the required priviledges to use this feature.");

	if (!isset($_POST["socialid"]) || !isset($_POST["gcm_reg_id"]) || strlen($_POST["gcm_reg_id"]) == 0)
	{
		echo json_encode(array("Items" => 0, "Count" => 0, "ScannedCount" => 0));
		exit();
	}

	// Init
	load_module("user");
	load_module("subs");
	load_library("mobapi.helper");

	// Start
	$user = user_get_by_email($_POST["socialid"]);

	// Create new user
	if (!$user) {
		echo json_encode(array("Items" => 0, "Count" => 0, "ScannedCount" => 0));
		exit();
	}

	if (!subs_get_info_by_user_id($user["user_id"]))
	{
		echo json_encode(array("Items" => 0, "Count" => 0, "ScannedCount" => 0));
		exit();
	}

	$gcm_ids = user_content_get($user["user_id"], "gcm_id");
	if (!$gcm_ids)
	{
		$value = json_encode(array($_POST["gcm_reg_id"]));
		user_content_set($user["user_id"], "gcm_id", $value);
	}
	else
	{
		// If the value is blank, add this one
		if (strlen($gcm_ids) == 0)
		{
			$value = json_encode(array($_POST["gcm_reg_id"]));
			user_content_set($user["user_id"], "gcm_id", $value);
		}
		$gcm_ids = json_decode($gcm_ids, true);
		// If the json object is empty, add this one
		if (count($gcm_ids) == 0)
		{
			$value = json_encode(array($_POST["gcm_reg_id"]));
			user_content_set($user["user_id"], "gcm_id", $value);
		}
		// If 1 GCM ID is present, add this one
		else if (count($gcm_ids) == 1)
		{
			if (strcmp($gcm_ids[0], $_POST["gcm_reg_id"]) != 0)
			{
				$gcm_ids[] = $_POST["gcm_reg_id"];
				$value = json_encode($gcm_ids);
				user_content_set($user["user_id"], "gcm_id", $value);
			}
		}
		// If 2 GCM IDs are already present
		else if (count($gcm_ids) > 1)
		{
			// And none of them match the given GCM ID, delete the older GCM ID and save the new one
			if ((strcmp($gcm_ids[0], $_POST["gcm_reg_id"]) != 0) && (strcmp($gcm_ids[1], $_POST["gcm_reg_id"]) != 0))
			{
				unset($gcm_ids[0]);
				$gcm_ids[] = $_POST["gcm_reg_id"];
				$value = json_encode($gcm_ids);
				user_content_set($user["user_id"], "gcm_id", $value);
			}
		}
	}
	
	echo json_encode(array("Items" => array(construct_mobile_app_user_array($user, $_POST["socialid"], $_POST["source"])), "Count" => 1, "ScannedCount" => 1));
	exit();
?>