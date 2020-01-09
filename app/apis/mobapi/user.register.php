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
	if (!auth_api("user.create")) die("You do not have the required priviledges to use this feature.");

	// Init
	load_module("user");

	if (!isset($_POST["emailid"]) || !isset($_POST["gcm_reg_id"]) || strlen($_POST["gcm_reg_id"]) == 0 || !isset($_POST["android_version"]) || strlen($_POST["android_version"]) == 0 || !isset($_POST["app_version"]) || strlen($_POST["app_version"]) == 0)
	{
		echo json_encode(array("Items" => "failure", "Count" => 0, "ScannedCount" => 0));
        exit();
	}

	$user = user_get_by_email($_POST["emailid"]);

	if (!$user) {

		if (!isset($_POST["firstname"]) || !isset($_POST["lastname"]) || !isset($_POST["soc"]))
		{
			echo json_encode(array("Items" => "failure", "Count" => 0, "ScannedCount" => 0));
        	exit();
		}

		$name = $_POST["firstname"]." ".$_POST["lastname"];

		if (strcmp($_POST["soc"], "facebook") == 0) $soc = "fb";
		else if (strcmp($_POST["soc"], "google") == 0) $soc = "gp";
		else if (strcmp($_POST["soc"], "linkedin") == 0) $soc = "li";

		$user = user_create($_POST["emailid"], substr(str_shuffle($name.str_replace("@", "0", str_replace(".", "", $_POST["emailid"]))), 0, 10), $name, $_POST["phone"], true);

		// Check for creation fail
		if ($user === false) die(json_encode(array("Items" => "failure", "Count" => 0, "ScannedCount" => 0)));

		user_update($user["user_id"], array(
			"soc_".$soc => $_POST["emailid"],
			"photo_url" => $_POST["photo_url"]
			));

		$value = json_encode(array($_POST["gcm_reg_id"]));
		user_content_set($user["user_id"], "gcm_id", $value);

		$lead_params = array();
    	$lead_params["ad_lp"] = "Android ".$_POST["android_version"];
    	$lead_params["type"] = "url";
    	$lead_params["trigger"] = "reg.android";
    	leads_basic_capture($lead_params, "id", $user["user_id"]);
	}
	else
	{
		$gcm_ids = user_content_get($user["user_id"], "gcm_id");
		if (!$gcm_ids)
		{
			$value = json_encode(array($_POST["gcm_reg_id"]));
			user_content_set($user["user_id"], "gcm_id", $value);
		}
		else
		{
			// If the value is blank, add this one
			if (strlen($gcm_ids) == 0)
			{
				$value = json_encode(array($_POST["gcm_reg_id"]));
				user_content_set($user["user_id"], "gcm_id", $value);
			}
			$gcm_ids = json_decode($gcm_ids, true);
			// If the json object is empty, add this one
			if (count($gcm_ids) == 0)
			{
				$value = json_encode(array($_POST["gcm_reg_id"]));
				user_content_set($user["user_id"], "gcm_id", $value);
			}
			// If 1 GCM ID is present, add this one
			else if (count($gcm_ids) == 1)
			{
				if (strcmp($gcm_ids[0], $_POST["gcm_reg_id"]) != 0)
				{
					$gcm_ids[] = $_POST["gcm_reg_id"];
					$value = json_encode($gcm_ids);
					user_content_set($user["user_id"], "gcm_id", $value);
				}
			}
			// If 2 GCM IDs are already present
			else if (count($gcm_ids) > 1)
			{
				// And none of them match the given GCM ID, delete the older GCM ID and save the new one
				if ((strcmp($gcm_ids[0], $_POST["gcm_reg_id"]) != 0) && (strcmp($gcm_ids[1], $_POST["gcm_reg_id"]) != 0))
				{
					unset($gcm_ids[0]);
					$gcm_ids[] = $_POST["gcm_reg_id"];
					$value = json_encode($gcm_ids);
					user_content_set($user["user_id"], "gcm_id", $value);
				}
			}
		}
	}

	echo json_encode(array("Items" => "success", "Count" => 1, "ScannedCount" => 1));
	exit();
?>