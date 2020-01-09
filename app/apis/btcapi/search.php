<?php

	// Prevent exclusive access
	if (!defined("JAWS")) {
		header("HTTP/1.1 401 Unauthorized");
		die();
	}

	// Init Session
	auth_session_init();

	// Auth Check - Expecting Session Only !
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

	$sanitized_query = db_sanitize("%".$_GET["q"]."%");

	$results = [];

	$query_exec_time = microtime(true);
	$res_users = db_query(
		"SELECT
			user.user_id AS id,
			enr.sis_id,
			user.name,
			user.email,
			IF(DATE(subs.start_date) = CURDATE(), 'Today', DATE_FORMAT(subs.start_date, '%e %b, %Y')) AS start_date,
			IF(DATE(subs.end_date) = CURDATE(), 'Today', DATE_FORMAT(subs.end_date, '%e %b, %Y')) AS end_date,
			subs.status,
			CEIL(DATEDIFF(DATE(subs.end_date), DATE(subs.start_date)) / 30) AS duration,
			IF(subs_meta.bundle_id IS NULL, 'Custom Combo', bundle.name) AS bundle,
			'user' AS type,
			subs_count.total_subs,
			user.photo_url
		FROM
			user
		LEFT JOIN
			subs ON subs.user_id = user.user_id AND subs.status = 'active'
		LEFT JOIN
			(
				SELECT
					COUNT(subs_id) AS total_subs,
					user_id
				FROM
					subs
				WHERE
					status = 'active'
				GROUP BY
					user_id
			) AS subs_count ON subs_count.user_id = user.user_id
		LEFT JOIN
			subs_meta ON subs_meta.subs_id = subs.subs_id
		LEFT JOIN
			user_enrollment AS enr ON enr.user_id = user.user_id AND enr.status = 'active'
		LEFT JOIN
			course_bundle AS bundle ON bundle.bundle_id = subs_meta.bundle_id
		WHERE
			user.email LIKE $sanitized_query
			OR
			user.email_2 LIKE $sanitized_query
			OR
			user.soc_fb LIKE $sanitized_query
			OR
			user.soc_gp LIKE $sanitized_query
			OR
			user.soc_li LIKE $sanitized_query
			OR
			user.name LIKE $sanitized_query
			OR
			user.phone LIKE $sanitized_query
			OR
			enr.sis_id LIKE $sanitized_query
			OR
			enr.lab_user LIKE $sanitized_query
		GROUP BY
			user.user_id
		ORDER BY
			subs.subs_id DESC,
			enr.enr_id ASC;"
	);

	$res_courses = db_query("SELECT course_id AS id, sis_id, name, 'course' AS type FROM course WHERE name LIKE $sanitized_query OR sis_id LIKE $sanitized_query OR sp_code LIKE $sanitized_query OR il_code LIKE $sanitized_query;");
	$res_bundles = db_query("SELECT bundle_id AS id, name, 'bundle' AS type FROM course_bundle WHERE name LIKE $sanitized_query;");

	$res_leads = [];
	if (!empty($_SESSION["user"]["batcave_pref"]["settings"]["search"]["leads"])) {

		if (strpos($sanitized_query, "@") !== false) {
			$res_leads = db_query("SELECT COUNT(lead_id) AS records, email, email AS id, name, phone, 'lead' AS type FROM user_leads_basic_compiled WHERE email LIKE $sanitized_query GROUP BY email;");
		}

	}

	$query_exec_time = microtime(true) - $query_exec_time;

	$results = array_merge($res_users, $res_courses, $res_bundles, $res_leads);

	if (empty($results)) {
		$query_exec_time = 0;
	}

	die(
		json_encode(
			[
				"time" => round($query_exec_time, 2, PHP_ROUND_HALF_DOWN),
				"results" => $results,
				"usersCount" => count($res_users),
				"coursesCount" => count($res_courses),
				"bundlesCount" => count($res_bundles),
				"leadsCount" => count($res_leads)
			]
		)
	);

?>