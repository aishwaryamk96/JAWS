<?php

	if (empty($_POST)) {
		$_POST = json_decode(file_get_contents("php://input"), true);
	}

	if (empty($_POST["email"])) {
		die(header("HTTP/1.1 400"));
	}

	$email = db_sanitize($_POST["email"]);

	$keys = [];
	foreach (["name", "phone", "utm_source", "utm_campaign", "utm_medium", "ip", "referer", "url"] as $key) {

		$keys[$key] = "NULL";
		if (!empty($_POST[$key])) {
			$key = db_sanitize($_POST[$key]);
		}

	}

	extract($keys);

	$meta = [];
	foreach (["city", "state", "form"] as $key) {

		if (!empty($_POST[$key])) {
			$meta[$key] = $_POST[$key];
		}

	}

	$meta = db_sanitize(json_encode($meta));
	
	//JA-127 START
	$status = 0;
	if(empty($name) && empty($email) && empty($phone))
	    $status = 6;
	    db_exec("INSERT INTO user_leads_basic (name, email, phone, utm_source, utm_campaign, utm_medium, ip, referer, ad_url, create_date, capture_trigger, capture_type, meta, status) VALUES ($name, $email, $phone, $utm_source, $utm_campaign, $utm_medium, $ip, $referrer, $url, CURRENT_TIMESTAMP, 'formsubmit', 'url', $meta,$status);");
	//JA-127 END

?>