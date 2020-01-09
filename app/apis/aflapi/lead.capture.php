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

	// IMPORTANT!!!!!!!!!
	// ====================================================================================================
	// NOTE!!:: THIS API IS OUTDATED. IF YOU WANT TO GIVE AN API FOR LEAD CAPTURE TO AN EXTERNAL AGENCY, USE "lead.php", IT IS PRESENT IN THE SAME DIRECTORY!!
	// ====================================================================================================
	// CANNOT STRESS MORE. HOPE YOUR ATTENTION IS CAUGHT HERE BEFORE YOU PROCEED TO ASSUME THIS IS THE ONE...

	// This will input and store a lead sent from an Active Campaign
	// Note: User-fetching (user id) is yet to be implemented !!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	// -------------------

	$auth = [
		"34pilluvhqqohtwk7k3k" => "Careers360",
		"GFGPSx6cPN0&IoBP24I2lwA&EqPtz5qN7HM" => "Generic"
	];

	// Auth check
	$headers = getallheaders();
	if (empty($headers["Authorization"])) {
		die(header("HTTP/1.1 401"));
	}
	$authorization = strtolower($headers["Authorization"]);
	$authorization = trim(str_replace("bearer", "", $authorization));
	if (empty($auth[$authorization])) {
		die(header("HTTP/1.1 401"));
	}

	if (empty($_POST)) {
		$_POST = json_decode(file_get_contents("php://input"), true);
	}
	if (empty($_POST["email"]) || empty($_POST["phone"]) || strlen(trim($_POST["phone"])) < 10) {
		die(header("HTTP/1.1 400"));
	}

	// Checks
	if ((!isset($_POST['name'])) || (strlen(trim($_POST['name'])) < 3)) {
		$_POST['name'] = 'Mr.e';
	}

	$_POST["phone"] = str_replace(["+", "-", " "], "", $_POST["phone"]);
	if (!empty($_POST["isd"])) {
		$_POST["phone"] = "+".trim($_POST["isd"], "+")."-".$_POST["phone"];
	}

	// Insert
	$ad_lp = $_POST['url'] ?? "";
	$url_components = parse_url($ad_lp);
	$ad_url = $url_components["path"] ?? "";
	db_exec("INSERT INTO system_log (source, data) VALUES ('lead.capture', ".db_sanitize(json_encode(
		[
			"source" => $auth[$authorization],
			"url" => $ad_lp,
			"data" => $_POST,
			"ad_lp" => $ad_lp,
			"components" => $url_components,
			"ad_url" => $ad_url
		]
	)).");");

	$query="INSERT INTO
				user_leads_basic (name,email,phone,utm_source,utm_campaign,utm_medium,utm_content,utm_term,ad_lp,ad_url,create_date,capture_trigger,capture_type,ip)
			VALUES
				(
					".db_sanitize($_POST['name']).",
					".db_sanitize($_POST['email']).",
					".db_sanitize($_POST['phone']).",
					".db_sanitize($_POST['utm_source'] ?? '').",
					".db_sanitize($_POST['utm_campaign'] ?? '').",
					".db_sanitize($_POST['utm_medium'] ?? '').",
					".db_sanitize($_POST['utm_content'] ?? '').",
					".db_sanitize($_POST['utm_term'] ?? '').",
					".db_sanitize($ad_lp).",
					".db_sanitize($ad_url).",
					CURRENT_TIMESTAMP,
					'formsubmit',
					'url',
					".db_sanitize($_POST['ip'] ?? '')."
				);";

	db_exec($query);
	//activity_debug_start();
	// activity_debug_log($query);
	echo json_encode(['status' => true]);
	exit();

?>
