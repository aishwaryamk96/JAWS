<?php

	class Nucleus {

		const StatusMapping = [
			"active" => "active",
			"disabled" => "disabled",
			"draft" => "draft",
			"enabled" => "active",
			"expired" => "deleted",
			"hidden" => "active",
			"offline" => "active",
			"upcoming" => "draft"
		];

		const VisibilityMapping = [
			"active" => "visible",
			"disabled" => "visible",
			"draft" => "visible",
			"enabled" => "visible",
			"expired" => "visible",
			"hidden" => "hidden",
			"offline" => "hidden",
			"upcoming" => "visible"
		];

		protected $devKey = "ed01550b271a8a7bad1968f0739ed2cec817d939d616c7356307e1cb5780aa50b3ac";
		protected $baseUrl = "https://nucleusdev.jigsawacademy.com/api/v1/";

		private function __construct() {

		}

		static function createContext() {
			return new Nucleus;
		}

		function exportCourse($courseId) {

			$course = db_query("SELECT * FROM course WHERE course_id = ".$courseId);
			$courseMeta = db_query("SELECT * FROM course_meta WHERE course_id = ".$course_id);
			if (!empty($course)) {
				$course = $course[0];
			}
			else {
				return false;
			}
			$courseMeta = $courseMeta[0] ?? [];

			$code = false;
			if (!empty($course["sp_code"])) {
				$code = substr($course["sp_code"], 1);
			}
			elseif (!empty($course["il_code"])) {
				$code = substr($course["il_code"], 1);
			}

			if (empty($code)) {
				return false;
			}

			$workflowState = $this->processWorkflowState($course["status"]);
			$visibility = $this->processVisibility($course["status"]);

			$meta = json_decode($courseMeta["content"] ?? "[]", true);

			$product = [
				"name" => $course["name"],
				"code" => $code,
				"workflowState" => $workflowState,
				"visibility" => $visibility,
				"description" => $meta["long_description"] ?? $courseMeta["desc"],
				"contextType" => "Course",
				"context" => [
					"name" => $course["name"],
					"code" => $course["sis_id"],
					"description" => $courseMeta["desc"],
					"platformId" => 3,
					"duration" => $course["duration_length"],
					"duration_unit" => $course["duration_unit"],
					"workflowState" => $workflowState
				],
				"variants" => []
			];

			if (!empty($meta["url_web"])) {
				$product["slug"] = trim("/", str_replace("https://www.jigsawacademy.com", "", $meta["url_web"]));
			}

			$product = $this->prepareCoursePricing($course, $product);
			return $this->export($product);

		}

		protected function prepareCoursePricing($course, $product) {

			$attributes = ["sp_price_inr", "il_price_inr", "sp_price_usd", "il_price_usd"];
			$product["variants"][] = ["price" => $this->processPricing($course, $attributes)];

			return $product;

		}

		function exportProgram($programId) {

		}

		protected function prepareProgramPricing($program, $product) {

			$attributes = ["price_inr", "price_usd"];
			$product["variants"][] = ["price" => $this->processPricing($program, $attributes)];

			return $product;

		}

		protected function export($product) {
			return $this->api("imports/products", "PUT", ["product" => $product]);
		}

		function api($url, $method = "POST", $data = [], $headers = []) {

			$url = $this->baseUrl.trim($url, "/");
			$curl = curl_init($url);

			$options = [
				CURLOPT_HEADER => true,
				CURLOPT_RETURNTRANSFER => true
			];

			if (in_array($method, ["POST", "PUT", "PATCH"])) {

				if (empty($headers["Content-type"])) {

					$headers["Content-type"] = "application/json";
					$options[CURLOPT_POSTFIELDS] = json_encode($data);

				}
				else {
					$options[CURLOPT_BINARYTRANSFER] = true;
				}

				if ($method != "POST") {
					$options[CURLOPT_CUSTOMREQUEST] = $method;
				}
				else {

					$options[CURLOPT_POST] = true;
					$options[CURLOPT_POSTFIELDS] = $data;

				}

			}

			$formattedHeaders = [];
			$headers["Authorization"] = $this->devKey;
			foreach ($headers as $key => $value) {
				$formattedHeaders[] = $key.": ".$value;
			}

			$options[CURLOPT_HTTPHEADER] = $formattedHeaders;
			curl_setopt_array($curl, $options);

			$response = curl_exec($curl);
			if ($error = curl_error($curl)) {
				$response = ["error" => $error];
			}
			else {
				$response = ["response" => $response];
			}

			$apiData = $response;
			$apiData["url"] = $url;
			$apiData = db_sanitize(json_encode($apiData));
			db_exec("INSERT INTO system_log (source, data) VALUES ('nucleus.api', $apiData);");

			return $response;

		}

		protected function processWorkflowState($status) {
			return Nucleus::StatusMapping[$status] ?? "disabled";
		}

		protected function processVisibility($status) {
			return Nucleus::VisibilityMapping[$status] ?? "hidden";
		}

		protected function processPricing($context, $attributes) {

			$prices = [];
			foreach ($attributes as $attr) {

				if (!empty($context[$attr])) {

					$can = true;
					$price = [
						"currency" => substr($attr, -3),
						"price" => doubleval($context[$attr]),
						"workflowState" => "active"
					];

					if (strpos($attr, "inr")) {
						$price["country"] = "IN";
					}

					$prices[] = $price;

				}

			}

			return $prices;

		}

	}

?>