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

	// Load stuff
	load_library("misc");

	function webinar_session_create($webinar_id, $start_date, $end_date, $content)
	{
		//Sanitize
		$start_date = db_sanitize($start_date);
		$end_date = db_sanitize($end_date);

		// Put check for pre existing record here <===================================================================================================

		// Insert
		db_exec("INSERT INTO webinar_session (webinar_id, start_date, end_date) VALUES (".$webinar_id.", ".$start_date.", ".$end_date.")");
		$webinar_session_id = db_get_last_insert_id();

		// Save content
		content_set("webinar_session", $webinar_session_id, $content);

		// All done
		return $webinar_session_id;
	}

	// This will update a webinar session dates and optionally the content too.
	// Note: It will NOT change the parent webinar, since that's a logical flaw.
	function webinar_session_update($webinar_session_id, $start_date, $end_date, $content = null)
	{
		//Sanitize
		$start_date = db_sanitize($start_date);
		$end_date = db_sanitize($end_date);

		// Update
		db_exec("UPDATE webinar_session SET start_date=".$start_date.", end_date=".$end_date." WHERE webinar_session_id=".$webinar_session_id);
		if (isset($content)) content_set("webinar_session", $webinar_session_id, $content);
	}

	function webinar_session_get_by_id($webinar_session_id)
	{
		// Check Existence
		$res = db_query("SELECT * FROM webinar_session WHERE webinar_session_id=".$webinar_session_id);
		if (!isset($res[0])) return false;

		// Return Data
		$res_webinar = $res[0];
		$res_webinar["content"] = content_get("webinar_session", $webinar_session_id);
		return $res_webinar;
	}

	// This will fetch the next upcoming webinar session of every webinar
	function webinar_session_get_upcoming()
	{
		$res_webinars = db_query("SELECT * FROM webinar WHERE status=1 ORDER BY webinar_id ASC;");
		if (!isset($res_webinars[0])) return false;		

		$webinars = array();
		foreach ($res_webinars as $webinar)
		{
			$sess = webinar_session_get_next($webinar["webinar_id"]);
			if ($sess === false) continue;
			else {
				$webinar["webinar_session"] = $sess;
				$webinars[] = $webinar;
			}
		}

		return $webinars;
	}

	// This will fetch the next upcoming webinar session of a particular webinar.
	function webinar_session_get_next($webinar_id) {
		$res = db_query("SELECT * FROM webinar_session WHERE (start_date>=CURDATE()) AND (webinar_id=".$webinar_id.") AND ((status='open') OR (status='upcoming')) ORDER BY start_date ASC LIMIT 1;");

		if (!isset($res[0])) return false;
		else {
			$res[0]["content"] = content_get("webinar_session", $res[0]["webinar_session_id"]);
			return $res[0];
		}
	}

	function webinar_session_reg($webinar_session_id, $user_id)
	{
		$res_reg = db_query("SELECT * FROM webinar_reg WHERE webinar_session_id=".$webinar_session_id." AND user_id=".$user_id);
		if (!$res_reg)
		{
			$res_webinar_id = db_query("SELECT webinar_id FROM webinar_session WHERE webinar_session_id=".$webinar_session_id.";");
			if (!$res_webinar_id)
				return false;
			db_exec("INSERT INTO webinar_reg (webinar_id, webinar_session_id, user_id, status) VALUES (".$res_webinar_id[0]["webinar_id"].", ".$webinar_session_id.", ".$user_id.", 'confirmed');");
		}
		return true;
	}

?>