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
	if (!auth_api("user.soc.remove")) die("You do not have the required priviledges to use this feature.");

	// Init
	load_module("user");

	// Start
	$user = user_get_by_id($_POST["user_id"]);

	$count = 0;
	if ((isset($user["soc_fb"])) ? (strlen($user["soc_fb"]) > 0) : false) $count++;
	if ((isset($user["soc_gp"])) ? (strlen($user["soc_gp"]) > 0) : false) $count++;
	if ((isset($user["soc_li"])) ? (strlen($user["soc_li"]) > 0) : false) $count++;

	// Perform checks
	if ($count <= 1) die ("false");
	if (isset($user["lms_soc"])) if (strcmp($user["lms_soc"], $_POST["soc"]) == 0) die("false");

	// exec
	user_update($user["user_id"], array("soc_".$_POST["soc"] => ""));

	// All done
	die("true");

?>  