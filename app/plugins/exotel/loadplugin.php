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

	JIGSAW ACADEMY WORKFLOW SYSTEM v2
	---------------------------------
*/

	// Prevent exclusive access
	if (!defined("JAWS")) {
			header('Location: https://www.jigsawacademy.com');
		die();
	}

	// Exotel SMS Gateway
	// ---------------------

	function sms_send($to, $text) {

		// Prep
		$smspost = http_build_query([
			'From' => JAWS_SMS_EXOTEL_SENDER,
			'To' => $to,
			'Body' => $text
		]);

		$smsopts = [
			'http' => [
				'method'  => 'POST',
				'header'  => 'Content-type: application/x-www-form-urlencoded',
				'content' => $smspost
		]];

		$smscontext = stream_context_create($smsopts);

		// Send
		return file_get_contents(JAWS_SMS_EXOTEL_URL, false, $smscontext);

	}

	// Exotel Connect Call Gateway
	// ---------------------

	function connect_call($from, $to, $callerId)
	{
		// Prep
		$callpost = http_build_query([
			'From' => $from,
			'To' => $to,
			'CallerId' => $callerId, // exotel virtual number    ,
			'CallType' => "trans" //Can be "trans" for transactional and "promo" for promotional content  : Promo no longer supported
		]);

		$callopts = [
			'http' => [
				'method'  => 'POST',
				'header'  => 'Content-type: application/x-www-form-urlencoded',
				'content' => $callpost
		]];

		$callconnect = stream_context_create($callopts);

		// Connect
		return file_get_contents(JAWS_CALL_EXOTEL_URL, false, $callconnect);
	}

	// MCube Click to Call API

	function connect_call_mcube($from, $to){

		$api_endpoint = 'http://mcube.vmc.in/api/outboundcall';

		$params = array(
			'apikey' => 'ceecd944836ce86eb97440e0c091c00b',         // mcube api key - required
			'exenumber' => trim($from),         // from call number - required
			'custnumber' => trim($to),         // to call number - required
			'mct' => '',          // max call time
			'wct' => '',       // warning call time
			'refid' => '',       // reference id,
			'did' => '8067140177',
			'url' => JAWS_PATH_WEB . '/mcube-response'         // callback url
		);

		// Prep
		$callpost = http_build_query($params);

		$callopts = [
			'http' => [
				'method'  => 'POST',
				'header'  => 'Content-type: application/x-www-form-urlencoded',
				'content' => $callpost
		]];

		$callconnect = stream_context_create($callopts);

		// Connect
		$response = file_get_contents($api_endpoint, false, $callconnect);

		$from = db_sanitize($from);
		$to = db_sanitize($to);
		$from_user_id = "NULL";
		if (!empty($_SESSION["user"]["user_id"])) {
			$from_user_id = db_sanitize($_SESSION["user"]["user_id"]);
		}

		// save response data
		$save = json_decode($response,true);
		db_exec("INSERT INTO `mcube_call_logs` (`call_id`, `from_num`, `from_user_id`, `to_num`, `before_data`, `after_data`) VALUES (" . db_sanitize($save['callid']) . ", $from, $from_user_id, $to, " . db_sanitize(json_encode($params)) . ", " . db_sanitize(json_encode($response)) . ");");

		return $response;

	}

	function mcube_response($call_id, $data){

		db_query("UPDATE mcube_call_logs SET `callback_response` = " . db_sanitize($data) . " WHERE `call_id` = " . db_sanitize($call_id) . ";");

		return true;
	}