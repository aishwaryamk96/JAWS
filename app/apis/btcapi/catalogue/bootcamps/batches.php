<?php

	authorize_api_call("", true);

	if (!empty($_GET["id"])) {

		if (!empty(($batch = batch_get($_GET["id"])))) {

			if (!empty(($external_assoc = db_query("SELECT * FROM system_persistence_map WHERE layer = 'wppl' AND entity_type = 'bundle' AND native_id = ".$program["bundle_id"].";")))) {
				$program["ext_id"] = $external_assoc[0]["ext_id"];
			}

			$enrs = db_query("SELECT subs.subs_id FROM subs INNER JOIN subs_meta AS meta ON meta.subs_id = subs.subs_id WHERE subs.status IN ('active', 'pending') AND meta.bundle_id = ".$program["bundle_id"].";");
			$program["enr_count"] = count($enrs);

			die(json_encode(["program" => $program]));

		}

		header("HTTP/1.1 404");

	}

?>