<?php

	// Prevent exclusive access
	if (!defined("JAWS")) {
		header("HTTP/1.1 401 Unauthorized");
		die();
	}

	// Init Session
	auth_session_init();

	// Auth Check - Expecting Session Only !
	if ((!auth_session_is_logged()) || (!auth_session_is_allowed("batcave") && (!auth_session_is_allowed("lab.view") || !auth_session_is_allowed("lab.edit")))) {
		header("HTTP/1.1 401 Unauthorized");
		die();
	}

	load_library("setting");

	// Send Headers
	header('Access-Control-Allow-Credentials: true');
	header("Content-Type: application/json");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Pragma-directive: no-cache");
	header("Cache-directive: no-cache");
	header("Cache-Control: private, must-revalidate, max-age=0, no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
	header("Pragma: no-cache");

	$labs = [];

	$res_labs = db_query("SELECT id, name, route, config, lifespan, domain, status, previous_aws_lab_id, DATE_FORMAT(created_at, '%e %M %Y, %l:%i %p') AS created_at, DATE_FORMAT(updated_at, '%e %M %Y, %l:%i %p') AS updated_at FROM aws_labs WHERE status > -2;");
	foreach ($res_labs as $lab) {

		$lab["config"] = json_decode($lab["config"]);
		$lab["versions"] = lab_get_previous_versions($lab);
		$lab["lifespan"] = intval($lab["lifespan"]);
		$lab["status"] = intval($lab["status"]);

		$labs[$lab["id"]] = $lab;

	}

	die(json_encode(["labs" => $labs, "edit" => auth_session_is_allowed("lab.edit"), "defaults" => json_decode(setting_get("aws.ami.defaults"))]));

	function lab_get_previous_versions($lab) {

		$prev = [];

		while (!is_null($lab["previous_aws_lab_id"])) {

			if (!empty(($lab = db_query("SELECT id, name, route, config, lifespan, domain, status, previous_aws_lab_id, DATE_FORMAT(created_at, '%e %M %Y, %l:%i %p') AS created_at, DATE_FORMAT(updated_at, '%e %M %Y, %l:%i %p') AS updated_at FROM aws_labs WHERE id = ".$lab["previous_aws_lab_id"])))) {
				$prev[] = $lab;
			}
			else {
				break;
			}

		}

		return $prev;

	}

?>