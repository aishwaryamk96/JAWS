<?php

	// Init Session
	auth_session_init();

	// Auth Check - Expecting Session Only !
	if ((!auth_session_is_logged()) || (!auth_session_is_allowed("enrollment.get.adv"))) {
		header("HTTP/1.1 401 Unauthorized");
		die();
	}

	// Send Headers
	header('Access-Control-Allow-Credentials: true');
	header("Content-Type: application/json");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Pragma-directive: no-cache");
	header("Cache-directive: no-cache");
	header("Cache-Control: private, must-revalidate, max-age=0, no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
	header("Pragma: no-cache");

	if (!auth_session_is_allowed("enrollment.get.adv")) {
		die(json_encode(["status" => false, "error" => "Are you trying to hack...? Coz, its not gonna work."]));
	}

	if (empty($_POST)) {
		$_POST = json_decode(file_get_contents("php://input"), true);
	}

	if (!isset($_POST["id"])) {

		header("HTTP/1.1 400 Bad Request");
		die();

	}

	load_module("user_enrollment");

	$subs_id = $_POST["id"];
	$remove_ids = $_POST["remove"] ?? [];

	$logs = [
		"removed" => [],
		"added" => []
	];

	$remove_course_ids = [];
	if (!empty($remove_ids)) {

		$res_remove_course_ids = db_query("SELECT enr.course_id, course.name FROM user_enrollment AS enr INNER JOIN course ON course.course_id = enr.course_id WHERE enr.enr_id IN (".implode(",", $remove_ids).");");
		foreach ($res_remove_course_ids as $remove_course_id) {

			$remove_course_ids[] = $remove_course_id["course_id"];
			$logs["removed"][] = $remove_course_id["name"];

		}

	}

	$new_courses = $_POST["add"] ?? [];

	$res_subs = db_query("SELECT subs.user_id, subs.combo, subs.combo_free, subs.status, enr.*, section.start_date FROM subs INNER JOIN user_enrollment AS enr ON enr.subs_id = subs.subs_id INNER JOIN course_section AS section ON section.id = enr.section_id WHERE subs.subs_id = ".db_sanitize($subs_id)." ORDER BY section.start_date DESC");
	if (empty($res_subs)) {
		die(json_encode(["status" => false, "error" => "Invalid subs ID"]));
	}

	$user_id = $res_subs[0]["user_id"];
	$sis_id = $res_subs[0]["sis_id"];
	$lms_pass = $res_subs[0]["lms_pass"] ?? null;
	$lab_user = $res_subs[0]["lab_user"];
	$lab_pass = $res_subs[0]["lab_pass"];

	$combo = $res_subs[0]["combo"];
	$combo_free= $res_subs[0]["combo_free"];

	$active_courses = [];

	$combo_new = [];
	$combo_free_new = [];

	$combo = explode(";", $combo);
	foreach ($combo as $enr) {

		$course = explode(",", $enr);
		if (!in_array($course[0], $remove_course_ids)) {

			$combo_new[] = $enr;
			$active_courses[] = $course[0];

		}

	}

	$combo_free = explode(";", $combo_free);
	foreach ($combo_free as $enr) {

		if (empty(trim($enr))) {
			continue;
		}

		$course = explode(",", $enr);
		if (!in_array($course[0], $remove_course_ids)) {
			$combo_free_new[] = $enr;
		}

	}

	$new_course_ids = [];
	$auto_batch_course_ids = [];
	$defined_batch_course_ids = [];

	$final_sequence = [];

	if (!empty($new_courses)) {

		$active_courses_to_find = [];
		foreach ($new_courses as $course) {

			$course_id = $course["id"];
			$active_courses_to_find[] = $course_id;
			$new_course_ids["c".$course_id] = $course;

		}

		$active_courses_found = [];

		$new_courses_found = db_query("SELECT course_id, name FROM course WHERE course_id IN (".implode(", ", $active_courses_to_find).");");
		foreach ($new_courses_found as $new_course) {

			$active_courses_found[] = $new_course["course_id"];
			$logs["added"][] = $new_course["name"];

		}

		// db_exec("INSERT INTO system_log (source, data) VALUES ('subs.edit', ".db_sanitize(json_encode(["1" => $active_courses_found, "2" => $new_course_ids])).");");
		// var_dump($active_courses_found); var_dump($new_course_ids); die;

		foreach ($active_courses_found as $course) {

			$course_id = $course;

			if ($new_course_ids["c".$course_id]["batch"]["auto"]) {
				$auto_batch_course_ids[] = $course_id;
			}
			else {
				$defined_batch_course_ids[] = $course_id;
			}

			if ($new_course_ids["c".$course_id]["isComplimentary"]) {
				$combo_free_new[] = $course_id.",2";
			}
			else {
				$combo_new[] = $course_id.",2";
			}

			$active_courses[] = $course_id;

		}

	}

	if (!empty($auto_batch_course_ids)) {

		$last_batch = $res_subs[0]["start_date"];

		$batch_info;
		$sequence;

		list($batch_info, $sequence) = process_auto_batch_courses($auto_batch_course_ids, $active_courses);
		$final_sequence = process_batches($batch_info, $sequence, $last_batch, $new_course_ids);

	}

	if (!empty($defined_batch_course_ids)) {

		foreach ($defined_batch_course_ids as $course_id) {

			$batch = $new_course_ids["c".$course_id]["batch"];
			$sis = $new_course_ids["c".$course_id]["sis"];

			$date = $batch["year"]."-".($batch["month"] + 1)."-01 00:00:00";

			$final_sequence[] = ["id" => $course_id, "batch" => date_create_from_format("Y-m-d H:i:s", $date), "sis" => $sis];

		}

	}

	create_enrolments($user_id, $subs_id, $sis_id, $lms_pass, $lab_user, $lab_pass, $final_sequence);
	update_enrollments($user_id, $subs_id, $remove_course_ids);
	update_subs($subs_id, $combo_new, $combo_free_new);

	$logs["user_id"] = $user_id;
	log_changes($logs);

	die(json_encode(["status" => true]));

	function process_auto_batch_courses($course_ids, $active_courses) {

		$batch_info = [];
		$sequence_found = [];

		$course_info = db_query("SELECT c.course_id, c.ml_duration_length, c.ml_duration_unit, c.ml_duration_length_alt, c.ml_duration_unit_alt, c.ml_dependency FROM course AS c INNER JOIN system_persistence_map AS seq ON seq.native_id = c.course_id WHERE c.course_id IN (".implode(",", $course_ids).") GROUP BY c.course_id ORDER BY CAST(seq.ext_id AS SIGNED) ASC;");
		foreach ($course_info as $course) {

			if (!empty($course["ml_dependency"])) {

				if (in_array($course["ml_dependency"], $active_courses)) {

					$course["ml_duration_length"] = $course["ml_duration_length_alt"] ?? $course["ml_duration_length"];
					$course["ml_duration_unit"] = (empty($course["ml_duration_length_alt"]) ? "weeks" : $course["ml_duration_unit_alt"]);

				}

			}

			if (empty($course["ml_duration_length"])) {

				$course["ml_duration_length"] = 2;
				$course["ml_duration_unit"] = "weeks";

			}

			$batch_info["c".$course["course_id"]] = ["ml_duration_unit" => $course["ml_duration_unit"], "ml_duration_length" => $course["ml_duration_length"]];
			$sequence_found[] = $course["course_id"];

		}

		foreach ($course_ids as $course_id) {

			if (!isset($batch_info["c".$course_id])) {

				$batch_info["c".$course_id] = ["ml_duration_unit" => "weeks", "ml_duration_length" => 2];
				$sequence_found[] = $course_id;

			}

		}

		return [$batch_info, $sequence_found];

	}

	function process_batches($batch_info, $sequence, $last_batch_date, $other_info) {

		$last_batch_date = date_create_from_format("Y-m-d H:i:s", $last_batch_date);
		$last_batch_date->add(new DateInterval("P14D"));

		$final_sequence = [];
		foreach ($sequence as $course_id) {

			$info = $batch_info["c".$course_id];

			$duration;
			if ($info["ml_duration_unit"] == "weeks") {
				$duration = intval($info["ml_duration_length"])*7;
			}
			else if ($info["ml_duration_unit"] == "months") {
				$duration = intval($info["ml_duration_length"])*30;
			}
			else {
				$duration = $info["ml_duration_length"];
			}

			$final_sequence[] = ["id" => $course_id, "batch" => clone $last_batch_date, "sis" => $other_info["c".$course_id]["sis"]];

			$interval = "P".$duration."D";
			$last_batch_date->add(new DateInterval($interval));

		}

		return $final_sequence;

	}

	function create_enrolments($user_id, $subs_id, $sis_id, $lms_pass, $lab_user, $lab_pass, $sequence) {

		$queries = [];

		foreach ($sequence as $course) {

			$section = section_get_for_date_create($course["id"], $course["batch"], 3);

			$queries[] = "(".$user_id.",".$subs_id.",".$course["id"].",".$section["id"].",'ml',".db_sanitize($sis_id).",".(!empty($lms_pass) ? db_sanitize($lms_pass) : "NULL").",".db_sanitize($lab_user).",".db_sanitize($lab_pass).",".($course["sis"] ? "'na'" : "'ul'").", 0, ".$_SESSION["user"]["user_id"].")";

		}

		db_exec("INSERT INTO user_enrollment (user_id, subs_id, course_id, section_id, learn_mode, sis_id, lms_pass, lab_user, lab_pass, sis_status, shall_notify, added_by) VALUES ".implode(",", $queries).";");

	}

	function update_enrollments($user_id, $subs_id, $course_ids) {
		db_exec("UPDATE user_enrollment SET status = 'deleted', sis_status = 'na' WHERE user_id = ".$user_id." AND subs_id = ".$subs_id." AND course_id IN (".implode(",", $course_ids).");");
	}

	function update_subs($subs_id, $combo, $combo_free) {

		if (empty($combo) && empty($combo_free)) {
			return true;
		}

		$query = "UPDATE subs SET";
		$set = [];
		if (!empty($combo)) {
			$set[] = " combo = ".db_sanitize(implode(";", $combo));
		}
		if (!empty($combo_free)) {
			$set[] = " combo_free = ".db_sanitize(implode(";", $combo_free));
		}

		$query .= implode(", ", $set)." WHERE subs_id = ".$subs_id.";";

		db_exec($query);

	}

	function log_changes($logs) {

		$description = "";
		$added = "";
		if (!empty($logs["added"])) {
			$added = implode_and($logs["added"]);
		}

		$removed = "";
		if (!empty($logs["removed"])) {
			$removed = implode_and($logs["removed"]);
		}

		if (!empty($removed) && !empty($added)) {
			$description = $removed." were swapped with ".$added.".";
		}
		else if (!empty($removed)) {
			$description = $removed." courses were removed.";
		}
		else if (!empty($added)) {
			$description = $added." courses were added.";
		}

		if (!empty($description)) {
			db_exec("INSERT INTO user_logs (user_id, category, created_by, description) VALUES (".$logs["user_id"].", 'subs.edit', ".$_SESSION["user"]["user_id"].", ".db_sanitize($description).");");
		}

	}

	function implode_and($arr) {

		$sub_arr = implode(", ", array_slice($arr, 0, -1));
		return (!empty($sub_arr) ? $sub_arr." and " : "").array_pop($arr);

	}

?>