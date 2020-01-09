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

	if (count($_POST) == 0) {
		$_POST = json_decode(file_get_contents('php://input'), true);
	}

	if (count($_POST) == 0) {
		die(json_encode(["status" => false, "code" => 106, "msg" => "No data received"]));
	}

	load_module("user");
	load_module("subs");

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

	// Check Auth
	$package_force_approve = false;
	// A user is either logged into JAWS or is using PSK
	if (isset($_POST["token"])) {

		// Perform token authentication
		$psk = psk_info_get($_POST["token"]);
		if (strcmp($psk["action"], "crm") != 0) {

			//die(json_encode(["status" => false, "code" => 401, "msg" => "Token authentication failed"]));
			header("HTTP/1.1 401 Unauthorized");
			die();

		}
		if (strcmp($psk["entity_type"], "user") != 0) {

			//die(json_encode(["status" => false, "code" => 401, "msg" => "Token authentication failed"]));
			header("HTTP/1.1 401 Unauthorized");
			die();

		}

		$user = user_get_by_id($psk["entity_id"]);
		if (!auth_feature_is_allowed("package.update", auth_get_roles($user["roles"])["feature_keys"])) {

			//die(json_encode(["status" => false, "code" => 401, "msg" => "You do not have the required priviledges to use this feature."]));
			header("HTTP/1.1 401 Unauthorized");
			die();

		}

		if (auth_feature_is_allowed("pm", auth_get_roles($user["roles"])["feature_keys"])) {
			$package_force_approve = true;
		}

	}
	else {

		if (!auth_session_is_logged()) {

			//die(json_encode(["status" => false, "code" => 401, "msg" => "Please login to continue"]));
			header("HTTP/1.1 401 Unauthorized");
			die();

		}
		if (!auth_session_is_allowed("package.update")) {

			//die(json_encode(["status" => false, "code" => 401, "msg" => "You do not have the required priviledges to use this feature."]));
			header("HTTP/1.1 401 Unauthorized");
			die();

		}
		if (auth_session_is_allowed("pm")) {
			$package_force_approve = true;
		}

		$user = $_SESSION["user"];

	}

	// Check POST data
	if (empty($_POST["package"])) {
		die(json_encode(["status" => false, "code" => 1040, "msg" => "Package not found"]));
	}

	$package = $_POST["package"];

	if (empty($package["package_id"])) {
		die(json_encode(["status" => false, "code" => 1041, "msg" => "Package not found"]));
	}

	$package_id = $package["package_id"];
	if (!empty($_POST["persistence"])) {

		if (!is_persistent(array("layer" => $_POST["persistence"]["package_id"]["layer"], "type" => $_POST["persistence"]["package_id"]["type"], "id" => $package["package_id"]))) {
			die(json_encode(["status" => false, "code" => 1042, "msg" => "Package not found"]));
		}

		$package_id = get_native_id(array("layer" => $_POST["persistence"]["package_id"]["layer"], "type" => $_POST["persistence"]["package_id"]["type"], "id" => $package["package_id"]))["id"];

	}

	// SECTION: Force approval on behalf of SM
	if (!empty($package["force"])) {

	 	if (intval($package["force"]) != 0) {

			if ($package_force_approve) {

				$package_old = package_get($package_id);

				if (strcmp($package_old["status_approval_sm"], "approved") != 0) {

					$package["status_approval_sm"] = "approved";
					$package["approver_comment_sm"] = $package_old["approver_comment_sm"]."Force approved on behalf of Sales Manager by ".$user["name"];

				}

			}
			else {
				activity_create("medium", "package.approval_sm.forced", "unauthorized", "package", $package_id, "user", $user["user_id"], "Unauthorized attempt to force approve a package on behalf of Sales Manager", "pending");
			}

		}

		unset($package["force"]);

	}

	// SECTION: Capture the approver
	if ($package_force_approve) {
		$package["approver_pm_id"] = $user["user_id"];
	}
	else {
		$package["approver_sm_id"] = $user["user_id"];
	}

	$package = package_update($package_id, $package);
	if ($package === false) {

		echo json_encode(array("status" => false, "code" => 106, "msg" => "Failed to save package"));
		exit();

	}
	if (!empty($_POST["persistence"])) {

		$package_id = get_external_id(array("layer" => $_POST["persistence"]["package_id"]["layer"], "type" => $_POST["persistence"]["package_id"]["type"], "id" => $package_id))["id"];
		die(json_encode(["package_id" => $package_id]));

	}

	$package = db_query("SELECT
				package.package_id,
				package.user_id,
				package.email,
				package.name,
				package.phone,
				package.combo,
				package.combo_free,
				package.bundle_id,
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
				package.status,
				package.expire_date,
				subs.subs_id,
				payment.pay_id, payment.status AS pay_status
				FROM `package`
				LEFT JOIN
					subs ON subs.package_id = package.package_id AND package.status IN ('approved','executed')
				LEFT JOIN
					payment ON payment.subs_id = subs.subs_id AND package.status IN ('approved','executed')
				LEFT JOIN
					user ON user.user_id = package.creator_id AND package.creator_type = 'agent'
				WHERE
					package.package_id=".$package_id.";
		");

	$package = $package[0];

	$create_date = date_create_from_format("Y-m-d H:i:s", $package["create_date"]);
	$package["create_date"] = ["date" => $create_date->format("d M, Y"), "time" => $create_date->format("h:i A"), "is_today" => $create_date->format("Y-m-d") == $today];

	$package["agent_comment"] = json_decode($package["agent_comment"], true);

	if (strcmp($package["status"], "executed") == 0) {

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
					link.status AS paylink_status
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
	else {

		$instl = json_decode($package["instl"], true);
		$package["instl"] = [];
		foreach ($instl as $key => $value) {
			$package["instl"][] = ["instl_count" => $key, "sum" => $value["sum"], "due_days" => $value["due_days"]];
		}

		if (strcmp($package["status"], "pending") == 0) {
			$package["status"] = ((strcmp($package["status_approval_sm"], "pending") == 0) ? "approvalsm" : ((strcmp($package["status_approval_pm"], "pending") == 0) ? "approvalpm" : 'sent'));
		}

	}

	$package["status"] = intval($status_codes[$package["status"]]);
	$package["sum_basic"] = intval($package["sum_basic"]);
	$package["sum_offered"] = intval($package["sum_offered"]);
	$package["sum_total"] = intval($package["sum_total"]);
	$package["instl_total"] = intval($package["instl_total"]);
	$package["instl_fees"] = intval($package["instl_fees"]);

	die(json_encode(["status" => true, "code" => 200, "msg" => "Package updated successfully", "package_id" => $package_id, "package" => $package]));

?>