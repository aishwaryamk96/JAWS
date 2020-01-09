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

	// This API function returns a list of all subscriptions that should start today and have "pending" as their status, plus the user information.

	if (!auth_api("subs.get"))
		die ("You do not have the required priviledges to use this feature.");

	load_module("subs");
	load_module("user");

	// Get all the subscriptions that should start today, i.e., start_date = today's date and status=pending
	$date_today = new DateTime("now");
	$date_today = db_sanitize($date_today->format("Y-m-d"));
	$res_subs = db_query("SELECT * FROM subs WHERE (start_date <= DATE_ADD(".$date_today.", INTERVAL 1 DAY)) AND status='pending';");

	// If no subscriptions found that satisfy the criteria, return count = 0
	if (!$res_subs)
	{
		echo json_encode(array("count" => 0));
		exit();
	}

	$subs_arr = array();
	foreach ($res_subs as $sub)
	{
		$res_user = db_query("SELECT * FROM user WHERE user_id=".$sub["user_id"]);
		$res_user_meta = db_query("SELECT * FROM user_meta WHERE user_id=".$sub["user_id"]);
		$res_user = $res_user[0];
		$res_user_meta = $res_user_meta[0];

		$subs["id"] = $sub["subs_id"];
		$subs["name"] = $res_user["name"];
		$subs["email"] = $res_user["email"];
		$subs["phone"] = $res_user["phone"];
		if (!isset($res_user["lms_soc"]) || strlen($res_user["lms_soc"]) == 0)
			continue;
		$subs["soc"] = $res_user["lms_soc"];
		$subs["soc_email"] = $res_user["soc_".$res_user["lms_soc"]];
		$subs["city"] = $res_user_meta["city"];
		$subs["state"] = $res_user_meta["state"];
		$subs["country"] = $res_user_meta["country"];
		$subs["gender"] = $res_user_meta["gender"];

		// Get the external course IDs for each course of the combo
		$combo = explode(";", $sub["combo"]);
		$combo_str = array();
		foreach ($combo as $course)
		{
			if (strlen($course) == 0)
					continue;
			$arr = explode(",", $course);
			// Concatenate UT course ID and learn_mode
			$combo_str[] = get_ext_course_id($arr[0], $_POST["persistence"]["subs"]["layer"])["id"].",".$arr[1];
		}
		$subs["combo"] = implode(";", $combo_str);

		// Get the external course IDs for each course of the complimentaries
		if (isset($sub["combo_free"]) && strlen($sub["combo_free"]) > 0)
		{
			$combo_free = explode(";", $sub["combo_free"]);
			$combo_free_str = array();
			foreach ($combo_free as $course)
			{
				if (strlen($course) == 0)
					continue;
				$arr = explode(",", $course);
				$combo_free_str[] = get_external_course_id($arr[0], $_POST["persistence"]["subs"]["layer"])["id"].",".$arr[1];
			}
			$subs["combo_free"] = implode(";", $combo_free_str);
		}
		else
			$subs["combo_free"] = "";

		// Set the corporate identifier too.
		$subs["corp"] = $sub["sorp"];

		$subs["start_date"] = $sub["start_date"];
		$subs["end_date"] = $sub["end_date"];

		$subs_arr[] = $subs;
	}

	// Return the subscriptions list with its count
	echo json_encode(array("count" => count($subs_arr), "subs" => $subs_arr));
	exit();

?>
