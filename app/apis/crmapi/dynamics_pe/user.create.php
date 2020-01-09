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

	// Check Auth
	if (!auth_api("crm")) die("You do not have the required priviledges to use this feature.");

	// Load Stuff
	load_library("persistence");
	load_module("user");

      	// Check
	if ((!isset($_POST["user_id"])) || (!isset($_POST["email"]))) die("You do not have the required priviledges to use this feature.");

	// Get the user
	$user = user_get_by_email($_POST["email"]);
	if ($user === false) {
		// Prep
		$name = isset($_POST["name"]) ? $_POST["name"] : "Unnamed User";
		$phone = isset($_POST["phone"]) ? $_POST["phone"] : "";
		$pass = substr(str_shuffle($name.str_replace("@", "0", str_replace(".", "", $_POST["email"]))), 0, 10);

		// Create the user
		$user = user_create($_POST["email"], $pass, $name, $phone);
	}

	//Add role - 15
	$roles = explode(";",$user["roles"]);
	if (!in_array("15", $roles)) $user["roles"] = trim($user["roles"], ";").";15";

	// Add GP SOC if email is JigsawAcademy.com
	$socgpflag = false;
	if (strcmp(strtoupper(substr($_POST["email"],stripos($_POST["email"], "@") + 1)), "jigsawacademy.com") == 0) $socgpflag = true;

	// Update the fields
	user_update($user["user_id"], $socgpflag ? ["soc_gp" => $_POST["email"], "roles" => $user["roles"]] : ["roles" => $user["roles"]]);

	// Save the discount_max for the user in user_content
	user_content_set($user["user_id"], "discount_max", $_POST["discount_max"]);

	//activity_debug_start();
	//activity_debug_log(json_encode($user));

	// Persist the User
	persist("dynpepl", "user", $user["user_id"], $_POST["user_id"]);

	// Return Web_ID of the user
	echo json_encode(array("web_id" => $user["web_id"]));
	exit();

  ?>
