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
	if (!auth_api("courses.get")) die("You do not have the required priviledges to use this feature.");

	// Load prerequisites
	load_module("course");
	load_library("mobapi.helper");

	header("Content-type: appliation/json");

	// If the courseid POST variable is set, fetch the course info
	if (isset($_POST["courseid"]))
	{
		echo json_encode(array("Items" => array(construct_mobile_app_course_array(course_get_info_by_id($_POST["courseid"]))), "Count" => 1, "ScannedCount" => 1));
		exit();
	}
	// If the branch POST variable is set, fetch courses belonging to this branch
	else if (isset($_POST["branch"]))
	{
		if (strcmp($_POST["branch"], "all") == 0)
			$courses = course_get_info_all(false, true);
		else
			$courses = course_get_info_by_branch($_POST["branch"], true);
	}
	// Get all courses
	else
		$courses = course_get_info_all(false, false);

	$courses_arr = array();
	// Construct the array according to the expectation on mobile app
	foreach ($courses as $course)
		$courses_arr[] = construct_mobile_app_course_array($course);

	echo json_encode(array("Items" => $courses_arr, "Count" => count($courses_arr), "ScannedCount" => count($courses_arr)));
	exit();

?>