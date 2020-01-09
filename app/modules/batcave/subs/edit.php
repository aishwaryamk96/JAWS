<?php

	function change_batch($subs) {

		if (empty($batch = identify_batch_change($subs))) {
			return false;
		}

		// If batch is array, it, actually, contains bundle (bootcamp)
		if (is_array($batch)) {

			$subs_id = db_sanitize($subs["subs_id"]);
			$batch_id = db_sanitize($subs["batch"]);

			db_exec("UPDATE subs_meta SET batch_id = $batch_id WHERE subs_id = $subs_id;");

			$batch = false;

		}
		else {
			db_exec("UPDATE subs_meta SET batch_id = NULL WHERE subs_id = $subs_id;");
		}

		enrollments_delete($subs);
		enrollments_create($subs, $batch);

		return true;

	}

	function change_bundle($subs) {

		$subs["status"] = "blocked";
		if (!disable_subs($subs)) {
			return false;
		}

		$subs["status"] = "active";
		$subs = subs_create_new($subs, true);

		if (!change_batch($subs)) {
			enrollments_create($subs);
		}

		return true;

	}

	function disable_subs($subs) {

		$subs_id = db_sanitize($subs["subs_id"]);
		if (!empty($subs["status"])) {

			if (!in_array($subs["status"], ["blocked", "alumni", "expired"])) {
				return false;
			}

		}
		else {
			$subs["status"] = "blocked";
		}

		edit_subs_status($subs);
		return true;

	}

	function edit_subs($subs) {

	}

	function edit_subs_status($subs) {

		$subs_id = db_sanitize($subs["subs_id"]);
		$status = db_sanitize($subs["status"]);
		if ($subs["status"] == "pending") {

			if (empty($subs["start_date"])) {
				$subs["start_date"] = date();
			}

		}
		else if ($subs["status"] == "reinstate") {

			if (!empty($enrs = enrollments_get($subs["subs_id"]))) {

				$subs["status"] = "active";
				db_query("UPDATE user_enrollment SET status = 'active', sis_status = 'na' WHERE subs_id = $subs_id AND status = 'deleted';");

			}
			else {
				$subs["status"] = "pending";
			}

		}
		db_query("UPDATE subs SET status = $status WHERE subs_id = $subs_id;");

		if ($subs["status"] == "blocked" || $subs["status"] == "expired" || $subs["status"] == "refunded") {
			db_query("UPDATE user_enrollment SET status = 'deleted', sis_status = 'na' WHERE subs_id = $subs_id AND status = 'active';");
		}

	}

	function identify_batch_change($subs) {

		if (isset($subs["batch"])) {

			if (empty($subs["originalBatch"]) || $subs["originalBatch"] != $subs["batch"]) {

				if (!empty($subs["bundle_id"])) {

					$bundle = bundle_get($subs["bundle_id"], false);

					$batch_id = db_sanitize($subs["batch"]);
					$bb = db_query("SELECT * FROM bootcamp_batches WHERE id = $batch_id;");
					if (!empty($bb)) {
						return $bb[0];
					}

					if ($bundle["bundle_type"] == "bootcamps" || $bundle["bundle_type"] == "programs") {
						return false;
					}

				}

				return section_get_date_from_number($subs["batch"]);

			}

		}

		return false;

	}

	function identify_bundle_change($subs) {

		if (isset($subs["originalBundleId"]) && isset($subs["bundle_id"])) {

			if ($subs["originalBundleId"] != $subs["bundle_id"]) {
				return true;
			}

		}

		return false;

	}

	function subs_create_new($subs, $force_change_combo = false) {

		if (empty($subs["combo"]) || $force_change_combo) {

			if (empty($subs["bundle_id"])) {
				return false;
			}

			$bundle = bundle_get($subs["bundle_id"], false);
			if (empty($bundle)) {
				return false;
			}
			else if (empty($bundle["combo"])) {
				return false;
			}

			// if ($bundle["bundle_type"] == "bootcamps") {
			// 	$bundle
			// }

			$subs["combo"] = $bundle["combo"];
			$subs["bundle_type"] = $bundle["bundle_type"];

		}

		if (!empty($subs["enr"])) {
			$subs = subs_parse_enr($subs);
		}

		if (!empty($subs["batch"])) {
			$subs["batch_id"] = $subs["batch"];
		}

		return subscription_create($subs);

	}

	function subs_parse_enr($subs) {

		$combo = enrollment_prepare_combo($subs["enr"], $subs["bundle_id"] ?? false);
		if (!empty($combo)) {
			$subs["combo"] .= ";".$combo;
		}

		return $subs;

	}

	function subs_status_change($subs, $original_subs) {

		if ($subs["status"] != $original_subs["status"]) {

			if (!in_array($subs["status"], ["inactive", "pending", "active", "blocked", "disabled", "expired", "alumni", "refunded", "reinstate"])) {
				return false;
			}

			edit_subs_status($subs);

		}

		return true;

	}

?>