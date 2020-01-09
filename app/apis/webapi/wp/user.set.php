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

	// Check Auth
	if (!auth_api("user.set") && !auth_session_is_logged()) die("You do not have the required priviledges to use this feature.");

	// Init
	load_module("user");

	// Start
	if (!auth_session_is_logged()) $user = user_get_by_id($_POST["user_id"]);
	else $user = $_SESSION["user"];

	// current details and updating details
	/*$log = array(
		"current" => $user,
		"updated" => $_POST,
		"date" => date("Y-m-d h:i:s")
	);*/

	//activity_debug_start();
	//activity_debug_log(json_encode($log));

	// Check
	if (!$user) die("false");

	// Update
	user_update($user["user_id"], array(
		"name" => ((isset($_POST["name"])) ? $_POST["name"] : "" ),
		"phone" => ((isset($_POST["phone"])) ? $_POST["phone"] : "" ),
		"photo_url" => ((isset($_POST["photo_url"])) ? $_POST["photo_url"] : (auth_session_is_logged() ? $_SESSION["user"]["photo_url"] : ""))
		));

	$user = user_get_by_id($user["user_id"]);
	if (auth_session_is_logged()) $_SESSION["user"] = $user;
	echo "true";

	// Code to create lead record for person updating hpone number - compense for CRM
	try {
		if (isset($_POST["phone"]) && (strlen($_POST["phone"]) >= 10)) {
			$query="INSERT INTO
						user_leads_basic (
							user_id,
							name,
							email,
							phone,
							ad_lp,
							ad_url,
							create_date,
							capture_trigger,
							capture_type
						)
					VALUES
						(
							".$user['user_id'].",
							".db_sanitize($user['name']).",
							".db_sanitize($user['email']).",
							".db_sanitize($user['phone']).",
							".db_sanitize('www.jigsawacademy.com').",
							".db_sanitize('#profile').",
							".db_sanitize(strval(date("Y-m-d H:i:s"))).",
							'phoneupdate',
							'cookie'
						);";

			db_exec($query);
		}
	} catch(Exception $e) {}

	// Custom Temporary Code - Send email to leads team on obtaining phone number
	/*if ((isset($_POST["phone"])) && (strlen($_POST["phone"]) >= 6))  {

		$to = "leads@jigsawacademy.com, info@jigsawacademy.com";
		$subject = "User has edited phone number - ".$user["name"];
		$msgline = "An user has edited/added a phone number!";

		if ((!isset($user["phone"])) || (strlen($user["phone"]) < 6)) {
			$subject = "A new user has provided phone number - ".$user["name"];
			$msgline = "A new user has signed up and provided a phone number !";
		}

		$message = "
		<html>
		<head>
		</head>
		<body>
		".$msgline."<br/><br/>
		Name: ".$user["name"]."<br/>
		Phone: ".$_POST["phone"]."<br/>
		Email: ".$user["email"]."<br/>
		</body>
		</html>";

		// More headers
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		$headers .= 'From: <no-reply@jigsawacademy.com>' . "\r\n";
		$headers .= 'Bcc: soumik@jigsawacademy.com' . "\r\n";

		mail($to, $subject, $message, $headers);
	}*/

	// Done
	exit();

?>
