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
    	header('Location: ../index.php');
    	die();
  	}

  	// register_shutdown_function(function() {
  	// 	if (!empty($error = error_get_last())) {
  	// 		var_dump($error);
  	// 	}
  	// });

    /*
  	// Load prerequisites
  	load_module("user");
    load_module("user_enrollment");

    // Get all the subscriptions that should start today or before, i.e., start_date <= today's date and status=pending
    $date_today = new DateTime("now");
	$date_today = db_sanitize($date_today->format("Y-m-d"));
	$res_subs = db_query("SELECT * FROM subs WHERE (start_date <= DATE_ADD(".$date_today.", INTERVAL 1 DAY)) AND status='pending';");

    // If subscriptions found that satisfy the criteria, work on them
    if ($res_subs) {

	    foreach ($res_subs as $res_sub) {

	    	$user = user_get_by_id($res_sub["user_id"]);
	    	if (strlen($res_sub["corp"]) > 0 || (isset($user["lms_soc"]) && strlen($user["lms_soc"]) > 0)) {
	        	enr_create($res_sub["subs_id"]);
	    	}

	    }

	}
    */
	// New way:
	load_module("user_enrollment");

	$res_subs = db_query("SELECT subs.subs_id FROM subs INNER JOIN user ON user.user_id = subs.user_id WHERE (subs.start_date <= DATE_ADD(CURRENT_DATE, INTERVAL 1 DAY)) AND subs.status='pending' AND (user.lms_soc IS NOT NULL OR (subs.corp IS NOT NULL AND subs.corp != ''));");
	if (!empty($res_subs)) {

		foreach ($res_subs as $subs) {
			enr_create($subs["subs_id"]);
		}

	}

	// Now start the SIS import and lab file creation

	//========================================================== SIS import Automation STARTS Here ==========================================================//
	sis_import();
	//========================================================== SIS import Automation ENDS Here ===========================================================//

	//=============================================================== Lab import STARTS Here ===============================================================//
	//lab_import();
	//================================================================ Lab import ENDS Here ================================================================//

?>
