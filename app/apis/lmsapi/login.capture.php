<?php

	// Prevent exclusive access
	if (!defined("JAWS")) {
		header('Location: https://www.jigsawacademy.com');
		die();
	}

	if (!auth_api("subs.get")) {

		header("HTTP/1.1 401");
		die;

	}

	db_exec("INSERT INTO system_log (source, data) VALUES ('login.capture', ".db_sanitize(json_encode($_POST)).");");

	if (empty($_POST["email"]) || empty($_POST["ip"]) || empty($_POST["login_at"])) {

		header("HTTP/1.1 400");
		die(json_encode(["status" => false, "msg" => "Some parameters missing"]));

	}

	load_library("setting");
	load_module("user");

	$user = user_get_by_email($_POST["email"]);

	if (($capture_level = setting_get("jlc.login.capture.level", "bootcamp")) == "bootcamp") {

		if (!empty(db_query("SELECT bundle.* FROM course_bundle AS bundle INNER JOIN subs_meta AS meta ON meta.bundle_id = bundle.bundle_id INNER JOIN subs ON subs.subs_id = meta.subs_id WHERE bundle.batch_end_date > CURRENT_DATE AND subs.status = 'active' AND subs.user_id = ".$user["user_id"]))) {
			log_login($user, $capture_level);
		}

	}
	else {
		log_login($user, $capture_level);
	}

	die(json_encode(["status" => true, "msg" => "Dhanyawaad!"]));

	function log_login($user, $capture_level) {
		db_exec("INSERT INTO user_jlc_login (user_id, ip, login_at, capture_level) VALUES (".$user["user_id"].",".db_sanitize($_POST["ip"]).",".db_sanitize($_POST["login_at"]).",".db_sanitize($capture_level).");");
	}

?>