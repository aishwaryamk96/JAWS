<?php

	$data = file_get_contents("php://input");
	if (!empty($data)) {

		db_exec("INSERT INTO system_log (source, data) VALUES ('leads.activities.sync', ".db_sanitize($data).");");
		return;

		$data = json_decode($data, true);

		$email = db_sanitize($data["EmailAddress"]);
		$lead_id = db_sanitize($data["ProspectID"]);

		$sanitized_data = db_sanitize($data);

		$res = db_query("SELECT id FROM crm_leads WHERE email LIKE $email;");
		if (!empty($res)) {
			db_exec("UPDATE crm_leads SET lead_id = $lead_id, crm = $sanitized_data WHERE id = ".$res[0]["id"]);
		}
		else {
			db_exec("INSERT INTO crm_leads (email, lead_id, crm) VALUES ($email, $lead_id, $sanitized_data);");
		}

	}

?>