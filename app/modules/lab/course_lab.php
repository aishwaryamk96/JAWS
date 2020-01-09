<?php

	function course_lab_add($course_lab) {

		$lab_id = db_sanitize($course_lab["lab_id"]);
		$code = db_sanitize($course_lab["code"]);
		$name = db_sanitize($course_lab["name"]);
		$lifespan = db_sanitize($course_lab["lifespan"]);
		$created_by = db_sanitize($course_lab["created_by"]);

		if (!empty($course_lab["id"])) {
			$result = db_exec("UPDATE course_labs SET lab_id = $lab_id, code = $code, name = $name, lifespan = $lifespan WHERE id = ".$course_lab["id"]);
		}
		else {
			$result = db_exec("INSERT INTO course_labs (lab_id, code, name, lifespan, created_by) VALUES ($lab_id, $code, $name, $lifespan, $created_by);");
		}

		if (!$result) {
			return false;
		}

		$course_lab["id"] = db_get_last_insert_id();
		return $course_lab;

	}

	function course_labs_get() {
		return db_query("SELECT * FROM course_labs ORDER BY id;");
	}

?>