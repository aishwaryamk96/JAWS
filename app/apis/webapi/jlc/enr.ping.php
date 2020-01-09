<?php

	if (empty($_POST["subs_id"])) {

		header("HTTP/1.1 400");
		die;

	}

	$subs_id = db_sanitize($_POST["subs_id"]);

	$enrs = db_query("SELECT e.sis_file, e.sis_status, s.sis_batch_id, c.sis_id FROM user_enrollment AS e INNER JOIN sis AS s ON s.filename = e.sis_file INNER JOIN course AS c ON c.course_id = e.course_id WHERE e.subs_id = $subs_id;");
	if (empty($enrs)) {

		header("HTTP/1.1 400");
		die;

	}

	header("Content-type: application/json");

	if ($enrs[0]["sis_status"] == "ul") {
		die(json_encode(["status" => 1, "token" => psk_generate("jlc.setup", $_POST["subs_id"], 'fskill'), "course_id" => $enrs[0]["sis_id"]]));
	}

	load_plugin("jlc");

	$status = -1;
	$token = "";

	$jlc = new JLC;
	$res = $jlc->sisImportStatus($enrs[0]["sis_batch_id"]);
	if (strpos($res["workflow_state"], "imported") !== false) {

		$status = 1;
		$token = psk_generate("jlc.setup", $_POST["subs_id"], 'fskill');
		$course_id = $enrs[0]["sis_id"];

	}
	else if ($res["workflow_state"] == "created") {
		$status = 0;
	}

	$response = ["status" => $status];
	if (!empty($token)) {

		$response["token"] = $token;
		$response["course_id"] = $course_id;

	}

	die(json_encode($response));

?>