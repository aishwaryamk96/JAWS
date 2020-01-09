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
			header('Location: ../index.php');
			die();
		}

		// SMS Library
		// ----------------
		
		// This library will send SMS using the default JAWS SMS Plugin
		// Do NOT load the plugin separately if you wish to use this library
		// ----------------

		// This will send an SMS
		function send_sms($to, $text) {
		 	try {
		 		load_plugin(JAWS_SMS_GATEWAY);
		 		return sms_send($to,$text);
		 	}
		 	catch (Exception $e) {
		 		try {
		 			return sms_send($to,$text);				
		 		}
		 		catch(Exception $e) {}
			}
		}


?>