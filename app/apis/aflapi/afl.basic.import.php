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

	// This will input and store a lead sent from an Affiliate marketing partner
	// Note: User-fetching (user id) is yet to be implemented !!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	// -------------------

	// Auth check
	if (!auth_api ("afl.leads.import")) die ("You do not have sufficient privileges to perform this operation");

	// Checks
	if ((!isset($_POST['name'])) || (strlen(trim($_POST['name'])) < 3)) die ("Invalid Parameter or Parameter Not Present - name");
	else if ((!isset($_POST['email'])) || (strlen(trim($_POST['email'])) < 5) || (!strpos($_POST['email'],'@')) || (!strpos($_POST['email'],'.'))) die ("Invalid Parameter or Parameter Not Present - email");
	else if ((!isset($_POST['phone'])) || (strlen(trim($_POST['phone'])) < 10) || (strlen(trim($_POST['phone'])) > 16) || (intval($_POST['phone'])==0)) die ("Invalid Parameter or Parameter Not Present - phone");
	else if ((!isset($_POST['source'])) || (strlen(trim($_POST['source'])) < 1)) die ("Invalid Parameter or Parameter Not Present - source");
	else if ((!isset($_POST['city'])) || (strlen(trim($_POST['city'])) < 3)) die ("Invalid Parameter or Parameter Not Present - city");

	//activity_debug_start();
	//activity_debug_log(json_encode(['afl.leads.import'=>'valuedirect','data'=>$_POST]));
	echo json_encode(['status' => true,'is_live'=>'true']);
	//exit();

	// Insert
	$query="INSERT INTO 
				user_leads_basic (name,email,phone,utm_source,utm_content,utm_campaign,utm_medium,utm_segment,ad_lp,ad_url,create_date,capture_trigger,capture_type) 
			VALUES 
				(
					".db_sanitize($_POST['name']).",
					".db_sanitize($_POST['email']).",
					".db_sanitize($_POST['phone']).",
					".db_sanitize($_POST['source']).",
					".db_sanitize($_POST['city']).",
					".db_sanitize($_POST['campaign'] ?? '').",
					".db_sanitize($_POST['medium'] ?? '').",
					".db_sanitize($_POST['segment'] ?? '').",
					'valuedirect',
					' ',
					".db_sanitize(strval(date("Y-m-d H:i:s"))).",
					'formsubmit',
					'url'
				);";

	db_exec($query);
	exit();

?>