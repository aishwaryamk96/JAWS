<?php

	// Init Session
	auth_session_init();

	// Auth Check - Expecting Session Only !
	if ((!auth_session_is_logged()) || (!auth_session_is_allowed("enrollment.get.adv"))) {
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

	if (!auth_session_is_allowed("enrollment.get.adv")) {
		die(json_encode(["status" => false, "error" => "Are you trying to hack...? Coz, its not gonna work."]));
	}

	if (empty($_POST)) {
		$_POST = json_decode(file_get_contents("php://input"), true);
	}

	if (!isset($_POST["subs_id"])) {

		header("HTTP/1.1 400 Bad Request");
		die();

	}

	$res_subs = db_query(
		"SELECT
			subs.user_id,
			subs.subs_id,
			DATE_FORMAT(subs.start_date, '%Y-%m-%d') AS subs_start_date,
			DATE_FORMAT(subs.end_date, '%Y-%m-%d') AS subs_end_date,
			IF (ad.id IS NOT NULL, DATE_FORMAT(ad.start_date, '%Y-%m-%d'), NULL) AS ad_start_date,
			IF (ad.id IS NOT NULL, DATE_FORMAT(ad.end_date, '%Y-%m-%d'), NULL) AS ad_end_date
		FROM
			subs
		LEFT JOIN
			access_duration AS ad
			ON ad.subs_id = subs.subs_id
		WHERE
			subs.subs_id = ".db_sanitize($_POST["subs_id"]).";"
	);
	if (!isset($res_subs[0])) {
		die(json_encode(["status" => false, "error" => "Invalid subscription"]));
	}

	$user_id = $res_subs[0]["user_id"];
	$subs_id = $res_subs[0]["subs_id"];

	$start_dates = [];
	$end_dates = [];
	foreach ($res_subs as $subs) {

		$subs_start_date = $subs["subs_start_date"]." 00:00:00";
		if (!in_array($subs_start_date, $start_dates)) {
			$start_dates[] = $subs_start_date;
		}

		if(!empty($subs["ad_start_date"])) {

			$ad_start_date = $subs["ad_start_date"]." 00:00:00";
			if (!in_array($ad_start_date, $start_dates)) {
				$start_dates[] = $ad_start_date;
			}

		}

		$subs_end_date = $subs["subs_end_date"]." 00:00:00";
		if (!in_array($subs_end_date, $end_dates)) {
			$end_dates[] = $subs_end_date;
		}

		if (!empty($subs["ad_end_date"])) {

			$ad_end_date = $subs["ad_end_date"]." 00:00:00";
			if (!in_array($ad_end_date, $end_dates)) {
				$end_dates[] = $ad_end_date;
			}

		}

	}

	$logs = [
		"user_id" => $user_id,
		"extn" => [
			"sd" => "",
			"ed" => ""
		],
		"edit" => [
			"sd" => "",
			"ed" => ""
		]
	];

	$query_subs = "UPDATE subs SET ";
	$set_subs = [];

	$query_ad = "INSERT INTO access_duration ";
	$insert_ad = [];

	if (!empty($_POST["start_date"])) {

		if (($start_date = date_create_from_format("d M, Y", $_POST["start_date"])) === false) {
			die(json_encode(["status" => false, "error" => "Invalid start date"]));
		}

		$start_date_str = $start_date->format("Y-m-d")." 00:00:00";

		if (!in_array($start_date_str, $start_dates)) {

			if ($_POST["extn_start_date"]) {

				$insert_ad["start_date"] = db_sanitize($start_date_str);
				$logs["extn"]["sd"] = $start_date->format("d F, Y");

			}
			else {

				$set_subs[] = "start_date = ".db_sanitize($start_date_str);
				$logs["edit"]["sd"] = $start_date->format("d F, Y");

			}

		}

	}

	if (!empty($_POST["end_date"])) {

		if (($end_date = date_create_from_format("d M, Y", $_POST["end_date"])) === false) {
			die(json_encode(["status" => false, "error" => "Invalid end date"]));
		}

		$end_date_str = $end_date->format("Y-m-d")." 00:00:00";

		if (!in_array($end_date_str, $end_dates)) {

			if ($_POST["extn_end_date"]) {

				if (empty($insert_ad["start_date"])) {
					$insert_ad["start_date"] = db_sanitize(array_pop($start_dates));
				}

				$insert_ad["end_date"] = db_sanitize($end_date_str);
				if (!empty($_POST["paid_extn"])) {
					$insert_ad["is_free"] = 0;
				}
				$logs["extn"]["ed"] = $end_date->format("d F, Y");

			}
			else {

				$set_subs[] = "end_date = ".db_sanitize($end_date_str);
				$logs["edit"]["ed"] = $end_date->format("d F, Y");

			}

		}

	}

	$update = false;
	if (!empty($set_subs)) {

		$query_subs .= implode(", ", $set_subs)." WHERE subs_id = ".$subs_id.";";
		db_exec($query_subs);

		$update = true;

	}

	if (!empty($insert_ad)) {

		$insert_ad["user_id"] = $user_id;
		$insert_ad["subs_id"] = $subs_id;

		$query_ad .= "(".implode(", ", array_keys($insert_ad)).") VALUES (".implode(", ", $insert_ad).");";
		db_exec($query_ad);

		$update = true;

	}

	if ($update) {

		$description = "";
		if (!empty($logs["extn"]["sd"])) {
			$description = "New extension added, from ".$logs["extn"]["sd"];
		}
		else if (!empty($logs["edit"]["sd"])) {
			$description = "Access start date changed to ".$logs["edit"]["sd"];
		}

		$ed = "";
		if (!empty($logs["extn"]["ed"])) {
			$ed = "extended till ".$logs["extn"]["ed"];
		}
		else if (!empty($logs["edit"]["ed"])) {
			$ed = "changed to ".$logs["edit"]["ed"];
		}

		if (!empty($ed)) {
			$description .= (!empty($description) ? " and end" : "End")." date ".$ed.".";
		}

		if (!empty($description)) {
			db_exec("INSERT INTO user_logs (user_id, category, created_by, description) VALUES (".$logs["user_id"].", 'subs.access.edit', ".$_SESSION["user"]["user_id"].", ".db_sanitize($description).");");
		}

	}

	die(json_encode(["status" => true, "update" => $update]));

?>