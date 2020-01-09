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

		header('Location: ../index.php');
		die();

	}

	function welcome_email_send($sis_file) {

		activity_debug_start();

		$message_content = array();

		$res_enrs = db_query(
				"SELECT
					enr.*,
					user.name, user.email, user.lms_soc, user.soc_fb, user.soc_gp, user.soc_li,
					course.name AS course_name, course.sis_id AS course_sis_id, course_meta.category AS course_category, course.course_id,
					subs.subs_id, subs.start_date, subs.end_date, subs.end_date_ext, subs.combo_free,
					bundle.name AS bundle_name,
					bundle.code AS bundle_code,
					bundle.bundle_id AS bundle_id,
					bundle.is_bootcamp AS is_bootcamp,
					bundle.iot_kit,
					bundle.meta AS bundle_meta,
					bb.id AS batch_id,
					bb.meta AS bb_meta,
					IF (bundle.batch_start_date IS NOT NULL, DATE_FORMAT(bundle.batch_start_date, '%M %D, %Y'), NULL) AS bootcamp_batch,
					bb.start_date AS batch_start_date,
					bundle_meta.category AS bundle_categories,
					pay.instl_total,
					pay.currency
				FROM
					user_enrollment AS enr
				INNER JOIN
					user ON user.user_id = enr.user_id
				INNER JOIN
					subs ON subs.subs_id = enr.subs_id
				LEFT JOIN
					subs_meta ON subs_meta.subs_id = subs.subs_id
				LEFT JOIN
					payment AS pay ON pay.subs_id = subs.subs_id
				INNER JOIN
					course ON course.course_id = enr.course_id
				INNER JOIN
					course_meta ON course_meta.course_id = enr.course_id
				LEFT JOIN
					course_bundle AS bundle ON bundle.bundle_id = subs_meta.bundle_id
				LEFT JOIN
					course_bundle_meta AS bundle_meta ON bundle_meta.bundle_id = bundle.bundle_id
				LEFT JOIN
					bootcamp_batches AS bb ON bb.id = subs_meta.batch_id
				WHERE
					enr.status='active'
				AND
					enr.shall_notify=1
				AND
					enr.sis_file=".db_sanitize($sis_file)." ORDER BY enr_id ASC"
			);

		$subs_ids = [];
		$iot_mail_subs_ids = [];

		foreach ($res_enrs as $enr) {

			if (!in_array($enr["subs_id"], $subs_ids)) {
				$subs_ids[] = $enr["subs_id"];
			}

			if (!isset($message_content[$enr["email"]])) {

				$message_content[$enr["email"]]["name"] = ucwords(strtolower($enr["name"]));
				$message_content[$enr["email"]]["lms_soc"] = $enr["lms_soc"];
				$message_content[$enr["email"]]["login"] = ($enr["lms_soc"] != "corp" ? $enr["soc_".$enr["lms_soc"]] : $enr["sis_id"]);

				$message_content[$enr["email"]]["bundle_id"] = $enr["bundle_id"];
				$message_content[$enr["email"]]["bundle_name"] = $enr["bundle_name"];

				$message_content[$enr["email"]]["sis_id"] = $enr["sis_id"];
				$message_content[$enr["email"]]["lms_pass"] = $enr["lms_pass"];
				$message_content[$enr["email"]]["lab_user"] = $enr["lab_user"];
				$message_content[$enr["email"]]["lab_pass"] = $enr["lab_pass"];
				$message_content[$enr["email"]]["start_date"] = $enr["start_date"];
				$end_date = (empty($enr["end_date_ext"]) ? $enr["end_date"] : $enr["end_date_ext"]);

				$start_date = date_create_from_format("Y-m-d H:i:s", $enr["start_date"]);
				$end_date = date_create_from_format("Y-m-d H:i:s", $end_date);

				$message_content[$enr["email"]]["end_date"] = $end_date->format("j F Y");
				$message_content[$enr["email"]]["duration"] = ceil(floatval($start_date->diff($end_date)->format("%a")) / 30);

				$message_content[$enr["email"]]["is_bootcamp"] = boolval($enr["is_bootcamp"]);
				$message_content[$enr["email"]]["bootcamp_batch"] = $enr["bootcamp_batch"];

				$message_content[$enr["email"]]["full_stack"] = 0;
				if (!empty($enr["bundle_categories"])) {

					$bundle_categories = explode(";", $enr["bundle_categories"]);
					foreach ($bundle_categories as $category) {

						if ($category == "full-stack") {

							$message_content[$enr["email"]]["full_stack"] = intval($enr["bundle_id"]);
							break;

						}

					}

				}

				$message_content[$enr["email"]]["suppress_lab_info"] = false;
				if (!empty($enr["bb_meta"])) {

					$bb_meta = json_decode($enr["bb_meta"], true);
					if (!empty($bb_meta["suppressed_lab_info"])) {
						$message_content[$enr["email"]]["suppress_lab_info"] = true;
					}

				}
				else if (!empty($enr["batch_meta"])) {

					$batch_meta = json_decode($enr["batch_meta"], true);
					if (!empty($batch_meta["suppressed_lab_info"])) {
						$message_content[$enr["email"]]["suppress_lab_info"] = true;
					}

				}

				$message_content[$enr["email"]]["enr"] = [];
				$message_content[$enr["email"]]["lab"] = [];
				$message_content[$enr["email"]]["attachments"] = [];

				$batch_start_date = false;
				if (!empty($enr["batch_start_date"])) {
					$batch_start_date = date_create_from_format("Y-m-d", $enr["batch_start_date"]);
				}

				if ($enr["bundle_id"] == 126 || $enr["bundle_id"] == 127) {

					$message_content[$enr["email"]]["attachments"][] = "media/misc/attachments/jlc/Jigsaw_Learning_Center_Login_&_Support_Guide.pdf";
					$log_data = db_sanitize(json_encode(["obj" => $batch_start_date, "format" => !empty($batch_start_date) ? $batch_start_date->format("F_Y") : "false"]));
					db_exec("INSERT INTO system_log (source, data) VALUES ('sis.email.ipba', $log_data);");
					if (!empty($batch_start_date)) {
						$message_content[$enr["email"]]["attachments"][] = "media/misc/attachments/jlc/ipba/IPBA_".$batch_start_date->format("F_Y")."_Schedule.pdf";
					}

				}
				elseif ($enr["bundle_id"] == 129) {

					$message_content[$enr["email"]]["attachments"][] = "media/misc/attachments/jlc/Jigsaw_Learning_Center_Login_&_Support_Guide.pdf";
					$log_data = db_sanitize(json_encode(["obj" => $batch_start_date, "format" => !empty($batch_start_date) ? $batch_start_date->format("F_Y") : "false"]));
					db_exec("INSERT INTO system_log (source, data) VALUES ('sis.email.ipba2', $log_data);");
					if (!empty($batch_start_date)) {
						$message_content[$enr["email"]]["attachments"][] = "media/misc/attachments/jlc/ipba2/".$enr["batch_id"]."/IPBA_Schedule.pdf";
					}

				}
				elseif ($enr["bundle_id"] == 122 || $enr["bundle_id"] == 123 || $enr["bundle_id"] == 131) {

					$message_content[$enr["email"]]["attachments"][] = "media/misc/attachments/jlc/Jigsaw_Learning_Center_Login_&_Support_Guide.pdf";
					// $log_data = db_sanitize(json_encode(["obj" => $batch_start_date, "format" => !empty($batch_start_date) ? $batch_start_date->format("F_Y") : "false"]));
					// db_exec("INSERT INTO system_log (source, data) VALUES ('sis.email.pgpdm', $log_data);");
					// if (!empty($batch_start_date)) {
					// 	$message_content[$enr["email"]]["attachments"][] = "media/misc/attachments/jlc/pgpdm/".$batch_start_date->format("F_Y")."/PGPDM_Schedule.pdf";
					// }
					$message_content[$enr["email"]]["attachments"][] = "media/misc/attachments/jlc/pgpdm/".$enr["batch_id"]."/PGPDM_Schedule.pdf";

				}
				elseif ($enr["lms_soc"] != "corp") {

					$message_content[$enr["email"]]["attachments"][] = "media/misc/attachments/jlc/Jigsaw_Learning_Center_Login_&_Support_Guide.pdf";

					if (!empty($batch_start_date)) {

						$bundle_code = strtolower($enr["bundle_code"]);
						if (file_exists("media/misc/attachments/jlc/$bundle_code/".$enr["bundle_code"]."_".$batch_start_date->format("F_Y")."_Schedule.pdf")) {
							$message_content[$enr["email"]]["attachments"][] = "media/misc/attachments/jlc/$bundle_code/".$enr["bundle_code"]."_".$batch_start_date->format("F_Y")."_Schedule.pdf";
						}

					}

				}
				else {

					$message_content[$enr["email"]]["attachments"][] = "media/misc/attachments/jlc/Jigsaw_Learning_Center_Login_Guide_Corporate.pdf";

					if (!empty($batch_start_date)) {

						$bundle_code = strtolower($enr["bundle_code"]);
						if (file_exists("media/misc/attachments/jlc/$bundle_code/".$enr["bundle_code"]."_".$batch_start_date->format("F_Y")."_Schedule.pdf")) {
							$message_content[$enr["email"]]["attachments"][] = "media/misc/attachments/jlc/$bundle_code/".$enr["bundle_code"]."_".$batch_start_date->format("F_Y")."_Schedule.pdf";
						}

					}

				}

				$message_content[$enr["email"]]["combo_free"] = [];
				$combo_frees = explode(";", $enr["combo_free"]);
				foreach ($combo_frees as $combo_free) {
					$message_content[$enr["email"]]["combo_free"][] = explode(",", $combo_free)[0];
				}

				$message_content[$enr["email"]]["instl_total"] = $enr["instl_total"];

				// if (($enr["course_category"] == "iot" && $enr["bundle_id"] != "59" && !empty($enr["bundle_id"])) || $enr["bundle_id"] == "71" || $enr["bundle_id"] == "73" || $enr["bundle_id"] == "74") {
				// 	$message_content[$enr["email"]]["iot"] = 1;
				// }
				$message_content[$enr["email"]]["iot"] = intval($enr["iot_kit"]);

				if ($enr["bundle_id"] == "72" || $enr["bundle_id"] == "59") {

					$message_content[$enr["email"]]["iot"] = 0;

					$message_content[$enr["email"]]["iot_nokit"] = 1;
					$message_content[$enr["email"]]["iot_nokit_bundle"] = ($enr["bundle_id"] == "72" ? "Full Stack Integrated Program in Analytics" : "IoT Analyst Certification");
					$message_content[$enr["email"]]["attachments"][] = "media/misc/attachments/jlc/IoT_Components.pdf";

				}

				if ($message_content[$enr["email"]]["iot"] == 1 && $enr["currency"] == "usd") {
					$message_content[$enr["email"]]["iot_nokit_usd"] = 1;
				}

				if ($enr["iot_kit"] == 1 && empty($message_content[$enr["email"]]["iot_nokit_usd"])) {
					$iot_mail_subs_ids[] = $enr["subs_id"];
				}

			}

			$learn_mode = "Catalyst";
			$learn_mode_code = "3";
			if ($enr["learn_mode"] == "sp") {

				$learn_mode = "Regular";
				$learn_mode_code = "2";

			}
			else if ($enr["learn_mode"] == "il") {

				$learn_mode = "Premium";
				$learn_mode_code = "1";

			}

			$complimentary = false;
			if (in_array($enr["course_id"], $message_content[$enr["email"]]["combo_free"])) {
				$complimentary = true;
			}

			$message_content[$enr["email"]]["enr"][] = ["course_id" => $enr["course_id"], "name" => $enr["course_name"], "learn_mode" => $learn_mode, "complimentary" => $complimentary];

			if (!empty($enr["lab_ip"])) {

				if (!isset($message_content[$enr["email"]]["lab"][$enr["lab_ip"]])) {
					$message_content[$enr["email"]]["lab"][$enr["lab_ip"]] = [];
				}
				$message_content[$enr["email"]]["lab"][$enr["lab_ip"]][] = $enr["course_name"];

			}

			$section = section_get_by_id($enr["section_id"]);
			if ($section["learn_mode"] == $learn_mode_code) {
				$message_content[$enr["email"]]["attachments"][] = "media/misc/attachments/jlc/prog_calendar/".$enr["course_id"]."/".$section["sis_id"].".pdf";
			}

		}

		foreach ($message_content as $email => $mail) {

			activity_debug_log(json_encode(["mail" => [$email, $mail]]));
			if (empty($email)) {

				activity_create("critical", "welcome.email.fail", "email.blank", "", "", "", "", json_encode(["mail" => [$email, $mail]]));
				continue;

			}

			$template = "sis.welcome.email";
			if ($mail["bundle_id"] == 126 || $mail["bundle_id"] == 127 || $mail["bundle_id"] == 129) {
				$template = "sis.welcome.email.ipba";
			}
			elseif ($mail["bundle_id"] == 122 || $mail["bundle_id"] == 123 || $mail["bundle_id"] == 131) {
				$template = "sis.welcome.email.pgpdm";
			}

			send_email_with_attachment($template, ["to" => $email, "bcc" => ((!empty($mail["iot"]) && ($mail["iot"] == 1)) ? "madhuri@jigsawacademy.com" : "")], $mail, $mail["attachments"]);
			if (!empty($mail["iot"]) && $mail["iot"] == 1 && empty($mail["iot_nokit"])) {
				send_email("iot.address.request", ["to" => $email], $mail);
			}

		}

		db_exec("UPDATE user_enrollment SET shall_notify=0 WHERE sis_file=".db_sanitize($sis_file).";");

		$date = new DateTime("now");
		$date_str = db_sanitize($date->format("Y-m-d H:i:s"));

		foreach ($subs_ids as $subs_id) {

			$res_enr_meta = db_query("SELECT * FROM user_enr_meta WHERE subs_id=".$subs_id);
			if (!isset($res_enr_meta[0])) {
				db_exec("INSERT INTO user_enr_meta (subs_id, email_sent_at) VALUES (".$subs_id.", ".$date_str.");");
			}
			else {
				db_exec("UPDATE user_enr_meta SET email_sent_at=".$date_str." WHERE subs_id=".$subs_id);
			}

			if (in_array($subs_id, $iot_mail_subs_ids)) {
				db_exec("UPDATE user_enr_meta SET iot_email_sent_at = $date_str WHERE subs_id = $subs_id;");
			}

		}

	}

	function welcome_email_send_by_user_id($user_id) {

		$message_content = [];

		$res_enrs = db_query(
				"SELECT
					enr.*,
					user.name, user.email, user.lms_soc, user.soc_fb, user.soc_gp, user.soc_li,
					course.name AS course_name, course.sis_id AS course_sis_id,
					subs.start_date, subs.end_date, subs.end_date_ext,
					bundle.name AS bundle_name
				FROM
					user_enrollment AS enr
				INNER JOIN
					user ON user.user_id = enr.user_id
				INNER JOIN
					subs ON subs.subs_id = enr.subs_id
				INNER JOIN
					subs_meta ON subs_meta.subs_id = subs.subs_id
				INNER JOIN
					course ON course.course_id = enr.course_id
				LEFT JOIN
					course_bundle AS bundle ON bundle.bundle_id = subs_meta.bundle_id
				WHERE
					enr.status='active'
				AND
					enr.user_id=".$user_id." ORDER BY enr_id ASC"
			);

		$message_content["email"] = $res_enrs[0]["email"];
		$message_content["name"] = $res_enrs[0]["name"];
		$message_content["lms_soc"] = $res_enrs[0]["lms_soc"];
		$message_content["login"] = ($res_enrs[0]["lms_soc"] != "corp" ? $res_enrs[0]["soc_".$res_enrs[0]["lms_soc"]] : $res_enrs[0]["sis_id"]);

		$message_content["bundle_name"] = $res_enrs[0]["bundle_name"];

		$message_content["sis_id"] = $res_enrs[0]["sis_id"];
		$message_content["lms_pass"] = $res_enrs[0]["lms_pass"];
		$message_content["lab_user"] = $res_enrs[0]["lab_user"];
		$message_content["lab_pass"] = $res_enrs[0]["lab_pass"];
		$message_content["start_date"] = $res_enrs[0]["start_date"];
		$end_date = (empty($res_enrs[0]["end_date_ext"]) ? $res_enrs[0]["end_date"] : $res_enrs[0]["end_date_ext"]);

		$start_date = date_create_from_format("Y-m-d H:i:s", $res_enrs[0]["start_date"]);
		$end_date = date_create_from_format("Y-m-d H:i:s", $end_date);

		$message_content["end_date"] = $end_date->format("j F, Y");
		$message_content["duration"] = ceil(floatval($start_date->diff($end_date)->format("%a")) / 30);

		$message_content["enr"] = [];
		$message_content["lab"] = [];
		$message_content["attachments"] = [];
		if ($res_enrs[0]["lms_soc"] != "corp") {
			$message_content["attachments"][] = "media/misc/attachments/jlc/Jigsaw_Learning_Center_Login_Guide.pdf";
		}
		else {
			$message_content["attachments"][] = "media/misc/attachments/jlc/Jigsaw_Learning_Center_Login_Guide_Corporate.pdf";
		}

		foreach ($res_enrs as $enr) {

			$learn_mode = "Catalyst";
			$learn_mode_code = "3";
			if ($enr["learn_mode"] == "sp") {

				$learn_mode = "Regular";
				$learn_mode_code = "2";

			}
			else if ($enr["learn_mode"] == "il") {

				$learn_mode = "Premium";
				$learn_mode_code = "1";

			}

			$message_content["enr"][] = ["course_id" => $enr["course_id"], "name" => $enr["course_name"], "learn_mode" => $learn_mode];

			if (!empty($enr["lab_ip"])) {

				if (!isset($message_content["lab"][$enr["lab_ip"]])) {
					$message_content["lab"][$enr["lab_ip"]] = [];
				}
				$message_content["lab"][$enr["lab_ip"]][] = $enr["course_name"];

			}

			$section = section_get_by_id($enr["section_id"]);
			if ($section["learn_mode"] == $learn_mode_code) {
				$message_content["attachments"][] = "media/misc/attachments/jlc/prog_calendar/".$enr["course_id"]."/".$section["sis_id"].".pdf";
			}

		}

		send_email_with_attachment("sis.welcome.email", ["to" => $message_content["email"]], $message_content, $message_content["attachments"]);

	}

	function welcome_email_send_by_subs_id($subs_id) {

		$message_content = [];

		$res_enrs = db_query(
				"SELECT
					enr.*,
					user.name, user.email, user.lms_soc, user.soc_fb, user.soc_gp, user.soc_li,
					course.name AS course_name, course.sis_id AS course_sis_id,
					subs.start_date, subs.end_date, subs.end_date_ext,
					bundle.name AS bundle_name
				FROM
					user_enrollment AS enr
				INNER JOIN
					user ON user.user_id = enr.user_id
				INNER JOIN
					subs ON subs.subs_id = enr.subs_id
				INNER JOIN
					subs_meta ON subs_meta.subs_id = subs.subs_id
				INNER JOIN
					course ON course.course_id = enr.course_id
				LEFT JOIN
					course_bundle AS bundle ON bundle.bundle_id = subs_meta.bundle_id
				WHERE
					enr.status='active'
				AND
					subs.subs_id=".$subs_id." ORDER BY enr_id ASC"
			);

		$message_content["email"] = $res_enrs[0]["email"];
		$message_content["name"] = $res_enrs[0]["name"];
		$message_content["lms_soc"] = $res_enrs[0]["lms_soc"];
		$message_content["login"] = ($res_enrs[0]["lms_soc"] != "corp" ? $res_enrs[0]["soc_".$res_enrs[0]["lms_soc"]] : $res_enrs[0]["sis_id"]);

		$message_content["bundle_name"] = $res_enrs[0]["bundle_name"];

		$message_content["sis_id"] = $res_enrs[0]["sis_id"];
		$message_content["lms_pass"] = $res_enrs[0]["lms_pass"];
		$message_content["lab_user"] = $res_enrs[0]["lab_user"];
		$message_content["lab_pass"] = $res_enrs[0]["lab_pass"];
		$message_content["start_date"] = $res_enrs[0]["start_date"];
		$end_date = (empty($res_enrs[0]["end_date_ext"]) ? $res_enrs[0]["end_date"] : $res_enrs[0]["end_date_ext"]);

		$start_date = date_create_from_format("Y-m-d H:i:s", $res_enrs[0]["start_date"]);
		$end_date = date_create_from_format("Y-m-d H:i:s", $end_date);

		$message_content["end_date"] = $end_date->format("j F, Y");
		$message_content["duration"] = ceil(floatval($start_date->diff($end_date)->format("%a")) / 30);

		$message_content["enr"] = [];
		$message_content["lab"] = [];
		$message_content["attachments"] = [];
		if ($res_enrs[0]["lms_soc"] != "corp") {
			$message_content["attachments"][] = "media/misc/attachments/jlc/Jigsaw_Learning_Center_Login_Guide.pdf";
		}
		else {
			$message_content["attachments"][] = "media/misc/attachments/jlc/Jigsaw_Learning_Center_Login_Guide_Corporate.pdf";
		}

		foreach ($res_enrs as $enr) {

			$learn_mode = "Catalyst";
			$learn_mode_code = "3";
			if ($enr["learn_mode"] == "sp") {

				$learn_mode = "Regular";
				$learn_mode_code = "2";

			}
			else if ($enr["learn_mode"] == "il") {

				$learn_mode = "Premium";
				$learn_mode_code = "1";

			}

			$message_content["enr"][] = ["course_id" => $enr["course_id"], "name" => $enr["course_name"], "learn_mode" => $learn_mode];

			if (!empty($enr["lab_ip"])) {

				if (!isset($message_content["lab"][$enr["lab_ip"]])) {
					$message_content["lab"][$enr["lab_ip"]] = [];
				}
				$message_content["lab"][$enr["lab_ip"]][] = $enr["course_name"];

			}

			$section = section_get_by_id($enr["section_id"]);
			if ($section["learn_mode"] == $learn_mode_code) {
				$message_content["attachments"][] = "media/misc/attachments/jlc/prog_calendar/".$enr["course_id"]."/".$section["sis_id"].".pdf";
			}

		}

		send_email_with_attachment("sis.welcome.email", ["to" => $message_content["email"]], $message_content, $message_content["attachments"]);

	}

	function notify_sis_import_failure($data) {

		if (is_array($data)) {

			$message = "SIS import failed on the LMS, and LMS responded with the following";

			if (isset($data["processing_warnings"]) || isset($data["processing_errors"])) {

				if (isset($data["processing_warnings"])) {

					$message .= " warning(s): <br /><br />";
					foreach ($data["processing_warnings"] as $warning) {
						$message .= (!empty($warning[0]) ? $warning[0]." - " : "").$warning[1]."<br /><br />";
					}

				}
				if (isset($data["processing_errors"])) {

					$message .= " error(s): <br /><br />";
					foreach ($data["processing_errors"] as $error) {
						$message .= (!empty($error[0]) ? $error[0]." - " : "").$error[1]."<br /><br />";
					}

				}

			}
			else if (isset($data["data"]["error_message"])) {

				$message .= " error message: <br /><br />";
				foreach ($data["error_message"] as $error) {
					$message .= $error[0]." - ".$error[1]."<br /><br />";
				}

			}
			else if (isset($data["errors"])) {
				$message .= " error: <br>".$data["errors"][0]["message"]." - ".ucwords(str_replace("_", " ", $data["errors"][0]["error_code"]))."<br><br>";
			}

			$message .= "Please take a look at the issue.<br />Please <a href='https://www.jigsawacademy.com/jaws/sis?sis=".$data["filename"]."'>click here</a> to download the troublesome file.";

		}
		else {
			$message = "SIS import for file <a href='https://www.jigsawacademy.com/jaws/sis?sis=".$data."'> failed thrice. CUrl seems to have failed.";
		}

		send_email("sis.notify", ["subject" => "SIS import failed!"], message_prepare("SIS import failed", $message, true));

	}

	function notify_code_failure($template, $data) {
		send_email($template, [], $data);
	}

	function notify_sis_creation($filename_timestamp) {
		send_email("sis.notify", [], message_prepare("SIS file available for download", "Please <a href='https://www.jigsawacademy.com/jaws/sis?sis=".$filename_timestamp."'>click here</a> to download the latest file."));
	}

	function notify_sis_write_failure($data) {
		send_email("sis.notify", ["subject" => "SIS file write failed"], message_prepare("SIS file creation failure", $data, true));
	}

	function message_prepare($header, $data, $severity = false) {
		return ["header" => $header, "text" => $data, "severity" => $severity];
	}

?>
