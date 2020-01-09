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
    	header('Location: https://www.jigsawacademy.com');
    	die();
  	}

	if (!auth_api ("course.import")) {
		die ("You do not have the required priviledges to use this feature.");
	}

	load_module ("course");

	if (isset($_POST["course"])) {

		$course = $_POST["course"];
		$course_jaws['name'] = $course['name'];
		$course_jaws['status'] = $course["status"];
		$course_jaws['sp_price_inr'] = str_replace(",", "", $course['price']);
		$course_jaws['sp_price_usd'] = str_replace(",", "", $course['price_usd']);
		$course_jaws['il_price_inr'] = str_replace(",", "", $course['price_premium']);
		$course_jaws['il_price_usd'] = str_replace(",", "", $course['price_premium_usd']);
		$course_jaws['description'] = $course['short_description'];
		$course_jaws['duration_length'] = $course['access_duration'];
		$course_jaws['duration_unit'] = $course["access_duration_unit"];

		if (isset($course["shortcode"]) && strlen($course["shortcode"]) > 0) {

			$course_jaws["il_code"] = "I".$course["shortcode"];
			$course_jaws["sp_code"] = "V".$course["shortcode"];

		}

		if (isset($course["status"])) {
			$course_jaws["status"] = $course["status"];
		}

		// Toggle comment this to protect overwriting the course SIS ID in an irresponsible manner
		// $course_jaws["sis_id"] = $course["shortcode"];

		$course_jaws["category"] = $course["category"];

		$ws_duration = $course['duration']." ".$course["duration_unit"];

		$content = array("url_web" => $course["url_web"], "branch" => $course["branch"], "img_main_big" => $course["img_main_big"], "img_main_small" => $course["img_main_small"], "tools" => $course["tools"], "prerequisite" => $course["prerequisite"], "hours_per_week" => $course["hours_per_week"], "tag_line" => $course["tag_line"], "long_description" => $course["long_description"], "ws_duration" => $ws_duration, "rating" => $course["rating"], "img_rating" => $course["img_rating"], "slug" => $course["slug"]);
		$course_jaws["content"] = json_encode($content);

		$course_id = false;

		// Check if the course has been persisted
		// If it has been, update it
		if (is_persistent (array("layer" => $_POST["persistence"]["course"]["layer"], "type" => $_POST["persistence"]["course"]["type"], "id" => $course["id"]))) {

			$native_id = get_native_course_id($course["id"], $_POST["persistence"]["course"]["layer"])["id"];
			$course_jaws["course_id"] = $course_id = $native_id;
			course_update($course_jaws);

		}
		// Or, create a new one
		else {

			$course_jaws["sis_id"] = $course["shortcode"];

			$course_info = course_build($course_jaws);
			if ($course_info["course_id"] != 0) {

				$course_id = $course_info["course_id"];
				persist($_POST["persistence"]["course"]["layer"], $_POST["persistence"]["course"]["type"], $course_info["course_id"], $course['id'], false);

			}

		}

		if ($course_id) {

			load_plugin("nucleus");

			$nucleus = Nucleus::createContext();
			$res = $nucleus->exportCourse($course_id);

			db_exec("INSERT INTO system_log (source, data) VALUES ('course.import', ".db_sanitize(var_export($res, true)).");");

		}

		echo json_encode(true);

	}
	else {
		echo json_encode(false);
	}

?>