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
	if (!auth_api("email.custom")) die("You do not have the required priviledges to use this feature.");

	// Load Stuff
	load_library("email");

	// Prep
	$content["fname"] = substr($_POST["name"], 0, ((strpos($_POST["name"], " ") !== false) ? strpos($_POST["name"], " ") : strlen($_POST["name"]))); 
	$content["header"] = $_POST["header"];
	$content["sub-header"] = $_POST["sub-header"];
	$content["text"] = $_POST["text"];
	$content["subject"] = ( isset($_POST["subject"]) && (strlen($_POST['subject']) > 0 ) ) ? $_POST["subject"] : "";
	$content["mail_data"] = $_POST["mail_data"];

	$attachments = (!empty($_POST['attachments'])) ? $_POST['attachments'] : '';

	// Send custom email
	$template = !empty($_POST["template"]) ? $_POST["template"] : "wp.formsubmit";
	
    // added template in contentto distinguish content for display in browser link.
	$content["template"] = $template;
	
	//activity_create("ignore","custom.email","send_email","","","","",json_encode($content));
	if(!empty($attachments)){
		send_email_with_attachment($template, array("to" => $_POST["email"]), $content, $attachments);
	} else {
		send_email($template, array("to" => $_POST["email"], "subject" => $content["subject"] ), $content);
	}

    	// All done
    	echo json_encode(true);
    	exit();

?>