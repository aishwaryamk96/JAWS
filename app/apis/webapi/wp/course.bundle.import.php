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

  	register_shutdown_function(function() {
  		if (!empty($errors = error_get_last())) {
  			log_activity("course.bundle.import.error", $errors);
  		}
  	});

	if (!auth_api("course.import"))
		die ("You do not have the required priviledges to use this feature.");

	load_module ("course");
	load_library("persistence");

	/*
	 * A bundle can be of 3 types:
	 * 1. Specialization: These are pre-defined bundles of courses which do not have any date of expiry.
	 * 2. Offers: Offers have a date of expiry.
	 * 3. Combo: They do not seem to have any date of expiry.
	*/

	if (!isset($_POST["bundle"]))
	{
		echo json_encode(false);
		exit();
	}

	// Get the type of the bundle
	$type = $_POST["bundle"]["type"];
	$bundle = $_POST["bundle"];

	$bundle_jaws['name'] = $bundle['name'];
	$bundle_jaws['price_inr'] = $bundle['price'];
	$bundle_jaws['price_usd'] = $bundle['price_usd'];
	$bundle_jaws['bundle_type'] = $type;

	activity_debug_start();
	//activity_debug_log(json_encode($bundle));

	// If the bundle is a specialization:
	if (strcmp($type, "specialization") == 0)
	{
		$bundle_jaws['subs_duration_length'] = $bundle['access_duration'];

		// We get the duration unit too from the caller
		$bundle_jaws['subs_duration_unit'] = $bundle["access_duration_unit"];
		$bundle_jaws['desc'] = $bundle['short_description'];

		$bundle_jaws["is_bootcamp"] = intval($bundle["is_bootcamp"]);
		if ($bundle_jaws["is_bootcamp"]) {
			$bundle_jaws["batch_start_date"] = $bundle["batch_start_date"];
			$bundle_jaws["batch_end_date"] = $bundle["batch_end_date"];
		}
		else {
			$bundle_jaws["batch_start_date"] = "";
		}

	}

	$bundle_jaws["batches"] = [];
	if (!empty($bundle["bootcamp_batches"])) {
		$bundle_jaws["batches"] = $bundle["bootcamp_batches"];
	}
	else if ($type == "bootcamps") {
		$bundle["status"] = "draft";
	}

	// If the bundle is an offer:
	else if (strcmp ($type, "offer") == 0)
		$bundle_jaws["expire_date"] = $bundle["end_date"];

	// Get the list of courses in the bundle
	$courses = array();
	foreach ($bundle["courses"] as $course)
		$courses[] = strval($course).",2";

	if (isset($bundle["premium_courses"]))
	{
		if (is_array($bundle["premium_courses"]))
		{
			foreach ($bundle["premium_courses"] as $course)
				$courses[] = strval($course).",1";
		}
		else if (strlen($bundle["premium_courses"]) > 0)
			$courses[] = strval($bundle["premium_courses"]).",1";
	}

	// Get native course IDs for the WP course IDs
	$courses_native = array();
	foreach ($courses as $course)
	{
		$course_id = explode(",", $course);
		$native_id = get_native_id(array("layer" => $_POST["persistence"]["bundle"]["layer"], "type" => "course", "id" => $course_id[0]));
		$courses_native[] = $native_id["id"].",".$course_id[1];
	}
	$bundle_jaws['combo'] = implode(";", $courses_native);

	// activity_debug_log(json_encode($courses_native));

	$img = [
		"url_web" => $bundle["url_web"],
		"img_main_big" => $bundle["img_main_big"],
		"img_main_small" => $bundle["img_main_small"],
		"tools" => $bundle["tools"],
		"prerequisite" => $course["prerequisite"] ?? "",
		"hours_per_week" => $course["hours_per_week"] ?? "",
		"tag_line" => $course["tag_line"] ?? "",
		"long_description" => $course["long_description"] ?? ""
	];
	$bundle_jaws["content"] = json_encode($img);
	log_activity('course.bundle', $bundle_jaws["content"]);

	if (isset($bundle["status"]))
		$bundle_jaws["status"] = $bundle["status"];
	else
		$bundle_jaws["status"] = "enabled";

	if (isset($bundle["category"]))
		$bundle_jaws["category"] = $bundle["category"];

	// Check if the bundle has been persisted
	// If it has been, update it
	$bundle_id;
	if (is_persistent (array("layer" => $_POST["persistence"]["bundle"]["layer"], "type" => $_POST["persistence"]["bundle"]["type"], "id" => $bundle["id"]))) {

		$bundle_id = get_native_id(array("layer" => $_POST["persistence"]["bundle"]["layer"], "type" => $_POST["persistence"]["bundle"]["type"], "id" => $bundle["id"]))["id"];
		$bundle_jaws["bundle_id"] = $bundle_id;
		bundle_update($bundle_jaws);

	}
	// Or, create a new one
	else {

		$ret_bundle = bundle_create($bundle_jaws);
		if ($ret_bundle["bundle_id"] != 0) {

			persist($_POST["persistence"]["bundle"]["layer"], $_POST["persistence"]["bundle"]["type"], $ret_bundle["bundle_id"], $bundle["id"]);
			$bundle_id = $ret_bundle["bundle_id"];

		}

	}

	if (/*$type == "bootcamps" &&*/ !empty($bundle_jaws["batches"])) {
		bootcamp_add_batches($bundle_id, $bundle_jaws["batches"]);
	}

	echo json_encode($bundle_jaws);

	// echo json_encode(true);

?>
