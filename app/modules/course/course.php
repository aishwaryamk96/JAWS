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

	// This will return the aggregate duration of all courses for a combo - from sum of duration of courses / duration of bundle + 3 months per complimentary course
	function course_get_duration($combo, $combo_free = "", $bundle_id = "") {

        $duration = 0;

        $has_bundle_duration = false;

		// Check if Bundle
		if (strlen($bundle_id) > 0) {
            $res = db_query("SELECT combo, subs_duration_length, subs_duration_unit FROM course_bundle WHERE bundle_id=".$bundle_id." LIMIT 1;");

            if (isset($res[0])) {
                if (isset($res[0]['subs_duration_length'])) {
                	if (strlen($res[0]['subs_duration_length']) > 0) {
                		$has_bundle_duration = true;
                 	   	if (strcmp($res[0]['subs_duration_unit'], 'days') == 0) $duration += intval($res[0]['subs_duration_length']);
                    	else if (strcmp($res[0]['subs_duration_unit'], 'months') == 0) $duration += intval($res[0]['subs_duration_length']) * 30;
                    	else if (strcmp($res[0]['subs_duration_unit'], 'weeks') == 0) $duration += intval($res[0]['subs_duration_length']) * 7;

                    	// If the combo contains more courses than the bundle itself, add the duration of each extra course
                    	$course_arr = explode(";", $combo);
                    	$bundle_combo = explode(";", $res[0]["combo"]);
                    	if (count($bundle_combo) < count($course_arr)) {
                    		$courses = [];
                    		foreach ($bundle_combo as $course) {
                    			$courses[] = explode(",", $course)[0];
                    		}
							foreach ($course_arr as $course) {
								$course_id = explode(",", $course);
								if (!in_array($course_id[0], $courses)) {
									$res = db_query("SELECT duration_length, duration_unit FROM course WHERE course_id=".$course_id[0].";");

									if (strcmp($res[0]['duration_unit'], 'days') == 0) $duration += intval($res[0]['duration_length']);
									else if (strcmp($res[0]['duration_unit'], 'months') == 0) $duration += intval($res[0]['duration_length']) * 30;
									else if (strcmp($res[0]['duration_unit'], 'weeks') == 0) $duration += intval($res[0]['duration_length']) * 7;
								}
							}
                    	}
                	}
                }
            }
        }
        if (!$has_bundle_duration) {
	        // Bundle not present
	        $course_arr = explode(";", $combo);

	        foreach ($course_arr as $course) {
	            $course_id = explode(",", $course);
	            $res = db_query("SELECT duration_length, duration_unit FROM course WHERE course_id=".$course_id[0].";");

	            if (strcmp($res[0]['duration_unit'], 'days') == 0) $duration += intval($res[0]['duration_length']);
	            else if (strcmp($res[0]['duration_unit'], 'months') == 0) $duration += intval($res[0]['duration_length']) * 30;
	            else if (strcmp($res[0]['duration_unit'], 'weeks') == 0) $duration += intval($res[0]['duration_length']) * 7;
	        }
	    }

	    // Complimentary course duration is renabled as per decision dated 15th June, 2017.
	    // Complimentary course duration has been disabled as per mail dated 14th March, 2019
	    // https://mail.google.com/mail/u/0/#inbox/FMfcgxwBWBCRXGXvjFKDMWCzfhQjnQKn
        // if (strlen($combo_free) != 0) {
        //     $course_free_arr = explode(";", $combo_free);
        //     $duration += count($course_free_arr) * 90;
        // }

	    // Cap the access duration to 22 months
	    // Based on mail dated 14th March, 2019
	    // https://mail.google.com/mail/u/0/#inbox/FMfcgxwBWBCRXGXvjFKDMWCzfhQjnQKn
        if ($duration > 660) {
        	$duration = 660;
        }

        return $duration;

	}

	// This will return an array of course_ids and corresponding learn modes from combo string - NOTE : THIS WILL NOT SORT
	function course_get_combo_arr($combo_str) {
		$course_info_arr = explode(";", $combo_str);
		$combo_arr = [];

		foreach($course_info_arr as $course_info_str) {
			if (strlen($course_info_str) == 0) continue;
			$course_info = explode(",", $course_info_str);
			$combo_arr[$course_info[0]] = isset($course_info[1]) ? $course_info[1] : "" ;
		}

		return $combo_arr;
	}

	// This will return a string of course combo from an combo info array of combo_id => learn_mode - NOTE : THIS WILL NOT SORT
	function course_get_combo_str($combo_arr) {
		$combo_str = "";
		foreach($combo_arr as $course_id => $learn_mode) $combo_str .= (strlen($combo_str > 0) ? ";" : "").$course_id.",".$learn_mode;
		return $combo_str;
	}

	// This will return a human readable string of course combo short codes
	function course_get_combo_code($combo_arr) {

		$combo_code = "";
		foreach($combo_arr as $course_id => $learn_mode) {

			$code_db_column = (strcmp($learn_mode, "1") == 0) ? "il_code" : "sp_code";
			$res = db_query("SELECT name, ".$code_db_column." FROM course WHERE course_id=".$course_id." LIMIT 1;");

			if (!isset($res[0][$code_db_column])) $res[0][$code_db_column] = "";
			$code = $res[0][$code_db_column];
			if (strlen($code) == 0) {
				if (!isset($res[0]["name"])) continue;
				$code = $res[0]["name"]." (".((strcmp($learn_mode, "1") == 0) ? "Premium" : "Regular").")";
			}

			$combo_code .= ((strlen($combo_code) > 0) ? " + " : "").$code;
		}

		return $combo_code;

	}

	// This will return an array of course names and modes in brackets (str)
	// - ----------------------- ADD WAY TO ADD (FREE) besides course name
	function course_get_combo_names_arr($combo_arr, $appendstr="") {

		$combo_names;
		foreach($combo_arr as $course_id => $learn_mode) {

			$res = db_query("SELECT name FROM course WHERE course_id=".$course_id." LIMIT 1;");

			if (!isset($res[0]["name"])) continue;
			$course["name"] = $res[0]["name"].((strlen($appendstr) > 0) ? " (".$appendstr.")" : "");
			$course["learn_mode"] = "(".((strcmp($learn_mode, "1") == 0)? "Premium" : "Regular").")";

			$combo_names[$course["name"]] = $course["learn_mode"];

		}

		return $combo_names;

	}

	// This will create a new record in the course and course_meta tables
	function course_build($course)
	{
		// Save original data function args for post execution hook !
		$data_hook = $course;

		// Set defaults, if not present and do some cleanup on prices
		$course = course_sanitize_nicely($course);

		$data_hook2 = $course;
		foreach ($data_hook as $key => $value) {
			$data_hook2[$key] = $value;
		}

		$query = "INSERT INTO course (name, status, sis_id, il_code, sp_code, il_price_inr, il_price_usd, sp_price_inr, sp_price_usd, il_status_inr, il_status_usd, sp_status_inr, sp_status_usd, duration_length, duration_unit) VALUES (".$course["name"].", ".$course["status"].", ".$course["sis_id"].", ".$course["il_code"].", ".$course["sp_code"].", ".$course["il_price_inr"].", ".$course["il_price_usd"].", ".$course["sp_price_inr"].", ".$course["sp_price_usd"].", ".$course["il_status_inr"].", ".$course["il_status_usd"].", ".$course["sp_status_inr"].",".$course["sp_status_usd"].",".$course["duration_length"].",".$course["duration_unit"].");";

		db_exec($query);

		// Get the ID of this course and set to course_id of the course array
		$course["course_id"] = db_get_last_insert_id();

		// If the record has not been saved in the table skip course_meta insertion
		if ($course["course_id"] === NULL) {
			return false;
		}

		// Insert into course_meta table
		$create_date = db_sanitize(date("Y-m-d H:i:s"));
		$query = "INSERT INTO course_meta (course_id, slug, `desc`, category, content, create_user, create_date) VALUES (".$course["course_id"].", ".$course["slug"].", ".$course["description"].", ".$course["category"].", ".$course["content"].", 0, ".$create_date.");";
		db_exec($query);

		// Post Execution Hook
		$data_hook["course_id"] = $course["course_id"];
		handle("course_create__", $data_hook2);

		return $course;

	}

	// This updates a course record in course and course_meta tables according to "id" specified in the $course array
	function course_update($course)
	{

		// Save original function args for post-execution hook
		$data_hook = $course;

		// Set defaults, if not present and do some cleanup on prices
		$course = course_sanitize_nicely($course);

		$data_hook2 = $course;
		foreach ($data_hook as $key => $value) {
			$data_hook2[$key] = $value;
		}

		$query = "UPDATE course SET name=".$course["name"].", status=".$course["status"].", ".(!empty($course["sis_id"]) && $course["sis_id"] != "NULL" ? "sis_id=".$course["sis_id"].", " : "")."il_code=".$course["il_code"].", sp_code=".$course["sp_code"].", il_price_inr=".$course["il_price_inr"].", il_price_usd=".$course["il_price_usd"].", sp_price_inr=".$course["sp_price_inr"].", sp_price_usd=".$course["sp_price_usd"].", il_status_inr=".$course["il_status_inr"].", il_status_usd=".$course["il_status_usd"].", sp_status_inr=".$course["sp_status_inr"].", sp_status_usd=".$course["sp_status_usd"].", duration_unit=".$course["duration_unit"].", duration_length=".$course["duration_length"]." WHERE course_id=".$course["course_id"];

		db_exec($query);

		// Update course_meta table
		$query = "UPDATE course_meta SET ".((!empty($course["slug"]) && $course["slug"] != "NULL") ? "slug=".$course["slug"].", " : "")."`desc`=".$course["description"].", category=".$course["category"].", content=".$course["content"]." WHERE course_id=".$course["course_id"].";";
		db_exec($query);

		handle("course_update__", $data_hook2); // Post-Execution Hook

	}

	// Performs formatting of prices and sets defaults values
	function course_sanitize_nicely($course) {

		$course["name"] = db_sanitize($course["name"]);
		$course["slug"] = empty($course["slug"]) ? "NULL" : db_sanitize($course["slug"]);
		$course["status"] = db_sanitize($course["status"]);
		// $course["il_code"] = db_sanitize($course["il_code"]);
		// $course["sp_code"] = db_sanitize($course["sp_code"]);

		// Set default prices, if not present
		if (!isset($course["il_price_inr"]) || strlen($course["il_price_inr"]) == 0) {
			$course["il_price_inr"] = "NULL";
		}
		if (!isset($course["il_price_usd"]) || strlen($course["il_price_usd"]) == 0) {
			$course["il_price_usd"] = "NULL";
		}
		if (!isset($course["sp_price_inr"]) || strlen($course["sp_price_inr"]) == 0) {
			$course["sp_price_inr"] = "NULL";
		}
		if (!isset($course["sp_price_usd"]) || strlen($course["sp_price_usd"]) == 0) {
			$course["sp_price_usd"] = "NULL";
		}

		if (!isset($course["il_code"]) || strlen($course["il_code"]) == 0) {
			$course["il_code"] = "NULL";
		}
		else {
			$course["il_code"] = db_sanitize($course["il_code"]);
		}

		if (!isset($course["sp_code"]) || strlen($course["sp_code"]) == 0) {
			$course["sp_code"] = "NULL";
		}
		else {
			$course["sp_code"] = db_sanitize($course["sp_code"]);
		}

		if (isset($course["sis_id"])) {

			$course["sis_id"] = trim($course["sis_id"]);
			if (!empty($course["sis_id"])) {
				$course["sis_id"] = db_sanitize($course["sis_id"]);
			}
			else {
				$course["sis_id"] = "NULL";
			}

		}
		else {
			$course["sis_id"] = "NULL";
		}

		// Set default statuses, if not set
		if (!isset($course["il_status_inr"]) || strlen($course["il_status_inr"]) == 0) {

			if (strcmp($course["il_price_inr"], "NULL") == 0) {
				$course["il_status_inr"] = 0;
			}
			else {
				$course["il_status_inr"] = 1;
			}

		}
		if (!isset($course["il_status_usd"]) || strlen($course["il_status_usd"]) == 0) {

			if (strcmp($course["il_price_usd"], "NULL") == 0) {
				$course["il_status_usd"] = 0;
			}
			else {
				$course["il_status_usd"] = 1;
			}

		}
		if (!isset($course["sp_status_inr"]) || strlen($course["sp_status_inr"]) == 0) {

			if (strcmp($course["sp_price_inr"], "NULL") == 0) {
				$course["sp_status_inr"] = 0;
			}
			else {
				$course["sp_status_inr"] = 1;
			}

		}
		if (!isset($course["sp_status_usd"]) || strlen($course["sp_status_usd"]) == 0) {

			if (strcmp($course["sp_price_usd"], "NULL") == 0) {
				$course["sp_status_usd"] = 0;
			}
			else {
				$course["sp_status_usd"] = 1;
			}

		}
		if (!isset($course["duration_length"]) || strlen($course["duration_length"]) == 0) {

			$course["duration_length"] = "90";
			$course["duration_unit"] = "days";

		}
		if (!isset($course["duration_unit"]) || strlen($course["duration_unit"]) == 0) {
			$course["duration_unit"] = "days";
		}
		$course["duration_unit"] = db_sanitize($course["duration_unit"]);

		if (!isset($course["description"]) || strlen($course["description"]) == 0) {
			$course["description"] = "NULL";
		}
		else {
			$course["description"] = db_sanitize($course["description"]);
		}
		if (!isset($course["category"]) || strlen($course["category"]) == 0) {
			$course["category"] = "NULL";
		}
		else {
			$course["category"] = db_sanitize($course["category"]);
		}
		if (!isset($course["content"]) || strlen($course["content"]) == 0) {
			$course["content"] = "NULL";
		}
		else {
			$course["content"] = db_sanitize($course["content"]);
		}

		return $course;

	}

	function course_get_short_code_str($combo_str) {
		return course_get_combo_code(course_get_combo_arr($combo_str));
	}

	function course_get_info_by_id($course_id)
	{
		$res_course = db_query("SELECT * FROM course WHERE course_id=".$course_id);
		if (!$res_course)
			return false;

		$res_course = $res_course[0];
		$res_course_meta = db_query("SELECT * FROM course_meta WHERE course_id=".$course_id);
		$res_course["meta"] = $res_course_meta[0];

		return $res_course;
	}

	function course_get_info_all($all = false, $no_show = false)
	{
		$res_courses = db_query("SELECT * FROM course WHERE ".($all ? "1" : "status='enabled'").($no_show ? " AND no_show=0" : "").";");
		$courses = array();
		foreach ($res_courses as $course)
		{
			$course_meta = db_query("SELECT * FROM course_meta WHERE course_id=".$course["course_id"]);
			$course["meta"] = $course_meta[0];
			$courses[] = $course;
		}

		return $courses;
	}

	// Get the courses by the branch (Wordpress branches)
	function course_get_info_by_branch($branch, $no_show = false)
	{
		$res_courses_meta = db_query("SELECT * FROM course_meta WHERE content LIKE '%\"branch\":\"".$branch."\"%'");
		$courses = array();
		foreach ($res_courses_meta as $course_meta)
		{
			$course = db_query("SELECT * FROM course WHERE course_id=".$course_meta["course_id"]);
			$course = $course[0];
			if ($course["no_show"] == 1 && $no_show) {
				continue;
			}
			$course["meta"] = $course_meta;
			$courses[] = $course;
		}

		return $courses;
	}

	/** function to get course information based on category = iot **/
	function course_get_info_by_category($category)
	{
		$res_course_meta = db_query("SELECT * FROM course as c INNER JOIN course_meta as cm on c.course_id = cm.course_id WHERE cm.category = ".db_sanitize($category).";");
		return $res_course_meta;
	}

?>
