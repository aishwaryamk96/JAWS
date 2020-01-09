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

	// This API function returns a list of all subscriptions that should start today and have "pending" as their status, plus the user information.

	if (!auth_api("subs.get")) {
		die ("You do not have the required priviledges to use this feature.");
	}

	load_module("user_enrollment");
	load_module("user");

	$res_lab_cred = db_query("SELECT enr.enr_id, user.name, enr.lab_user, enr.lab_pass FROM user_enrollment AS enr INNER JOIN user ON user.user_id=enr.user_id WHERE course_id IN (5,6, 109) AND enr_id>".$_POST["last_enr_id"].";");

	// If no subscriptions found that satisfy the criteria, return count = 0
	if (empty($res_lab_cred)) {
		die(json_encode(["count" => 0]));
	}

/*
	$labcred_arr = array();
	foreach ($res_lab_cred as $labcred)
	{
		$res_user = db_query("SELECT name FROM user WHERE user_id=".$labcred["user_id"]);
		$res_user = $res_user[0];

		$labcred["name"] = $res_user["name"];
		unset($labcred["user_id"]);

		$labcred_arr[] = $labcred;
	}
*/
	// Return the subscriptions list with its count
	die(json_encode(["count" => count($res_lab_cred), "labcreds" => $res_lab_cred], JSON_PARTIAL_OUTPUT_ON_ERROR));

?>
