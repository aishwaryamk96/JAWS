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

	function get_fname($name) {

		$tokens = explode(" ", $name);
		$i = 0;
		while ($i < count($tokens)) {

			if (strlen($tokens[$i]) > 2 && ctype_alpha($tokens[$i])) {
				break;
			}
			$i++;

		}
		if ($i == count($tokens)) {
			$i = 0;
		}
		return $tokens[$i];

	}

	// Check the system_activity for a record for this user and check the status.
	// If it is pending, no new records for now; if it is executed, create new records.
	// If the record is not present, create a new record.
	// This API checks if the email being referred is already available with JAWS or is a new one
	load_module("user");
	load_module("subs");
	load_module("leads");
	load_module("refer");

	load_library("email");

	// Exit if even one of the required POST parameters is not present
	if (!isset($_POST["token"]) || !isset($_POST["referral"]) || !isset($_POST["referral"]["email"]) || !isset($_POST["referral"]["name"]) || !isset($_POST["referral"]["consult"]) || !isset($_POST["referral"]["phone"])) {
		die("You do not have required priviledges to use this feature.");
	}

	$_POST["referral"]["name"] = trim($_POST["referral"]["name"]);
	$_POST["referral"]["email"] = trim($_POST["referral"]["email"]);
	$_POST["referral"]["phone"] = trim($_POST["referral"]["phone"]);
	$_POST["referral"]["consult"] = trim($_POST["referral"]["consult"]);

	$idm_only = ((!empty($_POST["idm_only"]) && $_POST["idm_only"] == "1") ? true : false);

	$privileged_user = (!empty($_POST["privileged_user"]) && $_POST["privileged_user"] == "1" ? true : false);

	// Authenticate the token here...
	$psk_info = psk_info_get($_POST["token"]);
	$user_src = $psk_info["entity_type"];
	$user;

	// Get the referer
	if ($user_src == "user") {
		$user = user_get_by_id($psk_info["entity_id"]);
	}
	else {

		$res_act = db_query("SELECT * FROM system_activity WHERE act_id=".$psk_info["entity_id"]);
		$user = json_decode($res_act[0]["content"], true);
		$user["user_id"] = $res_act[0]["act_id"];

	}

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

	$content["referred"]["name"] = $_POST["referral"]["name"];
	$content["coupon_code"] = $coupon;
	$content["referrer"]["name"] = $user["name"];
	$content["referrer"]["fname"] = get_fname($user["name"]);

	$GLOBALS['jaws_exec_test_email_to'] = "himanshu@jigsawacademy.com";

	$courses_ref = array();
	$bundles_ref = array();

	$pgpdm = false;

	if (!$idm_only) {
		$consults = explode(";", $_POST["referral"]["consult"]);
		foreach ($consults as $consult) {

			if (substr($consult, 0, 1) == "c") {
				$courses_ref[] = substr($consult, 1);
			}
			else {
				$bundles_ref[] = substr($consult, 1);
			}

		}

		$courses_str = array();
		$courses = array();
		$ref_str = array();
		if (count($courses_ref) > 0) {

			foreach ($courses_ref as $course_id) {

				$course = db_query("SELECT course.name, meta.content, meta.desc, meta.category FROM course INNER JOIN course_meta AS meta ON course.course_id = meta.course_id WHERE course.course_id=".$course_id)[0];
				$meta_content = json_decode($course["content"], true);
				$courses_str[] = "<span><a href='".JAWS_PATH_WEB."/redir?ru=".urlencode($meta_content["url_web"])."&lp=referral_email&email=".urlencode($_POST["referral"]["email"])."&name=".urlencode($_POST["referral"]["name"])."&phone=".urlencode($_POST["referral"]["phone"])."&utm_source=".urlencode($user["email"])."&utm_medium=".urlencode($course["name"])."&campaign=".urlencode("referrer_email=".$user["email"]."&course=".$course["name"])."'>".$course["name"]."</a></span>";
				$ref_str[] = $course["name"];
				$courses[] = array(
							"url" => JAWS_PATH_WEB."/redir?ru=".urlencode($meta_content["url_web"])."&lp=referral_email&email=".urlencode($_POST["referral"]["email"])."&name=".urlencode($_POST["referral"]["name"])."&phone=".urlencode($_POST["referral"]["phone"])."&utm_source=".urlencode($user["email"])."&utm_medium=".urlencode($course["name"]),
							"img" => $meta_content["img_main_small"],
							"name" => $course["name"],
							"desc" => $course["desc"]);

			}

		}
		if (count($bundles_ref) > 0) {

			foreach($bundles_ref as $bundle_id) {

				$bundle = db_query("SELECT bundle.name, meta.content, meta.desc, meta.category FROM course_bundle AS bundle INNER JOIN course_bundle_meta AS meta ON bundle.bundle_id = meta.bundle_id WHERE bundle.bundle_id=".$bundle_id)[0];
				$meta_content = json_decode($bundle["content"], true);
				$courses_str[] = "<span><a href='".JAWS_PATH_WEB."/redir?ru=".urlencode($meta_content["url_web"])."&lp=referral_email&email=".urlencode($_POST["referral"]["email"])."&name=".urlencode($_POST["referral"]["name"])."&phone=".urlencode($_POST["referral"]["phone"])."&utm_source=".urlencode($user["email"])."&utm_medium=".urlencode($bundle["name"])."&campaign=".urlencode("referrer_email=".$user["email"]."&bundle=".$bundle["name"])."'>".$bundle["name"]."</a></span>";
				$ref_str[] = $bundle["name"];
				$courses[] = array("url" => JAWS_PATH_WEB."/redir?ru=".urlencode($meta_content["url_web"])."&lp=referral_email&email=".urlencode($_POST["referral"]["email"])."&name=".urlencode($_POST["referral"]["name"])."&phone=".urlencode($_POST["referral"]["phone"])."&utm_source=".urlencode($user["email"])."&utm_medium=".urlencode($bundle["name"])."&campaign=".urlencode("referrer_email=".$user["email"]."&bundle=".$bundle["name"]), "img" => $meta_content["img_main_small"], "name" => $bundle["name"], "desc" => $bundle["desc"]);

				if ($bundle_id == 75) {
					$pgpdm = true;
				}

			}

		}

		$ref_str = implode(", ", $ref_str);

		$content["courses_str"] = implode(" and ", $courses_str);
		$content["courses"] = $courses;
		$content["courses_count"] = count($consults);

		$content["pgpdm"] = $pgpdm;

		send_email("referral.new", array("to" => (($GLOBALS["jaws_exec_live"]) ? $_POST["referral"]["email"] : "himanshu@jigsawacademy.com"), "subject" => $user["name"]." wants to boost your career"), $content);
	}
	else {
		$courses_ref = [];
		$bundles_ref[] = db_query("SELECT bundle_id FROM course_bundle_meta WHERE category='idm';")[0]["bundle_id"];
		$ref_str = "Integrated Program In Data Science & Machine Learning (IDM)";

		send_email_with_attachment("referral.new.idm", array("to" => $_POST["referral"]["email"], "subject" => $user["name"]." wants to boost your career"), $content, ["media/misc/attachments/refer/IDM_Schedule.pdf", "media/misc/attachments/refer/UC_brochure.pdf"]);
	}
	//$content["referred"]["fname"] = get_fname($_POST["referral"]["name"]);

	// insert details in refer table
	//refer_create($referrer_type, $referrer_id, $email, $name, $phone, $coupon_code, $courses = null, $course_bundles = null);
	$last_insert_id = refer_create($user_src, $user["user_id"], $_POST["referral"]["email"], $_POST["referral"]["name"], $_POST["referral"]["phone"], $coupon,  implode(";", $courses_ref), implode(";", $bundles_ref));

	// Send an email to leads email ID also
	// $headers = array();
	// $subject = "Referral invite sent by ".$user["email"];
	// $to = ($GLOBALS["jaws_exec_live"] ? ($pgpdm ? "pgpdm@jigsawacademy.com" : "leads@jigsawacademy.com") : "himanshu@jigsawacademy.com");
	// $body_leads = "<p>Hi,</p>
	// 		<p>Glad to tell you that our student ".$user["name"].", ".$user["email"]." has referred his/her friend ".$_POST["referral"]["name"].", ".$_POST["referral"]["email"].", ".$_POST["referral"]["phone"]." and has recommended course(s) ".$ref_str.".</p>
	// 		<p>You can contact the referred person to consult and help select the right set of courses.</p>

	// 		<p>Thank you,</p>
	// 		<p>JAWS Referral Feature</p><br />";

	// $headers[] = 'MIME-Version: 1.0';
	// $headers[] = 'Content-type: text/html; charset=iso-8859-1';
	// $headers[] = "From: info@jigsawacademy.com";
	// if ($idm_only) {
	// 	$headers[] = "Cc: kamal@jigsawacademy.com";
	// }
	// $headers[] = "Bcc: jagruti@jigsawacademy.com, himanshu@jigsawacademy.com";

	// mail($to, $subject, $body_leads, implode("\r\n", $headers));

	$options = ["subject" => "Referral invite sent by ".$user["email"]];
	if ($idm_only) {
		$options["to"] = "kamal@jigsawacademy.com";
	}
	elseif ($pgpdm) {
		$options["to"] = "pgpdm@jigsawacademy.com";
	}

	$content_internal = [
		"name" => $user["name"],
		"email" => $user["email"],
		"refer" => [
			"email" => $$_POST["refer"]["email"],
			"name" => $$_POST["refer"]["name"],
			"phone" => $$_POST["refer"]["phone"],
			"refered" => $ref_str
		],
		"source" => "JLC"
	];

	send_email("referral.new.internal", $options, $content_internal);
	// mail($to, $subject, $body_leads, implode("\r\n", $headers));

	// Expire the token and exit
	psk_expire($user_src, $user["user_id"], "jlc.referral");
	die(json_encode(array("token" => psk_generate($user_src, $user["user_id"], "jlc.referral"), "response" => array("name" => $_POST["referral"]["name"], "email" => $_POST["referral"]["email"], "date" => $date->format("d-M Y"), "coupon" => $coupon, "claim" => "Waiting for action", "ref" => $ref_str, "n" => $content["n"]))));

?>