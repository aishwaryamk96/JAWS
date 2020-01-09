<?php

	load_plugin("jlc");

	$sis_ids = [];
	$res = db_query(
		"SELECT
			e.sis_id
		FROM
			user_enrollment AS e
		INNER JOIN
			payment AS p
			ON p.subs_id = e.subs_id
		INNER JOIN
			payment_instl AS i
			ON i.pay_id = p.pay_id
		WHERE
			i.status NOT IN ('paid', 'disabled')
			AND DATE(i.due_date) = DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY)
			AND p.status = 'paid'
		GROUP BY
			p.subs_id;"
	);

	foreach ($res as $sis_id) {
		$sis_ids[] = $sis_id["sis_id"];
	}

	if (!empty($sis_ids)) {

		$jlc = new JLC;
		$jlc->disableAccounts($sis_ids);

		load_library("email");
		send_email("jlc.access.revoke", [], ["success" => true, "sis_ids" => $sis_ids]);

	}

?>