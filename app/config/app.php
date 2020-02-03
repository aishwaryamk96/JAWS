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
		//header('Location: http://www.jigsawacademydev.com');
                header('Location: '.WEBSITE_URL);
		die();
	}
	
	// Set application settings
//	define("JAWS_PATH_LOCAL","jaws");
//	define("JAWS_PATH_WEB","http://www.jigsawacademydev.com/jaws");
//	define("JAWS_VERSION","2.0");
        
	// JAWS LIVE/TEST Mode
	// WARNING ! It is Highly recommended that the mode be set to live (true) in the gobal configuration at all times !	
	// WARNING ! Using Test (false) mode will result in the use of debug mode of the following features -
	// 1. Payment Library - All payments will be in test mode and no actual transaction will happen !
	// 2. HybridAuth Plugin - All login using social network APIs will hapeen through test app accounts !
	// 3. Email Library - All outgoing emails will be sent to email test accounts! No CC and BCC will be used !
	// USAGE - Please set value to false inside entrypoints (views/apis/tasks) for live testing/developemental purposes !
	// Keep global configuration value to true on Live environment, at all times ! 
	// Use false on local setup w/ different DB configuration !
	global $jaws_exec_live;
	$GLOBALS['jaws_exec_live'] = true; // true = live, false = test	

	// Email Test Recipients
	global $jaws_exec_test_email_to;
	$GLOBALS['jaws_exec_test_email_to'] = MAILS_TO;	

	

?>
