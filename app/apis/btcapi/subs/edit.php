<?php

	// Auth Check - Expecting Session Only !
	authorize_api_call("enrollment.get.adv", true);

	if (empty($_POST)) {
		$_POST = json_decode(file_get_contents("php://input"), true);
	}

	if (empty($_POST["subs"]) || empty($_POST["subs"]["subs_id"])) {

		header("HTTP/1.1 422");
		die(json_encode(["errors" => ["subs_id cannot be null"]]));

	}

	// die(json_decode($_POST));
	register_shutdown_function(function() {
		if (!empty(($errors = error_get_last()))) {
			db_exec("INSERT INTO system_log (source, data) VALUES ('subs.edit', ".db_sanitize(json_encode($errors)).");");
		}
	});

	$subs = $_POST["subs"];
	$res_subs = subs_get($subs["subs_id"]);
	if (empty($res_subs)) {

		header("HTTP/1.1 422");
		die(json_encode(["errors" => ["subs not found"]]));

	}
	$subs["start_date"] = $res_subs["start_date"];
	$subs["end_date"] = $res_subs["end_date"];

	$subs["agent_id"] = $_SESSION["user"]["user_id"];

	$response = ["status" => true, "msg" => ""];

	$log_id = batcave_request_init($res_subs["user_id"], $_POST);
	// batcave_load_component("all");

	if (subs_status_change($subs, $res_subs) === false) {

		header("HTTP/1.1 422");
		die;

	}

	$bundle = "";
	if (!empty($subs["bundle_id"])) {

		$bundle_id = db_sanitize($subs["bundle_id"]);
		$bundle = db_query("SELECT name FROM course_bundle WHERE bundle_id = $bundle_id;")[0]["name"];

	}

	if (!empty($subs["statusNew"]) && $subs["statusNew"] != $res_subs["status"]) {

		$desc = db_sanitize("Changed subscription from '".$subs["status"]."' to '".$subs["statusNew"]."'");
		$subs["status"] = $subs["statusNew"];
		edit_subs_status($subs);
		user_add_log($subs["user_id"], "subs.status.edit", "", $_SESSION["user"]["user_id"], $desc, "done", ["subs", $subs["subs_id"]], $subs);

		die(json_encode($response));

	}

	if (identify_bundle_change($subs)) {

		$edit_subs = false;
		if (user_can("edit", "subs")) {

			if (!change_bundle($subs)) {

				batcave_append_log($log_id, ["Bundle change failed"]);
				die(json_encode(["status" => false, "msg" => "Bundle change failed"]));

			}

			$edit_subs = true;

		}

		$sub_category = "";
		$original_bundle = "Custom combo";
		if (!empty($subs["originalBundleId"])) {

			$original_bundle_id = db_sanitize($subs["originalBundleId"]);
			$original_bundle = db_query("SELECT name FROM course_bundle WHERE bundle_id = $original_bundle_id;")[0]["name"];

			$sub_category = "program.add";

		}
		$bundle_name = $bundle;
		if (!empty($subs["bundle_id"])) {

			if (!empty($sub_category)) {
				$sub_category = "program.swap";
			}
			else {
				$sub_category = "program.remove";
			}

		}
		else {
			$bundle_name = "Custom combo";
		}

		$desc = $original_bundle." swap".($edit_subs ? "ped" : "")." with ".$bundle_name.($edit_subs ? "" : "?");
		user_add_log($subs["user_id"], "subs.edit", $sub_category, $_SESSION["user"]["user_id"], $desc, ($edit_subs ? "done" : "pending"), ["subs", $subs["subs_id"]], $subs);

	}
	else {

		if (identify_batch_change($subs)) {

			$edit_batch = false;
			if (user_can("edit", "batch")) {

				if (!change_batch($subs)) {

					batcave_append_log($log_id, ["Batch change failed"]);
					die(json_encode(["status" => false, "msg" => "Batch change failed"]));

				}

				$edit_batch = true;

			}

			$original_batch = section_get_batch_from_number($subs["originalBatch"]);
			$new_batch = section_get_batch_from_number($subs["batch"]);

			$desc = $original_batch." change".($edit_batch ? "d" : "")." to ".$new_batch.(!empty($bundle) ? " for program ".$bundle : "").($edit_batch ? "" : "?");
			user_add_log($subs["user_id"], "subs.edit", "batch.change.program", $_SESSION["user"]["user_id"], $desc, ($edit_batch ? "done" : "pending"), ["subs", $subs["subs_id"]], $subs);

		}

		enrollments_change($subs["subs_id"], $subs["enr"]);

	}

	access_dates_change($subs["subs_id"], $subs["dates"]);
	payment_info_change($subs["pay"]);

	die(json_encode($response));

?>