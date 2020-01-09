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
	$subject = "GENPACT Enrollment Attempt - ".$_POST["name"];

	$message = "
	<html>
	<head>
	</head>
	<body>
	A person is attempting to enroll as a GENPACT Employee.<br/><br/>
	Name: ".$_POST["name"]."<br/>
	Phone: ".$_POST["phone"]."<br/>
	Email (GENPACT): ".$_POST["email"]."<br/>
	Email (Alternate): ".$_POST["email_alt"]."<br/>
	OHR ID: ".$_POST["empid"]."<br/>
	Genpact Office Location: ".$_POST["office"]."<br/>
	Country: ".$_POST["country"]."<br/>
	City: ".$_POST["city"]."<br/>
	Payment Mode: ".$_POST["paymode"]."<br/>	
	Note: Please check EBS backend for payment confirmation and verify provided personal details. If confirmed, please create corporate account for the student in LMS and UT. Else follow up with student on the above phone number for assistance (for failed payment or no payment).
	<br/><br/>
	For your reference,<br/>
	INR Price:  30,000 INR + 15% Service Tax = 34,500 INR<br/>
	USD Price: 750 USD<br/>
	<br/>
	--<br/>
	JAWS
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
	/*$content["fname"] = substr($_POST["name"], 0, ((strpos($_POST["name"], " ") !== false) ? strpos($_POST["name"], " ") : strlen($_POST["name"]))); 
	$template = "wns.paylink.request";    
    	send_email($template, array("to" => $_POST["email"]), $content);*/

    	// Log Activity
    	activity_create("high", "paylink.external", "genpact", "", "", "", "", json_encode($_POST), "logged");

	//All done
	echo json_encode(array("status" => true));
	exit();

?>