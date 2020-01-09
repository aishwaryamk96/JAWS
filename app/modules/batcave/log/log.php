<?php

	function batcave_create_log($user_id, $employee_id = false, $log = []) {

		$user_id = db_sanitize($user_id);
		$employee_id = db_sanitize($employee_id ?: $_SESSION["user"]["user_id"]);
		$log = db_sanitize(json_encode(is_array($log) ? $log : []));

		db_exec("INSERT INTO batcave_logs (user_id, employee_id, log) VALUES ($user_id, $employee_id, $log);");
		return db_get_last_insert_id();

	}

	function batcave_append_log($log_id, $log = []) {

		if (empty($log)) {
			return false;
		}

		if (($res = batcave_get_log($log_id)) == false) {
			return false;
		}

		$res_log = json_decode($res["log"], true);
		$res_log[] = $log;

		$log_id = db_sanitize($log_id);
		$res_log = db_sanitize(json_encode($res_log));

		db_exec("UPDATE batcave_logs SET log = $res_log WHERE id = $log_id;");
		return true;

	}

	function batcave_get_log($id, $type = "log") {

		$type = strtolower($type);
		if (!in_array($type, ["log", "user", "employee"])) {
			return false;
		}

		$type = $type == "log" ? "id" : $type."_id";
		$id = db_sanitize($id);

		$logs = db_query("SELECT * FROM batcave_logs WHERE $type = $id;");
		if ($type == "id") {
			return $logs ? $logs[0] : false;
		}

		return $logs;

	}

	function batcave_request_init($user_id, $post) {
		return batcave_create_log($user_id, $_SESSION["user"]["user_id"], [$post]);
	}

?>