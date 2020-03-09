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
		header("HTTP/1.1 401 Unauthorized");
		die();
    }

	// Init Session
	auth_session_init();

	// Auth Check - Expecting Session Only !
	if ((!auth_session_is_logged()) || (!auth_session_is_allowed("dash"))) {
		header("HTTP/1.1 401 Unauthorized");
		die();
	}

	// Load stuff
	load_module("user");
	load_plugin("phpexcel");

	// Clause Creater
	$where = "";
	function where_clause($name, $operator, $value, &$where) {
		$where .= strlen($where) > 0 ? "AND " : "";
		$where .= $name." ".$operator." ".$value." ";
	}

	$date = new DateTime("now");

	$status_codes = [
		"draft" => "0",
		"approvalsm" => "1",
		"approvalpm" => "2",
		"rejected" => "3",
		"sent" => "4",
		"paid" => "5",
		"due" => "6",
		"expired" => "7",
		"disabled" => "8"
	];

	$status_codes = array_merge($status_codes, array_flip($status_codes));

	//SELECT instl.*, link.*, package.*, IF(package.creator_type='system', 'Website', user.name) as agent_name FROM payment_instl AS instl LEFT JOIN payment_link AS link ON instl.instl_id = link.instl_id RIGHT JOIN payment ON payment.pay_id = instl.pay_id RIGHT JOIN subs ON subs.subs_id = payment.subs_id RIGHT JOIN package ON package.package_id = subs.package_id RIGHT JOIN user ON user.user_id = package.creator_id WHERE 1 ORDER BY package.package_id DESC LIMIT 100;

	$join = "INNER";

	// START building the query---

	// BEGIN: Where lead=
	if (!empty($_REQUEST["lead"])) {

		$users = [];
		$user_found = false;

		if (strpos($_REQUEST["lead"], "@") !== false) {

			$user = user_get_by_email($_REQUEST["lead"], true);
			if ($user !== false) {

				$users[] = $user["user_id"];
				$user_found = true;

			}
			else {
				die(jsin_encode([]));
			}

		}
		else {

			$res_users = db_query("SELECT user_id FROM user WHERE name LIKE ".db_sanitize("%".$_REQUEST["lead"]."%").";");
			if (isset($res_users[0])) {
				foreach($res_users as $user) {

					$users[] = $user["user_id"];
					$user_found = true;

				}
			}

		}

		if ($user_found) {
			$where .= "package.user_id IN (".implode(",", $users).") ";
		}
		else {
			$where .= "(package.name LIKE ".db_sanitize("%".$_REQUEST["lead"]."%")." OR package.email LIKE ".db_sanitize("%".$_REQUEST["lead"]."%").") ";
		}

	}
	// END: Where lead=

	// BEGIN: Where agent=
	if (!empty($_REQUEST["agent"])) {

		if (!is_numeric($_REQUEST["agent"])) {

			$users = [];
			$user_found = false;

			if (strpos($_REQUEST["agent"], "@") !== false) {

				$user = user_get_by_email($_REQUEST["agent"]);
				if ($user !== false) {

					$users[] = $user["user_id"];
					$user_found = true;

				}

			}
			else {

				$res_users = db_query("SELECT user_id FROM user WHERE name LIKE ".db_sanitize("%".$_REQUEST["agent"]."%")." OR email LIKE ".db_sanitize("%".$_REQUEST["agent"]."%").";");
				if (isset($res_users[0])) {
					foreach($res_users as $user) {

						$users[] = $user["user_id"];
						$user_found = true;

					}
				}

			}

			if ($user_found) {
				where_clause("package.creator_id", "IN", "(".implode(",", $users).")", $where);
			}
			else {
				die(json_encode([]));
			}

		}
		else {
			where_clause("package.creator_id", "=", $_REQUEST["agent"], $where);
		}

	}
	// END: Where agent=

	// BEGIN: Where source=
	if (!empty($_REQUEST["source"])) {
		where_clause("package.creator_type", "=", db_sanitize(strcmp($_REQUEST["source"], "user") == 0 ? "agent" : "system"), $where);
	}
	// END: Where source=

	// BEGIN: Where create_date >=
	if (!empty($_REQUEST["date_from"])) {
		$date = date_create_from_format("m/d/Y", $_REQUEST["date_from"]);
		$date->setTime(0, 0, 0);
		where_clause("package.create_date", ">=", db_sanitize($date->format("Y-m-d H:i:s")), $where);
	}
	// END: Where create_date >=

	// BEGIN: Where create_date <=
	if (!empty($_REQUEST["date_to"])) {
		$date = date_create_from_format("m/d/Y", $_REQUEST["date_to"]);
		$date->setTime(23, 59, 59);
		where_clause("package.create_date", "<=", db_sanitize($date->format("Y-m-d H:i:s")), $where);
	}
	// END: Where create_date <=

	// BEGIN: Where discount >=
	if (!empty($_REQUEST["discount_from"])) {
		where_clause("((package.sum_basic - package.sum_offered) * 100 / package.sum_basic)", "=", intval($_REQUEST["discount_from"]), $where);
	}
	// END: Where discount >=

	// BEGIN: Where discount <=
	if (!empty($_REQUEST["discount_to"])) {
		where_clause("((package.sum_basic - package.sum_offered) * 100 / package.sum_basic)", "=", intval($_REQUEST["discount_to"]), $where);
	}
	// END: Where discount <=

	// BEGIN: Where Currency=
	if (!empty($_REQUEST["currency"])) {
		where_clause("package.currency", "=", db_sanitize($_REQUEST["currency"]), $where);
	}
	// END: Where Currency=

	// BEGIN: Where Sum_offered >=
	if (!empty($_REQUEST["sum_from"])) {
		where_clause("package.sum_total", ">=", intval(str_replace(",", "", $_REQUEST["sum_from"])), $where);
	}
	// END: Where Sum_offered >=

	// BEGIN: Where Sum_offered <=
	if (!empty($_REQUEST["sum_to"])) {
		where_clause("package.sum_total", "<=", intval(str_replace(",", "", $_REQUEST["sum_to"])), $where);
	}
	// END: Where Sum_offered <=

	// BEGIN: Where Instl_count=
	if (!empty($_REQUEST["instl_total"])) {
		where_clause("package.instl_total", "=", intval($_REQUEST["instl_total"]), $where);
	}
	// END: Where Instl_count=

	// BEGIN: Where status=
	$payment_status_check = false;
	if (isset($_REQUEST["status"])) {

		if ($_REQUEST['status'] == '-1') {
			$join = "LEFT";
		}
		else {
			switch(strtolower($status_codes[$_REQUEST["status"]])) {
				case "draft":
				case "rejected":
					where_clause("package.status", "=", db_sanitize($status_codes[$_REQUEST["status"]]), $where);
					$join = "LEFT";
					break;

				case "approvalsm":
					where_clause("package.status", "=", db_sanitize('pending'), $where);
					where_clause("package.status_approval_sm", "=", db_sanitize("pending"), $where);
					$join = "LEFT";
					break;

				case "approvalpm":
					where_clause("package.status", "=", db_sanitize('pending'), $where);
					where_clause("package.status_approval_sm", "=", db_sanitize("approved"), $where);
					where_clause("package.status_approval_pm", "=", db_sanitize("pending"), $where);
					$join = "LEFT";
					break;

				case "sent": $join = "LEFT";
				case "paid":
				case "due":
				case "expired":
					where_clause("package.status", "IN", "('executed','approved')", $where);
					$payment_status_check = true;
					break;
			}
		}
	}
	// END: Where status=

	// START: Where team=
	if (!empty($_REQUEST["team"])) {

		where_clause("package.creator_type", "=", db_sanitize("agent"), $where);
		where_clause("package.creator_id", "IN", "(SELECT user_id FROM team WHERE team_id=".$_REQUEST["team"].")", $where);

	}
	// END: Where team=

	// Initialize Query
	$query = "SELECT
				package.package_id,
				package.user_id,
				package.email,
				package.name,
				package.phone,
				package.combo,
				package.combo_free,
				package.bundle_id,
				package.batch_id,
				package.currency,
				package.sum_basic,
				package.sum_offered,
				package.sum_total,
				package.tax,
				package.instl,
				package.instl_fees,
				package.instl_total,
				package.pay_mode,
				package.create_date,
				IF(package.creator_type = 'system', 'system', user.name) AS agent_name,
				IF(package.creator_type = 'system', '', user.email) AS agent_email,
				IF(package.creator_type = 'system', '', user.phone) AS agent_phone,
				package.status_approval_sm,
				package.status_approval_pm,
				approver_sm.name AS sm_name,
				approver_pm.name AS pm_name,
				package.approver_comment_sm,
				package.approver_comment_pm,
				package.status,
				package.expire_date,
				subs.subs_id,
				payment.pay_id, payment.status AS pay_status
			FROM `package`
			".$join." JOIN
				subs ON subs.package_id = package.package_id AND package.status IN ('approved','executed')
			".$join." JOIN
				payment ON payment.subs_id = subs.subs_id AND package.status IN ('approved','executed')
			LEFT JOIN
				user ON user.user_id = package.creator_id AND package.creator_type = 'agent'
			LEFT JOIN
				user AS approver_sm ON approver_sm.user_id = package.approver_sm_id
			LEFT JOIN
				user AS approver_pm ON approver_pm.user_id = package.approver_pm_id
			WHERE ";

	$sort = "DESC";
	if (!empty($_REQUEST["sort"])) {
		$sort = str_toupper($_REQUEST["sort"]);
	}
	$query .= ((strlen($where) > 0) ? $where : '1')." ORDER BY package.package_id ".$sort.";";

	// echo $query." ------ ".$where." ***************** ".json_encode($_REQUEST);
	// die();

	$res = db_query($query);
	if (!isset($res[0])) {
		die(json_encode([]));
	}

	$today = (new DateTime("now"))->format("Y-m-d");
	$today_alt = date("Y-m-d");
	$response = [];
	foreach ($res as $package) {

		$create_date = date_create_from_format("Y-m-d H:i:s", $package["create_date"]);
		$package["create_date"] = $create_date->format("d-M-Y H:i:s");


		if (strcmp($package["status"], "executed") != 0) {

			$instl = json_decode($package["instl"], true);
			$package["instl"] = [];
			foreach ($instl as $key => $value) {
				$package["instl"][] = ["instl_count" => $key, "sum" => $value["sum"], "due_days" => $value["due_days"]];
			}

		}

		if (strcmp($package["status"], "pending") == 0) {
			$package["status"] = ((strcmp($package["status_approval_sm"], "pending") == 0) ? "awaiting SM aprroval" : ((strcmp($package["status_approval_pm"], "pending") == 0) ? "awaiting PM approval" : 'sent'));
		}

		else if (strcmp($package["status"], "executed") == 0) {

			$query = "SELECT
					instl.pay_date,
					instl.due_date,
					instl.due_days,
					instl.pay_mode,
					instl.status AS payinstl_status,
					instl.instl_count,
					instl.gateway_name,
					instl.gateway_reference,
					instl.sum,
					link.expire_date,
					link.status AS paylink_status,
                    instl.receipt
				FROM payment_instl AS instl
				LEFT JOIN
					payment_link AS link
						ON link.instl_id = instl.instl_id
				WHERE
					instl.pay_id = ".$package["pay_id"]."
				ORDER BY
					instl.instl_id ASC";

			$res_instl = db_query($query);
			$package["instl"] = $res_instl;

			for ($icount = 0; $icount < count($package["instl"]); $icount++) {

				try {

					if (!empty($package["instl"][$icount]["due_date"])) {

						$due_date = strtotime($package["instl"][$icount]["due_date"]);
						if ($due_date !== false) {
							$package["instl"][$icount]["due_date"] = date("d-M-Y H:i:s", $due_date);
						}

					}

				}
				catch(Exception $e) {}

				try {

					if (!empty($package["instl"][$icount]["pay_date"])) {

						$pay_date = strtotime($package["instl"][$icount]["pay_date"]);
						if ($pay_date !== false) {
							$package["instl"][$icount]["pay_date"] = date("d-M-Y H:i:s", $pay_date);
						}

					}

				}
				catch(Exception $e) {}

                                try {

					if (!empty($package["instl"][$icount]["expire_date"])) {

						$expire_date = strtotime($package["instl"][$icount]["expire_date"]);

						if ($expire_date !== false) {
							$package["instl"][$icount]["expire_date"] = date("d-M-Y H:i:s", $expire_date);
						}

					}

				}
				catch(Exception $e) {}

			}

//			try {
//
//				if (!empty($package["instl"][0]["expire_date"])) {
//
//					$expire_date = strtotime($package["instl"][0]["expire_date"]);
//
//					if ($expire_date !== false) {
//						$package["instl"][0]["expire_date"] = date("d-M-Y H:i:s", $expire_date);
//					}
//
//				}
//
//			}
//			catch(Exception $e) {}

			if (strcmp($package["pay_status"], "pending") == 0) {
				$package["status"] = "sent";
			}
			else if (strcmp($package["pay_status"], "paid") == 0) {

				$status = "paid";
				$i = 0;
				foreach ($res_instl as $instl) {

					if (strcmp($instl["payinstl_status"], "due") == 0) {

						$status = "due";
						$package["instl_next"] = $instl["instl_count"];
						break;

					}
					else {

						if (strcmp($instl["payinstl_status"], "enabled") == 0) {

							$package["instl_next"] = $instl["instl_count"];
							break;

						}
						else if (strcmp($instl["payinstl_status"], "paid") == 0) {
							$i++;
						}

					}

				}

				if ($i == $package["instl_total"]) {
					$package["instl_next"] = 0;
				}

				$package["status"] = $status;

			}
			else {
				if (isset($package["instl"][0]['paylink_status']) && ($package["instl"][0]['paylink_status'] == 'expired')) $package['status'] = 'expired';
				else $package['status'] = 'disabled';
			}

		}

		if ($payment_status_check) {
			if ($package["status"] == $_REQUEST["status"]) {
				$response[] = $package;
			}
		}
		else {
			$response[] = $package;
		}

	}

	$res_courses = db_query("SELECT course_id, il_code, sp_code FROM course;");
	$courses = [];
	foreach ($res_courses as $course) {
		$courses[$course["course_id"]]["1"] = $course["il_code"];
		$courses[$course["course_id"]]["2"] = $course["sp_code"];
	}
	$res_bundles = db_query("SELECT bundle_id, name FROM course_bundle;");
	$bundles = [];
	foreach ($res_bundles as $bundle) {
		$bundles[$bundle["bundle_id"]] = $bundle["name"];
	}

	$col = [
        "Email",
        "Name",
        "Phone",
        "Courses",
        "Complimentary Courses",
        "Specialization",
        "Currency",
        "Sum Actual",
        "Sum Offered",
        "Total (incl. taxes)",
        "Total Instalments",
        "Pay mode",
        "Date",
        "Agent",
        "Sales Manager",
        "Sales Mgr Approval",
        "Payments Manager",
        "Payments Mgr Approval",
        "Status",
        "Instalment Number",
        "Instalment Amount",
        "Date of Payment",
        "Due Date",
        "Due Days",
        "Pay mode",
        "Instalment Status",
        "Gateway Name",
        "Gateway Reference",
        "Expire Date",
        "Receipt Number"
    ];
	$lines = [];


	foreach ($response as $package) {

		$combo_str = [];
		$combo = explode(";", $package["combo"]);
		foreach ($combo as $course) {

			$details = explode(",", $course);
			$combo_str[] = $courses[$details[0]][$details[1]];

		}

		$combo_free_str = [];
		if (!empty($package["combo_free"])) {

			$combo_free = explode(";", $package["combo_free"]);
			foreach ($combo_free as $course) {

				$details = explode(",", $course);
				$combo_free_str[] = $courses[$details[0]][$details[1]];
			}

		}

		foreach ($package["instl"] as $instl) {

			$lines[] = [
                $package["email"],
                $package["name"],
                $package["phone"],
                implode("+", $combo_str),
                implode("+", $combo_free_str),
                (!empty($package["bundle_id"]) ? $bundles[$package["bundle_id"]] : ""),
                $package["currency"],
                $package["sum_basic"],
                $package["sum_offered"],
                $package["sum_total"],
                $package["instl_total"],
                $package["pay_mode"],
                $package["create_date"],
                $package["agent_name"],
                (!empty($package["sm_name"]) ? $package["sm_name"] : "#N/A"),
                ucwords($package["status_approval_sm"]),
                (!empty($package["pm_name"]) ? $package["pm_name"] : "#N/A"),
                ucwords($package["status_approval_pm"]),
                ucwords($package["status"]),
                /*$line.*/$instl["instl_count"],
                $instl["sum"],
                (!empty($instl["pay_date"]) ? $instl["pay_date"] : ""),
                (!empty($instl["due_date"]) ? $instl["due_date"] : ""),
                (!empty($instl["due_days"]) ? $instl["due_days"] : ""),
                (!empty($instl["pay_mode"]) ? $instl["pay_mode"] : ""),
                (!empty($instl["payinstl_status"]) ? $instl["payinstl_status"] : ""),
                (!empty($instl["gateway_name"]) ? $instl["gateway_name"] : ""),
                (!empty($instl["gateway_reference"]) ? $instl["gateway_reference"] : ""),
                (!empty($instl["expire_date"]) ? $instl["expire_date"] : ""),
                (!empty($instl["receipt"]) ? $instl["receipt"] : "")
            ];
		}

	}

	phpexcel_write([
			0 => [
				"title" => "Payments",
				"cols" => $col,
				"data" => $lines
			]
		],

		["title" => "Payments (".date("F j, Y").")"],

		"Payments (".date("F j, Y").").xls"
	);

	exit();

?>
