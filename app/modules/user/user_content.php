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
		header('Location: https://www.jigsawacademy.com');
		die();
	}

	function user_content_get($user_id, $key) {

		$value = db_query("SELECT value FROM user_content WHERE user_id=".$user_id." AND `key`=".db_sanitize($key));
		if (!$value) {
			return false;
		}

		return $value[0]["value"];

	}

	function user_content_set($user_id, $key, $value) {

		if (!user_content_get($user_id, $key)) {
			db_exec("INSERT INTO user_content (user_id, `key`, value) VALUES (".$user_id.", ".db_sanitize($key).", ".db_sanitize($value).")");
		}
		else {
			db_exec("UPDATE user_content SET value=".db_sanitize($value)." WHERE user_id=".$user_id." AND `key`=".db_sanitize($key));
		}

	}

?>