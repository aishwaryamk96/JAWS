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

	//load_module("msg");
	load_module("user_enrollment");

	class MobileApp {

		function __construct() {

			require_once "Android.php";
			if (file_exists("iOS.php"))
				require_once "iOS.php";

		}

		function authorizeRequest($authHeader) {

			$authHeader = strtolower(trim($authHeader));

			if (($bearerPos = strpos($authHeader, "bearer")) === false) {
				return false;
			}

			$authHeader = trim(substr($authHeader, strpos("bearer:", $authHeader) + strlen("bearer:")));

			$apiAuth = db_query("SELECT * FROM system_api WHERE dev_key=".db_sanitize($authHeader).";");
			if (empty($apiAuth)) {
				return false;
			}

			if ($apiAuth[0]["dev_name"] != "mobapp") {
				return false;
			}

			return true;

		}

		function getRequestLocation($ip) {
			return ["country_code" => "IN"];
		}

		function saveDeviceId($user_id, $devId, $devType) {

			load_module("user");

			$devType = strtolower($devType);
			$devIds = user_content_get($user_id, $devType);
			if ($devIds == false) {
				$devIds = [];
			}
			else {
				$devIds = json_decode($devIds, true);
			}

			if (in_array($devId, $devIds)) {
				return;
			}

			$devIds[] = $devId;
			user_content_set($user_id, $devType, json_encode($devIds));

		}

		function authorizeDeviceID($user_id, $devId) {
			return true;
		}

		function getCoursesTopics($courseCodes, $jigId = false, $frozen = false) {

			if (is_array($courseCodes)) {
				$courseCodes = implode(";", $courseCodes);
			}

			$data["course_codes"] = $courseCodes;
			if ($jigId !== false) {

				$data["jig_id"] = $jigId;
				if ($frozen == true) {
					$data["frozen"] = true;
				}

			}

			$opts = array('http' => array(
					'method'  => 'POST',
					'header'  => 'Content-type: application/x-www-form-urlencoded',
					'content' => http_build_query($data)
				)
			);
			$context  = stream_context_create($opts);
			// die(file_get_contents("https://jigsawacademy.net/app/getcoursestopics.php", false, $context));
			return json_decode(file_get_contents("https://jigsawacademy.net/app/getcoursestopics.php", false, $context), true);

		}

		function updateTopicProgress($jigId, $topicId, $tagId) {

			$data["jig_id"] = $jigId;
			$data["tag_id"] = $tagId;

			$opts = array('http' => array(
					'method'  => 'POST',
					'header'  => ['Content-type: application/x-www-form-urlencoded', 'Authorization: Bearer M14WmlGoyEYe4OUe5QHmJVhisyCU5qtGyt1j1DrFtQhsD6xHuXR6CbTLenfsoDza'],
					'content' => http_build_query($data)
					)
				);
			$context  = stream_context_create($opts);
			// var_dump($opts); die;
			$res = file_get_contents("https://jigsawacademy.net/api/v1/modules/$topicId/update_module_progression", false, $context);
			return $res;

		}

		function notify($message, $user_ids, $src) {

			// Process Android users
			$android = $this->processAndroidClients($message, $user_ids, $src);

			// Process iOS users
			$ios = $this->processiOSClients($message, $user_ids, $src);

			return ["android" => $android, "iOS" => $ios];

		}

		function msg_send($receiver_type, $receiver_id, $sender_id, $message, $replied_to) {

			$msg_id = msg_create("low", $replied_to, "", "", "", "", $receiver_type, $receiver_id, "user", $sender_id, $message, null, true, "visible");

			// Var declarations
			$user_ids;

			if ($receiver_type == "course_section") {

				$section = section_get_by_id($receiver_id);
				$user_ids = array_merge(explode(";", $section["admin"]), explode(";", $section["users"]));

			}
			else
				$user_ids = [$receiver_id, $sender_id];

			$notification_data = ["data" => $message, "msg_id" => $msg_id, "receiver_type" => $receiver_type, "receiver_id" => $receiver_id, "sender_id" => $sender_id, "replied_to" => $replied_to];

			return $this->notify($notification_data, $user_ids, "chat");

		}

		private function processAndroidClients($message, $user_ids, $src) {

			// Process Android users
			$android_devs = array();

			$devices_all = db_query("SELECT value FROM user_content WHERE `key`='gcm_id' AND user_id IN (".implode(",", $user_ids).");");

			foreach ($devices_all as $devices_per_user) {

				$devices = json_decode($devices_per_user["value"], true);
				foreach ($devices as $device)
					$android_devs[] = $device;

			}

			$android = new Android($message, $android_devs, $src);
			$android->notify();

			return $android;

		}

		private function processiOSClients($message, $user_ids, $src) {

			if (!class_exists("iOS"))
				return null;

			$iOS_devs = array();
			$devices_all = db_query("SELECT value FROM user_content WHERE key='ios_id' AND user_id IN (".implode(",", $user_ids).");");

			foreach ($devices_all as $devices_per_user) {

				$devices = json_decode($devices_per_user, true);
				foreach ($devices as $device)
					$iOS_devs[] = $device;

			}

			$ios = new iOS($message, $android_devs, $src);
			$ios->notify();

			return $ios;

		}
	}

?>