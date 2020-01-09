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

	if (!auth_api("course.create")) {
		die ("You do not have the required priviledges to use this feature.");
	}

	// This API accepts an array of bundles and processes them one by one.
	$bundles_arr = $_POST["bundles_arr"];

	load_module ("course");

	function create_bundle($bundle, $type) {

		$bundle_jaws['name'] = $bundle['name'];
		$bundle_jaws['price_inr'] = $bundle['price'];
		$bundle_jaws['price_usd'] = $bundle['price_usd'];
		$bundle_jaws['bundle_type'] = $type;

		// If the bundle is a specialization:
		if (strcmp($type, "specialization") == 0) {

			$bundle_jaws['subs_duration_length'] = $bundle['duration'];
			// We get the duration unit too from the caller
			$bundle_jaws['subs_duration_unit'] = $bundle["duration_unit"];
			$bundle_jaws['desc'] = $bundle['short_description'];

			$bundle_jaws["is_bootcamp"] = intval($bundle["is_bootcamp"]);
			if ($bundle_jaws["is_bootcamp"]) {
				$bundle_jaws["batch_start_date"] = $bundle["batch_start_date"];
			}
			else {
				$bundle_jaws["batch_start_date"] = "";
			}

		}

		// If the bundle is an offer:
		else if (strcmp ($type, "offer") == 0) {
			$bundle_jaws["expire_date"] = $bundle["end_date"];
		}

		// Get the list of courses in the bundle
		$courses = array();
		foreach ($bundle["courses"] as $course) {
			$courses[] = $course.",2";
		}

		if (isset($bundle["premium_courses"]) && is_array($bundle["premium_courses"])) {

			foreach ($bundle["premium_courses"] as $course) {
				$courses[] = $course.",1";
			}

		}
		else if (isset($bundle["courses_premium"]) && is_array($bundle["courses_premium"])) {

			foreach ($bundle["courses_premium"] as $course) {
				$courses[] = $course.",1";
			}

		}

		// Get native course IDs for the WP course IDs
		$courses_native = array();
		foreach ($courses as $course) {

			$course_id = explode(",", $course);
			$courses_native[] = get_native_course_id($course_id[0], $_POST["persistence"]["bundles"]["layer"])["id"].",".$course_id[1];

		}
		$bundle_jaws['combo'] = implode(";", $courses_native);

		$img = array("url_web" => $bundle["url_web"], "img_main_big" => $bundle["img_main_big"], "img_main_small" => $bundle["img_main_small"]);
		$bundle_jaws["content"] = json_encode($img);

		// Create the bundle and persist it
		$ret_bundle = bundle_create($bundle_jaws, true);
		persist($_POST["persistence"]["bundles"]["layer"], $_POST["persistence"]["bundles"]["type"], $ret_bundle["bundle_id"], $bundle["id"], false);

	}

	if (isset($_POST["bundles_arr"])) {

		// Truncate the tables for initial migration
		db_exec("TRUNCATE TABLE course_bundle");
		db_exec("TRUNCATE TABLE course_bundle_meta");

		//var_dump($_POST["bundles_arr"]);
		foreach ($_POST["bundles_arr"] as $bundles) {

			$type = $bundles["type"];
			unset($bundles["type"]);

			foreach ($bundles as $bundle) {
				create_bundle($bundle, $type);
			}

		}

		echo json_encode(true);

	}
	else {
		echo json_encode(false);
	}

?>