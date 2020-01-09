<?php

	load_plugin("mobile_app");

	$mobile = new MobileApp;

	$headers = getallheaders();
	if (!$mobile->authorizeRequest($headers["Authorization"])) {

		header("HTTP/1.1 401 Unauthorized");
		die();

	}

	if (empty($_POST["user_id"]) || empty($_POST["topic_id"]) || empty($_POST["tag_id"])) {

		header("HTTP/1.1 400");
		die;

	}

	$jig = db_query("SELECT sis_id FROM user_enrollment WHERE user_id = ".db_sanitize($_POST["user_id"]).";");
	if (empty($jig)) {

		header("HTTP/1.1 400");
		die;

	}

	if (($res = $mobile->updateTopicProgress($jig[0]["sis_id"], $_POST["topic_id"], $_POST["tag_id"])) != "success") {

		db_exec("INSERT INTO system_log (source, data) VALUES ('mob.progress.update', ".db_sanitize($res).");");
		// header("HTTP/1.1 500");
		// die;

	}

?>