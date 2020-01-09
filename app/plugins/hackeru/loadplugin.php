<?php

	class Hackeru {

		function __construct() {
			load_library("email");
		}

		function export_subs($subs) {

			$subs_id = db_sanitize($subs["subs_id"]);
			db_exec("INSERT INTO subs_export (subs_id, platform_id) VALUES ($subs_id, 3);");

			if (!($json = $this->export_batch($subs))) {
				return false;
			}

			if ($this->export_enrollment($subs, $json)) {
				// $this->welcome_email($subs);
			}
			else {
				$this->notify_failure($subs);
			}

		}

		function export_batch($subs) {

			$return = ["batch" => $this->prepare_json_batch($subs)];

			if ($subs["batch_exported"] == "failed") {
				return false;
			}
			elseif ($subs["batch_exported"] != "pending") {
				return $return;
			}

			$sanitized_id = db_sanitize($subs["subs_id"]);
			$sanitized_json = db_sanitize(json_encode($return));
			db_exec("UPDATE subs_export SET request = $sanitized_json WHERE subs_id = $sanitized_id;");

			$exported = "exported";
			$response = $this->export_call($return["batch"]);
			if (is_string($response)) {

				$response = json_decode($response, true);
				if (!empty($response["error"])) {

					if (empty($response["failed"]) || strtolower($response["failed"]) != "groupkey is taken") {

						$return = false;
						$exported = "failed";

					}

				}

			}
			else {

				$response = ["error" => "RequestError"];
				$exported = "failed";
				$return = false;

			}

			$sanitized_response = db_sanitize(json_encode($response));
			$sanitized_exported = db_sanitize($exported);
			$sanitized_id = db_sanitize($subs["batch_id"]);

			db_exec("UPDATE bootcamp_batches SET exported = $sanitized_exported, export_response = $sanitized_response WHERE id = $sanitized_id;");

			return $return;

		}

		function export_enrollment($subs, $json) {

			$sanitized_id = db_sanitize($subs["subs_id"]);
			$sanitized_user_id = db_sanitize($subs["user_id"]);
			$json["enr"] = $this->prepare_json_enrollment($subs);

			$sanitized_json = db_sanitize(json_encode($json));
			db_exec("UPDATE subs_export SET request = $sanitized_json WHERE subs_id = $sanitized_id;");

			$status = "failed";
			$response = $this->export_call($json["enr"]);
			if (is_string($response)) {

				$response = json_decode($response, true);
				if (!empty($response["success"])) {

					$status = "success";

					$lms_pass = db_sanitize($response["response"]["studentPassword"]);
					db_exec("UPDATE user_enrollment SET lms_pass = $lms_pass WHERE user_id = $sanitized_user_id;");
					$this->welcome_email($subs, $json["enr"], $lms_pass);

				}

			}
			else {
				$response = ["error" => "RequestError"];
			}

			$sanitized_status = db_sanitize($status);
			$sanitized_response = db_sanitize(json_encode($response));

			db_exec("UPDATE subs_export SET status = $sanitized_status, response = $sanitized_response WHERE subs_id = $sanitized_id;");

			return $status == "success";

		}

		function prepare_json_batch($subs) {

			$batch_meta = json_decode($subs["batch_meta"], true);

			$request = [
				"requestType" => "groupRegistration",
				"groupName" => $batch_meta["name"],
				"groupKey" => $subs["batch_code"]
			];

			return $request;

		}

		function prepare_json_enrollment($subs) {

			$request = [
				"requestType" => "studentRegistration",
				"groupKey" => $subs["batch_code"],
			];

			return $this->get_variables($subs, $request);

		}

		function export_call($request) {

			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, "https://api.hackampus.com/partners");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers());
			if (!empty($request)) {

				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $this->finalize_request($request));

			}

			$response = curl_exec($ch);
			$sanitized_response = db_sanitize(json_encode(["response" => $response, "curl" => curl_getinfo($ch)]));
			db_exec("INSERT INTO system_log (source, data) VALUES ('hackeru', $sanitized_response);");
			curl_close($ch);

			return $response;

		}

		function headers() {
			return [
				"Content-type: application/json"
			];
		}

		function finalize_request($request) {

			$request["apiKey"] = "YTdsOFdOdGpoYWJTdlhCOHF1YmxUdz09";
			return json_encode($request);

		}

		function welcome_email($subs, $data, $lms_pass) {

			$content = [
				"bundle_name" => $subs["bundle_name"],
				"name" => $data["studentFirstName"],
				"email" => $data["studentEmail"],
				"password" => $lms_pass,
				"title" => "Welcome! Your account is ready!"
			];

			$status = send_email("welcome.email.hackeru", ["to" => $data["studentEmail"]], $content);
			db_exec("INSERT INTO system_log (source, data) VALUES ('hackeru.welcome.email', ".db_sanitize(var_export($status, true)).");");

			$res_enr_meta = db_query("SELECT * FROM user_enr_meta WHERE subs_id=".$subs["subs_id"]);
			if (!isset($res_enr_meta[0])) {
				db_exec("INSERT INTO user_enr_meta (subs_id, email_sent_at) VALUES (".$subs["subs_id"].", CURRENT_TIMESTAMP);");
			}
			else {
				db_exec("UPDATE user_enr_meta SET email_sent_at= CURRENT_TIMESTAMP WHERE subs_id=".$subs["subs_id"]);
			}

		}

		function notify_failure($subs) {

		}

		function get_variables($subs, $request) {

			$user = db_query("SELECT name, email, phone FROM user WHERE user_id = ".$subs["user_id"]);

			$names = explode(" ", trim($user[0]["name"]));
			$first_name = $names[0];
			$last_name = $names[count($names) - 1];
			$middle_name = "";
			if (count($names) > 2) {
				$middle_name = $names[1];
			}
			if (strlen($last_name) < 2) {
				$last_name = $first_name." ".$last_name;
			}

			$request["studentFirstName"] = $first_name;
			$request["studentLastName"] = $last_name;
			$request["studentEmail"] = $user[0]["email"];

			return $request;

		}

	}

?>
