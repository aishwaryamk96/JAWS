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
	if (!auth_api("shuriken")) die("You do not have the required priviledges to use this feature.");

	// Init
	load_plugin("shuriken");

	$result = array();
	$url_from_string = url_template_from_string($_POST['url_value']);
	$template_sanitized = url_template_sanitize($url_from_string);
	$parsed_url = url_template_to_string($template_sanitized);

	if($template_sanitized['status']!=1) {
		$result['error'] = $template_sanitized['error'];
		$result['parsed_url'] = $parsed_url;
	}
	
	else {
		$result['status'] = $template_sanitized['status'];
		$result['parsed_url'] = $parsed_url;
	}

	 echo json_encode($result);
?>