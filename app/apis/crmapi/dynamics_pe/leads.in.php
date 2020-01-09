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
		header('Location: https://www.jigsawacademy.com');
		die();
	}

	// Check Auth
	if (!auth_api("crm")) die("You do not have the required priviledges to use this feature.");

	load_module("leads");
	load_library("persistence");

	$lead["name"] = $_POST["name"];
	$lead["email"] = $_POST["email"];
	$lead["phone"] = $_POST["phone"];
	$lead["utm_source"] = $_POST["utm_source"];
	$lead["utm_campaign"] = $_POST["utm_campaign"];
	$lead["utm_term"] = $_POST["utm_term"];
	$lead["utm_medium"] = $_POST["utm_medium"];
	$lead["utm_content"] = $_POST["utm_content"];
	$lead["utm_segment"] = $_POST["utm_segment"];
	$lead["utm_numvisits"] = $_POST["utm_numvisits"];
	$lead["gcl_id"] = $_POST["gcl_id"];
	$lead["global_id_perm"] = $_POST["global_id_perm"];
	$lead["global_id_session"] = $_POST["global_id_session"];
	$lead["page_url"] = $_POST["page_url"];
	$lead["landing_url"] = $_POST["landing_url"];
	$lead["referer"] = $_POST["referer"];
	$lead["ip"] = $_POST["ip"];
	$lead["create_date"] = $_POST["create_date"];
	$lead["event"] = $_POST["event"];

	leads_basic_compiled_save($lead);

	//persist("dynpepl", "lead", $lead["email"], $lead["crmId"]);

	echo json_encode(array("status" => "success"));
	exit();

?>