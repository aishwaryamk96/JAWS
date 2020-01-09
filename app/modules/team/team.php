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
		header("HTTP/1.1 401 Unauthorized");
		die();
	}

	function team_create($team_id, $user_ids) {

		if (!is_array($user_ids)) {

			if (!team_is_member($team_id, $user_ids)) {
				db_exec("INSERT INTO team (team_id, user_id) VALUES (".$team_id.", ".$user_ids.");");
			}

		}
		else {

			$query = "INSERT INTO team (team_id, user_id) VALUES ";
			$values = "";
			foreach ($user_ids as $user_id) {

				if (!team_is_member($team_id, $user_id)) {
					$values .= "(".$team_id.", ".$user_id."),";
				}

			}

			if (strlen($values) > 0) {

				$query .= substr($values, 0, -1);
				db_exec($query);

			}

		}

	}

	function team_get($team_id) {

		$team = db_query("SELECT * FROM team WHERE team_id=".$team_id.";");
		if (!isset($team[0])) {
			return false;
		}
		return $team;

	}

	function team_set($team_id, $user_id) {

		if (!team_is_member($team_id, $user_id)) {
			db_exec("INSERT INTO team (team_id, user_id) VALUES (".$team_id.",".$user_id.");");
		}

	}

	function team_is_member($team_id, $user_id) {

		$is_member = db_query("SELECT * FROM team WHERE team_id=".$team_id." AND user_id=".$user_id.";");
		if (!isset($is_member[0])) {
			return false;
		}
		return true;

	}

	function team_remove_member($team_id, $user_id) {
		db_exec("DELETE FROM team WHERE team_id=".$team_id." AND user_id=".$user_id);
	}

	function team_get_all() {

		$teams = db_query("SELECT DISTINCT team_id FROM team;");
		if (!isset($teams[0])) {
			return false;
		}
		return $teams;

	}

?>