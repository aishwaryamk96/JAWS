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
	// if (!auth_api("email.custom")) die("You do not have the required priviledges to use this feature.");

    $web_id = $_POST['web_id'];
    $course_id = $_POST['course_id'];
    
    $course = get_native_course_id($course_id, 'wppl');
    $course_id = $course['id'];

    $course_details = db_query("SELECT app_num_format FROM course WHERE course_id=" . db_sanitize($course_id) . ";");
    $format = $course_details[0]['app_num_format'];
    
    if( empty($format) ){
        echo json_encode( array('status'=> false) );
        exit;
    }
    
    $last_app_num = db_query( "SELECT `app_num` FROM `payment`  WHERE app_num LIKE '" . $format . "%' ORDER BY `create_date` DESC LIMIT 1");
    if(!empty($last_app_num)){
        $last_app_num = intval(substr($last_app_num[0]['app_num'], 6));
        $last_app_num++;
    } else {
        $last_app_num = "1000";
    }

    $application_number = $format . date('m') . date("d") . $last_app_num;

    // save application number in payment table
    db_exec("UPDATE payment AS p INNER JOIN payment_link AS pl ON (p.pay_id = pl.pay_id) SET p.app_num = " . db_sanitize($application_number) . " WHERE pl.web_id = " . db_sanitize($web_id) . ";");

    // All done
    echo json_encode( array('status' => true, 'application_number' => $application_number) );
    exit();

?>