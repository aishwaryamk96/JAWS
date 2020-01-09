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

	if (empty($_POST["job"]["title"]) || empty($_POST["job"]["company"])) {

		header("HTTP/1.1 422");
		die("1");

	}

	load_module("careers");

	$_POST["job"]["created_by"] = 13683;

	$id = career_add($_POST["job"]);
	if (empty($id)) {

		header("HTTP/1.1 422");
		die("2");

	}

	$courses = $_POST["job"]["courses"];

	$job = career_get($id);
	$job["tools"] = json_decode($job["tools"], true);
	$job["preview"] = $job["html"];
	$job["html"] = htmlentities($job["html"]);

	die(json_encode($job));

?>