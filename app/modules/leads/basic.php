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

	// This tracks basic leads
	// Basic leads are UTM params and global IDs etc that can be captured when a person ands onto any of the leads capturing page
	// The captured leads are smartly associated with the information the user submits baut self, on form submition or login
	//-------------------

	// Load required stuff
	load_module("user");

	// This will associate an user with the given email ID with all leads that submit information with this emai ID (partially-associated leads)
	// Note: it does not remove any user information from the updated leads records, the association is done to leads which do not have an associated user_id
	function leads_basic_assoc_user_by_email($email) {

		handle("__leads_basic_assoc_user_by_email", func_get_args()); // Hook Handler - Pre Execution

		// Get User
		$user = user_get_by_email($email);
		if ($user === false) return false;

		// Check if user is Jigsaw employee - parse roles to see if leads feature key is denied
		// This is done to avoid Jigsaw employees from generating leads
		load_module("auth");
		$role = auth_get_roles($user["roles"]);
		if ((isset($role["feature_keys"]["leads.basic.capture"])) && ($role["feature_keys"]["leads.basic.capture"] == 0))  {
			// Internal employee role restriction
			// Delete the leads
			db_exec("DELETE FROM user_leads_basic WHERE (user_id IS NULL) AND (email=".db_sanitize($email).");");
			return false;
		}

		handle("_leads_basic_assoc_user_by_email", func_get_args()); // Hook Handler - Mid Execution

		db_exec("UPDATE user_leads_basic SET user_id=".$user["user_id"]." WHERE (user_id IS NULL) AND (email=".db_sanitize($email).");");
		return true;

	}

	// This will associate all leads with the given token to a specified user
	//Note: the provided token will be removed from the leads records.
	function leads_basic_assoc_user_by_id($user_id, $token) {

		handle("__leads_basic_assoc_user_by_id", func_get_args()); // Hook Handler - Pre Execution

		// Check
		if ((strlen($token) == 0) || (strlen($token) > 32)) return false;

		// Get User
		$user = user_get_by_id($user_id);
		if ($user === false) return false;

		// Check if user is Jigsaw employee - parse roles to see if leads feature key is denied
		// This is done to avoid Jigsaw employees from generating leads
		load_module("auth");
		$role = auth_get_roles($user["roles"]);

		if ((isset($role["feature_keys"]["leads.basic.capture"])) && ($role["feature_keys"]["leads.basic.capture"] == 0))  {
			// Internal employee role restriction
			// Delete the leads
			db_exec("DELETE FROM user_leads_basic WHERE (user_id IS NULL) AND (assoc_token=".db_sanitize($token).");");
			return false;
		}

		handle("_leads_basic_assoc_user_by_id", func_get_args()); // Hook Handler - Mid Execution

		db_exec("UPDATE user_leads_basic SET user_id=".$user["user_id"].", assoc_token=NULL WHERE (user_id IS NULL) AND (assoc_token=".db_sanitize($token).");");
		return true;

	}

	// This will put temp user info (when user is non-existant on JAWS) for all leads that have the given token. (creating partially associated leads)
	// These leads will later be assocced to the correct user when a user with the given email ID signs up or an user assocs the email ID to any of his 4 email fields (comm/soc)
	//Note: the provided token will be removed from the leads records.
	//Note: this will internally check if the email already belongs to an user
	function leads_basic_assoc_user_partial($user_info, $token) {

		handle("__leads_basic_assoc_user_partial", func_get_args()); // Hook Handler - Pre Execution

		// Check missing info
		if (!isset($user_info["email"])) $user_info["email"] = "";
		if (!isset($user_info["phone"])) $user_info["phone"] = "";
		if (!isset($user_info["name"])) $user_info["name"] = "";
		if ((strlen($token) == 0) || (strlen($token) > 32)) return false;

		// Check exisiting user
		$flag_user = false;
		$user;
		if (strlen($user_info["email"]) > 0) {
			$user = user_get_by_email($user_info["email"]);
			if (!($user === false)) {
				$flag_user = true;

				// Check if user is Jigsaw employee - parse roles to see if leads feature key is denied
				// This is done to avoid Jigsaw employees from generating leads
				load_module("auth");
				$role = auth_get_roles($user["roles"]);
				if ((isset($role["feature_keys"]["leads.basic.capture"])) && ($role["feature_keys"]["leads.basic.capture"] == 0))  {
					// Internal employee role restriction
					// Delete the leads
					db_exec("DELETE FROM user_leads_basic WHERE (user_id IS NULL) AND (assoc_token=".db_sanitize($token).");");
					return false;
				}

			}
		}

		handle("_leads_basic_assoc_user_partial", func_get_args()); // Hook Handler - Mid Execution

		// Exec
		db_exec("UPDATE user_leads_basic SET user_id=".( $flag_user ? $user["user_id"] : "NULL").", email=".db_sanitize($user_info["email"]).", phone=".db_sanitize($user_info["phone"]).", name=".db_sanitize($user_info["name"]).", assoc_token=NULL WHERE (user_id IS NULL) AND (assoc_token=".db_sanitize($token).");");

		// All done
		return true;

	}

	// This will capture a lead with the UTM parameters etc.
	// It can operate with user ID or no user ID, in which case it will either use the specified token or generate a token for use
	function leads_basic_capture($lead_params, $mode = "token", $id_or_token = "", $post_data = array() ) {

		handle("__leads_basic_capture", func_get_args()); // Hook Handler - Pre Execution

		// Set the missing data
		if (!isset($lead_params["utm_source"])) {
			$lead_params["utm_source"] = "";
		}
		if (!isset($lead_params["utm_campaign"])) {
			$lead_params["utm_campaign"] = "";
		}
		if (!isset($lead_params["utm_term"])) {
			$lead_params["utm_term"] = "";
		}
		if (!isset($lead_params["utm_medium"])) {
			$lead_params["utm_medium"] = "";
		}
		if (!isset($lead_params["utm_content"])) {
			$lead_params["utm_content"] = "";
		}
		if (!isset($lead_params["utm_segment"])) {
			$lead_params["utm_segment"] = "";
		}
		if (!isset($lead_params["utm_numvisits"])) {
			$lead_params["utm_numvisits"] = "";
		}
		if (!isset($lead_params["gcl_id"])) {
			$lead_params["gcl_id"] = "";
		}
		if (!isset($lead_params["global_id_perm"])) {
			$lead_params["global_id_perm"] = "";
		}
		if (!isset($lead_params["global_id_session"])) {
			$lead_params["global_id_session"] = "";
		}
		if (!isset($lead_params["referer"])) {
			$lead_params["referer"] = "";
		}
		if (!isset($lead_params["ip"])) {
			$lead_params["ip"] = "";
		}
		if (!isset($lead_params["ad_lp"])) {
			$lead_params["ad_lp"] = "";
		}
		if (!isset($lead_params["ad_url"])) {
			$lead_params["ad_url"] = "";
		}
		if (!isset($lead_params["type"])) {
			$lead_params["type"] = "";
		}
		if (!isset($lead_params["trigger"])) {
			$lead_params["trigger"] = "";
		}
		if (empty($lead_params["__tr"])) {
			$lead_params["__tr"] = "";
		}

		// post data save for logged in user as well.
		if (!isset($post_data["name"])) {
			$post_data["name"] = "";
		}
		if (!isset($post_data["email"])) {
			$post_data["email"] = "";
		}
		if (!isset($post_data["phone"])) {
			$post_data["phone"] = "";
		}

		if (empty($post_data["meta"])) {
			$post_data["meta"] = [];
		}

		if (!empty($post_data["submitted_form_name"])) {
			$post_data["meta"]["form_name"] = $post_data["submitted_form_name"];
		}

		// Check capture type
		if ((strcmp($lead_params["type"], "url") != 0) && (strcmp($lead_params["type"], "cookie") != 0) && (strcmp($lead_params["type"], "") != 0)) {
			return false;
		}

		// Length check + sanitize
		$lead_params["utm_source"] = db_sanitize(substr($lead_params["utm_source"], 0, 128));
		$lead_params["utm_campaign"] = db_sanitize(substr($lead_params["utm_campaign"], 0, 128));
		$lead_params["utm_term"] = db_sanitize(substr($lead_params["utm_term"], 0, 128));
		$lead_params["utm_medium"] = db_sanitize(substr($lead_params["utm_medium"], 0, 128));
		$lead_params["utm_content"] = db_sanitize(substr($lead_params["utm_content"], 0, 128));
		$lead_params["utm_segment"] = db_sanitize(substr($lead_params["utm_segment"], 0, 128));
		$lead_params["utm_numvisits"] = db_sanitize(substr($lead_params["utm_numvisits"], 0, 8));
		$lead_params["gcl_id"] = db_sanitize(substr($lead_params["gcl_id"], 0, 64));
		$lead_params["global_id_perm"] = db_sanitize(substr($lead_params["global_id_perm"], 0, 64));
		$lead_params["global_id_session"] = db_sanitize(substr($lead_params["global_id_session"], 0, 64));
		$lead_params["referer"] = db_sanitize(substr($lead_params["referer"], 0, 256));
		$lead_params["ip"] = db_sanitize(substr($lead_params["ip"], 0, 16));
		$lead_params["ad_lp"] = db_sanitize(substr($lead_params["ad_lp"], 0, 32));
		$lead_params["ad_url"] = db_sanitize(substr($lead_params["ad_url"], 0, 256));
		$lead_params["type"] = db_sanitize($lead_params["type"]);
		$lead_params["trigger"] = db_sanitize(substr($lead_params["trigger"], 0, 32));

		// Post data length check + sanitize
		$post_data["name"] = db_sanitize(substr($post_data["name"],0,64));
		$post_data["email"] = db_sanitize(substr($post_data["email"],0,64));
		$post_data["phone"] = db_sanitize(substr($post_data["phone"],0,16));

		// Prep
		$token = "";
		$date = strval(date("Y-m-d H:i:s"));

		// Check mode
		if (strcmp($mode, "id") == 0) {
			$user = user_get_by_id($id_or_token);
			if ($user === false) return false;

			// Check if user is Jigsaw employee - parse roles to see if leads feature key is denied
			// This is done to avoid Jigsaw employees from generating leads
			load_module("auth");
			$role = auth_get_roles($user["roles"]);
			if ((isset($role["feature_keys"]["leads.basic.capture"])) && ($role["feature_keys"]["leads.basic.capture"] == 0))  return false;

			handle("_leads_basic_capture", func_get_args()); // Hook Handler - Mid Execution

		}
		else if (strcmp($mode, "token") == 0) {

			if (strlen($id_or_token) == 0) $token = md5($date.$lead_params["ip"].$lead_params["ad_lp"].$lead_params["ad_url"]);
			else $token =  $id_or_token;

		}
		else return false;

		$__tr = $_COOKIE["__tr"] ?? "";
		if (!empty($__tr)) {
			$__tr = db_sanitize($__tr);
		}
		else {
			$__tr = "NULL";
		}

		if (!empty($lead_params["__tr"])) {
			$__tr = db_sanitize($lead_params["__tr"]);
		}

		//Exec
		db_exec("INSERT INTO user_leads_basic (user_id, utm_source, utm_campaign, utm_term, utm_medium, utm_content, utm_segment, utm_numvisits, gcl_id, global_id_perm, global_id_session, referer, ip, ad_lp, ad_url, create_date, assoc_token, capture_trigger, capture_type, name, email, phone, meta, __tr) VALUES (".((strcmp($mode, "id") == 0) ? $id_or_token : "NULL" ).", ".$lead_params["utm_source"].", ".$lead_params["utm_campaign"].", ".$lead_params["utm_term"].", ".$lead_params["utm_medium"].", ".$lead_params["utm_content"].", ".$lead_params["utm_segment"].", ".$lead_params["utm_numvisits"].", ".$lead_params["gcl_id"].", ".$lead_params["global_id_perm"].", ".$lead_params["global_id_session"].", ".$lead_params["referer"].", ".$lead_params["ip"].", ".$lead_params["ad_lp"].", ".$lead_params["ad_url"].", ".db_sanitize($date).", ".((strcmp($mode, "token") == 0) ? db_sanitize($token) : "NULL" ).", ".$lead_params["trigger"].", ".$lead_params["type"].", ".$post_data["name"].", ".$post_data["email"].", ".$post_data["phone"].", ".(isset($post_data["meta"]) ? db_sanitize(json_encode($post_data["meta"])) : 'NULL').", ".$__tr.");");

		//All done - return appropriate value
		if ($mode == "token") {
			if (strlen($id_or_token) == 0) return $token;
			else return true;
		}
		else return true;

	}

	load_library("setting");
	load_module("activity");

	$compilation_failure;

	function leads_basic_compile($params = "")
	{
		register_shutdown_function("leads_basic_compile_failure");
		GLOBAL $compilation_failure;
		$compilation_failure = true;
		$leads_arr = array();
		// Params is for referrals; the referrals are not yet storred in the database, they are simply forwarded to hooks.
		if (!is_array($params))
		{
			// Auxiliary storage of leads for skipping duplicate records
			$leads_data;
			// Get the last processed Leads ID
			$last_leads_id = setting_get("leads_basic_capture_last");

			$res_leads = db_query("SELECT * FROM user_leads_basic WHERE lead_id>".$last_leads_id." AND (user_id IS NOT NULL OR (email IS NOT NULL AND phone IS NOT NULL AND name IS NOT NULL)) AND (capture_trigger IN ('formsubmit', 'form-submit', 'reg', 'login', 'ws-gateway', 'cart', 'phoneupdate', 'reg.android', 'clickthrough')) ORDER BY lead_id ASC LIMIT 1;");

			if (!isset($res_leads[0])) return;

			foreach ($res_leads as $lead)
			{
                                //JA-113 changes
                                updateLeadStatus($lead['lead_id'],BASIC_PROCESSED);
                                //JA-113 ends
				$rec = array();
                                $rec["lead_id"] = $lead['lead_id']; //JA-113 prod issue fix , lead id not passed

				$rec["user_id"] = "";
				$rec["meta"] = $lead["meta"];
				$rec["__tr"] = $lead["__tr"];
				$rec["cookies"] = $lead["cookies"];

				if (strlen($lead["user_id"]) == 0 || $lead["user_id"] === NULL)
				{
					$user = user_get_by_email(trim($lead["email"]));
					if ($user === false)
					{
						$rec["name"] = trim($lead["name"]);
						$rec["email"] = trim($lead["email"]);
						$rec["phone"] = trim($lead["phone"]);
					}
					else
					{
						$rec["user_id"] = $user["user_id"];
						$rec["name"] = $user["name"];
						$rec["email"] = $user["email"];
						$rec["phone"] = ((strlen($user["phone"]) == 0) ? $lead["phone"] : $user["phone"]);
					}
				}
				else
				{
					$user = user_get_by_id($lead["user_id"]);
					$rec["user_id"] = $user["user_id"];
					$rec["name"] = $user["name"];
					$rec["email"] = $user["email"];
					((strlen($user["phone"]) == 0) ? ($rec["phone"] = $lead["phone"]) : ($rec["phone"] = $user["phone"]));
				}

				// Check if the lead record is a duplicate (if it arrived within 2 secs)
				if (isset($leads_data[$rec["email"]]))
				{
					$date = date_create_from_format("Y-m-d H:i:s", $leads_data[$rec["email"]]);
					$date->add(new DateInterval("PT2S"));
					$create_date = date_create_from_format("Y-m-d H:i:s", $lead["create_date"]);
					if ($create_date < $date)
						continue;
				}

				$rec["ip"] = $lead["ip"];
				$rec["create_date"] = $lead["create_date"];
				$rec["event"] = $lead["capture_trigger"];
				$rec["referer"] = $lead["referer"];

				if (strcmp($lead["capture_trigger"], "formsubmit") == 0) {

					if (strcmp(trim($lead["ad_lp"]), 'www.jigsawacademy.com') == 0 ) {

						$query = "SELECT * FROM user_leads_basic WHERE (capture_trigger='pageload' OR capture_trigger='formload') AND (email=".db_sanitize($rec["email"]);
						if (strlen($rec["user_id"]) > 0)
							$query .= " OR user_id=".$rec["user_id"];
						$query .= ") AND create_date<".db_sanitize($lead["create_date"])." ORDER BY create_date DESC LIMIT 1;";
						$res_load_event = db_query($query);
						//if (!isset($res_load_event[0])) continue;

						$rec["page_url"] = $res_load_event[0]["referer"] ?? $lead["ad_url"] ?? '';
						$rec["landing_url"] = $res_load_event[0]["ad_url"] ?? $lead["ad_url"] ?? '';

						$rec["utm_source"] = $res_load_event[0]["utm_source"] ?? $lead["utm_source"] ?? '';
						$rec["utm_campaign"] = $res_load_event[0]["utm_campaign"] ?? $lead["utm_campaign"] ?? '';
						$rec["utm_term"] = $res_load_event[0]["utm_term"] ?? $lead["utm_term"] ?? '';
						$rec["utm_medium"] = $res_load_event[0]["utm_medium"] ?? $lead["utm_medium"] ?? '';
						$rec["utm_content"] = $res_load_event[0]["utm_content"] ?? $lead["utm_content"] ?? '';
						$rec["utm_segment"] = $res_load_event[0]["utm_segment"] ?? $lead["utm_segment"] ?? '';

						$rec["utm_numvisits"] = $res_load_event[0]["utm_numvisits"] ?? $lead["utm_numvisits"] ?? '';
						$rec["gcl_id"] = $lead["gcl_id"] ?? '';
						$rec["global_id_perm"] = $lead["global_id_perm"] ?? '';
						$rec["global_id_session"] = $lead["global_id_session"] ?? '';
						$rec["xuid"] = $res_load_event[0]["xuid"] ?? $lead["xuid"] ?? '';

						if (empty($rec["__tr"])) {

							if (!empty($res_load_event[0]["__tr"])) {
								$rec["__tr"] = $res_load_event[0]["__tr"];
							}

						}

					}
					else {

						$rec["page_url"] =  $lead["ad_url"] ?? '';
						$rec["landing_url"] =  $lead["ad_url"] ?? '';

						if (trim($lead["ad_lp"]) == "naukri-lp") {
							$rec["page_url"] = "naukri-lp";
						}

						$rec["utm_source"] =  $lead["utm_source"] ?? '';
						$rec["utm_campaign"] =  $lead["utm_campaign"] ?? '';
						$rec["utm_term"] =  $lead["utm_term"] ?? '';
						$rec["utm_medium"] =  $lead["utm_medium"] ?? '';
						$rec["utm_content"] =  $lead["utm_content"] ?? '';
						$rec["utm_segment"] =  $lead["utm_segment"] ?? '';

						$rec["utm_numvisits"] =  $lead["utm_numvisits"] ?? '';
						$rec["gcl_id"] = $lead["gcl_id"] ?? '';
						$rec["global_id_perm"] = $lead["global_id_perm"] ?? '';
						$rec["global_id_session"] = $lead["global_id_session"] ?? '';
						$rec["xuid"] = $lead["xuid"] ?? '';

					}

					if (empty($rec["__tr"])) {
						$rec["__tr"] = $lead["__tr"];
					}

				}
				elseif (strcmp($lead["capture_trigger"], "reg") == 0) {

					// URL decode the ru portion first!!
					$rec["page_url"] = urldecode(explode("ru=", $lead["ad_url"])[1]);

					$query = "SELECT * FROM user_leads_basic WHERE (capture_trigger='pageload' OR capture_trigger='formload') AND (email=".db_sanitize($rec["email"]);
					if (strlen($rec["user_id"]) > 0) {
						$query .= " OR user_id=".$rec["user_id"];
					}
					$query .= ") AND capture_type='url' AND create_date<".db_sanitize($lead["create_date"])." ORDER BY create_date DESC LIMIT 1;";
					$res_load_event = db_query($query);

					if (!isset($res_load_event[0])) {

	                    $rec["landing_url"] = "";
	                    $rec["utm_source"] = "";
	                    $rec["utm_campaign"] = "";
	                    $rec["utm_term"] = "";
	                    $rec["utm_medium"] = "";
	                    $rec["utm_content"] = "";
	                    $rec["utm_segment"] = "";

	                }
	                else {

	    				$load_event = $res_load_event[0];
	    				$rec["landing_url"] = $load_event["ad_url"];

	    				$rec["utm_source"] = $load_event["utm_source"];
	    				$rec["utm_campaign"] = $load_event["utm_campaign"];
	    				$rec["utm_term"] = $load_event["utm_term"];
	    				$rec["utm_medium"] = $load_event["utm_medium"];
	    				$rec["utm_content"] = $load_event["utm_content"];
	    				$rec["utm_segment"] = $load_event["utm_segment"];
	    				$rec["xuid"] = $load_event["xuid"] ?? $lead["xuid"] ?? '';

						if (empty($rec["__tr"]) && !empty($load_event["__tr"])) {
							$rec["__tr"] = $load_event["__tr"];
						}

	                }

					$rec["gcl_id"] = $lead["gcl_id"];
					$rec["global_id_perm"] = $lead["global_id_perm"];
					$rec["global_id_session"] = $lead["global_id_session"];
					$rec["utm_numvisits"] = $lead["utm_numvisits"];

					if (empty($rec["__tr"])) {
						$rec["__tr"] = $lead["__tr"];
					}

				}
				elseif (strcmp($lead["capture_trigger"], "ws-gateway") == 0) {

					$query = "SELECT * FROM user_leads_basic WHERE (capture_trigger='pageload' OR capture_trigger='formload') AND (email=".db_sanitize($rec["email"]);
					if (strlen($rec["user_id"]) > 0) {
						$query .= " OR user_id=".$rec["user_id"];
					}
					$query .= ") AND create_date<".db_sanitize($lead["create_date"])." ORDER BY create_date DESC LIMIT 1;";
					$res_load_event = db_query($query);

					$rec["page_url"] = $res_load_event[0]["referer"] ?? $lead["ad_url"] ?? '';
					$rec["landing_url"] = $res_load_event[0]["ad_url"] ?? $lead["ad_url"] ?? '';

					$rec["utm_source"] = $res_load_event[0]["utm_source"] ?? $lead["utm_source"] ?? '';
					$rec["utm_campaign"] = $res_load_event[0]["utm_campaign"] ?? $lead["utm_campaign"] ?? '';
					$rec["utm_term"] = $res_load_event[0]["utm_term"] ?? $lead["utm_term"] ?? '';
					$rec["utm_medium"] = $res_load_event[0]["utm_medium"] ?? $lead["utm_medium"] ?? '';
					$rec["utm_content"] = $res_load_event[0]["utm_content"] ?? $lead["utm_content"] ?? '';
					$rec["utm_segment"] = $res_load_event[0]["utm_segment"] ?? $lead["utm_segment"] ?? '';

					$rec["utm_numvisits"] = $res_load_event[0]["utm_numvisits"] ?? $lead["utm_numvisits"] ?? '';
					$rec["gcl_id"] = $lead["gcl_id"] ?? '';
					$rec["global_id_perm"] = $lead["global_id_perm"] ?? '';
					$rec["global_id_session"] = $lead["global_id_session"] ?? '';
					$rec["xuid"] = $res_load_event[0]["xuid"] ?? $lead["xuid"] ?? '';

					if (empty($rec["__tr"])) {

						if (!empty($res_load_event[0]["__tr"])) {
							$rec["__tr"] = $res_load_event[0]["__tr"];
						}
						else {
							$rec["__tr"] = $lead["__tr"];
						}

					}

				}
				elseif (strcmp($lead["capture_trigger"], "login") == 0) {

					if (stripos($lead["ad_url"], 'checkout') !== false) {

						$rec["event"] = 'checkout';

						// URL decode the ru portion first!!
						$rec["page_url"] = urldecode(explode("ru=", $lead["ad_url"])[1]);

						$query = "SELECT * FROM user_leads_basic WHERE (capture_trigger='pageload' OR capture_trigger='formload') AND (email=".db_sanitize($rec["email"]);
						if (strlen($rec["user_id"]) > 0) {
							$query .= " OR user_id=".$rec["user_id"];
						}
						$query .= ") AND capture_type='url' AND create_date < ".db_sanitize($lead["create_date"])." ORDER BY create_date DESC LIMIT 1;";
						$res_load_event = db_query($query);

						if (!isset($res_load_event[0])) {

							$rec["landing_url"] = "";
							$rec["utm_source"] = "";
							$rec["utm_campaign"] = "";
							$rec["utm_term"] = "";
							$rec["utm_medium"] = "";
							$rec["utm_content"] = "";
							$rec["utm_segment"] = "";

						}
						else {

		    				$load_event = $res_load_event[0];
		    				$rec["landing_url"] = $load_event["ad_url"];

		    				$rec["utm_source"] = $load_event["utm_source"];
		    				$rec["utm_campaign"] = $load_event["utm_campaign"];
		    				$rec["utm_term"] = $load_event["utm_term"];
		    				$rec["utm_medium"] = $load_event["utm_medium"];
		    				$rec["utm_content"] = $load_event["utm_content"];
		    				$rec["utm_segment"] = $load_event["utm_segment"];
		    				$rec["xuid"] = $load_event["xuid"] ?? $lead["xuid"] ?? '';

							if (empty($rec["__tr"]) && !empty($lead["__tr"])) {
								$rec["__tr"] = $load_event["__tr"];
							}

					    }

						$rec["gcl_id"] = $lead["gcl_id"];
						$rec["global_id_perm"] = $lead["global_id_perm"];
						$rec["global_id_session"] = $lead["global_id_session"];
						$rec["utm_numvisits"] = $lead["utm_numvisits"];

						if (empty($rec["__tr"])) {
							$rec["__tr"] = $lead["__tr"];
						}

					}
					else {
						continue;
					}

				}
				elseif (strcmp($lead["capture_trigger"], "cart") == 0) {

					$query = "SELECT * FROM user_leads_basic WHERE (capture_trigger='pageload' OR capture_trigger='formload') AND (email=".db_sanitize($rec["email"]);
					if (strlen($rec["user_id"]) > 0) {
						$query .= " OR user_id=".$rec["user_id"];
					}
					$query .= ") AND create_date<".db_sanitize($lead["create_date"])." ORDER BY create_date DESC LIMIT 1;";
					$res_load_event = db_query($query);

					$rec["page_url"] = $lead["ad_url"] ?? '';
					$rec["landing_url"] = $res_load_event[0]["ad_url"] ?? $lead["ad_url"] ?? '';

					$rec["utm_source"] = $res_load_event[0]["utm_source"] ?? $lead["utm_source"] ?? '';
					$rec["utm_campaign"] = $res_load_event[0]["utm_campaign"] ?? $lead["utm_campaign"] ?? '';
					$rec["utm_term"] = $res_load_event[0]["utm_term"] ?? $lead["utm_term"] ?? '';
					$rec["utm_medium"] = $res_load_event[0]["utm_medium"] ?? $lead["utm_medium"] ?? '';
					$rec["utm_content"] = $res_load_event[0]["utm_content"] ?? $lead["utm_content"] ?? '';
					$rec["utm_segment"] = $res_load_event[0]["utm_segment"] ?? $lead["utm_segment"] ?? '';

					$rec["utm_numvisits"] = $res_load_event[0]["utm_numvisits"] ?? $lead["utm_numvisits"] ?? '';
					$rec["gcl_id"] = $lead["gcl_id"] ?? '';
					$rec["global_id_perm"] = $lead["global_id_perm"] ?? '';
					$rec["global_id_session"] = $lead["global_id_session"] ?? '';
					$rec["xuid"] = $res_load_event[0]["xuid"] ?? $lead["xuid"] ?? '';

					if (empty($rec["__tr"])) {

						if (!empty($res_load_event[0]["__tr"])) {
							$rec["__tr"] = $res_load_event[0]["__tr"];
						}
						else {
							$rec["__tr"] = $lead["__tr"];
						}

					}

				}
				else if (strcmp($lead["capture_trigger"], "phoneupdate") == 0) {

					$reg = db_query("SELECT * FROM user_leads_basic_compiled WHERE event='reg' AND email=".db_sanitize($lead["email"])." LIMIT 1;");
					if (!empty($reg[0])) {
						$reg = $reg[0];
					}

					$rec["phone"] = $lead["phone"];

					$rec["utm_source"] = "";
					$rec["utm_campaign"] = "";
					$rec["utm_term"] = "";
					$rec["utm_medium"] = "";
					$rec["utm_content"] = "";
					$rec["utm_segment"] = "";

					$rec["utm_numvisits"] = "";
					$rec["gcl_id"] = "";
					$rec["global_id_perm"] = "";
					$rec["global_id_session"] = "";
					$rec["xuid"] = "";

					$rec["page_url"] = $reg["page_url"] ?? "";
					$rec["landing_url"] = "";

					$rec["referrer"] = $reg["referrer"] ?? "";

					activity_debug_start();
					activity_debug_log("phoneupdate => ".json_encode($rec));

				}
				else if (strcmp($lead["capture_trigger"], "reg.android") == 0) {

					$rec["utm_source"] = "";
					$rec["utm_campaign"] = "";
					$rec["utm_term"] = "";
					$rec["utm_medium"] = "";
					$rec["utm_content"] = "";
					$rec["utm_segment"] = "";

					$rec["utm_numvisits"] = "";
					$rec["gcl_id"] = "";
					$rec["global_id_perm"] = "";
					$rec["global_id_session"] = "";
					$rec["xuid"] = "";

					$rec["page_url"] = "";
					$rec["landing_url"] = "";

				}
				else if (strcmp($lead["capture_trigger"], "clickthrough") == 0) {

					if ($lead["ad_lp"] == "referral_email") {

						$rec["page_url"] =  $lead["ad_url"] ?? '';
						$rec["landing_url"] =  $lead["ad_url"] ?? '';

						$rec["utm_source"] =  $lead["utm_source"] ?? '';
						$rec["utm_campaign"] =  $lead["utm_campaign"] ?? '';
						$rec["utm_term"] =  $lead["utm_term"] ?? '';
						$rec["utm_medium"] =  $lead["utm_medium"] ?? '';
						$rec["utm_content"] =  $lead["utm_content"] ?? '';
						$rec["utm_segment"] =  $lead["utm_segment"] ?? '';

						$rec["utm_numvisits"] =  $lead["utm_numvisits"] ?? '';
						$rec["gcl_id"] = $lead["gcl_id"] ?? '';
						$rec["global_id_perm"] = $lead["global_id_perm"] ?? '';
						$rec["global_id_session"] = $lead["global_id_session"] ?? '';
						$rec["xuid"] = $lead["xuid"] ?? '';

						$rec["event"] = "referral";

					}

				}

				// Save the data before sanitizing it
				$data = $rec;
                                //commented during LS Dashboard JA-113 production issu- status not changed
				//$leads_arr[] = $data; 

				$leads_data[$rec["email"]] = $lead["create_date"];

				// IOT Edit : Overwrite utm source if coming from IOT pages
				$rec["utm_source"] = ((stripos($rec["page_url"], '/iot') !== false) ||
							(stripos($rec["landing_url"], '/iot') !== false) ||
							(stripos($rec["referer"], '/iot') !== false)) ? 'iot' : $rec["utm_source"];

				// Sanitize the data
				$rec["name"] = db_sanitize($rec["name"]);
				$rec["email"] = db_sanitize($rec["email"]);
				$rec["phone"] = db_sanitize($rec["phone"]);
				$rec["utm_source"] = db_sanitize($rec["utm_source"]);
				$rec["utm_campaign"] = db_sanitize($rec["utm_campaign"]);
				$rec["utm_term"] = db_sanitize($rec["utm_term"]);
				$rec["utm_medium"] = db_sanitize($rec["utm_medium"]);
				$rec["utm_content"] = db_sanitize($rec["utm_content"]);
				$rec["utm_segment"] = db_sanitize($rec["utm_segment"]);
				$rec["utm_numvisits"] = db_sanitize($rec["utm_numvisits"]);
				$rec["gcl_id"] = db_sanitize($rec["gcl_id"]);
				$rec["global_id_perm"] = db_sanitize($rec["global_id_perm"]);
				$rec["global_id_session"] = db_sanitize($rec["global_id_session"]);
				$rec["xuid"] = db_sanitize($rec["xuid"]);
				$rec["page_url"] = db_sanitize($rec["page_url"]);
				$rec["landing_url"] = db_sanitize($rec["landing_url"]);
				$rec["referer"] = db_sanitize($rec["referer"]);
				$rec["ip"] = db_sanitize($rec["ip"]);
				$rec["create_date"] = db_sanitize($rec["create_date"]);
				$rec["event"] = db_sanitize($rec["event"]);
				$rec["meta"] = db_sanitize($rec["meta"]);
				$rec["cookies"] = db_sanitize($rec["cookies"]);
				if (!empty($rec["__tr"])) {
					$rec["__tr"] = db_sanitize($rec["__tr"]);
				}
				else {
					$rec["__tr"] = "NULL";
				}

				$insert = "INSERT INTO user_leads_basic_compiled (".
								((strlen($rec["user_id"]) == 0) ? "" : "user_id,").
								"name,
								email,
								phone,
								utm_source,
								utm_campaign,
								utm_term,
								utm_medium,
								utm_content,
								utm_segment,
								utm_numvisits,
								gcl_id,
								global_id_perm,
								global_id_session,
								xuid,
								page_url,
								landing_url,
								referer,
								ip,
								create_date,
								event,
								meta,
								cookies,
								__tr
								)
							VALUES (".
								((strlen($rec["user_id"]) == 0) ? "" : $rec["user_id"].", ").
								$rec["name"].", ".
								$rec["email"].", ".
								$rec["phone"].", ".
								$rec["utm_source"].", ".
								$rec["utm_campaign"].", ".
								$rec["utm_term"].",".
								$rec["utm_medium"].", ".
								$rec["utm_content"].", ".
								$rec["utm_segment"].", ".
								$rec["utm_numvisits"].", ".
								$rec["gcl_id"].", ".
								$rec["global_id_perm"].", ".
								$rec["global_id_session"].", ".
								$rec["xuid"].", ".
								$rec["page_url"].", ".
								$rec["landing_url"].", ".
								$rec["referer"].", ".
								$rec["ip"].", ".
								$rec["create_date"].", ".
								$rec["event"].", ".
								$rec["meta"].", ".
								$rec["cookies"].", ".
								$rec["__tr"].");";
				$stat = db_exec($insert);
                                
                                $compiledLeadId = db_get_last_insert_id();
				// Save the last lead_id
				$last_leads_id = $lead["lead_id"];
				setting_set("leads_basic_capture_last", $last_leads_id);
                                
                                
                                //JA-113 changes-prod issue
                                $data['compiledLeadId'] = $compiledLeadId;
                                $leads_arr[] = $data;
			}
			// Save the last lead_id
			$last_leads_id = $res_leads[count($res_leads) - 1]["lead_id"];
			setting_set("leads_basic_capture_last", $last_leads_id);
		}
		else
		{
			$rec["name"] = $params["name"];
			$rec["email"] = $params["email"];
			$rec["phone"] = $params["phone"];
			$rec["utm_source"] = "";
			$rec["utm_campaign"] = "";
			$rec["utm_term"] = "";
			$rec["utm_medium"] = "";
			$rec["utm_content"] = "";
			$rec["utm_segment"] = "";
			$rec["utm_numvisits"] = "";
			$rec["gcl_id"] = "";
			$rec["global_id_perm"] = "";
			$rec["global_id_session"] = "";
			$rec["xuid"] = "";
			$rec["page_url"] = "";
			$rec["landing_url"] = "";
			$rec["referer"] = "";
			$rec["ip"] = "";
			$rec["create_date"] = "";
			$rec["event"] = $params["event"];
			$rec["source_1"] = "";
			$rec["source_2"] = "";
			$rec["meta"] = "";

			$data = $rec;
			$leads_arr[] = $data;
		}

		$compilation_failure = false;

		// Handle the hook
		if (count($leads_arr) > 0) {
			handle("leads_basic_compile__", $leads_arr);
		}
	}

	function leads_basic_compile_failure()
	{
		GLOBAL $compilation_failure;
		if (!$compilation_failure) return;

		ob_start();
		var_dump(error_get_last());
		$dump = ob_get_contents();
		ob_clean();

		//activity_debug_start();
		//activity_debug_log($dump);
	}

	// This function is used to extract variables from an UTM Cookie
	function leads_cookie_utm_extract_var($cookieval, $var) {

		$pos = strpos($cookieval, $var);
		if ($pos === false) return "";

		$strsub = substr($cookieval, $pos + strlen($var) + 1);
		$pos = strpos($strsub, "|");
		if ($pos === false) $pos = strlen($strsub);

		return substr($strsub, 0, $pos);

	}

	function leads_basic_compiled_save($lead)
	{
		$lead["name"] = db_sanitize($lead["name"]);
		$lead["email"] = db_sanitize($lead["email"]);
		$lead["phone"] = db_sanitize($lead["phone"]);
		$lead["utm_source"] = db_sanitize($lead["utm_source"]);
		$lead["utm_campaign"] = db_sanitize($lead["utm_campaign"]);
		$lead["utm_term"] = db_sanitize($lead["utm_term"]);
		$lead["utm_medium"] = db_sanitize($lead["utm_medium"]);
		$lead["utm_content"] = db_sanitize($lead["utm_content"]);
		$lead["utm_segment"] = db_sanitize($lead["utm_segment"]);
		$lead["utm_numvisits"] = db_sanitize($lead["utm_numvisits"]);
		$lead["gcl_id"] = db_sanitize($lead["gcl_id"]);
		$lead["global_id_perm"] = db_sanitize($lead["global_id_perm"]);
		$lead["global_id_session"] = db_sanitize($lead["global_id_session"]);
		$lead["page_url"] = db_sanitize($lead["page_url"]);
		$lead["landing_url"] = db_sanitize($lead["landing_url"]);
		$lead["referer"] = db_sanitize($lead["referer"]);
		$lead["ip"] = db_sanitize($lead["ip"]);
		$lead["create_date"] = db_sanitize($lead["create_date"]);
		$lead["event"] = db_sanitize($lead["event"]);

		$query = "INSERT INTO user_leads_basic_compiled (name, email, phone, utm_source, utm_campaign, utm_term, utm_medium, utm_content, utm_segment, utm_numvisits, gcl_id, global_id_perm, global_id_session, page_url, landing_url, referer, ip, create_date, event) VALUES (".$lead["name"].",".$lead["email"].",".$lead["phone"].",".$lead["utm_source"].",".$lead["utm_campaign"].",".$lead["utm_term"].",".$lead["utm_medium"].",".$lead["utm_content"].",".$lead["utm_segment"].",".$lead["utm_numvisits"].",".$lead["gcl_id"].",".$lead["global_id_perm"].",".$lead["global_id_session"].",".$lead["page_url"].",".$lead["landing_url"].",".$lead["referer"].",".$lead["ip"].",".$lead["create_date"].",".$lead["event"].");";

		//activity_debug_start();
		//activity_debug_log(db_sanitize($query));

		//db_exec($query);
	}



?>
