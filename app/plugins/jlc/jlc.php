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

	class JLC {

		private $lmsUrl;
		private $lmsDevKey;
		public $curlError;

		function __construct($free = false) {

			load_library("setting");

			$this->lmsUrl = "lms_url";
			$this->lmsDevKey = "lms_dev_key";
                        //JA-54 changes - select sandbox JLC in case dev or uat environment
			if (!$GLOBALS["jaws_exec_live"] || APP_ENV != "prod" ) { 

				$this->lmsUrl .= "_debug";
				$this->lmsDevKey .= "_debug";

			}
			else if ($free) {

				$this->lmsUrl .= "_free";
				$this->lmsDevKey .= "_free";

			}
			$this->lmsUrl = setting_get($this->lmsUrl);
			$this->lmsDevKey = setting_get($this->lmsDevKey);

		}

		function statusFor($sisIds) {

			$response = $this->apiNew("users_status",
				[
					"data" => json_encode(
							["sis_user_ids" => implode(",", $sisIds)]
						),
					"content_type" => "application/json"
				]
			);
			if (!empty($this->curlError)) {
				$curlError = db_sanitize(json_encode($this->curlError));
				db_exec("INSERT INTO system_log (source, data) VALUES ('subs.revoke', $curlError);");
			}

			return json_decode($response, true);

		}

		function enableAccount($sisId) {

			$response = $this->apiNew("activate_user",
				[
					"data" => json_encode(
							["sis_user_id" => $sisId]
						),
					"content_type" => "application/json"
				]
			);
			if (!empty($this->curlError)) {
				$curlError = db_sanitize(json_encode($this->curlError));
				db_exec("INSERT INTO system_log (source, data) VALUES ('subs.grant', $curlError);");
			}
			else {
				$response = db_sanitize($response);
				db_exec("INSERT INTO system_log (source, data) VALUES ('subs.grant', $response);");
			}

			return $response;

		}

		function disableAccounts($sisIds) {

			$response = $this->apiNew("deactivate_users",
				[
					"data" => json_encode(
							["sis_user_ids" => implode(",", $sisIds)]
						),
					"content_type" => "application/json"
				]
			);
			if (!empty($this->curlError)) {
				$curlError = db_sanitize(json_encode($this->curlError));
				db_exec("INSERT INTO system_log (source, data) VALUES ('subs.revoke', $curlError);");
				return false;
			}
			else {
				$response = db_sanitize($response);
				db_exec("INSERT INTO system_log (source, data) VALUES ('subs.revoke', $response);");
				return true;
			}

		}

		function sisImport($sisFile) {
			return $this->api("sis_imports.json?import_type=instructure_csv", $sisFile);
		}

		function sisImportStatus($sisBatchID) {
			return $this->api("sis_imports/".$sisBatchID);
		}

		private function api($api, $attachment = null) {

			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $this->lmsUrl."api/v1/accounts/1/".$api);
			curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
			$headers = array("Authorization: Bearer ".$this->lmsDevKey);
			if ($attachment !== null) {

				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $attachment);
				$headers[] = "Content-Type: application/zip";

			}
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			// Go! Go!! GO!!!
			$response = curl_exec($curl);
			if (!$response) {
				$this->curlError = curl_error($curl);
			}

			return $response;

		}

		function apiNew($api, $attachment = null) {

			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $this->lmsUrl."api/v1/".$api);
			curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
			$headers = array("Authorization: Bearer ".$this->lmsDevKey);
			if ($attachment !== null) {

				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $attachment["data"]);
				if (!empty($attachment["content_type"])) {
					$headers[] = "Content-Type: ".$attachment["content_type"];
				}

			}
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			// Go! Go!! GO!!!
			$response = curl_exec($curl);
			if (!$response) {
				$this->curlError = curl_error($curl);
			}

			return $response;

		}

	}

?>