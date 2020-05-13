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

	load_module("user");

        //JA-150 changes
        //Get logged-in user's seller-profile
        $role = getUserSellerId();
        $roleQuery = ($role === FALSE)? '' : " AND user.roles IN (".implode(',',$role).") ";
        
	$teams = db_query("SELECT DISTINCT team.team_id, user.name FROM team INNER JOIN user ON team.team_id=user.user_id ".$roleQuery);
        //JA-150 ends
        
	// Output
	echo json_encode([
		"approver" => auth_session_is_allowed(/*"payment.pay_mode.set"*/"sm") || auth_session_is_allowed("pm"),
		"pm" => auth_session_is_allowed("pm"),
		"filter_default" => json_decode(user_content_get($_SESSION["user"]["user_id"], "filter_default"), true),
		"teams" => $teams
	]);

	// Done
	exit();

?>
