<?php

	register_shutdown_function("shutdown");

	load_plugin("mobile_app");
	load_module("user");

	header("Content-type: application/json");

	$mobile = new MobileApp;

	$headers = getallheaders();
	if (!$mobile->authorizeRequest($headers["Authorization"])) {
		die(header("HTTP/1.1 401"));
	}

	if (empty($_POST)) {
		$_POST = json_decode(file_get_contents("php://input"), true);
	}

	if (empty($_POST)) {
		die(header("HTTP/1.1 400"));
	}

	$user;
	$new_user = false;
	$res_user = user_get_by_email($_POST["email"]);
	if (!$res_user) {
		die(header("HTTP/1.1 401"));
	}

	$freeze = false;
	$user_id = $res_user["user_id"];
	$res_freeze = db_query("SELECT * FROM freeze WHERE user_id = $user_id AND DATE(start_date) < CURDATE() AND DATE(end_date) > CURDATE();");
	if (!empty($res_freeze)) {
		$freeze = true;
	}

	$courses = db_query("SELECT c.course_id, c.sis_id, e.sis_id AS jig_id FROM course AS c INNER JOIN user_enrollment AS e ON e.course_id = c.course_id WHERE e.user_id = $user_id AND e.status = 'active' AND c.sis_id != 'SKIPTHISCOURSE' GROUP BY c.course_id;");

	if (empty($courses)) {
		die(header("HTTP/1.1 401"));
	}

	$course_codes = [];
	foreach ($courses as $course) {
		$course_codes[$course["sis_id"]] = $course["course_id"];
	}

	$course_info = $mobile->getCoursesTopics(array_keys($course_codes), $courses[0]["jig_id"], $freeze);
	$course_topics = $course_info["t"];
	$course_progress = $course_info["p"];

	$enrs = [];
	foreach ($course_codes as $sis_id => $id) {

		if (empty($course_topics[$sis_id])) {
			continue;
		}

		$enrs[$id] = [
			"t" => $course_topics[$sis_id] ?? new StdClass,
			"p" => $course_progress[$sis_id]
		];

	}

	function shutdown() {
		die(var_dump(error_get_last()));
	}

	die(json_encode($enrs));

?>