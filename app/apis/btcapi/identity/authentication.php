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

	if (!empty($_POST["email"])) {

		load_module("user");

		$user = user_get_by_email($_POST["email"]);
		if (!empty($user)) {

			$_SESSION["user"] = $user;
			$_SESSION["user"]["phone"] = !empty($user["phone"]) ? (intval($user["phone"])) : "";
			auth_session_load_roles();
			if (!auth_session_is_allowed("batcave")) {
				die(header("HTTP/1.1 401"));
			}

			$batcave_pref = user_content_get($_SESSION["user"]["user_id"], "batcave.pref");
			if (!empty($batcave_pref)) {
				$_SESSION["user"]["batcave_pref"] = json_decode($batcave_pref, true);
			}
			else {
				$_SESSION["user"]["batcave_pref"] = [];
			}

		}
		else {
			die(header("HTTP/1.1 400"));
		}

	}
	else if (empty($_SESSION["user"])) {
		die(header("HTTP/1.1 400"));
	}
	else if (!empty($_GET["forced"])) {

		load_module("user");

		$_SESSION["user"] = user_get_by_id($_SESSION["user"]["user_id"]);
		$_SESSION["user"]["phone"] = !empty($_SESSION["user"]["phone"]) ? (intval($_SESSION["user"]["phone"])) : "";
		// $_SESSION["user"]["settings"]["drawer"]["fixed"] = true;
		auth_session_load_roles();
		if (!auth_session_is_allowed("batcave")) {
			die(header("HTTP/1.1 401"));
		}

		if (in_array($_SESSION["user"]["user_id"], [13683, 6273])) {
			$_SESSION["user"]["roles"]["feature_keys"]["batcave.edit.himanshu"] = 1;
		}

		$batcave_pref = user_content_get($_SESSION["user"]["user_id"], "batcave.pref");
		if (!empty($batcave_pref)) {
			$_SESSION["user"]["batcave_pref"] = json_decode($batcave_pref, true);
		}
		else {
			$_SESSION["user"]["batcave_pref"] = [];
		}

	}

	die(json_encode(["user" => $_SESSION["user"]]));

?>