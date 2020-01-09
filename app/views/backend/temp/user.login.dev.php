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

    // Init Session
    auth_session_init();

	// Auth Check - Expecting Authorized Session Only !
	if ((!auth_session_is_logged()) || (!auth_session_is_allowed("dev"))) {
		header("HTTP/1.1 401 Unauthorized");
		die();
	}

	// Check Params
	if (empty($_GET["email"])) {
		header("HTTP/1.1 401 Unauthorized");
		die();
	}

	// Load stuff
	load_module("ui");

    // Try Login
    $status = auth_session_login_forced($_GET["email"]);

    // Check
    if ($status) {

    	ui_render_msg_front(array(
            "type" => "info",
            "title" => "JAWS Developer Access",
            "header" => "User Account",
            "text" => "You have switched to the following account<br/><br/><br/><b>".$_SESSION['user']["name"]."</b><br/><span style='color: rgba(0,0,0,0.5); font-size: 70%; line-height:70%;'>".$_SESSION['user']["email"]."<br/>".$_SESSION['user']["phone"]."</span>"
        ));

    }
    else route("404");

    // All done
    exit();

?>
