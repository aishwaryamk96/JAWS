<?php

	// Send Headers
	header('Access-Control-Allow-Credentials: true');
	header("Content-Type: application/json");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Pragma-directive: no-cache");
	header("Cache-directive: no-cache");
	header("Cache-Control: private, must-revalidate, max-age=0, no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
	header("Pragma: no-cache");

	if (!auth_session_is_allowed("batcave")) {

		header("HTTP/1.1 403");
		die(json_encode(["status" => false, "error" => "Are you trying to hack...? Coz, its not gonna work."]));

	}

	if (empty($_POST)) {
		$_POST = json_decode(file_get_contents("php://input"), true);
	}

	if (empty($_POST["program"]) || empty($_POST["program"]["name"])) {

		header("HTTP/1.1 422");
		die;

	}

	die(json_encode(["program" => program_create($_POST["program"])]));

?>