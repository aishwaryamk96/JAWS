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

	// Shuriken.GIF
    	// This contains functions to Implement the shuriken pixel.
	// -------------------

	// Shuriken.GIF-Related Functions
	// The following functions are related to functionality of the GIF-based Shuriken endpoint
	// -------------------

	// This function is used to output the shuriken gif pixel on any website
	function shuriken_gif() {

		// Start Output Buffering
		shuriken_output_start();

		// Silent JS Mode
		if (isset($_REQUEST['iii'])) {

			// Output pixel and terminate output
			shuriken_gif_pixel();

			// End Output Buffering and Close Client Connection
    			shuriken_output_end('image/gif', $GLOBALS['shuriken']['temp']['origin']);

			// Start JS Endpoint in silent mode
			$_REQUEST['xx'] = '';
			shuriken_js();

		}

		// Gif Mode
		else {

			// Start Output Buffering
	    		shuriken_output_start();

			// Build Event
	    		$kue = shuriken_event_kue_create();
	          		shuriken_event_kue_parse($kue, false, 'pixelfire');

			// Execute Event Output Handlers
			shuriken_event_kue_execute($kue, 'output');			

			// End Output Buffering and Close Client Connection
	    		shuriken_output_end('image/gif');

	    		// Execute Event Storage Handlers
			shuriken_event_kue_execute($kue, 'process');

	    		// Store Event
			shuriken_event_kue_store($kue);

	    		// Execute Event End Handlers
			shuriken_event_kue_execute($kue, 'end');
		}

	}

	// Image-Related Functions
	// The following functions are related to outputting Image and GD processing
	// -------------------

	// Output the transparent GIF
	// Note: Using escape sequence is much faster than using base64_decode
	// Note: GIF are preferred over PNG for smaller data size
	function shuriken_gif_pixel() {
		echo "\x47\x49\x46\x38\x39\x61\x01\x00\x01\x00\x90\x00\x00\xff\x00\x00\x00\x00\x00\x21\xf9\x04\x05\x10\x00\x00\x00\x2c\x00\x00\x00\x00\x01\x00\x01\x00\x00\x02\x02\x04\x01\x00\x3b";
	}

	

?>