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
    	---------------------------------
*/

	// Prevent exclusive access
	if (!defined("JAWS")) {
		header('Location: https://www.jigsawacademy.com');
		die();
	}

	if (!isset($_POST["token"])) {
		die ("You do not have required priviledges to use this feature.");
	}

	load_module("user");
	load_module("refer");
	load_module("subs");

	load_library("email");

	$user;
	$user_src;

	// Authenticate the token here...
	 $psk_info = psk_info_get($_POST["token"]);
	 $id = $psk_info["entity_id"]; // this is the primary key id from refer table
	// Get the refer record
	$refer_details = refer_get_by_id($id);
	if ($refer_details["referrer_type"] == "user") {
		$user = user_get_by_id($refer_details["referrer_id"]);
	}
	else {

		$res_act = db_query("SELECT * FROM system_activity WHERE act_id=".$refer_details["referrer_id"]);
		$user = json_decode($res_act[0]["content"], true);
		$user["user_id"] = $res_act[0]["act_id"];
		$user_src = "system_activity";

	}

	$courses_ref;
	$bundles_ref;
	$courses_str = array();
	if (strlen($refer_details['courses']) > 0) {
		$courses_ref = explode(";", $refer_details['courses']);
	}

	if (strlen($refer_details['course_bundles']) > 0) {
		$bundles_ref = explode(";", $refer_details['course_bundles']);
	}

	if (count($courses_ref) > 0) {

		foreach ($courses_ref as $course_id) {
			$courses_str[] = db_query("SELECT name FROM course WHERE course_id=".$course_id)[0]['name'];
		}

	}

	if (count($bundles_ref) > 0) {

		foreach ($bundles_ref as $bundle_id) {
			$courses_str[] = db_query("SELECT name FROM course_bundle WHERE bundle_id=".$bundle_id)[0]['name'];
		}

	}

	//print_r($courses_str);die;
	$courses_str = implode(" and ", $courses_str);
	$referral_date = date_create_from_format("Y-m-d H:i:s", $refer_details["create_date"]);
	$claim_date = date('Y-m-d H:i:s');
	//update refer status
	db_exec("UPDATE refer SET status='awaiting_approval' , claim_date='".$claim_date."' WHERE id=".$id);

	// Send an email to concerned dept
	$subject = "Referral enrolled. Amazon voucher to be given to referrer ".$user["name"];
	$to = ($GLOBALS["jaws_exec_live"] ? "ishita@jigsawacademy.com,payments@jigsawacademy.com" : "himanshu@jigsawacademy.com");
	$body = "<p>Hi Finance Team,</p>
			<p>".$user["name"].", ".$user["email"]." is claiming for an Amazon voucher worth INR 1000.</p>
			<p>His/her friend ".$refer_details["name"].", ".$refer_details["email"]." has enrolled for ".$courses_str." on ".$referral_date->format("d M Y")."</p>
			<p>Both ".$user["name"]." and ".$refer_details["name"]." has paid the complete amount for their courses and the 7 days money back period has expired for them.</p>

			<p>We request you to do a final check from your side before proceeding to reward the voucher.​</p>

			<p>Thank you,</p>
			<p>JAWS Refer a friend feature</p>";

	$headers[] = 'MIME-Version: 1.0';
	$headers[] = 'Content-type: text/html; charset=iso-8859-1';
	$headers[] = "From: info@jigsawacademy.com";
	$headers[] = "Bcc: jagruti@jigsawacademy.com";

	mail($to, $subject, $body, implode("\r\n", $headers));

	die(json_encode(["success" => 1, "id" => $id]));

?>