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
	header('Access-Control-Allow-Origin: *');
	
	// Prevent exclusive access
	if (!defined("JAWS")) {
		header('Location: https://www.jigsawacademy.com');
		die();
	}

	// This will verify a generated OTP
	// This will also expire the OTP if it is valid
	// -------------------

	// Auth check
	if (!auth_api ("afl.otp")) die ("You do not have sufficient privileges to perform this operation");

	// Checks
	if ((!isset($_POST['otp'])) || (strlen(trim($_POST['otp'])) != 4)) die (json_encode(['status' => false]));
	else if ((!isset($_POST['lead'])) || (strlen(trim($_POST['lead'])) < 1)) die (json_encode(['status' => false]));

	if( $_POST["gateway"] == "msg91" ) {

		$lead_Arr = db_query("SELECT * FROM `user_leads_basic` WHERE lead_id =".db_sanitize($_POST["lead"]));
		
		load_plugin("msg91");
		$status = otp_verify($lead_Arr[0]["phone"], $_POST['otp']);
		$return = json_encode(['status' => $status]);
		
	} else {
		
		// OTP Check
		if (psk_get('lead', $_POST['lead'], 'otp') == $_POST['otp']) {

			/*$incr = intval((db_query("
				SELECT `AUTO_INCREMENT`
				FROM  INFORMATION_SCHEMA.TABLES
				WHERE TABLE_SCHEMA = 'jaws'
				AND TABLE_NAME   = 'user_leads_basic';
			"))[0]['AUTO_INCREMENT']);

			$query = "UPDATE
						user_leads_basic
					SET
						capture_trigger = 'formsubmit',
						lead_id = ".($incr + 10)."
					WHERE
						lead_id = ".db_sanitize($_POST['lead']).";
			";*/


			//db_exec($query);
			psk_expire('lead', $_POST['lead'], 'otp');
			$return = json_encode(['status' => true]);
		}

		else $return = json_encode(['status' => false]);
	
	}
	
	db_exec("INSERT INTO `user_leads_basic` (`user_id`,`name`,`email`,`phone`,`utm_source`,`utm_campaign`,`utm_term`,`utm_medium`,`utm_content`,`utm_segment`,`utm_numvisits`,`gcl_id`,`global_id_perm`,`global_id_session`,`referer`,`ip`,`ad_lp`,`ad_url`,`create_date`,`assoc_token`,`capture_trigger`,`capture_type`) SELECT `user_id`,`name`,`email`,`phone`,`utm_source`,`utm_campaign`,`utm_term`,`utm_medium`,`utm_content`,`utm_segment`,`utm_numvisits`,`gcl_id`,`global_id_perm`,`global_id_session`,`referer`,`ip`,`ad_lp`,`ad_url`,`create_date`,`assoc_token`,'formsubmit',`capture_type` FROM user_leads_basic WHERE lead_id=".db_sanitize($_POST["lead"]));
	
	echo $return;
?>
