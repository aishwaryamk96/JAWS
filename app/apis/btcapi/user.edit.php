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

	if (empty($_POST["userId"])) {

		header("HTTP/1.1 400 Bad Request");
		die();

	}

	if (empty(($user = db_query("SELECT * FROM user WHERE user_id = ".db_sanitize($_POST["userId"]).";")))) {

		header("HTTP/1.1 400 Bad Request");
		die();

	}
	$user = $user[0];
	$msg = [];

	if (!empty($_POST["freeze_date"]) && !empty($_POST["unfreeze_date"])) {

		$freeze = date_create_from_format("d M, Y", $_POST["freeze_date"]);
		$unfreeze = date_create_from_format("d M, Y", $_POST["unfreeze_date"]);

		db_exec("INSERT INTO freeze (user_id, start_date, end_date, is_free, approved_by) VALUES (".$user["user_id"].", ".db_sanitize($freeze->format("Y-m-d H:i:s")).", ".db_sanitize($unfreeze->format("Y-m-d H:i:s")).", 1, ".$_SESSION["user"]["user_id"].");");
		$freeze_id = db_get_last_insert_id();

		$diff = date_diff($unfreeze, $freeze);
		$_POST["days"] = $diff->format("%a");

		$subs = db_query("SELECT * FROM subs WHERE status='active' AND user_id = ".$user["user_id"].";");
		foreach ($subs as $sub) {
			access_duration_update($sub, $freeze_id);
		}

		$freeze = $freeze->format("d F, Y");
		$unfreeze = $unfreeze->format("d F, Y");
		$response["id"] = $freeze_id;

		db_exec("INSERT INTO user_logs (user_id, category, created_by, description, resolved_by) VALUES (".$user["user_id"].", 'profile.edit', ".$_SESSION["user"]["user_id"].", ".db_sanitize("Freeze added from $freeze to $unfreeze.").", ".$_SESSION["user"]["user_id"].");");

	}

	$lms_soc = $user["lms_soc"];

	$profile = [
		"name" => false,
		"email" => false,
		"email_2" => false,
		"phone" => false,
		"lms_soc" => false,
		"soc_fb" => true,
		"soc_gp" => true,
		"soc_li" => true,
	];

	$query = "UPDATE user SET ";
	$set = [];
	$removed_soc = [];
	$logs = [
		"removed" => [],
		"modified" => [],
		"user_id" => $user["user_id"]
	];
	foreach ($profile as $key => $value) {

		if ($value && !empty($_POST["remove_soc"][substr($key, 4)])) {

			$set[] = $key." = NULL";
			$removed_soc[] = substr($key, 4);
			$logs["removed"][] = strtoupper(substr($key, 4));

		}
		else if (isset($_POST[$key])) {

			if (($value = sanitize($_POST[$key], $key, $user, ($key == "email_2" ? true : false))) !== false) {

				if ($key == "email_2" && !empty($value)) {

					$check = db_sanitize($value);
					if (!empty(db_query("SELECT * FROM user WHERE email LIKE $check OR email_2 LIKE $check OR ((soc_fb LIKE $check OR soc_gp LIKE $check OR soc_li LIKE $check) AND user_id != ".$user["user_id"].");"))) {

						$msg[] = "'".$value."' is already in use";
						continue;

					}

				}

				$set[] = $key." = ".(is_null($value) ? "NULL" : db_sanitize($value));
				if ($key == "lms_soc") {

					$lms_soc = $value;
					$logs["modified"]["JLC Social Login"] = ["o" => $user[$key], "n" => $value];

				}
				else {
					$logs["modified"][ucfirst($key)] = ["o" => $user[$key], "n" => $value];
				}

			}

		}

	}

	if (in_array($lms_soc, $removed_soc)) {
		die(json_encode(["status" => false, "msg" => "Login channel cannot be one of the removed social logins. Please change the Login channel"]));
	}

	if (!empty($set)) {

		$query .= implode(", ", $set)." WHERE user_id = ".$user["user_id"].";";
		db_exec($query);

	}

	if (isset($_POST["jig_id"])) {

		$res_jig_id = db_query("SELECT sis_id FROM user_enrollment WHERE user_id = ".$user["user_id"].";");
		if (isset($res_jig_id[0])) {

			if ($res_jig_id[0]["sis_id"] != $_POST["jig_id"]) {

				$query = "UPDATE user_enrollment SET sis_id = ".db_sanitize($_POST["jig_id"]).($_POST["lab_user_update"] == true ? ", lab_user = ".db_sanitize($_POST["jig_id"]) : "")." WHERE user_id = ".$user["user_id"].";";
				db_exec($query);
				$logs["modified"]["Jig ID"] = ["o" => $res_jig_id[0]["sis_id"], "n" => $_POST["jig_id"]];

			}

		}

	}

	log_changes($logs);

	die(json_encode(["status" => true, "msg" => (empty($msg) ? "" : implode("\n", $msg))]));

	function sanitize($elem, $elem_type, $user, $nullable = false) {

		if (!empty($elem) && !ctype_space($elem)) {

			$elem = trim($elem);
			if ($elem == $user[$elem_type]) {
				return false;
			}

			return $elem;

		}

		return $nullable ? null : false;

	}

	function log_changes($logs) {

		$description = "";
		$modified = "";
		if (!empty($logs["modified"])) {
			$modified = implode_and($logs["modified"]);
		}

		$removed = "";
		if (!empty($logs["removed"])) {
			$removed = implode_and($logs["removed"]);
		}

		if (!empty($removed)) {
			$description = $removed." social login".(count($logs["removed"]) > 1 ? "s were" : " was")." removed.";
		}
		if (!empty($modified)) {
			$description .= $modified;
		}

		if (!empty($description)) {
			db_exec("INSERT INTO user_logs (user_id, category, created_by, description, resolved_by) VALUES (".$logs["user_id"].", 'profile.edit', ".$_SESSION["user"]["user_id"].", ".db_sanitize($description).", ".$_SESSION["user"]["user_id"].");");
		}

	}

	function implode_and($arr) {

		$res = [];
		foreach ($arr as $key => $value) {

			if (array_key_exists("o", $value) || array_key_exists("n", $value)) {

				if (is_null($value["o"]) && is_null($value["n"])) {
					continue;
				}

				if (is_null($value["n"])) {
					$res[] = $key." removed";
				}
				else if (is_null($value["o"])) {
					$res[] = $key." set to ".$value["n"];
				}
				else {
					$res[] = $key." changed from ".$value["o"]." to ".$value["n"];
				}

			}
			else {

				if (is_array($value)) {
					db_exec("INSERT INTO system_log (source, data) VALUES ('btc.user.edit', ".db_sanitize(json_encode($value)).");");
				}
				$res[] = $value;

			}

		}

		$sub_arr = implode(", ", array_slice($res, 0, -1));
		return (!empty($sub_arr) ? $sub_arr." and " : "").array_pop($res);

	}

	function access_duration_update($subs, $freeze_id = null) {

		// $subs = db_query("SELECT * FROM subs WHERE subs_id=".$subs_id);
		// $subs = $subs[0];

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
			$data = ["orig" => var_export($end_date_ext, true)];
			$end_date_ext->add(new DateInterval($interval));
			$data["new"] = var_export($end_date_ext, true);
			$data = db_sanitize(json_encode($data));
			db_exec("INSERT INTO system_log (source, data) VALUES ('access.edit.freeze', $data);");

		}

		db_exec("INSERT INTO access_duration (user_id, subs_id, start_date, end_date".(!empty($freeze_id) ? ", freeze_id" : "").") VALUES (".$subs["user_id"].", ".$subs["subs_id"].", ".db_sanitize($subs["start_date"]).", ".db_sanitize($end_date_ext->format("Y-m-d H:i:s")).(!empty($freeze_id) ? ", ".$freeze_id : "").");");

		return $end_date_ext;

	}

?>