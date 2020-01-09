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

	// Constructs an associative array according to the expectation on mobile application
	function construct_mobile_app_course_array($course)
	{
		// Mapping between table columns and what mobile app expects
		$keys = array(
			"course_id" => "courseid",
			"name" => "coursename",
			"sis_id" => "coursecode",
			"sp_price_inr" => "discounted_price",
			"branch" => "coursetype",
			"img_main_big" => "bgnd_big",
			"img_main_small" => "bgnd_small",
			"ws_duration" => "duration",
			"hours_per_week" => "hpw",
			"tag_line" => "description",
			"prerequisite" => "prerequisites"
			);

		// Get the course meta and unset the element
		$course["short_description"] = $course["meta"]["desc"];
		$content = json_decode($course["meta"]["content"], true);
		unset($course["meta"]);
		unset($content["course_id"]);

		if (!isset($course["sp_price_inr"]) || strlen($course["sp_price_inr"]) == 0 || $course["sp_price_inr"] == null)
			$course["sp_price_inr"] = $course["il_price_inr"];
		
		foreach ($course as $key => $value)
		{
			if (isset($keys[$key]))
			{
				$course[$keys[$key]] = $value;
				unset($course[$key]);
			}
		}

		foreach ($content as $key => $value)
		{
			if (isset($keys[$key]))
				$course[$keys[$key]] = $value;
			else
				$course[$key] = $value;
		}

		$course["actual_price"] = "null";
		$course["percent_off"] = "500 Rs";
		$course["offer_details"] = "Offer Details";

		unset($course["duration_unit"]);
		unset($course["status"]);
		unset($course["il_code"]);
		unset($course["sp_code"]);
		unset($course["il_price_inr"]);
		unset($course["il_price_usd"]);
		unset($course["sp_price_usd"]);
		unset($course["il_status_inr"]);
		unset($course["il_status_usd"]);
		unset($course["sp_status_inr"]);
		unset($course["sp_status_usd"]);

		return $course;
	}

	function construct_mobile_app_user_array($user, $socialid, $source)
	{
		$keys = array(
			"user_id" => "Jig_ID",
			"name" => "First_Name",
			"email" => "Email_ID",
			"phone" => "Mobile_Num",
			"gender" => "Gender",
			"city" => "City",
			"state" => "State",
			"country" => "Country"
			);

		// Get the course meta and unset the element
		$content = json_decode($user["meta"]["content"], true);
		unset($user["meta"]);

		foreach ($user as $key => $value)
		{
			if (isset($keys[$key]))
			{
				$user[$keys[$key]] = $value;
				unset($user[$key]);
			}
		}

		foreach ($content as $key => $value)
		{
			if (isset($keys[$key]))
				$user[$keys[$key]] = $value;
			else
				$user[$key] = $value;
		}

		$user["Last_Name"] = "";
		$user["Middle_Name"] = "";
		$user["ISD_Code"] = "";
		$user["Record_Complete"] = "1";
		$user["SocialID"] = $socialid;
		$user["SocialIDProvider"] = $source;
		$user["Created_at"] = "";
		$user["Updated_at"] = "";

		unset($user["web_id"]);
		unset($user["pass"]);
		unset($user["soc_fb"]);
		unset($user["soc_gp"]);
		unset($user["soc_li"]);
		unset($user["lms_soc"]);
		unset($user["roles"]);
		unset($user["allow"]);
		unset($user["deny"]);
		unset($user["session"]);
		unset($user["status"]);
		unset($user["account_type"]);

		return $user;
	}

?>