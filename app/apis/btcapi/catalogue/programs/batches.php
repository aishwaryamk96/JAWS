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

	if (!empty(($program = bundle_get($_GET["id"], false)))) {

		if ($program["bundle_type"] == "bootcamps") {

			$batches = [];

			$res_batches = batches_get_for_bootcamp($program["bundle_id"]);
			foreach ($res_batches as $batch) {

				$enr_count = db_query("SELECT COUNT(s.subs_id) AS total FROM subs AS s INNER JOIN subs_meta AS m ON m.subs_id = s.subs_id WHERE s.status = 'active' AND m.batch_id = ".$batch["id"].";");
				$batch["enr_count"] = intval($enr_count[0]["total"]);

				$batches[] = $batch;

			}

		}

		die(json_encode(["batches" => $batches]));

	}

	header("HTTP/1.1 404");

?>