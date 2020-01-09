<?php

/*
           8 8888       .8. `8.`888b                 ,8' d888888o.
           8 8888      .888. `8.`888b               ,8'.`8888:' `88.
           8 8888     :88888. `8.`888b             ,8' 8.`8888.   Y8
           8 8888    . `88888. `8.`888b     .b    ,8'  `8.`8888.
           8 8888   .8. `88888. `8.`888b    88b  ,8'    `8.`8888.
           8 8888  .8`8. `88888. `8.`888b .`888b,8'      `8.`8888.
88.        8 8888 .8' `8. `88888. `8.`888b8.`8888'        `8.`8888.
`88.       8 888'.8'   `8. `88888. `8.`888`8.`88'     8b   `8.`8888.
  `88o.    8 88'.888888888. `88888. `8.`8' `8,`'      `8b.  ;8.`8888
    `Y888888 ' .8'       `8. `88888. `8.`   `8'        `Y8888P ,88P'

    	JIGSAW ACADEMY WORKFLOW SYSTEM v2
    	---------------------------------
*/

	// Prevent exclusive access
	if (!defined("JAWS")) {
		header('Location: https://www.jigsawacademy.com');
		die();
	}

	if (!auth_api("subs.get")) {
		header("HTTP/1.1 401 Unauthorized");
		die();
	}

	$query = "SELECT
				subs.subs_id,
				enr.sis_id,
				enr.course_id,
				subs.start_date,
				IF (ad.subs_id IS NOT NULL, ad.end_date, IF (subs.end_date_ext IS NOT NULL, subs.end_date_ext, subs.end_date)) AS end_date,
				bundle.name
			FROM
				subs
			LEFT JOIN
				(
					SELECT
						subs_id,
						MAX(end_date) AS end_date
					FROM
						access_duration
					GROUP BY
						subs_id
				) AS ad
				ON ad.subs_id = subs.subs_id
			INNER JOIN
				user_enrollment AS enr
				ON enr.subs_id = subs.subs_id
			LEFT JOIN
				subs_meta
				ON subs_meta.subs_id = subs.subs_id
			LEFT JOIN
				course_bundle AS bundle
				ON bundle.bundle_id = subs_meta.bundle_id
			LEFT JOIN
            	system_persistence_map AS seq
            	ON seq.native_id = enr.course_id AND seq.layer = 'enr_sequence'";

	$where_clause = " WHERE ((ad.subs_id IS NOT NULL AND ad.end_date > CURRENT_DATE) OR subs.end_date > CURRENT_DATE) AND subs.status='active' AND enr.status='active' AND enr.course_id != 62 AND enr.course_id != 110 AND IF (bundle.bundle_id IS NOT NULL, bundle.bundle_id < 68 AND bundle.is_bootcamp = 0, 1)";

	if (!empty($_POST["query"])) {

		if (strpos($_POST["query"], "@") !== false) {

			$query .= " INNER JOIN user ON user.user_id = subs.user_id";
			$where_clause .= " AND user.email LIKE ".db_sanitize($_POST["query"]);

		}
		else {
			$where_clause .= " AND enr.sis_id LIKE ".db_sanitize($_POST["query"]);
		}

	}

	//die($query.$where_clause." ORDER BY subs.subs_id ASC, CAST(ext_id AS SIGNED) ASC;");

	$res_subs = db_query($query.$where_clause." ORDER BY subs.subs_id ASC, CAST(ext_id AS SIGNED) ASC;");

	if (!isset($res_subs[0])) {
		die(json_encode([]));
	}

	$coures = [];
	$response = [];
	foreach ($res_subs as $subs) {

		// $access = db_query("SELECT * FROM access_duration WHERE subs_id = ".$subs["subs_id"]." ORDER BY id DESC;");
		// if (isset($access[0])) {
		// 	$subs["end_date"] = $access[0]["end_date"];
		// }

		$response[$subs["sis_id"]][$subs["subs_id"]]["sd"] = $subs["start_date"];
		$response[$subs["sis_id"]][$subs["subs_id"]]["ed"] = $subs["end_date"];
		$response[$subs["sis_id"]][$subs["subs_id"]]["c"][] = $subs["course_id"];

		if (!in_array($subs["course_id"], $courses)) {
			$courses[] = $subs["course_id"];
		}

		if (!empty($subs["name"])) {
			$response[$subs["sis_id"]][$subs["subs_id"]]["b"] = $subs["name"];
		}

	}

	$courses = db_query("SELECT course_id, name, sis_id FROM course WHERE course_id IN (".implode(",", $courses).");");
	$response_courses = [];
	foreach ($courses as $course) {
		$response_courses[$course["course_id"]] = ["n" => $course["name"], "i" => $course["sis_id"]];
	}

	die(json_encode(["s" => $response, "c" => $response_courses, "n" => count($response)]));

?>