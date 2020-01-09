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

	// Value Leaf SMS Gateway
	// ---------------------

	function sms_send($to, $text) {

		// Prep
    	$context = stream_context_create(['http' => [
              	'method'  => 'GET',
               	'header'  => "content-type: application/x-www-form-urlencoded\r\ncache-control: no-cache"
            ]
        ]);

    	// Send
    	return file_get_contents(JAWS_SMS_VALUELEAF_URL."?method=sms&api_key=".JAWS_SMS_VALUELEAF_KEY."&to=".$to."&sender=".JAWS_SMS_VALUELEAF_SID."&message=".urlencode($text), false, $context);

	}