<?php

	function bundle_get($id, $normalized = true) {

		$id = db_sanitize($id);

		$res = db_query("SELECT b.*, m.slug, m.desc, m.category, m.content, m.create_date FROM course_bundle AS b INNER JOIN course_bundle_meta AS m ON m.bundle_id = b.bundle_id WHERE b.bundle_id = $id;");
		if (empty($res)) {
			return false;
		}

		$res = $res[0];

		if ($normalized) {
			return bundle_normalize($res);
		}

		return $res;

	}

	function bundles_all($formattedDates = false, $segregate = false) {

		$courses = [];
		$noCode = 0;

		$create_date = "m.create_date";
		if ($formattedDates) {
			$create_date = "DATE_FORMAT(m.create_date, '%e %b, %Y %I:%i %p') AS create_date";
		}

		$bundles = [];
		$count = 0;

		$res = db_query("SELECT b.*, m.slug, m.desc, m.category, m.content, $create_date FROM course_bundle AS b LEFT JOIN course_bundle_meta AS m ON m.bundle_id = b.bundle_id;");
		foreach ($res as $bundle) {

			$bundle = bundle_normalize($bundle);

			if (!$segregate) {
				$bundles[] = $bundle;
			}
			else {
				$bundles[$bundle["bundle_type"]][] = $bundle;
			}

			$count++;

		}

		return [$bundles, $count];

	}

	function program_create($bundle) {

		$bundle = bundle_sanitize($bundle, true);

		$columns = ["bundle_type", "name", "combo", "status", "subs_duration_unit"];
		$values = [
			$bundle["bundle_type"],
			$bundle["name"],
			$bundle["combo"],
			$bundle["status"],
			$bundle["subs_duration_unit"]
		];

		if (!empty($bundle["price_inr"])) {

			$columns[] = "price_inr";
			$values[] = $bundle["price_inr"];

		}
		if (!empty($bundle["price_usd"])) {

			$columns[] = "price_usd";
			$values[] = $bundle["price_usd"];

		}
		if (!empty($bundle["subs_duration_length"])) {

			$columns[] = "subs_duration_length";
			$values[] = $bundle["subs_duration_length"];

		}
		if (!empty($bundle["iot_kit"])) {

			$columns[] = "iot_kit";
			$values[] = $bundle["iot_kit"];

		}
		if (!empty($bundle["receipt_type"])) {

			$columns[] = "receipt_type";
			$values[] = $bundle["receipt_type"];

		}

		$columns = implode(", ", $columns);
		$values = implode(", ", $values);

		db_exec("INSERT INTO course_bundle ($columns) VALUES ($values);");
		$bundle_id = db_get_last_insert_id();

		$columns = ["bundle_id", "create_date", "create_id"];
		$values = [$bundle_id, "CURRENT_TIMESTAMP", $_SESSION["user"]["user_id"] ?? 0];
		if (!empty($bundle["category"])) {

			$columns[] = "category";
			$values[] = $bundle["category"];

		}

		$columns = implode(", ", $columns);
		$values = implode(", ", $values);

		db_exec("INSERT INTO course_bundle_meta ($columns) VALUES ($values);");

		return bundle_get($bundle_id);

	}

	function bundle_edit($bundle) {

		$bundle_id = $bundle["bundle_id"];
		$bundle = bundle_sanitize($bundle);

		$set = [
			"name = ".$bundle["name"],
			"bundle_type = ".$bundle["bundle_type"],
			"combo = ".$bundle["combo"],
			"status = ".$bundle["status"],
			"subs_duration_unit = ".$bundle["subs_duration_unit"]
		];

		if (!empty($bundle["price_inr"])) {
			$set[] = "price_inr = ".$bundle["price_inr"];
		}
		if (!empty($bundle["price_usd"])) {
			$set[] = "price_usd = ".$bundle["price_usd"];
		}
		if (!empty($bundle["subs_duration_length"])) {
			$set[]  = "subs_duration_length = ".$bundle["subs_duration_length"];
		}
		if (!empty($bundle["iot_kit"])) {
			$set[] = "iot_kit = ".$bundle["iot_kit"];
		}
		if (!empty($bundle["receipt_type"])) {
			$set[] = "receipt_type = ".$bundle["receipt_type"];
		}

		$set = implode(", ", $set);
		db_exec("UPDATE course_bundle SET $set WHERE bundle_id = ".$bundle["bundle_id"].";");

		$set = [];
		if (!empty($bundle["category"])) {
			$set[] = "category = ".$bundle["category"];
		}

		$set = implode(", ", $set);
		db_exec("UPDATE course_bundle_meta SET $set WHERE bundle_id = ".$bundle["bundle_id"].";");

		return bundle_get($bundle_id);

	}

	function bundle_sanitize($bundle, $new = false) {

		if (!$new) {

			if (empty($bundle["bundle_id"])) {
				return false;
			}

			$bundle["bundle_id"] = db_sanitize($bundle["bundle_id"]);

		}

		$bundle["name"] = db_sanitize($bundle["name"]);

		if (empty($bundle["category"])) {
			$bundle["category"] = [];
		}
		else if (!is_array($bundle["category"])) {
			$bundle["category"] = [$bundle["category"]];
		}

		if ($bundle["bundle_type"] == "full stack") {

			$bundle["bundle_type"] = "specialization";
			$bundle["category"][] = "full-stack";

		}

		$bundle["bundle_type"] = db_sanitize($bundle["bundle_type"]);
		$bundle["category"] = db_sanitize(implode(";", $bundle["category"]));

		$price = false;
		if (!empty($bundle["price_inr"])) {

			$bundle["price_inr"] = db_sanitize($bundle["price_inr"]);
			$price = true;

		}
		else {
			unset($bundle["price_inr"]);
		}
		if (!empty($bundle["price_usd"])) {

			$bundle["price_usd"] = db_sanitize($bundle["price_usd"]);
			$price = true;

		}
		else {
			unset($bundle["price_usd"]);
		}

		$status = "enabled";
		if (!$price) {
			$status = "draft";
		}

		if (!empty($bundle["combo"])) {

			$combo = [];
			foreach ($bundle["combo"] as $course) {

				if (!empty($course["removed"])) {
					continue;
				}

				$combo[] = $course["id"].",".$course["lm"];

			}

			$bundle["combo"] = db_sanitize(implode(";", $combo));

		}
		else {

			if ($new) {
				$bundle["combo"] = db_sanitize("");
			}

			$status = "draft";

		}

		if (!empty($bundle["combo_free"])) {
			$bundle["combo_free"] = db_sanitize($bundle["combo_free"]);
		}
		else {
			unset($bundle["combo_free"]);
		}

		if (!empty($bundle["subs_duration_length"])) {
			$bundle["subs_duration_length"] = db_sanitize($bundle["subs_duration_length"]);
		}
		else {
			unset($bundle["subs_duration_length"]);
		}

		if (!empty($bundle["subs_duration_unit"])) {
			$bundle["subs_duration_unit"] = db_sanitize(strtolower($bundle["subs_duration_unit"]));
		}
		else {
			$bundle["subs_duration_unit"] = db_sanitize("months");
		}

		if (isset($bundle["iot_kit"])) {
			$bundle["iot_kit"] = db_sanitize(intval(boolval($bundle["iot_kit"])));
		}

		if (!empty($bundle["receipt_type"])) {
			$bundle["receipt_type"] = db_sanitize($bundle["receipt_type"]);
		}
		else {
			unset($bundle["receipt_type"]);
		}

		if (!empty($bundle["status"])) {

			if ($status == "draft") {
				$bundle["status"] = "draft";
			}

		}
		else {
			$bundle["status"] = $status;
		}
		$bundle["status"] = db_sanitize($bundle["status"]);

		return $bundle;

	}

	function bundle_normalize($bundle, $bundle_type = "bundle_type") {

		$bundle["price_inr"] = intval($bundle["price_inr"]);
		$bundle["price_usd"] = intval($bundle["price_usd"]);

		if (!empty($bundle["content"])) {
			$bundle["content"] = json_decode($bundle["content"], true);
		}

		$bundle["category"] = explode(";", $bundle["category"]);

		if (!empty($bundle["combo"])) {

			$combo = explode(";", $bundle["combo"]);
			$bundle["combo"] = [];
			foreach ($combo as $each) {

				$course_info = explode(",", $each);
				$bundle["combo"][] = ["id" => $course_info[0], "lm" => intval($course_info[1])];

			}

		}
		else {
			$bundle["combo"] = [];
		}

		if (in_array("full-stack", $bundle["category"])) {

			$bundle[$bundle_type] = "full stack";
			$bundle["bundle_type_preserved"] = "full_stack";
			if (count($bundle["category"]) == 1) {
				$bundle["category"] = [];
			}

		}

		return $bundle;

	}

?>