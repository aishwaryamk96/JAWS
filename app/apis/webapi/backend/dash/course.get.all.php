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

    JIGSAW ACADEMY WORKFLOW SYSTEM v2
    ---------------------------------
*/

	// Prevent exclusive access
	if (!defined("JAWS")) {
		header("HTTP/1.1 401 Unauthorized");
		die();
	}

	// Init Session
	auth_session_init();

	// Auth Check - Expecting Session Only !
	if ((!auth_session_is_logged()) || (!auth_session_is_allowed("dash"))) {
		header("HTTP/1.1 401 Unauthorized");
		die();
	}

	// Prep
	load_module('course');

	// Output
	//echo json_encode(course_get_info_all(isset($_REQUEST['all'])));
	// Needed to show upcoming as well.
	// So copied the get_all function here.

	$res_courses = db_query("SELECT * FROM course WHERE ".($all ? "1" : "sellable = true")." AND b.seller IN (0, 1) ORDER BY position DESC, course_id ASC;");
	$courses = array();
	foreach ($res_courses as $course) {

			$course_meta = db_query("SELECT * FROM course_meta WHERE course_id=".$course["course_id"]);
			$course_meta = $course_meta[0];
			// if (!empty($course_meta["category"])) {

			// 	$category = $course_meta["category"];
			// 	if ($category == "others") {
			// 		continue;
			// 	}
			// 	// $category = explode(";", $category);
			// 	// if (in_array("full-stack", $category)) {
			// 	// 	continue;
			// 	// }

			// }
			$course["no_show"] = $course["no_show"] == "1";

			$course["meta"] = $course_meta;
			$courses[] = $course;

	}
	echo json_encode($courses);

	// Done
	exit();

?>
