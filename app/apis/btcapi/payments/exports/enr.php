<?php

	// Prevent exclusive access
	if (!defined("JAWS")) {
		header("HTTP/1.1 401 Unauthorized");
		die();
	}

	load_plugin("phpexcel");

	$cols = [
		"Jig ID",
		"Enrolled On",
		"Name",
		"Email",
		"Phone",
		"City",
		"State",
		"Offering",
		"Offering Status",
		"Batch",
		"MRP",
		"Total",
		"Currency",
		"Total Installments",
		"Month",
		"Due Installment",
		"Total Paid",
		"Total Due",
		"Creator Email",
		"Creator Name",
		"1st Installment Amount",
		"1st Installment Paid Date",
		"1st Installment Mode",
		"1st Installment Channel",
		"1st Installment Txn ID",
		"1st Installment Receipt",
		"2nd Installment Amount",
		"2nd Installment Due Date",
		"2nd Installment Paid Date",
		"2nd Installment Mode",
		"2nd Installment Channel",
		"2nd Installment Txn ID",
		"2nd Installment Receipt",
		"3rd Installment Amount",
		"3rd Installment Due Date",
		"3rd Installment Paid Date",
		"3rd Installment Mode",
		"3rd Installment Channel",
		"3rd Installment Txn ID",
		"3rd Installment Receipt",
		"4th Installment Amount",
		"4th Installment Due Date",
		"4th Installment Paid Date",
		"4th Installment Mode",
		"4th Installment Channel",
		"4th Installment Txn ID",
		"4th Installment Receipt",
		"5th Installment Amount",
		"5th Installment Due Date",
		"5th Installment Paid Date",
		"5th Installment Mode",
		"5th Installment Channel",
		"5th Installment Txn ID",
		"5th Installment Receipt",
		"6th Installment Amount",
		"6th Installment Due Date",
		"6th Installment Paid Date",
		"6th Installment Mode",
		"6th Installment Channel",
		"6th Installment Txn ID",
		"6th Installment Receipt",
		"7th Installment Amount",
		"7th Installment Due Date",
		"7th Installment Paid Date",
		"7th Installment Mode",
		"7th Installment Channel",
		"7th Installment Txn ID",
		"7th Installment Receipt",
		"8th Installment Amount",
		"8th Installment Due Date",
		"8th Installment Paid Date",
		"8th Installment Mode",
		"8th Installment Channel",
		"8th Installment Txn ID",
		"8th Installment Receipt",
		"9th Installment Amount",
		"9th Installment Due Date",
		"9th Installment Paid Date",
		"9th Installment Mode",
		"9th Installment Channel",
		"9th Installment Txn ID",
		"9th Installment Receipt",
		"10th Installment Amount",
		"10th Installment Due Date",
		"10th Installment Paid Date",
		"10th Installment Mode",
		"10th Installment Channel",
		"10th Installment Txn ID",
		"10th Installment Receipt",
	];

	$crm_cols = [
		"Lead ID",
		"Enrolled On",
		"Name",
		"Email",
		"Phone",
		"Lead Created Date",
		"Lead Source",
		"Source",
		"Sub Source",
		"Currency",
		"Expected Payment",
		"Total Paid Amount"
	];

	$data = [];
	$crm_data = [];

	$from = db_sanitize($_POST["from"]);
	$to = db_sanitize($_POST["to"]);

	$data_pay_ids = [];

	$res = db_query(
		"SELECT
			e.sis_id AS JigID,
			'' AS 'Enrolled On',
			u.name AS Name, u.email AS Email, u.phone AS Phone,
			um.city AS City, um.state AS State,
			b.name AS 'Offering',
			s.status AS 'Offering Status',
			'' AS 'Batch',
			b.price_inr AS 'bundle_price_inr',
			b.price_usd AS 'bundle_price_usd',
			bb.price AS 'batch_price_inr',
			bb.price_usd AS 'batch_price_usd',
			bb.meta AS 'batch_meta',
			s.combo,
			m.bundle_id,
			m.batch_id,
			p.pay_id,
			'' AS 'MRP',
			p.sum_total AS 'Total',
			p.currency AS 'Currency',
			p.instl_total AS 'Total Installments',
			DATE_FORMAT(p.create_date, '%M') AS 'Month',
			cl.crm
		FROM
			user AS u
		LEFT JOIN
			user_meta AS um
			ON um.user_id = u.user_id
		INNER JOIN
			subs AS s
			ON s.user_id = u.user_id
		INNER JOIN
			subs_meta AS m
			ON m.subs_id = s.subs_id
		LEFT JOIN
			course_bundle AS b
			ON b.bundle_id = m.bundle_id
		LEFT JOIN
			bootcamp_batches AS bb
			ON bb.id = m.batch_id
		LEFT JOIN
			user_enrollment AS e
			ON e.subs_id = s.subs_id AND e.status = 'active'
		INNER JOIN
			payment AS p
			ON p.subs_id = s.subs_id
		LEFT JOIN
			crm_leads AS cl
			ON cl.email = u.email
		WHERE
			p.status = 'paid'
			AND s.status IN ('pending', 'active', 'blocked', 'alumni', 'expired', 'refunded')
			AND p.create_date BETWEEN $from AND $to
		GROUP BY
			s.subs_id
		ORDER BY
			p.create_date;"
	);

	foreach ($res as $pay) {

		if (in_array($pay["pay_id"], $data_pay_ids)) {
			continue;
		}
		$data_pay_ids[] = $pay["pay_id"];

		$pay["Offering Status"] = ucwords($pay["Offering Status"]);

		if (!empty($pay["batch_meta"])) {

			$batch_meta = json_decode($pay["batch_meta"], true);
			$pay["Batch"] = $batch_meta["name"];

		}

		if (empty($pay["Offering"])) {

			$combo = explode(";", $pay["combo"]);
			$course_ids = [];
			foreach ($combo as $course) {
				$course_ids[] = explode(",", $course)[0];
			}
			$course_ids = implode(",", $course_ids);
			$courses = db_query("SELECT name, sp_price_inr, sp_price_usd FROM course WHERE course_id IN ($course_ids);");

			if (count($courses) == 1) {
				$pay["Offering"] = $courses[0]["name"];
			}
			else {
				$pay["Offering"] = count($courses)." Courses";
			}

			$price = 0;
			foreach ($courses as $course) {
				$price += $course["sp_price_".$pay["Currency"]];
			}

			$pay["MRP"] = $price;

		}
		else {

			if (!empty($pay["batch_id"]) && !empty(intval($pay["batch_price_".$pay["Currency"]]))) {
				$pay["MRP"] = intval($pay["batch_price_".$pay["Currency"]]);
			}
			elseif (!empty($pay["bundle_id"])) {
				$pay["MRP"] = $pay["bundle_price_".$pay["Currency"]];
			}

		}

		$pay_id = $pay["pay_id"];

		$instls = db_query(
			"SELECT
				i.instl_count,
				i.sum,
				i.due_date,
				i.pay_date,
				i.pay_mode,
				i.pay_comment,
				i.gateway_name,
				i.gateway_reference,
				i.receipt,
				u.email,
				u.name
			FROM
				payment_instl AS i
			LEFT JOIN
				payment_link AS l
				ON l.instl_id = i.instl_id
			LEFT JOIN
				user AS u
				ON u.user_id = l.create_entity_id AND l.create_entity_type = 'user'
			WHERE
				i.pay_id = $pay_id
			ORDER BY
				i.instl_count;"
		);

		$pay["Due Installment"] = "None";
		$pay["Total Paid"] = 0;
		$pay["Total Due"] = 0;

		$pay["Creator Email"] = $instls[0]["email"];
		$pay["Creator Name"] = $instls[0]["name"];

		foreach ($instls as $instl) {

			$pay[getIndex($instl["instl_count"])." Installment Amount"] = $instl["sum"];
			if ($instl["instl_count"] == 1) {
				$pay["Enrolled On"] = $instl["pay_date"];
			}
			else {
				$pay[getIndex($instl["instl_count"])." Installment Due Date"] = $instl["due_date"];
			}
			$pay[getIndex($instl["instl_count"])." Installment Paid Date"] = $instl["pay_date"];
			$pay[getIndex($instl["instl_count"])." Installment Mode"] = ucwords($instl["pay_mode"]);
			$pay[getIndex($instl["instl_count"])." Installment Channel"] = !empty($instl["gateway_name"]) ? ucwords($instl["gateway_name"]) : $instl["pay_comment"];
			$pay[getIndex($instl["instl_count"])." Installment Txn ID"] = $instl["gateway_reference"];
			$pay[getIndex($instl["instl_count"])." Installment Receipt"] = $instl["receipt"];

			if (!empty($instl["pay_date"])) {
				$pay["Total Paid"] += $instl["sum"];
			}
			else {

				$pay["Total Due"] += $instl["sum"];
				if ($pay["Due Installment"] == "None") {
					$pay["Due Installment"] = getIndex($instl["instl_count"]);
				}

			}

		}

		$crm = json_decode($pay["crm"] ?? "[]", true);
		if (!empty($crm)) {

			$crm_data[] = [
				"Lead ID" => $crm["ProspectID"],
				"Enrolled On" => $instls[0]["pay_date"],
				"Name" => $pay["Name"],
				"Email" => $pay["Email"],
				"Phone" => $pay["Phone"],
				"Created On" => $crm["CreatedOn"],
				"Source" => $crm["Source"],
				"Lead Source" => $crm["mx_Source"],
				"Sub Source" => $crm["mx_Sub_Source"],
				"Expected Payment" => $pay["Total"],
				"Total Paid Amount" => $pay["Total Paid"]
			];

		}

		unset($pay["bundle_price_inr"]);
		unset($pay["bundle_price_usd"]);
		unset($pay["batch_price_inr"]);
		unset($pay["batch_price_usd"]);
		unset($pay["batch_meta"]);
		unset($pay["combo"]);
		unset($pay["bundle_id"]);
		unset($pay["batch_id"]);
		unset($pay["pay_id"]);
		unset($pay["crm"]);

		$data[] = $pay;

	}

	phpexcel_write(
		[
			["title" => "Payments", "cols" => $cols, "data" => $data],
			["title" => "Leads", "cols" => $crm_cols, "data" => $crm_data]
		],
		["title" => "Payments (".date("F j, Y").")"],
		"Payments (".date("F j, Y").").xls"
	);

	exit();

	function getIndex($i) {

		if ($i == 1) {
			return "1st";
		}
		elseif ($i == 2) {
			return "2nd";
		}
		elseif ($i == 3) {
			return "3rd";
		}
		else {
			return $i."th";
		}

	}

?>