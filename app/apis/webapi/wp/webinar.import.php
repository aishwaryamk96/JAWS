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

	if (!auth_api ("webinar.import"))
		die ("You do not have the required priviledges to use this feature.");

	// This API creates/updates webinars and webinar_session.
	// The webinars on the website are persisted through webinar_session, instead of webinar, because, webinar_session is an
	// instance of a webinar which has start_date and end_date and a faculty assigned for that webinar session

	load_module ("webinar");

	if (isset($_POST["webinar"]))
	{
		$webinar = $_POST["webinar"];

		if (is_persistent (array("layer" => $_POST["persistence"]["webinar"]["layer"], "type" => $_POST["persistence"]["webinar"]["type"], "id" => $webinar["id"])))
		{
			$webinar_id = get_native_id(array("layer" => $_POST["persistence"]["webinar"]["layer"], "type" => $_POST["persistence"]["webinar"]["type"], "id" => $webinar["id"]))["id"];
			// Update the webinar too, to reflect changes in name and description
			webinar_update($webinar_id, $webinar["title"], $webinar["desc"]);
			// Get the webinar_session with the start_date as specified in the POST array
			$res = db_query("SELECT webinar_session_id FROM webinar_session WHERE webinar_id=".$webinar_id." AND start_date=".db_sanitize($webinar["start_date"]));
			if (!$res)
				webinar_session_create($webinar_id, $webinar["start_date"], $webinar["end_date"], json_encode(array("bgnd_pic" => $webinar["bgnd_pic"], "faculty_pic" => $webinar["faculty_pic"])));
			else
				webinar_session_update($res[0]["webinar_session_id"], $webinar["start_date"], $webinar["end_date"], json_encode(array("bgnd_pic" => $webinar["bgnd_pic"], "faculty_pic" => $webinar["faculty_pic"])));
		}
		// Or, create a new one
		else
		{
			$webinar_id = webinar_create($webinar["title"], $webinar["desc"], $webinar["category"]);
			webinar_session_create($webinar_id, $webinar["start_date"], $webinar["end_date"], json_encode(array("bgnd_pic" => $webinar["bgnd_pic"], "faculty_pic" => $webinar["faculty_pic"])));
			persist($_POST["persistence"]["webinar"]["layer"], $_POST["persistence"]["webinar"]["type"], $webinar_id, $webinar['id'], false);
		}
	}

?>