<?php

	define("Domain", "https://api-in21.leadsquared.com/v2/");

	hook("payment_confirm__", "ls_payinfo_log");
	hook("leads_basic_compile__", "ls_lead_capture");

	// Lead.Capture API is one stop API for creation & updating of leads
	function ls_lead_capture($leads, $new = true, $return = false) {

		load_library("url");

		$leads = $leads[0];
		foreach ($leads as $lead) {

			$api_url = "LeadManagement.svc/Lead.Capture";

			$key_mapping = [
				"lead_id" => "ProspectID",
				"email" => "EmailAddress",
				"name" => "FirstName",
				"phone" => "Phone",
				"source_2" => "Source",
				"page_url" => "Website",
				"page_url_2" => "mx_Website_URL",
				"utm_campaign" => "mx_UTM_Campaign",
				"utm_medium" => "mx_UTM_Medium",
				"utm_source" => "mx_UTM_Source",
				"utm_term" => "mx_UTM_Term",
				"comments" => "mx_Comments",
				"company" => "mx_Company",
				"reassigned_at" => "mx_Lead_assigned_date",
				"alt_email_1" => "mx_Alt_Email_1",
				"alt_phone_1" => "Mobile",
				"alt_email_2" => "mx_Alt_Email_2",
				"alt_phone_2" => "mx_Alt_Phone_2",
				/*"category" => "mx_Source",*/
				/*"source_2_2" => "mx_Source2",*/
				/*"channel" => "mx_Sub_Source",*/
				"path_finder_thingy" => "mx_Website_URL",
				"page_url_3" => "mx_Website_Form",
				"mx_City" => "mx_City",
				"mx_Location" => "mx_Location",
				"course" => "Course",
				"mx_Preferred_date" => "mx_Preferred_date"
			];

			if (strlen($lead["phone"]) < 10) {
				unset($lead["phone"]);
			}
			else {
				$lead["phone"] = str_replace("+91-", "", $lead["phone"]);
			}

			$old_lead = was_lead_prime_data_sent_before($lead["email"]);

			if (!$old_lead) {

				if (empty($lead["utm_source"])) {

					$lead["utm_source"] = "Social login";
					// $lead["category"] = "Online";

				}
				else if (strpos($lead["utm_source"], "FREECOURSE") === 0) {

					$free_course_info = explode("-", $lead["utm_source"]);

					$lead["utm_source"] = "FREECOURSE";
					$lead["channel"] = "Free Trial";

				}

			}

			if ($lead["page_url"] == "coupon") {
				$lead["category"] = "Mobile App";
			}

			if (empty($lead["channel"])) {

				if ($lead["event"] == "formsubmit") {
					// $lead["channel"] = "Form Fills";
				}
				else /*if ($lead["event"] == "reg" || $lead["event"] == "login")*/ {
					// $lead["channel"] = "Social Login";
				}

			}

			$meta = [];
			if (!empty($lead["meta"])) {
				$meta = json_decode($lead["meta"], true) ?: [];
			}

			if (!empty($meta["MXCProspectId"])) {
				$lead["lead_id"] = $meta["MXCProspectId"];
			}

			if (!empty($meta["form_name"])) {

				$lead["utm_term"] = $meta["form_name"];
				if ($meta["form_name"] == "resource-download" && ($meta["embed_url"] ?? "") == "www.jigsawacademy.com/online-analytics-training/") {
					$lead["utm_term"] = "IPBA Brochure";
				}

			}
			if (!empty($meta["city"])) {

				$lead["mx_City"] = $meta["city"];
				$lead["mx_Location"] = $meta["city"];

			}
			if (!empty($meta["course"])) {
				$lead["course"] = $meta["course"];
			}
			if (!empty($meta["time_to_call"])) {
				$lead["mx_Preferred_date"] = $meta["time_to_call"];
			}

			$page_url = trim($lead["page_url"], "/");
			if ($page_url == "wp-admin/admin-ajax.php") {

				$page_url = trim($lead["referer"], "/");
				$lead["page_url"] = $page_url;

			}

			if (strpos($page_url, "http") !== 0) {

				if (strpos($page_url, "www.jigsawacademy.com") !== 0) {
					$page_url = "www.jigsawacademy.com/".trim($page_url, "/");
				}

				$page_url = "https://".$page_url;

			}
			// $url_components = parse_url($page_url);
			// if (empty($url_components["host"])) {
			// 	$page_url = "https://www.jigsawacademy.com/".$page_url;
			// }
			$url_components = parse_url($page_url);
			$built_url = trim($url_components["path"] ?? "", "/");

			if (strpos($built_url, "cloud") !== false) {
				// $lead["category"] = "Cloud-Computing";
			}
			elseif (strpos($built_url, "analytics-courses-trial") !== false) {
				$lead["channel"] = "Free Trial";
			}
			else if (stripos($built_url, "corporate") !== false || stripos($built_url, "about-us/careers") !== false) {

				if (!$old_lead) {
					$lead["source_2"] = "Corporate";
				}
				else {
					$lead["source_2_2"] = "Corporate";
				}

				$lead["company"] = $meta["company"];
				$lead["comments"] = $meta["outcomes"];

			}
			elseif (stripos($built_url, "master-certificate-in-cyber-security-red-team") !== false) {

				if (!$old_lead) {
					// $lead["source_2"] = "Organic";
				}
				else {
					// $lead["source_2_2"] = "Organic";
				}

				// $lead["category"] = "Cyber Security";
				// $lead["channel"] = "Form Fills";

			}
			else if (stripos($built_url, "integrated-program-in-business-analytics") !== false) {

				if (!$old_lead) {
					// $lead["source_2"] = "Organic";
				}
				else {
					// $lead["source_2_2"] = "Organic";
				}

				// $lead["category"] = "IPBA";
				// $lead["channel"] = "Form Fills";

			}
			else if (stripos($built_url, "bocconi-business-analytics-program-mumbai") !== false) {
				// $lead["category"] = "PGPDM";
			}
			else if ($built_url == "elite-professionals-program") {

				if (!$old_lead) {
					// $lead["source_2"] = "Paid";
				}
				else {
					// $lead["source_2_2"] = "Paid";
				}

				// $lead["category"] = "PGPDM";
				// $lead["channel"] = "Quora";

			}
			else if ($built_url == "elite-professionals-program-august-ppc3") {

				if (!$old_lead) {
					// $lead["source_2"] = "Paid";
				}
				else {
					// $lead["source_2_2"] = "Paid";
				}

				// $lead["category"] = "PGPDM";
				// $lead["channel"] = "Search";

			}
			else if (in_array($built_url, ["analytics-classroom-training", "iot-bootcamp", "machine-learning-ai-classroom-training", "big-data-bootcamp"])) {

				// $lead["category"] = "Bootcamp";

				if ($built_url == "analytics-classroom-training") {
					// $lead["channel"] = "BC_FSDS";
				}
				else if ($built_url == "iot-bootcamp") {
					// $lead["channel"] = "BC_IOT";
				}
				else if ($built_url == "machine-learning-ai-classroom-training") {
					// $lead["channel"] = "BC_MLAI";
				}
				else {
					// $lead["channel"] = "BC_BIGDATA";
				}

			}
			else if (strpos($built_url, "iot") !== false) {

				// $lead["category"] = "IOT";

				if ($built_url == "iot-beginners-course") {
					$lead["channel"] = "IOT_FreeTrial";
				}
				else if ($built_url == "full-stack-iot") {
					// $lead["channel"] = "IOT_FS";
				}

			}
			else if (stripos($built_url, "lpinsta") !== false) {

				if (!$old_lead) {
					// $lead["source_2"] = "Paid";
				}
				else {
					// $lead["source_2_2"] = "Paid";
				}

				if (stripos($built_url, "ipba") !== false) {

					// $lead["category"] = "IPBA";
					// $lead["channel"] = "Search";

				}elseif (strpos($built_url, "pgd") !== false) {
					// $lead["category"] = "PGDS/FTDS";
				}elseif (strpos($built_url, "cloud") !== false) {
					// $lead["category"] = "Cloud-Computing";
				}
				else {

					// $lead["category"] = "Online";
					// $lead["channel"] = "Search";

				}

			}
			else if (stripos($built_url, "naukri-lp") !== false) {

				if (!$old_lead) {
					// $lead["source_2"] = "Naukri";
				}
				else {
					// $lead["source_2_2"] = "Naukri";
				}

			}
			else if ($lead["event"] == "referral") {

				if (!$old_lead) {
					$lead["source_2"] = "Referrals";
				}
				else {
					$lead["source_2_2"] = "Referrals";
				}

			}
			else if (stripos($built_url, "pgpdm") !== false || stripos($lead["referer"], "pgpdm") !== false) {
				// $lead["category"] = "PGPDM";
			}
			elseif (strpos($built_url, "pgd") !== false) {
				// $lead["category"] = "PGDS/FTDS";
			}
			/*else if (stripos($built_url, "big_data_careers") !== false || stripos($built_url, "business_analytics") !== false || stripos($built_url, "data_science_career") !== false) {
				$lead["source_2"] = "JA_Inbound";
			}*/
			// else if (stripos($built_url, "analytics-for-beginners") !== false) {

			// 	$lead["category"] = "Online";
			// 	// $lead["source_2"] = "AFB_FreeTrial";

			// }
			else {

				if ($lead["category"] ?? "" != "Mobile App") {
					$lead["category"] = "Online";
				}

			}

			if (!empty($meta["form_name"])) {

				if ($meta["form_name"] == "pathfinder") {

					if (!$old_lead) {
						$lead["source_2"] = "Organic";
					}
					else {
						$lead["source_2_2"] = "Organic";
					}

					$lead["category"] = "Online";
					$lead["channel"] = "Pathfinder";
					$lead["path_finder_thingy"] = $meta["path_name"];

				}

			}

			// WE MIGHT WANT TO REMOVE THIS ONE
			// if (strlen($lead["utm_term"]) > 0) {
			// 	$lead["source_2"] .= "+".$lead["utm_term"];
			// }
			// THIS ONE UP HERE!!

			// OLD CODE; NOT REQUIRED
			/*$landing_url_components = parse_url(trim($lead["landing_url"], "/"));
			$landing_url = $landing_url_components["path"];
			if (!empty($landing_url)) {

				if (stripos($landing_url, "corporate-training-analytics") !== false || stripos($landing_url, "about-us/careers") !== false) {

					$lead["source_2"] = "Corporate";
					// Corporate keys
					define("AccessKey", "u\$r15585100f3f082517d50b350365f2d08");
					define("SecretKey", "cc85fd4c9205ceda595e9c77e800e6df399db4ad");

					$meta = json_decode($lead["meta"], true);
					$lead["company"] = $meta["company"];
					$lead["comments"] = $meta["outcomes"];

				}
				else if (stripos($landing_url, "iot-beginners-course") !== false) {

					if (empty($lead["source_2"])) {
						$lead["source_2"] = "IOT Free Trial";
					}

				}

			}*/

			if (empty($lead["source_2"])) {

				if (!$old_lead) {

					// if (in_array($lead["category"] ?? "", ["PGPDM", "Bootcamp", "IOT", "Online"])) {
					// 	// $lead["source_2"] = "Organic";
					// }

				}
				else if (!empty($lead["source_2_2"])) {
					// $lead["source_2_2"] = "Organic";
				}

			}

			if (($lead["source_2"] ?? "") == "Corporate") {

				// Corporate keys
				define("AccessKey", "u\$r98364e50e96ad22f9ce3f40f2f2b3597");
				define("SecretKey", "75dfc985eefeb4c92a6eaeebb517155d049e840a");

			}
			else {

				define("AccessKey", "u\$r6daf2e31c28ab58d15cb696d4e0f6a43");
				define("SecretKey", "95c34e5756021c31bc0ded96a0fafd70320cd9a2");

			}

			if ($old_lead) {

				if ($old_lead == 1) {
					$lead["page_url_2"] = $lead["page_url"];
				}
				else {
					$lead["page_url_3"] = $lead["page_url"];
				}

				unset($lead["page_url"]);
				unset($lead["source_2"]);
				unset($lead["channel"]);
				unset($lead["category"]);

			}

			$payload = [];
			foreach ($lead as $key => $value) {

				if (empty(trim($value))) {
					continue;
				}

				if (isset($key_mapping[$key])) {
					$payload[] = ["Attribute" => $key_mapping[$key], "Value" => $value];
				}
				elseif (strpos($key, "mx_") === 0) {
					$payload[] = ["Attribute" => $key, "Value" => $value];
				}

			}

			if (($response = json_decode(ls_api($api_url, $payload, $lead["email"]), true)) === false) {
				return false;
			}
			if (empty($response["Status"]) || $response["Status"] != "Success" || empty($response["Message"]["Id"])) {
				return false;
			}

			$leadIdCrm = $response["Message"]["RelatedId"];
			// if (empty(db_query("SELECT * FROM ls_leads WHERE email = ".db_sanitize($lead["email"])))) {
			// 	db_exec("INSERT INTO ls_leads (email, lead_id) VALUES (".db_sanitize($lead["email"].", ".db_sanitize($leadIdCrm).");");
			// }

			if ($new) {
				update_lead_info($lead["email"], $lead["phone"] ?? "", $leadIdCrm, []);
			}

			if ($return) {
				return $leadIdCrm;
			}

		}

		// return $leadIdCrm;

	}

	function was_lead_prime_data_sent_before($email) {

		if (($lead = find_lead($email)) === false) {
			return false;
		}

		$email = db_sanitize($email);

		return count(db_query("SELECT id FROM ls_api WHERE email LIKE $email;"));

	}

	function sale_post($sales_info) {

	}

	function ls_payinfo_log($pay_id) {
		db_exec("INSERT INTO ls_payexport (pay_id) VALUES (".$pay_id[0].");");
	}

	function activity_post($activity, $api_url = "ProspectActivity.svc/Create", $account = "retail") {

		if ($account == "retail") {

			define("AccessKey", "u\$r6daf2e31c28ab58d15cb696d4e0f6a43");
			define("SecretKey", "95c34e5756021c31bc0ded96a0fafd70320cd9a2");

		}
		else if ($account == "pgpdm") {

			define("AccessKey", "u\$rd5e332f7de37dd3a511694244ad26322");
			define("SecretKey", "a2f2a0baf36118817dbe214e629e2c1604901c04");

		}

		$key_mapping = [
			"lead_id" => "RelatedProspectId",
			"activity_id" => "ActivityEvent",
			"create_date" => "ActivityDateTime"
		];

		foreach ($activity as $key => $value) {

			if (isset($key_mapping[$key])) {
				$payload[$key_mapping[$key]] = $value;
			}

		}

		if (isset($activity["fields"])) {

			$payload["Fields"] = [];
			foreach ($activity["fields"] as $key => $value) {
				$payload["Fields"][] = ["SchemaName" => $key, "Value" => $value];
			}

		}

		if (($response = json_decode(ls_api($api_url, $payload, $activity["email"]), true)) === false) {
			return false;
		}
		if (empty($response["Status"]) || $response["Status"] != "Success" || empty($response["Message"]["Id"])) {
			return ["status" => false, "msg" => $response["ExceptionMessage"]];
		}

		return $response["Message"]["Id"];

	}

	function ls_api($api_url, $data, $id, $params = []) {

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, api_url_construct($api_url, $params));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
		if (!empty($data)) {

			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

		}

		$response = curl_exec($ch);

		curl_close($ch);

		db_exec("INSERT INTO ls_api (email, request, response) VALUES (".db_sanitize($id).", ".db_sanitize(json_encode($data)).", ".db_sanitize(json_encode($response)).");");

		return $response;

	}

	function api_url_construct($api_url, $params = []) {

		$url = Domain.$api_url."?accessKey=".AccessKey."&secretKey=".SecretKey;

		$extra_params = [];
		foreach ($params as $key => $value) {
			$extra_params[] = $key."=".$value;
		}

		if (!empty($extra_params)) {
			$extra_params = "&".implode("&", $extra_params);
		}
		else {
			$extra_params = "";
		}

		return $url.$extra_params;

	}

	function find_lead($email, $lead_id = false) {

		$lead = db_query("SELECT * FROM ls_leads WHERE email = ".db_sanitize($email).(!empty($lead_id) ? " OR lead_id = ".db_sanitize($lead_id) : "").";");
		if (empty($lead)) {
			return false;
		}

		return $lead;

	}

	function update_lead_info($email, $phone, $lead_id, $lead_data = []) {

		if (!empty(find_lead($email, $lead_id))) {
			return;
		}

		$email = db_sanitize($email);
		$phone = db_sanitize($phone);
		$lead_id = db_sanitize($lead_id);
		$lead_data = db_sanitize(json_encode($lead_data));

		db_exec("INSERT INTO ls_leads (email, phone, lead_id, lead_data) VALUES (".$email.", ".$phone.", ".$lead_id.", ".$lead_data.");");

	}

	// Commented out and rewritten on 21st Feb, 2019 on request from Mahesh
	// This version invokes the Lead.Capture API and sends Lead assignment Date
	// The new versions invokes ProspectActivity.svc/Create API and adds a new
	// 'activity' to the lead, so that it does not appear as Lead Capture on LS
	/*function update_lead_assignment($data) {

		$email = $data["After"]["EmailAddress"];
		$lead_id = $data["After"]["ProspectID"];

		// $is_retail = $data["Before"]["OwnerIdEmailAddress"] == "reports@jigsawacademy.com";
		// if (!$is_retail) {
		// 	return;
		// }
		$reassigned_at = $data["After"]["LastModifiedOn"];

		$lead = find_lead($email, $lead_id);
		if (!empty($lead)) {

			if (empty($lead[0]["reassigned_at"])) {

				db_exec("UPDATE ls_leads SET reassigned_at = ".db_sanitize($reassigned_at)." WHERE email = ".db_sanitize($email).";");

				$lead = [
					"email" => $email,
					"reassigned_at" => $reassigned_at
				];

				$key_mapping = [
					"email" => "EmailAddress",
					"reassigned_at" => "mx_Lead_assigned_date"
				];

				// if ($is_retail) {

					define("AccessKey", "u\$r6daf2e31c28ab58d15cb696d4e0f6a43");
					define("SecretKey", "95c34e5756021c31bc0ded96a0fafd70320cd9a2");

				// }
				// else {

				// 	define("AccessKey", "u\$rd5e332f7de37dd3a511694244ad26322");
				// 	define("SecretKey", "a2f2a0baf36118817dbe214e629e2c1604901c04");

				// }

				$payload = [];
				foreach ($lead as $key => $value) {

					if (empty(trim($value))) {
						continue;
					}

					if (isset($key_mapping[$key])) {
						$payload[] = ["Attribute" => $key_mapping[$key], "Value" => $value];
					}

				}

				$api_url = "LeadManagement.svc/Lead.Capture";

				if (($response = json_decode(ls_api($api_url, $payload, $lead["email"]), true)) === false) {
					return false;
				}
				if (empty($response["Status"]) || $response["Status"] != "Success" || empty($response["Message"]["Id"])) {
					return false;
				}

			}

			$meta = db_sanitize(json_encode($data["After"]));
			db_exec("UPDATE ls_leads SET meta = $meta WHERE email = ".db_sanitize($email).";");

		}

	}*/

	// The new version as described above
	function update_lead_assignment($data) {

		$email = $data["After"]["EmailAddress"];
		$lead_id = $data["After"]["ProspectID"];

		$reassigned_at = $data["After"]["LastModifiedOn"];

		db_exec("UPDATE ls_leads SET reassigned_at = ".db_sanitize($reassigned_at)." WHERE email = ".db_sanitize($email).";");

		$lead = [
			"lead_id" => $lead_id,
			"create_date" => date("Y-m-d h:i:s"),
			"activity_id" => 218,
			"fields" => [
				"mx_Custom_1" => $reassigned_at
			],
			"email" => $email
		];

		$res = activity_post($lead);
		if ($res === false || is_array($res)) {
			return false;
		}

		move_tasks_for_lead($lead_id, $data["After"]["OwnerId"]);

		$lead = find_lead($email, $lead_id);
		if (!empty($lead)) {

			$meta = db_sanitize(json_encode($data["After"]));
			db_exec("UPDATE ls_leads SET meta = $meta WHERE email = ".db_sanitize($email).";");

		}

	}

	function move_tasks_for_lead($lead_id, $owner_id) {

		$tasks = fetch_lead_tasks($lead_id);
		$sanitized = db_sanitize(json_encode($tasks));
		// db_exec("INSERT INTO system_log (source, data) VALUES ('lead.tasks.move', $sanitized);");
		if ($tasks["RecordCount"] > 0) {

			foreach ($tasks["TaskList"] as $task) {

				$task_info = [
					"UserTaskId" => $task["TaskID"],
					"OwnerId" => $owner_id
				];

				ls_api("Task.svc/Update", $task_info, $lead_id);

			}

		}

	}

	function update_lead_task_ownership($data) {

		return;

		$email = $data["After"]["EmailAddress"];
		$lead_id = $data["After"]["ProspectID"];

		$is_retail = $data["Before"]["OwnerIdEmailAddress"] == "reports@jigsawacademy.com";
		$reassigned_at = $data["After"]["LastModifiedOn"];

		$lead = find_lead($email, $lead_id);
		if (!empty($lead)) {

			if (empty($lead[0]["reassigned_at"])) {

				db_exec("UPDATE ls_leads SET reassigned_at = ".db_sanitize($reassigned_at)." WHERE email = ".db_sanitize($email).";");

				$lead = [
					"email" => $email,
					"reassigned_at" => $reassigned_at
				];

				$key_mapping = [
					"email" => "EmailAddress",
					"reassigned_at" => "mx_Lead_assigned_date"
				];

				if ($is_retail) {

					define("AccessKey", "u\$r6daf2e31c28ab58d15cb696d4e0f6a43");
					define("SecretKey", "95c34e5756021c31bc0ded96a0fafd70320cd9a2");

				}
				else {

					define("AccessKey", "u\$rd5e332f7de37dd3a511694244ad26322");
					define("SecretKey", "a2f2a0baf36118817dbe214e629e2c1604901c04");

				}

				$payload = [];
				foreach ($lead as $key => $value) {

					if (empty(trim($value))) {
						continue;
					}

					if (isset($key_mapping[$key])) {
						$payload[] = ["Attribute" => $key_mapping[$key], "Value" => $value];
					}

				}

				$api_url = "LeadManagement.svc/Lead.Capture";

				if (($response = json_decode(ls_api($api_url, $payload, $lead["email"]), true)) === false) {
					return false;
				}
				if (empty($response["Status"]) || $response["Status"] != "Success" || empty($response["Message"]["Id"])) {
					return false;
				}

			}

		}

	}

	function fetch_lead($id, $email = false) {

		define("AccessKey", "u\$r6daf2e31c28ab58d15cb696d4e0f6a43");
		define("SecretKey", "95c34e5756021c31bc0ded96a0fafd70320cd9a2");

		$url = "LeadManagement.svc/Leads.";

		$params = [];
		if ($email) {

			$params["emailaddress"] = $id;
			$url .= "GetByEmailaddress";

		}
		else {

			$params["id"] = $id;
			$url .= "GetById";

		}

		return json_decode(ls_api($url, [], $id, $params), true);

	}

	function fetch_lead_activities($lead_id) {

		if (!defined("AccessKey")){
			define("AccessKey", "u\$r6daf2e31c28ab58d15cb696d4e0f6a43");
		}
		if (!defined("SecretKey")) {
			define("SecretKey", "95c34e5756021c31bc0ded96a0fafd70320cd9a2");
		}

		return json_decode(ls_api("ProspectActivity.svc/Retrieve", ["Paging" => ["Offset" => 0, "RowCount" => 50]], $lead_id, ["leadId" => $lead_id]), true);

	}

	function fetch_lead_tasks($lead_id) {

		define("AccessKey", "u\$r6daf2e31c28ab58d15cb696d4e0f6a43");
		define("SecretKey", "95c34e5756021c31bc0ded96a0fafd70320cd9a2");

		return json_decode(ls_api("LeadManagement.svc/RetrieveTaskByLeadId", [], $lead_id, ["leadId" => $lead_id]), true);

	}

?>
