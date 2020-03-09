<?php


	// Prevent exclusive access
	if (!defined("JAWS")) {
		header("HTTP/1.1 401 Unauthorized");
		die();
	}

	// Init Session
	auth_session_init();

	// Auth Check - Expecting Session Only !
	if ((!auth_session_is_logged()) || (!auth_session_is_allowed("dash"))) {
		header("HTTP/1.1 401 Unauthorized");
		die();
	}

	if (count($_POST) == 0) {
		$_POST = json_decode(file_get_contents('php://input'), true);
	}

	// Load stuff
	load_module("user");
        
        $editedData = $_POST;
        
        $currentInstlData = db_query("SELECT * FROM `payment_instl` WHERE subs_id=".db_sanitize($editedData['sub_id'])." AND pay_id=".db_sanitize($editedData['pay_id'])." and instl_count=". db_sanitize($editedData['instl']));
        
        print_r($currentInstlData);die;