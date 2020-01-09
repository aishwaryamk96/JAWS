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
		header('Location: https://www.jigsawacademy.com');
		die();
	}

	if (!auth_api("subs.get"))
		die("You do not have required priviledges to use this feature");

	if (!isset($_POST["email"]))
		die("You do not have required priviledges to use this feature");

	load_module("user");
	load_module("subs");
	load_module("course");

	$user = user_get_by_email($_POST["email"]);
	if (!$user)
		die("You do not have required priviledges to use this feature");

	$subs_arr = db_query("SELECT subs.*, subs_meta.bundle_id FROM subs LEFT JOIN subs_meta ON subs.subs_id=subs_meta.subs_id WHERE subs.status='active' AND subs.user_id=".$user["user_id"]);
	$ret = array();
	$i = 0;
	foreach ($subs_arr as $subs)
	{
		$ret[$i]["bundle"] = "";
		if (strlen($subs["bundle_id"]) > 0)
		{
			$res_bundle = db_query("SELECT name FROM course_bundle WHERE bundle_type='specialization' AND bundle_id=".$subs["bundle_id"]);
			if (isset($res_bundle[0]))
				$ret[$i]["bundle"] = $res_bundle[0]["name"];
		}

		$courses = $subs["combo"].(strlen($subs["combo_free"]) > 0 ? ";".$subs["combo_free"] : "");
		$courses = explode(";", $courses);
		$course_arr = array();
		foreach ($courses as $course)
		{
			$course_info = course_get_info_by_id(explode(",", $course)[0]);
			$ret[$i]["courses"][] = array("name" => $course_info["name"], "sis_id" => $course_info["sis_id"]);
		}

		$ret[$i]["start_date"] = $subs["start_date"];
		$ret[$i]["end_date"] = (strlen($subs["end_date_ext"]) > 0 ? $subs["end_date_ext"] : $subs["end_date"]);
		$i++;
	}

	echo json_encode($ret);
	exit();

?>