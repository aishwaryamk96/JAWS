<?php

	// Prevent exclusive access
	if (!defined("JAWS")) {
		header("HTTP/1.1 401 Unauthorized");
		die();
	}

	// Init Session
	auth_session_init();

	// Auth Check - Expecting Session Only !
	if ((!auth_session_is_logged()) || (!auth_session_is_allowed("batcave"))) {
		header("HTTP/1.1 401 Unauthorized");
		die();
	}

	// Send Headers
	header('Access-Control-Allow-Credentials: true');
	header("Content-Type: application/json");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Pragma-directive: no-cache");
	header("Cache-directive: no-cache");
	header("Cache-Control: private, must-revalidate, max-age=0, no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
	header("Pragma: no-cache");

	if (empty($_SESSION["user"]["phone"])) {
		die(json_encode(["msg" => "Batcave does not know your phone number.\nPlease add your phone number in your profile before you can call a student.\nMention error code: 1"]));
	}

	
        if (empty($_GET["userMobile"]) || !is_numeric($_GET["userMobile"])) {
		die(json_encode(["msg" => "Something went wrong...\nPlease contact IT for this issue.\nMention error code: 2"]));
	}

        $connectingPhone ='';
//        if(!empty($_GET["user"])){
//            $user = db_query("SELECT phone FROM user WHERE user_id = ".db_sanitize($_GET["user"]));
//            $connectingPhone = $user[0]["phone"];
//        }elseif($_GET["userMobile"]){
            $connectingPhone = $_GET["userMobile"];
        //}
        
	if (empty($connectingPhone)) {
		die(json_encode(["msg" => "Something went wrong...\nPlease contact IT for this issue.\nMention error code: 3"]));
	}

	load_plugin("exotel");

	connect_call_mcube($_SESSION["user"]["phone"], $connectingPhone, "09243522277");

	die(json_encode(["msg" => "Connecting to ".$connectingPhone."..."]));

?>