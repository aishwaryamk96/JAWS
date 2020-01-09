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

	load_plugin("mobile_app");

	$mobile = new MobileApp;

	$headers = getallheaders();
	if (!$mobile->authorizeRequest($headers["Authorization"])) {

		header("HTTP/1.1 401 Unauthorized");
		die();

	}

	load_module("webinar");
	load_module("user");

	load_library("email");

	if (!isset($_POST["email"]) || !isset($_POST["id"])) {

		header("HTTP/1.1 401 Unauthorized");
		die();

	}

	$user = user_get_by_email($_POST["email"]);

	if (!$user) {

		header("HTTP/1.1 401 Unauthorized");
		die();

	}

	if (webinar_session_reg($_POST["id"], $user["user_id"])) {

		$webinar_session = webinar_session_get_by_id($_POST["id"]);
		$start_date = date_create_from_format("Y-m-d H:i:s", $webinar_session["start_date"]);
		// Prep
	    $content["subject"] = "Jigsaw Academy Webinar Registration Confirmed";
	    $content["fname"] = $user["name"];
	    $content["header"] = "Jigsaw Academy Webinar Registration Confirmed";
	    $content["sub-header"] = "You have registered for webinar on ".$start_date->format("d-m-Y H:i").".";
	    $content["text"] = "You have registered for a webinar on ".$start_date->format("d-m-Y").", at ".$start_date->format("H:i").". Please visit the following link to participate in the webinar:<br />http://meet99498248.adobeconnect.com/jawebinar2016/<br /><br />If you would like more information or have any questions, please feel free to contact us at any time. <br /><br />With Best Wishes <br />Team Jigsaw Academy <br />Contact: info@jigsawacademy.com | +91 92435-222-77 <br /><br />";

	    // Send custom email
	    $template = "wp.formsubmit";
	    send_email($template, array("to" => $_POST["emailid"], "subject" => "Jigsaw Academy Webinar registration confirmed"), $content);

		die(json_encode(["success" => 1]));

	}
	else {

		header("HTTP/1.1 500");
		die();

	}

?>