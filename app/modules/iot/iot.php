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

	setlocale(LC_MONETARY, 'en_IN');

	// On Load
	function iot_view_init() {
		
		ob_start(); // Start Buffering 
		load_module("leads");
		auth_session_init();
		iot_get_specialization();
		if ((isset($_COOKIE["_jafl_t_iot"])) && (auth_session_is_logged())) {
			leads_basic_assoc_user_by_id($_SESSION["user"]["user_id"], $_COOKIE["_jafl_t_iot"]);
			iot_basic_capture_url("login", true, false);
		}

		else iot_basic_capture_url();
	}

	// This function will do a Lead Basic Capture (URL)
	function iot_basic_capture_url($trigger = "", $forcecapture = false, $cookiecapturefollowup = true) {

		// Load Stuff
		load_module("leads"); 	

		// Prep
		$lead_params["utm_source"] = (isset($_GET["utm_source"])) ? $_GET["utm_source"] : ""; 
		$lead_params["utm_campaign"] = (isset($_GET["utm_campaign"])) ? $_GET["utm_campaign"] : ""; 
		$lead_params["utm_medium"] = (isset($_GET["utm_medium"])) ? $_GET["utm_medium"] : ""; 
		$lead_params["utm_segment"] = (isset($_GET["utm_segment"])) ? $_GET["utm_segment"] : ""; 
		$lead_params["utm_term"] = (isset($_GET["utm_term"])) ? $_GET["utm_term"] : ""; 
		$lead_params["utm_numvisits"] = (isset($_GET["utm_numvisits"])) ? $_GET["utm_numvisits"] : ""; 

		// Check if capture is needed
		if ((!((isset($_GET["utm_source"])) || (isset($_GET["utm_campaign"])) || (isset($_GET["utm_medium"])) || (isset($_GET["utm_segment"])) || (isset($_GET["utm_term"])) || (isset($_GET["utm_numvisits"])))) && (!$forcecapture)) return;

		// Extract more from cookies
		if ((isset($_COOKIE["__utmz"])) && (strlen($_COOKIE["__utmz"]) > 0)) $lead_params["gcl_id"] = leads_cookie_utm_extract_var($_COOKIE["__utmz"], "utmgclid");
		//if ((isset($_COOKIE["_fp73"])) && (strlen($_COOKIE["_fp73"]) > 0)) $lead_params["global_id_perm"] = $_COOKIE["_fp73"];
		//if ((isset($_COOKIE["_fs73"])) && (strlen($_COOKIE["_fs73"]) > 0)) $lead_params["global_id_session"] = $_COOKIE["_fs73"];

		// Prep internal vars
		if (isset($_SERVER['HTTP_REFERER'])) $lead_params["referer"] = $_SERVER['HTTP_REFERER'];
		$lead_params["ip"] = $_SERVER['REMOTE_ADDR'];
		$lead_params["ad_lp"] = "www.jigsawacademy.com/iot";
		$lead_params["ad_url"] = $_SERVER['REQUEST_URI'];
		$lead_params["type"] = "url";
		$lead_params["trigger"] = (strcmp($trigger, "") == 0) ? "pageload" : $trigger;

		// JAWS Cookie Capture Flag
		if ($cookiecapturefollowup) {
	    		$_SESSION["leads"]["capture"] = true;
	    		$_SESSION["leads"]["trigger"] = $lead_params["trigger"];
		}

		// Check user log in state
		$mode;
		$id_or_token = "";
		$mode_var = "";

		if (auth_session_is_logged()) {
			$mode = "id";
			$mode_var = "user_id";
			$id_or_token = $_SESSION["user"]["user_id"];

			//unset token
			setcookie("_jafl_t_iot", "", time() - 3600);
		}

		else {
			$mode = "token";
			$mode_var = "token";
			if (isset($_COOKIE["_jafl_t_iot"])) $id_or_token = $_COOKIE["_jafl_t_iot"];
		}

		// Capture the lead via AFL API !
		$token_new = leads_basic_capture($lead_params, $mode, $id_or_token);

		// Process result
		if (!auth_session_is_logged()) setcookie("_jafl_t_iot", ((isset($_COOKIE["_jafl_t_iot"])) ? $id_or_token : $token_new), time() + (86400 * 10));		

	}

	function iot_a_part($ru,$login,$profile, $other = "")
	{
		if (strpos($ru, '?')) {
			$ru = $ru.'&utm_source=iot&utm_campaign=iot&utm_term=iot';
		}
		else
		{
			$ru = $ru.'?utm_source=iot&utm_campaign=iot&utm_term=iot';
		}
		if(auth_session_is_logged()===false) { ?>
			 href="#" data-toggle="modal" data-target="<?php echo $login; ?>" data-ru="<?php echo $ru ?>" data-verify="true"
		<?php }
		elseif(empty($_SESSION['user']['phone'])){ ?>
			href="#" data-toggle="modal" data-target="<?php echo $profile; ?>" data-ru="<?php echo JAWS_PATH_WEB."/view/frontend/redir/wp.login?redir=".urlencode($ru)?>" data-verify="true"
		<?php }
		else if (strlen($other) == 0)
		{ ?>
			href="<?php echo JAWS_PATH_WEB."/view/frontend/redir/wp.login?redir=".urlencode($ru) ?>"
		<?php }
		else {  ?>
			href="#" data-toggle="modal" data-target="<?php echo $other; ?>" data-ru="<?php echo JAWS_PATH_WEB."/view/frontend/redir/wp.login?redir=".urlencode($ru)?>" data-verify="true"
		<?php }
		
	}

	/** function to get id of specialization **/
	function iot_get_specialization()
	{
		load_module("course");
		load_library("persistence");

		$iot_courses["bundles"] = [];
		$iot_courses["courses"] = [];

		$bundles = course_bundle_get_by_category('iot');
		foreach ($bundles as $bundle)
		{
			$bundle["wp_id"] = get_external_id(["id" => $bundle["bundle_id"], "layer" => "wppl", "type" => "bundle"])["id"];
			$iot_courses["bundles"][] = $bundle;
		}

		$courses = course_get_info_by_category('iot');
		foreach ($courses as $course)
		{
			$course["wp_id"] = get_external_id(["id" => $course["course_id"], "layer" => "wppl", "type" => "course"])["id"];
			$iot_courses["courses"][] = $course;
		}
		$GLOBALS["iot_courses"] = $iot_courses;
	}
?>