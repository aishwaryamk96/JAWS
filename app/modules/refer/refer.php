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

/* refer module
  Author: Nikita Soni
  Date: 16 March 2017
*/

	// Prevent exclusive access
	if (!defined("JAWS")) {
		header('Location: https://www.jigsawacademy.com');
		die();
	}

	function refer_create($referrer_type, $referrer_id, $email, $name, $phone, $coupon_code, $courses = null, $course_bundles = null) {

		$referrer_type = db_sanitize($referrer_type);
		$email = db_sanitize($email);
		$name = db_sanitize($name);
		$phone = db_sanitize($phone);
		$coupon_code = db_sanitize($coupon_code);

		if ($courses !== null) {

			if (is_array($courses)) {
				$courses = implode(";", $courses);
			}
			$courses = db_sanitize($courses);

		}

		if ($course_bundles !== null) {

			if (is_array($course_bundles)) {
				$course_bundles = implode(";", $course_bundles);
			}
			$course_bundles = db_sanitize($course_bundles);

		}
		$create_date = new DateTime("now");
		$create_date = db_sanitize($create_date->format("Y-m-d H:i:s"));

		db_exec("INSERT INTO refer (referrer_type, referrer_id, name, email, phone, create_date, coupon_code".(!empty($courses) ? ", courses" : "").(!empty($course_bundles) ? ", course_bundles" : "").") VALUES (".$referrer_type.",".$referrer_id.",".$name.",".$email.",".$phone.",".$create_date.",".$coupon_code.(!empty($courses) ? ",".$courses : "").(!empty($course_bundles) ? ",".$course_bundles : "").");");

		return db_get_last_insert_id();

	}

	function refer_edit($refer) {

		$referrer_type = db_sanitize($refer["referrer_type"]);
		$email = db_sanitize($refer["email"]);
		$name = db_sanitize($refer["name"]);
		$phone = db_sanitize($refer["phone"]);
		$coupon_code = db_sanitize($refer["coupon_code"]);
		$status = db_sanitize($refer["status"]);

		$courses = $refer["courses"];
		if (!empty($courses)) {

			if (is_array($courses)) {
				$courses = implode(";", $courses);
			}
			$courses = db_sanitize($courses);

		}

		$course_bundles = $refer["course_bundles"];
		if (!empty($course_bundles)) {

			if (is_array($course_bundles)) {
				$course_bundles = implode(";", $course_bundles);
			}
			$course_bundles = db_sanitize($course_bundles);

		}
		$create_date = db_sanitize($refer["create_date"]);

		db_exec("UPDATE refer SET referrer_type=".$referrer_type.", referrer_id=".$refer["referrer_id"].", name=".$name.", email=".$email.", phone=".$phone.", create_date=".$create_date.", coupon_code=".$coupon_code.(!empty($courses) ? ", courses=".$courses : "").(!empty($course_bundles) ? ", course_bundles=".$course_bundles : "").", status=".$status." WHERE id=".$refer["id"].";");

	}

	function refer_get_by_email($email) {

		$email = db_sanitize($email);

		$result = db_query("SELECT * FROM refer WHERE email=".$email);
		if (!isset($result[0])) {
			return false;
		}

		return $result[0];

	}

	function refer_get_by_id($id) {

		$result = db_query("SELECT * FROM refer WHERE id=".$id);
		if (!isset($result[0])) {
			return false;
		}

		return $result[0];

	}

	function refer_get_by_referrer($referrer_type, $referrer_id, $descending = true) {

		$referrer_type = db_sanitize($referrer_type);

		$result = db_query("SELECT * FROM refer WHERE referrer_type=".$referrer_type." AND referrer_id=".$referrer_id." ORDER BY id ".($descending ? "DESC;" : "ASC;"));
		if (!isset($result[0])) {
			return false;
		}

		return $result;

	}

	function refer_get_by_date($date, $descending = true) {

		if (!is_a($date, "DateTime")) {

			$date = new DateTime("Y-m-d H:i:s", $date);
			if ($date === false) {
				return false;
			}
			$date = $date->format("Y-m-d H:i:s");

		}
		$date = $date->format("Y-m-d H:i:s");

		$result = db_query("SELECT * FROM refer WHERE create_date=".$date." ORDER BY ".$descending ? "DESC;" : "ASC;");
		if (!isset($result[0])) {
			return false;
		}

		return $result;

	}

	function refer_get_all($descending = true) {
		return db_query("SELECT * FROM refer ORDER BY ".$descending ? "DESC;" : "ASC;");
	}

	function refer_get_statistics($referrer_id = false, $referrer_type = "user") {

		refer_assoc_id();

		$today = new DateTime;
		$days7 = new DateTime;
		$days7->add(new DateInterval("P7D"));

		$where = "";
		if (!empty($referrer_id)) {
			$where = "ref.referrer_id = ".$referrer_id." AND ref.referrer_type = ".db_sanitize($referrer_type);
		}

		$res = db_query(
			"SELECT
				ref.*,
				subs.start_date AS subs_start_date,
				subs.status AS subs_status,
				IF (subs.subs_id IS NOT NULL, GROUP_CONCAT(DISTINCT instl.instl_id, '=', instl.status SEPARATOR '+'), NULL) AS pay_info,
				user.email AS ref_email,
				user.name AS ref_name,
				sa.content AS ref_content
			FROM
				refer AS ref
			LEFT JOIN
				user
				ON user.user_id = ref.referrer_id AND ref.referrer_type = 'user'
			LEFT JOIN
				system_activity AS sa
				ON sa.act_id = ref.referrer_id AND ref.referrer_type = 'system_activity'
			LEFT JOIN
				subs
				ON subs.user_id = ref.referral_id
			LEFT JOIN
				payment AS pay
				ON pay.subs_id = subs.subs_id
			LEFT JOIN
				payment_instl AS instl
				ON instl.pay_id = pay.pay_id
			GROUP BY
            	ref.id;"
		);

		if (empty($res)) {
			return [];
		}

		$result = [];
		foreach ($res as $ref) {

			$dirty = false;

			// If status is below claimable
			if ($ref["status_id"] < 3) {

				// If subs are present
				if (!empty($ref["subs_status"])) {

					// If payment has been made
					if (in_array($ref["subs_status"], ["pending", "active"])) {

						// If status is not 'enrolled', make it 'enrolled'
						if ($ref["status_id"] < 2) {

							$ref["status_id"] = 2;
							$ref["status"] = "enrolled";
							$dirty = true;

						}

						$can_claim = false;
						$start_date = date_create_from_format("Y-m-d H:i:s", $ref["subs_start_date"]);
						if ($start_date > $days7) {

							$instl_info = explode("+", $ref["pay_info"]);
							foreach ($instl_info as $instl) {

								$info = explode("=", $instl);
								if ($info[1] != "paid") {
									$can_claim = false;
								}
								else {
									$can_claim = true;
								}

							}

						}

						if ($can_claim) {

							$ref["status"] = "claim_reward";
							$ref["status_id"] = 3;
							$dirty = true;

						}

					}
					else if (refer_is_expired($ref)) {

						$ref["status"] = "invite_expired";
						$ref["status_id"] = -1;
						$dirty = true;

					}

				}
				else {

					if (refer_is_expired($ref)) {

						$ref["status"] = "invite_expired";
						$ref["status_id"] = -1;
						$dirty = true;

					}
					else if ($ref["status_id"] == 0 && !empty($ref["referral_id"])) {

						$ref["status"] = "registered";
						$ref["status_id"] = 1;
						$dirty = true;

					}

				}

			}

			$ref["status_id"] = intval($ref["status_id"]);

			if (empty($ref["ref_email"])) {

				$referrer = json_decode($ref["ref_content"], true);
				$ref["ref_email"] = $referrer["email"];
				$ref["ref_name"] = $referrer["name"];

			}
			unset($ref["ref_content"]);

			$result[] = $ref;

		}

		return $result;

	}

	function refer_assoc_id() {

		$res = db_query("SELECT id, email, phone FROM refer WHERE referral_id IS NULL;");
		if (empty($res)) {
			return;
		}

		load_module("user");

		foreach ($res as $ref) {

			$user = user_get_by_email($ref["email"]);
			if (empty($user)) {

				$res_user = db_query("SELECT * FROM user WHERE phone LIKE ".db_sanitize($ref["phone"]).";");
				if (!empty($res_user)) {
					// Do something about it...
				}

			}
			else {
				db_exec("UPDATE refer SET referral_id = ".$user["user_id"]." WHERE id = ".$ref["id"].";");
			}

		}

	}

	function refer_is_expired($ref) {

		$ref_date = date_create_from_format("Y-m-d H:i:s", $ref["create_date"]);
		$ref_date->add(new DateInterval("P30D"));
		if ($ref_date < (new DateTime)) {
			return true;
		}

		return false;

	}

?>