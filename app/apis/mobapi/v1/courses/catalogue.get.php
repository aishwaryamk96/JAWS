<?php

load_plugin("mobile_app");

define (HOSTNAME, substr(JAWS_PATH_WEB, 0, strpos(JAWS_PATH_WEB, "jaws")));

if (empty($GLOBALS["mobileObject"])) {

	load_module("user");

	$mobile = new MobileApp;
	$GLOBALS["mobileObject"] = $mobile;

	$GLOBALS["location"] = $GLOBALS["mobileObject"]->getRequestLocation($_SERVER["REMOTE_ADDR"])["country_code"];

	$headers = getallheaders();
	if (!$mobile->authorizeRequest($headers["Authorization"])) {

		header("HTTP/1.1 401 Unauthorized");
		die();

	}

	header("Content-type: application/json");

	$user = user_get_by_id($_POST["user_id"]);

	$GLOBALS["cl"] = [];
	$courses = course_catalogue_get();
	$courses["bu"] = HOSTNAME;
	$courses["cl"] = $GLOBALS["cl"];

	// die(json_encode(["bu" => HOSTNAME, "cl" => $GLOBALS["cl"], "c" => $courses/*, "e" => executives_get(), "s" => specializations_get()*/]));

	die(json_encode($courses));

}

function executives_get() {
	// To be implemented
}

function specializations_get() {
	// To be implemented
}

function course_catalogue_get() {

	load_module("course");

	if ($GLOBALS["location"] == "IN") {
		$currency = "inr";
	}
	else {
		$currency = "usd";
	}

	$sis_ids = [];
	$res_course_sis_ids = db_query("SELECT sis_id FROM course WHERE status='enabled' ;");
	foreach ($res_course_sis_ids as $course_sis_id) {
		$sis_ids[] = $course_sis_id["sis_id"];
	}
	$course_topics = $GLOBALS["mobileObject"]->getCoursesTopics($sis_ids);

	$courses = course_get_info_all(false, true);
	foreach ($courses as $course) {

		if (strtoupper($course["sis_id"]) == "SKIPTHISCOURSE" || $course["sellable"] == 0) {
			continue;
		}

		$content = json_decode($course["meta"]["content"], true);
		$category = $course["meta"]["category"];
		if (strlen($category) == 0) {
			$category = "analytics";
		}
		else {

			$category = explode(";", $category);
			if (in_array("full-stack", $category)) {
				continue;
			}
			if (in_array("others", $category)) {
				continue;
			}
			if (in_array("iot", $category)) {
				$category = "iot";
			}

		}

		if (empty($content["branch"])) {
			$content["branch"] = "all";
		}

		if (!empty($content["branch"]) && !in_array($content["branch"], $GLOBALS["cl"])) {
			$GLOBALS["cl"][] = $content["branch"];
		}

		$return[$category]["all"][$course["course_id"]] = [
			"n" => $course["name"],
			"sp" => $course["sp_price_".$currency],
			"ip" => $course["il_price_".$currency],
			"u" => url_strip_hostname($content["url_web"]),
			"i" => url_strip_hostname($content["img_main_small"]),
			"ts" => $content["tools"],
			"p" => $content["prerequisite"],
			"tl" => $content["tag_line"],
			"d" => $course["meta"]["desc"],
			"ld" => $content["long_description"],
			"h" => $content["hours_per_week"],
			"d" => $content["ws_duration"],
			"r" => $content["rating"],
			"ri" => url_strip_hostname($content["img_rating"]),
			"t" => $course_topics["t"][$course["sis_id"]] ?? new StdClass
		];

		if (strlen($content["branch"]) > 0 && $content["branch"] != "all") {
			$return[$category][$content["branch"]][] = $course["course_id"];
		}

	}

	return $return;

}

function url_strip_hostname($url) {
	return substr($url, strlen(HOSTNAME));
}

?>