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

	$stats = ["total" => 0];
	$res_stats = db_query("SELECT COUNT(id) AS count, `key` FROM user_content WHERE `key` IN ('android', 'gcm_id', 'ios') GROUP BY `key`;");
	foreach ($res_stats as $numbers) {

		$stats[$numbers["key"]] = $numbers["count"];
		$stats["total"] += $numbers["count"];
	}

	die(json_encode($stats));

?>