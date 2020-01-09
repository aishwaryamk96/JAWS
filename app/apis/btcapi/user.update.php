<?php

	// Prevent exclusive access
	if (!defined("JAWS")) {
		header("HTTP/1.1 401 Unauthorized");
		die();
	}

	// Init Session
	auth_session_init();

	// Auth Check - Expecting Session Only !
	if ((!auth_session_is_logged()) || (!auth_session_is_allowed("support")) || (!auth_session_is_allowed("enrollment.get.adv"))) {
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

	if (count($_POST) == 0) {
		$_POST = json_decode(file_get_contents('php://input'), true);
	}

	$response;

	if (!empty($_POST["freezeDate"]) && !empty($_POST["unfreezeDate"])) {

		$freeze = date_create_from_format("d M, Y", $_POST["freezeDate"]);
		$unfreeze = date_create_from_format("d M, Y", $_POST["unfreezeDate"]);

		db_exec("INSERT INTO freeze (user_id, start_date, end_date, is_free, approved_by) VALUES (".$_POST["user_id"].", ".db_sanitize($freeze->format("Y-m-d H:i:s")).", ".db_sanitize($unfreeze->format("Y-m-d H:i:s")).", 1, ".$_SESSION["user"]["user_id"].");");
		$freeze_id = db_get_last_insert_id();

		$diff = date_diff($unfreeze, $freeze);
		$_POST["days"] = $diff->format("%a");

		$subs = db_query("SELECT * FROM subs WHERE status='active' AND user_id = ".$_POST["user_id"].";");
		foreach ($subs as $sub) {
			access_duration_update($sub["subs_id"], $freeze_id);
		}

		$response["freeze"] = $freeze->format("d F, Y");
		$response["unfreeze"] = $unfreeze->format("d F, Y");
		$response["id"] = $freeze_id;

	}
	else if (!empty($_POST["subs_id"]) && (!empty($_POST["end_date_ext"]) || !empty($_POST["days"]))) {
		$response["end_date_ext"] = access_duration_update($_POST["subs_id"]);
	}

	$response["success"] = true;
	die(json_encode($response));

	function access_duration_update($subs_id, $freeze_id = null) {

		$subs = db_query("SELECT * FROM subs WHERE subs_id=".$subs_id);
		$subs = $subs[0];

		$end_date_ext;
		if (!empty($_POST["end_date_ext"])) {
			$end_date_ext = date_create_from_format("d M, Y", $_POST["end_date_ext"]);
		}
		else if (!empty($_POST["days"])) {

			$access_duration = db_query("SELECT * FROM access_duration WHERE subs_id=".$subs["subs_id"]." ORDER BY id DESC LIMIT 1;");
			if (!isset($access_duration[0])) {

				db_exec("INSERT INTO access_duration (user_id, subs_id, start_date, end_date) VALUES (".$subs["user_id"].", ".$subs["subs_id"].", ".db_sanitize($subs["start_date"]).", ".db_sanitize($subs["end_date"]).");");
				if (!empty($subs["end_date_ext"])) {

					db_exec("INSERT INTO access_duration (user_id, subs_id, start_date, end_date) VALUES (".$subs["user_id"].", ".$subs["subs_id"].", ".db_sanitize($subs["start_date"]).", ".db_sanitize($subs["end_date_ext"]).");");
					$end_date_ext = date_create_from_format("Y-m-d H:i:s", $subs["end_date_ext"]);
				}
				else {
					$end_date_ext = date_create_from_format("Y-m-d H:i:s", $subs["end_date"]);
				}

			}
			else {
				$end_date_ext = date_create_from_format("Y-m-d H:i:s", $access_duration[0]["end_date"]);
			}

			$interval = "P".$_POST["days"]."D";
			$end_date_ext->add(new DateInterval($interval));

		}

		db_exec("INSERT INTO access_duration (user_id, subs_id, start_date, end_date".(!empty($freeze_id) ? ", freeze_id" : "").") VALUES (".$subs["user_id"].", ".$subs["subs_id"].", ".db_sanitize($subs["start_date"]).", ".db_sanitize($end_date_ext->format("Y-m-d H:i:s")).(!empty($freeze_id) ? ", ".$freeze_id : "").");");

		return $end_date_ext;

	}

?>