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

	// Dynamics PE CRM Plugin
	// ------------------------

	// The use of this plugin is to sync data with MS Dynamics PE Portal
	// This will mostly send out data by hook-ing in to the JAWS core (Data-Out)
	// This will also define functions used by the CRM/dynamics_pe API (Data-In)
	// ------------------------

	// Load Stuff
	// IMPORTANT ! DO NOT LOAD ANYTHING HERE !
	// All modules and libraries are to be loaded within the handler functions ONLY !!
	// As this plugin will be Auto-Loaded when set in CRM config, if modules/libraries are loaded in GLOBAL scope, they will be loaded everytime on JAWS execution, even if plugin functions are not being used, thereby unnecessarily increasing load times !
	// ------------------------

	// Hooks In
	// ------------------------

	hook("course_update__", "dynamics_pe_course_push");
	hook("course_create__", "dynamics_pe_course_push");
	hook("leads_basic_compile__", "dynamics_pe_lead_push");
	//hook("package_create__", "dynamics_pe_package_push");
	//hook("package_update__", "dynamics_pe_package_push");

	// Debug Log Hook
	hook("dynamics_pe_data_push__", "dynamics_pe_data_log");

	/*hook("_leads_basic_assoc_user_by_id", "dynamics_pe_lead_handler_capture_on_assoc_id");
	hook("_leads_basic_assoc_user_partial", "dynamics_pe_lead_handler_capture_on_assoc_partial");
	hook("_leads_basic_capture", "dynamics_pe_lead_handler_capture_full");*/

	// Handlers - Courses
	// ------------------------

	// This function is used to push course info to the CRM
	function dynamics_pe_course_push($data, $from) {

		// Load Library
		load_library("persistence");
		$data = $data[0];

		// Construct the JSON object that the CRM API expects
		$course = array(
				"course_id" => $data["course_id"],
				"name" => $data["name"],
				"premium_code" => $data["il_code"],
				"regular_code" => $data["sp_code"],
				"regular_price_inr" => $data["sp_price_inr"],
				"premium_price_inr" => $data["il_price_inr"],
				"regular_price_usd" => $data["sp_price_usd"],
				"premium_price_usd" => $data["il_price_usd"],
				"regular_status_inr" => $data["sp_status_inr"],
				"premium_status_inr" => $data["il_status_inr"],
				"regular_status_usd" => $data["sp_status_usd"],
				"premium_status_usd" => $data["il_status_usd"],
		);

		// Try Push - IMPORTANT !!! USE SCHEDULER FOR PUSH !!! <<---------------------------------
		$ret = json_decode(dynamics_pe_api("UpdateCourse", $course), true);

		// Persist - Will auto check current persistence status internally
		if (strcmp($ret["success"], "true") == 0) persist("dynpepl", "course", $data["course_id"], $ret["crmId"]);
		else {
			// Log transaction failure - gateway was not successful
        			activity_create("high", "dynpepl.course.persist", "fail", "course_id",  $data["course_id"], "", "", $ret["errorMessage"], "logged");

        			// RESCHEDULE THE TASK HERE !!! <<--------------------------------------------
		}

	}

	// Handlers - Leads
	// ------------------------

	/*function dynamics_pe_lead_handler_capture_on_assoc_id($data, $from) {

	}

	function dynamics_pe_lead_handler_capture_on_assoc_partial($data, $from) {

	}

	function dynamics_pe_lead_handler_capture_full($data, $from) {

	}*/

	// This function is used to push a lead data to the CRM
	function dynamics_pe_lead_push($data, $from) {

		$data = $data[0];

		load_library("persistence");
		load_library("url");

		// Try Push - IMPORTANT !!! USE SCHEDULER FOR PUSH !!! <<---------------------------------
		// add return value when scheduler is used...to remove from pending stack if successful
		foreach ($data as $lead) {

			if (strlen($lead["phone"]) < 6) {
				continue;
			}

			// CRM specific modifications
			$lead["source_1"] = $lead["xuid"];
			$lead["source_2"] = "";

			if (strlen($lead["utm_source"]) == 0) {

				if (strcmp($lead["event"], "formsubmit") == 0) {
					$lead["source_2"] = "Site Form";
				}
				else if (strcmp($lead["event"], "reg") == 0) {
					$lead["source_2"] = "Social login";
				}
				else if (strcmp($lead["event"], "login") == 0) {
					$lead["source_2"] = "Repeat login";
				}

			}

			if (strlen($lead["utm_term"]) > 0) {
				$lead["source_2"] .= "+".$lead["utm_term"];
			}

			unset($lead["user_id"]);

			$page_url = url_template_from_string($lead["page_url"]);
			if (strpos($lead["page_url"], "#") !== false) {
				$built_url = substr($lead["page_url"], 0, strpos($lead["page_url"], "#"));
			}
			if (strpos($lead["page_url"], "?") !== false) {
				$built_url = substr($lead["page_url"], 0, strpos($lead["page_url"], "?"));
			}
			$built_url = trim($built_url, "/");
			if ((strcmp("https://www.jigsawacademy.com/about-us/careers", $built_url) == 0) || strcmp("https://www.jigsawacademy.com/corporate-training-analytics", $built_url) == 0 || strcmp("corporate-training-analytics", $built_url) == 0 || strcmp("about-us/careers", $built_url) == 0) {
				//continue;
				$lead["source_2"] = "Corporate";
			}
			else if ($built_url == "https://www.jigsawacademy.com/bocconi-business-analytics-program-mumbai" || strpos($lead["page_url"], "bocconi-business-analytics-program-mumbai") !== false) {
				$lead["source_2"] = "Bocconi";
			}
			else if ($built_url == "https://www.jigsawacademy.com/pgpdm" || strpos($lead["page_url"], "pgpdm") !== false || strpos($lead["referer"], "pgpdm") !== false) {
				$lead["source_2"] = "PGPDM-Organic";
			}

			$page_url = trim($lead["page_url"], "/");
			$landing_url = trim($lead["landing_url"], "/");
			if ($page_url == "corporate-training-analytics" || $page_url == "about-us/careers" || $landing_url == "corporate-training-analytics" || $landing_url == "about-us/careers") {
				$lead["source_2"] = "Corporate";
			}
			//$lead["source_3"] = $page_url["path"];

			if (strpos($lead["page_url"], "idm") !== false || strpos($lead["landing_url"], "idm") !== false || strpos($lead["referer"], "idm") !== false) {
				$lead["source_2"] = "IDM";
			}
			if ($lead["page_url"] == "naukri-lp") {
				$lead["source_2"] = "Naukri";
			}

			if ($lead["event"] == "referral") {
				$lead["source_2"] = "refer";
			}

			$return_data = dynamics_pe_api("CreateLeadEnquiry", $lead);

			if ($return_data === false) {

				activity_create("critical", "dynpepl.lead.persist", "critical failure", "lead_email", "", $lead["email"], "", json_encode(["email" => $lead["email"], "error" => "No data received from CRM"]), "logged");
				return;

			}

			$ret = json_decode($return_data, true);

			if (strcmp($ret["success"], "true") == 0) {

				if (!is_persistent(array("layer" => "dynpepl", "type" => "package", "id" => $ret["crmId"]))) {
					persist("dynpepl", "lead", $lead["email"], $ret["crmId"]);
				}

			}
			else {
				// Log transaction failure - gateway was not successful
	       		activity_create("high", "dynpepl.lead.persist", "fail", "lead_email",  "", $lead["email"], "", json_encode(["email" => $lead["email"], "error" => $ret["errorMessage"]]), "logged");
			}

		}

	}

	// Handler - Package
	function dynamics_pe_package_push($data, $from)
	{
		load_library("persistence");

		if (isset($_GET["token"])) {
			return;
		}

		$data[0]["discount"] = $data[0]["data_payment_discount"];

		// Unset specific fields
		//unset($data[0]["package_id"]);
		unset($data[0]["user_id"]);
		unset($data[0]["bundle_id"]);
		unset($data[0]["approval_require_comment"]);
		unset($data[0]["serialized"]);
		unset($data[0]["data_courses_combo"]);
		unset($data[0]["data_courses_actual"]);
		unset($data[0]["data_courses_discount"]);
		unset($data[0]["data_payment_discount"]);
		unset($data[0]["data_tax_amount"]);
		unset($data[0]["data_discount_amount"]);
		unset($data[0]["data_offered_amount"]);
	unset($data[0]["data_instalment_amount"]);
		unset($data[0]["data_net_payable"]);
		unset($data[0]["data_edit_offered_price"]);
		unset($data[0]["data_edit_discount_amount"]);
		unset($data[0]["data_edit_discount_percent"]);
		unset($data[0]["data_edit_tax_amount"]);
		unset($data[0]["data_bundle_price"]);
		unset($data[0]["data_bundle_combo"]);
		unset($data[0]["data_instalment_fees_inr"]);
		unset($data[0]["data_instalment_fees_usd"]);

		// Replace the native creator_id with the CRM user ID
		$data[0]["creator_id"] = get_external_id(array("layer" => "dynpepl", "type" => "user", "id" => $data[0]["creator_id"]))["id"];

		// Replace the instl json with the format expected by CRM
		$instl = json_decode($data[0]["instl"], true);
		$instl_summary = array();
		foreach ($instl as $count => $emi)
			$instl_summary[] = array("instl_count" => $count."", "sum" => $emi["sum"], "due_days" => $emi["due_days"]);

		$data[0]["instl"] = $instl_summary;
		$data[0]["creator_comment"] = json_decode($data[0]["creator_comment"], true);

		// Try push!
		$ret = json_decode(dynamics_pe_api("CreatePackage", $data[0]), true);

		if (strcmp($ret["success"], "true") == 0) {
			// if (is_persistent(array("layer" => "dynpepl", "type" => "package", "id" => $ret["crmId"])))
				// db_exec("DELETE FROM system_persistence_map WHERE layer='dynpepl' AND entity_type='package' AND ext_id=".db_sanitize($ret["crmId"]).";");
			persist("dynpepl", "package", $data[0]["package_id"], $ret["crmId"]);
		}
		else {
			// Log transaction failure - gateway was not successful
        		activity_create("high", "dynpepl.package.persist", "fail", "package_id",  $data[0]["package_id"], "", "", $ret["errorMessage"], "logged");
		}
	}

	// Updates payment to CRM
	function dynamics_pe_package_payment_update($pay_info)
	{
		dynamics_pe_api("CreatePayment", $pay_info);
	}


	// Dynamics_PE Plugin Specific Functions
	// ------------------------

	// This function will connect to the dynamics_pe CRM and call its API
	// This is used to PUSH the data
	// This function is used internally only by the hook handlers - this is plugin specific function
	function dynamics_pe_api($func, $data) {

		// Prep
        		$opts = array('http' => array(
                  			'method'  => 'POST',
                  			'header'  => "content-type: application/json\r\n".
              					"api-key: ".JAWS_CRM_DYNAMICS_PE_KEY."\r\n".
              					"access-token: ".JAWS_CRM_DYNAMICS_PE_TOKEN."\r\n".
              					"cache-control: no-cache",
                  			'content' => json_encode($data)
                  		)
                	);
    		$context  = stream_context_create($opts);

    		// Send
    		$return_data = file_get_contents(JAWS_CRM_DYNAMICS_PE_URL."/".$func, false, $context);

    		$data = array("data" => $data, "return_data" => $return_data);

    		handle("dynamics_pe_data_push__", $data);

    		return $return_data;

	}
	// This is same as above but only package data being sent to another url not production url as per mohan's mail on 25-10-16
	function dynamics_pe_api2($func, $data) {

		// Prep
        		$opts = array('http' => array(
                  			'method'  => 'POST',
                  			'header'  => "content-type: application/json\r\n".
              					"api-key: ".JAWS_CRM_DYNAMICS_PE_KEY."\r\n".
              					"access-token: ".JAWS_CRM_DYNAMICS_PE_TOKEN."\r\n".
              					"cache-control: no-cache",
                  			'content' => json_encode($data)
                  		)
                	);
    		$context  = stream_context_create($opts);
$url = "https://jigsawstudentdemo.positiveedge.net/JigsawCrmSyncService.svc/CreatePackage";
    		// Send
    		$return_data = file_get_contents($url."/".$func, false, $context);

    		$data = array("data" => $data, "return_data" => $return_data);

    		handle("dynamics_pe_data_push__", $data);

    		return $return_data;

	}


	// Log the data being sent out
	function dynamics_pe_data_log($data, $from)
	{
		//activity_debug_start();
		//activity_debug_log(json_encode($data[0]));
	}






?>
