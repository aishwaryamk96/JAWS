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

	$enr_id = db_sanitize($_GET["id"]);

	$res_enr = db_query(
		"SELECT
			user.user_id,
			user.name AS user_name,
			user.email,
			enr.sis_id AS jig_id,
			course.course_id,
			course.name AS course_name,
			section.id AS section_id,
			section.sis_id AS section_code,
			enr.lab_ip,
			enr.lab_user,
			enr.lab_pass,
			CONCAT(MONTHNAME(section.start_date), ' - ', IF (section.learn_mode = 3, 'Mentor led', IF (section.learn_mode = 2, 'Regular', 'Catalyst'))) AS section_name
		FROM
			user_enrollment AS enr
		INNER JOIN
			user
			ON user.user_id = enr.user_id
		INNER JOIN
			course
			ON course.course_id = enr.course_id
		INNER JOIN
			course_section AS section
			ON section.id = enr.section_id
		WHERE
			enr.enr_id = $enr_id;"
	);


	if (!empty($res_enr)) {

		$res_enr = $res_enr[0];
		$res_enr["jlc"] = json_decode(file_get_contents("https://jigsawacademy.net/app/enrollment.php?id=".$res_enr["jig_id"]."&section_code=".$res_enr["section_code"]), true);

	}

	die(json_encode($res_enr));

?>