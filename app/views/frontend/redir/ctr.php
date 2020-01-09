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

	// This will capture a basic lead from an email as GET params.
	// This is used to calculate Click-Through-Rates
	// -------------------

	// Check
	if (empty($_GET['ru']) || empty($_GET['lp']) || (empty($_GET['email']) && empty($_GET['user_id']))) {
		header('Location: https://www.jigsawacademy.com');
		die();
	}

	// Save Information
	$query="INSERT INTO 
				user_leads_basic (
					user_id,
					name,
					email,
					phone,
					utm_source,
					utm_campaign,
					utm_term,
					utm_medium,
					utm_segment,
					utm_content,
					utm_numvisits,
					referer,
					ip,
					ad_lp,
					ad_url,
					create_date,
					capture_trigger,
					capture_type
				) 
			VALUES 
				(
					".(empty($_GET['user_id']) ? 'NULL' : intval($_GET['user_id'])).",
					".(empty($_GET['name']) ? 'NULL' : db_sanitize($_GET['name'])).",
					".(empty($_GET['email']) ? 'NULL' : db_sanitize($_GET['email'])).",
					".(empty($_GET['phone']) ? 'NULL' : db_sanitize($_GET['phone'])).",
					".(empty($_GET['source']) ? 'NULL' : db_sanitize($_GET['source'])).",
					".(empty($_GET['campaign']) ? 'NULL' : db_sanitize($_GET['campaign'])).",
					".(empty($_GET['term']) ? 'NULL' : db_sanitize($_GET['term'])).",
					".(empty($_GET['medium']) ? 'NULL' : db_sanitize($_GET['medium'])).",
					".(empty($_GET['segment']) ? 'NULL' : db_sanitize($_GET['segment'])).",
					".(empty($_GET['content']) ? 'NULL' : db_sanitize($_GET['content'])).",
					".(empty($_GET['numvisits']) ? 'NULL' : db_sanitize($_GET['numvisits'])).",
					".db_sanitize($_SERVER["HTTP_REFERER"] ?? '').",
					".db_sanitize($_SERVER['REMOTE_ADDR'] ?? '').",
					".db_sanitize($_GET['lp']).",
					".db_sanitize($_GET['url'] ?? '').",
					".db_sanitize(strval(date("Y-m-d H:i:s"))).",
					'clickthrough',
					'url'
				);";

	db_exec($query);

	// Redirect
	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	header('Location: '.$_GET['ru']);
	die();

?>