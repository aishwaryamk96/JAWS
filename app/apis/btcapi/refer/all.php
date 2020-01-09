<?php

	// Prevent exclusive access
	if (!defined("JAWS")) {
		header("HTTP/1.1 401 Unauthorized");
		die();
	}

	// Init Session
	// auth_session_init();

	// // Auth Check - Expecting Session Only !
	// if ((!auth_session_is_logged()) || (!auth_session_is_allowed("support"))) {
	// 	header("HTTP/1.1 401 Unauthorized");
	// 	die();
	// }

	// Send Headers
	header('Access-Control-Allow-Credentials: true');
	header("Content-Type: application/json");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Pragma-directive: no-cache");
	header("Cache-directive: no-cache");
	header("Cache-Control: private, must-revalidate, max-age=0, no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
	header("Pragma: no-cache");

	$date = new DateTime();
	$where = "";
	if (!empty($_GET["user"])) {

		load_module("user");
		$user = user_get_by_email($_GET["user"]);
		if (empty($user)) {

			if (!empty(($act = db_query("SELECT act_id FROM system_activity WHERE act_type = 'jlc.user.not_found' AND activity = ".db_sanitize($_GET["user"]).";")))) {
				$where = "refer.referrer_type = 'system_activity' AND refer.referrer_id = ".$act[0]["act_id"];
			}
			else {
				$where = "referrer_type = 'blah'";
			}

		}
		else {
			$where = "refer.referrer_type = 'user' AND refer.referrer_id = ".$user["user_id"];
		}

	}

	if (!empty($where)) {
		$where = "WHERE ".$where;
	}

	$count = [
		"invite_expired" => [
			"enrolled" => 0,
			"registered" => 0,
			"no_action" => 0,
			"default" => 0,
			"total" => 0,
		],
		"registered" => 0,
		"enrolled" => 0,
		"can_claim" => 0,
		"claim_reward" => 0,
		"awaiting_approval" => 0,
		"voucher_awarded" => 0,
		"fee_adjusted" => 0,
		"no_action" => 0
	];

	$refer_all = db_query("SELECT
								refer.id,
								refer.email,
								refer.name,
								refer.phone,
								DATE_FORMAT(refer.create_date, '%b %e, %Y') AS create_date,
								DATE_FORMAT(refer.expiry_date, '%b %e, %Y') AS expiry_date,
								refer.coupon_code,
								refer.status,
								refer.courses,
								refer.course_bundles AS bundles,
								DATE_FORMAT(refer.claim_date, '%b %e, %Y') AS claim_date,
								DATE_FORMAT(refer.voucher_awarded_date, '%b %e, %Y') AS voucher_awarded_date,
								refer.resent_count,
								referral.user_id AS referral_id,
								refer.referrer_type,
								IF (refer.referrer_type = 'user', referrer.user_id, NULL) AS referrer_id,
								IF (refer.referrer_type = 'user', referrer.email, NULL) AS referrer_email,
								IF (refer.referrer_type = 'user', referrer.name, NULL) AS referrer_name,
								IF (refer.referrer_type = 'system_activity', act.content, NULL) AS referrer_info,
								DATE_FORMAT(referral_meta.reg_date, '%b %e, %Y') AS reg_date,
								refer.amount
							FROM
								refer
							LEFT JOIN
								user AS referrer
								ON referrer.user_id = refer.referrer_id AND refer.referrer_type = 'user'
							LEFT JOIN
								system_activity AS act
								ON act.act_id = refer.referrer_id AND refer.referrer_type = 'system_activity'
							LEFT JOIN
								user AS referral
								ON referral.email = refer.email OR referral.email_2 = refer.email
							LEFT JOIN
								user_meta AS referral_meta
								ON referral_meta.user_id = referral.user_id
							$where
							ORDER BY
								refer.id DESC;
		");

	$output = [];
	foreach ($refer_all as $refer) {

		if (empty($refer["referrer_email"])) {

			$referrer = json_decode($refer["referrer_info"], true);
			$refer["referrer_email"] = $referrer["email"];
			$refer["referrer_name"] = $referrer["name"];

		}
		unset($refer["referrer_info"]);
		$refer["referrer_email"] = strtolower($refer["referrer_email"]);
		$refer["email"] = strtolower($refer["email"]);

		if (in_array($refer["status"], ["no_action", "registered", "enrolled", "invite_expired"])) {

			if (!empty($refer["referral_id"])) {

				if ($refer["status"] == "no_action") {
					$refer["status"] = "registered";
				}
				else if ($refer["status"] == "invite_expired") {
					$refer["invite_expired"] = "registered";
				}

				$subs = db_query("SELECT start_date, subs_id FROM subs WHERE (status='active' OR status='pending') AND user_id=".$refer["referral_id"]." ORDER BY start_date ASC LIMIT 1;");
				if (isset($subs[0])) {

					if (!in_array($refer["status"], ["enrolled", "invite_expired"])) {
						$refer["status"] = "enrolled";
					}
					else if ($refer["status"] == "invite_expired") {
						$refer["invite_expired"] = "enrolled";
					}

					$subs = $subs[0];

					$start_date = date_create_from_format("Y-m-d H:i:s", $subs["start_date"]);

					$refer["enr_date"] = $start_date->format("%F %D, %Y");

					$paid = true;
					if ($start_date->add(new DateInterval("P7D")) < $date) {
						$paid = false;
					}

					if ($paid) {

						$pay = db_query("SELECT pay.status AS pay_status, instl.status AS instl_status FROM payment AS pay INNER JOIN payment_instl AS instl ON instl.pay_id = pay.pay_id WHERE pay.subs_id=".$subs["subs_id"].";");
						foreach ($pay as $instl) {

							if ($instl["instl_status"] != "paid") {

								$paid = false;
								break;

							}

						}
						if ($paid) {

							if (!empty($refer["referrer_email"])) {

								$pay = db_query("SELECT pay.status AS pay_status, instl.status AS instl_status FROM payment AS pay INNER JOIN payment_instl AS instl ON instl.pay_id = pay.pay_id WHERE pay.status = 'paid' AND pay.subs_id=".$subs["subs_id"].";");
								if (isset($pay[0])) {

									foreach ($pay as $instl) {

										if ($instl["instl_status"] != "paid") {

											$paid = false;
											break;

										}

									}
								}

							}

						}

					}

					if ($paid && $refer["status"] != "invite_expired") {
						$refer["status"] = "claim_reward";
					}

				}
				else {

					$refer_date = date_create_from_format("M d, Y", $refer["create_date"]);
					if ($refer_date->add(new DateInterval("P30D")) < $date) {
						$refer["status"] = "invite_expired";
					}

				}

			}
			else {

				$refer_date = date_create_from_format("M d, Y", $refer["create_date"]);
				if ($refer_date->add(new DateInterval("P30D")) < $date) {
					$refer["status"] = "invite_expired";
				}

			}

		}
		else if ($refer["status"] != "invite_expired") {

			$subs = db_query("SELECT DATE_FORMAT(start_date, '%b %e, %Y') AS start_date, subs_id FROM subs WHERE (status='active' OR status='pending') AND user_id=".$refer["referral_id"]." ORDER BY start_date ASC LIMIT 1;");
			if (!empty($subs)) {

				$subs = $subs[0];
				$refer["enr_date"] = $subs["start_date"];

			}

		}

		if ($refer["status"] == "invite_expired") {

			$count["invite_expired"]["default"]++;
			$count["invite_expired"]["total"]++;

		}
		else {
			$count[$refer["status"]]++;
		}

		if (!empty($refer["invite_expired"])) {

			$count["invite_expired"][$refer["invite_expired"]]++;
			$count["invite_expired"]["total"]++;

		}

		if (!in_array($refer["status"], ["invite_expired", "no_action", "registered"])) {
			$count["enrolled"]++;
		}

		$refer["status_name"] = ucwords(str_replace("_", " ", $refer["status"]));

		$output[] = $refer;

	}

	if (!empty($_GET["export"])) {

		load_plugin("phpexcel");

		$refer = [
			[
				"title" => "ASC",
				"data" => $output
			]
		];

		$prop = [
			"title" => "Referral List (".date("F j, Y").")",
		];

		phpexcel_write($refer, $prop, "Referral List (".date("F j, Y").").xls");

	}
	else {
		die(json_encode(["refer" => $output, "count" => $count], JSON_PARTIAL_OUTPUT_ON_ERROR));
	}

?>