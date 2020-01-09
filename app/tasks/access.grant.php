<?php

	$res = db_query("SELECT l.id, e.sis_id FROM user_enrollment AS e INNER JOIN user_logs AS l ON l.user_id = e.user_id WHERE l.category = 'access.grant' AND l.status = 'pending' AND e.status = 'active' GROUP BY l.user_id;");

	if (empty($res)) {
		die;
	}

	$jigIds = [];
	foreach ($res as $row) {
		$jigIds[$row["sis_id"]] = $row["id"];
	}

	load_plugin("jlc");
	$jlc = new JLC;

	$pending = [];
	$response = $jlc->statusFor(array_keys($jigIds));
	foreach ($response as $jigId => $row) {

		if ($row["user"] == "registered" && $row["pseudonym"] == "active") {
			db_exec("UPDATE user_logs SET status = 'done' WHERE id = ".$jigIds[$jigId].";");
		}
		else {

			if ($jlc->enableAccount($jigId)) {
				db_exec("UPDATE user_logs SET status = 'done' WHERE id = ".$jigIds[$jigId].";");
			}

		}

	}

?>