<?php

	load_plugin("leadsquared");

	$data = file_get_contents("php://input");
	if (!empty($data)) {

		$data = json_decode($data, true);

		// db_exec("INSERT INTO system_log (source, data) VALUES ('lead.reassign', ".db_sanitize(json_encode($data)).");");

		update_lead_assignment($data);

	}

?>