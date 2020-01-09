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

	if (!isset($_GET["token"]) || !isset($_GET["email"]) || !isset($_GET["referral_user_id"]))
		die ("You do not have required priviledges to use this feature.");

	load_module("user");
	load_module("subs");
	$user;
	$user_src;

	// Authenticate the token here...
	$psk = psk_info_get($_GET["token"]);
	$user_src = $psk["entity_type"];

	// Get the referer
	if ($user_src == "user")
		$user = user_get_by_id($psk["entity_id"]);
	else
	{
		$res_act = db_query("SELECT * FROM system_activity WHERE act_id=".$psk["entity_id"]);
		$user = json_decode($res_act[0]["content"], true);
		$user["user_id"] = $psk["entity_id"];
	}

	// Set the referral record to completed
	$res_act = db_query("SELECT * FROM system_activity WHERE act_type='jlc.referral' AND activity='referral' AND context_type=".db_sanitize($user_src)." AND context_id=".$user["user_id"]." ORDER BY act_id DESC;");

	$referral_user;
	$referral_date;
	$courses_str = array();

	foreach ($res_act as $activity)
	{
		$content = json_decode($activity["content"], true);
		$content_new = array();
		$write_back = false;
		foreach ($content["r"] as $referral)
		{
			$ref = user_get_by_id($_GET["referral_user_id"]);
			$referral["e"] = strtolower($referral["e"]);
			if ($ref["email"] == $referral["e"] || $ref["soc_fb"] == $referral["e"] || $ref["soc_gp"] == $referral["e"] || $ref["soc_li"] == $referral["e"])
			{
				$referral["x"] = "1";
				$write_back = true;
				$referral_user = $ref;
			}

			$content_new[] = $referral;
		}

		if ($write_back)
			db_exec("UPDATE system_activity SET content=".db_sanitize(json_encode(array("r" => $content_new, "n" => $content["n"])))." WHERE act_id=".$activity["act_id"]);
	}

	psk_expire($user_src, $user["user_id"], "jlc.referral.claim");

	$subs = db_query("SELECT * FROM subs WHERE status='active' AND user_id=".$referral_user["user_id"]." ORDER BY subs_id ASC LIMIT 1;");
	$subs = $subs[0];

	if (isset($subs["bundle_id"]) && strlen($subs["bundle_id"]) > 0)
		$courses_str = db_query("SELECT name FROM course_bundle WHERE bundle_id=".$subs["meta"]["bundle_id"])[0]["name"];
	else
	{
		$courses = explode(";", trim($subs["combo"].";".$subs["combo_free"], ";"));
		$courses_str = array();
		foreach ($courses as $course)
			$courses_str[] = db_query("SELECT name FROM course WHERE course_id=".explode(",", $course)[0])[0]["name"];

		$courses_str = implode(", ", array_splice($courses_str, -1))." and ".$courses_str[-1];
	}
	$referral_date = date_create_from_format("Y-m-d H:i:s", $subs["start_date"]);

	// Send an email to concerned dept
	$subject = "Referral enrolled. Amazon voucher to be given to referrer ".$user["name"];
	$to = ($GLOBALS["jaws_exec_live"] ? "payments@jigsawacademy.com, ishita@jigsawacademy.com" : "himanshu@jigsawacademy.com");
	$body = "<p>Hi Finance Team,</p>
			<p>".$user["name"].", ".$user["email"]." is claiming for an Amazon voucher worth INR 1000.</p>
			<p>His/her friend ".$referral_user["name"].", ".$referral_user["email"]." has enrolled for ".$courses_str." on ".$referral_date->format("d M Y")."</p>
			<p>Both ".$user["name"]." and ".$referral_user["name"]." has paid the complete amount for their courses and the 7 days money back period has expired for them.</p>

			<p>We request you to do a final check from your side before proceeding to reward the voucher.​</p>

			<p>Thank you,</p>
			<p>JAWS Refer a friend feature</p>";

	$headers[] = 'MIME-Version: 1.0';
	$headers[] = 'Content-type: text/html; charset=iso-8859-1';
	$headers[] = "From: info@jigsawacademy.com";
	$headers[] = "Bcc: jagruti@jigsawacademy.com";

	mail($to, $subject, $body, implode("\r\n", $headers));

	die(json_encode(["success" => 1]));

?>