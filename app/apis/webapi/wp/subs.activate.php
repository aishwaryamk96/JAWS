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

	// This API function receives a list of subscription IDs for whom the status has to be changed to active.

	// Auth Check
	if (!auth_api("subs.set")) die ("You do not have the required priviledges to use this feature.");

	load_module("subs");

	if (!isset($_POST["subs_ids"]))
		exit();

	foreach ($_POST["subs_ids"] as $subs_id)
	{
		$res = db_query("SELECT * FROM subs WHERE subs_id=".$subs_id.";");
		if ($res)
		{
			$res = $res[0];
			// Precautionary check to make sure the status of this subscription is "pending"
			if (strcmp($res["status"], "pending") == 0)
				db_exec("UPDATE subs SET status='active' WHERE subs_id=".$subs_id.";");
		}
	}

?>