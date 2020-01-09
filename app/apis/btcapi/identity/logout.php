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

	$_SESSION = [];
	session_destroy();

	// if (!empty($_POST["email"])) {

	// 	load_module("user");

	// 	$user = user_get_by_email($_POST["email"]);
	// 	if (!empty($user)) {

	// 		$_SESSION["user"] = $user;
	// 		auth_session_load_roles();

	// 	}
	// 	else {
	// 		die(header("HTTP/1.1 400"));
	// 	}

	// }
	// else if (empty($_SESSION["user"])) {
	// 	die(header("HTTP/1.1 400"));
	// }
	// else if (!empty($_GET["forced"])) {

	// 	load_module("user");

	// 	$_SESSION["user"] = user_get_by_id($_SESSION["user"]["user_id"]);
	// 	auth_session_load_roles();

	// }

	// die(json_encode(["user" => $_SESSION["user"]]));

?>