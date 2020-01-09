<?php

	$data = [];

	$res = db_query(
		"SELECT
			u.name, u.email, u.phone,
			p.pay_id, p.sum_total, p.currency, p.instl_total, p.create_date
		FROM
			payment AS p
		INNER JOIN
			user AS u
			ON u.user_id = p.user_id
		INNER JOIN
			subs_meta AS m
			ON m.subs_id = p.subs_id
		WHERE
			DATE(p.create_date) >= '2019-04-01'
			AND m.bundle_id IN (127, 126)
			AND p.user_id NOT IN (13683, 7822);"
	);

	$data[] = implode(",", array_keys($res[0]));
	foreach ($res as $i => $pay) {

		$instls = db_query(
			"SELECT
				sum, due_date, pay_date
			FROM
				payment_instl
			WHERE
				pay_id = ".$pay["pay_id"]."
			ORDER BY
				pay_id ASC, instl_count ASC;"
		);

		if ($i == 0) {
			$data[0] .= ",".implode(",", array_keys($instls[0]));
		}

		foreach ($instls as $j => $instl) {
			foreach ($instl as $key => $value) {
				$pay[$key."_".($j + 1)] = $value;
			}
		}

		$data[] = implode(",", $pay);

	}

	echo implode("<br>", $data);

?>