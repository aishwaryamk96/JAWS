<?php

	authorize_api_call("enrollment.get.adv", true);

	if (empty($_POST)) {
		$_POST = json_decode(file_get_contents("php://input"), true);
	}

	if (empty($_POST["user_id"])) {

		header("HTTP/1.1 422");
		die(json_encode(["errors" => ["user_id cannot be null"]]));

	}

	$user_id = db_sanitize($_POST["user_id"]);
	$res = db_query("SELECT sis_id FROM user_enrollment WHERE user_id = $user_id AND status = 'active' ORDER BY enr_id DESC LIMIT 1;");

	load_plugin("jlc");
	$jlc = new JLC;
	echo json_encode(["status" => $jlc->enableAccount([$res[0]["sis_id"]])]);

?>