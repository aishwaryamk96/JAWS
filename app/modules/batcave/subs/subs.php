<?php

	function subs_get($id, $type = "subs") {

		if (!in_array($type, ["subs", "user", "bundle", "batch"])) {
			return false;
		}

		$return_type = $type == "subs" ? 1 : 0;

		if ($type == "subs" || $type == "user") {
			$type = "subs.".$type."_id";
		}
		else {
			$type = "meta.".$type."_id";
		}

		$id = db_sanitize($id);
		$res_subs = db_query("SELECT subs.*, meta.bundle_id, meta.batch_id, meta.create_date, meta.agent_id FROM subs LEFT JOIN subs_meta AS meta ON meta.subs_id = subs.subs_id WHERE $type = $id;");
		if (empty($res_subs)) {
			return $return_type == 1 ? false : [];
		}

		return $return_type == 1 ? $res_subs[0] : $res_subs;

	}

	function subscription_create($subs) {

		$subs_sanitized = subs_sanitize($subs);

		$values_subs = implode(", ", [
			$subs_sanitized["user_id"],
			$subs_sanitized["pay_id"],
			$subs_sanitized["package_id"],
			$subs_sanitized["combo"],
			$subs_sanitized["combo_free"],
			$subs_sanitized["corp"],
			$subs_sanitized["start_date"],
			$subs_sanitized["end_date"],
			$subs_sanitized["status"]
		]);
		db_exec("INSERT INTO subs (user_id, pay_id, package_id, combo, combo_free, corp, start_date, end_date, status) VALUES ($values_subs);");
		$subs["subs_id"] = db_get_last_insert_id();

		$values_subs_meta = implode(", ", [
			$subs["subs_id"],
			$subs_sanitized["bundle_id"],
			$subs_sanitized["batch_id"],
			$subs_sanitized["create_date"],
			$subs_sanitized["agent_id"]
		]);

		db_exec("INSERT INTO subs_meta (subs_id, bundle_id, batch_id, create_date, agent_id) VALUES ($values_subs_meta);");
		if (!empty($subs["pay"]["pay_id"])) {
			payment_transfer_subs($subs["pay"]["pay_id"], $subs["subs_id"]);
		}

		return $subs;

	}

	function subs_sanitize($subs, $create = false) {

		$subs["user_id"] = db_sanitize($subs["user_id"]);
		$subs["pay_id"] = empty($subs["pay_id"]) ? "NULL" : db_sanitize($subs["pay_id"]);
		$subs["package_id"] = empty($subs["package_id"]) ? "NULL" : db_sanitize($subs["package_id"]);
		$subs["combo"] = db_sanitize($subs["combo"]);
		$subs["combo_free"] = db_sanitize((empty($subs["combo_free"]) ? "" : $subs["combo_free"]));
		$subs["corp"] = empty($subs["corp"]) ? "NULL" : db_sanitize($subs["corp"]);
		$subs["start_date"] = empty($subs["start_date"]) ? "NULL" : db_sanitize($subs["start_date"]);
		$subs["end_date"] = empty($subs["end_date"]) ? "NULL" : db_sanitize($subs["end_date"]);
		$subs["status"] = db_sanitize($subs["status"]);
		$subs["bundle_id"] = empty($subs["bundle_id"]) ? "NULL" : db_sanitize($subs["bundle_id"]);
		$subs["batch_id"] = empty($subs["batch_id"]) ? "NULL" : db_sanitize($subs["batch_id"]);
		$subs["create_date"] = empty($subs["create_date"]) ? "CURRENT_TIMESTAMP" : db_sanitize($subs["create_date"]);
		$subs["agent_id"] = empty($subs["agent_id"]) ? "NULL" : db_sanitize($subs["agent_id"]);

		return $subs;

	}

?>