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

	load_module("user");
	load_module("refer");
	load_library("email");

	$date = date("Y-m-d");
	$refer = db_query("SELECT * FROM refer where status='no_action' ");
	$arr = array();

	$idm_date_range = [];
	$idm_date_range[0] = date_create_from_format("Y-m-d H:i:s", "2017-06-16 00:00:00");
	$idm_date_range[1] = date_create_from_format("Y-m-d H:i:s", "2017-06-30 23:59:59");

	$referrer = [];

	foreach($refer as $ref) {

		$create_date = date_create_from_format("Y-m-d H:i:s", $ref["create_date"]);
		if ($create_date >= $idm_date_range[0] && $create_date <= $idm_date_range[1]) {
			continue;
		}
		//$create_date = date('Y-m-d', strtotime($ref['create_date']));
		$create_date = $create_date->format("Y-m-d");
		$ref_date_plus_25 = date('Y-m-d', strtotime($create_date. ' + 25 days'));
		if ($date == $ref_date_plus_25) {

			if (!empty($ref["course_bundles"])) {

				if (strpos($ref["course_bundles"], "75")) {
					continue;
				}

			}

			if ($ref["referrer_type"] == "user") {
				$user = user_get_by_id($ref["referrer_id"]);
			}
			else {

				$res_act = db_query("SELECT * FROM system_activity WHERE act_id=".$ref["referrer_id"]);
				$user = json_decode($res_act[0]["content"], true);
				$user["user_id"] = $res_act[0]["act_id"];
				$user_src = "system_activity";

			}

			$arr["referrer_email"] = $user["email"];
			$arr["referrer_name"] = $user['name'];
			$arr["referral_name"] = $ref['name'];
			$arr["referral_email"] = $ref['email'];
			$arr["date"] = $ref['create_date'];
			$arr["cc"] = $ref['coupon_code'];

			$content["referrer_name"] = $user['name'];
			$content["referral_name"] = $ref["name"];
			$content["date"] = $ref['create_date'];
			$content["coupon_code"] = $ref["coupon_code"];
			// send mail to referral ( person who was referred)
			send_email("no.action.referral", array("to" => (($GLOBALS["jaws_exec_live"]) ? $ref['email'] : "himanshu@jigsawacademy.com"), "subject" => "Hurry up! Coupon shared by ".$user['name']." is about to expire"), $content);

			$referrer[$user["user_id"]]["content"][] = $arr;

		}

	}

	// send mail to referrer (person who referred his friend)

	foreach ($referrer as $email) {
		send_email("no.action.referrer", array("to" => (($GLOBALS["jaws_exec_live"]) ? $email["content"][0]["referrer_email"] : "himanshu@jigsawacademy.com"), "subject" => "Can't wait to give you Amazon voucher"), $email);
	}