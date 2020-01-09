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
    	-----------------------------------------
*/

	// Prevent exclusive access
	if (!defined("JAWS")) {
		header('Location: https://www.jigsawacademy.com');
		die();
	}

	// Check Auth
	// $headers = getallheaders();
	// if (empty($headers["Authorization"])) {

	// 	header("HTTP/1.1 403");
	// 	die;

	// }

	// $auth = strtolower(trim(substr($headers["Authorization"], strlen("bearer"))));
	// if ($auth != "rcavqs6310vmh6voveamcp74z8ihkc5pl6uu9284e2jdgijsvvt9k8n95vo3te6i") {

	// 	header("HTTP/1.1 403");
	// 	die;

	// }

	if (!isset($_POST["subs_id"]) || !isset($_POST["token"])) {

		header("HTTP/1.1 422");
		die;

	}

	// Get PSK
	$psk_info = psk_info_get($_REQUEST['token']);

	// PSK Check
	if ($psk_info === false) {

		header("HTTP/1.1 404");
		die;

	}
	psk_expire($psk_info['entity_type'], $psk_info['entity_id'], $psk_info['action']);

	$subs_id = db_sanitize($_POST["subs_id"]);

	// Get Activity
	// $act = db_query('SELECT content FROM system_activity WHERE act_id="'.$psk_info['entity_id'].'"');

	// Activity Check
	// if ($act[0]['content'] != $_REQUEST['subs_id']) die('false');
	// else die('true');
	$email = db_query("SELECT u.email FROM user AS u INNER JOIN subs AS s ON s.user_id = u.user_id WHERE s.subs_id = $subs_id;");
	if (empty($email)) {

		header("HTTP/1.1 422");
		die;

	}

	die(json_encode(["status" => true, "email" => $email[0]["email"]]));

?>