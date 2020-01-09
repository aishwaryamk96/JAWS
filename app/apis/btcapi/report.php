<?php

	// Init Session
	auth_session_init();

	// Auth Check - Expecting Session Only !
	if ((!auth_session_is_logged()) || (!auth_session_is_allowed("batcave"))) {
		header("HTTP/1.1 401 Unauthorized");
		die();
	}

	// Send Headers
	header('Access-Control-Allow-Credentials: true');
	header("Content-Type: application/json");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Pragma-directive: no-cache");
	header("Cache-directive: no-cache");
	header("Cache-Control: private, must-revalidate, max-age=0, no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
	header("Pragma: no-cache");

	if (empty($_POST)) {
		$_POST = json_decode(file_get_contents("php://input"), true);
	}

	$categories = [
		"Other issue",
		"Enrollment information not available",
		"Enrolment information incomplete",
		"Batch information incorrect",
		"Communication information incorrect",
		"Login information incorrect",
		"Payment information missing",
		"Payment information incorrect",
		"Lab login information missing",
		"Lab login information incorrect",
	];

	if (empty($_POST["user_id"]) || empty($_POST["category"])) {

		header("HTTP/1.1 400 Bad Request");
		die();

	}

	$category = $categories[intval($_POST["category"])];

	$desc = "";
	if (!empty($_POST["desc"])) {
		$desc = trim($_POST["desc"]);
	}
	$category .= (empty($desc) ? "" : ": ".$desc);

	db_exec("INSERT INTO user_logs (category, user_id, created_by, description) VALUES ('issue', ".db_sanitize($_POST["user_id"]).", ".$_SESSION["user"]["user_id"].",".$category.");");

	die(json_encode(["status" => true]));

?>