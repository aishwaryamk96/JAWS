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

	load_module("user");
	load_module("refer");
	load_module("subs");
	load_module("leads");

	load_library("email");

	function get_fname($name)
	{
		$tokens = explode(" ", $name);
		$i = 0;
		while ($i < count($tokens))
		{
			if (strlen($tokens[$i]) > 2 && ctype_alpha($tokens[$i]))
				break;
			$i++;
		}
		if ($i == count($tokens))
			$i = 0;
		return $tokens[$i];
	}

	$user;
	// Authenticate the token here...
	$psk_info = psk_info_get($_POST["token"]);
	$id = $psk_info["entity_id"]; // this is the primary key id from refer table
	
	// Get the refer record
	$refer_details = refer_get_by_id($id);
	if ($refer_details["referrer_type"] == "user") {
		$user = user_get_by_id($refer_details["referrer_id"]);
	}
	else {

		$res_act = db_query("SELECT * FROM system_activity WHERE act_id=".$refer_details["referrer_id"]);
		$user = json_decode($res_act[0]["content"], true);
		$user["user_id"] = $res_act[0]["act_id"];
		$user_src = "system_activity";

	}

	$resent_count = $refer_details["resent_count"] + 1;

	// update resent count in database
	db_exec("UPDATE refer SET resent_count = '".$resent_count."' WHERE id=".$id);

	//send mail
	$courses_ref;
	$bundles_ref;

	if(strlen($refer_details['courses']) > 0){
		$courses_ref = explode(";", $refer_details['courses']);
	}

	if(strlen($refer_details['course_bundles']) > 0)
	{
		$bundles_ref = explode(";", $refer_details['course_bundles']);
	}
	
	$consults = count($courses_ref) + count($bundles_ref);
	//$courses_ref = array();
	//$bundles_ref = array();
	$courses_str = array();
	$courses = array();
	$ref_str = array();
	if (count($courses_ref) > 0) {
		foreach ($courses_ref as $course_id)
		{

			$course = db_query("SELECT course.name, meta.content, meta.desc FROM course INNER JOIN course_meta AS meta ON course.course_id = meta.course_id WHERE course.course_id=".$course_id)[0];
			$meta_content = json_decode($course["content"], true);
			$courses_str[] = "<span><a href='".JAWS_PATH_WEB."/redir?ru=".urlencode($meta_content["url_web"])."&lp=referral_email&email=".urlencode($refer_details["email"])."&name=".urlencode($refer_details["name"])."&phone=".urlencode($refer_details["phone"])."&source=".urlencode($user["email"])."&medium=".urlencode($course["name"])."&campaign=".urlencode("referrer_email=".$user["email"]."&course=".$course["name"])."'>".$course["name"]."</a></span>";
			$ref_str[] = $course["name"];
			$courses[] = array(
						"url" => JAWS_PATH_WEB."/redir?ru=".urlencode($meta_content["url_web"])."&lp=referral_email&email=".urlencode($refer_details["email"])."&name=".urlencode($refer_details["name"])."&phone=".urlencode($refer_details["phone"])."&source=".urlencode($user["email"])."&medium=".urlencode($course["name"]),
						"img" => $meta_content["img_main_small"],
						"name" => $course["name"],
						"desc" => $course["desc"]);
		}
	}
	if (count($bundles_ref) > 0)	 {
		foreach($bundles_ref as $bundle_id) {
			$bundle = db_query("SELECT bundle.name, meta.content, meta.desc FROM course_bundle AS bundle INNER JOIN course_bundle_meta AS meta ON bundle.bundle_id = meta.bundle_id WHERE bundle.bundle_id=".$bundle_id)[0];
			$meta_content = json_decode($bundle["content"], true);
			$courses_str[] = "<span><a href='".JAWS_PATH_WEB."/redir?ru=".urlencode($meta_content["url_web"])."&lp=referral_email&email=".urlencode($refer_details["email"])."&name=".urlencode($refer_details["name"])."&phone=".urlencode($refer_details["phone"])."&source=".urlencode($user["email"])."&medium=".urlencode($bundle["name"])."&campaign=".urlencode("referrer_email=".$user["email"]."&bundle=".$bundle["name"])."'>".$bundle["name"]."</a></span>";
			$ref_str[] = $bundle["name"];
			$courses[] = array("url" => JAWS_PATH_WEB."/redir?ru=".urlencode($meta_content["url_web"])."&lp=referral_email&email=".urlencode($refer_details["email"])."&name=".urlencode($refer_details["name"])."&phone=".urlencode($refer_details["phone"])."&utm_source=".urlencode($user["email"])."&utm_medium=".urlencode($bundle["name"])."&campaign=".urlencode("referrer_email=".$user["email"]."&bundle=".$bundle["name"]), "img" => $meta_content["img_main_small"], "name" => $bundle["name"], "desc" => $bundle["desc"]);
		}
	}

	
	$content["courses_str"] = implode(" and ", $courses_str);
	$content["referred"]["name"] = $refer_details["name"];
	$content["coupon_code"] = $refer_details["coupon_code"];
	$content["referrer"]["name"] = $user["name"];
	$content["referrer"]["fname"] = get_fname($user["name"]);
	//$content["referred"]["fname"] = get_fname($_POST["referral"]["name"]);
	$content["courses"] = $courses;
	$content["courses_count"] = $consults;
	$GLOBALS['jaws_exec_test_email_to'] = "himanshu@jigsawacademy.com";

	send_email("referral.new", array("to" => (($GLOBALS["jaws_exec_live"]) ? $refer_details["email"] : "himanshu@jigsawacademy.com"), "subject" => $user["name"]." wants to boost your career"), $content);

	die(json_encode(["rc" => $resent_count, "id" => $refer_details["id"]]));
