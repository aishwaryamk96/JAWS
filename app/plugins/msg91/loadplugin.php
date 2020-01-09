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

	// MSG91 OTP Gateway
	// ---------------------

	function otp_send($to, $country_code = '91') {

		// Prep Data
		$data = json_encode([
			"countryCode" => $country_code,
			"mobileNumber" => $to,
			"getGeneratedOTP" => true
		]);

		// Prep CURL
		$curl = curl_init(JAWS_OTP_MSG91_URL.'/generateOTP');
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_AUTOREFERER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($data),
			'application-Key: '.JAWS_OTP_MSG91_KEY
		));

		// Send Request
		$response = json_decode(curl_exec($curl), true);
		curl_close($curl);

		// Parse Response
        if($response["status"] == "error") {
            activity_create('critical','msg91.otp.gen','fail','','','','','Request : '.json_encode($data).' --- Response : '.$response["response"]["code"],'logged');
            return false;
        }
		else return $response["response"]["oneTimePassword"];

	}

	function otp_verify($to, $otp, $country_code = '91') {

		// Prep Data
		$data = json_encode([
			"countryCode" => $country_code,
			"mobileNumber" => $to,
			"oneTimePassword" => $otp
		]);

		// Prep CURL
		$curl = curl_init(JAWS_OTP_MSG91_URL.'/verifyOTP');
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_AUTOREFERER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($data),
			'application-Key: '.JAWS_OTP_MSG91_KEY
		));

		// Send Request
		$response = json_decode(curl_exec($curl), true);
		curl_close($curl);

		// Parse Response
		if ($response["status"] == "error") return false;
		else return true;

	}
