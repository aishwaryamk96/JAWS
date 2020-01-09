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

	if (empty($_POST["user_id"])) {

		header("HTTP/1.1 400 Bad Request");
		die();

	}

	load_plugin("jlc");
	$jlc = new JLC;

	$response = false;
	$retry = 0;

	$status = false;

	$sis_id = db_query("SELECT sis_id FROM user_enrollment WHERE user_id = ".$_POST["user_id"].";");
	if (empty($sis_id)) {
		die(json_encode(["status" => false, "msg" => "User not found"]));
	}
	$sis_id = $sis_id[0]["sis_id"];

	while ($response === false) {

		$response = $jlc->apiNew("users/permissions_add", ["data" => http_build_query(["sis_id" => $sis_id]), "content_type" => "application/x-www-form-urlencoded"]);

		if ($response === false) {
			$retry++;
		}
		else {

			$response = json_decode($response, true);
			$status = true;
			break;

		}

		if ($retry >= 2) {

			$status = false;
			break;

		}

	}

	if ($status) {
		db_exec("INSERT INTO user_logs (user_id, category, created_by, description) VALUES (".db_sanitize($_POST["user_id"]).", 'app.access', ".$_SESSION["user"]["user_id"].", 'Complete access to mobile app was granted.');");
	}

	die(json_encode(["status" => $status]));

?>