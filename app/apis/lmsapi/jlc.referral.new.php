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
	load_module("leads");

	load_library("email");

	// Exit if even one of the required POST parameters is not present
	if (!isset($_POST["token"]) || !isset($_POST["referral"]) || !isset($_POST["referral"]["email"]) || !isset($_POST["referral"]["name"]) || !isset($_POST["referral"]["consult"]) || !isset($_POST["referral"]["phone"]))
		die("You do not have required priviledges to use this feature.");

	$_POST["referral"]["name"] = trim($_POST["referral"]["name"]);
	$_POST["referral"]["email"] = strtolower(trim($_POST["referral"]["email"]));
	$_POST["referral"]["phone"] = trim($_POST["referral"]["phone"]);
	$_POST["referral"]["consult"] = trim($_POST["referral"]["consult"]);

	/*
		$_POST["referral"] = Parent element;
		$_POST["referral"]["email"] = Email of referral;
		$_POST["referral"]["name"] = Name of referral;
		$_POST["referral"]["consult"] = Courses or bundles referred;
		$_POST["referral"]["msg"] = Any custom msg by the referer;
		$_POST["cc"] = 1, if student selects "CC me" option;
	*/

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

	/* Exit if the token does not belong to the refering user
	if (($psk["entity_id"] != $user["user_id"]) || (strcmp($psk["entity_type"], "user") != 0))
		die("You do not have the required priviledges to use this feature. :P");
/*
	// Check if this email ID or the phone number has already been referred
	$res_referral_act = db_query("SELECT content FROM system_activity WHERE act_type='jlc.referral' AND activity='referral';");
	if (isset($res_referral_act[0]))
	{
		foreach ($res_referral_act as $referral_act)
		{
			$content = json_decode($referral_act["content"], true);
			foreach ($content as $referral)
			{
				if (strcmp($_POST["referral"]["email"], $referral["e"]) == 0 || strcmp($_POST["referral"]["phone"], $referral["p"]) == 0)
					die ("Referral has already been referred");
			}
		}
	}
/*
	// Check if the referral is already enrolled with us
	$referred_user = user_get_by_email($_POST["referral"]["email"]);
	if (!$referred_user)
	{
		$referred_user = db_query("SELECT user_id FROM user WHERE phone=".db_sanitize($_POST["referral"]["phone"]).";");
		if (isset($referred_user[0]))
			$referred_user = $referred_user[0];
	}
	if ($referred_user)
	{
		if (subs_get_info_by_user_id($referred_user["user_id"]))
			die ("Referral is already a student of Jigsaw Academy");
	}
*/
	// Check referral activity for the student
	$res_act = db_query("SELECT * FROM system_activity WHERE act_type='jlc.referral' AND activity='referral' AND context_type=".db_sanitize($user_src)." AND context_id=".$user["user_id"]." ORDER BY act_id DESC;");

	$content = array();
	if (isset($res_act[0]))
		$content = json_decode($res_act[0]["content"], true)["r"];

	$date = new DateTime();
	$validity = new DateTime();
	$validity->add(new DateInterval("P30D"));

	// Create a coupon code and send it to WP
	$coupon;
	$response = false;
	$retries = 0;
	while ((isset($response["error"]) || !$response || is_null($response)) && $retries < 3)
	{
		$coupon = substr($user["name"], 0, 4).chr(rand(65, 90)).rand(10, 100)."5";
		$data = array(
					"dev" => "suyog",
					"key" => "himanshu",
					"coupon_type" => "%",
					"coupon_code" => $coupon,
					"validity" => $validity->format("Ymd"),
					"value" => "5",
					"number" => 1
				);

		$opts = array('http' => array(
				'method'  => 'POST',
				'header'  => 'Content-type: application/x-www-form-urlencoded',
				'content' => http_build_query($data)
			)
		);
		$context  = stream_context_create($opts);
		$response = json_decode(file_get_contents("https://www.jigsawacademy.com/coupon-api", false, $context), true);
		$retries++;
	}

	// The entities that are being referred will be storred in a string of the format:
	// <b/c><course/bundle_id>[;<b/c><course/bundle_id>]
	$content[] = array("e" => $_POST["referral"]["email"], "n" => $_POST["referral"]["name"], "p" => $_POST["referral"]["phone"], "m" => $_POST["referral"]["msg"], "c" => explode(";", $_POST["referral"]["consult"]), "d" => $date->format("Y-m-d H:i:s"), "cc" => $coupon);

	$content = array("r" => $content, "n" => ((count($content) % 5) == 0 ? "1" : "0"));

	// Insert the referral into the database
	if (!isset($res_act[0]))
		activity_create("ignore", "jlc.referral", "referral", "", "", $user_src, $user["user_id"], json_encode($content));
	else
		db_exec("UPDATE system_activity SET content=".db_sanitize(json_encode($content))." WHERE act_id=".$res_act[0]["act_id"]);

	// Add record in leads_basic_capture_compiled for the referred
	//leads_basic_capture_compile(array("email" => $_POST["referral"]["email"], "phone" => $_POST["referral"]["phone"], "name" => $_POST["referral"]["name"], "event" => "referral"));

	$consults = explode(";", $_POST["referral"]["consult"]);
	$courses_str = array();
	$courses = array();
	$ref_str = array();
	foreach ($consults as $consult)
	{
		if (substr($consult, 0, 1) == "c")
		{
			$course = db_query("SELECT course.name, meta.content, meta.desc FROM course INNER JOIN course_meta AS meta ON course.course_id = meta.course_id WHERE course.course_id=".substr($consult, 1))[0];
			$meta_content = json_decode($course["content"], true);
			$courses_str[] = "<span><a href='".JAWS_PATH_WEB."/redir?ru=".urlencode($meta_content["url_web"])."&lp=referral_email&email=".urlencode($_POST["referral"]["email"])."&name=".urlencode($_POST["referral"]["name"])."&phone=".urlencode($_POST["referral"]["phone"])."&source=".urlencode($user["email"])."&medium=".urlencode($course["name"])."&campaign=".urlencode("referrer_email=".$user["email"]."&course=".$course["name"])."'>".$course["name"]."</a></span>";
			$ref_str[] = $course["name"];
			$courses[] = array(
						"url" => JAWS_PATH_WEB."/redir?ru=".urlencode($meta_content["url_web"])."&lp=referral_email&email=".urlencode($_POST["referral"]["email"])."&name=".urlencode($_POST["referral"]["name"])."&phone=".urlencode($_POST["referral"]["phone"])."&source=".urlencode($user["email"])."&medium=".urlencode($course["name"]),
						"img" => $meta_content["img_main_small"],
						"name" => $course["name"],
						"desc" => $course["desc"]);
		}
		else
		{
			$bundle = db_query("SELECT bundle.name, meta.content, meta.desc FROM course_bundle AS bundle INNER JOIN course_bundle_meta AS meta ON bundle.bundle_id = meta.bundle_id WHERE bundle.bundle_id=".substr($consult, 1))[0];
			$meta_content = json_decode($bundle["content"], true);
			$courses_str[] = "<span><a href='".JAWS_PATH_WEB."/redir?ru=".urlencode($meta_content["url_web"])."&lp=referral_email&email=".urlencode($_POST["referral"]["email"])."&name=".urlencode($_POST["referral"]["name"])."&phone=".urlencode($_POST["referral"]["phone"])."&source=".urlencode($user["email"])."&medium=".urlencode($bundle["name"])."&campaign=".urlencode("referrer_email=".$user["email"]."&bundle=".$bundle["name"])."'>".$bundle["name"]."</a></span>";
			$ref_str[] = $bundle["name"];
			$courses[] = array("url" => JAWS_PATH_WEB."/redir?ru=".urlencode($meta_content["url_web"])."&lp=referral_email&email=".urlencode($_POST["referral"]["email"])."&name=".urlencode($_POST["referral"]["name"])."&phone=".urlencode($_POST["referral"]["phone"])."&utm_source=".urlencode($user["email"])."&utm_medium=".urlencode($bundle["name"])."&campaign=".urlencode("referrer_email=".$user["email"]."&bundle=".$bundle["name"]), "img" => $meta_content["img_main_small"], "name" => $bundle["name"], "desc" => $bundle["desc"]);
		}
	}

	$ref_str = implode(", ", $ref_str);

	$content["courses_str"] = implode(" and ", $courses_str);
	$content["referred"]["name"] = $_POST["referral"]["name"];
	$content["coupon_code"] = $coupon;
	$content["referrer"]["name"] = $user["name"];
	$content["referrer"]["fname"] = get_fname($user["name"]);
	//$content["referred"]["fname"] = get_fname($_POST["referral"]["name"]);
	$content["courses"] = $courses;
	$content["courses_count"] = count($consults);

	send_email("referral.new", array("to" => (($GLOBALS["jaws_exec_live"]) ? $_POST["referral"]["email"] : "himanshu@jigsawacademy.com"), "subject" => $user["name"]." wants to boost your career"), $content);

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
	$headers = array();
	$subject = "Referral invite sent by ".$user["email"];
	$to = ($GLOBALS["jaws_exec_live"] ? "leads@jigsawacademy.com" : "himanshu@jigsawacademy.com");
	$body_leads = "<p>Hi,</p>
			<p>Glad to tell you that our student ".$user["name"].", ".$user["email"]." has referred his/her friend ".$_POST["referral"]["name"].", ".$_POST["referral"]["email"].", ".$_POST["referral"]["phone"]." and has recommended course(s) ".$ref_str.".</p>
			<p>You can contact the referred person to consult and help select the right set of courses.</p>

			<p>Thank you,</p>
			<p>JAWS Referral Feature</p><br />";

	$headers[] = 'MIME-Version: 1.0';
	$headers[] = 'Content-type: text/html; charset=iso-8859-1';
	$headers[] = "From: info@jigsawacademy.com";
	$headers[] = "Bcc: jagruti@jigsawacademy.com";

	mail($to, $subject, $body_leads, implode("\r\n", $headers));

	// Expire the token and exit
	psk_expire($user_src, $user["user_id"], "jlc.referral");
	die(json_encode(array("token" => psk_generate($user_src, $user["user_id"], "jlc.referral"), "response" => array("name" => $_POST["referral"]["name"], "email" => $_POST["referral"]["email"], "date" => $date->format("d-M Y"), "coupon" => $coupon, "claim" => "30 days left | Waiting for action", "ref" => $ref_str, "n" => $content["n"]))));

?>