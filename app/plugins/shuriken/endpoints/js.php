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

	// Shuriken.JS
    	// This contains functions to Implement the shuriken script.
	// -------------------

	// Shuriken.JS-Related Functions
	// The following functions are related to functionality of the JS-based Shuriken endpoint
	// -------------------

	// The following hooks make the standard event queue functional on JS
	hook('shuriken_event_output_pageload','shuriken_js_eventkue_pageload');

  	// This function implements a security check at the start of JS endpoint
  	// It checks if the given referer reflects the URLs on which embedding the JS is allowed.
  	function shuriken_js_auth() {

  		// Parse referer
    		$GLOBALS['shuriken']['temp']['url'] = url_template_from_string($_SERVER['HTTP_REFERER']);

    		// Domain Check
    		if (!$GLOBALS['shuriken']['security']['domain_check']) return true;
    		else { 
    			if (($GLOBALS['shuriken']['temp']['url']['domain'] != 'jigsawacademy.com') && ($GLOBALS['shuriken']['temp']['url']['domain'] != 'jigsawacademy.net') && ($GLOBALS['shuriken']['temp']['url']['domain'] != 'analyticstraining.com') && ($GLOBALS['shuriken']['temp']['url']['domain'] != 'analyticscourses.in')) return false;

    			return true;
    		}
	}

	// This function triggers the JS endpoint and the event parsing
	function shuriken_js() {

  		// Output Verbosity
		$silent = isset($_REQUEST['xx']);
		if ($silent) shuriken_output_terminate();

		// Security Check
		if (!shuriken_js_auth()) die();
		$kue;

		// Parse page token
		if (isset($_REQUEST['iii'])) {
			$kue = shuriken_event_kue_get();
			if ($kue === false) die();

    			if (!$silent) shuriken_output_start();
    			shuriken_event_kue_parse($kue, false, 'pageprop');
		}

		// No page token
		else {			
    			if (!$silent) shuriken_output_start();
    			$kue = shuriken_event_kue_create();
    			shuriken_event_kue_parse($kue, true, 'pageload');
		}

		// Execute Event Output Handlers
		shuriken_event_kue_execute($kue, 'output');

    		// End Output Buffering and Close Client Connection
    		if (!$silent) shuriken_output_end('text/javascript', $GLOBALS['shuriken']['temp']['origin']);

    		// Execute Event Storage Handlers
		shuriken_event_kue_execute($kue, 'process');

    		// Store Event
		shuriken_event_kue_store($kue);

    		// Execute Event End Handlers
		shuriken_event_kue_execute($kue, 'end');
		
	}

	// Script-Related Functions
	// The following functions are related to outputting JS code
	// -------------------

	//  Event (output) : pageload, handler for page token output
	function shuriken_js_eventkue_pageload($kue, $i) {
		echo "var _shuriii='".((string) $kue['_id'])."', _xdmn=".(($GLOBALS['shuriken']['temp']['url']['domain'] == 'jigsawacademy.com') ? '!1' : '!0').";";
	}

	// This function will output a 'shuri' injector code for the given HTML fragment
	// This function can use the following selector types for injection - TagName, ClassName, Id, Name
	// Note : Selector types are case-sensitive
	// Note : Injector can parse multiple elements at once for the same Selector and Type. It is recommended to club fragments together for injection into the same selector and type.
	// WARNING ! Code must be properly sanitized (escaped) before being passed to this function !
  	function shuriken_js_inject($html, $selector='body', $selector_type = 'TagName') {
  		echo "shuri(decodeURIComponent(('".urlencode($html)."').replace(/\+/g, '%20')),'".$selector."','".$selector_type."');";
  	}

  	// This function will output a ''document.ready" event listener function
  	function shuriken_js_domready_open() {
  		echo '$(document).ready(function(){';
  	}

  	// This function will output a ''window.load" event listener function
  	function shuriken_js_winload_open() {
  		echo "window.addEventListener('load',function(){";
  	}

  	// This function will close the "document.ready" event listener function
  	function shuriken_js_compound_close() {
  		echo "});";
  	}




?>