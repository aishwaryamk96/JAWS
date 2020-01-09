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

   	// This gets a webinar by ID and it's associated next upcoming session.
	// Note : Persistence is not yet implemented on this API !!
  	// --------

    	// Auth check
    	if (!auth_api ("webinar.get")) die ("You do not have sufficient privileges to perform this operation");

    	// Load Stuff
    	load_module("webinar");

    	// Check
    	if (!isset($_POST["webinar_id"])) die("You do not have sufficient privileges to perform this operation");

    	// Load the webinar & session
    	$webinar = webinar_get_by_id($_POST["webinar_id"]);

    	if ($webinar === false) echo json_encode(array("status" => false));
    	else {    		
    		$sess = webinar_session_get_next($_POST["webinar_id"]);
    		if ($sess !== false) $webinar["webinar_session"] = $sess;
    		echo json_encode(array(
    			"status" => true,
    			"webinar" => $webinar
    		));
    	}
    	
    	// All done
    	exit();   

?>

