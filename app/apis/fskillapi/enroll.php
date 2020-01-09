<?php

	load_library("jwt");
	load_plugin("future_skills");

	if ($_SERVER["CONTENT_TYPE"] == "application/json") {
		$_POST = json_decode(file_get_contents("php://input"), true);
	}

	$log_id = log_action($_POST);

	if (!isset($_POST["data"]) || !isset($_POST["auth_token"])) {

		header("HTTP/1.1 401");
		die;

	}

	$token = $_POST["auth_token"];
	// var_dump(jwt_encode($_POST["data"], "940xyz105273214434b7432316e2d1e290"));die;
	// Old secret
	// if (($data = jwt_decode($token, "940xyz105273214434b7432316e2d1e290")) === false) {
	if (($data = jwt_decode($token, "e69a8d30d04e468aa83a3dd1fa85c8d8")) === false) {

		log_action($log_id, "bad_token");
		header("HTTP/1.1 401");
		die;

	}

	if ($data != $_POST["data"]) {

		log_action($log_id, "token_mismatch");
		header("HTTP/1.1 401");
		die;

	}

	$data = $_POST["data"];

	$context = [];
	if (isset($data["card"])) {

		if (!isset($data["card"]["external_id"]) || !is_numeric($data["card"]["external_id"])) {

			log_action($log_id, "bad_context_id");
			header("HTTP/1.1 401");
			die;

		}

		$context["type"] = "course";
		if ($data["card"]["external_id"] > 10000) {

			$context["type"] = "bundle";
			$data["card"]["external_id"] %= 10000;

		}

		$context["id"] = $data["card"]["external_id"];

	}
	else if (isset($data["BLAH"])) {

		$context["type"] = "bundle";
		if (!isset($data["card"]["external_id"])) {

			log_action($log_id, "bad_context_id");
			header("HTTP/1.1 401");
			die;

		}

		$context["id"] = $data["BLAH"]["external_id"];

	}
	else {

		header("HTTP/1.1 401");
		die;

	}

	$data["payment"]["paid"] = true;
	if (($subs = enroll_user($log_id, $data["user"], $context, $data["payment"])) === false) {

		header("HTTP/1.1 401");
		die;

	}

	log_action($log_id, "success");

	die;

	// header("Content-Type: application/json");

?>