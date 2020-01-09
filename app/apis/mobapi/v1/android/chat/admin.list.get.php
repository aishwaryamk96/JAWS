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

	if (!auth_api("mobapp.chat"))
		die ("You do not have required priviledges to use this feature.");

	load_module("user");

	$res_sections = db_query("SELECT admin FROM course_section;");

	$admin_arr = array();
	foreach ($res_sections as $section)
	{
		$admins = explode(";", $section["admin"]);
		foreach ($admins as $admin)
		{
			if (strlen($admin) == 0 || in_array($admin, $admin_arr))
				continue;
			$admin_arr[] = $admin;
		}
	}

	$response = array();
	foreach ($admin_arr as $admin)
	{
		$user = user_get_by_id($admin);
		$response[] = array("contact_id" => $admin, "contact_name" => $user["name"], "contact_pic" => $user["photo_url"]);
	}

	die(json_encode(["Items" => $response, "Count" => count($response), "ScannedCount" => count($response)]));

?>
