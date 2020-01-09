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

	// Gets the value for the specified setting. $value specifies what to return in case the setting is not found.
	function setting_get($setting, $value = false) {

		$setting = db_sanitize($setting);
		$res_value = db_query("SELECT value FROM system_setting WHERE setting=".$setting.";");

		// If the record is not found in the database or if the value is not set, return the value specified value param
		if (!$res_value || !isset($res_value[0]["value"]) || strlen($res_value[0]["value"]) == 0) {
			return $value;
		}

		return $res_value[0]["value"];

	}

	// Sets the setting-value pair specified
	function setting_set($setting, $value) {

		$setting = db_sanitize($setting);
		$value = db_sanitize($value);

		$res_value = db_query("SELECT value FROM system_setting WHERE setting=".$setting.";");
		if (!$res_value) {
			db_exec("INSERT INTO system_setting VALUES (".$setting.", ".$value.");");
		}
		else {
			db_exec("UPDATE system_setting SET value=".$value." WHERE setting=".$setting.";");
		}

	}