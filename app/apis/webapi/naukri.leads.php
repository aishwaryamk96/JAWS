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

	// Auth check
	if (!auth_api ("naukri.leads.save")) die ("You do not have sufficient privileges to perform this operation.");

	$error = array();

	// Checks
	if ( empty($_REQUEST['name']) || (strlen(trim($_REQUEST['name'])) < 3) ) {
        $error['message'] = "Please provide name.";
    }
	if ( empty($_REQUEST['email']) || (strlen(trim($_REQUEST['email'])) < 3) || !filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL) ) {
        $error['message'] = "Please provide valid email.";
    }
	if ( empty($_REQUEST['phone']) || (strlen(trim($_REQUEST['phone'])) < 3) || !ctype_digit(trim($_REQUEST['phone'])) ) {
        $error['message'] = "Please provide phone number.";
    }

    if(empty($error)){

        // Insert
        $query="INSERT INTO
				user_leads_basic (name,email,phone,utm_source,utm_campaign,utm_medium,utm_content,ad_lp,ad_url,create_date,capture_trigger,capture_type,ip,meta,xuid)
			VALUES
				(
					".db_sanitize($_REQUEST['name']).",
					".db_sanitize($_REQUEST['email']).",
					".db_sanitize($_REQUEST['phone']).",
					".db_sanitize(($_REQUEST['utm_source'] ?? '')).",
					".db_sanitize(($_REQUEST['utm_campaign'] ?? '')).",
					".db_sanitize(($_REQUEST['utm_medium'] ?? '')).",
					".db_sanitize(($_REQUEST['utm_content'] ?? '')).",
					'naukri-lp',
					" . db_sanitize($_REQUEST['url'] ?? '') . ",
					NOW(),
					'formsubmit',
					'url',
					".db_sanitize($_REQUEST['ip4'] ?? '').",
					".db_sanitize(json_encode($_REQUEST['info'])).",
					".db_sanitize($_REQUEST["xuid"])."
				);";

        db_exec($query);

        echo json_encode(['status' => true]);

    } else {
        $error['status'] = false;
        echo json_encode($error);
    }

	exit();

?>
