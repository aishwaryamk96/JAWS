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

    namespace wp_login;

	// Prevent exclusive access
	if (!defined("JAWS")) {
		header('Location: https://www.jigsawacademy.com/index.php');
		die();
	}

	function soc_to_session($soc)
	{
		if (strcmp($soc, "fb") == 0)
			$_SESSION["user"]["jlc.free.soc"] = $soc;
		elseif (strcmp($soc, "gp") == 0)
			$_SESSION["user"]["jlc.free.soc"] = $soc;
		elseif (strcmp($soc, "li") == 0)
			$_SESSION["user"]["jlc.free.soc"] = $soc;

		//if (!empty($_SESSION["user"]['lms_free_soc'])) $_SESSION["user"]["jlc.free.soc"] = $_SESSION["user"]['lms_free_soc'];
		//else {
		//	load_module('user');
		//	user_update($_SESSION["user"]['user_id'], ['lms_free_soc' => $soc]);
		//	$_SESSION["user"]["jlc.free.soc"] = $soc;
		//}
	}

?>