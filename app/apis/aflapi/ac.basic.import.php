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

	// This will input and store a lead sent from an Active Campaign
	// Note: User-fetching (user id) is yet to be implemented !!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	// -------------------

	// Auth check
	//if (!auth_api ("ac.leads.import")) die ("You do not have sufficient privileges to perform this operation");

	// Checks
	if ((!isset($_POST['contact']['first_name'])) || (strlen(trim($_POST['contact']['first_name'])) < 3)) $_POST['contact']['first_name'] = 'Mr.e';

	// More Checks
	//if ((!isset($_POST['contact']['email'])) || (strlen(trim($_POST['contact']['email'])) < 5) || (!strpos($_POST['contact']['email'],'@')) || (!strpos($_POST['contact']['email'],'.'))) die ("Invalid Parameter or Parameter Not Present - email");
	//else if ((!isset($_POST['contact']['number'])) || (strlen(trim($_POST['contact']['number'])) < 10) || (strlen(trim($_POST['contact']['number'])) > 16) || (intval($_POST['contact']['number'])==0)) die ("Invalid Parameter or Parameter Not Present - phone");

	// activity_debug_start();
	// activity_debug_log('hello ac');
	// activity_debug_log(json_encode($_REQUEST));

	// Insert
	$ad_lp = $_POST['contact']['fields']['url'] ?? "";
	$url_components = parse_url($ad_lp);
	$ad_url = $url_components["path"];
	db_exec("INSERT INTO system_log (source, data) VALUES ('ac.basic.import', ".db_sanitize(json_encode(
		[
			"url" => $_POST['contact']['fields']['url'],
			"meta" => $_POST["contact"],
			"ad_lp" => $ad_lp,
			"components" => $url_components,
			"ad_url" => $ad_url
		]
	)).");");
	$query="INSERT INTO
				user_leads_basic (name,email,phone,utm_source,utm_campaign,utm_medium,utm_content,ad_lp,ad_url,create_date,capture_trigger,capture_type,ip,meta,xuid)
			VALUES
				(
					".db_sanitize($_POST['contact']['first_name'].($_POST['contact']['last_name'] ?? '')).",
					".db_sanitize($_POST['contact']['email']).",
					".db_sanitize($_POST['contact']['phone']).",
					".db_sanitize($_POST['contact']['fields']['utm_source'] ?? '').",
					".db_sanitize($_POST['contact']['fields']['utm_campaign'] ?? '').",
					".db_sanitize($_POST['contact']['fields']['utm_medium'] ?? '').",
					".db_sanitize($_POST['contact']['tags'] ?? '').",
					".db_sanitize($ad_lp).",
					".db_sanitize($ad_url).",
					".db_sanitize(strval(date("Y-m-d H:i:s"))).",
					'formsubmit',
					'url',
					".db_sanitize($_POST['contact']['ip4'] ?? '').",
					".db_sanitize(json_encode($_POST['contact']['info'])).",
					".db_sanitize($_POST["contact"]["fields"]["xuid"])."
				);";

	db_exec($query);
	//activity_debug_start();
	// activity_debug_log($query);
	echo json_encode(['status' => true]);
	exit();

?>
