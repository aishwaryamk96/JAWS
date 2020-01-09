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

	// This will capture a basic lead either directly by user_id or a token
	// -------------------

	// Check
	if ((!isset($_POST['name'])) || (!isset($_POST['phone'])) || (!isset($_POST['email'])) || (!isset($_POST['url']))) die();

	// Prep
	load_module("leads");
	//auth_session_init(); - //redundant...is logged will auto init session..

	$mode='';
	$id_or_token='';

	// Check Log in
	if (auth_session_is_logged()) {
		$mode = "id";
		$id_or_token = $_SESSION['user']['user_id'];
	}
	else {
		$mode = "token";
		if (isset($_COOKIE["_jafl_t_iot"])) $id_or_token = $_COOKIE["_jafl_t_iot"];
	}

	// Prep Lead Params
	$lead_params["utm_source"] = ''; 
	$lead_params["utm_campaign"] = ''; 
	$lead_params["utm_medium"] = ''; 
	$lead_params["utm_term"] = ''; 
	$lead_params["utm_segment"] = ''; 
	$lead_params["utm_content"] = ''; 
	$lead_params["utm_numvisits"] = '';

	$lead_params["gcl_id"] = ''; 
	$lead_params["global_id_perm"] = '';
	$lead_params["global_id_session"] = '';

	$lead_params["ad_url"] = $_POST['url'];
	$lead_params["referer"] = $_SERVER['HTTP_REFERER'] ?? '';
	$lead_params["ip"] = $_SERVER['REMOTE_ADDR'];
	$lead_params["ad_lp"] = "www.jigsawacademy.com";
	$lead_params["type"] = "cookie";
	$lead_params["trigger"] = 'formsubmit';

	// Prep User Info
	$user_info = [
		'name' => $_POST["name"],
		'email' => $_POST["email"],
		'phone' => $_POST["phone"],
		'meta' => [
			'course_id' => $_POST['course_id'] ?? ''
		]
	];

	// Capture
	$token = leads_basic_capture($lead_params, $mode, $id_or_token, $user_info);

	// Assign cookie ??
	if (!auth_session_is_logged()) setcookie("_jafl_t_iot", ((isset($_COOKIE["_jafl_t_iot"])) ? $id_or_token : $token), time() + (86400 * 10));

	// Partial assoc ??
	if (!auth_session_is_logged()) leads_basic_assoc_user_partial($user_info, $token);

	// Done
	echo json_encode(['status'=>true]);
	exit();

?>


