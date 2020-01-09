<?php

register_shutdown_function(function() {
	if (!empty($errors = error_get_last())) {
		$errors = db_sanitize(json_encode($errors));
		db_exec("INSERT INTO system_log (source, data) VALUES ('leadsquared.error', $errors);");
	}
});

	$data = file_get_contents("php://input");
	if (!empty($data)) {

		// db_exec("INSERT INTO system_log (source, data) VALUES ('leadsquared1', ".db_sanitize($data).")");

		$data = json_decode($data, true);
		$data = $data["After"];
		db_exec("INSERT INTO system_log (source, data) VALUES ('leadsquared.data', implode(',', $data)));

		load_plugin("leadsquared");
		$lead = fetch_lead($data["EmailAddress"], true);
		if (!empty($lead["Status"])) {
			return;
		}
		$lead = $lead[0];

		$sanitized_email = db_sanitize($lead["EmailAddress"]);
		$sanitized_lead_id = db_sanitize($lead["ProspectID"]);
		$sanitized_crm = db_sanitize(json_encode($lead));

		$res = db_query("SELECT id FROM crm_leads WHERE email LIKE $sanitized_email;");
		if (!empty($res)) {

			db_exec("UPDATE crm_leads SET lead_id = $sanitized_lead_id, crm = $sanitized_crm WHERE id = ".$res[0]["id"]);
			$lead_id = $res[0]["id"];

		}
		else {

			db_exec("INSERT INTO crm_leads (email, lead_id, crm) VALUES ($sanitized_email, $sanitized_lead_id, $sanitized_crm);");
			$lead_id = db_get_last_insert_id();

		}

		$activities = fetch_lead_activities($data["ProspectID"]);
		if (empty($activities["RecordCount"])) {
			return;
		}
		foreach ($activities["ProspectActivities"] as $activity) {
			put_activity($lead_id, $activity);
		}

	}

	function put_activity($lead_id, $activity) {

		$sanitized_activity_id = db_sanitize($activity["Id"]);
		$activity = db_sanitize(json_encode($activity));
		$res = db_query("SELECT id FROM crm_activities WHERE activity_id = $sanitized_activity_id;");
		if (!empty($res)) {
			db_exec("UPDATE crm_activities SET activity = $activity WHERE id = ".$res[0]["id"]);
		}
		else {
			db_exec("INSERT INTO crm_activities (lead_id, activity_id, activity) VALUES ($lead_id, $sanitized_activity_id, $activity);");
		}

	}

?>
