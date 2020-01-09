<?php

	// Auth Check - Expecting Session Only !
	authorize_api_call("enrollment.get.adv", true);

	$return = [];
	$sis_ids = [];
	$res = db_query(
		"SELECT
			u.user_id, u.name, u.email, u.phone,
			p.subs_id, p.pay_id, p.sum_total, p.currency,
			IF (b.bundle_id IS NOT NULL, b.name, 'Custom') AS bundle_name,
			i.sum, DATE_FORMAT(i.due_date, '%b %d, %y') AS due_date, i.instl_count,
			e.sis_id
		FROM
			user AS u
		INNER JOIN
			payment AS p
			ON p.user_id = u.user_id
		INNER JOIN
			payment_instl AS i
			ON i.pay_id = p.pay_id
		INNER JOIN
			subs_meta AS m
			ON m.subs_id = p.subs_id
		LEFT JOIN
			course_bundle AS b
			ON b.bundle_id = m.bundle_id
		INNER JOIN
			user_enrollment AS e
			ON e.subs_id = p.subs_id
		WHERE
			DATE(i.due_date) < CURRENT_DATE
			AND i.status != 'paid'
			AND p.status = 'paid'
		GROUP BY
			p.subs_id
		ORDER BY
			i.due_date ASC;"
	);

	foreach ($res as $pay) {

		$pay_id = $pay["pay_id"];
		$instls = db_query(
			"SELECT
				i.instl_id, i.sum,
				i.due_date, i.pay_date,
				i.instl_count, i.notify_count, i.receipt
			FROM
				payment_instl AS i
			WHERE
				i.pay_id = $pay_id
			ORDER BY
				i.instl_count ASC;"
		);

		$pay["instl"] = $instls;
		$sis_ids[] = $pay["sis_id"];

		$return[] = $pay;

	}

	load_plugin("jlc");
	$jlc = new JLC;
	$response = $jlc->statusFor($sis_ids);

	$values = [];
	foreach ($return as $rec) {

		$rec["jlc_status"] = $response[$rec["sis_id"]];
		$values[] = $rec;

	}

	die(json_encode($values));

?>