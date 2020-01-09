<?php

	die();

	load_module("user");

	$data = file("external/temp/end_dates.csv");
	foreach ($data as $line) {

		$line = explode(",", $line);

		$email = trim($line[0]);
		$user = user_get_by_email($email);
		if (empty($user)) {

			echo $email." not found<br>";
			continue;

		}

		$start_date = trim($line[1]);
		$subs = db_query("SELECT subs_id, start_date, DATE(end_date) AS end_date, status FROM subs WHERE user_id = ".$user["user_id"]." AND DATE(start_date) = ".db_sanitize($start_date).";");
		if (empty($subs)) {

			echo $email." and ".$start_date." not found<br>";
			continue;

		}

		$end_date = db_sanitize(trim($line[3]));
		$subs = $subs[0];
		// echo "[subs_id => ".$subs["subs_id"].", start_date => ".$subs["start_date"].", end_date => ".$subs["end_date"].", status => ".$subs["status"].", end_date_new => ".$end_date.", same? => ".($subs["end_date"] == $end_date ? 'Yes' : 'No')."]<br>";
		db_exec("UPDATE subs SET end_date = $end_date WHERE subs_id = ".$subs["subs_id"].";");

	}

?>