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

    	JIGSAW ACADEMY WORKFLOW SYSTEM v2
    	---------------------------------
*/

	// Prevent exclusive access
	if (!defined("JAWS")) {
		header('Location: https://www.jigsawacademy.com');
		die();
	}

	// This API checks if the email being referred is already available with JAWS or is a new one
	load_module("user");

	if ($_POST["criterion"] == "email") {
		$user = user_get_by_email($_POST["text"]);
	}
	else {
		$user = db_query("SELECT * FROM user WHERE phone=".db_sanitize($_POST["text"]));
	}

	if ($user) {

		$subs = db_query("SELECT * FROM subs WHERE user_id=".$user["user_id"]);
		if (isset($subs[0])) {
			die (json_encode(array("error" => 1, "error_desc" => "Referral is already registerred with us.")));
		}

	}

	$res_act = db_query("SELECT content FROM system_activity WHERE act_type='jlc.referral' AND activity='referral';");

	if (isset($res_act[0])) {

		foreach ($res_act as $activity) {

			$content = json_decode($activity["content"], true)["r"];
			foreach ($content as $referral) {

				if ($_POST["criterion"] == "email") {
					$search = $referral["e"];
				}
				else {
					$search = $referral["p"];
				}

				if (strcmp($search, $_POST["text"]) == 0) {
					die (json_encode(array("error" => 1, "error_desc" => "Referral has been referred already.")));
				}

			}

		}

	}

	die (json_encode(array("result" => 1)));

?>