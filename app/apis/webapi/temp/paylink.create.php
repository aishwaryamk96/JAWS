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
	if (!auth_session_is_logged()) die("You do not have the required priviledges to use this feature.");
	if (!auth_session_is_allowed("paylink.create")) die("You do not have the required priviledges to use this feature.");

	//Check
	if ((!isset($_POST["email"])) || (strlen($_POST["email"]) == 0)) echo json_encode(array("status" => false));

	// Init
	load_module("user");
	load_module("subs");
	load_module("course");

	// Prep User Info
	$email = trim($_POST["email"]);
	$name = trim($_POST["name"]);
	
	// Prep Subs + Pay Info
	$subs_info["combo"] = $_POST["combo"];
	$pay_info["instl_total"] = intval($_POST["instl_total"]);

	$sum = 0;
	for ($count = 1; $count <= $pay_info["instl_total"]; $count ++) {
		$pay_info["instl"][$count] = array(
			"instl_count" => $count,
			"instl_total" => $pay_info["instl_total"],
			"sum" => intval($_POST["instl"][$count][0]),
			"due_days" => intval($_POST["instl"][$count][1]),
			"create_entity_type" => "user",
			"create_entity_id" => $_SESSION["user"]["user_id"]
			);

		$sum += intval($_POST["instl"][$count][0]);
	}

	$pay_info["sum_basic"] = $sum;
	$pay_info["sum_total"] = $sum;
	$pay_info["currency"] = "inr";

	if (strcmp($_POST["paymode"], "online") == 0) $pay_info["status"] = "pending";
	else {
		$pay_info["status"] = "paid";
		$pay_info["instl"][1]["pay_mode"] = $_POST["paymode"];
	}

	// Exec
	if (subscribe($email, $subs_info, $pay_info, true, true, $name) === false) {

		// Subs was created successfully... set user as corp??
		if (isset($_POST["corp"]) && (strcmp($_POST["corp"], "y") == 0)) {
			$user = user_get_by_email($email);

			// Note : This is just a temporary solution - we are setting the lms_soc to corp as well - to avoid the whole survey process for corp students, as corp survey is not yet ready.. do not set it when corp survey flow is ready.
			// only account type should be set to 'corp' then.

			user_update($user["user_id"], array(
										"lms_soc" => "corp", 
										"account_type" => "corp"
			));
		}

		// done
		echo json_encode(false);
	}

	// failed
	else echo json_encode(true);

	//All done
	exit();

?>