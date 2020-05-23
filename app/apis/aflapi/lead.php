<?php

	// IMPORTANT!! THIS API IS THE ONE!! HAHAHAHA (EVIL LAUGHTER)
	// IF YOU ARE LOOKING FOR THE API THAT IS USED BY MANIPAL FOR LEADS PUSH, THAT WOULD BE jaws/app/apis/enxtapi/capture.php
	// THAT CAN ALSO BE MOVED HERE, IF ANYONE FEELS SO, PLEASE PROCEED, NO CAUTION REQUIRED AS SUCH

	// This API accepts leads from any random source

	if (empty($_POST)) {
		$_POST = json_decode(file_get_contents("php://input"), true);
	}

	$sanitized_post = db_sanitize(json_encode($_POST));
	db_exec("INSERT INTO system_log (source, data) VALUES ('lead.new', $sanitized_post);");

	if (empty($_POST["email"]) && empty($_POST["phone"])) {
		die(header("HTTP/1.1 400"));
	}

	$auth = [
		"Naukri" => "y5Vtzt8JnKOnvIoqrrNPo1j2Kt3GoiXqNJ6968d1"
	];

	$source = false;
	$headers = getallheaders();
	if (empty($headers["Authorization"])) {
		die(header("HTTP/1.1 401"));
	}
	else {

		$auth_header = str_replace("Bearer ", "", $headers["Authorization"]);
		foreach ($auth as $key => $value) {

			if ($auth_header == $value) {
				$source = $key;
			}

		}
		if (empty($key)) {
			die(header("HTTP/1.1 401"));
		}

	}

	$keys = [];
	foreach (["email", "name", "phone", "utm_source", "utm_campaign", "utm_medium", "ip", "url"] as $key) {

		$keys[$key] = "NULL";
		if (!empty($_POST[$key])) {

			$keys[$key] = db_sanitize($_POST[$key]);
			unset($_POST[$key]);

		}
		elseif ($key == "url") {
			$keys[$key] = db_sanitize("");
		}

	}

	extract($keys);

	$meta = ["source" => $source];
	foreach ($_POST as $key => $value) {
		$meta[$key] = $value;
	}

	$meta = db_sanitize(json_encode($meta));

	//JA-127 START
	$status = 0;
	if(empty($name) && empty($email) && empty($phone))
	    $status = 6;
	    db_exec("INSERT INTO user_leads_basic (name, email, phone, utm_source, utm_campaign, utm_medium, ip, ad_url, create_date, capture_trigger, capture_type, meta, status) VALUES ($name, $email, $phone, $utm_source, $utm_campaign, $utm_medium, $ip, $url, CURRENT_TIMESTAMP, 'formsubmit', 'url', $meta,$status);");
    //JA-127 END

	header("HTTP/1.1 201");
	die(json_encode(["status" => "success"]));

?>