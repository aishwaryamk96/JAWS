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

	// Shuriken - Output
	// This contains functions to buffer the output and manage client-server connection.
	// It helps close the client connection before performing time-consuming processing tasks like storage.
	// ------------------

	// Global Shuriken Cookie 
	$GLOBALS['shuriken']['cookies'] = [];

	// This function is used to buffer cookies to be output.
	// These cookies will be output when the output buffering is stopped and flushed.
	function shuriken_output_cookie($name, $value, $expiry, $path) {
		array_push($GLOBALS['shuriken']['cookies'], [
			'name' => $name,
			'value' => $value,
			'expiry' => $expiry,
			'path' => $path
		]);
	}

	// This function will start the PHP output buffer
	function shuriken_output_start() { ob_start(); }

	// This function will add headers, flush the buffered output and close the client connection, and then continue the script execution.
	// WARNING ! Cannot use session after this function has been called !
	function shuriken_output_end($content_type='text/javascript', $allow_origin = '*') {

		// Get Output length
		$ob_length = ob_get_length();

		// Output Headers - Cookies
		foreach($GLOBALS['shuriken']['cookies'] as $c) setcookie($c['name'], $c['value'], $c['expiry'], $c['path']);

		// Output Headers - CORS
		header('Access-Control-Allow-Origin: '.$allow_origin);		
    		header('Access-Control-Allow-Methods: GET, POST');
    		header('Access-Control-Allow-Credentials: true');

		// Output Headers - Content Descriptors
	    	header("Content-Type: ".$content_type.""); 
		header("Content-Length: ".$ob_length."");

		// Output Headers - Cache Control
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Pragma-directive: no-cache");
    		header("Cache-directive: no-cache");
    		header("Cache-Control: private, must-revalidate, max-age=0, no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
		header("Pragma: no-cache");

		// Flush and Close the connection
		shuriken_output_terminate();
	}

	// This will pre maturely terminate the client connection without any output
	function shuriken_output_terminate() {
		
		// Output Headers - Connection Close
		header("Connection: close");

		// Flush Buffer
		ob_end_flush();
		ob_flush();
		flush();

		// Close Session
		session_write_close();
		session_destroy();

		// End Request
		//fastcgi_finish_request();

	}

	

	

?>