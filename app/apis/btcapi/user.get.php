<?php

	// Prevent exclusive access
	if (!defined("JAWS")) {
		header("HTTP/1.1 401 Unauthorized");
		die();
	}

	if ($_SERVER["HTTP_HOST"] == "www.jigsawacademy.com") {
		// Init Session
		auth_session_init();
	}

	// Auth Check - Expecting Session Only !
	if (!auth_session_is_logged()){

		header("HTTP/1.1 401 Unauthorized");
		die();

	}

	if (!auth_session_is_allowed("batcave")) {

		header("HTTP/1.1 403");
		die;

	}

	// Send Headers
	header('Access-Control-Allow-Credentials: true');
	header("Content-Type: application/json");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Pragma-directive: no-cache");
	header("Cache-directive: no-cache");
	header("Cache-Control: private, must-revalidate, max-age=0, no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
	header("Pragma: no-cache");

	$res_user = db_query("SELECT
							user.user_id,
							user.web_id,
							user.name,
							user.email,
							user.email_2,
							user.phone,
							user.lms_soc,
							user.soc_fb,
							user.soc_gp,
							user.soc_li,
							user.photo_url,
							user.status,
							DATE_FORMAT(meta.reg_date, '%b %e, %Y') AS reg_date,
							DATE_FORMAT(meta.survey_date, '%b %e, %Y') AS survey_date,
							meta.city,
							meta.state,
							meta.country,
							meta.zipcode,
							meta.gender,
							meta.age,
							meta.qualification,
							meta.experience,
							meta.leads_media_src,
							meta.survey_data,
							enr.sis_id AS jig_id,
							enr.lms_pass
						FROM
							user
						LEFT JOIN
							user_meta AS meta ON meta.user_id = user.user_id
						LEFT JOIN
							user_enrollment AS enr ON enr.user_id = user.user_id
						WHERE
							user.user_id = ".$_GET["user"]."
						GROUP BY
							user.user_id;"
		);

	if (!isset($res_user[0])) {
		die(json_encode([]));
	}

	$res_user = $res_user[0];

	$res_user["city"] = ucfirst(strtolower($res_user["city"]));

	$lms_soc_status = empty($res_user["lms_soc"]);

	$course_codes = [];

	/* If the user can view enrollments, get them */
	if (/*auth_session_is_allowed("enrollment.get") || auth_session_is_allowed("enrollment.get.adv")*/true) {

		$res_user["frozen"] = false;

		$res_subs = db_query("SELECT
								subs.subs_id,
								subs.user_id,
								subs.pay_id,
								subs.package_id,
								IF (subs.status != 'inactive',
									IF (access.id IS NOT NULL,
										IF (MIN(DATE(access.start_date)) = CURDATE(), 'Today', DATE_FORMAT(MIN(access.start_date), '%e %b, %Y')),
										IF (DATE(subs.start_date) = CURDATE(), 'Today', DATE_FORMAT(subs.start_date, '%e %b, %Y'))
									),
									NULL
								) AS start_date,
								IF (subs.status != 'inactive', DATE_FORMAT(subs.start_date, '%e %b %Y'), NULL) AS start_date_orig,
								IF (subs.status != 'inactive',
									IF (access.id IS NOT NULL,
										IF (MAX(DATE(access.end_date)) = CURDATE(), 'Today', DATE_FORMAT(MAX(access.end_date), '%e %b, %Y')),
										IF (subs.end_date_ext IS NOT NULL,
											IF (DATE(subs.end_date_ext) = CURDATE(), 'Today', DATE_FORMAT(subs.end_date_ext, '%e %b, %Y')),
											IF (DATE(subs.end_date) = CURDATE(), 'Today', DATE_FORMAT(subs.end_date, '%e %b, %Y'))
										)
									),
									NULL
								) AS end_date,
								IF (subs.status != 'inactive', DATE_FORMAT(subs.end_date, '%e %b %Y'), NULL) AS end_date_orig,
								IF (subs.status != 'inactive',
									IF (access.id IS NOT NULL,
										CEIL(DATEDIFF(MAX(DATE(access.end_date)), MIN(DATE(access.start_date))) / 30),
										IF (subs.end_date_ext IS NOT NULL,
											CEIL(DATEDIFF(DATE(subs.end_date_ext), DATE(subs.start_date)) / 30),
											CEIL(DATEDIFF(DATE(subs.end_date), DATE(subs.start_date)) / 30))
										),
									NULL
								) AS duration,
								IF (subs.freeze_date IS NOT NULL, IF (DATE(subs.freeze_date) = CURDATE(), 'Today', DATE_FORMAT(subs.freeze_date, '%e %b, %Y')), NULL) AS freeze_date,
								IF (subs.unfreeze_date IS NOT NULL, IF (DATE(subs.unfreeze_date) = CURDATE(), 'Today', DATE_FORMAT(subs.unfreeze_date, '%e %b, %Y')), NULL) AS unfreeze_date,
								IF (DATE(subs.freeze_date) <= CURDATE() AND DATE(subs.unfreeze_date) > CURDATE(), true, false) AS freeze_on,
								CASE subs.status
									WHEN 'inactive' THEN -2
									WHEN 'pending' THEN 1
									WHEN 'active' THEN 5
									WHEN 'blocked' THEN 0
									WHEN 'frozen' THEN 3
									WHEN 'alumni' THEN 2
									WHEN 'expired' THEN -1
									WHEN 'refunded' THEN 4
								END AS status_str,
								GROUP_CONCAT(CONCAT(DATE_FORMAT(access.start_date, '%e %b, %Y'), '=', DATE_FORMAT(access.end_date, '%e %b, %Y'), '=', access.is_free) SEPARATOR '+') AS durations,
								subs.status,
								subs.combo,
								subs.combo_free,
                                                                subs.individual_course,
								IF (meta.bundle_id IS NOT NULL, meta.bundle_id, 0) AS bundle_id,
								IF (meta.bundle_id IS NULL OR meta.bundle_id = 0, 'Custom Combo', bundle.name) AS bundle,
								IF (bundle.bundle_type IS NOT NULL, bundle.bundle_type, 'custom') AS bundle_type,
								bb.id AS bootcamp_batch_id,
								bb.code AS bootcamp_batch_code,
								bb.meta AS bootcamp_batch_meta,
								bundle.platform_id,
								p.name AS platform_name,
								se.request AS export_request,
								se.http_code AS export_response_code,
								se.response AS export_response,
								se.created_at AS exported_at
							FROM
								subs
							LEFT JOIN
								subs_meta AS meta ON meta.subs_id = subs.subs_id
							LEFT JOIN
								course_bundle AS bundle ON bundle.bundle_id = meta.bundle_id
							LEFT JOIN
								bootcamp_batches AS bb ON bb.id = meta.batch_id
							LEFT JOIN
								access_duration AS access
								ON access.subs_id = subs.subs_id
							LEFT JOIN
								platform AS p
								ON p.id = bundle.platform_id
							LEFT JOIN
								(
									SELECT *
									FROM subs_export
									GROUP BY subs_id
									ORDER BY id DESC
								) AS se
								ON se.subs_id = subs.subs_id
							WHERE
								subs.user_id = ".$_GET["user"]."
							GROUP BY
								subs.subs_id
							ORDER BY
								status_str DESC,
								subs.start_date DESC;
			");

		$subs = [];
		if (isset($res_subs[0])) {

			$pending_subs = false;

			$res_freeze = db_query("SELECT
										freeze.id,
										IF (DATE(freeze.start_date) = CURDATE(), 'Today', DATE_FORMAT(freeze.start_date, '%e %b, %Y')) AS start_date,
										IF (DATE(freeze.end_date) = CURDATE(), 'Today', DATE_FORMAT(freeze.end_date, '%e %b, %Y')) AS end_date,
										IF (DATE(freeze.start_date) <= CURDATE() AND DATE(freeze.end_date) > CURDATE(), true, false) AS frozen,
										freeze.is_free,
										requestor.name AS requested_by,
										approver.name AS approved_by,
										freeze.created_at,
										freeze.updated_at
									FROM
										freeze
									LEFT JOIN
										user AS requestor ON requestor.user_id = freeze.requested_by
									LEFT JOIN
										user AS approver ON approver.user_id = freeze.approved_by
									WHERE
										freeze.user_id=".$res_user["user_id"].";"
										);

			$freeze_info = [];
			if (isset($res_freeze[0])) {

				foreach ($res_freeze as $freeze) {

					if ($freeze["frozen"] == 1) {
						$res_user["frozen"] = true;
					}

					if (!empty($freeze["is_free"])) {
						$freeze["is_free"] = true;
					}
					else {
						$freeze["is_free"] = false;
					}

					$freeze_info[] = $freeze;

				}


			}

			$res_user["freeze"] = $freeze_info;

			$res_user["subs_dates"] = [];

			$res_user["platforms"] = [];

			/* For each subs, get pther info */
			foreach ($res_subs as $each_subs) {

				if ($each_subs["platform_id"] != 2) {

					$each_subs["export_request"] = json_decode($each_subs["export_request"], true);
					$each_subs["export_response"] = json_decode($each_subs["export_response"], true);

				}

				$res_user["subs_dates"][$each_subs["subs_id"]] = ["start_date" => $each_subs["start_date_orig"], "end_date" => $each_subs["end_date_orig"]];

				// $res_duration = db_query("SELECT
				// 							id,
				// 							user_id,
				// 							subs_id,
				// 							IF (DATE(start_date) = CURDATE(), 'Today', DATE_FORMAT(start_date, '%e %b, %Y')) AS start_date,
				// 							IF (DATE(end_date) = CURDATE(), 'Today', DATE_FORMAT(end_date, '%e %b, %Y')) AS end_date,
				// 							freeze_id,
				// 							is_free,
				// 							requested_by,
				// 							approved_by,
				// 							created_at,
				// 							updated_at
				// 						FROM
				// 							access_duration
				// 						WHERE
				// 							subs_id=".$each_subs["subs_id"]."
				// 						ORDER BY id DESC;"
				// 		);

				// if (isset($res_duration[0])) {
				// 	$each_subs["end_date"] = $res_duration[0]["end_date"];
				// }

				// if (!empty($each_subs["freeze_on"]) && $each_subs["freeze_on"] == "1") {
				// 	$res_user["freeze_on"] = true;
				// }

				// if (!empty($each_subs["freeze_date"])) {
				// 	$res_user["freeze"][] = ["start_date" => $each_subs["freeze_date"], "end_date" => $each_subs["unfreeze_date"]];
				// }
				// unset($each_subs["freeze_date"]);
				// unset($each_subs["unfreeze_date"]);

				/*
				if (!auth_session_is_allowed("payment.get")) {
					if ($each_subs["status"] == "inactive") {
						continue;
					}
				}
				*/

				$durations = explode("+", $each_subs["durations"]);
				$each_subs["durations"] = [];
				if (count($durations) > 0) {

					foreach ($durations as $duration) {
						$each_subs["durations"][] = explode("=", $duration);
					}

				}

				// $dates = explode("+", $each_subs["dates"]);
				// $each_subs["dates"] = [];
				// if (count($dates) > 0) {

				// 	foreach ($dates as $duration) {

				// 		$duration = explode("=", $duration);
				// 		$duration[2] = boolval($duration[2]);
				// 		$each_subs["dates"][] = $duration;

				// 	}

				// }
				if (function_exists("access_dates_get_for_subs")) {

					if (!empty($each_subs["start_date"])) {
						$each_subs["dates"] = access_dates_get_for_subs($each_subs["subs_id"]);
					}
					else {
						$each_subs["dates"] = [];
					}

				};

				if ($each_subs["status"] == "pending") {
					$pending_subs = true;
				}

				if (!empty($each_subs["bootcamp_batch_id"])) {

					$each_subs["batch"] = $each_subs["bootcamp_batch_id"];
					$each_subs["bootcamp_batch_id"] = intval($each_subs["bootcamp_batch_id"]);
					$each_subs["bootcamp_batch_meta"] = json_decode($each_subs["bootcamp_batch_meta"], true);

				}

				/* If the user can view payments info, get that */
				/* For now, all can */
				if (/*auth_session_is_allowed("payment.get")*/true) {

					$res_pay = db_query("SELECT
											pay_id,
											sum_basic,
											sum_total,
											currency,
											DATE_FORMAT(create_date, '%e %b, %Y') AS create_date,
											instl_total,
											CASE status
												WHEN 'paid' THEN 1
												WHEN 'pending' THEN 0
												WHEN 'failed' THEN -1
											END AS pay_status,
											status,
											app_num
										FROM
											payment
										WHERE
											subs_id = ".$each_subs["subs_id"]."
										LIMIT 1;");

					if (isset($res_pay[0])) {

						$res_pay = $res_pay[0];

						$instl = db_query("SELECT
											instl.instl_count,
											instl.sum,
											instl.currency,
											IF (instl.pay_date IS NOT NULL, DATE_FORMAT(instl.pay_date, '%e %b, %Y'), NULL) AS pay_date,
											IF (instl.pay_date IS NOT NULL, DATE(instl.pay_date), NULL) AS pay_date_plain,
											IF (instl.pay_date IS NOT NULL, NULL, DATE_FORMAT(instl.due_date, '%e %b, %Y')) AS due_date,
											IF (instl.pay_date IS NOT NULL, NULL, DATE(instl.due_date)) AS due_date_plain,
											IF (instl.pay_date IS NOT NULL, NULL, instl.due_days) AS due_days,
											IF (instl.pay_date IS NOT NULL, NULL, DATEDIFF(CURDATE(), DATE(instl.due_date))) AS due_in,
											instl.pay_mode,
											CASE instl.gateway_name
												WHEN 'ebs' THEN 'EBS'
												WHEN 'razorpay' THEN 'Razorpay'
											END AS gateway_name,
											instl.gateway_reference,
											instl.pay_comment,
											instl.status,
											instl.notify_count,
											link.web_id,
											IF (link.create_entity_type = 'system' OR link.create_entity_type IS NULL, 'Direct Enrollment', user.name) AS agent_name,
											IF (link.create_entity_type = 'system' OR link.create_entity_type IS NULL, 'Direct Enrollment', user.email) AS agent_email,
											instl.receipt,
											instl.meta
										FROM
											payment_instl AS instl
										LEFT JOIN
											payment_link AS link ON instl.paylink_id = link.paylink_id
										LEFT JOIN
											user ON user.user_id = link.create_entity_id
										WHERE
											instl.pay_id = ".$res_pay["pay_id"]."
										ORDER BY
											instl.instl_count ASC;
							");

						if (isset($instl[0])) {
							$res_pay["instl"] = $instl;
						}

						$each_subs["pay"] = $res_pay;

					}
					else {
						$each_subs["pay"] = [];
					}

				}

				if (!in_array($each_subs["status"], ["pending", "inactive"])) {

					/* Get the enrollments now */
					$enr = db_query("SELECT
										enr.enr_id,
										enr.sis_id,
										enr.course_id,
										course.sis_id AS course_code,
										course.name,
										enr.lms_pass,
										enr.learn_mode,
										enr.lab_ip,
										enr.lab_user,
										enr.lab_pass,
										enr.sis_file,
										enr.sis_status,
										enr.lab_status,
										enr.status,
										section.sis_id AS section_id,
										((YEAR(section.start_date) - 2014) * 100 + MONTH(section.start_date) - 1) AS section_num,
										CONCAT(DATE_FORMAT(section.start_date, '%M %y'), ' - ', IF (enr.learn_mode = 'ml', 'Catalyst', IF (enr.learn_mode = 'sp', 'Regular', 'Premium'))) AS section_name
									FROM
										user_enrollment AS enr
									LEFT JOIN
										course_section AS section ON section.id = enr.section_id
									INNER JOIN
										course ON course.course_id = enr.course_id
									WHERE
										enr.status = 'active' AND
										enr.subs_id = ".$each_subs["subs_id"]."
									ORDER BY
										enr.enr_id ASC;
						");

					/* Only if enrollments are present, other info will be present */
					if (isset($enr[0])) {

						if ($each_subs["platform_id"] != 1) {

							$res_user["platforms"][$each_subs["platform_id"]] = [
								"name" => $each_subs["platform_name"],
								"password" => $enr[0]["lms_pass"]
							];

						}

						if ($each_subs["bundle_type"] == "specialization") {

							$months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
							$section = $enr[0]["section_id"];
							$batch_year = "20".substr($section, -2);
							$batch_month = substr($section, -7, 3);
							$each_subs["batch"] = (($batch_year - 2014) * 100 + array_search($batch_month, $months))."";

						}

						$lab_info = db_query("SELECT IF (cl.domain IS NOT NULL, cl.domain, e.lab_ip) AS lab_ip, e.lab_user, e.lab_pass FROM user_enrollment AS e LEFT JOIN course_lab AS cl ON cl.course_id = e.course_id WHERE e.lab_ip IS NOT NULL AND e.lab_ip != '' AND e.lab_user IS NOT NULL AND e.lab_pass IS NOT NULL AND e.user_id = ".$res_user["user_id"]." GROUP BY e.lab_ip;");
						$res_user["labs"] = $lab_info;

						$sis = $enr[0]["sis_file"];
						if (!empty($sis)) {

							$sis_date = date_create_from_format("Y-m-d.H.i.s", $sis);
							if (!empty($sis_date)) {

								if ($sis_date->format("Y-m-d") == date("Y-m-d")) {
									$each_subs["sis"] = $sis_date->format("h:i A")." Today";
								}
								else {
									$each_subs["sis"] = $sis_date->format("d M Y, h:i A");
								}

							}

							$each_subs["sis_file"] = $sis;

						}

						foreach ($enr as $each) {

							if ($each["course_code"] == "SKIPTHISCOURSE") {
								continue;
							}

							$course_codes[] = $each["course_code"];

						}

						if (count($enr) == 1 && $each_subs["bundle"] == "Custom Combo") {
							$each_subs["bundle"] = $enr[0]["name"];
						}

						/* Identify the complimentary courses */
						if (!empty($each_subs["combo_free"])) {

							$free_courses = [];
							$combo_free = explode(";", $each_subs["combo_free"]);
							foreach ($combo_free as $course) {
								$free_courses[] = explode(",", $course)[0];
							}
							foreach ($enr as $each_enr) {

								if (in_array($each_enr["course_id"], $free_courses)) {
									$each_enr["complimentary"] = true;
								}

								$each_subs["enr"][] = $each_enr;

							}

						}elseif (!empty($each_subs["individual_course"])) {
                                                        $each_subs["invidualCourseFlag"] = true;
							$individualCourses = [];
							$combo_free = explode(";", $each_subs["individual_course"]);
							foreach ($combo_free as $course) {
								$individualCourses[] = explode(",", $course)[0];
							}
							foreach ($enr as $each_enr) {

								if (in_array($each_enr["course_id"], $individualCourses)) {
									$each_enr["individual"] = true;
								}

								$each_subs["enr"][] = $each_enr;

							}

						}
						else {
							$each_subs["enr"] = $enr;
						}

						/* Get welcome call info */
						$meta = db_query("SELECT
											IF(DATE(support.email_sent_at) = CURDATE(), DATE_FORMAT(support.email_sent_at, '%h:%i %p Today'), DATE_FORMAT(support.email_sent_at, '%e %b %Y, %h:%i %p')) AS email_sent_at,
											IF(DATE(support.called_at) = CURDATE(), DATE_FORMAT(support.called_at, '%h:%i %p Today'), DATE_FORMAT(support.called_at, '%e %b %Y, %h:%i %p')) AS called_at,
											agent_caller.name AS caller,
											support.call_status,
											agent_smser.name AS smser,
											IF(DATE(support.sms_sent_at) = CURDATE(), DATE_FORMAT(support.sms_sent_at, '%h:%i %p Today'), DATE_FORMAT(support.sms_sent_at, '%e %b %Y, %h:%i %p')) AS sms_sent_at,
											support.comments
										FROM
											user_enr_meta AS support
										LEFT JOIN
											user AS agent_caller ON agent_caller.user_id = support.called_by AND support.called_by IS NOT NULL
										LEFT JOIN
											user AS agent_smser ON agent_smser.user_id = support.sms_sent_by AND support.sms_sent_by IS NOT NULL
										WHERE
											support.subs_id = ".$each_subs["subs_id"].";
							");

						if (isset($meta[0])) {
							$each_subs["support"] = $meta;
						}
						else {
							$each_subs["support"] = [];
						}

						/* Get support call summary */
						// $history = db_query("SELECT
						// 						history.channel,
						// 						historical_rep.name,
						// 						history.status,
						// 						DATE_FORMAT(history.timestamp, '%e %b %Y, %h:%i %p'),
						// 						history.comments
						// 					FROM
						// 						user_enr_meta_history AS history
						// 					INNER JOIN
						// 						user AS historical_rep ON historical_rep.user_id = history.rep_id AND history.rep_id IS NOT NULL
						// 					WHERE
						// 						history.subs_id = ".$each_subs["subs_id"]."
						// 					ORDER BY `timestamp`;
						// 				");

						// if (isset($history[0])) {
						// 	$each_subs["history"] = $history;
						// }
						// else {
						// 	$each_subs["history"] = [];
						// }

					}
					/* Nothing is available */
					else {

						$each_subs["enr"] = [];
						$each_subs["support"] = [];
						$each_subs["history"] = [];
						$combo = explode(";", $each_subs["combo"]);
						if (count($combo) == 1) {

							$combo_course = explode(",", $combo[0]);
							$course_info = db_query("SELECT name FROM course WHERE course_id = ".$combo_course[0]);
							if (!empty($course_info) && $each_subs["bundle"] == "Custom Combo") {
								$each_subs["bundle"] = $course_info[0]["name"];
							}

						}

					}

				}

				$subs[$each_subs["status"]][] = $each_subs;

			}

			if (!$otherPlatforms) {
				$res_user["lms_pass"] = null;
			}

			if ($lms_soc_status && $pending_subs) {
				$res_user["lms_soc_link"] = JAWS_PATH_WEB."/setupaccess?user=".$res_user["web_id"];
			}

		}

		if (empty($res_user["platforms"])) {
			$res_user["platforms"] = false;
		}

		$res_user["subs"] = $subs;

		$res_user["progress"] = [];
		if (!empty($course_codes)) {

			$data["jig_id"] = $res_user["jig_id"];
			$data["course_codes"] = implode(";", $course_codes);

			$opts = array('http' => array(
					'method'  => 'POST',
					'header'  => 'Content-type: application/x-www-form-urlencoded',
					'content' => http_build_query($data)
				)
			);

			$context  = stream_context_create($opts);
			// die(file_get_contents("https://jigsawacademy.net/app/getcoursestopics.php", false, $context));
			$response = json_decode(file_get_contents("https://jigsawacademy.net/app/getcourseprogress.php", false, $context), true);
			foreach ($response["p"] as $course_code => $percentage) {

				$res_user["progress"][$course_code]["p"] = floatval($percentage["p"]);
				$res_user["progress"][$course_code]["c"] = (!empty($percentage["c"]) ? date_create_from_format("Y-m-d H:i:s.u", $percentage[c])->format("d M Y g:i A") : "0");

			}

			$res_user["assignments"] = $response["a"];

			load_plugin("jlc");
			$jlc = new JLC;
			$res_user["jlc_status"] = $jlc->statusFor([$res_user["jig_id"]])[$res_user["jig_id"]];

		}

	}

	$edit = auth_session_is_allowed("enrollment.get.adv");
	$electives = [];
	$courses = [];
	$bundles= [];
	if ($edit) {

		$electives = db_query("SELECT name, course_id, sis_id FROM course WHERE is_elective = 1 AND sis_id IS NOT NULL AND status != '';");
		$courses = db_query("SELECT name, course_id, sis_id FROM course WHERE is_elective != 1 AND sis_id IS NOT NULL AND status != '';");
		$res_bundles = db_query("SELECT name, bundle_id, bundle_type, status FROM course_bundle WHERE combo != '' AND name != '' AND status != '' AND bundle_type IN ('specialization', 'programs', 'bootcamps');");
		foreach ($res_bundles as $bundle) {

			$bundle["batches"] = [];
			$batches = db_query("SELECT * FROM bootcamp_batches WHERE bundle_id = ".$bundle["bundle_id"]);
			foreach ($batches as $batch) {

				$batch["meta"] = json_decode($batch["meta"], true);
				$bundle["batches"][$batch["id"]] = $batch["meta"]["name"];

			}

			if ($bundle["bundle_type"] == "specialization") {
				$bundles["specializations"][$bundle["bundle_id"]] = $bundle;
			}
			else if ($bundle["bundle_type"] == "programs") {
				$bundles["programs"][$bundle["bundle_id"]] = $bundle;
			}
			else {
				$bundles["bootcamps"][$bundle["bundle_id"]] = $bundle;
			}

		}

	}

	$logs = db_query(
		"SELECT
			CASE ul.category
				WHEN 'profile.edit' THEN 'Profile edit'
				WHEN 'subs.edit' THEN 'Enrolment edit'
				WHEN 'lab.edit' THEN 'Lab login edit'
				WHEN 'subs.access.edit' THEN 'Access duration edit'
			END AS category,
			CASE ul.category
				WHEN 'profile.edit' THEN 1
				WHEN 'subs.edit' THEN 2
				WHEN 'lab.edit' THEN 3
				WHEN 'subs.access.edit' THEN 4
			END AS category_id,
			ul.sub_category,
			ul.description,
			DATE_FORMAT(ul.created_at, '%e %b, %Y') AS created_at,
			user.name,
			IF (ul.resolved_by = ul.created_by, NULL, ul.created_by) AS created_by,
			ul.resolved_by,
			ul.context_type,
			ul.context_id
		FROM
			user_logs AS ul
		INNER JOIN
			user
			ON user.user_id = ul.created_by
		WHERE
			ul.category != 'issue'
			AND
			ul.user_id = ".$res_user["user_id"]."
		ORDER BY
			ul.created_at DESC;"
	);

	$pending_logs = db_query(
		"SELECT
			CASE ul.category
				WHEN 'profile.edit' THEN 'Profile edit'
				WHEN 'subs.edit' THEN 'Enrolment edit'
				WHEN 'lab.edit' THEN 'Lab login edit'
				WHEN 'subs.access.edit' THEN 'Access duration edit'
			END AS category,
			CASE ul.category
				WHEN 'profile.edit' THEN 1
				WHEN 'subs.edit' THEN 2
				WHEN 'lab.edit' THEN 3
				WHEN 'subs.access.edit' THEN 4
			END AS category_id,
			ul.sub_category,
			ul.description,
			DATE_FORMAT(ul.created_at, '%e %b, %Y') AS created_at,
			user.name,
			ul.created_by,
			ul.context_type,
			ul.context_id
		FROM
			user_logs AS ul
		INNER JOIN
			user
			ON user.user_id = ul.created_by
		WHERE
			ul.category != 'issue'
			AND
			ul.status = 'pending'
			AND
			ul.user_id = ".$res_user["user_id"]."
		ORDER BY
			ul.created_at DESC;"
	);

	$new_notifications = 0;
	$notifications = [];
	$notifcations_list = db_query(
		"SELECT
			id,
			issue,
			IF (
				DATE(created_at) = CURDATE(),
				DATE_FORMAT(created_at, '%h:%i %p Today'),
				DATE_FORMAT(created_at, '%e %b %Y, %h:%i %p')
			) AS created_at,
			IF (
				resolved_at IS NOT NULL,
				IF (
					DATE(resolved_at) = CURDATE(),
					DATE_FORMAT(resolved_at, '%h:%i %p Today'),
					DATE_FORMAT(resolved_at, '%e %b %Y, %h:%i %p')
				),
				NULL
			) AS resolved_at
		FROM
			user_issues
		WHERE
			user_id = ".$res_user["user_id"].";"
	);
	foreach ($notifcations_list as $notif) {

		if (empty($notif["resolved_at"])) {

			$notif["resolved_at"] = "No";
			$new_notifications++;

		}

		$notif["issue"] = json_decode($notif["issue"], true);
		$notifications[] = $notif;

	}

	die(json_encode(["user" => $res_user, "edit" => $edit, "bundles" => $bundles, "electives" => $electives, "courses" => $courses, "edit_payment" => auth_session_is_allowed("pm"), "himanshu" => ($_SESSION["user"]["email"] == "himanshu@jigsawacademy.com"), "logs" => $logs, "pending_logs" => $pending_logs, "can_call" => !empty($_SESSION["user"]["phone"]), "notifications" => $notifications, "new_notifications" => $new_notifications]));

?>