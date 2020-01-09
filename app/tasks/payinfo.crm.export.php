<?php

die;

	load_plugin("leadsquared");

	$pay_info = db_query(
		"SELECT
			ls_leads.lead_id,
			ls_leads.email,
			user.email AS user_email,
			ls_pay.pay_id,
			pay.sum_total,
			pay.instl_total,
			subs.start_date,
			subs.combo_free,
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
		LEFT JOIN
			ls_leads
			ON ls_leads.email = user.email
		WHERE
			ls_pay.status = 'pending'
			AND
			pay.status = 'paid'
		GROUP BY
			ls_pay.pay_id
		LIMIT 5;"
	);

	if (empty($pay_info)) {
		exit;
	}

	foreach ($pay_info as $pay) {

		$frees = [];
		if (!empty($pay["combo_free"])) {

			$frees = explode(";", $pay["combo_free"]);
			foreach ($frees as $free) {

				$info = explode(",", $free);
				$course = db_query("SELECT name FROM course WHERE course_id = ".$info[0].";")[0];
				$frees[] = $course["name"];

			}

		}

		$fields = [
			"mx_Custom_1" => $pay["bundle_name"] ?? "",
			"mx_Custom_2" => implode(", ", $frees),
		];

		$start_date_custom_field_id = "mx_Custom_4";
		$start_date_reminder_custom_field_id = "mx_Custom_5";

		$activity_id = 208;
		$create_date;
		if ($pay["instl_total"] > 1) {

			$activity_id = 209;
			$start_date_custom_field_id = "mx_Custom_18";
			$start_date_reminder_custom_field_id = "mx_Custom_19";

			$fields["mx_Custom_3"] = intval($pay["sum_total"]);
			$fields["mx_Custom_4"] = intval($pay["instl_total"]);
			$instls = explode("+", $pay["instls"]);

			$i = 4;
			foreach ($instls as $instl) {

				$info = explode("=", $instl);

				if ($info[1] == 1) {

					$create_date = $info[4];
					$fields["mx_Custom_5"] = intval($info[2]);

				}
				else {

					$fields["mx_Custom_".($info[1] + $i)] = intval($info[2]);
					if ($info[5] != 'paid') {
						$fields["mx_Custom_".($info[1] + $i + 1)] = $info[3];
					}
					else {
						$fields["mx_Custom_".($info[1] + $i + 1)] = $info[4];
					}
					$fields["mx_Custom_".($info[1] + $i + 2)] = $info[5];

					$i += 2;

				}

			}

		}

		$fields[$start_date_custom_field_id] = $pay["start_date"];
		$fields[$start_date_reminder_custom_field_id] = date_create_from_format("Y-m-d H:i:s", $pay["start_date"])->add(new DateInterval("P2D"))->format("Y-m-d H:i:s");

		$activity = [
						"lead_id" => $pay["lead_id"],
						"activity_id" => $activity_id,
						"create_date" => $create_date,
						"email" => $pay["email"] ?? $pay["user_email"]
					];

		$activity["fields"] = $fields;

		if (($activity_result = activity_post($activity)) !== false) {

			if (is_string($activity_result)) {
				db_exec("UPDATE ls_payexport SET status = 'done', activity_id = ".db_sanitize($activity_result)." WHERE pay_id = ".$pay["pay_id"].";");
			}
			else {
				db_exec("UPDATE ls_payexport SET status = 'failed', message = ".db_sanitize($result["msg"])." WHERE pay_id = ".$pay["pay_id"].";");
			}

		}

	}

?>