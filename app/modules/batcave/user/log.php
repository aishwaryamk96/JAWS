<?php

	function user_add_log($user_id, $category, $sub_category, $created_by, $description, $status = "done", $context = [], $request = []) {

		if (empty($status)) {
			$status = "done";
		}

		$values = implode(", ", [
			db_sanitize($user_id),
			db_sanitize($category),
			empty($sub_category) ? "NULL" : db_sanitize($sub_category),
			db_sanitize($created_by),
			empty($request) ? "NULL" : db_sanitize(json_encode($request)),
			db_sanitize($description),
			empty($context) ? "NULL" : db_sanitize($context[0]),
			empty($context) ? "NULL" : db_sanitize($context[1]),
			db_sanitize($status)
		]);

		db_exec("INSERT INTO user_logs (user_id, category, sub_category, created_by, request, description, context_type, context_id, status) VALUES ($values);");
		return db_get_last_insert_id();

	}

	function user_get_log($log_id) {

		$res = db_query("SELECT * FROM user_logs WHERE id = ".db_sanitize($log_id).";");
		if (empty($res)) {
			return false;
		}

		return $res[0];

	}

	function user_update_log($log_id, $status = "done") {

		$log_id = db_sanitize($log_id);
		$status = db_sanitize($status);
		$resolved_by = db_sanitize($_SESSION["user"]["user_id"] ?? 0);

		db_exec("UPDATE user_logs SET status = $status, resolved_by = $resolved_by WHERE id = $log_id;");

	}

?>