<?php

	load_plugin("leadsquared");

	function getIndex($num) {

		if ($num == 1) {
			return "1st";
		}
		if ($num == 2) {
			return "2nd";
		}if ($num == 3) {
			return "3rd";
		}

		return $num."th";

	}

	$pay_info = db_query(
		"SELECT
			user.email,
			ls_pay.pay_id,
			pay.sum_total,
			pay.instl_total,
			subs.start_date,
			subs.combo,
			bundle.name AS bundle_name,
			GROUP_CONCAT(instl.instl_id, '=', instl.instl_count, '=', instl.sum, '=', IF (instl.due_date IS NOT NULL, instl.due_date, ''), '=', IF (instl.pay_date IS NOT NULL, instl.pay_date, ''), '=', instl.status SEPARATOR '+') AS instls
		FROM
			ls_payexport AS ls_pay
		INNER JOIN
			payment AS pay
			ON pay.pay_id = ls_pay.pay_id
		INNER JOIN
			payment_instl AS instl
			ON instl.pay_id = pay.pay_id
		INNER JOIN
			subs
			ON subs.pay_id = pay.pay_id
		INNER JOIN
			subs_meta AS meta
			ON meta.subs_id = subs.subs_id
		LEFT JOIN
			course_bundle AS bundle
			ON bundle.bundle_id = meta.bundle_id
		INNER JOIN
			user
			ON user.user_id = pay.user_id
		WHERE
			ls_pay.status = 'pending'
			AND
			pay.status = 'paid'
		GROUP BY
			ls_pay.pay_id
		LIMIT 5;"
	);

	if (empty($pay_info)) {
		exit("Nothing to run...\n");
	}

	foreach ($pay_info as $pay) {

		$activity = [
			"email" => $pay["email"],
			"mx_Course_Enrolled" => $pay["bundle_name"] ?? "Custom"
		];

		$combo = explode(";", $pay["combo"]);
		if (count($combo) == 1) {

			$course_id = db_sanitize(explode(",", $combo[0])[0]);
			$course = db_query("SELECT name, after_sales FROM course WHERE course_id = $course_id;");
			$course = $course[0];

			$activity["mx_Course_Enrolled"] = $course["name"];
			if (!empty($course["after_sales"])) {

				if (isset($course["after_sales"]["jlc"]) && empty($course["after_sales"]["jlc"])) {

					$activity["mx_4th_Installment_Amount"] = intval($pay["sum_total"]);
					$activity["mx_paylink_sent_Agent_name"] = $course["name"];

				}

			}

		}

		if (empty($activity["mx_paylink_sent_Agent_name"])) {

			$activity["mx_Total_Amount"] = intval($pay["sum_total"]);
			$activity["mx_Total_Installments"] = intval($pay["instl_total"]);
			$instls = explode("+", $pay["instls"]);

			foreach ($instls as $instl) {

				$info = explode("=", $instl);

				if ($info[1] > 3) {
					continue;
				}

				$index = "mx_".getIndex($info[1])."_Installment_";

				$activity[$index."Amount"] = intval($info[2]);
				if ($info[1] == 1) {
					continue;
				}

				$activity[$index."Date"] = $info[$info[5] != 'paid' ? 3 : 4];

				$activity[$index."payment_status"] = $info[5] == "paid" ? "Paid" : "Due";

			}

		}

		if (($activity_result = ls_lead_capture([[$activity]], false, true)) !== false) {

			if (is_string($activity_result)) {
				db_exec("UPDATE ls_payexport SET status = 'done', activity_id = ".db_sanitize($activity_result)." WHERE pay_id = ".$pay["pay_id"].";");
			}
			else {
				db_exec("UPDATE ls_payexport SET status = 'failed', message = ".db_sanitize($result["msg"])." WHERE pay_id = ".$pay["pay_id"].";");
			}

		}

	}

?>