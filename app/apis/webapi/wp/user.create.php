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

	// Check Auth or login
	if (!auth_api("user.create")) die("You do not have the required priviledges to use this feature.");

	// Init
	load_module("user");

	// Start
	$user = user_get_by_email($_POST["email"]);
	$flag = "false";

	// Create new user
	if (!$user) {
		$user = user_create($_POST["email"], substr(str_shuffle($_POST["name"].str_replace("@", "0", str_replace(".", "", $_POST["email"]))), 0, 10), $_POST["name"], $_POST["phone"], false);

		// Check for creation fail
		if ($user === false) die(json_encode(false));

		user_update($user["user_id"], array(
			"soc_".$_POST["soc"] => $_POST["email"], 
			"photo_url" => $_POST["photo_url"]
			));

		$user["soc_".$_POST["soc"]] = $_POST["email"];
		$user["photo_url"] = $_POST["photo_url"];
		$flag = "true";
	}
	else {

		// Check Auth again
		if (!auth_feature_is_allowed("user.login.force", $GLOBALS["temp"]["api"]["feature_keys"])) die("You do not have the required priviledges to use this feature.");

		if ($user["soc_".$_POST["soc"]] == "") {
			user_update($user["user_id"], array("soc_".$_POST["soc"] => $_POST["email"]));
			$user["soc_".$_POST["soc"]] = $_POST["email"];
		}
	}

	// Return the new/existing user info
	$ret = array(
		"user_id" => $user["user_id"],
		"web_id" => $user["web_id"],
		"name" => $user["name"],
		"phone" => (isset($user["phone"]) ? $user["phone"] : ""),
		"email" => $user["email"],
		"photo_url" => (isset($user["photo_url"]) ? $user["photo_url"] : ""),
		"soc_fb" => (isset($user["soc_fb"]) ? $user["soc_fb"] : ""),
		"soc_gp" => (isset($user["soc_gp"]) ? $user["soc_gp"] : ""),
		"soc_li" => (isset($user["soc_li"]) ? $user["soc_li"] : ""),
		"created" => $flag
		);
	
	echo json_encode($ret);

	// Custom Temporary Code - Send email to leads team on registration
	/*if ((strcmp($flag, "true") == 0) && (isset($user["phone"])) && (strlen($user["phone"]) > 0))  {	*/	
	/*if  (strcmp($flag, "true") == 0) {
		$to = "leads@jigsawacademy.com, info@jigsawacademy.com";
		$subject = "New user registered on Website - ".$user["name"];
	
		$message = "
		<html>
		<head>
		</head>
		<body>
		A new user has signed up on www.jigsawacademy.com!<br/><br/>
		Name: ".$user["name"]."<br/>
		Phone: ".$user["phone"]." (Note: This field may be blank! A separate email will come when user provides his phone number.)<br/>
		Social ID: ".$user["email"]."<br/>
		Social Provider: ".$_POST["soc"]."<br/>
		</body>
		</html>";
		
		// More headers
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		$headers .= 'From: <no-reply@jigsawacademy.com>' . "\r\n";		
		$headers .= 'Bcc: soumik@jigsawacademy.com' . "\r\n";
		
		mail($to, $subject, $message, $headers);
	}*/

	// Done
	exit();

?>  