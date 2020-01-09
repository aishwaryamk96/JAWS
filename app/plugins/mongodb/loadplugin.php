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
	namespace MongoDB;

	// Prevent exclusive access
	if (!defined("JAWS")) {
	   	header('Location: https://www.jigsawacademy.com');
	   	die();
	}

	// This plugin implements the PHPLIB/MongoDB wrapper for the new MongoDB driver
	// This will re-use the same Mongo Manager object for connecting to the DB
	// This plugin can function parallelly with the db.php JAWS DB library

	// Load stuff
	class MongoDBLoader {
    		public static function load($className) { require_once("app/plugins/mongodb/src/".str_replace("\\", "/",  substr($className, strpos("MongoDB\\", $className) + strlen("MongoDB\\"))).".php"); }
	}
	
	spl_autoload_register(__NAMESPACE__."\\MongoDBLoader::load");
    	require_once("app/plugins/mongodb/src/functions.php");

