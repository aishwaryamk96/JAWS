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

	// Check Auth
	if (!auth_api("subs.create")) die("You do not have the required priviledges to use this feature.");

	// Check
	if (!isset($_POST["user_id"])) {
		activity_create("critical","subs.create.fail","fail","","","","","No user_id received.");
		die('');
	}

	// Init
	load_module("user");
	load_module("course");
	load_module("subs");

	// Start
	$user = user_get_by_id($_POST["user_id"]);
	$subs_info;
	$pay_info;

	if ($user===false) {
		activity_create("critical","subs.create.fail","fail","","","","","No user found for user_id = ".$_POST['user_id'].".");
		die('');
	}

	// Prep Courses
	if (isset($_POST["persistence"]["combo"])) {

		$ext_combo_arr = course_get_combo_arr($_POST["combo"]);
		$native_combo_arr;

		foreach ($ext_combo_arr as $ext_course_id => $mode) {
			$native_course_entity = get_native_course_id($ext_course_id, $_POST["persistence"]["combo"]["layer"]);
			$native_combo_arr[$native_course_entity["id"]] = $mode;
		}

		$subs_info["combo"] = course_get_combo_str($native_combo_arr);

	}
	else $subs_info["combo"] = $_POST["combo"];

	if ($subs_info["combo"] == "239,2") {

		$bb = db_exec("SELECT * FROM bootcamp_batches WHERE bundle_id = 135 AND start_date >= CURRENT_DATE ORDER BY id DESC;");
		if (!empty($bb)) {

			$subs_info["bundle_id"] = 135;
			$subs_info["batch_id"] = $bb[0]["id"];

		}

	}

	// Prep Free Courses
	if (isset($_POST["combo_free"])) {
		if (isset($_POST["persistence"]["combo_free"])) {

			$ext_combo_arr = course_get_combo_arr($_POST["combo_free"]);
			$native_combo_arr_free;
			foreach ($ext_combo_arr as $ext_course_id => $mode) {
				$native_course_entity = get_native_course_id($ext_course_id, $_POST["persistence"]["combo_free"]["layer"]);
				$native_combo_arr_free[$native_course_entity["id"]] = $mode;
			}

			$subs_info["combo_free"] = course_get_combo_str($native_combo_arr_free);

		}
		else $subs_info["combo_free"] = $_POST["combo_free"];
	}

	// Prep Bundle
	if (isset($_POST["bundle_id"])) {
		if (isset($_POST["persistence"]["bundle_id"])) {

			$entity["layer"] = $_POST["persistence"]["bundle_id"]["layer"];
			$entity["type"] = "bundle";
			$entity["id"] =  $_POST["bundle_id"];

			$native_entity = get_native_id($entity);
			$subs_info["bundle_id"] = $native_entity["id"];

		}
		else $subs_info["bundle_id"] = $_POST["bundle_id"];
	}

	// Prep bootcamp batch
	if (isset($_POST["batch_id"])) {

		$batch = db_query("SELECT * FROM bootcamp_batches WHERE code = ".db_sanitize($_POST["batch_id"]).";");
		$batch = $batch[0];

		$subs_info["bundle_id"] = $batch["bundle_id"];
		$subs_info["batch_id"] = $batch["id"];

	}

	// Prep coupons
	// if (isset($_POST["coupons"])) {
	// 	if (isset($_POST["persistence"]["coupons"])) {

	// 		$ext_coupon_arr = explode(";",$_POST["coupons"]);
	// 		$native_coupons_str = "";
	// 		$count = 0;

	// 		foreach ($ext_coupon_arr as $ext_coupon_id) {
	// 			$entity["layer"] = $_POST["persistence"]["coupons"]["layer"];
	// 			$entity["type"] = "coupon";
	// 			$entity["id"] =  $ext_coupon_id;

	// 			$native_entity = get_native_id($entity);
	// 			$native_coupons_str .= (($count > 0) ? ";" : "" ).$native_entity["id"];
	// 			$count ++;
	// 		}

	// 		$pay_info["coupons"] = $native_coupons_str;

	// 	}
	// 	else $pay_info["coupons"] = $_POST["coupons"];
	// }

	// Prep rest
	$pay_info["status"] = "pending";
	$pay_info["currency"] = $_POST["currency"];
	$pay_info["sum_basic"] = $_POST["sum_basic"];
	$pay_info["sum_total"] = $_POST["sum_total"];
	$pay_info["instl_total"] = $_POST["instl_total"];
	$pay_info["instl"] = $_POST["instl"];
	$pay_info["receipt_type"] = "retail";

	// prep Instl
	$count = 1;
	while ($count <= intval($pay_info["instl_total"])) {
		$pay_info["instl"][$count]["create_entity_type"] = "system";
		$count ++;
	}

	// Create the subs
	$subscription = subscribe($user["email"], $subs_info, $pay_info, false);

	// Package creation
	if (isset($subscription["web_id"]))
	{
		$subs_id = db_query("SELECT subs_id FROM payment_link WHERE web_id=".db_sanitize($subscription["web_id"]).";");
		if (isset($subs_id[0]))
			activity_create("high", "package", "package.create", "", "", "subs", $subs_id[0]["subs_id"], "package.create", "pending");
	}

	echo json_encode($subscription);

	// Create Leads record
	db_exec("INSERT INTO user_leads_basic (
		user_id,
		referer,
		ip,
		ad_lp,
		ad_url,
		create_date,
		capture_trigger,
		capture_type
	) VALUES (".
		$_POST["user_id"].", ".
		db_sanitize($_SERVER['HTTP_REFERER']).", ".
		db_sanitize($_SERVER['REMOTE_ADDR']).", ".
		"'www.jigsawacademy.com', ".
		db_sanitize($_SERVER['REQUEST_URI']).", ".
		db_sanitize(strval(date("Y-m-d H:i:s"))).", ".
		"'ws-gateway'".", ".
		"'cookie'".
	");");

	// Done
	exit();

?>