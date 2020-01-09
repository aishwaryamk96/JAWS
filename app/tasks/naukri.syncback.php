<?php

	$last_timestamp = db_query("SELECT value FROM system_setting WHERE setting='naukri.timestamp';");

	$where = "";
	if (!empty($last_timestamp)) {

		$where = " AND create_date > ".db_sanitize($last_timestamp[0]["value"]);
		db_exec("UPDATE system_setting SET value=NOW() WHERE setting='naukri.timestamp';");

	}
	else {
		db_exec("INSERT INTO system_setting VALUES ('naukri.timestamp', NOW());");
	}

	$res_leads = db_query("SELECT name, email, create_date FROM user_leads_basic WHERE ad_lp='naukri-lp'".$where.";");

	if (!empty($res_leads)) {

		load_library("email");
		send_email("naurkri.syncback", [], ["leads" => $res_leads]);

	}

?>