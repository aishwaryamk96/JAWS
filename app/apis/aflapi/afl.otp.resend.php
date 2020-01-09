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

	// This will resend an already generated OTP for a lead.
	// It can use the provided sms gateway for this.
	// -------------------

	// Auth check
	if (!auth_api ("afl.otp")) die ("You do not have sufficient privileges to perform this operation");

	// Checks
	if (!isset($_POST['lead'])) die('You do not have sufficient privileges to perform this operation');

	// OTP
	$otp = psk_get('lead', $_POST['lead'], 'otp');
	if (!$otp) die('You do not have sufficient privileges to perform this operation');

	// Retreive Lead details
	$qphone = db_query('SELECT phone FROM user_leads_basic WHERE lead_id='.$_POST['lead'].';');
	$phone = $qphone[0]['phone'];

	// Update record to show resend
	$query = "UPDATE 
					user_leads_basic 
				SET 
					capture_trigger = 'formsubmit-preotp-re-".($_POST['gateway'] ?? 'default')."' 
				WHERE 
					lead_id = ".db_sanitize($_POST['lead']).";
		";
	db_exec($query);

	// Re-Send OTP - Custom Gateway	
	if (isset($_POST['gateway'])) {
		try {
			load_plugin($_POST['gateway']);
			sms_send($phone,"OTP for your Jigsaw Academy verification is: ".$otp.". Your OTP is confidential. Please do not share it with anyone.");
		}
		catch(Exception $e) {
			send_sms($phone,"OTP for your Jigsaw Academy verification is: ".$otp.". Your OTP is confidential. Please do not share it with anyone.");
		}
	}

	// Re-Send OTP - Default Gateway
	else send_sms($phone,"OTP for your Jigsaw Academy verification is: ".$otp.". Your OTP is confidential. Please do not share it with anyone.");

	// Reply
	echo json_encode([
		'status' => true
	]);

	// Done
	exit();
?>



