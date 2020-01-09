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

	$user;

	load_module("subs");
	load_module("user");

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

	}
	else {

		if (!auth_session_is_logged()) {

			//die(json_encode(["status" => false, "code" => 401, "msg" => "Please login to continue"]));
			header("HTTP/1.1 401 Unauthorized");
			die();

		}
		if (!auth_session_is_allowed("package.create")) {

			//die(json_encode(["status" => false, "code" => 401, "msg" => "You do not have the required priviledges to use this feature."]));
			header("HTTP/1.1 401 Unauthorized");
			die();

		}
		$user = $_SESSION["user"];

	}

	// Check POST data
	if (empty($_POST["package"])) {
		die(json_encode(["status" => false, "code" => 1040, "Package not found"]));
	}

	$package = $_POST["package"];
	$package["creator_id"] = $user["user_id"];

	if (!empty($package["bundle_id"])) {

		$bundle_type = db_query("SELECT bundle_type FROM course_bundle WHERE bundle_id = ".db_sanitize($package["bundle_id"]).";");
		if (empty($bundle_type)) {
			die(json_encode(["status" => false, "code" => 1040, "msg" => "Specialization or bootcamp not found"]));
		}

		if ($bundle_type[0]["bundle_type"] == "bootcamps") {

			if (empty($package["batch_id"])) {
				die(json_encode(["status" => false, "code" => 1040, "msg" => "No bootcamp batch selected"]));
			}

		}

	}

	unset($package["instl"][0]);

	$approval_status = package_require_approval_check($package);
	if ($approval_status === false) {

		$package["require_approval_sm"] = 0;
		$package["require_approval_pm"] = 1;
		$package["status_approval_sm"] = "approved";
		$package["status_approval_pm"] = "pending";

	}
	else {

		$package["approval_require_comment"] = json_encode($approval_status);
		$package["require_approval_sm"] = $package["require_approval_pm"] = 1;
		$package["status_approval_sm"] = $package["status_approval_pm"] = "pending";

	}
	$package["status"] = "draft";

	$ret_package_id;
	if (!empty($package["package_id"])) {

		$ret_package_id = $package["package_id"];
		if (!empty($_POST["persistence"])) {

			$package_id = get_native_id(array("layer" => $_POST["persistence"]["package_id"]["layer"], "type" => $_POST["persistence"]["package_id"]["type"], "id" => $package["package_id"]))["id"];
			$package = package_update($package_id, $package);

		}
		else {
			$package = package_update($package["package_id"], $package);
		}

		if ($package === false) {
			die(json_encode(["status" => false, "code" => 104, "Package not found"]));
		}

	}
	else {

		$package = package_create($package);
		$ret_package_id = $package["package_id"];
		if (!empty($_POST["persistence"])) {
			$ret_package_id = get_external_id(array("layer" => $_POST["persistence"]["package_id"]["layer"], "type" => $_POST["persistence"]["package_id"]["type"], "id" => $ret_package_id))["id"];
		}

	}

	echo json_encode(array("status" => true, "code" => 200, "msg" => "Package saved successfully", "package_id" => $ret_package_id, "status_comments" => json_encode($approval_status)));
	exit();

?>
