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

	// Check the system_activity for a record for this user and check the status.
	// If it is pending, no new records for now; if it is executed, create new records.
	// If the record is not present, create a new record.
	// This API checks if the email being referred is already available with JAWS or is a new one
	load_module("user");
	load_module("subs");

	load_library("email");

	// Exit if even one of the required POST parameters is not present
	if (!isset($_POST["token"]) || !isset($_POST["referral_email"]))
		die("You do not have required priviledges to use this feature.");

	// Authenticate the token here...
	$psk_info = psk_info_get($_POST["token"]);
	$user_src = $psk_info["entity_type"];
	$user;

	// Get the referer
	if ($user_src == "user")
		$user = user_get_by_id($psk_info["entity_id"]);
	else
	{
		$res_act = db_query("SELECT * FROM system_activity WHERE act_id=".$psk_info["entity_id"]);
		$user = json_decode($res_act[0]["content"], true);
		$user["user_id"] = $res_act[0]["act_id"];
	}

	// Check referral activity for the student
	$res_act = db_query("SELECT * FROM system_activity WHERE act_type='jlc.referral' AND activity='referral' AND context_type=".db_sanitize($user_src)." AND context_id=".$user["user_id"]." ORDER BY act_id DESC;");

	$ref_info = json_decode($res_act[0]["content"], true);
	$ref;
	$write_back = array();
	$rc;
	foreach ($ref_info["r"] as $referral)
	{
		if ($referral["e"] == $_POST["referral_email"])
		{
			$ref = $referral;
			if (!isset($referral["rc"]))
				$referral["rc"] = 0;
			$referral["rc"] = intval($referral["rc"]) + 1;
			$rc = $referral["rc"];
		}
		$write_back[] = $referral;
	}

	//die("UPDATE system_activity SET content=".json_encode(["r" => $write_back, "n" => $ref_info["n"]])." WHERE act_id=".$res_act[0]["act_id"]);

	db_exec("UPDATE system_activity SET content=".db_sanitize(json_encode(["r" => $write_back, "n" => $ref_info["n"]]))." WHERE act_id=".$res_act[0]["act_id"]);

	$consults = explode(";", $ref["c"]);
	$courses_str = array();
	$courses = array();
	$ref_str = array();
	foreach ($consults as $consult)
	{
		if (substr($consult, 0, 1) == "c")
		{
			$course = db_query("SELECT course.name, meta.content, meta.desc FROM course INNER JOIN course_meta AS meta ON course.course_id = meta.course_id WHERE course.course_id=".substr($consult, 1))[0];
			$meta_content = json_decode($course["content"], true);
			$courses_str[] = "<span><a href='".JAWS_PATH_WEB."/redir?ru=".urlencode($meta_content["url_web"])."&lp=referral_email&email=".urlencode($ref["e"])."&name=".urlencode($ref["n"])."&phone=".urlencode($ref["p"])."&source=".urlencode($user["email"])."&medium=".urlencode($course["name"])."&campaign=".urlencode("referrer_email=".$user["email"]."&course=".$course["name"])."'>".$course["name"]."</a></span>";
			$ref_str[] = $course["name"];
			$courses[] = array(
						"url" => JAWS_PATH_WEB."/redir?ru=".urlencode($meta_content["url_web"])."&lp=referral_email&email=".urlencode($ref["e"])."&name=".urlencode($ref["n"])."&phone=".urlencode($ref["p"])."&source=".urlencode($user["email"])."&medium=".urlencode($course["name"]),
						"img" => $meta_content["img_main_small"],
						"name" => $course["name"],
						"desc" => $course["desc"]);
		}
		else
		{
			$bundle = db_query("SELECT bundle.name, meta.content, meta.desc FROM course_bundle AS bundle INNER JOIN course_bundle_meta AS meta ON bundle.bundle_id = meta.bundle_id WHERE bundle.bundle_id=".substr($consult, 1))[0];
			$meta_content = json_decode($bundle["content"], true);
			$courses_str[] = "<span><a href='".JAWS_PATH_WEB."/redir?ru=".urlencode($meta_content["url_web"])."&lp=referral_email&email=".urlencode($ref["e"])."&name=".urlencode($ref["n"])."&phone=".urlencode($ref["p"])."&source=".urlencode($user["email"])."&medium=".urlencode($bundle["name"])."&campaign=".urlencode("referrer_email=".$user["email"]."&bundle=".$bundle["name"])."'>".$bundle["name"]."</a></span>";
			$ref_str[] = $bundle["name"];
			$courses[] = array("url" => JAWS_PATH_WEB."/redir?ru=".urlencode($meta_content["url_web"])."&lp=referral_email&email=".urlencode($ref["e"])."&name=".urlencode($ref["n"])."&phone=".urlencode($ref["p"])."&utm_source=".urlencode($user["email"])."&utm_medium=".urlencode($bundle["name"])."&campaign=".urlencode("referrer_email=".$user["email"]."&bundle=".$bundle["name"]), "img" => $meta_content["img_main_small"], "name" => $bundle["name"], "desc" => $bundle["desc"]);
		}
	}

	$ref_str = implode(", ", $ref_str);

	$content["courses_str"] = implode(" and ", $courses_str);
	$content["referred"]["name"] = $ref["n"];
	$content["coupon_code"] = $coupon;
	$content["referrer"]["name"] = $user["name"];
	$content["referrer"]["fname"] = get_fname($user["name"]);
	//$content["referred"]["fname"] = get_fname($_POST["referral"]["name"]);
	$content["courses"] = $courses;
	$content["courses_count"] = count($consults);
	$GLOBALS['jaws_exec_test_email_to'] = "himanshu@jigsawacademy.com";

	send_email("referral.new", array("to" => (($GLOBALS["jaws_exec_live"]) ? $ref["e"] : "himanshu@jigsawacademy.com"), "subject" => $user["name"]." wants to boost your career"), $content);

