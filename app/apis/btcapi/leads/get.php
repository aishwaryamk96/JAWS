<?php

	// Prevent exclusive access
	if (!defined("JAWS")) {
		header("HTTP/1.1 401 Unauthorized");
		die();
	}

	// Send Headers
	header('Access-Control-Allow-Credentials: true');
	header("Content-Type: application/json");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Pragma-directive: no-cache");
	header("Cache-directive: no-cache");
	header("Cache-Control: private, must-revalidate, max-age=0, no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
	header("Pragma: no-cache");

	load_module("user");

	$email = db_sanitize($_GET["email"]);

	$records = db_query("SELECT * FROM user_leads_basic_compiled WHERE email LIKE $email;");

	$user_id = 0;
	$user = user_get_by_email($records[0]["email"]);
	if (!empty($user)) {
		$user_id = $user["user_id"];
	}

	$lead = [
		"email" => $records[0]["email"],
		"name" => $records[0]["name"],
		"phone" => $records[0]["phone"] ?: $user["phone"],
		"user_id" => $user_id
	];

	$activities = [];

	// Look for lead records with __tr
	$lead_activities = db_query("SELECT lu.* FROM lead_uuids AS lu INNER JOIN user_leads_basic_compiled AS ul ON ul.__tr = lu.__tr WHERE ul.email LIKE $email AND ul.__tr IS NOT NULL GROUP BY ul.__tr;");
	foreach ($lead_activities as $uuid) {

		$uuid["sessions"] = [];

		$sessions = db_query("SELECT * FROM lead_sessions WHERE lead_uuid_id = ".$uuid["id"]);
		foreach ($sessions as $session) {

			$session["activities"] = db_query("SELECT * FROM lead_activities WHERE lead_session_id = ".$session["id"]);
			$uuid["sessions"][] = $session;

		}

		$activities[] = $uuid;

	}

	$res_crm = db_query("SELECT * FROM ls_leads WHERE email LIKE $email;");
	$crm = $res_crm[0];

	if (!empty($crm["meta"])) {
		$crm["meta"] = json_decode($crm["meta"], true);
	}

	load_plugin("leadsquared");
	$crm["crm"] = fetch_lead($crm["email"], true);
	if (!empty($crm["crm"])) {

		$crm["crm"] = $crm["crm"][0];

		$crm["crm"]["activities"] = [];
		$crm_activities = fetch_lead_activities($crm["crm"]["ProspectID"]);
		if ($crm_activities["RecordCount"] > 0) {
			$crm["crm"]["activities"] = array_reverse($crm_activities["ProspectActivities"]);
		}

		$crm["crm"]["tasks"] = fetch_lead_tasks($crm["crm"]["ProspectID"]);

	}

	die(json_encode(["lead" => $lead, "records" => $records, "activities" => $activities, "crm" => $crm]));

?>