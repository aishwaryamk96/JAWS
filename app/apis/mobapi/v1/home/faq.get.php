<?php

	load_plugin("mobile_app");
	load_module("user");
	load_library("misc");

	$mobile = new MobileApp;
	$GLOBALS["mobileObject"] = $mobile;

	$headers = getallheaders();
	if (!$mobile->authorizeRequest($headers["Authorization"])) {

		header("HTTP/1.1 401 Unauthorized");
		die();

	}

	header("Content-type: application-json");

	// $key = "mobile.faq";

	$user = user_get_by_id($_POST["user_id"]);

	$key = "s";
	$res_subs = db_query("SELECT * FROM subs WHERE (status='active' OR status='pending') AND user_id=".$user["user_id"].";");
	if (empty($res_subs)) {
		$key = "n";
	}

	$faqs = [];

	$faqs_arr = json_decode(content_get("mobile.faq.new"), true);
	if ($faqs_arr !== false) {

		foreach ($faqs_arr as $faq) {

			if (!isset($faq["target"])) {
				$faqs[] = $faq;
			}
			else {

				$targets = explode(",", $faq["target"]);
				if (in_array($key, $targets)) {

					unset($faq["target"]);
					$faqs[] = $faq;

				}

			}

		}

	}

	die(json_encode($faqs));

?>