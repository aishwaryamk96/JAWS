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

	EVENT HANDLERS
	-------------------
*/

// Prevent exclusive access
if (!defined("JAWS")) {
	header('Location: https://www.jigsawacademy.com/index.php');
	die();
}

// Install hooks
hook('event_handle_pageload', 'pageload_handler');
hook('event_tag_handle_ty', 'ty_handler');
hook('event_handle_otp', 'otp_handler');

// Handle hooks
function otp_handler(&$data) {



}

function ty_handler(&$data) {

	// Add to leads_basic
	try {
	    //JA-127 START
	    $name = $data["post"]["name"] ?? $data["post"]["fname"] ?? $data["post"]["firstname"] ?? $data["post"]["fullname"] ?? $data["post"]["lname"];
	    $email = $data["post"]["email"] ?? $data["post"]["e-mail"] ?? $data["post"]["username"] ?? $data["post"]["emailid"];
	    $phone = $data["post"]["phone"] ?? $data["post"]["mobile"] ?? $data["post"]["contact"] ?? $data["post"]["mobileno"]?? $data["post"]["telephone"] ?? $data["post"]["contactno"];
	    $status = 0;
	    if(empty($name) && empty($email) && empty($phone))
	        $status = 6;
	        db_exec("INSERT INTO user_leads_basic (
				name,
				email,
				phone,
				utm_source,
				utm_campaign,
				utm_term,
				utm_medium,
				utm_content,
				utm_segment,
				utm_numvisits,
				gcl_id,
				global_id_perm,
				xuid,
				referer,
				ip,
				ad_lp,
				ad_url,
				create_date,
				capture_trigger,
				capture_type,
				status
			) VALUES (".
	            db_sanitize($name).", ".
	            db_sanitize($email).", ".
	            db_sanitize($phone).", ".
	            db_sanitize($data["post"]["utm_source"] ?? '').", ".
	            db_sanitize($data["post"]["utm_campaign"] ?? '').", ".
	            db_sanitize($data["post"]["utm_term"] ?? '').", ".
	            db_sanitize($data["post"]["utm_medium"] ?? '').", ".
	            db_sanitize($data["post"]["utm_content"] ?? '').", ".
	            db_sanitize($data["post"]["utm_segment"] ?? '').", ".
	            db_sanitize($data["post"]["utm_numvisits"] ?? '').", ".
	            db_sanitize($data["post"]["gclid"] ?? '').", ".
	            db_sanitize($data["post"]["global_id"] ?? '').", ".
	            db_sanitize($data["post"]['xuid'] ?? '').", ".
	            db_sanitize($data["ref"] ?? '').", ".
	            db_sanitize($data["post"]["ip"] ?? '').", ".
	            db_sanitize('LP').", ".
	            db_sanitize($data["location"] ?? $data["url"] ?? '').", ".
	            db_sanitize(strval(date("Y-m-d H:i:s"))).", ".
	            "'formsubmit'".", ".
	            "'url'".", ".
	            $status.");");
	        //JA-127 END
	}
	catch (Exception $e) {
		activity_create('critical','event.tag.ty','handler.fail','','','','',json_encode($_REQUEST)." --- ".json_encode($data));
	}

}

function pageload_handler(&$data) {
	// Generate or check for leadID
	$data['cookies']['evlid'] = $data['cookies']['evlid'] ?? uniqid('evlid_', true);

	// Reply
	echo json_encode([
		'status' => true,
		'cookies' => [
				'evlid' => [
					'value' => $data['cookies']['evlid'],
					'expire'=> time() + (86400 * 365)
				]
			]
		]
	);

}

?>
