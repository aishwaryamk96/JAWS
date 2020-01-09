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
	load_module("subs");

	if ((!isset($_POST["package_id"])) || (!isset($_POST["status_approval_sm"])) || (!isset($_POST["approver_comment_sm"]))) die("You do not have the required priviledges to use this feature.");

	// If the package is not already persisted, reject the request
	if (!is_persistent(array("layer" => "dynpepl", "type" => "package", "id" => $_POST["package_id"])))
		die("You do not have the required priviledges to use this feature.");

	// Get native package ID
	$package_id = get_native_id(array("layer" => "dynpepl", "type" => "package", "id" => $_POST["package_id"]))["id"];

	if (strcmp($_POST["status_approval_sm"], "approved") != 0 && strcmp($_POST["status_approval_sm"], "rejected") != 0)
		die("You do not have the required priviledges to use this feature.");

	if (strcmp($_POST["status_approval_sm"], "rejected") == 0 && strlen($_POST["approver_comment_sm"]) == 0)
		die("You do not have the required priviledges to use this feature.");

	// Update the status
	package_update($package_id, array("status_approval_sm" => $_POST["status_approval_sm"], "approver_comment_sm" => $_POST["approver_comment_sm"]));

	echo (json_encode(array("status" => "success")));
	exit();

?>
