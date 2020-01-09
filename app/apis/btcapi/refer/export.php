<?php

	// Prevent exclusive access
	if (!defined("JAWS")) {
		header("HTTP/1.1 401 Unauthorized");
		die();
	}

	// Init Session
	auth_session_init();

	// Auth Check - Expecting Session Only !
	if ((!auth_session_is_logged()) || (!auth_session_is_allowed("support"))) {
		header("HTTP/1.1 401 Unauthorized");
		die();
	}

	load_plugin("phpexcel");

	$date = new DateTime();

	$refer_all = db_query("SELECT
								refer.email,
								refer.name,
								refer.phone,
								DATE_FORMAT(refer.create_date, '%b %e, %Y') AS create_date,
								DATE_FORMAT(refer.expiry_date, '%b %e, %Y') AS expiry_date,
								refer.status,
								DATE_FORMAT(refer.claim_date, '%b %e, %Y') AS claim_date,
								DATE_FORMAT(refer.voucher_awarded_date, '%b %e, %Y') AS voucher_awarded_date,
								IF (refer.referrer_type = 'user', referrer.email, NULL) AS referrer_email,
								IF (refer.referrer_type = 'user', referrer.name, NULL) AS referrer_name,
								IF (refer.referrer_type = 'system_activity', act.content, NULL) AS referrer_info,
								referral_meta.reg_date
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
								ON referral.email = refer.email
							LEFT JOIN
								user_meta AS referral_meta
								ON referral_meta.user_id = referral.user_id
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

		if (in_array($refer["status"], ["no_action", "registered", "enrolled"])) {

			if (!empty($refer["referral_id"])) {

				if ($refer["status"] == "no_action") {
					$refer["status"] = "registered";
				}

				$subs = db_query("SELECT start_date, subs_id FROM subs WHERE (status='active' OR status='pending') AND user_id=".$refer["referral_id"]." ORDER BY start_date ASC LIMIT 1;");
				if (isset($subs[0])) {

					if ($refer["status"] != "enrolled") {
						$refer["status"] = "enrolled";
					}

					$subs = $subs[0];
					$refer["enr_date"] = $subs["start_date"];
					$pay = db_query("SELECT pay.status AS pay_status, instl.status AS instl_status FROM payment AS pay INNER JOIN payment_instl AS instl ON instl.pay_id = pay.pay_id WHERE pay.subs_id=".$subs["subs_id"].";");
					$paid = true;
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

					if ($paid) {
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

			$subs = db_query("SELECT start_date, subs_id FROM subs WHERE (status='active' OR status='pending') AND user_id=".$refer["referral_id"]." ORDER BY start_date ASC LIMIT 1;");
			$subs = $subs[0];
			$refer["enr_date"] = $subs["start_date"];

		}

		$refer["status"] = ucwords(str_replace("_", " ", $refer["status"]));

		$output[] = $refer;

	}

	$cols = ["email", "name", "phone", "referred_on", "expired_on", "status", "claimed_on", "voucher_awarded_on", "referrer_email", "referrer_name", "registered_on", "enrolled_on"];

	phpexcel_write([
			0 => [
				"title" => "Referrals",
				"cols" => array_map(function($a) { return ucwords(str_replace("_", " ", $a)); }, $cols),
				"data" => $output
			]
		],

		["title" => "Referrals (".date("F j, Y").")"],

		"Referrals (".date("F j, Y").").xls"
	);

?>