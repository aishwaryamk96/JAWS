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

	EVENT CORE v0.22
	-------------------
*/

	// Prevent exclusive access
	if (!defined("JAWS")) {
		header('Location: https://www.jigsawacademy.com/index.php');
		die();
	}

	// Load Pre-requisites
	load_library('url');
	load_library('misc');
	load_plugin("mongodb");

	// This will parse the event
	function event_parse($allow_origin = '*') {

		// Headers
		header('Access-Control-Allow-Origin: '.$allow_origin);
		header('Access-Control-Allow-Methods: GET, POST');
		header('Access-Control-Allow-Credentials: true');
		header("Content-Type: application/json");
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Pragma-directive: no-cache");
		header("Cache-directive: no-cache");
		header("Cache-Control: private, must-revalidate, max-age=0, no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
		header("Pragma: no-cache");

		// Security Prep
		unset($_REQUEST['auth']);
		unset($_POST['auth']);
		unset($_GET['auth']);

		// Handle Event Hook
		try { handle_strict('event_handle_'.$_REQUEST['event'], $_REQUEST['data']); } catch (Exception $e) {}

		// Handle Tags Hooks
		foreach($_REQUEST['data']['tags'] as $tag) {
			try { handle_strict('event_tag_handle_'.$tag, $_REQUEST['data']); } catch (Exception $e) {}
		}

		// Prep URLS
		$_REQUEST['data']['url'] = url_template_from_string($_REQUEST['data']['url']);
		$_REQUEST['data']['ref'] = url_template_from_string($_REQUEST['data']['ref']);

		// Storage Prep
		$event = $_REQUEST['data'];
		$event['event'] = $_REQUEST['event'];
		$event['ver']['svr'] = 0.22;
		$event['ver']['cln'] = $_REQUEST['ver']['cln'];
		$event['time'] = new MongoDB\BSON\UTCDateTime(time()*1000);

		// Store
		(new MongoDB\Client())->jaws->events->insertOne($event);

	}

?>
