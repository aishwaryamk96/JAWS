<?php

	// Prevent exclusive access
	if (!defined("JAWS")) {
		header("HTTP/1.1 401 Unauthorized");
		die();
	}

	// Init Session
	auth_session_init();

	// Auth Check - Expecting Session Only !
	if ((!auth_session_is_logged()) || (!auth_session_is_allowed("batcave"))) {
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

	$course_id = db_sanitize($_GET["id"]);

	$res_course = db_query(
		"SELECT
			course.*,
			IF (lab.lab_ip IS NOT NULL, GROUP_CONCAT(lab.lab_ip SEPARATOR '+'), NULL) AS labs,
			GROUP_CONCAT(
					section.id, '=',
					section.sis_id, '=',
					section.learn_mode, '=',
					CONCAT(MONTHNAME(section.start_date), ' - ', IF (section.learn_mode = 3, 'Mentor led', IF (section.learn_mode = 2, 'Regular', 'Catalyst')))
				SEPARATOR '+'
			) AS sections
		FROM
			course
		INNER JOIN
			course_section AS section
			ON section.course_id = course.course_id
		LEFT JOIN
			course_lab AS lab
			ON lab.course_id = course.course_id AND lab.status = 1
		WHERE
			course.course_id = $course_id
		GROUP BY
			course.course_id
		ORDER BY
			section.start_date DESC;"
	);

	if (!empty($res_course)) {

		$res_course = $res_course[0];
		$labs = [];
		if (!empty($res_course["labs"])) {
			$labs = explode("=", $res_course["labs"]);
		}
		$res_course["labs"] = $labs;

		$sections = [];
		$res_sections = explode("+", $res_course["sections"]);
		foreach ($res_sections as $section) {

			$section = explode("=", $section);
			$sections[] = ["id" => $section[0], "sis_id" => $section[1], "learn_mode" => $section[2], "name" => $section[3]];

		}
		$res_course["sections"] = $sections;

		$enrs = db_query("SELECT COUNT(enr_id) AS enrs FROM user_enrollment WHERE course_id = $course_id AND status = 'active';");
		if (!empty($enrs)) {
			$res_course["enrollments"] = $enrs[0]["enrs"];
		}
		else {
			$res_course["enrollments"] = 0;
		}

	}

	die(json_encode($res_course));

?>