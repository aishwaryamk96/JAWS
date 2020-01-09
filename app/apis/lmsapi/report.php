<?php

	if (isset($_POST["t"])) {

		// $user = db_query("SELECT user.user_id FROM user INNER JOIN system_psk AS psk ON psk.entity_id = user.user_id WHERE psk.action = 'jlc.profile.report' AND psk.token = ".db_sanitize($_GET["token"]).";");
		$user = db_query("SELECT * FROM user WHERE web_id = ".db_sanitize($_POST["t"]).";");
		if (empty($user)) {

			header("HTTP/1.1 401");
			die();

		}

		unset($_POST["t"]);

		$user = $user[0];
		// psk_expire("user", $user["user_id"], "jlc.profile.report");

		db_exec("INSERT INTO user_issues (user_id, issue, created_by) VALUES (".$user["user_id"].", ".db_sanitize(json_encode($_POST)).",".$user["user_id"].");");

		// $report_token = psk_generate("user", $user["user_id"], "jlc.profile.report", "1", "days", true);
		die("Thank you. The issue has been logged successfully!\nPlease allow us a duration of 7 working days to process your request.");

	}
	else {
		die;
	}

?>