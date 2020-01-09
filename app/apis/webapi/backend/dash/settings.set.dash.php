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
		header("HTTP/1.1 401 Unauthorized");
		die();
	}

	// Init session
	auth_session_init();

	// Auth Check - Expecting session with dash priviledges !
	if ((!auth_session_is_logged()) || (!auth_session_is_allowed('dash'))) {
	 	header("HTTP/1.1 401 Unauthorized");
	 	die();
	}

	// Check
	if (empty($_REQUEST['settings'])) die(json_encode(['status' => false, 'msg'=>'No settings specified', 'code' => 1]));

	// Load stuff
	load_module('user');

	//	Overwrite
	if (isset($_REQUEST['overwrite'])) user_content_set($_SESSION["user"]["user_id"], "dash_default", json_encode($_REQUEST['settings']));

	// Parse
	else {
		$settings = user_content_get($_SESSION["user"]["user_id"], "dash_default");
		if ($settings === false) $settings = [];
		else $settings = json_decode($settings, true);
		foreach($_REQUEST['settings'] as $key => $value) $settings[$key] = $value;
		user_content_set($_SESSION["user"]["user_id"], "dash_default", json_encode($settings));
	}

	// Done
	die(json_encode(['status' => true, 'msg'=>'Your preferences have been saved.']));
?>
