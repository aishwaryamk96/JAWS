<?php

	$forms = [
		"uc-apply" => [],
		"cyber-apply" => [],
		"ipba-apply" => [],
		"pgds-apply" => []
	];

	$mapping = [
		"uc-apply" => ["150,2", "(123, 122)", "PGPDM"],
		"cyber-apply" => ["239,2", "(135)", "Cyber Security"],
		"ipba-apply" => ["219,2", "(126, 127, 129)", "IPBA"],
		"pgds-apply" => ["249,2", "(131)", "PGDDS"]
	];

	$opts = [
		'http' => [
			'method'  => 'GET',
			'header'  => 'Authorization: Bearer YkkYkxFWM1OZLZkpxI5hXuzrQ4jrSAUJ'
		]
	];

	$context  = stream_context_create($opts);
	$response = json_decode(file_get_contents("https://www.jigsawacademy.com/applications-api", false, $context), true);
	if (empty($response)) {
		die(var_dump($response));
	}

	foreach ($response as $id => $submit) {

		$form_id = db_sanitize($id);
		$res_app = db_query("SELECT * FROM application WHERE form_submit_id = $form_id;");
		if (!empty($res_app)) {
			continue;
		}

		$pay_id = "NULL";

		$partial = strpos($submit["form_name"], "-attempt") !== false;
		if ($partial) {

			$main_form = str_replace("-attempt", "", $submit["form_name"]);
			if (empty($forms[$main_form][$submit["email"]])) {
				$forms[$main_form][$submit["email"]] = [];
			}

			$forms[$main_form][$submit["email"]][] = $id;

		}
		else {

			if (!empty($forms[$submit["form_name"]][$submit["email"]])) {
				$partial_id = array_unshift($forms[$submit["form_name"]][$submit["email"]]);
			}

			db_exec("UPDATE application SET full_submit = 1 WHERE form_submit_id = $partial_id;");

			$email = db_sanitize($submit["email"]);
			$combo = db_sanitize($mapping[$submit["form_name"]][0]);

			$payment = db_query(
				"SELECT
					p.*
				FROM
					payment AS p
				INNER JOIN
					subs AS s
					ON s.subs_id = p.subs_id
				INNER JOIN
					user AS u
					ON u.user_id = p.user_id
				WHERE
					u.email LIKE $email
					AND s.combo LIKE $combo
				ORDER BY
					p.pay_id ASC;"
			);
			if (!empty($payment)) {
				$pay_id = $payment[0]["pay_id"];
			}

		}

		$form_name = db_sanitize($submit["form_name"]);
		$email = db_sanitize($submit["email"]);
		$submitted_on = db_sanitize($submit["submitted_on"]);
		$form_submit = db_sanitize(json_encode($submit));
		$full_submit = $partial ? 0 : 1;
		$partial = $partial ? 1 : 0;

		db_exec("INSERT INTO application (email, pay_id, form_submit_id, form_name, form_submit, partial, full_submit, submitted_on) VALUES ($email, $pay_id, $form_id, $form_name, $form_submit, $partial, $full_submit, $submitted_on);");

	}