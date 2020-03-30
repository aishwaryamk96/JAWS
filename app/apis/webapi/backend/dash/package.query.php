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

	// Send Headers
	header('Access-Control-Allow-Credentials: true');
	header("Content-Type: application/json");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Pragma-directive: no-cache");
	header("Cache-directive: no-cache");
	header("Cache-Control: private, must-revalidate, max-age=0, no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
	header("Pragma: no-cache");

	// Load stuff
	load_module("user");

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
	if (!empty($_POST["lead"])) {

		$users = [];
		$user_found = false;

		if (strpos($_POST["lead"], "@") !== false) {

			$user = user_get_by_email($_POST["lead"], true);
			if ($user !== false) {

				$users[] = $user["user_id"];
				$user_found = true;

			}

		}
		else {

			$res_users = db_query("SELECT user_id FROM user WHERE name LIKE ".db_sanitize("%".$_POST["lead"]."%").";");
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
			$where .= "(package.name LIKE ".db_sanitize("%".$_POST["lead"]."%")." OR package.email LIKE ".db_sanitize("%".$_POST["lead"]."%").") ";
		}

	}
	// END: Where lead=

	// BEGIN: Where agent=
	if (!empty($_POST["agent"])) {

		if (!is_numeric($_POST["agent"])) {

			$users = [];
			$user_found = false;

			if (strpos($_POST["agent"], "@") !== false) {

				$user = user_get_by_email($_POST["agent"]);
				if ($user !== false) {

					$users[] = $user["user_id"];
					$user_found = true;

				}

			}
			else {

				$res_users = db_query("SELECT user_id FROM user WHERE name LIKE ".db_sanitize("%".$_POST["agent"]."%")." OR email LIKE ".db_sanitize("%".$_POST["agent"]."%").";");
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
				die(json_encode(["status" => false, "code" => 404, "msg" => "Agent not found"]));
			}

		}
		else {
			where_clause("package.creator_id", "=", $_POST["agent"], $where);
		}

	}
	// END: Where agent=

	// BEGIN: Where source=
	if (!empty($_POST["source"])) {
		where_clause("package.creator_type", "=", db_sanitize(strcmp($_POST["source"], "user") == 0 ? "agent" : "system"), $where);
	}
	// END: Where source=

	// BEGIN: Where create_date >=
	if (!empty($_POST["date_from"])) {

		$date = date_create_from_format("m/d/Y", $_POST["date_from"]);
		$date->setTime(0, 0, 0);
		where_clause("package.create_date", ">=", db_sanitize($date->format("Y-m-d H:i:s")), $where);

	}
	// END: Where create_date >=

	// BEGIN: Where create_date <=
	if (!empty($_POST["date_to"])) {
		$date = date_create_from_format("m/d/Y", $_POST["date_to"]);
		$date->setTime(23, 59, 59);
		where_clause("package.create_date", "<=", db_sanitize($date->format("Y-m-d H:i:s")), $where);
	}
	// END: Where create_date <=

	// BEGIN: Where discount >=
	if (!empty($_POST["discount_from"])) {
		where_clause("((package.sum_basic - package.sum_offered) * 100 / package.sum_basic)", "=", intval($_POST["discount_from"]), $where);
	}
	// END: Where discount >=

	// BEGIN: Where discount <=
	if (!empty($_POST["discount_to"])) {
		where_clause("((package.sum_basic - package.sum_offered) * 100 / package.sum_basic)", "=", intval($_POST["discount_to"]), $where);
	}
	// END: Where discount <=

	// BEGIN: Where Currency=
	if (!empty($_POST["currency"])) {
		where_clause("package.currency", "=", db_sanitize($_POST["currency"]), $where);
	}
	// END: Where Currency=

	// BEGIN: Where Sum_offered >=
	if (!empty($_POST["sum_from"])) {
		where_clause("package.sum_total", ">=", intval(str_replace(",", "", $_POST["sum_from"])), $where);
	}
	// END: Where Sum_offered >=

	// BEGIN: Where Sum_offered <=
	if (!empty($_POST["sum_to"])) {
		where_clause("package.sum_total", "<=", intval(str_replace(",", "", $_POST["sum_to"])), $where);
	}
	// END: Where Sum_offered <=

	// BEGIN: Where Instl_count=
	if (!empty($_POST["instl_total"])) {
		where_clause("package.instl_total", "=", intval($_POST["instl_total"]), $where);
	}
	// END: Where Instl_count=

	// BEGIN: Where status=
	$payment_status_check = false;
	if (isset($_POST["status"])) {

		if ($_POST['status'] == '-1') {
			$join = "LEFT";
		}
		else {

			switch(strtolower($status_codes[$_POST["status"]])) {

				case "draft":
				case "rejected":
					where_clause("package.status", "=", db_sanitize($status_codes[$_POST["status"]]), $where);
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

				case "disabled":
					//where_clause("package.status", "=", db_sanitize("disabled"), $where);
					//$join = "LEFT";
					$payment_status_check = true;
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
	if (!empty($_POST["team"])) {

		where_clause("package.creator_type", "=", db_sanitize("agent"), $where);
		where_clause("package.creator_id", "IN", "(SELECT user_id FROM team WHERE team_id=".$_POST["team"].")", $where);

	}
	// END: Where team=

	// Initialize Query
        //JA-57 changes-
        // added line : "CAST(subs.access_duration AS UNSIGNED) as access_duration,subs.start_date,subs.end_date,"
	$query = "SELECT
				package.package_id,
				package.user_id,
				package.email,
				package.name,
				package.phone,
				package.combo,
				package.combo_free,
				package.bundle_id,
				IF (subs_meta.batch_id IS NOT NULL, subs_meta.batch_id, package.batch_id) AS batch_id,
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
				package.creator_id,
				IF(package.creator_type = 'system', 'system', user.name) AS agent_name,
				IF(package.creator_type = 'system', '', user.email) AS agent_email,
				IF(package.creator_type = 'system', '', user.phone) AS agent_phone,
				package.creator_comment AS agent_comment,
				package.status_approval_sm,
				package.status_approval_pm,
				package.approver_comment_sm,
				package.approver_comment_pm,
				approver_sm.name AS approver_sm,
				approver_pm.name AS approver_pm,
				package.status,
				package.expire_date,
				subs.subs_id,CAST(subs.access_duration AS UNSIGNED) as access_duration,subs.start_date,subs.end_date,
				payment.pay_id, payment.status AS pay_status,
                package.receipt_type,
                payment.app_num
			FROM `package`
			$join JOIN
				subs ON subs.package_id = package.package_id AND package.status IN ('approved','executed')
			$join JOIN
                payment ON payment.subs_id = subs.subs_id AND package.status IN ('approved','executed')
            $join JOIN
                subs_meta ON subs_meta.subs_id = subs.subs_id AND package.status IN ('approved','executed')
			LEFT JOIN
				user ON user.user_id = package.creator_id AND package.creator_type = 'agent'
			LEFT JOIN
				user AS approver_pm
				ON approver_pm.user_id = package.approver_pm_id
			LEFT JOIN
				user AS approver_sm
				ON approver_sm.user_id = package.approver_sm_id
			WHERE ";

	$sort = "DESC";
	if (!empty($_POST["sort"])) {
		$sort = str_toupper($_POST["sort"]);
	}
	$query .= ((strlen($where) > 0) ? $where : '1')." ORDER BY package.package_id ".$sort." LIMIT 1000;";

	//echo $query." ------ ".$where." ***************** ".json_encode($_POST);
	//die();

	$res = db_query($query);
	if (!isset($res[0])) {
		die(json_encode([]));
	}

	$today = (new DateTime("now"))->format("Y-m-d");
	$today_alt = date("Y-m-d");
	$response = [];
	foreach ($res as $package) {

		$create_date = date_create_from_format("Y-m-d H:i:s", $package["create_date"]);
		$package["create_date"] = ["date" => $create_date->format("d M, Y"), "time" => $create_date->format("h:i A"), "is_today" => $create_date->format("Y-m-d") == $today];

		$package["agent_comment"] = json_decode($package["agent_comment"], true);

		if (strcmp($package["status"], "executed") != 0) {

			$instl = json_decode($package["instl"], true);
			$package["instl"] = [];
			foreach ($instl as $key => $value) {
				$package["instl"][] = ["instl_count" => $key, "sum" => $value["sum"], "due_days" => $value["due_days"]];
			}

		}

		if (strcmp($package["status"], "pending") == 0) {
			$package["status"] = ((strcmp($package["status_approval_sm"], "pending") == 0) ? "approvalsm" : ((strcmp($package["status_approval_pm"], "pending") == 0) ? "approvalpm" : 'sent'));
		}

		else if (strcmp($package["status"], "executed") == 0) {
                        //JA-57 changes ends - added " instl.instl_id"
			$query = "SELECT
                                        instl.instl_id,
					instl.pay_date,
					instl.due_date,
					instl.due_days,
					instl.pay_mode,
					instl.status AS payinstl_status,
					instl.instl_count,
					instl.gateway_name,
					instl.gateway_reference,
					instl.gateway_channel_info,
					instl.sum,
                    instl.receipt,
					link.expire_date,
					link.status AS paylink_status
				FROM payment_instl AS instl
				LEFT JOIN
					payment_link AS link
						ON link.instl_id = instl.instl_id
				WHERE
					instl.pay_id = ".$package["pay_id"]." AND instl.instl_edited != 2 
				ORDER BY
					instl.instl_id ASC";
                        
                        //JA-57 changes ends - added " AND instl.instl_edited != 2"
                        
			$res_instl = db_query($query);
			$package["instl"] = $res_instl;

			for ($icount = 0; $icount < count($package["instl"]); $icount++) {
                                //JA-57 changes start
                                if (!empty($package["instl"][$icount]["pay_date"])) {
                                    $package["lastPayDate"] = $package["instl"][$icount]["pay_date"];
                                }
                                //JA-57 changes ends        
				try {

                                        
					if (!empty($package["instl"][$icount]["due_date"])) {

						$due_date = strtotime($package["instl"][$icount]["due_date"]);
						if ($due_date !== false) {
							$package["instl"][$icount]["due_date"] = [
								"date" => date("d M, Y", $due_date),
								"is_today" => date("Y-m-d", $due_date) == $today_alt
							];
						}

					}

				}
				catch(Exception $e) {}

				try {

					if (!empty($package["instl"][$icount]["pay_date"])) {

						$pay_date = strtotime($package["instl"][$icount]["pay_date"]);
						if ($pay_date !== false) {
							$package["instl"][$icount]["pay_date"] = [
								"date" => date("d M, Y", $pay_date),
								"is_today" => date("Y-m-d", $pay_date) == $today_alt
							];
						}

					}

				}
				catch(Exception $e) {}
			}

			try {

				if (!empty($package["instl"][0]["expire_date"])) {

					$expire_date = strtotime($package["instl"][0]["expire_date"]);
					if ($expire_date !== false) {
						$package["instl"][0]["expire_date"] = [
							"date" => date("d M, Y", $expire_date),
							"is_today" => date("Y-m-d", $expire_date) == $today_alt
						];
					}

				}

			}
			catch(Exception $e) {}

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
						else if (strcmp($instl["payinstl_status"], "disabled") == 0) {
							$status = "disabled";
						}

					}

				}

				if ($i == $package["instl_total"]) {
					$package["instl_next"] = 0;
				}

				$package["status"] = $status;

			}
			else {

				if (isset($package["instl"][0]['paylink_status']) && ($package["instl"][0]['paylink_status'] == 'expired')) {
					$package['status'] = 'expired';
				}
				else {
					$package['status'] = 'disabled';
				}

			}

		}

		$package["status"] = intval($status_codes[$package["status"]]);
		$package["sum_basic"] = intval($package["sum_basic"]);
		$package["sum_offered"] = intval($package["sum_offered"]);
		$package["sum_total"] = intval($package["sum_total"]);
		$package["instl_total"] = intval($package["instl_total"]);
		$package["instl_fees"] = intval($package["instl_fees"]);
                
                //JA-57 changes
                $lastPayDate = new DateTime(($package["lastPayDate"]));
                $today = new DateTime();

                $package['startDue']= ($today->diff($lastPayDate)->format("%a"))+1;
                
                //JA-57 ends
		if(!empty($package['bundle_id'])){
			$bundle = db_query("SELECT bundle_type, name FROM `course_bundle` WHERE bundle_id = " . db_sanitize($package['bundle_id']) . ";");
			$package["bundle_details"] = $bundle[0];
		}

		if ($payment_status_check) {

			if ($package["status"] == $_POST["status"]) {
				$response[] = $package;
			}

		}
		else {
			$response[] = $package;
		}

		if (count($response) > 99) {
			break;
		}

	}

	if (empty($_POST["team"])) {
		$_POST["team"] = "";
	}
	$_POST["status"] = intval($_POST["status"]);
	user_content_set($_SESSION["user"]["user_id"], "filter_default", json_encode($_POST));

	die(json_encode($response));

?>
