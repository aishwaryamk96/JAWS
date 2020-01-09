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
	if (!auth_api("user.get")) die("You do not have the required priviledges to use this feature.");

	// Init
	load_module("user");

	// Start
	$user = user_get_by_id($_POST["user_id"]);

	// Check
	if (!$user) die("false");

	// Return the existing user info
	$ret = array(
		"user_id" => $user["user_id"],
		"name" => $user["name"],
		"phone" => (isset($user["phone"]) ? $user["phone"] : ""),
		"email" => $user["email"],
		"photo_url" => (isset($user["photo_url"]) ? $user["photo_url"] : ""),
		"soc_fb" => (isset($user["soc_fb"]) ? $user["soc_fb"] : ""),
		"soc_gp" => (isset($user["soc_gp"]) ? $user["soc_gp"] : ""),
		"soc_li" => (isset($user["soc_li"]) ? $user["soc_li"] : ""),
		"created" => "false"
		);
	
	echo json_encode($ret);

	// Done
	exit();

?>  