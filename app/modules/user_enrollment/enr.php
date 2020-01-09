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

	function enr_create($subs_id, $ml_effective = false, $start_date = false) {

		$subs = subs_get_info($subs_id);
		if (empty($subs)) {
			return;
		}

		$bundle_id = $subs["meta"]["bundle_id"];
		if (!empty($bundle_id)) {

			$platform = db_query("SELECT p.code FROM platform AS p INNER JOIN course_bundle AS b ON b.platform_id = p.id WHERE b.bundle_id = $bundle_id;");
			if ($platform[0]["code"] != "JLC") {
				return;
			}

		}

		$start_date = date_create_from_format("Y-m-d H:i:s", (empty($start_date) ? $subs["start_date"] : $start_date));

		$enr_params = enr_params_set($subs);

		$combo = $subs["combo"].(strlen($subs["combo_free"]) > 0 ? ";".$subs["combo_free"] : "");
		if (!empty($subs["meta"]["batch_id"])) {

			$batch = db_query("SELECT * FROM bootcamp_batches WHERE id = ".$subs["meta"]["batch_id"]);
			$start_date = date_create_from_format("Y-m-d", $batch[0]["start_date"]);

			$sequence = enr_sequence_bootcamp($subs["combo"], $subs["combo_free"], $start_date, $subs["meta"]["bundle_id"], $subs["meta"]["batch_id"]);

		}
		else {

			$ml_effective_date = date_create_from_format("Y-m-d H:i:s", "2017-09-01 00:00:00");
			if ($start_date < $ml_effective_date) {
				$GLOBALS["ml_effective"] = false;
			}
			else {

				if (!$ml_effective) {
					$GLOBALS["ml_effective"] = (setting_get("ml_effective", false) == "true" ? true : false);
				}
				else {
					$GLOBALS["ml_effective"] = true;
				}

			}

			$bootcamp_info = db_query("SELECT is_bootcamp, batch_start_date FROM course_bundle WHERE is_bootcamp = 1 AND bundle_id = ".$bundle_id);
			if (empty($bootcamp_info)) {
				$bootcamp_info = false;
			}
			else {

				$bootcamp_info = $bootcamp_info[0];
				$bootcamp_info["batch_start_date"] = date_create_from_format("Y-m-d", $bootcamp_info["batch_start_date"]);

			}

			if ($GLOBALS["ml_effective"] == true) {
				$sequence = enr_sequence_ml($combo, clone $start_date, bundle_identify_full_stack($bundle_id), $subs["combo_free"], $bootcamp_info, $subs_id);
			}
			else {
				$sequence = enr_sequence($combo, clone $start_date);
			}

			$static_batches = db_query("SELECT static_batches FROM course_bundle WHERE bundle_id = ".$bundle_id);
			if (!empty($static_batches)) {
				$static_batches = $static_batches[0]["static_batches"];
			}
			else {
				$static_batches = "";
			}

		}


		insert($enr_params, $sequence, $start_date, $bootcamp_info, $static_batches, $bundle_id);

		db_exec("UPDATE subs SET status='active' WHERE subs_id=".$subs_id);

	}

	function enr_params_set($subs) {

		$sis_id = "";
		$lms_pass = "";
		$lab_user = "";
		$lab_pass = "";

		// Check if this user has any old enrollments; if yes, use the same SIS ID Lab user ID and lMS and Lab passwords for new enrollments
		$res_enr = db_query("SELECT sis_id, lms_pass, lab_user, lab_pass FROM user_enrollment WHERE user_id=".$subs["user_id"]." LIMIT 1");
		if (isset($res_enr[0])) {

			$sis_id = $res_enr[0]["sis_id"];
			if (strlen($res_enr[0]["lms_pass"]) > 0) {
				$lms_pass = $res_enr[0]["lms_pass"];
			}
			else {
				$lms_pass = "Jigsaw".$user["name"][0].rand(1000, 9999);
			}
			$lab_user = $res_enr[0]["lab_user"];
			if (strlen($res_enr[0]["lab_pass"]) > 0) {
				$lab_pass = $res_enr[0]["lab_pass"];
			}
			else {
				$lab_pass = $lms_pass;
			}

		}
		else {

			$login = db_query("SELECT * FROM platform_login WHERE platform_id = 1 AND user_id = ".$subs["user_id"]);
			if (!empty($login)) {

				$sis_id = $login[0]["sis_id"];
				$lms_pass = $login[0]["lms_pass"];
				$lab_user = $login[0]["lab_user"];
				$lab_pass = $login[0]["lab_pass"];

			}
			else {

				$user = user_get_by_id($subs["user_id"]);

				// Get the last SIS ID used and set a new one for this user
				$last_sis_id = setting_get("sis_id.last", "Jig10000");
				// Set a new username and password
				$sis_id = $lab_user = "Jig".(substr($last_sis_id, 3) + 1);
				$lms_pass = $lab_pass = "Jigsaw".$user["name"][0].rand(1000, 9999);
				// Update this SIS ID in the database as the last used SIS ID
				setting_set("sis_id.last", $sis_id);

			}

		}

		// If the subscription is a corporate subscription, we need to set the LMS password too
		if (empty($subs["corp"])) {
			$lms_pass = "";
		}

		return ["user_id" => $subs["user_id"], "subs_id" => $subs["subs_id"], "sis_id" => $sis_id, "lms_pass" => $lms_pass, "lab_user" => $lab_user, "lab_pass" => $lab_pass];

	}

	function bundle_identify_type($bundle_id) {

		$bundle = db_query("SELECT b.bundle_type, m.category FROM course_bundle AS b INNER JOIN course_bundle_meta AS m ON m.bundle_id = b.bundle_id WHERE b.bundle_id = ".$bundle_id);
		if (empty($bundle)) {
			return false;
		}

		$bundle = $bundle[0];
		if ($bundle["bundle_type"] == "bootcamps") {
			return [$bundle_id, "bootcamps"];
		}

		if (empty($bundle["category"])) {
			return false;
		}
		$categories = explode(";", $bundle["category"]);
		foreach ($categories as $category) {

			if ($category == "full-stack") {
				return [$bundle_id, "full-stack"];
			}

		}

		return false;

	}

	function bundle_identify_full_stack($bundle_id) {

		$bundle = db_query("SELECT category FROM course_bundle_meta WHERE bundle_id = ".$bundle_id);
		if (empty($bundle)) {
			return false;
		}
		$bundle = $bundle[0];
		if (empty($bundle["category"])) {
			return false;
		}
		$categories = explode(";", $bundle["category"]);
		foreach ($categories as $category) {

			if ($category == "full-stack") {
				return $bundle_id;
			}

		}
		return false;

	}

	function enr_sequence($combo, $start_date) {

		$courses = explode(";", $combo);
		$course_ids = [];
		$il_course_ids = [];

		foreach ($courses as $course) {

			$enr = explode(",", $course);
			if ($enr[1] == 1) {
				$il_course_ids[] = $enr[0];
			}
			$course_ids[] = $enr[0]."";

		}

		$sequence = db_query("SELECT native_id FROM system_persistence_map WHERE native_id IN (".implode(",", $course_ids).")  AND layer='enr_sequence' ORDER BY CAST(ext_id AS SIGNED) ASC");
		$enr_seq = [];
		$sequenced_ids = [];
		$il_start_days_offset = 0;
		foreach ($sequence as $enr) {

			$lm = 2;
			if (in_array($enr["native_id"], $il_course_ids)) {

				$lm = 1;
				$interval = "P".(180 * $il_start_days_offset)."D";
				$start_date->add(new DateInterval($interval));
				$il_start_days_offset = 1;

			}

			$enr_seq[] = ["id" => $enr["native_id"], "lm" => $lm, "start_date" => $start_date];
			$sequenced_ids[] = $enr["native_id"];

		}

		foreach ($course_ids as $course_id) {

			if (!in_array($course_id, $sequenced_ids)) {
				$enr_seq[] = ["id" => $enr["native_id"], "lm" => (in_array($enr["native_id"], $il_course_ids) ? 1 : 2), "start_date" => $start_date];
			}

		}

		return $enr_seq;

	}

	function enr_sequence_ml($combo, $start_date, $bundle_id = null, $combo_free = "", $bootcamp_info, $subs_id) {

		$course_ids = [];
		$course_learn_modes = [];
		$course_durations = [];
		$date_sequence = [];
		$sequenced_ids = [];
		$enr_seq = [];

		if (!empty($combo_free)) {
			$combo .= ";".$combo_free;
		}

		$courses = explode(";", $combo);
		foreach ($courses as $course) {

			$course_info = explode(",", $course);
			$course_ids[] = $course_info[0];
			$course_learn_modes[$course_info[0]] = $course_info[1];

		}

		$is_sequenced = false;
		$course_ids_for_learn_modes = [];

		if (!empty($bundle_id)) {

			// $bundle_sequence = content_get("bundle", $bundle_id);
			$bundle_sequence = false;
			if ($bundle_sequence !== false) {

				$course_durations = json_decode($bundle_sequence, true);
				foreach ($course_durations as $course_info) {
					$course_ids_for_learn_modes[] = $course_info["course_id"];
				}
				$course_ids_for_learn_modes = implode(",", $course_ids_for_learn_modes);
				$course_learn_modes_temp = db_query("SELECT course_id, learn_modes FROM course WHERE course_id IN ($course_ids_for_learn_modes);");
				$course_ids_for_learn_modes = [];
				foreach ($course_learn_modes_temp as $course) {
					$course_ids_for_learn_modes[$course["course_id"]] = $course["learn_modes"];
				}

				if ($combo_free !== "") {

					$combo_free = explode(";", $combo_free);
					foreach ($combo_free as $course) {
						$free_ids[] = explode(",", $course)[0];
					}

					$free_course_durations = db_query("SELECT course_id, ml_duration_length AS duration_length, ml_duration_unit AS duration_unit, ml_dependency, ml_duration_length_alt, ml_duration_unit_alt, learn_modes FROM course WHERE course_id IN (".implode(",", $free_ids).");");

					if (empty($free_course_durations)) {

						foreach ($free_ids as $free_course) {
							$free_course_durations[] = ["course_id" => $free_course, "duration_unit" => "weeks", "duration_length" => 3];
						}

					}

					$course_durations = array_merge($course_durations, $free_course_durations);

				}

				$is_sequenced = true;

			}
			else {
				$course_durations = db_query("SELECT course_id, ml_duration_length AS duration_length, ml_duration_unit AS duration_unit, ml_dependency, ml_duration_length_alt, ml_duration_unit_alt, learn_modes FROM course WHERE course_id IN (".implode(",", $course_ids).");");
			}

		}
		else {
			$course_durations = db_query("SELECT course_id, ml_duration_length AS duration_length, ml_duration_unit AS duration_unit, ml_dependency, ml_duration_length_alt, ml_duration_unit_alt, learn_modes FROM course WHERE course_id IN (".implode(",", $course_ids).");");
		}

		foreach ($course_durations as $course) {

			if (empty($course["learn_modes"])) {

				if (!empty($course_ids_for_learn_modes[$course["course_id"]])) {
					$learn_mode = $course_ids_for_learn_modes[$course["course_id"]];
				}
				else {
					$learn_mode = 3;
				}

			}
			else {

				$learn_modes = explode(";", $course["learn_modes"]);
				$ml = false;
				$il = false;
				$custom_il = false;
				foreach ($learn_modes as $learn_mode) {

					if ($learn_mode == 3) {
						$ml = true;
					}
					if ($learn_mode == 1) {
						$il = true;
					}
					if ($learn_mode == 4) {
						$custom_il = true;
					}

				}

				if ($custom_il) {
					$learn_mode = 4;
				}
				else if ($ml) {
					$learn_mode = 3;
				}
				else {

					$learn_mode = ($course_info[$course["course_id"]] == 1 && $il ? 1 : 2);
					$course["duration_length"] = 0;
					$course["duration_unit"] = "weeks";
					$course["ml_dependency"] = [];

				}

			}

			$course_learn_modes[$course["course_id"]] = $learn_mode;

			if (!empty($course["ml_dependency"])) {

				$dependencies = explode(";", $course["ml_dependency"]);
				if (!empty(array_intersect($course_ids, $dependencies))) {

					$course["duration_length"] = $course["ml_duration_length_alt"];
					$course["duration_unit"] = $course["ml_duration_unit_alt"];

				}

			}

			if (isset($course["duration_unit"]) && $course["duration_unit"] == "days") {
				$date_sequence[$course["course_id"]] = $course["duration_length"];
			}
			else if (isset($course["duration_unit"]) && $course["duration_unit"] == "months") {
				$date_sequence[$course["course_id"]] = $course["duration_length"] * 30;
			}
			else if (isset($course["duration_unit"]) && $course["duration_unit"] == "weeks") {
				$date_sequence[$course["course_id"]] = $course["duration_length"] * 7;
			}

			if ($is_sequenced) {
				$sequence[]["native_id"] = $course["course_id"];
			}

		}

		if (!$is_sequenced) {
			$sequence = db_query("SELECT native_id FROM system_persistence_map WHERE native_id IN (".implode(",", $course_ids).")  AND layer='enr_sequence' ORDER BY CAST(ext_id AS SIGNED) ASC");
		}

		if (!empty($bootcamp_info)) {

			if (intval($bootcamp_info["is_bootcamp"]) == 1) {

				if ($start_date < $bootcamp_info["batch_start_date"]) {
					$start_date = $bootcamp_info["batch_start_date"];
				}

			}

		}

		// foreach ($sequence as $enr) {

		// 	$enr_seq[] = ["id" => $enr["native_id"], "start_date" => (clone $start_date), "learn_mode" => $course_learn_modes[$enr["native_id"]]];
		// 	if (empty($date_sequence[$enr["native_id"]])) {
		// 		$date_sequence[$enr["native_id"]] = 0;
		// 	}

		// 	$interval = "P".$date_sequence[$enr["native_id"]]."D";
		// 	$start_date->add(new DateInterval($interval));
		// 	$sequenced_ids[] = $enr["native_id"];

		// }

		foreach ($course_ids as $course_id) {

			if (!in_array($course_id, $sequenced_ids)) {

				if (!isset($date_sequence[$course_id]) || !is_numeric($date_sequence[$course_id])) {

					// OLD:: Assume 4 weeks & go on
					// NEW:: Don't assume anything, but go on
					$date_sequence[$course_id] = "0";
					notify_code_failure("enr.sequence.ml", ["course_id" => $course_id, "bundle_id" => $bundle_id, "subs_id" => $subs_id]);

				}

				$start_date->add(new DateInterval("P".$date_sequence[$course_id]."D"));
				$enr_seq[] = ["id" => $course_id, "start_date" => (clone $start_date), "learn_mode" => $course_learn_modes[$course_id]];

			}

		}

		return $enr_seq;

	}

	function enr_sequence_bootcamp($combo, $combo_free, $start_date, $bundle_id, $batch_id) {

		$courses = [];

		$bundle_combo = db_query("SELECT combo FROM course_bundle WHERE bundle_id = ".$bundle_id)[0]["combo"];
		$bundle_combo = explode(";", $bundle_combo);
		foreach ($bundle_combo as $course) {
			$courses[] = explode(",", $course)[0];
		}

		$combo = explode(";", $combo);
		foreach ($combo as $course) {

			$course = explode(",", $course)[0];
			if (!in_array($course, $courses)) {
				$courses[] = $combo;
			}

		}
		if (!empty($combo_free)) {

			$combo_free = explode(";", $combo_free);
			foreach ($combo_free as $course) {

				$course = explode(",", $course)[0];
				if (!in_array($course, $courses)) {
					$courses[] = $course;
				}

			}

		}

		$enr = [];
		foreach ($courses as $course) {
			$enr[] = ["id" => $course, "start_date" => $start_date, "learn_mode" => 3, "batch_id" => $batch_id];
		}

		return $enr;

	}

	function insert($enr_params, $sequence, $start_date, $bootcamp_info, $static_batches = "", $bundle_id = "") {

		// Sanitize values
		$sis_id = db_sanitize($enr_params["sis_id"]);
		$lms_pass = (strlen($enr_params["lms_pass"]) > 0 ? db_sanitize($enr_params["lms_pass"]) : "NULL");
		$lab_user = db_sanitize($enr_params["lab_user"]);
		$lab_pass = db_sanitize($enr_params["lab_pass"]);

		// $insert_values = [];
		$course_codes = [];

		// echo "<pre>";
		// die(var_dump($sequence));

		if (!empty($static_batches)) {
			$static_batches = json_decode($static_batches, true);
		}

		$i = 0;
		foreach ($sequence as $enr) {

			$course_sis_id = db_query("SELECT sis_id FROM course WHERE course_id = ".$enr["id"]);
			$course_sis_id = $course_sis_id[0];
			if (strtolower($course_sis_id["sis_id"]) == "skipthiscourse") {
				continue;
			}

			$course_codes[] = $course_sis_id["sis_id"];

			$il_start_date = "NULL";
			if (!$GLOBALS["ml_effective"]) {

				if (!empty($enr["batch_id"])) {

					$section_id = section_get_for_date_create($enr["id"], $start_date, $enr["learn_mode"], "", false, $enr["batch_id"])["id"];
					$lm = $enr["learn_mode"] == 1 ? "il" : ($enr["learn_mode"] == 2 ? "sp" : "ml");

				}
				else {

					$lm = 2;
					if ($enr["lm"] == "1") {

						if ($enr["start_date"] == $start_date) {
							$lm = 1;
						}
						else {
							$il_start_date = db_sanitize($enr["start_date"]->format("Y-m-d H:i:s"));
						}

					}

					$section_id = section_get_for_date_create($enr["id"], $start_date, $lm)["id"];
					$lm = $enr["lm"] == 2 ? "sp" : "il";

				}

			}
			else {

				$start_date = $enr["start_date"];
				if (!empty($bootcamp_info) && $i == 0) {
					$start_date = intval($bootcamp_info["batch_start_date"]->format("d")) > 15 ? $start_date : $start_date->format("Y-m")."-15 00:00:00";
				}

				if (!empty($enr["learn_mode"])) {

					$section_id = section_get_for_date_create($enr["id"], $start_date, $enr["learn_mode"], "", true)["id"];
					if ($enr["learn_mode"] == 4) {
						$lm = "il";
					}
					else {
						$lm = $enr["learn_mode"] == 1 ? "il" : ($enr["learn_mode"] == 2 ? "sp" : "ml");
					}

				}
				else {

					$section_id = section_get_for_date_create($enr["id"], $start_date, 3)["id"];
					$lm = "ml";

				}

			}

			if (!empty($static_batches)) {

				$static_start_date = "";
				foreach ($static_batches as $key => $batch) {

					$batch_start_date = date_create_from_format("Y-m-d", $batch);
					// die(json_encode(["bsd" => $batch_start_date, "sd" => $start_date]));
					if (!empty($batch_start_date) && $start_date > $batch_start_date) {
						$static_start_date = $key;
					}

				}

				if (!empty($static_start_date)) {

					if ($bundle_id == "90" || $bundle_id == "124" || $bundle_id == "97" || $bundle_id == "89") {
						$enr["learn_mode"] = 1;
					}

					$static_section = section_get_for_date_create($enr["id"], $static_start_date." 00:00:00", $enr["learn_mode"], "", true);
					if (!empty($static_section)) {
						$section_id = $static_section["id"];
					}

				}

			}

			$lm = db_sanitize($lm);

			$lab_ip = db_sanitize(lab_ip_get($enr_params["user_id"], $enr["id"]));
			// $insert_values[] = "(".$enr_params["user_id"].",".$enr_params["subs_id"].",".$enr["id"].",".$lm.",".$sis_id.",".$lms_pass.",".$section_id.",".$il_start_date.",".$lab_ip.",".$lab_user.",".$lab_pass.")";
			db_exec("INSERT INTO user_enrollment (user_id, subs_id, course_id, learn_mode, sis_id, lms_pass, section_id, ".(!empty($il_start_date) ? "il_start_date, " : "")."lab_ip, lab_user, lab_pass) VALUES (".$enr_params["user_id"].",".$enr_params["subs_id"].",".$enr["id"].",".$lm.",".$sis_id.",".$lms_pass.",".$section_id.",".$il_start_date.",".$lab_ip.",".$lab_user.",".$lab_pass.");");

			$i++;

		}

		if (!empty($course_codes)) {

			// var_dump($course_codes);

			db_exec("INSERT INTO system_log (source, data) VALUES ('enr.enr.enr', ".db_sanitize(json_encode($course_codes)).")");
			// return false;

		}

		// db_exec("INSERT INTO user_enrollment (user_id, subs_id, course_id, learn_mode, sis_id, lms_pass, section_id, il_start_date, lab_ip, lab_user, lab_pass) VALUES ".implode(",", $insert_values));

	}

	function lab_ip_get($user_id, $course_id) {

		$res_lab = db_query("SELECT lab_ip FROM user_enrollment WHERE user_id=".$user_id." AND lab_ip IN (SELECT lab_ip FROM course_lab WHERE course_id=".$course_id.")");
		if ($res_lab) {
			$lab_ip = $res_lab[0]["lab_ip"];
		}
		else {

			$res_lab = db_query("SELECT lab_id, lab_ip FROM course_lab WHERE status=1 AND course_id=".$course_id." ORDER BY user_count ASC LIMIT 1");
			if (!$res_lab) {
				$lab_ip = "";
			}
			else {

				$lab_ip = $res_lab[0]["lab_ip"];
				db_exec("UPDATE course_lab SET user_count=user_count+1 WHERE lab_id=".$res_lab[0]["lab_id"]);

			}

		}

		return $lab_ip;

	}

	function enrollments_get_by_user_id($user_id, $all = false) {
		return db_query("SELECT * FROM user_enrollment WHERE ".($all == false ? "status='active' AND " : "")."user_id=".$user_id);
	}

	function enrollments_get_by_subs_id($subs_id, $all = false) {
		return db_query("SELECT * FROM user_enrollment WHERE ".($all == false ? "status='active' AND " : "")."subs_id=".$subs_id);
	}

	function enrollments_get_by_sis_id($sis_id, $all = false) {
		return db_query("SELECT * FROM user_enrollment WHERE ".($all == false ? "status='active' AND " : "")."sis_id=".db_sanitize($sis_id));
	}

	function user_enrollment_get_by_user_course($user_id, $course_id, $status_check = true) {
		return db_query("SELECT * FROM user_enrollment WHERE ".($status_check == true ? "status='active' AND " : "")."user_id=".$user_id." AND course_id=".$course_id)[0];
	}

?>