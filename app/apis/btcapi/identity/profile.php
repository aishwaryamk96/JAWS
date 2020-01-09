<?php

	// Prevent exclusive access
	if (!defined("JAWS")) {
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

	$user = $_POST["user"];

	$user_changes = [
		"name = ".db_sanitize($user["name"]),
		"email = ".db_sanitize($user["email"]),
		"phone = ".db_sanitize($user["phone"])
	];

	load_module("user");

	$user_changes = implode(", ", $user_changes);
	db_exec("UPDATE user SET $user_changes WHERE user_id = ".$_SESSION["user"]["user_id"]);

	$batcave_pref = $_SESSION["user"]["batcave.pref"] ?? [];

	$batcave_pref = $_POST["preferences"];
	user_content_set($_SESSION["user"]["user_id"], "batcave.pref", json_encode($batcave_pref));

	die(json_encode(["status" => true]));

?>