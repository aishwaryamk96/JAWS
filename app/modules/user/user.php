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
		header('Location: https://www.jigsawacademy.com');
		die();
	}

	// This will contain all errors related to users
	$GLOBALS["jaws_user"]["error"] = "";

	// This will return the default role for a newly registered user
	function user_get_default_role() {

		$res = db_query("SELECT value FROM system_setting WHERE setting='user.role.default' LIMIT 1;");

		if (!isset($res[0])) {
			$GLOBALS["jaws_user"]["error"] .= "\nDefault role for new user is not set (user.role.default = NULL). Using role_id '1'.";
			return "1";
		}

		else {
			$role_id = db_sanitize($res[0]["value"]);
			$role = db_query("SELECT * FROM system_role WHERE role_id=".$role_id." LIMIT 1;");

			if (!isset($role[0])) {
				$GLOBALS["jaws_user"]["error"] .= "\nDefault role for new user is not valid (user.role.default = ".$role_id."). Using role_id '1'.";
				return "1";
			}
			else return $res[0]["value"];
		}

	}

	// This function returns a user by his user_id
	function user_get_by_id($user_id) {

		$res = db_query("SELECT * FROM user WHERE user_id=".$user_id." LIMIT 1;");
		if (!isset($res[0])) return false;
		else return $res[0];

	}

	// This function returns a user by his email ID in any of the fields - comm,fb,gp,li and optionally partial accounts as well
	function user_get_by_email($email, $allow_partial = false) {

		// Check
		if (strlen($email) == 0) return false;

		//Prep
		$res;
		$email = db_sanitize($email);

		if ($allow_partial) {
			// First pref to partial accounts
			$res = db_query("SELECT * FROM user WHERE (email=".$email." OR email_2=".$email." OR soc_fb=".$email." OR soc_gp=".$email." OR soc_li=".$email.") AND (status='pending') LIMIT 1;");

			// Second pref to normal accounts
			if (!isset($res[0])) {
				$res = db_query("SELECT * FROM user WHERE email=".$email." OR email_2=".$email." OR soc_fb=".$email." OR soc_gp=".$email." OR soc_li=".$email." LIMIT 1;");
				if (!isset($res[0])) return false;
				else return $res[0];
			}
			else return $res[0];
		}

		else {
			$res = db_query("SELECT * FROM user WHERE (email=".$email." OR email_2=".$email." OR soc_fb=".$email." OR soc_gp=".$email." OR soc_li=".$email.") AND (status='active') LIMIT 1;");
			if (!isset($res[0])) return false;
			else return $res[0];
		}

    }
    
    function user_get_by_email_all($email) {

        // Check
        if (empty($email)) {
            return false;
        }

		//Prep
        $email = db_sanitize($email);

        $res = db_query("SELECT * FROM user WHERE (email=".$email." OR email_2=".$email." OR soc_fb=".$email." OR soc_gp=".$email." OR soc_li=".$email.") LIMIT 1;");

        if (!isset($res[0])) {
            return false;
        }
        else {
            return $res[0];
        }

    }

	// This function returns a user by his soc email ID
	function user_get_by_soc($soc_email, $soc) {

		$soc_email = db_sanitize($soc_email);
		$soc = "soc_".$soc;

		$res = db_query("SELECT * FROM user WHERE ".$soc."=".$soc_email." LIMIT 1;");
		if (!isset($res[0])) return false;
		else return $res[0];

	}

	// This function returns a user by his web_id
	function user_get_by_webid($webid) {

		$webid = db_sanitize($webid);
		$res = db_query("SELECT * FROM user WHERE web_id=".$webid." LIMIT 1;");
		if (!isset($res[0])) return false;
		else return $res[0];

	}

	// This will tell if an user has been surveyed and get the data
	function user_get_meta($user_id) {

		$res = db_query("SELECT * FROM user_meta WHERE user_id=".$user_id." LIMIT 1;");
		if (!isset($res[0])) return false;
		else return $res[0];

	}

	// This function will create a new user
	function user_create($email, $pass, $name, $phone="", $autologin = false) {

		if (strlen($email) == 0) return false;

		if (user_get_by_email($email) === false)  {

			// prep web_id
			$dt = getdate();
			$web_id_str = $dt["seconds"].$dt["minutes"].$dt["hours"].$dt["mday"].$dt["weekday"].$dt["mon"].$dt["year"].$dt[0].$email;
			$web_id = db_sanitize(hash("sha256", $web_id_str));

			// prep role
			$role = db_sanitize(user_get_default_role().";");

			// sanitize the remaining
			$email = db_sanitize($email);
			$pass = db_sanitize(hash('md5', $pass));
			$name = db_sanitize($name);
			$phone = db_sanitize($phone);

			// create main record
			db_exec("INSERT INTO user (email, pass, name, phone, web_id, roles, status) VALUES (".$email.",".$pass.",".$name.",".$phone.",".$web_id.",".$role.", 'active');");

			// get the inserted auto-incremented user_id
			$user_id = db_get_last_insert_id();

			// create meta record
			db_exec("INSERT INTO user_meta (user_id, reg_date) VALUES (".$user_id.",".db_sanitize(date("Y-m-d H:i:s")).");");

			// Assoc partial account
			$partial = user_get_by_email($email, true);
			if ($partial !== false) {
				user_attach_partial($user_id, $partial["user_id"]);
				$web_id = $partial["web_id"];
			}

			// login this user
			if ($autologin) {
				load_module("auth");
				auth_session_login_forced($email);
			}

			// done
			$user["user_id"] = $user_id;
			$user["email"] = trim($email, "'");
			$user["pass"] = trim($pass, "'");
			$user["name"] = trim($name, "'");
			$user["phone"] = trim($phone, "'");
			$user["web_id"] = trim($web_id, "'");
			$user["roles"] = trim($role, "'");
			$user["status"] = 'active';

			// This will associate all leads that were captured with this email ID to this newly created user
			load_module("leads");
			leads_basic_assoc_user_by_email($user["email"]);

			// All done
			return $user;

		}

		else return false;

	}

	// This function will create a new 'partially complete' user with partial information available
	function user_create_partial($email, $name = "") {

		if (strlen($email) == 0) return false;

		if (user_get_by_email($email, true) === false) {

			// prep web_id
			$dt = getdate();
			$web_id_str = $dt["seconds"].$dt["minutes"].$dt["hours"].$dt["mday"].$dt["weekday"].$dt["mon"].$dt["year"].$dt[0].$email;
			$web_id = db_sanitize(hash("sha256", $web_id_str));

			// prep role
			$role = db_sanitize(user_get_default_role().";");

			// sanitize the remaining
			$email = db_sanitize($email);
			$name = db_sanitize($name);

			// create main record
			db_exec("INSERT INTO user (email, name, web_id, roles, status) VALUES (".$email.",".$name.",".$web_id.",".$role.", 'pending');");

			// get the inserted auto-incremented user_id
			$user_id = db_get_last_insert_id();

			// create meta record
			db_exec("INSERT INTO user_meta (user_id, reg_date) VALUES (".$user_id.",".db_sanitize(date("Y-m-d H:i:s")).");");

			// This will associate all leads that were captured with this email ID to this newly created user
			load_module("leads");
			leads_basic_assoc_user_by_email(trim($email,"'"));

			// done
			return array("user_id" => $user_id, "name" => trim($name, "'"), "web_id" => trim($web_id,"'"), "roles" => trim($role, "'"), "email" => trim($email,"'"), "status" => "pending");

		}
		else return false;

	}

	// This function will update given fields in the user table
	function user_update($user_id, $fields_arr) {

		if (user_get_by_id($user_id) === false) return false;

		$query_str = "UPDATE user SET ";
		$count = 0;

		foreach($fields_arr as $field => $value) {
			if(strcmp($field, 'pass') == 0) $value = hash('md5', $value);
			$query_str .= ($count > 0 ? ", " : "").$field."=".db_sanitize($value);
			$count++;
		}

		$query_str .= " WHERE user_id=".$user_id.";";
		db_exec($query_str);

		// This will associate all leads that were captured with given email IDs to this updated user
		load_module("leads");
		if ((isset($fields_arr["email"])) && (strlen($fields_arr["email"]) > 0)) leads_basic_assoc_user_by_email($fields_arr["email"]);
		if ((isset($fields_arr["soc_fb"])) && (strlen($fields_arr["soc_fb"]) > 0)) leads_basic_assoc_user_by_email($fields_arr["soc_fb"]);
		if ((isset($fields_arr["soc_gp"])) && (strlen($fields_arr["soc_gp"]) > 0)) leads_basic_assoc_user_by_email($fields_arr["soc_gp"]);
		if ((isset($fields_arr["soc_li"])) && (strlen($fields_arr["soc_li"]) > 0)) leads_basic_assoc_user_by_email($fields_arr["soc_li"]);

		// All done
		return true;

	}

	// This function will update given fields in the user_meta table
	function user_update_meta($user_id, $fields_arr) {

		if (user_get_by_id($user_id) === false) return false;

		$query_str = "UPDATE user_meta SET ";
		$count = 0;

		foreach($fields_arr as $field => $value) {
			$query_str .= ($count > 0 ? ", " : "").$field."=".db_sanitize($value);
			$count++;
		}

		$query_str .= " WHERE user_id=".$user_id.";";
		db_exec($query_str);
		return true;

	}

	// This will attach a partial (pending) user account to a full user account by shifting all belongings of the partial account
	// Note: This will overwrite the full user's communication email with the partial account's comm email
	function user_attach_partial($full_user_id, $partial_user_id, $partialcheck = true) {

		// Check
		if (strcmp($full_user_id, $partial_user_id) == 0) return true;

		// Get the partial user
		$user_partial = user_get_by_id($partial_user_id);
		$user_full = user_get_by_id($full_user_id);

		// Checks
		if ($user_partial === false) return false;
		if ($user_full === false) return false;
		if ((strcmp($user_partial["status"],"pending") != 0) && ($partialcheck)) return false;

		// Shift Payments
		db_exec("UPDATE payment SET user_id=".$full_user_id." WHERE user_id=".$partial_user_id.";");
		db_exec("UPDATE payment_instl SET user_id=".$full_user_id." WHERE user_id=".$partial_user_id.";");
		db_exec("UPDATE payment_link SET user_id=".$full_user_id." WHERE user_id=".$partial_user_id.";");

		// Shift Subscription
		db_exec("UPDATE subs SET user_id=".$full_user_id." WHERE user_id=".$partial_user_id.";");

		// Shift package
		db_exec("UPDATE package SET user_id=".$full_user_id." WHERE user_id=".$partial_user_id.";");

		// Shift Msgs
		db_exec("UPDATE user_msg SET from_id=".$full_user_id." WHERE from_id=".$partial_user_id." AND from_type='user';");
		db_exec("UPDATE user_msg SET to_id=".$full_user_id." WHERE to_id=".$partial_user_id." AND to_type='user';");

		// Shift Logs
		db_exec("UPDATE system_activity SET entity_id=".$full_user_id." WHERE entity_id=".$partial_user_id." AND entity_type='user_id';");
		db_exec("UPDATE system_activity SET context_id=".$full_user_id." WHERE context_id=".$partial_user_id." AND context_type='user_id';");

		// Shift Persistence
		db_exec("UPDATE system_persistence_map SET native_id=".db_sanitize($full_user_id)." WHERE native_id=".db_sanitize($partial_user_id)." AND entity_type='user';");

		// Shift Enrollments
		db_exec("UPDATE user_enrollment SET user_id=".$full_user_id." WHERE user_id=".$partial_user_id.";");

		// Shift Leads
		db_exec("UPDATE user_leads_basic SET user_id=".$full_user_id." WHERE user_id=".$partial_user_id.";");

		// Shift Webinar Reg
		db_exec("UPDATE webinar_reg SET user_id=".$full_user_id." WHERE user_id=".$partial_user_id.";");

		// Shift User
		user_update($full_user_id, array(
					"email" => $user_partial["email"],
					"account_type" => $user_partial["account_type"],
					"web_id" => $user_partial["web_id"]
		));

		// Remove Partial User
		db_exec("DELETE FROM user_meta WHERE user_id=".$partial_user_id.";");
		db_exec("DELETE FROM user WHERE user_id=".$partial_user_id.";");

		// Log
		activity_create("ignore", "user.merge", "success", "user_id_from", $partial_user_id, "user_id_to", $full_user_id, "User Accounts Merged", "logged");

		return true;

	}

?>