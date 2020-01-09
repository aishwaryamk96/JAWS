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

	if (!auth_api ("course.create"))
		die ("You do not have the required priviledges to use this feature.");

	load_module ("course");

	if (isset($_POST["courses"]))
	{
		unset($_POST["courses"]["type"]);
		$courses = $_POST["courses"];

		// Truncate the tables for initial migration
		db_exec ("TRUNCATE TABLE course");
		db_exec ("TRUNCATE TABLE course_meta");

		foreach ($courses as $course)
		{
			$course_jaws['name'] = $course['name'];
			$course_jaws['duration_length'] = $course['duration'];
			$course_jaws['duration_unit'] = $course["duration_unit"];
			$course_jaws['status'] = $course["status"];
			$course_jaws['sp_price_inr'] = $course['price'];
			$course_jaws['sp_price_usd'] = $course['price_usd'];
			$course_jaws['il_price_inr'] = $course['price_premium'];
			$course_jaws['il_price_usd'] = $course['price_premium_usd'];
			$course_jaws['description'] = $course['short_description'];
			$course_jaws['sis_id'] = "TBF";

			$img = array("url_web" => $course["url_web"], "branch" => $course["branch"], "img_main_big" => $course["img_main_big"], "img_main_small" => $course["img_main_small"]);
			$course_jaws["content"] = json_encode($img);

			$course_info = course_build($course_jaws);

			persist($_POST["persistence"]["courses"]["layer"], $_POST["persistence"]["courses"]["type"], $course_info['course_id'], $course["id"], false);
		}

		echo json_encode(true);
	}
	else
		echo json_encode(false);

?>