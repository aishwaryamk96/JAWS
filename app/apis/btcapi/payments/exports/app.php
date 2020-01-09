<?php

	// Prevent exclusive access
	if (!defined("JAWS")) {
		header("HTTP/1.1 401 Unauthorized");
		die();
	}

	load_plugin("phpexcel");

	$cols = [
		"Jig ID",
		"Name",
		"Email",
		"Phone",
		"Offering",
		"Total Installments",
		"Base Amount",
		"Total",
		"Currency",
		"MRP",
		"Creator Email",
		"Creator Name",
		"Paid Date",
		"Payment Mode",
		"Payment Channel",
		"Payment Txn ID",
		"Payment Receipt"
	];

	$data = [];

	$from = db_sanitize($_POST["from"]);
	$to = db_sanitize($_POST["to"]);

	$res = db_query(
		"SELECT
			e.sis_id,
			u.name, u.email, u.phone,
			p.instl_total,
			p.sum_offered,
			p.sum_total,
			p.currency,
			i.gateway_name,
			i.gateway_reference,
			i.receipt,
			a.email, a.name
		FROM
			user AS u
		INNER JOIN
			subs AS s
			ON s.user_id = u.user_id
		INNER JOIN
			subs_meta AS m
			ON m.subs_id = s.subs_id
		INNER JOIN
			payment AS p
			ON p.subs_id = s.subs_id
		INNER JOIN
			payment_instl AS i
			ON i.pay_id = p.pay_id
		INNER JOIN
			payment_link AS l
			ON l.instl_id = i.instl_id
		LEFT JOIN
			user AS a
			ON a.user_id = l.create_entity_id AND l.create_entity_type = 'user'
		LEFT JOIN
			user_enrollment AS e
			ON e.subs_id = s.subs_id
		LEFT JOIN
			crm_leads AS cl
			ON cl.email = u.email
		WHERE
			p.status = 'paid'
			AND s.status = 'active'
			AND e.subs_id = NULL
			AND p.create_date BETWEEN $from AND $to
		GROUP BY
			s.subs_id;"
	);

	foreach ($res as $pay) {

		$courses = explode("+", $pay["courses"]);
		if (empty($pay["Offering"])) {

			if (count($courses) == 1) {
				$pay["Offering"] = $courses[0][1];
			}
			else {
				$pay["Offering"] = count($courses)." Courses";
			}

			$price = 0;
			$index = $pay["Currency"] == "inr" ? 2 : 3;
			foreach ($courses as $course) {
				$price += $course[$index];
			}

			$pay["MRP"] = $price;

		}
		else {

			if (!empty($pay["batch_id"])) {
				$pay["MPR"] = $pay["batch_price_".$pay["Currency"]];
			}
			elseif (!empty($pay["bundle_id"])) {
				$pay["MPR"] = $pay["bundle_price_".$pay["Currency"]];
			}

		}

		$pay["Creator Email"] = $instls[0]["email"];
		$pay["Creator Name"] = $instls[0]["name"];

		unset($pay["bundle_price_inr"]);
		unset($pay["bundle_price_usd"]);
		unset($pay["batch_price_inr"]);
		unset($pay["batch_price_usd"]);
		unset($pay["bundle_id"]);
		unset($pay["batch_id"]);
		unset($pay["courses"]);
		unset($pay["pay_id"]);

		$data[] = $pay;

	}

	phpexcel_write(
		[["title" => "Payments", "cols" => $cols, "data" => $data]],
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