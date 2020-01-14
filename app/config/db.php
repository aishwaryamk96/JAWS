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

	// Set DB Creds
	define("JAWS_DB_HOST", "localhost");
	define("JAWS_DB_NAME", "jaws");
	define("JAWS_DB_USER", "root");
	define("JAWS_DB_PASS", "techjini");

	// Set Mongo DB Creds
	define("JAWS_MONGO_HOST", "mongodb://localhost:27017");
	define("JAWS_MONGO_NAME", "jaws");
	define("JAWS_MONGO_USER", "jaws");
	define("JAWS_MONGO_PASS", "Jigsaw@1234");

	// Global DB objects to be used for functions
	global $jaws_db;
	global $jaws_mongo;
	
	// Misc MySQL DB Settings
	define("JAWS_DB_FETCHMODE_NAMED", 0);
	define("JAWS_DB_FETCHMODE_INDEXED", 1);
	define("JAWS_DB_FETCHMODE_OBJ", 2);

	$jaws_db["fetch_mode"] = JAWS_DB_FETCHMODE_NAMED;
	$jaws_db["error"] = ""; 
	$jaws_db["write_restrict"] = false;

	// Misc Mongo DB Settings
	define("JAWS_MONGO_AUTOLOAD", false);

?>
