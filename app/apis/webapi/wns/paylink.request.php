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
	// No auth is being checked for now !

	//load stuff
	load_library("email");
	
	// Send the email
	$to = "sales@jigsawacademy.com, payments@jigsawacademy.com";
	$subject = "WNS Nomination - ".$_POST["name"];

	$message = "
	<html>
	<head>
	</head>
	<body>
	New WNS Nomination received!<br/><br/>
	Name: ".$_POST["name"]."<br/>
	Phone: ".$_POST["phone"]."<br/>
	Email: ".$_POST["email"]."<br/>
	Employee ID: ".$_POST["empid"]."<br/>
	Country: ".$_POST["country"]."<br/>
	City: ".$_POST["city"]."<br/>
	Wants to use Social Login (Y/N): ".( (strcmp($_POST["soc"], "y") == 0) ? "Y" : "N")."<br/>
	Note: If student has NOT opted to use Social Login, please send EBS links for payment, not KForm. Such a link will not be part of the automated JAWS flow, and the student will have to be added manually to the UT.
	<br/><br/>
	Original Total Price (w/o taxes): INR ".$_POST["sum_total"]." (Please re-calculate for verification)<br/>
	Discounted (25%) Total Price (incl. taxes): INR ".$_POST["nett_total"]." (Please re-calculate for verification)<br/>
	<br/>
	List of Courses -<br/>";

	$courses = $_POST["courses"];
	foreach($courses as $course) $message .= $course[0]."&nbsp;(".$course[1].")<br/>";	

	$message .= "
	</body>
	</html>";
	
	// More headers
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	$headers .= 'From: <no-reply@jigsawacademy.com>' . "\r\n";
	$headers .= 'Cc: info@jigsawacademy.com' . "\r\n";
	$headers .= 'Bcc: soumik@jigsawacademy.com' . "\r\n";	
	mail($to, $subject, $message, $headers);

	//Send acknowledgement email - using library function
	$content["fname"] = substr($_POST["name"], 0, ((strpos($_POST["name"], " ") !== false) ? strpos($_POST["name"], " ") : strlen($_POST["name"]))); 
	$template = "wns.paylink.request";    
    send_email($template, array("to" => $_POST["email"]), $content);

    // Log Activity
    activity_create("high", "paylink.request", "wns", "", "", "", "", json_encode($_POST), "pending");

	//All done
	echo json_encode(array("status" => true));
	exit();

?>