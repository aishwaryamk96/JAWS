<?php

	load_plugin("mobile_app");
	header("Content-type: application/json");

	$mobile = new MobileApp;

	$headers = getallheaders();
	if (!$mobile->authorizeRequest($headers["Authorization"])) {

		header("HTTP/1.1 401 Unauthorized");
		die();

	}

	load_module("user");
	load_module("refer");
	load_library("email");

	$user = user_get_by_email($_POST["email"]);

	if (empty($_POST["refer"]["email"])) {

		$courses_list= array();
		$bundles = get_all_bundles("programs", "bootcamps", "full_stacks", "specializations");
		// $bundles =
		foreach ($bundles as $bundle) {

			$content = json_decode($bundle["content"], true);
			if (!empty($content["url_web"])) {
				$courses_list[] = array("id" => "b".$bundle["bundle_id"], "name" => $bundle["name"], "url" => $content["url_web"]);
			}

		}
		$courses = db_query("SELECT course.course_id, course.name, meta.content FROM course INNER JOIN course_meta AS meta ON course.course_id = meta.course_id WHERE course.sis_id IS NOT NULL AND course.sellable = 1 AND course.status='enabled'".($idm_only ? " AND meta.category='idm'" : "").";");
		foreach ($courses as $course) {

			$content = json_decode($course["content"], true);
			$courses_list[] = array("id" => "c".$course["course_id"], "name" => $course["name"], "url" => $content["url_web"]);

		}

		$nope = 1;

		$date = new DateTime("now");

		$claimable = $user["user_id"] == 18 ? true : validate_payment_for_claim($user["user_id"]);

		$num_invite_expired = 0;
		$num_no_action = 0;
		$num_registerred = 0;
		$num_enrolled = 0;
		$num_claimable = 0;
		$num_claimed = 0;
		$num_awaiting_approval = 0;
		$num_awarded = 0;

		$result = [];
		$referrals_total = 0;
		$referrals = refer_get_by_referrer("user", $user["user_id"]);
		if (isset($referrals) && !empty($referrals)) {
			$referrals_total = count($referrals);
		}

		if ($referrals !== false) {

			foreach ($referrals as $referral) {

				$write_back = false;

				if ($referral["status"] == "no_action" || $referral["status"] == "registered" || $referral["status"] == "enrolled") {

					$ref_user = user_get_by_email($referral["email"]);
					if ($ref_user !== false) {

						if ($referral["status"] == "no_action") {

							$referral["status"] = "registered";
							$write_back = true;

						}

						$ref_enr = db_query("SELECT * FROM subs WHERE (status='active' OR status='pending') AND user_id=".$ref_user["user_id"]." ORDER BY start_date ASC;");
						if (isset($ref_enr[0])) {

							if ($referral["status"] == "registered") {

								$referral["status"] = "enrolled";
								$write_back = true;

							}
							$ref_enr = $ref_enr[0];
							$enr_date = date_create_from_format("Y-m-d H:i:s", $ref_enr["start_date"]);
							$referral["enr_date"] = $enr_date;

							//$referral["claimable"] = $claimable;
							if ($claimable && validate_payment_for_claim($ref_user["user_id"])) {

								$referral["status"] = "claim_reward";
								$num_claimable++;
								$referral["claimable"] = true;
								$write_back = true;

							}
							$num_enrolled++;

						}
						else {
							$num_registerred++;
						}

					}
					else {

						$create_date = date_create_from_format("Y-m-d H:i:s", $referral["create_date"]);
						if ($date > $create_date->add(new DateInterval("P30D"))) {

							$referral["status"] = "invite_expired";
							$num_invite_expired++;
							$write_back = true;

						}
						else {

							$referral["resend_token"] = psk_generate("refer", $referral["id"], "jlc.refer.resend");
							$referral["days_left"] = $create_date->diff($date)->format("%a");
							$num_no_action++;

						}

					}

				}
				else {

					if ($referral["status"] == "claimed") {

						$num_claimed++;
						$num_enrolled++;

					}
					else if ($referral["status"] == "awaiting_approval") {

						$num_awaiting_approval++;
						$num_enrolled++;

					}
					else if ($referral["status"] == "voucher_awarded") {

						$num_awarded++;
						$num_enrolled++;

					}
					else if ($referral["status"] == "claim_reward") {

						$num_claimable++;
						$referral["claimable"] = true;
						$num_enrolled++;

					}
					else if ($referral["status"] == "invite_expired") {
						$num_invite_expired++;
					}

				}

				$consult_arr = array();
				$bundles = str_replace(";", ",", $referral["course_bundles"]);
				if (strlen($bundles) > 0) {

					$bundle = db_query("SELECT name FROM course_bundle WHERE bundle_id IN (".$bundles.");");
					foreach ($bundle as $b) {
						$consult_arr[] = $b['name'];
					}

				}

				$courses = str_replace(";", ",", $referral["courses"]);
				if (strlen($courses) > 0) {

					$courses = db_query("SELECT name FROM course WHERE course_id IN (".$courses.");");
					foreach ($courses as $c) {
						$consult_arr[] = $c['name'];
					}

				}

				$referral["consults"] = implode(", ", $consult_arr);

				$result[] = $referral;
				if ($write_back) {
					refer_edit($referral);
				}

			}

		}

		die(json_encode(["refers" => $result, "courses" => $courses_list, "total" => $referrals_total, "invite_expired" => $num_invite_expired, "no_action" => $num_no_action, "registerred" => $num_registerred, "enrolled" => $num_enrolled, "claimable" => $num_claimable, "claimed" => $num_claimed, "awaiting_approval" => $num_awaiting_approval, "awarded" => $num_awarded]));

	}
	else {

		$date = new DateTime();
		$validity = new DateTime();
		$validity->add(new DateInterval("P30D"));

		if (refer_get_by_email($_POST["refer"]["email"]) !== false) {
			die(json_encode(["status" => false, "error" => "User has already been referred"]));
		}
		else {

			$user_ref = user_get_by_email($_POST["refer"]["email"]);
			if (!empty((db_query("SELECT * FROM subs WHERE user_id = ".$user_ref["user_id"])))) {
				die(json_encode(["status" => false, "error" => "User has already enrolled with us"]));
			}

		}

		// Create a coupon code and send it to WP
		$coupon;
		$response = false;
		$retries = 0;
		while ((isset($response["error"]) || !$response || is_null($response)) && $retries < 3) {

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
			$response = /*json_decode(file_get_contents("https://www.jigsawacademy.com/coupon-api", false, $context), true);*/["blah" => true];
			$retries++;

		}

		$content["referred"]["name"] = $_POST["refer"]["name"];
		$content["coupon_code"] = $coupon;
		$content["referrer"]["name"] = $user["name"];
		$content["referrer"]["fname"] = get_fname($user["name"]);

		$GLOBALS['jaws_exec_test_email_to'] = "himanshu@jigsawacademy.com";

		$courses_ref = array();
		$bundles_ref = array();

		$idm_only = false;
		$pgpdm = false;

		if (!$idm_only) {

			$consults = explode(";", $_POST["refer"]["consult"]);
			foreach ($consults as $consult) {

				if (substr($consult, 0, 1) == "c") {

					$id = substr($consult, 1);
					if (!is_numeric($id)) {
						die("Invalid consult ".$consult);
					}
					$courses_ref[] = $id;

				}
				else if (substr($consult, 0, 1) == "b") {

					$id = substr($consult, 1);
					if (!is_numeric($id)) {
						die("Invalid consult ".$consult);
					}
					$bundles_ref[] = $id;

				}
				else {
					die("Invalid consult ".$consult);
				}

			}

			$courses_str = array();
			$courses = array();
			$ref_str = array();
			if (count($courses_ref) > 0) {

				foreach ($courses_ref as $course_id) {

					$course = db_query("SELECT course.name, meta.content, meta.desc, meta.category FROM course INNER JOIN course_meta AS meta ON course.course_id = meta.course_id WHERE course.course_id=".$course_id)[0];
					$meta_content = json_decode($course["content"], true);
					$courses_str[] = "<span><a href='".JAWS_PATH_WEB."/redir?ru=".urlencode($meta_content["url_web"])."&lp=referral_email&email=".urlencode($_POST["refer"]["email"])."&name=".urlencode($_POST["refer"]["name"])."&phone=".urlencode($_POST["refer"]["phone"])."&utm_source=".urlencode($user["email"])."&utm_medium=".urlencode($course["name"])."&utm_campaign=referrer_email&utm_term=".urlencode($user["email"])."&course=".urlencode($course["name"])."'>".$course["name"]."</a></span>";
					$ref_str[] = $course["name"];
					$courses[] = array(
								"url" => JAWS_PATH_WEB."/redir?ru=".urlencode($meta_content["url_web"])."&lp=referral_email&email=".urlencode($_POST["refer"]["email"])."&name=".urlencode($_POST["refer"]["name"])."&phone=".urlencode($_POST["refer"]["phone"])."&utm_source=".urlencode($user["email"])."&utm_medium=".urlencode($course["name"]),
								"img" => $meta_content["img_main_small"],
								"name" => $course["name"],
								"desc" => $course["desc"]);

				}

			}
			if (count($bundles_ref) > 0) {

				foreach($bundles_ref as $bundle_id) {

					$bundle = db_query("SELECT bundle.name, meta.content, meta.desc, meta.category FROM course_bundle AS bundle INNER JOIN course_bundle_meta AS meta ON bundle.bundle_id = meta.bundle_id WHERE bundle.bundle_id=".$bundle_id)[0];
					$meta_content = json_decode($bundle["content"], true);
					$courses_str[] = "<span><a href='".JAWS_PATH_WEB."/redir?ru=".urlencode($meta_content["url_web"])."&lp=referral_email&email=".urlencode($_POST["refer"]["email"])."&name=".urlencode($_POST["refer"]["name"])."&phone=".urlencode($_POST["refer"]["phone"])."&utm_source=".urlencode($user["email"])."&utm_medium=".urlencode($bundle["name"])."&utm_campaign=referrer_email&utm_term=".urlencode($user["email"])."&bundle=".urlencode($bundle["name"])."'>".$bundle["name"]."</a></span>";
					$ref_str[] = $bundle["name"];
					$courses[] = [
						"url" => JAWS_PATH_WEB."/redir?ru=".urlencode($meta_content["url_web"]).
									"&lp=referral_email".
									"&email=".urlencode($_POST["refer"]["email"]).
									"&name=".urlencode($_POST["refer"]["name"]).
									"&phone=".urlencode($_POST["refer"]["phone"]).
									"&utm_source=".urlencode($user["email"]).
									"&utm_medium=".urlencode($bundle["name"]).
									"&utm_campaign=referrer_email".
									"&utm_term=".urlencode($user["email"]).
									"&bundle=".urlencode($bundle["name"]),
						"img" => $meta_content["img_main_small"],
						"name" => $bundle["name"],
						"desc" => $bundle["desc"]
					];

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

			send_email("referral.new", array("to" => (($GLOBALS["jaws_exec_live"]) ? $_POST["refer"]["email"] : "himanshu@jigsawacademy.com"), "subject" => $user["name"]." wants to boost your career"), $content);
		}
		else {
			$courses_ref = [];
			$bundles_ref[] = db_query("SELECT bundle_id FROM course_bundle_meta WHERE category='idm';")[0]["bundle_id"];
			$ref_str = "Integrated Program In Data Science & Machine Learning (IDM)";

			send_email_with_attachment("referral.new.idm", array("to" => $_POST["refer"]["email"], "subject" => $user["name"]." wants to boost your career"), $content, ["media/misc/attachments/refer/IDM_Schedule.pdf", "media/misc/attachments/refer/UC_brochure.pdf"]);
		}
		//$content["referred"]["fname"] = get_fname($_POST["referral"]["name"]);

		// insert details in refer table
		//refer_create($referrer_type, $referrer_id, $email, $name, $phone, $coupon_code, $courses = null, $course_bundles = null);
		$last_insert_id = refer_create("user", $user["user_id"], $_POST["refer"]["email"], $_POST["refer"]["name"], $_POST["refer"]["phone"], $coupon,  implode(";", $courses_ref), implode(";", $bundles_ref));

		// Send an email to leads email ID also
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
			"source" => "mobile app"
		];

		send_email("referral.new.internal", $options, $content_internal);
		// mail($to, $subject, $body_leads, implode("\r\n", $headers));

		die(json_encode(["status" => true, "coupon" => $coupon]));

	}

	function get_all_bundles() {

		$order = func_get_args();
		if (empty($order)) {
			return db_query("SELECT bundle.bundle_id, bundle.name, meta.content FROM course_bundle AS bundle INNER JOIN course_bundle_meta AS meta ON bundle.bundle_id = meta.bundle_id WHERE bundle.status='enabled' AND bundle.bundle_type='specialization'".($idm_only ? " AND meta.category='idm'" : "").";");
		}

		$bundles = [];
		foreach ($order as $type) {

			$category = null;
			if ($type == "full_stacks") {

				$type = "specialization";
				$category = "'%full-stack%'";

			}

			$res = db_query("SELECT bundle.bundle_id, bundle.name, meta.content FROM course_bundle AS bundle INNER JOIN course_bundle_meta AS meta ON bundle.bundle_id = meta.bundle_id WHERE bundle.status='enabled' AND bundle.bundle_type='$type'".($category ? " AND meta.category LIKE $category" : "").";");
			if (!empty($res)){
				$bundles = array_merge($bundles, $res);
			}

		}

		return $bundles;

	}

	function validate_payment_for_claim($user_id) {

		$date = new DateTime("now");
		$paid_flag = false;

		$pay_info = payment_get_info_by_user_id($user_id);
		if ($pay_info === false) {
			return true;
		}

		foreach ($pay_info as $payment) {

			if (strcmp($payment["status"], "paid") != 0) {
				$paid_flag = $paid_flag || false;
			}
			else {

				foreach ($payment["instl"] as $instl) {

					if (strcmp($instl["status"], "paid") != 0) {

						$paid_flag = false;
						break 2;

					}
					else {
						$paid_flag = true;
					}

				}

			}

		}

		if ($paid_flag) {

			// Check if the referrer is still in the 7 days refund period; if yes, the user cannot claim the referral reward
			$subs = db_query("SELECT start_date FROM subs WHERE user_id=".$user_id." ORDER BY start_date DESC LIMIT 1;");
			$start_date = date_create_from_format("Y-m-d H:i:s", $subs[0]["start_date"])->add(new DateInterval("P7D"));
			if ($date < $start_date) {
				$paid_flag = false;
			}

		}

		return $paid_flag;

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

?>