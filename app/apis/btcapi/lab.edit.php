<?php

	// Init Session
	auth_session_init();

	// Auth Check - Expecting Session Only !
	if ((!auth_session_is_logged()) || (!auth_session_is_allowed("lab.edit"))) {
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

	if (!auth_session_is_allowed("lab.edit")) {
		die(json_encode(["status" => false, "error" => "Are you trying to hack...? Coz, its not gonna work."]));
	}

	if (empty($_POST)) {
		$_POST = json_decode(file_get_contents("php://input"), true);
	}

	if (empty($_POST["lab"])) {

		header("HTTP/1.1 400 Bad Request");
		die();

	}

	$new_lab = $_POST["lab"];
	$old_lab = false;

	if (!empty($new_lab["id"])) {

		if ($new_lab["id"] != "-1") {

			if (empty(($lab = db_query("SELECT * FROM aws_labs WHERE id = ".db_sanitize($new_lab["id"]))))) {

				header("HTTP/1.1 500");
				die;

			}

			$lab = $lab[0];
			if (($status = ami_config_changed(json_decode($lab["config"], true), $new_lab["config"])) == -1) {
				die(json_encode(["status" => false, "error" => "One of the config fields is empty"]));
			}

			if ($status == 1) {

				$new_lab["previous_aws_lab_id"] = $lab["id"];
				$old_lab = $lab;

			}

		}
		else {
			unset($new_lab["id"]);
		}

	}

	$backup = $new_lab;
	if (($new_lab = validate_lab_info($new_lab)) === false) {
		die(json_encode(["status" => false, "error" => "One or more required fields are missing."]));
	}

	if (!empty($new_lab["id"])) {
		db_exec("UPDATE aws_labs SET name=".$new_lab["name"].", route=".$new_lab["route"].", config=".$new_lab["config"].", lifespan=".$new_lab["lifespan"].", domain=".$new_lab["domain"].", status=".$new_lab["status"].", previous_aws_lab_id=".$new_lab["previous_aws_lab_id"]." WHERE id = ".db_sanitize($new_lab["id"]).";");
	}
	else {

		db_exec("INSERT INTO aws_labs (name, route, config, lifespan, domain, status, previous_aws_lab_id) VALUES (".$new_lab["name"].", ".$new_lab["route"].", ".$new_lab["config"].", ".$new_lab["lifespan"].", ".$new_lab["domain"].", ".$new_lab["status"].", ".$new_lab["previous_aws_lab_id"].");");
		$backup["id"] = db_get_last_insert_id();

	}

	if ($old_lab !== false) {
		db_exec("UPDATE aws_labs SET status = '-2' WHERE id = ".db_sanitize($old_lab["id"]).";");
	}

	die(json_encode(["status" => true, "lab" => $backup]));

	function ami_config_changed($old_config, $new_config) {

		foreach ($old_config as $key => $value) {

			if (!array_key_exists($key, $new_config)) {
				return -1;
			}

			if ($new_config[$key] != $value) {
				return 1;
			}

		}

		return 0;

	}

	function validate_lab_info($lab) {

		if (empty($lab["name"])) {
			return false;
		}

		if (empty($lab["route"])) {
			return false;
		}

		if (empty($lab["domain"])) {
			return false;
		}

		if (empty($lab["lifespan"]) || intval($lab["lifespan"]) == 0) {
			return false;
		}

		if (!array_key_exists("status", $lab)) {
			$lab["status"] = 1;
		}
		if (!is_numeric($lab["status"])) {
			return false;
		}

		load_library("setting");

		$defaults = json_decode(setting_get("aws.ami.defaults"), true);
		foreach ($defaults as $key => $value) {

			if (empty($lab["config"][$key])) {
				return false;
			}

		}

		$lab["name"] = db_sanitize($lab["name"]);
		$lab["route"] = db_sanitize($lab["route"]);
		$lab["domain"] = db_sanitize($lab["domain"]);
		$lab["lifespan"] = db_sanitize(intval($lab["lifespan"]));
		$lab["status"] = db_sanitize(intval($lab["status"]));
		$lab["previous_aws_lab_id"] = empty($lab["previous_aws_lab_id"]) ? "NULL" : db_sanitize(intval($lab["previous_aws_lab_id"]));
		$lab["config"] = db_sanitize(json_encode($lab["config"]));

		return $lab;

	}

?>