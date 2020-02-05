<?php

	function bootcamp_get_by_code($code) {

		if (empty(($batch = db_query("SELECT * FROM bootcamp_batches WHERE code LIKE ".db_sanitize($code).";")))) {
			return false;
		}

		return $batch;

	}

	function bootcamp_get_all() {

		$bs = db_query("SELECT * FROM course_bundle AS b WHERE b.bundle_type = 'bootcamps' AND b.status != 'disabled' AND b.combo != '' ORDER BY b.bundle_id DESC;");

		if (empty($bs)) {
			return [];
		}

		$today = new DateTime;

		$bootcamps = [];
		foreach ($bs as $b) {

			$batches = db_query("SELECT * FROM bootcamp_batches WHERE bundle_id = ".$b["bundle_id"]);
			if (empty($batches)) {
				continue;
			}

			foreach ($batches as $batch) {

				$start_date = date_create_from_format("Y-m-d", $batch["start_date"]);
				if ($today->diff($start_date)->format("%r%a") < -BATCHES_PERIOD) {
					$batch["no_show"] = true;
				}
				$batch["meta"] = json_decode($batch["meta"], true);

				$b["batches"][] = $batch;

			}

			$bootcamps[] = $b;

		}

		return $bootcamps;

	}

	function bootcamp_add_batches($bundle_id, $batches) {

		foreach ($batches as $code => $batch) {
			bootcamp_add_batch($bundle_id, $code, $batch);
		}

	}

	function bootcamp_add_batch($bundle_id, $code, $batch) {

		if (($batch_current = bootcamp_get_by_code($code)) === false) {

			$batch = bootcamp_sanitize_arr($batch);
			db_exec("INSERT INTO bootcamp_batches (bundle_id, code, start_date, end_date, price, price_usd, visible, meta) VALUES (".
				db_sanitize($bundle_id).",".
				db_sanitize($code).",".
				$batch["start_date"].",".
				$batch["end_date"].",".
				$batch["price"].",".
				$batch["price_usd"].",".
				$batch["visible"].",".
				$batch["meta"].");"
			);

		}
		else {
			$batch = bootcamp_edit_batch($bundle_id, $code, $batch_current, $batch);
		}

	}

	function bootcamp_edit_batch($bundle_id, $code, $batch_current, $batch_new) {

		$batch = bootcamp_batch_compare($batch_current, $batch_new);
		if (!empty(($batch = bootcamp_sanitize_arr($batch)))) {

			db_exec("UPDATE bootcamp_batches SET start_date=".$batch["start_date"].", end_date=".$batch["end_date"].", price=".$batch["price"].", price_usd=".$batch["price_usd"].", visible=".$batch["visible"].", meta=".$batch["meta"]." WHERE code = ".db_sanitize($code).";");
			return true;

		}

		return false;

	}

	function bootcamp_sanitize_arr($batch) {

		$status = true;
		if (!empty($batch["start_date"])) {
			$batch["start_date"] = db_sanitize($batch["start_date"]);
		}
		else {

			$batch["start_date"] = "NULL";
			$status = false;

		}

		if (!empty($batch["end_date"])) {
			$batch["end_date"] = db_sanitize($batch["end_date"]);
		}
		else {

			$batch["end_date"] = "NULL";
			$status = false;

		}

		if (!empty($batch["price"]) && intval($batch["price"]) > 0) {
			$batch["price"] = db_sanitize($batch["price"]);
		}
		else {

			$batch["price"] = db_sanitize("0");
			$status = false;

		}

		if (!empty($batch["price_usd"]) && intval($batch["price_usd"]) > 0) {
			$batch["price_usd"] = db_sanitize($batch["price_usd"]);
		}
		else {
			$batch["price_usd"] = db_sanitize("0");
		}

		if (!empty($batch["visible"])) {
			$batch["visible"] = intval($batch["visible"] && $status);
		}
		else {
			$batch["visible"] = 0;
		}

		$meta = [];
		foreach ($batch as $key => $value) {

			if (!in_array($key, ["start_date", "end_date", "price", "price_usd", "visible"])) {
				$meta[$key] = $value;
			}

		}
		$batch["meta"] = db_sanitize(json_encode($meta));

		return $batch;

	}

	function bootcamp_batch_compare($batch_current, $batch_new) {

		foreach ($batch_current as $key => $value) {

			if ($batch_new[$key] == $value) {
				unset($batch_new[$key]);
			}

		}

		return $batch_new;

	}

?>