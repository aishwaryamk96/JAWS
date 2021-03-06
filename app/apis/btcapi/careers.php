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

	// Send Headers
	header('Access-Control-Allow-Credentials: true');
	header("Content-Type: application/json");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Pragma-directive: no-cache");
	header("Cache-directive: no-cache");
	header("Cache-Control: private, must-revalidate, max-age=0, no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
	header("Pragma: no-cache");

	load_module("careers");

	$careers = [];
	$jobs = careers_get() ?: [];
	foreach ($jobs as $job) {

		$job["tools"] = json_decode($job["tools"], true);
		$job["preview"] = $job["html"];
		$job["html"] = htmlentities($job["html"]);

		$careers[] = $job;

	}

	$courses = [];

	$res_courses = json_decode(file_get_contents("https://jigsawacademy.net/app/courses.php?attrs=id,name,sis_source_id&state=available&order=-id"), true);
	foreach ($res_courses as $course) {
		$courses[] = ["id" => $course["id"], "sis_id" => $course["sis_source_id"], "name" => $course["name"]];
	}

	die(json_encode(["canDownload" => true, "canCreate" => true, "careers" => $careers, "courses" => $courses]));

?>