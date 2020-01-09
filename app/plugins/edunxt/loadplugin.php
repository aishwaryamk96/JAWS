<?php

	class Edunxt {

		protected $attributes = [];

		function __construct() {
			load_library("email");
		}

		function export_subs($subs) {

			$subs_id = db_sanitize($subs["subs_id"]);
			db_exec("INSERT INTO subs_export (subs_id, platform_id) VALUES ($subs_id, 2);");

			$xml = $this->prepare_xml($subs);
			$sanitized_xml = db_sanitize($xml);
			db_exec("UPDATE subs_export SET request = $sanitized_xml WHERE subs_id = $subs_id;");
			if ($this->export_call($xml, $subs_id)) {
				$this->welcome_email($subs);
			}
			else {
				$this->notify_failure($subs);
			}

		}

		function prepare_xml($subs) {

			$today = new DateTime;
			list($email, $first_name, $last_name, $middle_name, $phone, $sis_id, $password, $start_date, $bundle_code, $courses) = $this->get_variables($subs);

			ob_start();
			require_once __DIR__."/template.xml.php";
			$xml = ob_get_clean();

			return $xml;

		}

		function export_call($xml, $subs_id) {

			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, "https://jigsaw.manipalprolearn.com/?q=MULNPerson/createExternalUser");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers());
			if (!empty($xml)) {

				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(["Data" => $xml]));

			}

			$response = curl_exec($ch);
			$status = "failed";
			if (($http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE)) < 400) {
				$status = $this->get_status($response);
			}
			curl_close($ch);

			$sanitized_response = db_sanitize($response);
			db_exec("UPDATE subs_export SET status = '$status', http_code = $http_code, response = $sanitized_response WHERE subs_id = $subs_id;");

			return $status == "success";

		}

		function headers() {
			return [
				/*"Content-type: text/xml",*/
				"clientid: sapadmin",
				"apikey: ade3b3682e199f55dad8e93ebbf22a45"
			];
		}

		function get_status($response) {

			$xmlParsed = new SimpleXMLElement($response);
			return $xmlParsed->response["status"] == "FRESH_SUCCESS" ? "success" : "failed";

		}

		function welcome_email($subs) {

			$end_date = date_create_from_format("Y-m-d H:i:s", $subs["end_date"]);
			$content = [
				"bundle_name" => $subs["bundle_name"],
				"name" => $this->attributes["first_name"]." ".$this->attributes["last_name"],
				"email" => $this->attributes["email"],
				"password" => $this->attributes["lms_pass"],
				"title" => "Welcome! Your account is ready!",
				"end_date" => $end_date->format("d M Y")
			];

			send_email("welcome.email.edunxt", ["to" => $this->attributes["email"]], $content);

		}

		function notify_failure($subs) {

		}

		function get_variables($subs) {

			if (!empty($this->attributes)) {
				return;
			}

			$user = db_query("SELECT name, email, phone FROM user WHERE user_id = ".$subs["user_id"]);
			$bundle = db_query("SELECT code FROM course_bundle WHERE bundle_id = ".$subs["bundle_id"]);

			$courses = [];
			$enrs = db_query("SELECT e.sis_id, e.lms_pass, c.sis_id AS course_code FROM user_enrollment AS e INNER JOIN course AS c ON c.course_id = e.course_id WHERE e.status = 'active' AND e.subs_id = ".$subs["subs_id"].";");
			foreach ($enrs as $enr) {

				$mode = "OM";
				$course_info = explode("_", $enr["course_code"]);
				if (count($course_info) > 1) {
					$mode = $course_info[1];
				}

				$courses[] = ["code" => $enr["course_code"], "mode" => $mode];

			}

			$start_date = date_create_from_format("Y-m-d H:i:s", $subs["start_date"]);

			$names = explode(" ", trim($user[0]["name"]));
			$first_name = $names[0];
			$last_name = $names[count($names) - 1];
			$middle_name = "";
			if (count($names) > 2) {
				$middle_name = $names[1];
			}

			$this->attributes["email"] = $user[0]["email"];
			$this->attributes["first_name"] = $first_name;
			$this->attributes["last_name"] = $last_name;
			$this->attributes["middle_name"] = $middle_name;
			$this->attributes["phone"] = $user[0]["phone"];
			$this->attributes["sis_id"] = $enrs[0]["sis_id"];
			$this->attributes["lms_pass"] = $enrs[0]["lms_pass"];
			$this->attributes["start_date"] = $start_date;
			$this->attributes["bundle_code"] = $bundle[0]["code"];

			return [$user[0]["email"], $first_name, $last_name, $middle_name, $user[0]["phone"], $enrs[0]["sis_id"], $enrs[0]["lms_pass"], $start_date, $bundle[0]["code"], $courses];

		}

	}

?>
