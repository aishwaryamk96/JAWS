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
	if (!auth_api("webinar.get")) die("You do not have the required priviledges to use this feature.");

	load_module("webinar");

	$res_webinars = webinar_session_get_upcoming();
	if (!$res_webinars)
	{
		echo json_encode(array("Items" => false, "Count" => 0, "ScannedCount" => 0));
		exit();
	}
	$webinars = array();
	foreach ($res_webinars as $res_webinar)
	{
		$date = date_create_from_format("Y-m-d H:i:s", $res_webinar["webinar_session"]["start_date"]);
		$content = json_decode($res_webinar["webinar_session"]["content"], true);
		$webinars[] = array("webinarid" => $res_webinar["webinar_session"]["webinar_session_id"], "title" => $res_webinar["name"], "description" => $res_webinar["desc"], "date" => $date->format("Y-m-d"), "day" => $date->format("l"), "time" => $date->format("H:i:s"), "bgnd_pic" => $content["bgnd_pic"], "faculty_pic" => $content["faculty_pic"]);
	}

	echo json_encode(array("Items" => $webinars, "Count" => count($webinars), "ScannedCount" => count($webinars)));
	exit();
?>