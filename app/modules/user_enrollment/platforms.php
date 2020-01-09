<?php

	function create_enrollments($subs) {

		list($user_id, $subs_id, $sis_id, $lms_pass) = sanitize_fields($subs);

		$courses = get_courses_from_combo($subs["combo"]);
		foreach ($courses as $course) {

			$course_id = db_sanitize($course);
			$values[] = "($user_id, $subs_id, $course_id, 'sp', $sis_id, $lms_pass, 'ul', 0)";

		}

		$values = implode(", ", $values);

		db_exec("INSERT INTO user_enrollment (user_id, subs_id, course_id, learn_mode, sis_id, lms_pass, sis_status, shall_notify) VALUES $values;");
		db_exec("UPDATE subs SET status = 'active' WHERE subs_id = $subs_id;");

	}

	function sanitize_fields($subs) {

		list($sis_id, $lms_pass) = get_sis_id($subs);
		return [db_sanitize($subs["user_id"]), db_sanitize($subs["subs_id"]), db_sanitize($sis_id), db_sanitize($lms_pass)];

	}

	function get_sis_id($subs) {

		$sis_id = "";
		$lms_pass = "";

		$res_enr = db_query("SELECT sis_id, lms_pass, lab_user, lab_pass FROM user_enrollment WHERE user_id=".$subs["user_id"]." LIMIT 1;");
		if (isset($res_enr[0])) {

			$sis_id = $res_enr[0]["sis_id"];
			if (strlen($res_enr[0]["lms_pass"]) > 0) {
				$lms_pass = $res_enr[0]["lms_pass"];
			}
			else {
				$lms_pass = bin2hex(random_bytes(4));
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

			$user = user_get_by_id($subs["user_id"]);

			// Get the last SIS ID used and set a new one for this user
			$last_sis_id = setting_get("sis_id.last", "Jig10000");
			// Set a new username and password
			$sis_id = $lab_user = "Jig".(substr($last_sis_id, 3) + 1);
			$lms_pass = $lab_pass = "Jigsaw".$user["name"][0].rand(1000, 9999);
			// Update this SIS ID in the database as the last used SIS ID
			setting_set("sis_id.last", $sis_id);

		}

		return [$sis_id, $lms_pass];

	}

	function get_courses_from_combo($combo) {

		$courses = [];
		$combo = explode(";", $combo);
		foreach ($combo as $course) {

			$course = explode(",", $course);
			$courses[] = $course[0];

		}

		return $courses;

	}

?>