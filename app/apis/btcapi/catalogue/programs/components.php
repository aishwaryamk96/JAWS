<?php

	// Send Headers
	header('Access-Control-Allow-Credentials: true');
	header("Content-Type: application/json");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Pragma-directive: no-cache");
	header("Cache-directive: no-cache");
	header("Cache-Control: private, must-revalidate, max-age=0, no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
	header("Pragma: no-cache");

	if (!auth_session_is_allowed("batcave")) {

		header("HTTP/1.1 403");
		die(json_encode(["status" => false, "error" => "Are you trying to hack...? Coz, its not gonna work."]));

	}

	if (empty($_GET["id"])) {
		die(header("HTTP/1.1 404"));
	}

	if (!empty(($program = bundle_get($_GET["id"])))) {

		if (!empty(($external_assoc = db_query("SELECT * FROM system_persistence_map WHERE layer = 'wppl' AND entity_type = 'bundle' AND native_id = ".$program["bundle_id"].";")))) {
			$program["ext_id"] = $external_assoc[0]["ext_id"];
		}

		$enrs = db_query("SELECT subs.subs_id FROM subs INNER JOIN subs_meta AS meta ON meta.subs_id = subs.subs_id WHERE subs.status IN ('active', 'pending') AND meta.bundle_id = ".$program["bundle_id"].";");
		$program["enr_count"] = count($enrs);

		die(json_encode(["program" => $program]));

	}

	header("HTTP/1.1 404");

?>