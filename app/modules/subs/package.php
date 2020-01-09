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

    load_module("user");

	function package_create($package_info) {

		// Check if a user with this email ID is present
		$user = user_get_by_email($package_info["email"]);
		$package_info["user_id"] = "";
		if ($user) {
			$package_info["user_id"] = $user["user_id"];
		}

		if (!isset($package_info["bundle_id"])) {
			$package_info["bundle_id"] = "NULL";
		}
		if (empty($package_info["batch_id"])) {
			$package_info["batch_id"] = "NULL";
		}

		// JSONize the instl element
		$package_info["instl"] = json_encode($package_info["instl"]);

		// Set create_date
		$date = new DateTime("now");
		$package_info["create_date"] = $date->format("Y-m-d H:i:s");
		$package_info["creator_comment"] = json_encode($package_info["creator_comment"]);

		// Serialize the package
		$serialized = array(
			"data_courses_actual" => $package_info["data_courses_actual"],
			"data_courses_discount" => $package_info["data_courses_discount"],
			"data_payment_discount" => $package_info["data_payment_discount"],
			"data_tax_amount" => $package_info["data_tax_amount"],
			"data_discount_amount" => $package_info["data_discount_amount"],
			"data_offered_amount" => $package_info["data_offered_amount"],
			"data_instalment_amount" => $package_info["data_instalment_amount"],
			"data_net_payable" => $package_info["data_net_payable"],
			"data_edit_offered_price" => $package_info["data_edit_offered_price"],
			"data_edit_discount_amount" => $package_info["data_edit_discount_amount"],
			"data_edit_discount_percent" => $package_info["data_edit_discount_percent"],
			"data_edit_tax_amount" => $package_info["data_edit_tax_amount"],
			"data_bundle_price" => $package_info["data_bundle_price"],
			"data_bundle_combo" => $package_info["data_bundle_combo"],
			"data_instalment_fees_inr" => $package_info["data_instalment_fees_inr"],
			"data_instalment_fees_usd" => $package_info["data_instalment_fees_usd"],
			"data_kform_version" => $package_info["data_kform_version"],
			"data_bundle_unselect" => $package_info["data_bundle_unselect"],
            "course_start_date" => $package_info["course_start_date"] ?? false,
            "user_state" => $package_info["data_user_state"] ?? ""
		);
		$package_info["serialized"] = json_encode($serialized);

		// Backup the data before sanitizing
		$package = $package_info;

		// Sanitize the data
		$package_info["email"] = db_sanitize($package_info["email"]);
		$package_info["name"] = db_sanitize($package_info["name"]);
		$package_info["phone"] = db_sanitize($package_info["phone"]);
		$package_info["combo"] = db_sanitize($package_info["combo"]);
		$package_info["combo_free"] = db_sanitize($package_info["combo_free"]);
		$package_info["currency"] = db_sanitize($package_info["currency"]);
		$package_info["instl"] = db_sanitize($package_info["instl"]);
		$package_info["pay_mode"] = db_sanitize($package_info["pay_mode"]);
		$package_info["create_date"] = db_sanitize($package_info["create_date"]);
		$package_info["creator_type"] = db_sanitize($package_info["creator_type"]);
		$package_info["creator_comment"] = db_sanitize($package_info["creator_comment"]);
		$package_info["approval_require_comment"] = db_sanitize($package_info["approval_require_comment"]);
		$package_info["status_approval_sm"] = db_sanitize($package_info["status_approval_sm"]);
		$package_info["status_approval_pm"] = db_sanitize($package_info["status_approval_pm"]);
		$package_info["approver_comment_sm"] = db_sanitize($package_info["approver_comment_sm"]);
		$package_info["approver_comment_pm"] = db_sanitize($package_info["approver_comment_pm"]);
		$package_info["status"] = db_sanitize($package_info["status"]);
        $package_info["serialized"] = db_sanitize($package_info["serialized"]);
        $package_info["receipt_type"] = db_sanitize($package_info["receipt_type"] ?? "retail");

		$query = "INSERT INTO package (".(strlen($package_info["user_id"]) > 0 ? "user_id," : "")." email, name, phone, combo, combo_free, bundle_id, batch_id, currency, sum_basic, sum_offered, sum_total, tax, instl_fees, instl_total, instl, pay_mode, create_date, creator_type, creator_id, creator_comment, approval_require_comment, require_approval_sm, require_approval_pm, status_approval_sm, status_approval_pm, approver_comment_sm, approver_comment_pm, status, serialized, receipt_type) VALUES (".(strlen($package_info["user_id"]) > 0 ? $package_info["user_id"]."," : "").$package_info["email"].",".$package_info["name"].",".$package_info["phone"].",".$package_info["combo"].",".$package_info["combo_free"].",".$package_info["bundle_id"].",".$package_info["batch_id"].",".$package_info["currency"].",".$package_info["sum_basic"].",".$package_info["sum_offered"].",".$package_info["sum_total"].",".$package_info["tax"].",".$package_info["instl_fees"].",".$package_info["instl_total"].",".$package_info["instl"].",".$package_info["pay_mode"].",".$package_info["create_date"].",".$package_info["creator_type"].",".$package_info["creator_id"].",".$package_info["creator_comment"].",".$package_info["approval_require_comment"].",".$package_info["require_approval_sm"].",".$package_info["require_approval_pm"].",".$package_info["status_approval_sm"].",".$package_info["status_approval_pm"].",".$package_info["approver_comment_sm"].",".$package_info["approver_comment_pm"].",".$package_info["status"].",".$package_info["serialized"].", ".$package_info["receipt_type"].");";

		// Insert the record
		db_exec($query);
		$package_id = db_get_last_insert_id();

		$package["package_id"] = $package_id;

		// If the package status is approved, execute the package
		if (strcmp($package["status"], "approved") == 0) {
			package_exec($package_id);
		}
		else if (strcmp($package["status"], "rejected") == 0) {
			package_log($package_id);
		}

		// Handle the hook, if it is not in test mode
		if ($package["test"] != "1") {
			handle("package_create__", $package);
		}
		return $package;

	}

	function package_exec($package_id, $send_mail = true) {

		package_log($package_id);

		$res_package = db_query("SELECT * FROM package WHERE package_id=".$package_id.";");
		if (!isset($res_package[0])) {
			return false;
		}

		$res_package = $res_package[0];
		$subs_info["package_id"] = $package_id;
		$subs_info["combo"] = $res_package["combo"];
		$subs_info["combo_free"] = $res_package["combo_free"];
		//$subs_info["agent_id"] = $res_package["creator_id"];
		$subs_info["bundle_id"] = $res_package["bundle_id"];
		$subs_info["batch_id"] = $res_package["batch_id"];

        $serialized = json_decode($res_package["serialized"], true);
        $user_state = "";
		if (!empty($serialized["course_start_date"])) {

			$start_date = date_create_from_format("d/m/Y", $serialized["course_start_date"]);
            $subs_info["start_date"] = $start_date->format("Y-m-d H:i:s");

        }
        $subs_info["user_state"] = $serialized["user_state"] ?? "";

		$pay_info["status"] = "pending";
		if (!empty($res_package["pay_mode"]) && strcmp($res_package["pay_mode"], "online") != 0) {
			$pay_info["status"] = "paid";
		}
		$pay_info["currency"] = $res_package["currency"];
		$pay_info["sum_basic"] = $res_package["sum_basic"];
		$pay_info["sum_total"] = $res_package["sum_total"];
		$pay_info["instl_total"] = $res_package["instl_total"];
		$pay_info["instl"] = json_decode($res_package["instl"], true);
        $pay_info["agent_id"] = $res_package["creator_id"];
        $pay_info["receipt_type"] = $res_package["receipt_type"];

		$package_meta = json_decode($res_package["serialized"], true);
		$pay_info["sum_offered"] = $package_meta["data_offered_amount"];
		$pay_info["tax_amount"] = $package_meta["data_tax_amount"];

		// prep Instl creator
		if (isset($pay_info["agent_id"]) && (strlen($pay_info["agent_id"]) > 0)) {

			$count = 1;
			while ($count <= intval($pay_info["instl_total"])) {

				$pay_info["instl"][$count]["create_entity_type"] = "user";
				$pay_info["instl"][$count]["create_entity_id"] = $pay_info["agent_id"];
				$pay_info["instl"][$count]["pay_mode"] = $res_package["pay_mode"];
				$count ++;

			}

		}

		// Removed The following debug mode code -
		//$GLOBALS['jaws_exec_live'] = false;
		subscribe($res_package["email"], $subs_info, $pay_info, $send_mail, true, $res_package["name"], $res_package["phone"]);
		//$GLOBALS['jaws_exec_live'] = true;

		db_exec("UPDATE package SET status='executed' WHERE package_id=".$package_id.";");

	}

	function package_update($package_id, $package_info) {

		$res_package = db_query("SELECT * FROM package WHERE package_id=".$package_id.";");
		if (!isset($res_package[0])) {
			return false;
		}

		$query = "UPDATE package SET ";
		if (isset($package_info["user_id"])) $query .= "user_id=".$package_info["user_id"].",";
		if (isset($package_info["email"])) $query .= "email=".db_sanitize($package_info["email"]).",";
		if (isset($package_info["name"])) $query .= "name=".db_sanitize($package_info["name"]).",";
		if (isset($package_info["phone"])) $query .= "phone=".db_sanitize($package_info["phone"]).",";
		if (isset($package_info["combo"])) $query .= "combo=".db_sanitize($package_info["combo"]).",";
		if (isset($package_info["combo_free"])) $query .= "combo_free=".db_sanitize($package_info["combo_free"]).",";
		if (isset($package_info["bundle_id"])) $query .= "bundle_id=".$package_info["bundle_id"].",";
		if (isset($package_info["batch_id"])) $query .= "batch_id=".$package_info["batch_id"].",";
		if (isset($package_info["currency"])) $query .= "currency=".db_sanitize($package_info["currency"]).",";
		if (isset($package_info["sum_basic"])) $query .= "sum_basic=".$package_info["sum_basic"].",";
		if (isset($package_info["sum_offered"])) $query .= "sum_offered=".$package_info["sum_offered"].",";
		if (isset($package_info["sum_total"])) $query .= "sum_total=".$package_info["sum_total"].",";
		if (isset($package_info["tax"])) $query .= "tax=".$package_info["tax"].",";
		if (isset($package_info["instl_fees"])) $query .= "instl_fees=".$package_info["instl_fees"].",";
		if (isset($package_info["instl_total"])) $query .= "instl_total=".$package_info["instl_total"].",";
		if (isset($package_info["instl"])) $query .= "instl=".db_sanitize(json_encode($package_info["instl"])).",";
		if (isset($package_info["pay_mode"])) $query .= "pay_mode=".db_sanitize($package_info["pay_mode"]).",";
		if (isset($package_info["creator_type"])) $query .= "creator_type=".db_sanitize($package_info["creator_type"]).",";
		if (isset($package_info["creator_id"])) $query .= "creator_id=".$package_info["creator_id"].",";
		if (isset($package_info["creator_comment"])) $query .= "creator_comment=".db_sanitize(json_encode($package_info["creator_comment"])).",";
		if (isset($package_info["approval_require_comment"])) $query .= "approval_require_comment=".db_sanitize($package_info["approval_require_comment"]).",";
		if (isset($package_info["require_approval_sm"])) $query .= "require_approval_sm=".$package_info["require_approval_sm"].",";
		if (isset($package_info["require_approval_pm"])) $query .= "require_approval_pm=".$package_info["require_approval_pm"].",";
		// If any of the approvers rejects the package, the package is rejected altogether
		if (isset($package_info["status_approval_sm"])) {

			if (strcmp($package_info["status_approval_sm"], "rejected") == 0) {
				$package_info["status"] = "rejected";
			}
			$query .= "status_approval_sm=".db_sanitize($package_info["status_approval_sm"]).",";

		}
		if (isset($package_info["status_approval_pm"])) {

			if (strcmp($package_info["status_approval_pm"], "rejected") == 0) {
				$package_info["status"] = "rejected";
			}
			$query .= "status_approval_pm=".db_sanitize($package_info["status_approval_pm"]).",";

		}
		if (isset($package_info["approver_sm_id"])) $query .= "approver_sm_id=".$package_info["approver_sm_id"].",";
		if (isset($package_info["approver_pm_id"])) $query .= "approver_pm_id=".$package_info["approver_pm_id"].",";
		if (isset($package_info["approver_comment_sm"])) $query .= "approver_comment_sm=".db_sanitize($package_info["approver_comment_sm"]).",";
		if (isset($package_info["approver_comment_pm"])) $query .= "approver_comment_pm=".db_sanitize($package_info["approver_comment_pm"]).",";
		if (isset($package_info["status"])) $query .= "status=".db_sanitize($package_info["status"]).",";
		if (isset($package_info["data_courses_actual"])) {

			$serialized = array(
                "data_courses_actual" => $package_info["data_courses_actual"],
                "data_courses_discount" => $package_info["data_courses_discount"],
                "data_payment_discount" => $package_info["data_payment_discount"],
                "data_tax_amount" => $package_info["data_tax_amount"],
                "data_discount_amount" => $package_info["data_discount_amount"],
                "data_offered_amount" => $package_info["data_offered_amount"],
                "data_instalment_amount" => $package_info["data_instalment_amount"],
                "data_net_payable" => $package_info["data_net_payable"],
                "data_edit_offered_price" => $package_info["data_edit_offered_price"],
                "data_edit_discount_amount" => $package_info["data_edit_discount_amount"],
                "data_edit_discount_percent" => $package_info["data_edit_discount_percent"],
                "data_edit_tax_amount" => $package_info["data_edit_tax_amount"],
                "data_bundle_price" => $package_info["data_bundle_price"],
                "data_bundle_combo" => $package_info["data_bundle_combo"],
                "data_instalment_fees_inr" => $package_info["data_instalment_fees_inr"],
                "data_instalment_fees_usd" => $package_info["data_instalment_fees_usd"],
                "data_kform_version" => $package_info["data_kform_version"],
                "data_bundle_unselect" => $package_info["data_bundle_unselect"],
                "user_state" => $package_info["data_user_state"] ?? ""
            );
			$package_info["serialized"] = json_encode($serialized);

		}
        if (isset($package_info["serialized"])) {
            $query .= "serialized=".db_sanitize($package_info["serialized"]).",";
        }
        if (isset($package_info["receipt_type"])) {
            $query .= "receipt_type=".db_sanitize($package_info["receipt_type"]).",";
        }

		$query = substr($query, 0, -1);
		$query .= " WHERE package_id=".$package_id.";";
		db_exec($query);

		// Handle the hook
		$res_package = db_query("SELECT * FROM package WHERE package_id=".$package_id.";");
		$res_package = $res_package[0];

		if (strcmp($res_package["status"], "approved") != 0 && strcmp($res_package["status"], "rejected") != 0 && strcmp($res_package["status"], "executed") != 0) {

			if (strcmp($res_package["status_approval_sm"], "approved") == 0 && strcmp($res_package["status_approval_pm"], "approved") == 0) {
				$res_package["status"] = "approved";
			}
			else if (strcmp($res_package["status_approval_sm"], "rejected") == 0 || strcmp($res_package["status_approval_pm"], "rejected") == 0) {
				$res_package["status"] = "rejected";
			}

			db_exec("UPDATE package SET status=".db_sanitize($res_package["status"])." WHERE package_id=".$res_package["package_id"].";");

		}

		// Set the package status as executed before sending the data to the hook
		if (strcmp($res_package["status"], "approved") == 0) {

			package_exec($res_package["package_id"]);
			$res_package["status"] = "executed";

		}
		else if (strcmp($res_package["status"], "rejected") == 0) {
			package_log($package_id);
		}
		// Handle the hook, if it is not in test mode
		if ($package_info["test"] != "1") {
			handle("package_update__", $res_package);
		}

		return res_package;

	}

	function package_get($package_id) {

		$res_package = db_query("SELECT * FROM package WHERE package_id=".$package_id.";");
		if (!isset($res_package[0])) {
			return false;
		}
		$res_package = $res_package[0];
		return $res_package;

	}

	function package_get_serialized($package_id) {

		$res_package = db_query("SELECT serialized FROM package WHERE package_id=".$package_id.";");
		if (!isset($res_package[0])) {
			return false;
		}
		$res_package = $res_package[0];
		return $res_package["serialized"];

	}

	function package_require_approval_check($package_info) {

		load_library("setting");

		$status_messages = array();

		$user_id = $package_info["creator_id"];

		// Discount %age criterion
		$discount_max = user_content_get($user_id, "discount_max");
		if ($discount_max < intval($package_info["data_payment_discount"])) {
			$status_messages["data_payment_discount"] = "Discount ".$package_info["data_payment_discount"]."% exceeds the allowed amount, ".$discount_max."%. Package will require approval.";
		}

		// Pay mode criterion
		if (strlen($package_info["pay_mode"], "online") != 0) {
			$status_messages["pay_mode"] = "All payment modes apart from online payments will require approval. Payment mode ".$package_info["pay_mode"].".";
		}

		// Discount amount criterion
		if ((strlen($package_info["data_edit_offered_price"]) > 0) && ($package_info["sum_basic"] != $package_info["data_edit_offered_price"])) {
			$status_messages["sum_offered"] = "Any discount will require approval";
		}

		// Instalment amount criterion
		if (intval($package_info["instl_total"]) > 1) {

			$instl_sum = intval($package_info["sum_offered"]) / intval($package_info["instl_total"]) + json_decode(setting_get("payment.instl.fee"), true)[$package_info["currency"]];
			$flag = false;
			foreach ($package_info["instl"] as $instl) {

				if ($instl["sum"] != $instl_sum) {
					$flag = true;
				}
				if ($instl["due_days"] != 30) {
					$flag = true;
				}

			}
			if ($flag) {
				$status_messages["instl"] = "Modified instalment sum or due days will require approval";
			}

		}

		// Instalment count criterion
		$instl_criteria = json_decode(setting_get("payment.instl.criteria"), true);
		$instl_criteria = $instl_criteria[$package_info["currency"]];
		for ($i = 1; $i <= count($instl_criteria); $i++) {

			if ($package_info["sum_offered"] >= $instl_criteria[$i]) {

				if (isset($instl_criteria[$i + 1]) && $package_info["sum_offered"] < $instl_criteria[$i + 1]) {

					if ($package_info["instl_total"] > $i) {
						$status_messages["instl_total"] = "Number of installments exceeds the installment criteria. Package will require approval.";
					}

				}
				else if (!isset($instl_criteria[$i + 1])) {
					if ($package_info["instl_total"] > $i) {
						$status_messages["instl_total"] = "Number of installments exceeds the installment criteria. Package will require approval.";
					}
				}

			}

		}

		// Combo free criteria
		if (!empty($package_info["combo_free"])) {
			$status_messages["combo_free"] = "Complimentary courses will require approval";
		}

		if (count($status_messages) > 0) {
			return $status_messages;
		}
		return false;

	}

	// Fetched packages for AForm
	function package_fetch($start = 0, $limit = 10, $email = "") {

		// approvd by sales, pending from pm
		// pending by sales
		// approved - last 2 days
		// rejected - last 2 days

		if (strlen($email) == 0) {
			$query = "SELECT * FROM package WHERE (status_approval_sm='approved' AND status_approval_pm='pending') OR (status_approval_sm='pending') OR ((status='approved' OR status='executed') AND DATEDIFF(NOW(), create_date) < 2) OR (status='rejected' AND DATEDIFF(NOW(), create_date) < 2) ORDER BY create_date DESC LIMIT ".$start.", ".$limit.";";
		}
		else {
			$query = "SELECT * FROM package WHERE email=".db_sanitize($email)." ORDER BY create_date DESC LIMIT ".$start.", ".$limit.";";
		}

		return db_query($query);

	}

	function package_fetch_count($email = "") {

		if (strlen($email) == 0) {
			$query = "SELECT COUNT(*) FROM package WHERE (status_approval_sm='approved' AND status_approval_pm='pending') OR (status_approval_sm='pending') OR ((status='approved' OR status='executed') AND DATEDIFF(NOW(), create_date) < 2) OR (status='rejected' AND DATEDIFF(NOW(), create_date) < 2);";
		}
		else {
			$query = "SELECT COUNT(*) FROM package WHERE email=".db_sanitize($email);
		}

		$count = db_query($query);

		if (!isset($count[0])) {
			return 0;
		}
		return $count[0]["COUNT(*)"];

	}

	// NOTE: Modify this function to include installment payment info too.
	function package_get_subs_status($package_id) {

		$package = db_query("SELECT status FROM package WHERE package_id=".$package_id.";");
		if (!isset($package[0]) || strcmp($package[0]["status"], "executed") != 0) {
			return false;
		}
		$subs = db_query("SELECT * FROM subs WHERE package_id=".$package_id.";");
		$subs = $subs[0];
		if (strcmp($subs["status"], "active") == 0) {
			return "Access ready and Confirmed.";
		}
		$pay = db_query("SELECT * FROM payment WHERE subs_id=".$subs["subs_id"].";");
		$pay = $pay[0];
		if (strcmp($pay["status"], "pending") == 0) {
			return "Email Sent. Waiting for Payment.";
		}
		else if (strcmp($pay["status"], "paid") == 0) {

			$user = user_get_by_id($subs["user_id"]);
			if (strlen($user["lms_soc"]) == 0) {
				return "Payment Confirmed. Waiting for Access Setup.";
			}
			else {
				return "System is processing access.";
			}

		}

	}

	function package_log($package_id) {

		$package = db_query("
			SELECT
				package.user_id,
				package.email,
				package.name,
				package.phone,
				package.combo,
				package.combo_free,
				package.currency,
				package.sum_total AS sum,
				package.creator_type,
				package.creator_id,
				IF(package.creator_type = 'system', '(Website)', user.name) AS agent_name,
				IF(package.creator_type = 'system', '', user.email) AS agent_email,
				IF(package.creator_type = 'system', '', user.phone) AS agent_phone,
				package.creator_comment,
				package.status_approval_sm,
				package.status_approval_pm,
				package.approver_comment_sm,
				package.approver_comment_pm,
				package.instl,
				package.instl_total,
                IF(package.status = 'executed', 'approved', package.status) AS status,
                package.receipt_type
			FROM
				package
			LEFT JOIN
				user ON user.user_id=package.creator_id AND package.creator_type='agent'
			WHERE
				package.package_id=".$package_id);

		$package = $package[0];

		if (!empty($package["combo_free"])) {
			$package["combo"] .= ";".$package["combo_free"];
		}

		$combo = explode(";", $package["combo"]);
		$courses = [];
		$courses_arr = [];
		foreach ($combo as $each) {

			$course = explode(",", $each);
			$courses["c".$course[0]] = $course[1];
			$courses_arr[] = $course[0];

		}

		$feed_course = [];

		$courses_arr = db_query("SELECT course_id, name FROM course WHERE course_id IN (".implode(",", $courses_arr).");");
		foreach ($courses_arr as $course) {
			$feed_course[$course["name"]] = $courses["c".$course["course_id"]] == "2" ? "Regular" : "Premium";
		}

		$tags = ["it"];
		$agent;
		if ($paylink_info["create_entity_type"] == "user") {
			$agent = user_get_by_id($package["creator_id"]);
			if ($agent !== false) $tags []= "for_".$package["creator_id"];
		}

		try {
			activity_log(
				"Package for [".ucwords($package["name"])."] has been [[".strtolower($package["status"])."]].",
				[
					$package["email"].(empty($package["phone"]) ? "" : " (".$package["phone"].")"),
					"Offered by ".$package["agent_name"]
				],
				"[Pricing :]",
				[
					"Nett Total" => (strtoupper($package["currency"]) == "INR" ? '&#8377;' : '&#36;').$package["sum"],
					"Installments" => $package["instl_total"]
				],
				"[Courses :]",
				$feed_course,
				$tags,
				[
					'c' => ($package["status"] == "approved" ? "complete" : "danger") //approved info, rejected danger,
				]
			);
		}
		catch (Exception $e) {
			activity_debug_start();
			activity_debug_log("package.approve() failed : ".($e->getMessage()));
		}

	}

?>
