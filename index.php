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

	// Initialize
	define("JAWS", "2");
	if (version_compare(PHP_VERSION, '5.3.10', '<')) die('Your host needs to use PHP 5.3.10 or higher to run JAWS!');
	date_default_timezone_set("Asia/Kolkata");
	error_reporting(0);

	// Load stuff	
	require_once "app/libraries/loader.php"; 
	load_config();

	// Route and begin
	if (!auth_is_cli()) route($_SERVER["REQUEST_URI"]);

	// Tasks
	else {
		if (!isset($argv[1])) die("JAWS - No task specified!");
		else route($argv[1]);
	}


?>
