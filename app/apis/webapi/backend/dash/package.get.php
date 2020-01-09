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

	// Init Session
	auth_session_init();

	// Auth Check - Expecting Session Only !
	if ((!auth_session_is_logged()) || (!auth_session_is_allowed("dash"))) {
		header("HTTP/1.1 401 Unauthorized");
		die();
	}

	load_module("subs");

	if (!empty($_REQUEST["package_id"])) {

		/*$instl_arr = json_decode($package["instl"], true);
		$package["instl"] = "";
		foreach ($instl_arr as $instl) {
			$package["instl"][] = $instl;
		}*/
		$package = package_get($_REQUEST["package_id"]);
		if ((strcmp($package["creator_type"], "agent") == 0 && $package["creator_id"] == $_SESSION["user"]["user_id"]) || auth_session_is_allowed("package.superuser")) {
			die(json_encode($package));
		}
		else {
			header("HTTP/1.1 401 Unauthorized");
			die();
		}

	}

?>
