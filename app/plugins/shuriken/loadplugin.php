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

	// Shuriken - Marketing Automation & Analysis
	// Feature : Tag Management - Inject external scripts / pixel on any URL
	// Feature : Event Queue - Track clicks, pageviews, scrolls etc across all campaigns
	// Feature : Data Channel - Capture submitted user data.
	// ------------------

	// WARNING ! Allows client to disconnect but continue script execution. Might be exploited for DDOS.
    	ignore_user_abort(true);

	// Config - Internal plugin use
	// The following settings are set using variables but not constants, to allow use of test plugin simultanesouly with an autoloaded plugin

	$GLOBALS['shuriken']['plugin'] = "shuriken";					// Set to shuriken_test for test mode.
	if (!$GLOBALS['jaws_exec_live']) $GLOBALS['shuriken']['plugin'] = "shuriken_test";
	$GLOBALS['shuriken']['path'] = "app/plugins/".$GLOBALS['shuriken']['plugin'];// Set to actual plugin path for Live env !

    	$GLOBALS['shuriken']['security']['allow_output'] = true; 
    	$GLOBALS['shuriken']['security']['allow_process'] = true; 
    	$GLOBALS['shuriken']['security']['allow_storage'] = true; 			// Set to 'false' for HARDCORE SECURITY ONLY! Only process-stage hooked events will be stored!
    	$GLOBALS['shuriken']['security']['allow_storage_on_hook'] = true;		// Set to 'false' for HARDCORE SECURITY ONLY! All events will require handlers to explicitly allow storage!
    	$GLOBALS['shuriken']['security']['allow_end'] = true; 
    	$GLOBALS['shuriken']['security']['append_check'] = false;			// Enable for good security! No impact on perf!  - HAVING ISSUES !!!!!
    	$GLOBALS['shuriken']['security']['domain_check'] = true; 			// Enable only in live env! No impact on perf!
    	$GLOBALS['shuriken']['security']['token_check'] = true;  			// Requires all 3 tokens for JS endpoint! Will block synchronous X-Domain XHRs! No impact on perf!

    	$GLOBALS['shuriken']['storage']['pre_alloc'] = 128; 				// Mongo storage padding
    	$GLOBALS['shuriken']['storage']['collection_events'] = $GLOBALS['shuriken']['plugin']."_events";
    	$GLOBALS['shuriken']['storage']['collection_leads'] = $GLOBALS['shuriken']['plugin']."_leads";

	// Load Pre-requisites
	load_library('url');
	load_library('misc');
	load_plugin("mongodb");

	// Load stuff
	require_dir_once($GLOBALS['shuriken']['path']."/core");
	require_dir_once($GLOBALS['shuriken']['path']."/endpoints");
	require_dir_once($GLOBALS['shuriken']['path']."/hooks");	

	// Parse Header
	$GLOBALS['shuriken']['temp']['origin'] = '*';
    	foreach(getallheaders() as $header => $value) if (strtolower($header) == 'origin') $GLOBALS['shuriken']['temp']['origin'] = $value;

?>