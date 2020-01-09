<?php

	// Prevent exclusive access
	if (!defined("JAWS")) {
		header("HTTP/1.1 401 Unauthorized");
		die();
	}

	// Init Session
	auth_session_init();

	// Auth Check - Expecting Session Only !
	if ((!auth_session_is_logged()) || (!auth_session_is_allowed("batcave") && !auth_session_is_allowed("btc.mobile"))) {
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

	if (!empty($_GET["id"])) {
		$where = "course_id = ".db_sanitize($_GET["id"]);
	}
	else {

		$res_courses = db_query(
			"SELECT
				name,
				course_id AS id
			FROM
				course
			WHERE
				sis_id IS NOT NULL
				AND sis_id != ''
			ORDER BY
				course_id ASC;"
		);

		die(json_encode(/*["courses" => */$res_courses/*]*/));

	}

?>