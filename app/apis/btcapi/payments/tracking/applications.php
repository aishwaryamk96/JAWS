<?php

/* TODO:

	Add batch of the program,
	Edit features: Agent, JAT score, interview, comment, city

	Change interview to lead status (dropdown)

	Export

*/

	// Auth Check - Expecting Session Only !
	authorize_api_call(["applications.get", "applications.get.adv"], true);

	$unrestricted = auth_session_is_allowed("applications.get.adv");

	$form_name_mapping = [
		"uc-apply" => ["(123, 122)", "PGPDM"],
		"cyber-apply" => ["(135)", "Cyber Security"],
		"ipba-apply" => ["(126, 127, 129)", "IPBA"],
		"pgds-apply" => ["(131)", "PGDDS"]
	];

	$combo_mapping = [
		"150,2" => ["PGPDM", "(123, 122)", "uc-apply"],
		"239,2" => ["Cyber Security", "(135)", "cyber-apply"],
		"219,2" => ["IPBA", "(126, 127, 129)", "ipba-apply"],
		"249,2" => ["PGDDS", "(131)", "pgds-apply"],
	];

	// $this_month = db_sanitize(date("Y-m-01"));

	$where = "";
	$join = "";
	if (!$unrestricted) {

		$join = "LEFT JOIN team AS t ON t.team_id = ".$_SESSION["user"]["user_id"];
		$where = "AND (a.agent_id = ".$_SESSION["user"]["user_id"]." OR a.agent_id = t.user_id)";

	}

	$return = [];
	$pay_ids = [];
	$payments = db_query("SELECT p.user_id, p.app_num, p.sum_total, a.*, p.pay_id, u.name, u.email, u.phone, s.combo, p.status, p.create_date, p.currency, um.city, ag.email AS agent_email, ag.name AS agent_name, cl.crm FROM payment AS p LEFT JOIN application AS a ON a.pay_id = p.pay_id INNER JOIN subs AS s ON s.pay_id = p.pay_id INNER JOIN user AS u ON u.user_id = p.user_id INNER JOIN user_meta AS um ON um.user_id = u.user_id LEFT JOIN user AS ag ON ag.user_id = a.agent_id LEFT JOIN crm_leads AS cl ON cl.email = u.email $join WHERE s.combo IN ('150,2', '239,2', '219,2', '249,2') AND p.create_date >= '2019-01-01' $where ORDER BY p.pay_id DESC;");
	foreach ($payments as $pay) {

		$pay_ids[] = $pay["pay_id"];
		$pay["form_submit"] = json_decode($pay["form_submit"] ?? "[]", true);
		$pay["partial"] = 0;

		if (empty($pay["city"])) {
			$pay["city"] = $pay["form_submit"]["city"] ?? "";
		}

		$bundle_ids = $combo_mapping[$pay["combo"]][1];
		$pay["main_payment"] = db_query("SELECT p.*, bb.meta FROM payment AS p INNER JOIN subs_meta AS m ON m.subs_id = p.subs_id INNER JOIN bootcamp_batches AS bb ON bb.id = m.batch_id WHERE m.bundle_id IN $bundle_ids AND p.status = 'paid' AND p.user_id = ".$pay["user_id"].";");
		if (!empty($pay["main_payment"])) {

			$bb_meta = json_decode($pay["main_payment"][0]["meta"], true);
			$pay["main_payment"][0]["batch"] = $bb_meta["name"];

		}

		if (!empty($pay["form_name"])) {
			$pay["form_name_formatted"] = $form_name_mapping[$pay["form_name"]][1].($pay["partial"] == 1 ? " - Attempt" : "");
		}
		else {

			$pay["form_name"] = $combo_mapping[$pay["combo"]][2];
			$pay["form_name_formatted"] = $combo_mapping[$pay["combo"]][0];

		}

		if (empty($pay["form_submit"])) {

			$pay["form_submit"] = ["city" => $pay["city"]];
			$pay["website"] = false;

		}
		else {
			$pay["website"] = true;
		}

		$pay["jat_score"] = $pay["jat_score"] ?: "NA";
		$pay["agent"] = $pay["agent"] ?: "NA";
		$pay["lead_status"] = $pay["lead_status"] ?: "NA";

		if (empty($pay["submitted_on"])) {
			$pay["submitted_on"] = $pay["create_date"];
		}

		if (!empty($pay["submitted_on"])) {

			$submitted_on = date_create_from_format("Y-m-d H:i:s", $pay["submitted_on"]);
			$pay["submitted_on"] = $submitted_on->format("c");

		}

		$crm = json_decode($pay["crm"], true);
		$pay["form_submit"]["Lead Created On"] = $crm["CreatedOn"];
		$pay["form_submit"]["Lead Source"] = $crm["Source"];
		$pay["form_submit"]["Lead Lead Source"] = $crm["mx_Source"];
		$pay["form_submit"]["Lead Sub Source"] = $crm["mx_Sub_Source"];

		$return[] = $pay;

	}

	$pay_ids = implode(", ", $pay_ids);

	$apps = db_query("SELECT *, 'pending' AS status, cl.crm FROM application AS a LEFT JOIN crm_leads AS cl ON cl.email = a.email LEFT JOIN crm_leads AS cl ON cl.email = a.email WHERE a.partial = 1 AND a.full_submit = 0 OR a.partial = 0 AND a.pay_id NOT IN ($pay_ids) ORDER BY id DESC LIMIT 100;");
	foreach ($apps as $app) {

		$app["form_submit"] = json_decode($app["form_submit"], true);
		$app["name"] = $app["form_submit"]["name"];
		$app["email"] = $app["form_submit"]["email"];
		$app["phone"] = $app["form_submit"]["phone"];
		$form_name = str_replace("-attempt", "", $app["form_name"]);
		$app["form_name_formatted"] = $form_name_mapping[$form_name][1].($app["partial"] == 1 ? " - Attempt" : "");

		$submitted_on = date_create_from_format("Y-m-d H:i:s", $app["form_submit"]["submitted_on"]);
		$app["form_submit"]["submitted_on"] = $submitted_on->format("c");

		$crm = json_decode($app["crm"], true);
		$app["form_submit"]["Lead Created On"] = $crm["CreatedOn"];
		$app["form_submit"]["Lead Source"] = $crm["Source"];
		$app["form_submit"]["Lead Lead Source"] = $crm["mx_Source"];
		$app["form_submit"]["Lead Sub Source"] = $crm["mx_Sub_Source"];

		$return[] = $app;

	}

	die(json_encode($return));

?>