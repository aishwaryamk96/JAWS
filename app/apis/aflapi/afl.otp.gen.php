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

	// This will capture a basic lead and create an OTP for contact no. verification
	// It can use the provided sms gateway for this.
	// -------------------

	// Auth check
	if (!auth_api ("afl.otp")) die ("You do not have sufficient privileges to perform this operation");

	// Checks
	if ((!isset($_POST['name'])) || (strlen(trim($_POST['name'])) < 3)) die (json_encode(['status' => false, 'field' => 'name']));
	else if ((!isset($_POST['email'])) || (strlen(trim($_POST['email'])) < 5) || (!strpos($_POST['email'],'@')) || (!strpos($_POST['email'],'.'))) die (json_encode(['status' => false, 'field' => 'email']));
	else if ((!isset($_POST['phone'])) || (strlen(trim($_POST['phone'])) < 10) || (strlen(trim($_POST['phone'])) > 16) || (intval($_POST['phone'])==0)) die (json_encode(['status' => false, 'field' => 'phone']));

	// Insert into leads
	$query="INSERT INTO 
				user_leads_basic (
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
					".db_sanitize($_POST['name']).",
					".db_sanitize($_POST['email']).",
					".db_sanitize($_POST['phone']).",
					".db_sanitize($_POST['source'] ?? '').",
					".db_sanitize($_POST['campaign'] ?? '').",
					".db_sanitize($_POST['term'] ?? '').",
					".db_sanitize($_POST['medium'] ?? '').",
					".db_sanitize($_POST['segment'] ?? '').",
					".db_sanitize($_POST['content'] ?? '').",
					".db_sanitize($_POST['numvisits'] ?? '').",
					".db_sanitize($_POST['referer'] ?? '').",
					".db_sanitize($_POST['ip'] ?? '').",
					".db_sanitize($_POST['lp'] ?? '').",
					".db_sanitize($_POST['url'] ?? '').",
					".db_sanitize(strval(date("Y-m-d H:i:s"))).",
					'formsubmit-preotp-".($_POST['gateway'] ?? 'default')."',
					'url'
				);";

	db_exec($query);

	$lead_id = db_get_last_insert_id();

	/* // Generate OTP
	$lead_id = db_get_last_insert_id();
	$otp = psk_generate('lead', $lead_id, 'otp', "", "", "days", true, 4);

	// Send OTP - Custom Gateway	
	if (isset($_POST['gateway'])) {
		try {
			load_plugin($_POST['gateway']);
			sms_send($_POST['phone'],"OTP for your Jigsaw Academy verification is: ".$otp.". Your OTP is confidential. Please do not share it with anyone.");
		}
		catch(Exception $e) {
			send_sms($_POST['phone'],"OTP for your Jigsaw Academy verification is: ".$otp.". Your OTP is confidential. Please do not share it with anyone.");
		}
	}

	// Send OTP - Default Gateway
	else send_sms($_POST['phone'],"OTP for your Jigsaw Academy verification is: ".$otp.". Your OTP is confidential. Please do not share it with anyone."); */
	
	
	if( !empty($_POST['gateway']) ){ 
		
		load_plugin($_POST['gateway']);
		
		if( $_POST['gateway'] == "msg91" ){

			// MSG91
			otp_send($_POST['phone']); 

		} else {
	
			// Generate OTP
			$otp = psk_generate('lead', $lead_id, 'otp', "", "", "days", true, 4);
			
			// Send OTP - Custom Gateway
			sms_send($_POST['phone'],"OTP for your Jigsaw Academy verification is: ".$otp.". Your OTP is confidential. Please do not share it with anyone.");

		}
		
	} else {
		
		// Send OTP - Default Gateway
		load_plugin(JAWS_SMS_GATEWAY);
		send_sms($_POST['phone'],"OTP for your Jigsaw Academy verification is: ".$otp.". Your OTP is confidential. Please do not share it with anyone.");
		
	}

	// Reply
	echo json_encode([
		'status' => true,
		'lead' => $lead_id
	]);

	// Done
	exit();
?>



