<?php

	// Prevent exclusive access
	if (!defined("JAWS")) {
		header("HTTP/1.1 401 Unauthorized");
		die();
	}

	// Init Session
	auth_session_init();

	// // Auth Check - Expecting Session Only !
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

	$sc = [];
	$scp = 0;
	$in = [];
	$inp = 0;
	$scIds = [];
	$inIds = [];

	$all = [];
	$get_all = $_GET["all"] ?? false;

	$issues = db_query("SELECT ui.id, ui.user_id, ui.issue, ui.created_by, DATE_FORMAT(ui.created_at, '%e %b, %Y %I:%i %p') AS created_at, DATE_FORMAT(ui.resolved_at, '%e %b, %Y %I:%i %p') AS resolved_at, TIMESTAMPDIFF(DAY, ui.created_at, NOW()) AS days, u.name, u.photo_url, a.user_id AS creator_id, a.name AS creator_name, r.user_id AS resolver_id, r.name AS resolved_name FROM user_issues AS ui INNER JOIN user AS u ON u.user_id = ui.user_id INNER JOIN user AS a ON a.user_id = ui.created_by LEFT JOIN user AS r ON r.user_id = ui.resolved_by ORDER BY ui.resolved_at ASC, ui.created_at ASC;");
	foreach ($issues as $issue) {

		$i = json_decode($issue["issue"], true);
		$issue["desc"] = $i["d"];
		if ($issue["created_by"] == $issue["user_id"]) {

			if (empty($issue["resolved_at"])) {
				$scp++;
			}

			if ($get_all) {

				$issue["sc"] = 1;
				$all[] = $issue;

			}
			else {
				$sc[] = $issue;
			}

			$scIds[] = $issue["id"];

		}
		else {

			if (empty($issue["resolved_at"])) {
				$inp++;
			}

			if ($get_all) {

				$issue["in"] = 1;
				$all[] = $issue;

			}
			else {
				$in[] = $issue;
			}

			$inIds[] = $issue["id"];

		}

	}

	die(json_encode(["sc" => $sc, "in" => $in, "scp" => $scp, "inp" => $inp, "all" => $all, "t" => $scp + $inp, "scIds" => $scIds, "inIds" => $inIds]));

?>