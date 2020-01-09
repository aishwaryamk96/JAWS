<?php

	authorize_api_call("", true);

	if (empty($_GET)) {

		$res = courses_all(true, true);
		die(json_encode(["courses" => $res[0], "noCodeCount" => $res[1]]));

	}
	else if (!empty($_GET["id"])) {

		if (!empty(($course = course_get($_GET["id"])))) {

			$external = db_query("SELECT * FROM system_persistence_map WHERE layer = 'wppl' AND entity_type = 'course' AND native_id = ".$course["course_id"]);
			if (!empty($external)) {
				$course["ext_id"] = $external[0]["ext_id"];
			}

			$enrs = db_query("SELECT enr_id FROM user_enrollment WHERE status = 'active' AND course_id = ".$course["course_id"].";");
			$course["enr_count"] = count($enrs);

			die(json_encode(["course" => $course]));

		}

		header("HTTP/1.1 404");

	}
	else if (!empty($_GET["sis_id"])) {

		$sis_id = strtoupper($_GET["sis_id"]);
		if (!empty(($course = course_get($sis_id, "sis_id")))) {
			die(json_encode(["course" => $course]));
		}

		if (!empty($_GET["jlc"])) {

			load_plugin("jlc");
			$jlc = new JLC;
			$res = json_decode($jlc->apiNew("check_course", ["data" => ["sis_source_id" => $sis_id]]), true);
			if (!empty($res["status"])) {

				header("HTTP/1.1 404");
				die();

			}
			else {
				die(json_encode($res));
			}

		}

		header("HTTP/1.1 404");

	}
	else if (!empty($_GET["components"])) {

		$courses = [];

		$res = db_query("SELECT course_id, sis_id, name FROM course WHERE sis_id IS NOT NULL AND sis_id != '' ORDER BY course_id DESC;");
		foreach ($res as $course) {
			$courses[$course["course_id"]] = $course;
		}

		die(json_encode(["courses" => $courses]));

	}

?>