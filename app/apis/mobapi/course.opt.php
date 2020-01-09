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
	if (!auth_api("course.opt")) die("You do not have the required priviledges to use this feature.");

    load_module("user");
    load_module("course");
    load_module("activity");

    if (!isset($_POST["emailid"]) || !isset($_POST["course_id"]))
    {
    	echo json_encode(array("Items" => 0, "Count" => 0, "ScannedCount" => 0));
		exit();
    }

	$user = user_get_by_email($_POST["emailid"]);
	$course = course_get_info_by_id($_POST["course_id"]);

	if (!$user || !$course)
	{
		echo json_encode(array("Items" => 0, "Count" => 0, "ScannedCount" => 0));
		exit();
	}

	activity_create("ignore", "app.course.opt", "success", "course", $course["id"], "user", $user["id"], "Course opted for on Mobile App");

	$date = new DateTime("now");
	$start_date = $date->format("Y-m-d");
	$date->add(new DateInterval("P15D"));
	$end_date = $date->format("Y-m-d");
	$coupons_arr = array("couponid" => "JAAPP500", "startdate" => $start_date, "enddate" => $end_date, "validdays" => "15", "value" => "500");

	// Send the email
    $to = "leads@jigsawacademy.com";
    $subject = "Mobile App Lead";

    $message = "
    <html>
    <head>
    </head>
    <body>
    Dear Marketing Team,<br /> This is to state that user '".$user["name"]."' has shown interest in the course '".$course["name"]."' through Mobile App.<br /> The details of the user are as below <br /><br /> USERNAME : ".$user["name"]." <br /> EMAIL ID : ".$user["email"]." <br /> PHONE NUMBER : ".$user["phone"]." <br /><br /> He is assured of this coupon : JAAPP500. Pls do not forget to give a discount of Rs. 500. <br /><br /> With Best Wishes <br /> Mobile App Team <br /><br />
    </body>
    </html>";
    
    // More headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: <no-reply@jigsawacademy.com>' . "\r\n";
    $headers .= 'Cc: ravi@jigsawacademy.com' . "\r\n";
    mail($to, $subject, $message, $headers);

	echo json_encode(array("Items" => array($coupons_arr), "Count" => 1, "ScannedCount" => 1));
	exit();

?>