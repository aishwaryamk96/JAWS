<?php

	// Prevent exclusive access
	if (!defined("JAWS")) {
		header("HTTP/1.1 401 Unauthorized");
		die();
	}

	// Init Session
	auth_session_init();

	// // Auth Check - Expecting Session Only !
	// if ((!auth_session_is_logged()) || (!auth_session_is_allowed("batcave"))) {
	// 	header("HTTP/1.1 401 Unauthorized");
	// 	die();
	// }

	if (empty($_POST)) {
		$_POST = json_decode(file_get_contents("php://input"), true);
	}

	// Send Headers
	header('Access-Control-Allow-Credentials: true');
	header("Content-Type: application/json");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Pragma-directive: no-cache");
	header("Cache-directive: no-cache");
	header("Cache-Control: private, must-revalidate, max-age=0, no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
	header("Pragma: no-cache");

	if (empty($_POST["ami"]["ami_id"]) || empty($_POST["ami"]["meta"]) || empty($_POST["ami"]["type"])) {
		die(header("HTTP/1.1 422"));
	}

	load_module("lab");

	$_POST["ami"]["created_by"] = $_SESSION["user"]["user_id"];

	if (empty($ami = lab_add($_POST["ami"]))) {
		die(header("HTTP/1.1 422"));
	}

	$ami["name"] = $_SESSION["user"]["name"];
	$ami["created_at"] = "Now";

	die(json_encode($ami));

?>