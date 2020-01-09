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

	// Test Mode !!!!!!!!!!!! <<< ================================================ REMOVE WHEN LIVE !!!!!!!!!!!! =====================================
    //$GLOBALS['jaws_exec_live'] = false;

	// Ignore User Abort & Output Buffering
	ignore_user_abort(true);
	ob_start();

	// Load stuff
	load_module("ui");
	load_module("user");
	load_module("course");
	load_module("user_enrollment");
	load_library('email');

	// Check Course
	if (!isset($_REQUEST['course'])) {
        ui_render_msg_front(array(
            "type" => "error",
			"title" => "A problem ran into you :(",
			"header" => "Oops !",
			"text" => "Sorry, but this link seems to be invalid!<br /><br />Please contact our support team for assistance. (Code: 1)",
			"footer" => ["phone" => "+91-90193-17000", 'website' => 'iot']
		));
		exit();
	}

	$course_info = db_query("SELECT c.*, m.category FROM course AS c INNER JOIN course_meta AS m ON m.course_id = c.course_id WHERE c.sp_code = ".db_sanitize("V".$_POST["course"])." ORDER BY c.course_id ASC;");
	if (empty($course_info)) {
		ui_render_msg_front(array(
            "type" => "error",
			"title" => "A problem ran into you :(",
			"header" => "Oops !",
			"text" => "Sorry, but this link seems to be invalid!<br /><br />Please contact our support team for assistance. (Code: 0)",
			"footer" => ["phone" => "+91-90193-17000", 'website' => 'iot']
		));
		exit();
	}

	$course_info = $course_info[0];
	if (empty($course_info["is_free"])) {
		ui_render_msg_front(array(
            "type" => "error",
			"title" => "A problem ran into you :(",
			"header" => "Oops !",
			"text" => "Sorry, but this link seems to be invalid!<br /><br />Please contact our support team for assistance. (Code: 0)",
			"footer" => ["phone" => "+91-90193-17000", 'website' => 'iot']
		));
		exit();
	}

	$course = $course_info["course_id"];
	$category = explode(";", $course_info["category"]);
	if (in_array("iot", $category)) {

		$category = "iot";
		$GLOBALS["content"]["footer"]["phone"] = "+91-90193-17000";

	}
	else {

		$category = "analytics";
		$GLOBALS["content"]["footer"]["phone"] = "+91-90192-17000";

	}

	$duration = $course_info["free_duration"];

	$user;
	$lms_auth = "soc";
	$lms_auth_value;
	$no_enrs = false;
	// Check Mode : Social
	if (!isset($_POST['corp'])) {

		$user = user_get_by_email($_REQUEST["email"]);
		if (!empty($user["lms_soc"])) {
			$lms_auth_value = $user["lms_soc"];
		}
		else {

			$lms_auth_value = $_REQUEST["soc"] ?? "";
			$no_enrs = true;

		}

	}
	// Mode : Corp
	else {

		if ((!isset($_POST['name'])) || (!isset($_POST['email'])) || (!isset($_POST['phone'])) || (!isset($_POST['password']))) {
			ui_render_msg_front(array(
				"type" => "error",
				"title" => "A problem ran into you :(",
				"header" => "Oops !",
				"text" => "Sorry, but this link seems to be invalid!<br /><br />Please contact our support team for assistance. (Code: 4)",
				"footer" => ["phone" => "+91-90193-17000", 'website' => 'iot']
			));
			exit();
		}

		// create lead with utm parameters if met with condition
		if( !empty($_POST["ldc"]) && $_POST["ldc"] == true ){

			$source = $_POST["source"] ?? "";
			if (!empty($source)) {
				$source .= "-".$_POST["course"];
			}

			// Insert into leads
			$query = "INSERT INTO
						user_leads_basic (
							name,
							email,
							phone,
							utm_source,
							utm_campaign,
							utm_term,
							utm_medium,
							utm_segment,
							utm_content,
							utm_numvisits,
							referer,
							ip,
							ad_lp,
							ad_url,
							create_date,
							capture_trigger,
							capture_type
						)
					VALUES
						(
							".db_sanitize($_POST['name']).",
							".db_sanitize($_POST['email']).",
							".db_sanitize($_POST['phone']).",
							".db_sanitize($source).",
							".db_sanitize($_POST['campaign'] ?? '').",
							".db_sanitize($_POST['term'] ?? '').",
							".db_sanitize($_POST['medium'] ?? '').",
							".db_sanitize($_POST['segment'] ?? '').",
							".db_sanitize($_POST['content'] ?? '').",
							".db_sanitize($_POST['numvisits'] ?? '').",
							".db_sanitize($_POST['referer'] ?? '').",
							".db_sanitize($_POST['ip'] ?? '').",
							".db_sanitize($_POST['lp'] ?? '').",
							".db_sanitize($_POST['url'] ?? '').",
							".db_sanitize(strval(date("Y-m-d H:i:s"))).",
							'formsubmit',
							'url'
						);";

			db_exec($query);
			// $lead_id = db_get_last_insert_id();
		}

		$user = user_get_by_email($_POST["email"]);
		if (empty($user)) {

			$user = user_create($_POST["email"], substr(str_shuffle($name.str_replace("@", "0", str_replace(".", "", $_POST["email"]))), 0, 10), $name, $_POST["phone"] ?? "", true);
			$no_enrs = true;
		}

		$lms_auth = "pass";
		$lms_auth_value = $_POST["password"];

	}

	if ($lms_auth == "soc" && empty($lms_auth_value)) {

		if (!empty($user["soc_gp"])) {
			$lms_auth_value = $user["soc_gp"];
		}
		else if (!empty($user["soc_fb"])) {
			$lms_auth_value = $user["soc_fb"];
		}
		else if (!empty($user["soc_li"])) {
			$lms_auth_value = $user["soc_li"];
		}

	}

	if (!$no_enrs) {

		$old_enr = db_query("SELECT * FROM user_enrollment WHERE course_id = $course AND user_id = ".$user["user_id"].";");
		if (!empty($old_enr)) {

			$mode = "corp";
			$provider = "";
			$email = $user["email"];
			$password = $old_enr[0]["lms_pass"];

			if ($user["lms_soc"] != "corp") {

				$mode = "corp";
				$provider = $user["lms_soc"];
				$email = $user["soc_".$user["lms_soc"]];
				$password = "";

			}

			send_email_with_attachment(
				'lms.free.setup.success',
				['to' => $email],
				[
					'fname' => substr($user["name"], 0, ((strpos($user["name"], " ") !== false) ? strpos($user["name"], " ") : strlen($user["name"]))),
					'course' => [
						'name' => $course_info['name'],
						'category' => $category
					],
					'access' => [
						'duration' => $duration,
						'end_date' => date('dS M Y', strtotime("+" . $duration . " days")),
						'account' => [
							'mode' => $mode,
							'provider' => (!empty($provider)) ? ($provider == 'fb' ? 'Facebook' : ($provider == 'gp' ? 'Google+' : 'LinkedIn')) : '',
							'username' => $email,
							'password' => $password ?? ''
						]
					]
				],
				["media/misc/attachments/Terms_and_Conditions.pdf"]
			);

			ui_render_msg_front(array(
				"type" => "info",
				"title" => "Already Signed Up",
				"header" => "Free Learning",
				"text" => "Hey ".substr($user["name"], 0, ((strpos($user["name"], " ") !== false) ? strpos($user["name"], " ") : strlen($user["name"]))).",<br/>It seems you are already registered with us.<br/><br/>Kindly visit <a href='https://freelearning.jigsawacademy.net'><b style='text-decoration: none!important;'>www.jigsawAcademy.net</b></a> and use the login details we sent to you by email.",
				"footer" => ["phone" => "+91-90193-17000", 'website' => 'iot']
			));

			exit();

		}

	}

	// Build subs and call subscribe
	$subs = [
		"combo" => $course.",2",
		"combo_free" => "",
		"status" => "pending"
	];
	$pay_info = [
		"status" => "paid",
		"currency" => "inr",
		"sum_basic" => 0,
		"sum_total" => 0,
		"instl_total" => 1,
		"instl" => [
			"instl_count" => 1,
			"instl_total" => 1,
			"sum" => 0,
			"due_days" => 0
		],
		"receipt_type" => "retail",
	];

	$subscription = subscribe($user["email"], $subs_info, $pay_info, false, false, "", true);
	$psk = $subscription["subs_id"];
	enr_create($subscription["subs_id"]);

	ob_start();
	include __DIR__."/index.php";
	$ob_length = ob_get_length();
	apache_setenv('no-gzip', 1);
	// Send Headers
	header("Content-Type: text/html;");
	header("Content-Length: ".$ob_length.";");
	header($_SERVER["SERVER_PROTOCOL"] . " 200 Accepted;");
	header("Status: 200 Accepted;");
	header("Connection: close;");

	// Flush Buffer
	ob_end_flush();
	ob_flush();
	flush();
	ob_end_clean();

	// Close & Destroy Session
	session_write_close();
	// session_destroy();

	sis_import();

?>