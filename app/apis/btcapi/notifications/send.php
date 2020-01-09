<?php

	// Prevent exclusive access
	if (!defined("JAWS")) {
		header("HTTP/1.1 401 Unauthorized");
		die();
	}

	// Init Session
	// auth_session_init();

	// // Auth Check - Expecting Session Only !
	if ((!auth_session_is_logged()) || (!auth_session_is_allowed("batcave") && !auth_session_is_allowed("btc.mobile"))) {
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

	$data = [];
	foreach ($_POST as $key => $value) {

		if (!in_array($key, ["notificationTitle", "sendAt", "category", "live"])) {

			if (is_numeric($value)) {
				$value = intval($value);
			}

			$value = format_date($key, $value);

			$data[$key] = $value;

		}

	}

	$notification = [
		"message" => [
			"topic" => $_POST["category"],
			"notification" => [
				"title" => $_POST["notificationTitle"],
			],
			"data" => $data
		]
	];

	if ($_POST["live"] == 1) {

		$notification["token"] = getRelevantTokens();
		die(json_encode(["status" => "App is not live yet...!"]));

	}
	else {

		$notification["token"] = "<Live token will go here>";
		die(json_encode($notification));

	}

	function format_date($key, $value) {

		if (in_array($key, ["from", "to", "at", "submit_at"])) {

			$value = date_create($value);
			$value = $value->format("Y-m-d H:i:s");

		}
		else if (in_array($key, ["on", "posted_on", "reply_by"])) {

			$value = date_create($value);
			$value = $value->format("Y-m-d");

		}

		return $value;

	}

	function getRelevantTokens() {
		return "";
	}

?>