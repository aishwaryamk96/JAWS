<?php

	authorize_api_call("applications.get.adv", true);

	load_module("user");

	if (empty($_POST)) {
		$_POST = json_decode(file_get_contents("php://input"), true);
	}
	if (empty($_POST["application"])) {
		die(header("HTTP/1.1 400"));
	}

	if (!empty($_POST["application"]["id"])) {

		$id = db_sanitize($_POST["application"]["id"]);
		$application = db_query("SELECT * FROM application WHERE id = $id;");
		if (empty($application)) {
			die(header("HTTP/1.1 404"));
		}

		$application = $application[0];
		list($pay_id, $form_submit_id, $form_name, $form_submit, $partial, $full_submit, $jat_score, $lead_status, $agent, $agent_id, $comment, $email) = getParams();

		db_exec("UPDATE application SET jat_score = $jat_score, lead_status = $lead_status, agent_id = $agent_id, comment = $comment WHERE id = $id;");
		$id = $_POST["application"]["id"];

	}
	else {

		list($pay_id, $form_submit_id, $form_name, $form_submit, $partial, $full_submit, $jat_score, $lead_status, $agent, $agent_id, $comment, $email) = getParams();

		$prev_application = db_query("SELECT * FROM application WHERE form_name = $form_name AND form_submit->'$.email' LIKE $email ORDER BY id DESC;");
		if (!empty($prev_application)) {

			$prev_application = $prev_application[0];
			db_exec("UPDATE application SET full_submit = 1 WHERE id = ".$prev_application["id"]);

		}

		db_exec("INSERT INTO application (pay_id, form_submit_id, form_name, form_submit, partial, full_submit, jat_score, lead_status, agent_id, comment) VALUES ($pay_id, $form_submit_id, $form_name, $form_submit, $partial, $full_submit, $jat_score, $lead_status, $agent_id, $comment);");
		$id = db_get_last_insert_id();

	}

	die(json_encode(["id" => $id, "agent_name" => $agent["name"]]));

	function getParams() {

		$jat_score = "NULL";
		if (!empty($_POST["application"]["jat_score"]) && $_POST["application"]["jat_score"] != "NA") {
			$jat_score = db_sanitize($_POST["application"]["jat_score"]);
		}

		$lead_status = "NULL";
		if (!empty($_POST["application"]["lead_status"]) && $_POST["application"]["lead_status"] != "NA") {
			$lead_status = db_sanitize($_POST["application"]["lead_status"]);
		}

		$comment = "NULL";
		if (!empty($_POST["application"]["comment"]) && $_POST["application"]["comment"] != "NA") {
			$comment = db_sanitize($_POST["application"]["comment"]);
		}

		$agent = ["name" => ""];
		$agent_id = "NULL";
		if (!empty($_POST["application"]["agent_email"])) {

			$agent = user_get_by_email($_POST["application"]["agent_email"]);
			if (empty($agent)) {
				header("HTTP/1.1 400");
				die(json_encode(["error" => "Agent not found by the given email"]));
			}

			$agent_id = $agent["user_id"];

		}

		$pay_id = "NULL";
		$full_submit = 0;
		if (!empty($_POST["application"]["pay_id"])) {

			$pay_id = db_sanitize($_POST["application"]["pay_id"]);
			$full_submit = 1;

		}

		$form_submit_id = 0;
		if (!empty($_POST["application"]["form_submit_id"])) {
			$form_submit_id = db_sanitize($_POST["application"]["form_submit_id"]);
		}

		$form_name = "NULL";
		if (!empty($_POST["application"]["form_name"])) {
			$form_name = db_sanitize($_POST["application"]["form_name"]);
		}

		$form_submit = "NULL";
		if (!empty($_POST["application"]["form_submit"])) {

			if (!empty($_POST["application"]["city"])) {
				$_POST["application"]["form_submit"]["city"] = !empty($_POST["application"]["form_submit"]["city"]) ? $_POST["application"]["form_submit"]["city"] : $_POST["application"]["city"];
			}
			$form_submit = db_sanitize(json_encode($_POST["application"]["form_submit"]));

		}

		$partial = "NULL";
		if (isset($_POST["application"]["partial"])) {
			$partial = db_sanitize(json_encode($_POST["application"]["partial"]));
		}

		$email = db_sanitize($_POST["application"]["email"]);

		return [$pay_id, $form_submit_id, $form_name, $form_submit, $partial, $full_submit, $jat_score, $lead_status, $agent, $agent_id, $comment, $email];

	}
?>