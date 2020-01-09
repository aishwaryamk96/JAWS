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
		header('Location: ../index.php');
		die();
	}

	class Android {

		// Google Notification send URL
		private $_url;

		// Google notifications developer key
		private $_key;

		// Construct message array
		private $_msg;

		// List of devide IDs; will be overwritten based on live status
		private $_deviceIds;

		function __construct($message, $deviceIds, $src) {

			load_library("setting");

			$this->_url = "https://android.googleapis.com/gcm/send";
			$this->_key = JAWS_ANDROID_API_KEY;

			$this->_msg = $this->constructMessage($message, $src);

			$this->validateDeviceIDs($deviceIds);
		}

		function notify() {

			$payload = array(
					"registration_ids" => $this->_deviceIds,
					"data" => $this->_msg
				);

			$headers = array(
					"Authorization: key=".$this->_key,
					"Content-Type: application/json"
				);

			// Open connection
			$ch = curl_init();

			// Set the url, number of POST vars, POST data
			curl_setopt($ch, CURLOPT_URL, $this->_url);

			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			// disable SSL certificate support
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

			// execute post
			$result = curl_exec($ch);

			// Close connection
			curl_close($ch);

			if ($result === FALSE)
				return array("error" => 1, "error_desc" => curl_error($ch));
			else
				return array("success" => 1, "result" => $result);

		}

		function getMessage() {
			return $this->_msg;
		}

		private function validateDeviceIDs($deviceIds) {

			if (!$GLOBALS["jaws_exec_live"]) {

				$testDeviceIds = setting_get("android.test_device_ids");
				if ($testDeviceIds !== false && strlen($testDeviceIds) > 0) {
					$this->_deviceIds = explode(";", $testDeviceIds);
				}
				else {
					$this->_deviceIds = $deviceIds;
				}

			}
			else {
				$this->deviceIds = $deviceIds;
			}

		}

		private function constructMessage($message, $src) {

			if ($src == "chat") {

				$msgTitle; $isBatch = "N";
				$sender = user_get_by_id($message["sender_id"]);
				$otherId = $message["receiver_id"];

				if ($message["receiver_type"] == "course_section") {

					$receiver = section_get_by_id($message["receiver_id"]);
					$msgTitle = db_query("SELECT name FROM course WHERE course_id=".$receiver["course_id"])[0]["name"];
					$is_batch = "Y";
					$other_id = $receiver["sis_id"];
				}
				else
					$msgTitle = user_get_by_id($message["receiver_id"])["name"];

				$msgUrlTitle = "";
				$msgUrlDomain = "";
				$msgUrlLogo = "";

				$urls = $this->getUrls($message["message"]);

				if (isset($urls[0]))
				{
					$urlContents = file_get_contents($urls[0]);

					preg_match("/<title>(.*)<\/title>/i", $urlContents, $matches);
					$msgUrlTitle = $matches[1];
					$result = parse_url($urls[0]);
					$msgUrlDomain = $result['host'];
					$msgUrlLogo = $this->getFavIcon($urls[0]);
				}

				return array(
					"message" => $message["data"],
						"title" => $msgTitle,
						"is_student" => "Y",
						"type" => "10",
						"message_id" => $message["msg_id"],
						"self_id" => $message["sender_id"],
						"self_name" => $sender["name"],
						"other_id" => $other_id,
						"is_batch" => $is_batch,
						"chat_room_id" => $message["receiver_id"],
						"msg_reply_id" => $message["replied_to"],
						"msg_url_title" => $msgUrlTitle,
						"msg_url_domain" => $msgUrlDomain,
						"msg_url_logo" => $msgUrlLogo,
						"msg_createdat" => date("Y-m-d H:i:s")
					);
			}
			else
				return $message;

		}

		private function getUrls($string)
		{
			$regex = '/https?\:\/\/[^\" ]+/i';
			preg_match_all($regex, $string, $matches);

			return ($matches[0]);
		}

		private function getFavIcon($url)
		{
			$favicon = '';
			$html = file_get_contents($url);
			$dom = new DOMDocument();
			$dom->loadHTML($html);
			$links = $dom->getElementsByTagName('link');
			for ($i = 0; $i < $links->length; $i++)
			{
				$link = $links->item($i);
				if ($link->getAttribute('rel') == 'icon')
					$favicon = $link->getAttribute('href');
			}

			return $favicon;
		}
	}

?>