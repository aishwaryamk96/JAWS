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
		header("HTTP/1.1 401 Unauthorized");
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
	load_library("persistence");

	$user;
	$require_approval = true;
	$can_create_prepaid = false;

	// Check Auth
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
		if (!auth_feature_is_allowed("package.create", auth_get_roles($user["roles"])["feature_keys"])) {

			//die(json_encode(["status" => false, "code" => 401, "msg" => "You do not have the required priviledges to use this feature."]));
			header("HTTP/1.1 401 Unauthorized");
			die();

		}
		if (auth_feature_is_allowed("payment.pay_mode.set", auth_get_roles($user["roles"])["feature_keys"])) {
			$can_create_prepaid = true;
		}
		if (auth_feature_is_allowed("payment.pay_mode.set", auth_get_roles($user["roles"])["feature_keys"])) {

			$require_approval = false;
			$can_create_prepaid = true;

		}

	}
	else {

		auth_session_init();
		if (!auth_session_is_logged()) {
			die(json_encode(["status" => false, "code" => 401, "msg" => "Please login to continue"]));
		}
		if (!auth_session_is_allowed("package.send")) {
			die(json_encode(["status" => false, "code" => 401, "msg" => "You do not have the required priviledges to use this feature."]));
		}
		$user = $_SESSION["user"];
		if (auth_session_is_allowed("payment.pay_mode.set")) {
			$can_create_prepaid = true;
		}
		if (auth_session_is_allowed("payment.pay_mode.set")) {

			$require_approval = false;
			$can_create_prepaid = true;

		}

	}

	// Check POST data
	if (empty($_POST["package"])) {
		die(json_encode(["status" => false, "code" => 1040, "msg" => "Package not found"]));
	}

	$package = $_POST["package"];
	$package["creator_id"] = $user["user_id"];

	if (!empty($package["bundle_id"])) {

		$bundle_type = db_query("SELECT bundle_type, receipt_type FROM course_bundle WHERE bundle_id = ".db_sanitize($package["bundle_id"]).";");
		if (empty($bundle_type)) {
			die(json_encode(["status" => false, "code" => 1040, "msg" => "Specialization or bootcamp not found"]));
		}

		if ($bundle_type[0]["bundle_type"] == "bootcamps") {

			if (empty($package["batch_id"])) {
				die(json_encode(["status" => false, "code" => 1040, "msg" => "No bootcamp batch selected"]));
			}

        }

        $package["receipt_type"] = $bundle_type[0]['receipt_type'];

	}
	if ($package["combo"] == "239,2") {

		$bb = db_exec("SELECT * FROM bootcamp_batches WHERE bundle_id = 135 AND start_date >= CURRENT_DATE ORDER BY id DESC;");
		if (!empty($bb)) {

			$package["bundle_id"] = 135;
			$package["batch_id"] = $bb[0]["id"];

		}

	}

	unset($package["instl"][0]);

	$total_amt = intval($package["sum_total"]);
	$instl_sum = 0;
	foreach ($package["instl"] as $instl) {
		$instl_sum += intval($instl["sum"]);
	}

	if (($instl_sum < $total_amt - 2) && ($instl_sum > $total_amt + 2)) {
		die (json_encode(["status" => false, "code" => 500, "msg" => "Instalments sum and total amount do not match"]));
	}

	$package_id;
	$approval_status;

	if (!$can_create_prepaid && strcmp($package["pay_mode"], "online") != 0) {
		die (json_encode(["status" => false, "code" => 101, "msg" => "Cannot create a prepaid package"]));
	}

	if (strcmp($package["pay_mode"], "ebs") == 0) {
		$package["pay_mode"] = "external";
	}

	if (empty($package["package_id"])) {

		$approval_status = package_require_approval_check($package);
		if ($approval_status === false) {

			$package["require_approval_sm"] = 0;
			$package["require_approval_pm"] = 1;
			$package["status_approval_sm"] = "approved";
			$package["status_approval_pm"] = "approved";
			$package["status"] = "approved";

		}
		else {

			$package["approval_require_comment"] = json_encode($approval_status);
			$package["require_approval_sm"] = $package["require_approval_pm"] = 1;
			$package["status_approval_sm"] = $package["status_approval_pm"] = "pending";
			$package["status"] = "pending";

		}

		if (!$require_approval) {

			$package["status_approval_sm"] = "approved";
			$package["status_approval_pm"] = "approved";
			$package["status"] = "approved";

		}

		$package = package_create($package);
		//$package_id = $package["package_id"];

	}
	else {

		if (!empty($_POST["persistence"])) {

			if (!is_persistent(array("layer" => $_POST["persistence"]["package_id"]["layer"], "type" => $_POST["persistence"]["package_id"]["type"], "id" => $package["package_id"]))) {
				die(json_encode(["status" => false, "code" => 1042, "msg" => "Package not found"]));
			}

			$package_id = get_native_id(array("layer" => $_POST["persistence"]["package_id"]["layer"], "type" => $_POST["persistence"]["package_id"]["type"], "id" => $package["package_id"]))["id"];

		}
		else {
			$package_id = $package["package_id"];
		}

		$package["status"] = "pending";

		$package_load = package_get($package_id);

		// If the package is not in draft state, it cannot be edited from this API
		if (strcmp($package_load["status"], "draft") != 0) {
			die(json_encode(["status" => false, "code" => 101, "Cannot modify package"]));
		}

		$approval_status = package_require_approval_check($package);

		if (!$require_approval) {

			$package["status_approval_sm"] = "approved";
			$package["status_approval_pm"] = "approved";
			$package["status"] = "approved";

		}

		$package = package_update($package_id, $package);
		if ($package === false) {
			die(json_encode(array("status" => false, "code" => "106", "msg" => "Package updation failed")));
		}

	}
	/*$ret_package_id = $package_id;
	if (isset($_POST["persistence"]))
		$ret_package_id = get_external_id(array("layer" => $_POST["persistence"]["package_id"]["layer"], "type" => $_POST["persistence"]["package_id"]["type"], "id" => $ret_package_id))["id"];*/

	if (!empty($_POST["persistence"])) {
		die(json_encode(["status" => (($approval_status === false) ? 1 : 2)]));
	}
	die(json_encode(["status" => true, "code" => (($approval_status === false) ? 100 : 102), "msg" => "Package sent".((($approval_status === false) ? "" : " for approval"))]));

?>
