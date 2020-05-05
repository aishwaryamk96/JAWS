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
		header('Location: https://www.jigsawacademy.com');
		die();
	}

	function bundle_create($bundle)
	{

		// Do some price formatting and set defaults
		$bundle = bundle_formatting_and_defaults($bundle);

		$query = "INSERT INTO course_bundle (bundle_type, name, combo, combo_free, price_inr, price_usd, status_inr, status_usd, start_date, expire_date, subs_duration_length, subs_duration_unit, status, is_bootcamp, batch_start_date, batch_end_date) VALUES (".$bundle["bundle_type"].", ".$bundle["name"].", ".$bundle["combo"].", ".$bundle["combo_free"].", ".$bundle["price_inr"].", ".$bundle["price_usd"].", ".$bundle["status_inr"].", ".$bundle["status_usd"].", ".$bundle["start_date"].", ".$bundle["expire_date"].", ".$bundle["subs_duration_length"].", ".$bundle["subs_duration_unit"].", ".$bundle["status"].", ".$bundle["is_bootcamp"].", ".$bundle["batch_start_date"].", ".$bundle["batch_end_date"].");";

		db_exec($query);

		// Get the ID for this bundle and set to bundle_id of the bundle array
		$bundle["bundle_id"] = db_get_last_insert_id();

		// If the record has not been saved in the table skip course_bundle_meta insertion, still return the array to let caller know that insertion has failed
		if ($bundle["bundle_id"] == 0)
			return $bundle;

		// Insert into bundle_meta
		$create_date = db_sanitize(date("Y-m-d H:i:s"));
		$query = "INSERT INTO course_bundle_meta (bundle_id, slug, `desc`, ".((isset($bundle["category"]) && strlen($bundle["category"]) > 0) ? "category, " : "")."content, template_path, create_id, create_date) VALUES (".$bundle["bundle_id"].", ".$bundle["slug"].", ".$bundle["description"].", ".((isset($bundle["category"]) && strlen($bundle["category"]) > 0) ? $bundle["category"].", " : "").$bundle["content"].", ".$bundle["template_path"].", 0, ".$create_date.");";
		db_exec($query);

		return $bundle;

	}

	function bundle_update($bundle)
	{

		// Do some price formatting and set defaults
		$bundle = bundle_formatting_and_defaults($bundle);

		$query = "UPDATE course_bundle SET bundle_type=".$bundle["bundle_type"].", name=".$bundle["name"].", combo=".$bundle["combo"].", combo_free=".$bundle["combo_free"].", price_inr=".$bundle["price_inr"].", price_usd=".$bundle["price_usd"].", status_inr=".$bundle["status_inr"].", status_usd=".$bundle["status_usd"].", start_date=".$bundle["start_date"].", expire_date=".$bundle["expire_date"].", subs_duration_length=".$bundle["subs_duration_length"].", subs_duration_unit=".$bundle["subs_duration_unit"].", status=".$bundle["status"].", is_bootcamp=".$bundle["is_bootcamp"].", batch_start_date=".$bundle["batch_start_date"].", batch_end_date=".$bundle["batch_end_date"]." WHERE bundle_id=".$bundle["bundle_id"].";";

		db_exec($query);

		// Update course_meta also
		$query = "UPDATE course_bundle_meta SET slug=".$bundle["slug"].", `desc`=".$bundle["description"].", ".((isset($bundle["category"]) && strlen($bundle["category"]) > 0) ? "category=".$bundle["category"].", " : "")."content=".$bundle["content"].", template_path=".$bundle["template_path"]." WHERE bundle_id=".$bundle["bundle_id"].";";
		db_exec($query);

	}

	function bundle_formatting_and_defaults($bundle)
	{
		$bundle["bundle_type"] = db_sanitize($bundle["bundle_type"]);
		$bundle["name"] = db_sanitize($bundle["name"]);
		$bundle["slug"] = db_sanitize($bundle["slug"] ?? "");
		$bundle["combo"] = db_sanitize($bundle["combo"]);

		if (!isset($bundle["combo_free"]) || strlen($bundle["combo_free"]) == 0) $bundle["combo_free"] = "";
		$bundle["combo_free"] = db_sanitize($bundle["combo_free"]);

		// Set prices to NULL if not present
		if (!isset($bundle["price_inr"]) || strlen($bundle["price_inr"] == 0)) $bundle["price_inr"] = "NULL";
		if (!isset($bundle["price_usd"]) || strlen($bundle["price_usd"] == 0)) $bundle["price_usd"] = "NULL";

		// Set start_date and expire_date to blank
		if (!isset($bundle["start_date"]) || strlen($bundle["start_date"]) == 0) $bundle["start_date"] = "";
		$bundle["start_date"] = db_sanitize($bundle["start_date"]);
		if (!isset($bundle["expire_date"]) || strlen($bundle["expire_date"]) == 0) $bundle["expire_date"] = "";
		$bundle["expire_date"] = db_sanitize($bundle["expire_date"]);

		// Set duration to NULL if not present
		if (!isset($bundle["subs_duration_length"]) || strlen($bundle["subs_duration_length"]) == 0) $bundle["subs_duration_length"] = "NULL";
		if (!isset($bundle["subs_duration_unit"]) || strlen($bundle["subs_duration_unit"]) == 0) $bundle["subs_duration_unit"] = "NULL";
		$bundle["subs_duration_unit"] = db_sanitize($bundle["subs_duration_unit"]);

		// Set the statuses if not present
		$bundle["status"] = db_sanitize($bundle["status"]);
		if (!isset($bundle["status_inr"]) || strlen($bundle["status_inr"]) == 0) {

			if (strcmp($bundle["price_inr"], "NULL") == 0) {
				$bundle["status_inr"] = 0;
			}
			else {
				$bundle["status_inr"] = 1;
			}

		}
		if (!isset($bundle["status_usd"]) || strlen($bundle["status_usd"]) == 0) {

			if (strcmp($bundle["price_usd"], "NULL") == 0) {
				$bundle["status_usd"] = 0;
			}
			else {
				$bundle["status_usd"] = 1;
			}

		}

		// Precautionary measure for some currency values which might have a comma in them... (who does that?!)
		if (strpos($bundle["price_inr"], ",")) {
			$bundle["price_inr"] = str_replace(",", "", $bundle["price_inr"]);
		}
		if (strpos($bundle["price_usd"], ",")) {
			$bundle["price_usd"] = str_replace(",", "", $bundle["price_usd"]);
		}

		if (!isset($bundle["description"]) || strlen($bundle["description"]) == 0) $bundle["description"] = "NULL";
		else $bundle["description"] = db_sanitize($bundle["description"]);
		if (!isset ($bundle["template_path"]) || strlen($bundle["template_path"]) == 0) $bundle["template_path"] = "NULL";
		else $bundle["template_path"] = db_sanitize($bundle["template_path"]);
		if (!isset($bundle["content"]) || strlen($bundle["content"]) == 0) $bundle["content"] = "NULL";
		else $bundle["content"] = db_sanitize($bundle["content"]);
		if (isset($bundle["category"]) && strlen($bundle["category"]) > 0) $bundle["category"] = db_sanitize($bundle["category"]);

		if (intval($bundle["is_bootcamp"]) > 0) {

			$bundle["is_bootcamp"] = 1;
			$batch_start_date = date_create_from_format("Ymd", $bundle["batch_start_date"]);
			$batch_end_date = date_create_from_format("Ymd", $bundle["batch_end_date"]);
			if ($batch_start_date === false) {

				$bundle["is_bootcamp"] = 0;
				$bundle["batch_start_date"] = "NULL";

			}
			else {
				$bundle["batch_start_date"] = db_sanitize($batch_start_date->format("Y-m-d"));
			}

			if ($batch_end_date === false) {
				$bundle["batch_end_date"] = "NULL";
			}
			else {
				$bundle["batch_end_date"] = db_sanitize($batch_end_date->format("Y-m-d"));
			}

		}
		else {

			$bundle["is_bootcamp"] = 0;
			$bundle["batch_start_date"] = "NULL";
			$bundle["batch_end_date"] = "NULL";

		}

		return $bundle;
	}

	function specalization_get_info_all() {

		$res_specs = db_query("SELECT * FROM course_bundle WHERE bundle_type = 'specialization' AND status <> 'disabled' AND combo <> '';");
		$specs = array();
		foreach ($res_specs as $spec) {

			if (!empty($spec["electives"])) {

				$electives = explode(";", $spec["electives"]);
				$courses = db_query("SELECT name FROM course WHERE course_id IN (".implode(", ", $electives).");");
				$elective_str = [];
				foreach ($courses as $course) {
					$elective_str[] = $course["name"];
				}
				$spec["electives"] = $electives;
				$spec["electives_str"] = $elective_str;

			}

			$spec_meta = db_query("SELECT * FROM course_bundle_meta WHERE bundle_id=".$spec["bundle_id"]);
			$spec["meta"] = $spec_meta[0];
			if ($spec["meta"]["category"] != "full-stack") {
				$specs[] = $spec;
			}

		}

		return $specs;

	}

	function specalization_get_info_all_desc() {

		$res_specs = db_query("SELECT * FROM course_bundle WHERE bundle_type = 'specialization' AND status != 'disabled' AND status != 'expired' AND combo != '' ORDER BY position DESC, bundle_id DESC;");
		$specs = array();
		foreach ($res_specs as $spec) {

			if (!empty($spec["electives"])) {

				$electives = explode(";", $spec["electives"]);
				$courses = db_query("SELECT name FROM course WHERE course_id IN (".implode(", ", $electives).");");
				$elective_str = [];
				foreach ($courses as $course) {
					$elective_str[] = $course["name"];
				}
				$spec["electives"] = $electives;
				$spec["electives_str"] = $elective_str;

			}

			$spec_meta = db_query("SELECT * FROM course_bundle_meta WHERE bundle_id=".$spec["bundle_id"]);
			$spec["meta"] = $spec_meta[0];
			if ($spec["meta"]["category"] != "full-stack") {
				$specs[] = $spec;
			}
			else {

				$batches = db_query("SELECT * FROM bootcamp_batches WHERE bundle_id = ".$spec["bundle_id"]);
				if (empty($batches)) {
					$specs[] = $spec;
				}

			}

		}

		return $specs;

	}

	function fullstack_get_info_all_desc() {

		$today = new DateTime;

		$res_specs = db_query("SELECT * FROM course_bundle WHERE bundle_type = 'specialization' AND status != 'disabled' AND status != 'expired' AND combo != '' AND seller IN (0, 1) ORDER BY position DESC, bundle_id DESC;");
		$specs = array();
		foreach ($res_specs as $spec) {

			if (!empty($spec["electives"])) {

				$electives = explode(";", $spec["electives"]);
				$courses = db_query("SELECT name FROM course WHERE course_id IN (".implode(", ", $electives).");");
				$elective_str = [];
				foreach ($courses as $course) {
					$elective_str[] = $course["name"];
				}
				$spec["electives"] = $electives;
				$spec["electives_str"] = $elective_str;

			}

			$spec_meta = db_query("SELECT * FROM course_bundle_meta WHERE bundle_id=".$spec["bundle_id"]);
			$spec["meta"] = $spec_meta[0];
			if ($spec["meta"]["category"] == "full-stack") {

				$spec["batches"] = [];
				$batches = db_query("SELECT * FROM bootcamp_batches WHERE bundle_id = ".$spec["bundle_id"]);
				foreach ($batches as $batch) {

					$start_date = date_create_from_format("Y-m-d", $batch["start_date"]);
					if (!$batch["visible"]) {
						$batch["no_show"] = true;
					}
					if ($today->diff($start_date)->format("%r%a") < -BATCHES_PERIOD) {
						// $batch["no_show"] = true;
					}
					$batch["meta"] = json_decode($batch["meta"], true);

					$spec["batches"][] = $batch;

				}

				if (!empty($spec["batches"])) {
					$specs[] = $spec;
				}

			}

		}

		return $specs;

	}

	function course_bundle_get_by_id($bundle_id)
	{
		$res_bundle = db_query("SELECT * FROM course_bundle WHERE bundle_id=".$bundle_id.";");
		$bundle_meta = db_query("SELECT * FROM course_bundle_meta WHERE bundle_id=".$res_bundle[0]["bundle_id"]);
		$res_bundle[0]["meta"] = $bundle_meta[0];
		return $res_bundle[0];
	}

	function course_bundle_get_by_category($category)
	{
		$res_bundle = db_query("SELECT * FROM course_bundle as b INNER JOIN course_bundle_meta as bm on b.bundle_id = bm.bundle_id WHERE bm.category ='".$category."' ");
		return $res_bundle;
	}

?>