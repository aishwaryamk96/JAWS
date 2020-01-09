<?php

	load_module("user_enrollment");

	function enrollment_create($subs_id, $enr) {

		if (isset($enr["enr_id"])) {
			unset($enr["enr_id"]);
		}

		$enr_sanitized = enrollment_sanitize($enr, true);

		$columns = implode(", ", array_keys($enr_sanitized));
		$values = implode(", ", array_values($enr_sanitized));

		db_query("INSERT INTO user_enrollment ($columns) VALUES ($values);");

	}

	function enrollment_delete($enr_id) {

		if (is_array($enr_id)) {
			$enr_id = $enr_id["enr_id"];
		}

		$enr_id = db_sanitize($enr_id);
		db_exec("UPDATE user_enrollment SET status = 'deleted', sis_status = 'na' WHERE enr_id = $enr_id;");

	}

	function enrollment_edit($enr, $old_enr) {

		// We give precedence to section_num over section_id
		if (!empty($enr["section_num"])) {
			$enr["section_id"] = section_get_for_date_create($enr["course_id"], section_get_date_from_number($enr["section_num"]), $enr["learn_mode"], "", false, ($enr["section_num"] < 100 ? $enr["section_num"] : false))["id"];
		}

		$enr_sanitized = enrollment_sanitize($enr, false, $old_enr);

		// $columns = array_keys($enr_sanitized);
		// $values = array_values($enr_sanitized);

		// $columns = implode(", ", $columns);
		// $values = implode(", ", $values);
		$keys = [];
		$values =[];
		$enr_id = $enr_sanitized["enr_id"];
		foreach ($enr_sanitized as $key => $value) {

			if ($key != "enr_id") {

				$keys[] = $key;
				$values[] = $value;

			}

		}
		$keys = implode(", ", $keys);
		$values = implode(", ", $values);

		$old_enr_id = db_sanitize($old_enr["enr_id"]);
		db_exec("UPDATE user_enrollment SET status = 'deleted', sis_status = 'na' WHERE enr_id = $old_enr_id;");
		db_exec("INSERT INTO user_enrollment ($keys) VALUES ($values);");
		$enr["enr_id"] = db_get_last_insert_id();

		return $enr;

	}

	function enrollment_get($enr_id) {

		$enr = enrollments_get($enr_id, "");
		if (empty($enr)) {
			return false;
		}

		return $enr[0];

	}

	function enrollment_match_batch($enr1, &$enr2) {

		// $enr1 is expected to have the numeric batch
		$enr2["section_num"] = section_get_number_for_start_date($enr2["section_id"]);
		return $enr1["section_num"] == $enr2["section_num"];

	}

	function enrollment_sanitize($enr, $create = false, $old_enr = []) {

		$enr_sanitized = [];
		if (!$create) {
			$enr_sanitized["enr_id"] = db_sanitize($enr["enr_id"]);
		}

		$enr_sanitized["user_id"] = db_sanitize($enr["user_id"] ?? $old_enr["user_id"]);
		$enr_sanitized["subs_id"] = db_sanitize($enr["subs_id"] ?? $old_enr["subs_id"]);
		$enr_sanitized["course_id"] = db_sanitize($enr["course_id"]);
		$enr_sanitized["learn_mode"] = db_sanitize($enr["learn_mode"]);
		if (!empty($enr["section_id"])) {
			// Nothing to do
		}
		elseif (!empty($enr["section_num"])) {
			$enr["section_id"] = section_get_for_date_create($enr["course_id"], section_get_date_from_number($enr["section_num"]), $enr["learn_mode"], "", false, ($enr["section_num"] < 100 ? $enr["section_num"] : false))["id"];
		}
		$enr_sanitized["section_id"] = db_sanitize($enr["section_id"]);

		$enr_sanitized["sis_id"] = db_sanitize($enr["sis_id"]);
		if (!empty($enr["lms_pass"])) {
			$enr_sanitized["lms_pass"] = db_sanitize($enr["lms_pass"]);
		}
		else if (!empty($old_enr["lms_pass"])) {
			$enr_sanitized["lms_pass"] = db_sanitize($old_enr["lms_pass"]);
		}
		else {
			unset($enr["lms_pass"]);
		}
		// unset($enr["sis_file"]);
		// unset($enr["sis_status"]);
		if (!empty($enr["lab_ip"])) {
			$enr_sanitized["lab_ip"] = db_sanitize($enr["lab_ip"]);
		}
		else if (!empty($old_enr["lab_ip"])) {
			$enr_sanitized["lab_ip"] = db_sanitize($old_enr["lab_ip"]);
		}
		// else {
		// 	unset($enr["lab_ip"]);
		// }
		if (!empty($enr["lab_user"])) {
			$enr_sanitized["lab_user"] = db_sanitize($enr["lab_user"]);
		}
		else if (!empty($old_enr["lab_user"])) {
			$enr_sanitized["lab_user"] = db_sanitize($old_enr["lab_user"]);
		}
		// else {
		// 	unset($enr["lab_user"]);
		// }
		if (!empty($enr["lab_pass"])) {
			$enr_sanitized["lab_pass"] = db_sanitize($enr["lab_pass"]);
		}
		else if (!empty($old_enr["lab_pass"])) {
			$enr_sanitized["lab_pass"] = db_sanitize($old_enr["lab_pass"]);
		}
		// else {
		// 	unset($enr["lab_pass"]);
		// }
		$enr_sanitized["status"] = db_sanitize($enr["status"]);
		$enr_sanitized["added_by"] = db_sanitize($_SESSION["user"]["user_id"] ?? 0);
		$enr_sanitized["sis_status"] = "'na'";
		$enr_sanitized["shall_notify"] = 0;

		return $enr_sanitized;

	}

	function enrollments_create($subs, $batch = false) {
		enr_create($subs["subs_id"], false, $batch);
	}

	function enrollments_delete($subs, $status = "deleted") {

		if ($status != "deleted" && $status != "swapped") {
			return false;
		}

		$status = db_sanitize($status);
		$subs_id = db_sanitize($subs["subs_id"]);

		db_exec("UPDATE user_enrollment SET status = $status, sis_status = 'na' WHERE subs_id = $subs_id;");
		return true;

	}

	function enrollments_get($id, $type = "subs_id") {

		$id = db_sanitize($id);

		$type = strtolower($type);
		if (empty($type)) {
			$type = "enr_id";
		}
		else if ($type != "subs_id" && $type != "user_id" && $type != "course_id") {
			return false;
		}

		return db_query("SELECT * FROM user_enrollment WHERE $type = $id;");

	}

	function enrollment_prepare_combo($enrs, $bundle_id = false) {

		$combo = [];
		foreach ($enrs as $enr) {

			if ($enr["status"] != "deleted") {
				$combo[] = $enr["course_id"].",2";
			}

		}

		return implode(";", $combo);

	}

	function enrollments_change($subs_id, $enrs) {

		$user_id = "";
		$curr_enrs_associated = [];
		$curr_enrs = enrollments_get($subs_id);
		foreach ($curr_enrs as $enr) {

			$curr_enrs_associated[$enr["enr_id"]] = $enr;
			$user_id = $enr["user_id"];

		}

		$edit_logs = [];
		$delete_logs = [];
		$create_logs = [];

		$new = [];
		foreach ($enrs as $enr) {

			if (!empty($enr["new"])) {
				$new[] = $enr;
			}
			else if ($enr["status"] == "deleted") {
				enrollment_delete($enr);
			}
			else if ($enr["course_id"] != $curr_enrs_associated[$enr["enr_id"]]["course_id"]) {
				enrollment_edit($enr, $curr_enrs_associated[$enr["enr_id"]]);
			}
			else if (enrollment_match_batch($enr, $curr_enrs_associated[$enr["enr_id"]]) == false) {

				enrollment_edit($enr, $curr_enrs_associated[$enr["enr_id"]]);
				$edit_logs[] = $enr["course_code"]." batch: ".section_get_batch_from_number($curr_enrs_associated[$enr["enr_id"]]["section_num"])." to ".section_get_batch_from_number($enr["section_num"]).".";

			}

		}

		foreach ($new as $enr) {

			foreach ($curr_enrs[0] as $key => $value) {

				if (!in_array($key, ["course_id", "status", "section_id"])) {
					$enr[$key] = $value;
				}

			}

			enrollment_create($subs_id, $enr);

		}

		if (!empty($edit_logs)) {
			user_add_log($user_id, "subs.edit", "batch.change.course", $_SESSION["user"]["user_id"], implode("\n", $edit_logs), "done", ["subs", $subs_id]);
		}

	}

?>