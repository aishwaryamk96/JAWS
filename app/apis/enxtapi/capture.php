<?php

	if (empty($_POST)) {
		$_POST = json_decode(file_get_contents("php://input"), true);
	}

	$sanitized_post = db_sanitize(json_encode($_POST));
	db_exec("INSERT INTO system_log (source, data) VALUES ('enxtapi', $sanitized_post);");

	if (empty($_POST["email"]) && empty($_POST["phone"])) {
		die(header("HTTP/1.1 400"));
	}

	$headers = getallheaders();
	if (empty($headers["Authorization"]) || $headers["Authorization"] != "Bearer FMfcgxwDqxRlPWdbpBWtKzhvDsLlSqjr") {
		die(header("HTTP/1.1 401"));
	}

	$keys = [];
	foreach (["email", "name", "phone", "utm_source", "utm_campaign", "utm_medium", "ip", "url"] as $key) {

		$keys[$key] = "NULL";
		if (!empty($_POST[$key])) {

			$keys[$key] = db_sanitize($_POST[$key]);
			unset($_POST[$key]);

		}

	}

	extract($keys);

	$meta = [];
	// foreach (["city", "state", "form"] as $key) {

	// 	if (!empty($_POST[$key])) {
	// 		$meta[$key] = $_POST[$key];
	// 	}

	// }
	foreach ($_POST as $key => $value) {
		$meta[$key] = $value;
	}

	$meta = db_sanitize(json_encode($meta));

	$referer = db_sanitize("https://www.manipalprolearn.com");

	//JA-127 START
	$status = 0;
	if(empty($name) && empty($email)&& empty($phone))
	    $status = 6;
	    db_exec("INSERT INTO user_leads_basic (name, email, phone, utm_source, utm_campaign, utm_medium, ip, referer, ad_url, create_date, capture_trigger, capture_type, meta, status) VALUES ($name, $email, $phone, $utm_source, $utm_campaign, $utm_medium, $ip, $referer, $url, CURRENT_TIMESTAMP, 'formsubmit', 'url', $meta, $status);");
	    //JA -127 END

	die(json_encode(["status" => "Yay!"]));

?>