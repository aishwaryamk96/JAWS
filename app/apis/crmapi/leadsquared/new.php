<?php

	load_plugin("leadsquared");

	$data = file_get_contents("php://input");
	if (!empty($data)) {

		$data = json_decode($data, true);

		// db_exec("INSERT INTO system_log (source, data) VALUES ('leadsquared', ".db_sanitize(print_r($data, true)).")");

		$email = $data["EmailAddress"];
		$phone = $data["Phone"];
		$lead_id = $data["ProspectID"];

		update_lead_info($email, $phone, $lead_id, $data);

		// $inserts[] = "(".db_sanitize($email).",".db_sanitize($lead_id).",".db_sanitize(json_encode($data)).")";

		// if (!empty($inserts)) {
		// 	db_exec("INSERT INTO ls_leads (email, lead_id, lead_data) VALUES ".implode(",", $inserts).";");
		// }

	}

?>