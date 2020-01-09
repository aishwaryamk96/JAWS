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
	
// activity_debug_start();
// activity_debug_log("POST");
// activity_debug_log(json_encode($_POST));

      	// Check
	if (!isset($_POST["user_id"])) die("You do not have the required priviledges to use this feature.");
	if (!is_persistent(array("layer" => "dynpepl", "type" => "user", "id" => $_POST["user_id"]))) die("You do not have the required priviledges to use this feature.");

	// Get native user
	$user_entity = get_native_id(array("layer" => "dynpepl", "type" => "user", "id" => $_POST["user_id"]));
	
// activity_debug_log("USER");
// activity_debug_log(json_encode($user_entity));

	// Generate token and return
	$return = json_encode(array("token" => psk_generate("user",$user_entity["id"], "crm","dynpe","","",true)));
	
	// activity_debug_log("TOKEN");
	// activity_debug_log($return);
	
	echo $return;
	exit();



  ?>