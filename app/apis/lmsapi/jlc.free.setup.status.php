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
	
	// No Auth Required - Function checks for PSK

	// Check token and email and course
	if ((!isset($_REQUEST['email'])) || (!isset($_REQUEST['token'])) || (!isset($_REQUEST['course']))) die("You do not have the required priviledges to use this feature.");

	// PSK Check
	$psk_info = psk_info_get($_REQUEST['token']);
	if ($psk_info === false) die("You do not have the required priviledges to use this feature.");

	// Retreive & Check status
	$enract = db_query('SELECT act_id, status FROM system_activity WHERE act_type="jlc.free" AND activity="setup" AND content='.db_sanitize($_REQUEST['email']).' AND entity_type="course" AND entity_id='.intval($_REQUEST['course']).';');
	if (isset($enract[0]['status']) && ($enract[0]['status'] == 'executed')) echo json_encode(['status' => true]);
	else if (isset($enract[0]['status']) && ($enract[0]['status'] == 'fail')) echo json_encode(['status' => false, 'perma_fail' => true]);
	else echo json_encode(['status' => false]);

	// Done
	exit();

?>