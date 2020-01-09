<?php

if (!empty($_POST)) {

    $host = "jigsawacademy.com";
    $path = "/";

	$__se;
	if (!isset($_COOKIE["__se"])) {

		$__se = hash("sha256", time()."".rand());
		setcookie("__se", $__se, 0, $path, $host, true);

	}
	else {
		$__se = $_COOKIE["__se"];
	}

	$__tr;
	if (!isset($_COOKIE["__tr"])) {

		$__tr = hash("sha256", $__se);
		setcookie("__tr", $__tr, time()+60*60*24*30, $path, $host, true);

	}
	else {
		$__tr = $_COOKIE["__tr"];
	}

	if (empty(($uuid = db_query("SELECT * FROM lead_uuids WHERE __tr = ".db_sanitize($__tr).";")))) {

		db_exec("INSERT INTO lead_uuids (__tr, screen_width, screen_height) VALUES (".db_sanitize($__tr).",".db_sanitize($_POST["swd"]).",".db_sanitize($_POST["sht"]).");");
		$uuid = db_get_last_insert_id();

	}
	else {
		$uuid = $uuid[0]["id"];
	}

	$ip = get_ip();
	if (empty(($session = db_query("SELECT * FROM lead_sessions WHERE __se = ".db_sanitize($__se).";")))) {

		db_exec("INSERT INTO lead_sessions (lead_uuid_id, __se, pathname, width, height, user_agent, ip, cookies) VALUES (".$uuid.",".db_sanitize($__se).",".db_sanitize($_POST["p"]).",".db_sanitize($_POST["wd"]).",".db_sanitize($_POST["ht"]).",".db_sanitize($_SERVER["HTTP_USER_AGENT"]).",".db_sanitize($ip).",".db_sanitize(json_encode($_COOKIE)).");");
		$session = db_get_last_insert_id();

	}
	else {

		if (empty($session[0]["cookies"])) {
			db_exec("UPDATE lead_sessions SET cookies = ".db_sanitize(json_encode($_COOKIE))." WHERE id = ".$session[0]["id"]);
		}

		$session = $session[0]["id"];

	}

	$pi = "";
	if (!empty($_POST["pi"])) {
		$pi = db_sanitize($_POST["pi"]);
	}

	$tc = [];
	if (!empty($_POST["tc"])) {
		$tc = explode(" ", $_POST["tc"]);
	}

	db_exec("INSERT INTO lead_activities (lead_uuid_id, lead_session_id, url, pathname, activity, x, y, page_y0, page_y1, client_x, client_y, tag, tag_id, tag_class, tag_meta, ".(!empty($pi) ? "parent_id, " : "")."ip) VALUES (".$uuid.",".$session.",".db_sanitize($_POST["u"]).",".db_sanitize($_POST["p"]).",".db_sanitize($_POST["e"]).",".db_sanitize($_POST["x"]).",".db_sanitize($_POST["y"]).",".db_sanitize($_POST["y0"]).",".db_sanitize($_POST["y1"]).",".db_sanitize($_POST["cx"]).",".db_sanitize($_POST["cy"]).",".db_sanitize($_POST["tt"]).",".db_sanitize($_POST["ti"]).",".db_sanitize(json_encode($tc)).",".db_sanitize($_POST["tm"]).",".(!empty($pi) ? $pi.", " : "").db_sanitize($ip).");");

	exit;

}

function get_ip() {

	$ip = "";

	$headers = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
	foreach ($headers as $header) {

		if (!empty($_SERVER[$header]) && filter_var($_SERVER[$header], FILTER_VALIDATE_IP) !== false) {
			$ip = $_SERVER[$header];
		}

	}

	return $ip;

}

?>