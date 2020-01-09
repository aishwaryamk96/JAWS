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

	// Check Auth
	if (!auth_api("user.subs.read")) {
		die("You do not have the required priviledges to use this feature.");
	}

	if (!isset($_POST["jigid"])) {
		die(json_encode(array("Items" => 0, "Count" => 0, "ScannedCount" => 0)));
	}

	// Init
	load_module("subs");

	$subs_all = subs_get_info_by_user_id($_POST["jigid"]);

	if (!$subs_all) {
		die(json_encode(array("Items" => 0, "Count" => 0, "ScannedCount" => 0)));
	}

	$subs_arr = array();
	foreach ($subs_all as $subs) {

		if (strcmp($subs["status"], "active") != 0) {
			continue;
		}

		$courses_str = $subs["combo"];
		if (strlen($subs["combo_free"]) > 0) {
			$courses_str .= ";".$subs["combo_free"];
		}
		$courses_str = str_replace(array(",1", ",2"), "", $courses_str);
		$courses_str = str_replace(";", ",", $courses_str);

		$course_sis_ids = db_query("SELECT sis_id FROM course WHERE course_id IN (".$courses_str.");");
		$course_codes = array();
		foreach ($course_sis_ids as $sis_id) {
			$course_codes[] = $sis_id["sis_id"];
		}

		// Get topics count for the course
		$data = array("course_codes" => $course_codes);
		$opts = [
			'http' => [
				'method'  => 'POST',
				'header'  => 'Content-type: application/x-www-form-urlencoded',
				'content' => http_build_query($data)
			]
		];

		$context  = stream_context_create($opts);
		$entities = json_decode(file_get_contents("https://jigsawacademy.net/app/getentitiescount.php", false, $context), true);

		$end_date;

		$res_access = db_query("SELECT end_date FROM access_duration WHERE subs_id = ".$subs["subs_id"]." ORDER BY id DESC LIMIT 1;");
		if (!empty($res_access)) {
			$end_date = $res_access[0]["end_date"];
		}
		else {
			$end_date = (empty($subs["end_date_ext"]) ? $subs["end_date"] : $subs["end_date_ext"]);
		}

		if (strlen($subs["combo_free"]) > 0) {
			$subs["combo"] .= ";".$subs["combo_free"];
		}
		$enrollments = explode(";", $subs["combo"]);
		//array_merge($enrollments, explode(";", $subs["combo_free"]));
		foreach ($enrollments as $enrollment) {

			$course_id = explode(",", $enrollment)[0];
			$course_info = db_query("SELECT name, sis_id FROM course WHERE course_id=".$course_id);
			$course_info = $course_info[0];
			$course_branch = db_query("SELECT content FROM course_meta WHERE course_id=".$course_id);
			$course_branch = json_decode($course_branch[0]["content"], true)["branch"];
			$res_enroll = db_query("SELECT enr_id, learn_mode, section_id FROM user_enrollment WHERE subs_id=".$subs["subs_id"]." AND course_id=".$course_id);
			if (!$res_enroll) {
				continue;
			}
			$res_enroll = $res_enroll[0];
			$days_left = date_diff(date_create(), date_create_from_format("Y-m-d H:i:s", $end_date));
			if (intval($days_left->format("%R%a")) < 0) {
				$days_left = 0;
			}
			else {
				$days_left = $days_left->format("%a");
			}

			$subs_arr[] = [
				"course_id" => $course_id,
				"course_code" => $course_info["sis_id"],
				"course_name" => $course_info["name"],
				"course_start" => $subs["start_date"],
				"course_end" => $end_date,
				"course_delivery" => ((strcmp($res_enroll["learn_mode"], "sp") == 0) ? "Self Paced" : ($res_enroll["learn_mode"] == "il" ? "Instructor Led" : "Catalyst")),
				"batch_number" => $res_enroll["section_id"],
				"bcgnd" => "",
				"topics_count" => $entities[$course_info["sis_id"]]["topic_count"],
				"videos_count" => $entities[$course_info["sis_id"]]["video_count"],
				"is_active" => (strcmp($subs["status"], "active") == 0),
				"days_left" => $days_left,
				"branch" => $course_branch
			];

		}

	}

	die(json_encode(array("Items" => (count($subs_arr) == 0 ? 0 : $subs_arr), "Count" => count($subs_arr), "ScannedCount" => count($subs_arr))));

?>