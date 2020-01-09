<?php

	load_plugin("mobile_app");

	$mobile = new MobileApp;

	$headers = getallheaders();
	if (!$mobile->authorizeRequest($headers["Authorization"])) {

		header("HTTP/1.1 401 Unauthorized");
		die();

	}

	$res_user = db_query("SELECT * FROM user WHERE user_id = ".$_POST["user_id"].";");
	if ($res_user === false) {

		header("HTTP/1.1 401 Unauthorized");
		die();

	}
	$res_user = $res_user[0];

	if (!$mobile->authorizeDeviceID($res_user["user_id"], $_POST["dev_id"])) {

		header("HTTP/1.1 401 Unauthorized");
		die();

	}

	db_exec("UPDATE user SET phone=".db_sanitize($_POST["cc"].$_POST["phone"])." WHERE user_id=".$res_user["user_id"]);

	die(json_encode(["success" => true]));

?>