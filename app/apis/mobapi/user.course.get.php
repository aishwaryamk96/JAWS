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
	if (!auth_api("user.subs.read")) die("You do not have the required priviledges to use this feature.");

	if (!isset($_POST["jigid"]) || !isset($_POST["courseid"]))
	{
		echo json_encode(array("Items" => 0, "Count" => 0, "ScannedCount" => 0));
		exit();
	}

	// Init
	load_module("user_enrollment");
	load_module("subs");

	$course = db_query("SELECT course_id, name, sis_id FROM course WHERE course_id=".$_POST["courseid"]);
	$course = $course[0];
	$enr = user_enrollment_get_by_user_course($_POST["jigid"], $course["course_id"]);

	if (!$enr)
	{
		echo json_encode(array("Items" => 0, "Count" => 0, "ScannedCount" => 0));
		exit();
	}

	$subs = subs_get_info($enr["subs_id"]);

	// Get topics count for the course
	$data = array("course_code" => $course["sis_id"]);
	$opts = array('http' => array(
          'method'  => 'POST',
          'header'  => 'Content-type: application/x-www-form-urlencoded',
          'content' => http_build_query($data)
          )
        );
	$context  = stream_context_create($opts);
	$entities = json_decode(file_get_contents("https://jigsawacademy.net/app/getentitiescount.php", false, $context), true);

	$data = array("section_id" => $enr["section_id"]);
			$opts = array('http' => array(
                  'method'  => 'POST',
                  'header'  => 'Content-type: application/x-www-form-urlencoded',
                  'content' => http_build_query($data)
                  )
                );
    		$context  = stream_context_create($opts);
    		$section_name = file_get_contents("https://jigsawacademy.net/app/get_section_name.php", false, $context);

    $end_date;
	$res_access = db_query("SELECT end_date FROM access_duration WHERE subs_id = ".$subs["subs_id"]." ORDER BY id DESC LIMIT 1;");
	if (!empty($res_access)) {
		$end_date = $res_access[0]["end_date"];
	}
	else {
		$end_date = (empty($subs["end_date_ext"]) ? $subs["end_date"] : $subs["end_date_ext"]);
	}
	$days_left = date_diff(date_create_from_format("Y-m-d H:i:s", $subs["start_date"]), date_create_from_format("Y-m-d H:i:s", $end_date));
	if ($days_left->format("%R%a") < 0) $days_left = 0;
	else $days_left = $days_left->format("%a");
	$enr = array("course_id" => $course["course_id"], "course_code" => $course["sis_id"], "course_name" => $course["name"], "course_start" => $subs["start_date"], "course_end" => $end_date, "course_delivery" => ((strcmp($enr["learn_mode"], "sp") == 0) ? "Self Paced" : "Instructor Led"), "batch_number" => $section_name, "bcgnd" => "", "topics_count" => $entities["topic_count"], "videos_count" => $entities["video_count"], "is_active" => ((strcmp($subs["status"], "active") == 0) ? true : false), "days_left" => $days_left);

	echo json_encode(array("Items" => array($enr), "Count" => 1, "ScannedCount" => 1));
	exit();

?>