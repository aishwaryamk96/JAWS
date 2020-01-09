<?php

	function access_dates_change($subs_id, $dates) {

		$dates_orig = access_dates_get_for_subs($subs_id, "object");

		$dates_new = [];
		$dates_old = [];
		foreach ($dates as $date) {

			if (!empty($date["id"])) {
				$dates_old[$date["id"]] = access_dates_construct_objects($date);
			}
			else {
				$dates_new[] = access_dates_construct_objects($date);
			}

		}

		foreach ($dates_orig as $date) {

			if (compare_dates($date, $dates_old[$date["id"]])) {
				access_date_edit($dates_old[$date["id"]]);
			}

		}

		foreach ($dates_new as $date) {

			if (empty($date["start_date"])) {
				$date["start_date"] = $dates_orig[0];
			}

			access_date_add($subs_id, $date);

		}

	}

	function access_date_add($subs_id, $date) {

		$start_date = db_sanitize($date["start_date"]->format("Y-m-d H:i:s"));
		$end_date = db_sanitize($date["end_date"]->format("Y-m-d H:i:s"));
		$is_free = db_sanitize($date["is_free"]);

	}

	function access_date_edit($date) {

		$start_date = db_sanitize($date["start_date"]->format("Y-m-d H:i:s"));
		$end_date = db_sanitize($date["end_date"]->format("Y-m-d H:i:s"));
		$is_free = db_sanitize($date["is_free"]);

		$id = db_sanitize($date["id"]);
		db_exec("UPDATE access_duration SET start_date = $start_date, end_date = $end_date, is_free = $is_free WHERE id = $id;");

	}

	function access_dates_get_for_subs($subs_id, $date_format = "plain") {

		$start_date = "start_date";
		$end_date = "end_date";
		if ($date_format == "date") {

			$start_date = "DATE(start_date) AS start_date";
			$end_date = "DATE(end_date) AS end_date";

		}

		$subs_id = db_sanitize($subs_id);
		if (empty(($dates = db_query("SELECT id, user_id, subs_id, $start_date, $end_date, freeze_id, is_free, requested_by, approved_by, created_at, updated_at FROM access_duration WHERE subs_id = $subs_id ORDER BY id;")))) {

			if (empty(($subs = db_query("SELECT * FROM subs WHERE subs_id = $subs_id;")))) {
				return false;
			}

			$dates = access_dates_create($subs[0]);

		}

		if ($date_format == "object") {

			$ret = [];
			foreach ($dates as $date) {
				$ret[] = access_dates_construct_objects($date);
			}

			return $ret;

		}

		return $dates;

	}

	function access_dates_create($subs) {

		if (!is_array($subs)) {
			$subs = subs_get($subs);
		}

		if (empty($subs)) {
			return false;
		}

		$user_id = db_sanitize($subs["user_id"]);
		$subs_id = db_sanitize($subs["subs_id"]);
		$start_date = db_sanitize($subs["start_date"]);
		$end_date = db_sanitize($subs["end_date"]);

		$dates = [];
		db_exec("INSERT INTO access_duration (user_id, subs_id, start_date, end_date) VALUES ($user_id, $subs_id, $start_date, $end_date);");
		$dates[] = [
			"id" => db_get_last_insert_id(),
			"user_id" => $subs["user_id"],
			"subs_id" => $subs["subs_id"],
			"start_date" => $subs["start_date"],
			"end_date" => $subs["end_date"],
			"freeze_id" => null,
			"is_free" => 0,
			"requested_by" => 0,
			"approved_by" => 0,
			"created_at" => date("Y-m-d H:i:s"),
			"updated_at" => date("Y-m-d H:i:s")
		];

		if (!empty($subs["end_date_ext"])) {

			$end_date = db_sanitize($subs["end_date_ext"]);
			db_exec("INSERT INTO access_duration (user_id, subs_id, start_date, end_date) VALUES ($user_id, $subs_id, $start_date, $end_date);");
			$dates[] = [
				"id" => db_get_last_insert_id(),
				"user_id" => $subs["user_id"],
				"subs_id" => $subs["subs_id"],
				"start_date" => $subs["start_date"],
				"end_date" => $subs["end_date"],
				"freeze_id" => null,
				"is_free" => 0,
				"requested_by" => 0,
				"approved_by" => 0,
				"created_at" => date("Y-m-d H:i:s"),
				"updated_at" => date("Y-m-d H:i:s")
			];

		}

		return $dates;

	}

	function access_dates_construct_objects($dates) {

		$dates["start_date"] = date_create_from_format("Y-m-d H:i:s", $dates["start_date"]);
		$dates["end_date"] = date_create_from_format("Y-m-d H:i:s", $dates["end_date"]);

		return $dates;

	}

	function compare_dates($dates1, $dates2) {

		if ($dates1["start_date"] != $dates2["start_date"]) {
			return true;
		}
		if ($dates1["end_date"] != $dates2["end_date"]) {
			return true;
		}

		return false;

	}

?>