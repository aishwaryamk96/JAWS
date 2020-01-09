<?php

	$headers = getallheaders();
	if (empty($headers["Authorization"]) || strtolower($headers["Authorization"]) != "bearer gyfov7sciely6f7y4ufg7ea4r34f734ofgauef") {
		die(header("HTTP/1.1 401"));
	}

	if (empty(($_POST["sis_id"]))) {
		die(header("HTTP/1.1 400"));
	}

	$sis_id = db_sanitize($_POST["sis_id"]);

	$enrs = db_query(
		"SELECT
			b.jlc_name,
			GROUP_CONCAT(c.sis_id SEPARATOR ';') AS courses
		FROM
			user_enrollment AS e
		INNER JOIN
			course AS c
			ON c.course_id = e.course_id
		INNER JOIN
			subs_meta AS m
			ON m.subs_id = e.subs_id
		INNER JOIN
			course_bundle AS b
			ON b.bundle_id = m.bundle_id
		WHERE
			e.sis_id = $sis_id
			AND e.status = 'active'
			AND b.jlc_name IS NOT NULL
		GROUP BY
			e.subs_id;"
	);

	header("Content-Type: application/json");

	if (empty($enrs)) {
		die(json_encode([]));
	}

	$res = [];
	foreach ($enrs as $enr) {
		$res[$enr["jlc_name"]] = explode(";", $enr["courses"]);
	}

	die(json_encode($res));

?>