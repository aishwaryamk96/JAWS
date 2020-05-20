<?php 

//New Error Logging library
load_library("errormanager");
 logErrors(DEBUG_LOG, "itpapi", "DATA:",$_POST);

	// db_exec("INSERT INTO system_log (source, data) VALUES ('instapage.ping', CURRENT_TIMESTAMP);");

	db_exec("INSERT INTO system_log (source, data) VALUES ('instapage.capture', ".db_sanitize(json_encode($_POST)).");");

	if (empty($_POST["Name"])) {
		die(false);
	}
	$name = db_sanitize($_POST["Name"]);

	if (empty($_POST["Email"])) {
		die(false);
	}
	$email = db_sanitize($_POST["Email"]);

	$phone = (!empty($_POST["Phone_"]) ? db_sanitize($_POST["Phone_"]) : (!empty($_POST["Phone"]) ? db_sanitize($_POST["Phone"]) : "NULL"));

	$ad_url = (!empty($_POST["page_url"])) ? db_sanitize($_POST["page_url"]) : db_sanitize("NA");

	$ip = (!empty($_POST["ip"])) ? db_sanitize($_POST["ip"]) : (!empty($_POST["ipaddress"]) ? db_sanitize($_POST["ipaddress"]) : "NULL");

	$utm_source = "NULL";
	$utm_campaign = "NULL";
	$utm_medium = "NULL";
	if (!empty($_POST["referralsource"])) {

		$url_components = parse_url($_POST["referralsource"]);
		$get = $url_components["query"];
		$_get = explode("&", $get);
		foreach ($_get as $get) {

			$components = explode("=", $get);
			if ($components[0] == "utm_source") {
				$utm_source = db_sanitize($components[1]);
			}
			else if ($components[0] == "utm_campaign") {
				$utm_campaign = db_sanitize($components[1]);
			}
			else if ($components[0] == "utm_medium") {
				$utm_medium = db_sanitize($components[1]);
			}

		}

	}

	$referrer = (!empty($_POST["referralsource"]) ? db_sanitize($_POST["referralsource"]) : "NULL");

	$meta = [];
	if (!empty($_POST["City"])) {
		$meta = ["city" => $_POST["City"]];
	}
	if (!empty($_POST["time_to_call"])) {
		$meta["time_to_call"] = $_POST["time_to_call"];
	}
        //hotfix changes - Apr 22,2020 LiveCall issue
        if (!empty($_POST["Qualification"])) {
		$meta["Qualification"] = $_POST["Qualification"];
	}
        //
	//hotfix changes - May 19,2020 added more fields in Instapage forms
        if (!empty($_POST["Experience"])) {
		$meta["Experience"] = $_POST["Experience"];
	}
	    if (!empty($_POST["Course"])) {
		$meta["course"] = $_POST["Course"];
	}
    //
	$meta = db_sanitize(json_encode($meta));

	db_exec("INSERT INTO user_leads_basic (name, email, phone, utm_source, utm_campaign, utm_medium, ip, referer, ad_url, create_date, capture_trigger, capture_type, meta) VALUES ($name, $email, $phone, $utm_source, $utm_campaign, $utm_medium, $ip, $referrer, $ad_url, CURRENT_TIMESTAMP, 'formsubmit', 'url', $meta);");

	die(true);
?>