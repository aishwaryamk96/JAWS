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

	// Send Headers
	header('Access-Control-Allow-Credentials: true');
	header("Content-Type: application/json");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Pragma-directive: no-cache");
	header("Cache-directive: no-cache");
	header("Cache-Control: private, must-revalidate, max-age=0, no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
	header("Pragma: no-cache");

	$where = [];
	if (!empty($_POST["student"])) {
		$where[] = "user.email LIKE ".db_sanitize($_POST["student"]);
	}
	if (!empty($_POST["start_date_from"])) {
		$where[] = "DATE(subs.start_date) >= ".db_sanitize($_POST["start_date_from"]);
	}
	if (!empty($_POST["start_date_to"])) {
		$where[] = "DATE(subs.start_date) <= ".db_sanitize($_POST["start_date_to"]);
	}
	if (!empty($_POST["end_date_from"])) {
		$where[] = "DATE(subs.end_date) >= ".db_sanitize($_POST["end_date_from"]);
	}
	if (!empty($_POST["end_date_to"])) {
		$where[] = "DATE(subs.end_date) <= ".db_sanitize($_POST["end_date_to"]);
	}
	if (!empty($_POST["assigned_to"])) {
		$where[] = "support.assigned_to = ".$_POST["assigned_to"];
	}
	if (!empty($_POST["rep_id"])) {
		$where[] = "(support.called_by = ".$_POST["rep_id"]." OR support.sms_sent_by = ".$_POST["rep_id"].")";
	}
	if (!empty($_POST["call_status"])) {
		$where[] = "support.call_status = ".db_sanitize($_POST["call_status"]);
	}
	if (!empty($_POST["course"])) {
		$where[] = "enr.course_id = ".$_POST["course"];
	}
	if (!empty($_POST["bundle"])) {
		$where[] = "subs_meta.bundle_id = ".$_POST["bundle"];
	}

	if (count($where) == 0) {
		$where = "subs.start_date > DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
	}
	else {
		$where = implode(" AND ", $where);
	}

	$res_subs = db_query("SELECT
								enr.sis_id AS sis_id,
								enr.lab_pass AS lab_pass,
								subs.subs_id,
								user.name AS name,
								user.email AS email,
								user.phone AS phone,
								IF(support.assigned_to IS NOT NULL, assignee.name, 'Unassigned') AS assigned_to,
								um.city AS city,
								IF(DATE(subs.start_date) = CURDATE(), 'Today', DATE_FORMAT(subs.start_date, '%e %b, %Y')) AS start_date,
								IF(DATE(subs.end_date) = CURDATE(), 'Today', DATE_FORMAT(subs.end_date, '%e %b, %Y')) AS end_date,
								CEIL(DATEDIFF(DATE(subs.end_date), DATE(subs.start_date)) / 30) AS duration,
								GROUP_CONCAT(CONCAT(enr.enr_id, '=', course.name, '=', IF(enr.lab_ip IS NOT NULL, enr.lab_ip, ''), '=', enr.lab_status) separator ';') AS courses,
								DATE_FORMAT(support.email_sent_at, '%e %b %Y, %h:%i %p') AS email_sent_at,
								IF(DATE(support.email_sent_at) = CURDATE(), DATE_FORMAT(support.email_sent_at, '%h:%i %p Today'), DATE_FORMAT(support.email_sent_at, '%e %b %Y, %h:%i %p')) AS email_sent_at_flag,
								DATE_FORMAT(support.called_at, '%e %b %Y, %h:%i %p') AS called_at,
								IF(DATE(support.called_at) = CURDATE(), DATE_FORMAT(support.called_at, '%h:%i %p Today'), DATE_FORMAT(support.called_at, '%e %b %Y, %h:%i %p')) AS called_at_flag,
								agent_caller.name AS caller,
								support.call_status,
								agent_smser.name AS smser,
								DATE_FORMAT(support.sms_sent_at, '%e %b %Y, %h:%i %p') AS sms_sent_at,
								IF(DATE(support.sms_sent_at) = CURDATE(), DATE_FORMAT(support.sms_sent_at, '%h:%i %p Today'), DATE_FORMAT(support.sms_sent_at, '%e %b %Y, %h:%i %p')) AS sms_sent_at_flag,
								support.comments,
								IF(subs_meta.bundle_id IS NULL, 'Custom Combo', bundle.name) AS bundle,
								(SELECT
										GROUP_CONCAT(
											CONCAT(
												history.channel, '@#', historical_rep.name, '@#', history.status, '@#', DATE_FORMAT(history.timestamp, '%e %b %Y, %h:%i %p'), '@#', history.comments
											) SEPARATOR '^#^'
										)
									FROM
										user_enr_meta_history AS history
									INNER JOIN
										user AS historical_rep ON historical_rep.user_id = history.rep_id AND history.rep_id IS NOT NULL
									WHERE
										history.subs_id = subs.subs_id
									ORDER BY `timestamp`
								) AS historical
							FROM
								subs
							LEFT JOIN
								subs_meta ON subs_meta.subs_id = subs.subs_id
							INNER JOIN
								user ON user.user_id = subs.user_id
							INNER JOIN
								user_meta AS um ON um.user_id = subs.user_id
							INNER JOIN
								user_enrollment AS enr ON enr.subs_id = subs.subs_id
							INNER JOIN
								course ON course.course_id = enr.course_id
							LEFT JOIN
								user_enr_meta AS support ON support.subs_id = subs.subs_id
							LEFT JOIN
								user AS agent_caller ON agent_caller.user_id = support.called_by AND support.called_by IS NOT NULL
							LEFT JOIN
								user AS agent_smser ON agent_smser.user_id = support.sms_sent_by AND support.sms_sent_by IS NOT NULL
							LEFT JOIN
								course_bundle AS bundle ON bundle.bundle_id = subs_meta.bundle_id
							LEFT JOIN
								user AS assignee ON assignee.user_id = support.assigned_to
							WHERE
								subs.status='active'
								AND
								enr.status='active'
								AND
								".$where."
							GROUP BY subs.subs_id
							ORDER BY subs.start_date DESC");

	//echo json_encode($res_subs); exit();

	$ret = [];
	foreach ($res_subs as $subs) {

		$courses = explode(";", $subs["courses"]);
		unset($subs["courses"]);
		foreach ($courses as $course) {

			$each = explode("=", $course);
			$subs["courses"][] = ["enr_id" => $each[0], "name" => $each[1], "lab" => $each[2], "lab_status" => ($each[3] == "ul" ? true : false)];

		}
		$historical = explode("^#^", $subs["historical"]);
		unset($subs["historical"]);
		foreach ($historical as $history) {

			$each = explode("@#", $history);
			$subs["historical"][] = ["channel" => $each[0], "name" => $each[1], "status" => $each[2], "time" => $each[3], "comments" => $each[4]];

		}

		$ret[] = $subs;

	}

	die(json_encode($ret));

?>