/*
	// Send the email to referral email ID:
	$subject = $user["name"]." wants to boost your career";
	$to = (($GLOBALS["jaws_exec_live"]) ? $_POST["referral"]["email"] : "himanshu@jigsawacademy.com");
	$body = "<p>Hey ".$_POST["referral"]["name"].",</p>
			<p>Give your career that dhinchyak boost with these highly recommended courses ".implode(" and ", $courses_str)." from Jigsaw Academy. ".$user["name"]." thinks that these courses would be ideal for your career.</p>

			<p>Enroll within the next 30 days using this coupon code <b>".$coupon."</b> and get a further discount of 5% on the courses.</p>

			<p>Jigsaw Academy, their specializations, student councilors and services are exactly of the standard that will support your career and take it places. If you'd like to explore other courses at Jigsaw, visit <a href='https://www.jigsawacademy.com/online-analytics-training/'>Jigsaw Academy Courses page</a>.</p>

			<p>Regards,</p>
			<p>Team Jigsaw</p>";

	$headers[] = 'MIME-Version: 1.0';
	$headers[] = 'Content-type: text/html; charset=iso-8859-1';
	$headers[] = "From: info@jigsawacademy.com";
	// Send email to referer if CC is set
	$headers[] = "Cc: ".((isset($_POST["referral"]["cc"]) && strcmp($_POST["referral"]["cc"], "1") == 0) ? $user["email"] : "");
	$headers[] = "Bcc: jagruti@jigsawacademy.com";

	mail($to, $subject, $body, implode("\r\n", $headers));
*/


	// Send an email to leads email ID also
	/*
	$headers = array();
	$subject = "Referral invite sent by ".$user["email"];
	$to = ($GLOBALS["jaws_exec_live"] ? "leads@jigsawacademy.com" : "himanshu@jigsawacademy.com");
	$body_leads = "<p>Hi,</p>
			<p>Glad to tell you that our student ".$user["name"].", ".$user["email"]." has referred his/her friend ".$ref["n"].", ".$ref["e"].", ".$ref["p"]." and has recommended course(s) ".$ref_str.".</p>
			<p>You can contact the referred person to consult and help select the right set of courses.</p>

			<p>Thank you,</p>
			<p>JAWS Referral Feature</p><br />";

	$headers[] = 'MIME-Version: 1.0';
	$headers[] = 'Content-type: text/html; charset=iso-8859-1';
	$headers[] = "From: info@jigsawacademy.com";
	$headers[] = "Bcc: jagruti@jigsawacademy.com";

	mail($to, $subject, $body_leads, implode("\r\n", $headers));*/

	// Expire the token and exit
	die(json_encode(["rc" => $rc, "id" => $res_act[0]["act_id"]]));

?>