<?php

/*
           8 8888       .8. `8.`888b                 ,8' d888888o.
           8 8888      .888. `8.`888b               ,8'.`8888:' `88.
           8 8888     :88888. `8.`888b             ,8' 8.`8888.   Y8
           8 8888    . `88888. `8.`888b     .b    ,8'  `8.`8888.
           8 8888   .8. `88888. `8.`888b    88b  ,8'    `8.`8888.
           8 8888  .8`8. `88888. `8.`888b .`888b,8'      `8.`8888.
88.        8 8888 .8' `8. `88888. `8.`888b8.`8888'        `8.`8888.
`88.       8 888'.8'   `8. `88888. `8.`888`8.`88'     8b   `8.`8888.
  `88o.    8 88'.888888888. `88888. `8.`8' `8,`'      `8b.  ;8.`8888
    `Y888888 ' .8'       `8. `88888. `8.`   `8'        `Y8888P ,88P'

    JIGSAW ACADEMY WORKFLOW SYSTEM v1
    ---------------------------------
*/

	// Prevent exclusive access
	if (!defined("JAWS")) {
		header('Location: ../index.php');
		die();
	}


	// This module handles generation and use of Pre-Shared Keys (PSK)


	// This will create a new OTP token and return it
	function psk_generate($entity_type, $entity_id, $action, $seed="", $validity = "", $validity_unit = "days", $force_expire = false, $length = 64) {

		// check - expire old if avail
		if ($force_expire) psk_expire($entity_type, $entity_id, $action);
		else {
			// Find
			$res = db_query("SELECT * FROM system_psk WHERE entity_type=".db_sanitize($entity_type)." AND entity_id=".$entity_id." AND action=".db_sanitize($action)." LIMIT 1;");
			if (isset($res[0])) return $res[0]["token"];
		}

		// Prep
		$token = substr(hash("sha256", strval(time()).$seed.$entity_type.$entity_id.$action), 0, $length);
		$entity_type = db_sanitize($entity_type);
		$action = db_sanitize($action);
		$create_date = date("Y-m-d H:i:s");
		$expire_date = "";
		if (strlen($validity > 0)) {
			$expire_date = strval(date('Y-m-d H:i:s', strtotime("+ ".$validity." ".$validity_unit)));
		}

		$expire_date = strlen($expire_date) ? db_sanitize($expire_date) : "NULL";
		$create_date = db_sanitize($create_date);

		// Exec
		db_exec("INSERT INTO system_psk (token, entity_type, entity_id, action, create_date, expire_date) VALUES (".db_sanitize($token).",".$entity_type.",".$entity_id.",".$action.",".$create_date.",".$expire_date.");");

		// Done
		return $token;

	}

	// This will fetch an PSK - it will also expire the PSK if its past validity period
	function psk_get($entity_type, $entity_id, $action) {

		// Prep
		$entity_type = db_sanitize($entity_type);
		$action = db_sanitize($action);

		// Find
		$res = db_query("SELECT * FROM system_psk WHERE entity_type=".$entity_type." AND entity_id=".$entity_id." AND action=".$action." LIMIT 1;");

		// Process
		if (!isset($res[0])) return false;
		else {

			// Process
			if (!isset($res[0]["expire_date"])) return $res[0]["token"];
			else {

				if ((time() - strtotime($res[0]["expire_date"])) < 0) return $res[0]["token"];
				else {
					db_exec("DELETE FROM system_psk WHERE psk_id=".db_sanitize($res[0]["psk_id"]).";");
					return false;
				}

			}
		}
	}

	// This will expire a PSK
	function psk_expire($entity_type, $entity_id, $action) {
		db_exec("DELETE FROM system_psk WHERE entity_type=".db_sanitize($entity_type)." AND entity_id=".$entity_id." AND action=".db_sanitize($action).";");
	}

	// This will see if a particular KEY is present already and retreive its info
	function psk_info_get($token) {
		$res = db_query("SELECT * FROM system_psk WHERE token=".db_sanitize($token)." LIMIT 1;");

		if (isset($res[0])) return $res[0];
		else return false;
	}



?>