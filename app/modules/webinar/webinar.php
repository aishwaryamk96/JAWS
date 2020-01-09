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

	// Webinar Module
	// Each Webinar can have multiple sessions
	// Each session can have multiple registrations
	// Status of webinar is on or off
	// Status of session is upcoming -> open -> closed -> disabled
	// Note : Status of webinar and session must be checked independantly. Code does not check for webinar status everywhere <-- This issue must be fixed!!!
	// --------

	function webinar_create($name, $desc, $category = null)
	{
		// Prep
		$name = db_sanitize($name);
		$desc = db_sanitize($desc);
		if (isset($category)) $category = db_sanitize($category);
		else $category = "NULL";

		// Create
		db_exec("INSERT INTO webinar (name, `desc`, category) VALUES (".$name.", ".$desc.", ".$category.")");
		$webinar_id = db_get_last_insert_id();

		// Done
		return $webinar_id;
	}

	function webinar_update($webinar_id, $name, $desc, $category = null, $status = true)
	{
		// Prep
		$name = db_sanitize($name);
		$desc = db_sanitize($desc);
		if (isset($category) && (strcmp($category, "NULL") != 0)) $category = db_sanitize($category);

		// Update		
		db_exec("UPDATE webinar SET name=".$name.", `desc`=".$desc.", status=".($status ? "1" : "0").(isset($category) ? ", category=".$category : "")." WHERE webinar_id=".$webinar_id);
	}

	// This will fetch an (optionally filtered active) webinar by id
	function webinar_get_by_id($webinar_id, $filter_inactive = true) {
		$res = db_query("SELECT * FROM webinar WHERE (webinar_id=".$webinar_id.")".($filter_inactive ? " AND (status=1)" : "")." LIMIT 1;");

		if (!isset($res[0])) return false;
		else return $res[0];
	}

	// This will fetch (optionally filtered active) webinars of the given category
	function webinar_get_by_category($category, $filter_inactive = true) {
		$category = strtolower($category);
		$res = db_query("SELECT * FROM webinar WHERE (category LIKE ".db_sanitize($category).")".($filter_inactive ? " AND (status=1)" : "").";");

		if (!isset($res[0])) return false;
		else return $res;
	}

?>