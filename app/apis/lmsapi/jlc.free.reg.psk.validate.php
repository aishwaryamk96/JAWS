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
    	-----------------------------------------
*/
    
	// Prevent exclusive access
	if (!defined("JAWS")) {
		header('Location: https://www.jigsawacademy.com');
		die();
	}
	
	// Check Auth
	if (!auth_api("jlc_free")) die("You do not have the required priviledges to use this feature.");

	// Check token and email
	if ((!isset($_REQUEST['token'])) || (!isset($_REQUEST['email']))) die("You do not have the required priviledges to use this feature.");

	// Get PSK
	$psk_info = psk_info_get($_REQUEST['token']);

	// PSK Check
	if ($psk_info === false) die("You do not have the required priviledges to use this feature.");
	psk_expire($psk_info['entity_type'], $psk_info['entity_id'], $psk_info['action']);

	// Get Activity
	$act = db_query('SELECT content FROM system_activity WHERE act_id="'.$psk_info['entity_id'].'"');

	// Activity Check
	if ($act[0]['content'] != $_REQUEST['email']) die('false');
	else die('true');

?>