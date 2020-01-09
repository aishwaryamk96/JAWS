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
	if (!auth_api("faqs.get")) die("You do not have the required priviledges to use this feature.");

	// Load prerequisites
	load_library("misc");

	$faqs_arr = content_get("mobile.faq");
	if (!$faqs_arr)
	{
		echo json_encode(array("Items" => false, "Count" => 0, "ScannedCount" => 0));
		exit();
	}
	$faqs_arr = json_decode($faqs_arr, true);
	echo json_encode(array("Items" => $faqs_arr, "Count" => count($faqs_arr), "ScannedCount" => count($faqs_arr)));
	exit();
?>