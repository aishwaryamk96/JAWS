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
		header('Location: http://www.jigsawacademy.com');
		die();
	}

	require_once "base/base.php";

	authorize_call("user.auth");

	$response;

	load_module("user");
	load_module("subs");

	$user = user_get_by_email($_POST["email"]);

	if ($user === false) {

		// Create user

		// Test
		$user = user_get_by_id(16767);

	}

	$response["u"] = ["id" => $user["user_id"], "n" => $user["name"], "e" => $user["email"], "i" => $user["photo_url"], "p" => $user["phone"], "fb" => $user["soc_fb"], "gp" => $user["soc_gp"], "li" => $user["soc_li"]];

	$subs = subs_get_info_by_user_id($user["user_id"]);

	if ($subs === false) {
		$response["c"] = courses_array_prepare();
	}
	else {
		$response["s"] = subscriptions_array_prepare($subs);
	}

	die(json_encode($response));

	function subscriptions_array_prepare($subs) {



	}

?>