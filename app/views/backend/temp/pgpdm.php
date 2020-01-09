<?php

	load_module("user");
	load_module("user_enrollment");

	$input = file("external/temp/pgpdm.csv");

	$GLOBALS["batch"] = date_create_from_format("Y-m-d", "2018-07-01");
	$pgpdm["combo"] = "62,2;176,2;177,2;178,2;179,2;180,2;181,2;182,2;183,2;184,2;185,2";

	$combo = [];
	$course_code_map = [];
	$temp_combo = explode(";", $pgpdm["combo"]);
	foreach ($temp_combo as $course) {
		$combo[] = explode(",", $course)[0];
	}

	$courses = db_query("SELECT course_id, sis_id FROM course WHERE course_id IN (".implode(",",$combo).");");
	foreach ($courses as $course) {
		$course_code_map[$course["sis_id"]] = $course["course_id"];
	}

	foreach ($input as $line) {

		$info = explode(",", $line);

		$email = trim($info[0]);

		$user = user_get_by_email($email);
		if (empty($user)) {

			echo "USER NOT FOUND:".$email."<br>";
			continue;

		}

		// $subs = db_query("SELECT * FROM subs WHERE combo = '62,2' AND status = 'active' AND user_id = ".$user["user_id"].";");
		$subs = db_query("SELECT s.subs_id FROM subs AS s INNER JOIN subs_meta AS m ON m.subs_id = s.subs_id WHERE m.bundle_id = 104 AND s.status = 'active' AND s.user_id = ".$user["user_id"].";");
		if (!empty($subs)) {
			// $subs_id = edit_subs($subs[0], $pgpdm["combo"]);
			$subs_id = $subs[0]["subs_id"];
		}
		else {

		// 	echo "SUBS NOT FOUND:".$email."<br>";
		// 	continue;

		// }
			$subs = db_query("SELECT * FROM subs WHERE combo = '62,2' AND status = 'active' AND user_id = ".$user["user_id"].";");
			if (!empty($subs)) {
				$subs_id = edit_subs($subs[0], $pgpdm["combo"]);
			}
			else {
				$subs_id = create_subs($user["user_id"], $info, $pgpdm["combo"]);
			}

		}


		$jig_id = trim($info[1]);
		$password = trim($info[2]);
		for ($i = 6; $i < count($info); $i = $i + 2) {

			if (!empty($info[$i]) && !empty($course_code_map[trim($info[$i])])) {
				create_enr($user["user_id"], $subs_id, $jig_id, $password, $course_code_map[trim($info[$i])], trim($info[$i + 1]));
			}

		}

	}

	function create_enr($user_id, $subs_id, $jig_id, $password, $course_id, $section_code) {

		$section = get_or_create_section($course_id, $section_code);
		$section_id = $section["id"];

		$jig_id = db_sanitize($jig_id);
		$password = db_sanitize($password);

		db_exec("INSERT INTO user_enrollment (user_id, subs_id, course_id, learn_mode, section_id, sis_id, lms_pass, sis_status, status, shall_notify) VALUES ($user_id, $subs_id, $course_id, 'ml', $section_id, $jig_id, $password, 'ul', 'active', 0);");

	}

	function create_subs($user_id, $info, $combo) {

		$combo = db_sanitize($combo);
		$start_date = date_create_from_format("Y-m-d", trim($info[3]));
		$start_date = db_sanitize($start_date->format("Y-m-d H:i:s"));
		$end_date = date_create_from_format("Y-m-d", trim($info[4]));
		$end_date = db_sanitize($end_date->format("Y-m-d H:i:s"));

		db_exec("INSERT INTO subs (user_id, combo, start_date, end_date, status) VALUES ($user_id, $combo, $start_date, $end_date, 'active');");
		$subs_id = db_get_last_insert_id();
		db_exec("INSERT INTO subs_meta (subs_id, bundle_id) VALUES ($subs_id, 100);");

		return $subs_id;

	}

	function edit_subs($subs, $combo) {

		// $combo = $subs["combo"].";".$combo;
		db_exec("UPDATE subs SET combo = '$combo' WHERE subs_id = ".$subs["subs_id"]);
		db_exec("UPDATE subs_meta SET bundle_id = 104 WHERE subs_id = ".$subs["subs_id"]);

		return $subs["subs_id"];

	}

	function get_or_create_section($course_id, $section_code) {

		if (!empty($GLOBALS[$section_code])) {
			return $GLOBALS[$section_code];
		}

		$section = db_query("SELECT * FROM course_section WHERE sis_id = ".$section_code);
		if (!empty($section)) {
			return $GLOBALS[$section_code] = $section[0];
		}
		else {
			return $GLOBALS[$section_code] = section_get_for_date_create($course_id, $GLOBALS["batch"], 2, $section_code);
		}

	}

?>