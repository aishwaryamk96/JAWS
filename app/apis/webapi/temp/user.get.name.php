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
	if (!auth_session_is_logged()) die("You do not have the required priviledges to use this feature.");
	if (!auth_session_is_allowed("paylink.create")) die("You do not have the required priviledges to use this feature.");

	//Check
	if ((!isset($_POST["email"])) || (strlen($_POST["email"]) == 0)) echo json_encode(array("status" => false));

	// Init
	load_module("user");

	// Start
	$email = trim($_POST["email"]);
	$user = user_get_by_email($email);

	// Check
	if ($user === false) echo json_encode(array("status" => false));
	else echo json_encode(array("status" => true, "name" => $user["name"]));	

	// Done
	exit();

?>  