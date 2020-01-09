<?php

	$res = db_query("SELECT l.email FROM user_leads_basic_compiled AS l LEFT JOIN crm_leads AS c ON c.email = l.email WHERE c.email IS NULL ORDER BY l.lead_id ASC LIMIT 1;");

	load_plugin("leadsquared");
	$lead = fetch_lead($res[0]["email"], true);
	if (empty($lead)) {
		$lead["ProspectID"] = "";
	}
	else {
		$lead = $lead[0];
	}

	$sanitized_email = db_sanitize($res[0]["email"]);
	$sanitized_lead_id = db_sanitize($lead["ProspectID"]);
	$sanitized_crm = db_sanitize(json_encode($lead));
	db_exec("INSERT INTO crm_leads (email, lead_id, crm) VALUES ($sanitized_email, $sanitized_lead_id, $sanitized_crm);");
	$lead_id = db_get_last_insert_id();

	if (!empty($lead["ProspectID"])) {

		$activities = fetch_lead_activities($lead["ProspectID"]);
		if (empty($activities["RecordCount"])) {
			return;
		}
		foreach ($activities["ProspectActivities"] as $activity) {

			if (!empty($activity)) {
				put_activity($lead_id, $activity);
			}

		}

	}

	function put_activity($lead_id, $activity) {

		$sanitized_activity_id = db_sanitize($activity["Id"]);
		$activity = db_sanitize(json_encode($activity));
		$res = db_query("SELECT id FROM crm_activities WHERE activity_id = $sanitized_activity_id;");
		if (!empty($res)) {
			db_exec("UPDATE crm_activities activity = $activity WHERE id = ".$res[0]["id"]);
		}
		else {
			db_exec("INSERT INTO crm_activities (lead_id, activity_id, activity) VALUES ($lead_id, $sanitized_activity_id, $activity);");
		}

	}

?>