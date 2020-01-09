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

	load_plugin("mobile_app");

	if (!auth_api("mobapp.chat") || !isset($_POST["fwd_chat_room_id"]) || !isset($_POST["self_id"]) || !isset($_POST["fwd_other_id"]) || !isset($_POST["fwd_msg_id"]))
		die("You do not have required privileges to use this feature.");

	// Get the messages for the message IDs
	$msgs = array();
	$res_msgs = db_query("SELECT content FROM user_msg WHERE msg_id IN (".$_POST["fwd_msg_id"].")");
	foreach ($res_msgs as $msg) {
		$msgs[] = $msg["content"];
	}

	// From the plugin
	$mobileApp = new MobileApp();

	// Break the receivers and their types from the respective arrays
	$receivers = explode(",", $_POST["fwd_chat_room_id"]);
	$receiver_types = explode(",", $_POST["fwd_other_id"]);

	$response_arr = array();

	for ($i = 0; $i < count($receivers); $i++) {

		foreach ($msgs as $msg) {
			
			// The other ID for a course_section will be it's sis ID, which will not be numeric, but alphanumeric
			$objects = $mobileApp->msg_send((!intval($receiver_types[$i]) ? "course_section" : "user"), $receivers[$i], $_POST["self_id"], $msg);

			// Append the message received from the returned object into the response array
			$response_arr[] = $object["android"]->getMessage();

		}

	}

	die (json_enode(["Items" => $response_arr, "Count" => count($response_arr), "ScannedCount" => count($response_arr)]));

?>