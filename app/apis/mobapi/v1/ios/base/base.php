<?php

/* 
           8 8888       .8. `8.`888b                 ,8' d888888o.   
           8 8888      .888. `8.`888b               ,8'.`8888:' `88. 
           8 8888     :88888. `8.`888b             ,8' 8.`8888.   Y8 
           8 8888    . `88888. `8.`888b     .b    ,8'  `8.`8888.     
           8 8888   .8. `88888. `8.`888b    88b  ,8'    `8.`8888.    
           8 8888  .8`8. `88888. `8.`888b .`888b,8'      `8.`8888.   
88.        8 8888 .8' `8. `88888. `8.`888b8.`8888'        `8.`8888.  
`88.       8 888'.8'   `8. `88888. `8.`888`8.`88'     8b   `8.`8888. 
  `88o.    8 88'.888888888. `88888. `8.`8' `8,`'      `8b.  ;8.`8888 
    `Y888888 ' .8'       `8. `88888. `8.`   `8'        `Y8888P ,88P' 

    JIGSAW ACADEMY WORKFLOW SYSTEM v1					
    ---------------------------------
*/

	// Prevent exclusive access
	if (!defined("JAWS")) {
		header('Location: http://www.jigsawacademy.com');
		die();
	}

	define (HOSTNAME, substr(JAWS_PATH_WEB, 0, strpos(JAWS_PATH_WEB, "jaws")));

	if (!auth_api("iOS.app"))
		error_throw();

	$GLOBALS["location"] = "inr";
	if (isset($_POST["location"]) && strcmp($_POST["location"], "india") != 0)
		$GLOBALS["location"] = "usd";

	function authorize_call($authorizations) {

		if (!is_array($authorizations)) {
			if (!auth_api($authorizations))
				error_throw();
		}
		else {
			foreach ($authorizations as $authorization) {
				if (!auth_api($authorization))
					error_throw();
			}
		}

	}

	function error_throw() {

		$status = 0;
		$message = "You do not required priviledges to use this feature";

		if (func_num_args() > 0) {

			$args = func_get_args();
			$status = $args[0];
			$message = $args[1];

		}

		die(json_encode(["status" => $status, "message" => $message]));

	}

	function courses_array_prepare() {

		load_module("course");

		$location = $GLOBALS["location"];

		$return["bu"] = HOSTNAME;

		$courses = course_get_info_all();
		foreach ($courses as $course) {
			
			$content = json_decode($course["meta"]["content"], true);
			$category = $course["meta"]["category"];
			if (strlen($category) == 0) {
				$category = "analytics";
			}

			$return[$category]["all"][$course["course_id"]] = [
				"n" => $course["name"],
				"sp" => $course["sp_price_".$location],
				"ip" => $course["il_price_".$location],
				"u" => url_strip_hostname($content["url_web"]),
				"i" => url_strip_hostname($content["img_main_small"]),
				"t" => $content["tools"],
				"p" => $content["prerequisite"],
				"tl" => $content["tag_line"],
				"d" => $course["meta"]["desc"],
				"ld" => $content["long_desc"],
				"h" => $content["hours_per_week"],
				"d" => $content["ws_duration"],
				"r" => $content["rating"],
				"ri" => url_strip_hostname($content["img_rating"])
			];

			if (strlen($content["branch"]) > 0)
				$return[$category][$content["branch"]][] = $course["course_id"];

		}

		return $return;

	}

	function url_strip_hostname($url) {
		return substr($url, strlen(HOSTNAME));
	}

?>