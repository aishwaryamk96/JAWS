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

   	// This gets all webinars by category and their associated next upcoming session.
	// Note : Persistence is not yet implemented on this API !!
  	// --------

    	// Auth check
    	if (!auth_api ("webinar.get")) die ("You do not have sufficient privileges to perform this operation");

    	// Load Stuff
    	load_module("webinar");

    	// Check
    	if (!isset($_POST["category"])) die("You do not have sufficient privileges to perform this operation");

    	// Load the webinars & session
    	$ret = webinar_get_by_category($_POST["category"]);

    	if ($ret === false) echo json_encode(array("status" => false));
    	else {   
    		$webinars = array();
          		foreach($ret as $webinar) {
          			$sess = webinar_session_get_next($webinar["webinar_id"]);
    			if ($sess !== false) $webinar["webinar_session"] = $sess;
    			$webinars[] = $webinar;
          		}

    		echo json_encode(array(
    			"status" => true,
    			"webinars" => $webinars
    		));
    	}
    	
    	// All done
    	exit();   

?>

