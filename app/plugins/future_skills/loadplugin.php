<?php

	load_module("user");
	load_module("subs");
	load_module("user_enrollment");

	function create_subs($user, $subs_info, $payment, $return) {

		$pay_info = prepare_pay_info($payment);
		$subs_info["start_date"] = date("Y-m-d H:i:s");
		$subs_info["corp"] = "FSKILL";
		$subs = subscribe($user["email"], $subs_info, $pay_info, false, true, ($user["name"] ?? ""), true);

		if ($pay_info["status"] == "paid") {

			enr_create($subs["subs_id"]);
			sis_import();

		}

		if ($return) {
			return $subs;
		}

		return true;

	}

	function enroll_user($log_id, $user, $context, $payment, $return = false) {

		if (($user = get_or_create_user($user)) === false) {

			log_action($log_id, "bad_user");
			return false;

		}

		$lms_soc = ["lms_soc" => "corp"];
		user_update($user["user_id"], $lms_soc);

		// if (empty($slug = parse_url($context["slug"]))) {

		// 	log_action($log_id, "bad_context_url");
		// 	return false;

		// }

		// $slug = trim($slug["path"], "/");
		if (empty($context["id"])) {

			log_action($log_id, "bad_context_url");
			return false;

		}

		if ($context["type"] == "course") {
			return enroll_into_course($log_id, $user, $context["id"], $payment, $return);
		}
		else if ($context["type"] == "bundle") {
			return enroll_into_bundle($log_id, $user, $context["id"], $payment, $return);
		}

	}

	function enroll_into_bundle($log_id, $user, $id, $payment, $return) {

		if (empty(($bundle = db_query("SELECT bundle_id, combo, combo_free FROM course_bundle WHERE bundle_id = ".db_sanitize($id))))) {

			log_action($log_id, "context_url_404");
			return false;

		}
		$bundle = $bundle[0];

		$subs_info["combo"] = $bundle["combo"];
		$subs_info["combo_free"] = $bundle["combo_free"];

		return create_subs($user, $subs_info, $payment, $return);

	}

	function enroll_into_course($log_id, $user, $id, $payment, $return) {

		if (empty(($course = db_query("SELECT course_id FROM course WHERE course_id = ".db_sanitize($id))))) {

			log_action($log_id, "context_url_404");
			return false;

		}
		$course = $course[0];

		$subs_info["combo"] = $course["course_id"].",2";
		$subs_info["combo_free"] = "";

		return create_subs($user, $subs_info, $payment, $return);

	}

	function get_or_create_user($user) {

		$user_present = user_get_by_email($user["email"]);
		if (!empty($user_present)) {

			// if (!empty($user_present["lms_soc"])) {
			// 	return false;
			// }

			return $user_present;

		}

		$user_name = $user["first_name"]." ".$user["last_name"];
		return user_create($user["email"], $user["email"].$user_name.date("Ymdhisu"), $user_name);

	}

	function log_action($id = null, $extra = null) {

		if (is_array($id)) {

			$email = $id["data"]["user"]["email"] ?? "";

			db_exec("INSERT INTO fskill_api_logs (".(!empty($email) ? "email, " : "")."request) VALUES (".(!empty($email) ? db_sanitize($email).", " : "").db_sanitize(json_encode($id)).");");
			return db_get_last_insert_id();

		}

		if (is_array($extra)) {

			if (isset($extra["did_authenticate"]) && $extra["did_authenticate"] == false) {
				return db_exec("UPDATE fskill_api_logs SET did_authenticate = 0, status = 'unauthorized' WHERE id = ".$id);
			}

		}
		else if (is_string($extra)) {
			return db_exec("UPDATE fskill_api_logs SET status = ".db_sanitize($extra)." WHERE id = ".$id);
		}

	}

	function prepare_pay_info($payment) {

		$pay_info["status"] = ($payment["paid"] ? "paid" : "pending");
		if (empty($payment["currency"]) || empty($payment["amount"])) {

			log_action($log_id, "bad_payment_info");
			return false;

		}

		$currency = strtolower($payment["currency"]);
		if (!in_array($currency, ["inr", "usd", "skillcoin"])) {

			log_action($log_id, "bad_payment_info");
			return false;

		}

		if ($currency == "skillcoin") {
			$currency = "fsc";
		}

		$pay_info["currency"] = $currency;
		$pay_info["sum_basic"] = $payment["amount"];
		$pay_info["sum_total"] = $payment["amount"];
		$pay_info["instl_total"] = 1;
		$pay_info["instl"] = [1 => ["sum" => $payment["amount"], "due_days" => 0, "pay_mode" => "online"]];

		return $pay_info;

	}

?>