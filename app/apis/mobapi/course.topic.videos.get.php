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

	// Check Auth
	if (!auth_api("course.topic.videos.get")) die("You do not have the required priviledges to use this feature.");

    $data = array("topic_id" => $_POST["topic_id"], "topicindex" => $_POST["topicindex"], "enrollment" => $_POST["enrollment"]);
    if (isset($_POST["jigid"]) && strcmp($_POST["enrollment"], "y") == 0)
    {
    	$res_sis = db_query("SELECT sis_id FROM user_enrollment WHERE user_id=".$_POST["jigid"]." LIMIT 1");
    	if ($res_sis)
        	$data["jig_id"] = $res_sis[0]["sis_id"];
    }
    
	$opts = array('http' => array(
                  'method'  => 'POST',
                  'header'  => 'Content-type: application/x-www-form-urlencoded',
                  'content' => http_build_query($data)
                  )
                );
    $context  = stream_context_create($opts);
    echo file_get_contents("https://jigsawacademy.net/app/getcoursevideos.php", false, $context);
	exit();
?>