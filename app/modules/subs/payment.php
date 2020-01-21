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

	// Pay_info array standard structure -
	// ["pay_id"] = ..
	// ["sum_total"] = ..
	// [payment table column name] = value
	// ["instl"] = array(1 => instl_1_info, 2 => ..)

	// Instl_info array standard structure -
	// [payment_instl table column name] = value

	// This will create a payment record and corresponding installment records
	function payment_create($user_id, $pay_info) {

		//Prep
		if (!isset($pay_info["coupons"])) $coupons = "";
		else if (strlen($pay_info["coupons"]) == 0) $coupons = "";
		else $coupons = $pay_info["coupons"];

		//Some extra var
		$paylink_web_id_first = "";

		// sanitize
		$create_date = db_sanitize(strval(date('Y-m-d H:i:s')));
		$status = db_sanitize($pay_info["status"]);
		$currency = db_sanitize($pay_info["currency"]);
		$coupons = db_sanitize($coupons);

		$receipt_type = db_sanitize("retail");
		if(!empty($pay_info["receipt_type"])){
			$receipt_type = db_sanitize(trim($pay_info["receipt_type"]));
		}

		// create the main record
		db_exec("
			INSERT INTO
				payment (
					user_id,
					subs_id,
					sum_basic,
					sum_total,
					currency,
					instl_total,
					create_date,
					coupons,
					status,
					type
				)
			VALUES (
				" . $user_id . ",
				" . $pay_info["subs_id"] . ",
				" . $pay_info["sum_basic"] . ",
				" . $pay_info["sum_total"] . ",
				" . $currency . ",
				" . $pay_info["instl_total"] . ",
				" . $create_date . ",
				" . $coupons . ",
				" . $status . ",
				" . $receipt_type . "
			);");

		// get the pay_id we just inserted
		$pay_id = db_get_last_insert_id();
		$pay_info["pay_id"] = $pay_id;

		// create installment records and corresponding paylinks
		$instl_total = intval($pay_info["instl_total"]);
		$instl_count = 1;

		while($instl_count <= $instl_total) {

			// prep
			$pay_date = "";
			$due_date = "";
			$assoc_entity_type = "";
			$assoc_entity_id = "";
			$pay_ip = "";
			$pay_comment = "";
			$status = "enabled";
			$due_days = $pay_info["instl"][$instl_count]["due_days"];

			// Some extra var
			$due_date_unsanitized = "";

			if ($instl_count == 1) {

				if (strcmp($pay_info["status"], "paid") == 0) {
					$pay_date = strval(date('Y-m-d H:i:s'));
					$status = "paid";

					if (!isset($pay_info["instl"][$instl_count]["pay_ip"])) $pay_ip = "";
					else $pay_ip = $pay_info["instl"][$instl_count]["pay_ip"];
				}
			}

			else {
				if (strcmp($pay_info["status"], "paid") == 0) {

					$dayslapsed = 0;
					for($count = 1; $count <= $instl_count; $count++ ) $dayslapsed += $pay_info["instl"][$count]["due_days"];
					$due_date = strval(date('Y-m-d H:i:s', strtotime("+ ".strval($dayslapsed)." days", strtotime(date('Y-m-d H:i:s')))));
				}

				else $status = "disabled";
			}

			$due_date = validate_due_date($pay_info["subs_id"], $due_date);

			if (!isset($pay_info["instl"][$instl_count]["assoc_entity_type"])) $assoc_entity_type = "";
			else $assoc_entity_type = $pay_info["instl"][$instl_count]["assoc_entity_type"];

			if (!isset($pay_info["instl"][$instl_count]["assoc_entity_id"])) $assoc_entity_id = "NULL";
			else $assoc_entity_id = $pay_info["instl"][$instl_count]["assoc_entity_id"];

			if (!empty($pay_info["instl"][$instl_count]["due_date"])) {

				$due_date_temp = date_create_from_format("Y-m-d", $pay_info["instl"][$instl_count]["due_date"]);
				if (!empty($due_date_temp)) {
					$due_date = $due_date_temp->format("Y-m-d H:i:s");
				}

			}

			// sanitize
			$due_date_unsanitized = $due_date;
			$pay_date = db_sanitize($pay_date);
			$due_date = db_sanitize($due_date);
			$assoc_entity_type = db_sanitize($assoc_entity_type);
			$assoc_entity_id = $assoc_entity_id;
			$pay_mode = db_sanitize('online');
			if(isset($pay_info["instl"][$instl_count]['pay_mode'])) $pay_mode = db_sanitize($pay_info["instl"][$instl_count]['pay_mode']);
			$pay_ip = db_sanitize($pay_ip);
			$pay_comment = db_sanitize($pay_comment);
			$status = db_sanitize($status);

			// create installment record
			db_exec("INSERT INTO payment_instl (pay_id, subs_id, user_id, instl_count, instl_total, sum, currency, due_days, due_date, pay_date, pay_mode, pay_ip, pay_comment, assoc_entity_type, assoc_entity_id, status) VALUES (".$pay_id.",".$pay_info["subs_id"].",".$user_id.",".$instl_count.",".$instl_total.",".$pay_info["instl"][$instl_count]['sum'].",".$currency.",".$due_days.",".((strlen($due_date) > 2) ? $due_date : "NULL").",".((strlen($pay_date) > 2) ? $pay_date : "NULL").",".$pay_mode.",".$pay_ip.",".$pay_comment.",".$assoc_entity_type.",".$assoc_entity_id.",".$status.");");

			// get the instl_id we just inserted
			$instl_id = db_get_last_insert_id();

			if (($instl_count > 1) || (strcmp($pay_info["status"], "pending") == 0)) {

				// prep
				$web_id = md5(strval($pay_id).strval($instl_id).strval($user_id).strval($pay_info["subs_id"]).strval($instl_count).strval(date('Y-m-d H:i:s')));
				$expire_date = "";
				$status = "enabled";
				$create_entity_type = "";
				$create_entity_id = "";

				if (!isset($pay_info["instl"][$instl_count]["expire_date"])) {
					if ($instl_count == 1) $expire_date = "";
					else {
						if (strcmp($pay_info["status"], "paid") == 0 ) {
							if (strlen($due_date_unsanitized) > 0) $expire_date = $due_date_unsanitized;
							else $expire_date = "";
						}
						else $expire_date = "";
					}
				}
				else $expire_date = $pay_info["instl"][$instl_count]["expire_date"];

				if (!isset($pay_info["instl"][$instl_count]["create_entity_type"])) $create_entity_type = "";
				else $create_entity_type = $pay_info["instl"][$instl_count]["create_entity_type"];

				if (!isset($pay_info["instl"][$instl_count]["create_entity_id"])) $create_entity_id = "NULL";
				else $create_entity_id = $pay_info["instl"][$instl_count]["create_entity_id"];

				if (($instl_count > 1) && (strcmp($pay_info["status"], "pending") == 0)) $status = "disabled";

				// sanitize
				$web_id = db_sanitize($web_id);
				$expire_date = db_sanitize($expire_date);
				$status = db_sanitize($status);
				$create_entity_type = db_sanitize($create_entity_type);
				$create_entity_id = $create_entity_id;

				// create paylink record
				db_exec("INSERT INTO payment_link (web_id, pay_id, instl_id, user_id, subs_id, create_date, expire_date, create_entity_type, create_entity_id, status) VALUES (".$web_id.",".$pay_id.",".$instl_id.",".$user_id.",".$pay_info["subs_id"].",".$create_date.",".((strlen($expire_date) > 2) ? $expire_date : "NULL" ).",".$create_entity_type.",".$create_entity_id.",".$status.");");

				// get the paylink_id we just inserted
				$paylink_id = db_get_last_insert_id();

				// update the installment record with the correspoding paylink_id we just created
				db_exec("UPDATE payment_instl SET paylink_id=".$paylink_id." WHERE instl_id=".$instl_id.";");

				// populate new info for return
				$pay_info["instl"][$instl_count]["paylink_id"] = $paylink_id;
				$pay_info["instl"][$instl_count]["web_id"] = trim($web_id, "'");

				// generate the OTP
				if (($instl_count == 1) && (strcmp($pay_info["status"], "pending") == 0)) {
					load_module("auth");
					$pay_info["instl"][$instl_count]["token"] = psk_generate("payment_link", $paylink_id, "user.login.force", $user_id);
				}

			}

			// populate new info for return
			$pay_info["instl"][$instl_count]["instl_id"] = $instl_id;

			// next
			$instl_count ++;

		}

		// return the paylink web_id for the first payment to be made - if not paid already this will be populated
		return $pay_info;

	}

	// This will load a payment link info
	function payment_link_parse($web_id) {

		// load link info
		$res = db_query("SELECT * FROM payment_link WHERE web_id=".db_sanitize($web_id)." AND status='enabled' LIMIT 1;");

		if (!isset($res[0])) {
			$res = db_query("SELECT * FROM payment_link WHERE web_id=".db_sanitize($web_id)." AND status='expired' LIMIT 1;");
			if (isset($res[0])) activity_create("ignore", "paylink.parse.expired", "fail", "paylink_id", $res[0]["paylink_id"], "user_id", $res[0]["user_id"], "Expired Payment Link Clicked", "logged");
			return false;
		}

		$paylink = $res[0];
		$user_id = $paylink["user_id"];
		$subs_id = $paylink["subs_id"];
		$instl_id = $paylink["instl_id"];
		$pay_id = $paylink["pay_id"];
		$create_entity_id = $paylink["create_entity_id"];
		$create_entity_type = $paylink["create_entity_type"];

		// load instl info
		$res = db_query("SELECT * FROM payment_instl WHERE instl_id=".$instl_id." LIMIT 1;");
		if (!isset($res[0])) return false;
		if ((strcmp($res[0]["status"], "paid") == 0) || (strcmp($res[0]["status"], "disabled") == 0)) return false;

		$instl = $res[0];
		$sum = intval($instl["sum"]);
		$currency = $instl["currency"];
		$instl_count = intval($instl["instl_count"]);
		$instl_total = intval($instl["instl_total"]);

		// instl status check
		if (($instl_count > 1) && (strcmp($instl["status"],"due") != 0)) return false;
		if (($instl_count == 1) && (strcmp($instl["status"],"enabled") != 0)) return false;

		// load pay info
		$res = db_query("SELECT * FROM payment WHERE pay_id=".$pay_id." LIMIT 1;");
		if (!isset($res[0])) return false;

		$pay = $res[0];

		// pay status check
		if (($instl_count > 1) && (strcmp($pay["status"], "paid") != 0)) return false;
		if (($instl_count == 1) && (strcmp($pay["status"],"pending") != 0)) return false;

		$sum_total = intval($pay["sum_total"]);

		// prep the return variable
		return array(
			"paylink_id" => $paylink["paylink_id"],
			"user_id" => $user_id,
			"subs_id" => $subs_id,
			"instl_id" => $instl_id,
			"pay_id" => $pay_id,
			"create_entity_type" => $create_entity_type,
			"create_entity_id" => $create_entity_id,
			"sum" => $sum,
			"sum_total" => $sum_total,
			"currency" => $currency,
			"instl_count" => $instl_count,
			"instl_total" => $instl_total,
			"web_id" => $web_id,
            /*"create_entity_type" => $paylink["create_entity_type"],*/
            "receipt_type" => $pay["type"],
		);

	}

	// This will get all info about a payment
	function payment_get_info($pay_id) {

		$res = db_query("SELECT * FROM payment WHERE pay_id=".$pay_id." LIMIT 1;");
		if (!isset($res[0])) return false;

		$res_instl = db_query("SELECT * FROM payment_instl WHERE pay_id=".$pay_id." ORDER BY instl_count ASC;");
		$count = 1;
		foreach($res_instl as $instl) {
			$res[0]["instl"][$count] = $instl;
			$count ++;
		}

		return $res[0];

	}

	// This will get all payments info about a user
	function payment_get_info_by_user_id($user_id) {

		$res_pay = db_query("SELECT * FROM payment WHERE user_id=".$user_id.";");
		if (!isset($res_pay[0])) return false;
		$payments = array();

		foreach ($res_pay as $pay) {
			$res_instl = db_query("SELECT * FROM payment_instl WHERE pay_id=".$pay["pay_id"]." ORDER BY instl_count ASC;");
			$res_paylink = db_query("SELECT * FROM payment_link WHERE pay_id=".$pay["pay_id"]." ORDER BY paylink_id ASC;");

			$count = 1;
			foreach($res_instl as $instl) {
				$pay["instl"][$count] = $instl;
				if (isset($res_paylink[$count-1])) $pay["instl"][$count]["paylink"] = $res_paylink[$count-1];

				$count ++;
			}

			$payments[] = $pay;
		}

		return $payments;

	}

	// This will expire a payment link
	function payment_link_expire($web_id, $used = true) {

	}

	// This will reactivate an link - but not an used link
	function payment_link_reactivate($web_id) {

	}

	// This will confirm the payment of a payment link and its corresponding installment record
	function payment_link_confirm($web_id, $transaction_reponse) {

		register_shutdown_function(function() {
			if (!empty($error = error_get_last())) {
				log_activity("payment.link.confirm", $error);
			}
		});

		// Load stuff
		load_module("course");

		// load link info
		$res_paylink = db_query("SELECT * FROM payment_link WHERE web_id=".db_sanitize($web_id)." LIMIT 1;");
		if (!isset($res_paylink[0])) return false;
		$res_paylink = $res_paylink[0];

		// Load payment record
		$res_payment = db_query("SELECT * FROM payment WHERE pay_id=".db_sanitize($res_paylink["pay_id"])." LIMIT 1;");
		if (!isset($res_payment[0])) return false;
		$res_payment = $res_payment[0];

		// Load instl record
		$res_instl = db_query("SELECT * FROM payment_instl WHERE instl_id=".db_sanitize($res_paylink["instl_id"])." LIMIT 1;");
		if (!isset($res_instl[0])) return false;
		$res_instl = $res_instl[0];

		// Check
		if ((strcmp($res_instl["status"], "paid") == 0) || (strcmp($res_instl["status"], "disabled") == 0)) return false;
		if (strcmp($res_paylink["status"], "enabled") != 0) return false;
		if (strcmp($res_instl["instl_count"], "1") == 0) { if (strcmp($res_payment["status"], "pending") != 0) return false; }
		else { if (strcmp($res_payment["status"], "paid") != 0) return false; }

		// Load subs record
		$res_subs = db_query("SELECT * FROM subs WHERE pay_id=".db_sanitize($res_paylink["pay_id"])." LIMIT 1;");
		if (!isset($res_subs[0])) return false;
		$res_subs = $res_subs[0];
		$subs_meta = db_query("SELECT * FROM subs_meta WHERE subs_id = ".$res_subs["subs_id"]);
		if (!empty($subs_meta)) {

			$res_subs["bundle_id"] = $subs_meta[0]["bundle_id"];
			$res_subs["batch_id"] = $subs_meta[0]["batch_id"];

		}

		// First set the link record
		db_exec("UPDATE payment_link SET status='used' WHERE web_id=".db_sanitize($web_id).";");

		// Set the instl record
		$sql = "UPDATE payment_instl SET status='paid', gateway_reference=".db_sanitize($transaction_reponse["reference_id"]).", gateway_channel_type=".db_sanitize($transaction_reponse["channel_type"]).", gateway_channel_info=".db_sanitize($transaction_reponse["channel_info"]).", gateway_name=".db_sanitize($transaction_reponse["pg"]).", assoc_entity_type='system', pay_date=".db_sanitize(strval(date('Y-m-d H:i:s')));

		if(!empty($transaction_reponse['meta'])){
            $meta = array(
                'order_api' => json_decode($res_instl['meta'] ?? "[]", true),
                'payment_capture_api' => json_decode($transaction_reponse["meta"] ?? "[]", true)
            );
			$sql .= ", meta=" . db_sanitize(json_encode($meta));
		}

		$sql .= " WHERE instl_id=".db_sanitize($res_paylink["instl_id"]).";";

		db_exec($sql);

		// Find if this is the first payment
		if (strcmp($res_instl["instl_count"], "1") == 0) {

			// Set the payment record
			db_exec("UPDATE payment SET status='paid' WHERE pay_id=".db_sanitize($res_paylink["pay_id"]).";");

			// Proccess access start date
			if (!isset($res_subs["start_date"])) {
				$res_subs["start_date"] = strval(date('Y-m-d H:i:s'));
			}
			else {

				if (strlen($res_subs["start_date"]) == 0) {
					$res_subs["start_date"] = strval(date('Y-m-d H:i:s'));
				}
				else {

					$date1 = date_create($res_subs["start_date"]);
					$date2 = date_create();
					$diff = date_diff($date1 , $date2);

					if (intval($diff->format('%s')) > 0 ) {
						$res_subs["start_date"] = $date2->format("Y-m-d H:i:s");
					}

				}

			}

			// Process access duration
			$access_duration = 0;
			if (empty($res_subs["access_duration"])) $access_duration = course_get_duration($res_subs["combo"], $res_subs["combo_free"], $res_subs["bundle_id"]);
			else $access_duration = intval($res_subs["access_duration"]);

			$end_date;
			$end_date_defined = false;
			if (!empty($res_subs["batch_id"])) {

				$bootcamp_batch = db_query("SELECT * FROM bootcamp_batches WHERE id = ".$res_subs["batch_id"]);
				if (($end_date = date_create_from_format("Y-m-d", $bootcamp_batch[0]["end_date"])) !== false) {

					$start_date = date_create_from_format("Y-m-d H:i:s", $res_subs["start_date"]);
					$data = db_sanitize(json_encode([
						"com" => var_export($end_date > $start_date, true),
						"sd" => $start_date,
						"ed" => $end_date
					]));
					db_exec("INSERT INTO system_log (source, data) VALUES ('subs.dates', $data);");

					if ($end_date > $start_date) {

						$end_date_defined = true;
						$end_date = $end_date->format("Y-m-d H:i:s");

					}

				}

			}
			else {

				$res_subs_meta = db_query("SELECT bundle.batch_end_date, bundle.category FROM course_bundle AS bundle INNER JOIN subs_meta AS meta ON meta.bundle_id = bundle.bundle_id WHERE meta.subs_id = ".$res_subs["subs_id"]);
				if (!empty($res_subs_meta)) {

					$res_subs_meta = $res_subs_meta[0];
					if (!empty($res_subs_meta["batch_end_date"])) {

						if (($end_date = date_create_from_format("Y-m-d", $res_subs_meta["batch_end_date"])) !== false) {

							$start_date = date_create_from_format("Y-m-d H:i:s", $res_subs["start_date"]);
							if ($end_date > $start_date) {

								$end_date_defined = true;
								$end_date = $end_date->format("Y-m-d H:i:s");

							}

						}

					}

				}

			}

			// Process end date
			if (!$end_date_defined) {
				$end_date = strval(date('Y-m-d H:i:s', strtotime("+ ".strval($access_duration)." days", strtotime($res_subs["start_date"]))));
			}

			// sanitize
			$start_date_unsanitized = $res_subs["start_date"];
			$res_subs["start_date"] = db_sanitize($res_subs["start_date"]);
			$end_date = db_sanitize($end_date);

			// Set the subs record
			db_exec("UPDATE subs SET status='pending', start_date=".$res_subs["start_date"].", end_date=".$end_date." WHERE pay_id=".db_sanitize($res_paylink["pay_id"]).";");

			// Get all installments
			$instl_arr = db_query("SELECT * FROM payment_instl WHERE pay_id=".db_sanitize($res_paylink["pay_id"])." ORDER BY instl_count ASC;");

			// Set due dates for next installments
			$instl_total = intval($res_payment["instl_total"]);
			$instl_count = 2;
			$dayslapsed = 0;

			while($instl_count <= $instl_total) {

				// Prep
				$instl = $instl_arr[$instl_count - 1];
				$dayslapsed += $instl["due_days"];
				$due_date = strval(date('Y-m-d H:i:s', strtotime("+ ".strval($dayslapsed)." days", strtotime($start_date_unsanitized))));

				$due_date = validate_due_date($res_payment["subs_id"], $due_date);

				// Sanitize
				$due_date = db_sanitize($due_date);

				$query = "UPDATE payment_instl SET status='enabled', ".(empty($instl["due_date"]) ? "due_date=$due_date" : "")." WHERE instl_id=".db_sanitize($instl["instl_id"]).";";

				// Update instl table
				db_exec($query);

				// Update links table
				db_exec("UPDATE payment_link SET status='enabled' WHERE paylink_id=".db_sanitize($instl["paylink_id"]).";");

				// Next
				$instl_count ++;

			}

		}

		// All done !
		return true;

	}

	function validate_due_date($subs_id, $due_date) {

		if (empty($due_date)) {
			return $due_date;
		}

		$res_batch_info = db_query(
			"SELECT
				DATE_ADD(DATE(bb.start_date), INTERVAL 2 MONTH) AS start_date
			FROM
				bootcamp_batches AS bb
			INNER JOIN
				subs_meta AS m
				ON m.batch_id = bb.id
			WHERE
				m.bundle_id IN (126, 127)
				AND m.subs_id = $subs_id
				AND DATE_ADD(DATE(bb.start_date), INTERVAL 2 MONTH) < DATE(".db_sanitize($due_date).");"
		);
		if (!empty($res_batch_info)) {
			$due_date = $res_batch_info[0]["start_date"];
		}

		return $due_date;

	}


    function generateApplicationNumber($subs_id, $format){

        if (empty($format)) {
            return false;
        }

        // find last application number created.
        $last_app_num = db_query( "SELECT `app_num` FROM `payment`  WHERE app_num LIKE 'P%' AND app_num IS NOT NULL ORDER BY `create_date`  DESC LIMIT 1");
        if (!empty($last_app_num)) {
            $last_app_num = intval(substr($last_app_num[0]['app_num'], 5));
            $last_app_num++;
        }
        else {
            $last_app_num = "1000";
        }

        $application_number = "P" . date('m') . date("d") . $last_app_num;

        // save application number in payment table
        db_exec("UPDATE payment SET app_num = " . db_sanitize($application_number) . " WHERE subs_id = " . $subs_id . ";");

        return $application_number;

    }

    function generateApplicationNumberIPBA($subs_id, $format){

        if (empty($format)) {
            return false;
        }

        // find last application number created.
        $last_app_num = db_query( "SELECT `app_num` FROM `payment`  WHERE app_num LIKE 'IP%' AND app_num IS NOT NULL ORDER BY `create_date` DESC LIMIT 1");
        if (!empty($last_app_num)) {
            $last_app_num = intval(substr($last_app_num[0]['app_num'], 6));
            $last_app_num++;
        }
        else {
            $last_app_num = "1000";
        }

        $application_number = "IP" . date('m') . date("d") . $last_app_num;

        // save application number in payment table
        db_exec("UPDATE payment SET app_num = " . db_sanitize($application_number) . " WHERE subs_id = " . $subs_id . ";");

        return $application_number;

    }

    function getApplicationNumber($subs_id, $format) {

    	if ($format == "P") {
    		return generateApplicationNumber($subs_id, $format);
    	}
    	elseif ($format == "IP") {
    		return generateApplicationNumberIPBA($subs_id, $format);
    	}

    	return false;

    }

?>
