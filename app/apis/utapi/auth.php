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
		header('Location: http://www.jigsawacademy.com');
		die();
	}

	if (!auth_api("ut.auth")) {
		die("You do not have required priviledges to use this feature. :P");
	}

	if (!isset($_POST["username"]) || !isset($_POST["password"]) || !isset($_POST["domain"])) {
		die("You do not have required priviledges to use this feature.");
	}

	$res_credentials = db_query("SELECT * FROM lab_credentials_dummy WHERE username=".db_sanitize($_POST["username"])." AND password=".db_sanitize($_POST["password"])." AND domain=".db_sanitize($_POST["domain"]));

	if (isset($res_credentials[0])) {

		$res_credentials = $res_credentials[0];

		$ret["name"] = $res_credentials["name"];
		$ret["domain"] = $res_credentials["domain"];
		$ret["ip"] = $res_credentials["lab_ip"];
		$ret["start_date"] = $res_credentials["start_date"];
		$ret["end_date"] = $res_credentials["end_date"];
		$ret["freeze"] = "off";
		die(json_encode($ret));

	}

	$res_enr = db_query("SELECT * FROM user_enrollment AS enr INNER JOIN course_lab AS lab ON enr.course_id = lab.course_id WHERE lab.domain = ".db_sanitize($_POST["domain"])." AND enr.lab_user = ".db_sanitize($_POST["username"])." LIMIT 1;");

	if (!isset($res_enr[0])) {
		die(json_encode(array("Error" => "Student not found")));
	}

	$res_enr = $res_enr[0];
	if (strcmp($res_enr["lab_pass"], $_POST["password"]) != 0) {
		die(json_encode(array("Error" => "Invalid login credentials")));
	}

	load_module("user");
	load_module("subs");

	$user = user_get_by_id($res_enr["user_id"]);
	$subs = subs_get_info($res_enr["subs_id"]);

	$ret["name"] = $user["name"];
	$ret["domain"] = $_POST["domain"];
	$ret["ip"] = $res_enr["lab_ip"];
	$ret["start_date"] = $subs["start_date"];
	$ret["end_date"] = $subs["end_date"];

	if (!isset($subs["freeze_date"]) || strlen($subs["freeze_date"]) == 0) {
		$ret["freeze"] = "off";
	}
	else {

		$date = new DateTime("now");
		$freeze_date = new DateTime($subs["freeze_date"]);
		$unfreeze_date = new DateTime($subs["unfreeze_date"]);
		if ($date > $freeze_date && $date < $unfreeze_date) {
			$ret["freeze"] = "on";
		}
		else {
			$ret["freeze"] = "off";
		}

	}

	die(json_encode($ret));

?>
