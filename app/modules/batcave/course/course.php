<?php

	function course_get($id, $type = "id") {

		$id = db_sanitize($id);
		if ($type == "id") {
			$type = "course_id";
		}
		else if ($type == "sis_id") {
			$type = "sis_id";
		}

		$res = db_query("SELECT c.*, m.slug, m.desc, m.category, m.content, m.create_date FROM course AS c INNER JOIN course_meta AS m ON m.course_id = c.course_id WHERE c.$type = $id;");
		if (empty($res)) {
			return false;
		}

		return course_normalize($res[0]);

	}

	function courses_all($formattedDates = false, $noCodeCount = false) {

		$courses = [];
		$noCode = 0;

		$create_date = "m.create_date";
		if ($formattedDates) {
			$create_date = "DATE_FORMAT(m.create_date, '%e %b, %Y %I:%i %p') AS create_date";
		}

		$res = db_query("SELECT c.*, m.slug, m.desc, m.category, m.content, $create_date FROM course AS c LEFT JOIN course_meta AS m ON m.course_id = c.course_id;");
		foreach ($res as $course) {

			$course = course_normalize($course);

			if (empty($course["sis_id"])) {
				$noCode++;
			}

			$courses[] = $course;

		}

		if (!$noCodeCount) {
			return $courses;
		}
		else {
			return [$courses, $noCode];
		}

	}

	function course_create($course) {

		$course = course_sanitize($course, true);
		unset($course["content"]);

		$category = "";
		if (!empty($course["category"])) {

			$category = $course["category"];
			unset($course["category"]);

		}

		$columns = implode(", ", array_keys($course));
		$values = implode(", ", array_values($course));

		$create_user = $_SESSION["user"] ?? ["user_id" => 0];
		$create_user = $create_user["user_id"];

		db_exec("INSERT INTO course ($columns) VALUES ($values)");
		$course_id = db_get_last_insert_id();

		$columns = ["course_id", "create_user"];
		$values = [$course_id, $create_user];
		if (!empty($category)) {

			$columns[] = "category";
			$values[] = $category;

		}

		$columns = implode(", ", $columns);
		$values = implode(", ", $values);

		db_exec("INSERT INTO course_meta ($columns) VALUES ($values);");

		return course_get($course_id);

	}

	function course_edit($course) {

		$course_id = $course["course_id"];
		$course = course_sanitize($course);
		unset($course["content"]);

		$category = "";
		$set = [];
		$skip = ["create_date"];
		foreach ($course as $key => $value) {

			if (in_array($key, $skip)) {
				continue;
			}

			if ($key == "category") {
				$category = $value;
			}
			else if (!empty($value)) {
				$set[] = $key." = ".$value;
			}

		}

		$set = implode(", ", $set);

		db_exec("UPDATE course SET $set WHERE course_id = ".$course_id);
		if (!empty($category)) {
			db_exec("UPDATE course_meta SET category = $category WHERE course_id = ".$course_id);
		}

		return course_get($course_id);

	}

	function course_sanitize($course, $new = false) {

		if (!$new) {
			$course["course_id"] = db_sanitize($course["course_id"]);
		}

		$course["name"] = db_sanitize($course["name"]);
		if (!empty($course["sis_id"])) {
			$course["sis_id"] = db_sanitize(strtoupper($course["sis_id"]));
		}
		else {
			unset($course["sis_id"]);
		}

		$course["duration_length"] = db_sanitize($course["duration_length"]);
		$course["duration_unit"] = db_sanitize($course["duration_unit"]);

		if (!empty($course["il_code"])) {
			$course["il_code"] = db_sanitize($course["il_code"]);
		}
		if (!empty($course["sp_code"])) {
			$course["sp_code"] = db_sanitize($course["sp_code"]);
		}

		if (!empty($course["p_code"])) {

			if (empty($course["il_code"])) {
				$course["il_code"] = db_sanitize("I".$course["p_code"]);
			}
			if (empty($course["sp_code"])) {
				$course["sp_code"] = db_sanitize("V".$course["p_code"]);
			}

		}
		unset($course["p_code"]);

		if (!empty($course["il_price_inr"])) {
			$course["il_price_inr"] = db_sanitize($course["il_price_inr"]);
		}
		else {
			unset($course["il_price_inr"]);
		}
		if (!empty($course["il_price_usd"])) {
			$course["il_price_usd"] = db_sanitize($course["il_price_usd"]);
		}
		else {
			unset($course["il_price_usd"]);
		}
		if (!empty($course["sp_price_inr"])) {
			$course["sp_price_inr"] = db_sanitize($course["sp_price_inr"]);
		}
		else {
			unset($course["sp_price_inr"]);
		}
		if (!empty($course["sp_price_usd"])) {
			$course["sp_price_usd"] = db_sanitize($course["sp_price_usd"]);
		}
		else {
			unset($course["sp_price_usd"]);
		}

		$course["il_price_inr_alt"] = db_sanitize($course["il_price_inr_alt"] ?? 0);
		$course["il_price_usd_alt"] = db_sanitize($course["il_price_usd_alt"] ?? 0);
		$course["sp_price_inr_alt"] = db_sanitize($course["sp_price_inr_alt"] ?? 0);
		$course["sp_price_usd_alt"] = db_sanitize($course["sp_price_usd_alt"] ?? 0);

		$course["no_show"] = db_sanitize($course["no_show"] ?? 0);
		$course["section_meta"] = $course["section_meta"] ?? [];
		if (!empty($course["ibm_content"])) {
			$course["section_meta"]["ibm_content"] = $course["ibm_content"];
		}
		$course["section_meta"] = db_sanitize(json_encode($course["section_meta"]));
		if (!empty($course["category"])) {
			$course["category"] = db_sanitize($course["category"]);
		}

		if (!empty($course["status"])) {
			$course["status"] = db_sanitize($course["status"]);
		}
		if (!empty($course["create_date"])) {
			$course["create_date"] = db_sanitize($course["create_date"]);
		}

		unset($course["invalid"]);
		unset($course["sis_id_tip"]);
		unset($course["sis_id_tip_class"]);

		return $course;

	}

	function course_normalize($course, $sp_prices = false) {

		if (!$sp_prices) {

			$course["sp_price_inr"] = intval($course["sp_price_inr"]);
			$course["il_price_inr"] = intval($course["il_price_inr"]);
			$course["sp_price_usd"] = intval($course["sp_price_usd"]);
			$course["il_price_usd"] = intval($course["il_price_usd"]);

		}
		else {

			$course["price_inr"] = intval($course["price_inr"]);
			$course["price_usd"] = intval($course["price_usd"]);

		}

		if (!empty($course["content"])) {
			$course["content"] = json_decode($course["content"], true);
		}

		return $course;

	}

?>