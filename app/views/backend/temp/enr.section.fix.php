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
		header('Location: ../index.php');
		die();
	}

	load_module("user_enrollment");

	/*$res_enr = db_query("SELECT enr.enr_id, enr.learn_mode, enr.section_id, enr.course_id, subs.start_date FROM user_enrollment AS enr INNER JOIN subs ON subs.subs_id = enr.subs_id;");
	foreach ($res_enr as $enr)
	{
		$res_section = db_query("SELECT * FROM course_section WHERE sis_id=".db_sanitize($enr["section_id"]));
		if (!isset($res_section[0]))
		{
			$learn_mode = ($enr["learn_mode"] == "sp" ? 2 : 1);
			$start_date = date_create_from_format("Y-m-d H:i:s", $enr["start_date"]);
			$start_date = date_create_from_format("Y-m-d H:i:s", $start_date->format("Y-m")."-01 00:00:00");
			db_exec("INSERT INTO course_section (course_id, start_date, learn_mode, sis_id) VALUES (".$enr["course_id"].",".db_sanitize($start_date->format("Y-m-d H:i:s")).",".$learn_mode.",".db_sanitize($enr["section_id"]).")");
			//$section_id = db_get_last_insert_id();
		}
		else
			$section_id = $res_section[0]["section_id"];
	}*/

	/*$res_enr = db_query("SELECT enr_id, section_id FROM user_enrollment");
	foreach ($res_enr as $enr) {

		$section = section_get_by_sis_id($enr["section_id"]);
		if ($section !== false) {
			db_exec("UPDATE user_enrollment SET section_id=".db_sanitize($section["id"])." WHERE enr_id=".$enr["enr_id"]);
		}

	}*/

?>