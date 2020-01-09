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
		header('Location: https://www.jigsawacademy.com/index.php');
		die();
	}

	// Shuriken User-Defined Event Hooks (and Handlers)
	// -------------------

	// Hooks In
	hook('shuriken_event_output_test', 'shuriken_event_output_test');
	hook('shuriken_event_output_pageprop', 'shuriken_event_output_test');
	//hook('shuriken_event_output_form', 'shuriken_event_output_test');

	// Event : test, Handler for test output
	function shuriken_event_output_test($kue, $i) {

		echo "/*\n";
		var_dump($kue);
		echo "\n*/";
		if (isset($kue['v'][$i]['c'])) echo $kue['v'][$i]['c'].'();';

	}