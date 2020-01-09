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

  	// This task is executed to update installments and send payment reminders to students
  	// This is a taken up by a daily CRON job

  	// Remove This Line To execute This Task < =============================================================================================================
  	//die("'subs.instl.notify' Task is Ready! Please enable this task by removing this line from the code!");

  	// Load Stuff
  	load_library("email");
  	load_library("sms");
  	load_module("course");
  	load_module("subs");

  	// Get all payments which have any installment other than first with 'enabled' or 'due' status (as in not paid already or disabled by admin)
  	$res = db_query("SELECT
  			pay.user_id AS user_id,
  			pay.subs_id AS subs_id,
            pay.type AS receipt_type,

  			instl.pay_id AS pay_id,
  			instl.instl_id AS instl_id,
  			instl.paylink_id AS paylink_id,
  			instl.instl_count AS instl_count,
  			instl.instl_total AS instl_total,
  			instl.sum AS sum,
  			instl.currency AS currency,
  			instl.due_date AS due_date,
  			instl.notify_count AS notify_count,
  			instl.status AS status,

  			subs.combo AS combo,
  			subs.combo_free as combo_free,

  			user.name AS name,
  			user.email AS email,
  			user.phone AS phone

  		FROM payment_instl AS instl

  		INNER JOIN payment AS pay
  			ON instl.pay_id = pay.pay_id
  		INNER JOIN subs AS subs
  			ON instl.subs_id = subs.subs_id
  		INNER JOIN user AS user
  			ON instl.user_id = user.user_id

  		WHERE
  			(instl.instl_count > 1) AND (instl.status = 'enabled' OR instl.status = 'due') AND (pay.status = 'paid')
  		GROUP BY
  			instl.pay_id
  		ORDER BY
  			instl.pay_id ASC, instl.instl_count ASC;"
  	);

  	if (count($res) == 0) die();

  	// prep
  	$notify_internal;
  	$notify_user_template_emails = array("0" => "subs.instl.notify.warn", "2" => "subs.instl.notify.remind", "7" => "subs.instl.notify.due");
  	$notify_user_template_sms = array(
  		"0" => "Please pay your installment today to continue to access your Jigsaw course. Please check your email for more details.",
  		"2" => "Dear student, this is a reminder to pay your due installment towards your Jigsaw course. Details in the email from us."
  		);
  	$date_curr = date('Y-m-d H:i:s');

  	// Process each of these
  	foreach ($res as $row) {

  		// Find due days remaining
  		$daysdiff = floor((strtotime($row["due_date"]) - strtotime($date_curr))/(60*60*24));
  		$notify_count = intval($row["notify_count"]);

		// prep
		$notify_info;
		$subs;
		$flag_notify_internal = false;
		$flag_notify_user = false;
		$notify_stage = "";
		$flag_notify_prepped = false;

		// create notification according to days difference
		// Internal followups and notification to student are separated from expiry condition coz if notification was not sent to student, it will be sent during the grace period of two days

		// Send Internal notifications
		if ($daysdiff <= -20) {

			db_exec("UPDATE payment_instl SET status='disabled' WHERE pay_id=".$row["pay_id"]." AND status != 'paid';");
			db_exec("UPDATE payment_link SET status='expired' WHERE pay_id=".$row["pay_id"]." AND status != 'used';");

		}
		else if (($daysdiff == -2) && ($notify_count > 0)) {
		// Change the == to <= later . It is == so that this internal email is sent only once per student. Later on the system will have the facility to change the status of an entire payment stream as blocked or expired to avoid it being run multiple times.

			$flag_notify_internal = true;
			$notify_stage = "-2";

		}

		// Send Student Notification
		if (($daysdiff <= 0) && ($notify_count < 3)) {
			$flag_notify_user = true;
			$flag_notify_internal = true;
			$notify_stage = "0";
			db_exec("UPDATE payment_instl SET notify_count=3, status='due' WHERE instl_id=".$row["instl_id"].";");
		}

		else if (($daysdiff <= 2) && ($notify_count < 2)) {
			$flag_notify_user = true;
			$flag_notify_internal = true;
			$notify_stage = "2";
			db_exec("UPDATE payment_instl SET notify_count=2, status='due' WHERE instl_id=".$row["instl_id"].";");
		}

		else if (($daysdiff <= 7) && ($notify_count < 1)) {
			$flag_notify_user = true;
			$notify_stage = "7";
			db_exec("UPDATE payment_instl SET notify_count=1, status='due' WHERE instl_id=".$row["instl_id"].";");
		}

		// notify internal
		if ($flag_notify_internal) {

			// get paylink
			$paylink_res = db_query("SELECT web_id FROM payment_link WHERE instl_id=".$row["instl_id"]." LIMIT 1;");

			// Prep Email content
			$notify_info["name"] = $row["name"];
			$notify_info["fname"] = substr($row["name"], 0, ((strpos($row["name"], " ") !== false) ? strpos($row["name"], " ") : strlen($row["name"])));
			$notify_info["email"] = $row["email"];
			$notify_info["phone"] = $row["phone"];
			$notify_info["due_date"] = $row["due_date"];
			$notify_info["paylink_id"] = $paylink_res[0]["web_id"];
			$notify_info["instl_count"] = strval($row["instl_count"]);
			$notify_info["instl_total"] = strval($row["instl_total"]);
			$notify_info["sum"] = $row["sum"];
			$notify_info["currency"] = $row["currency"];
			$notify_info['coursestr'] = course_get_short_code_str($row["combo"].((isset($row["combo_free"]) && (strlen($row["combo_free"]) > 0)) ? ";".$row["combo_free"] : ""));

			$flag_notify_prepped = true;
			$notify_internal[$notify_stage][count($notify_internal[$notify_stage])] = $notify_info;

		}

		// notify student
		if ($flag_notify_user) {

			if (!$flag_notify_prepped) {

				// get paylink
				$paylink_res = db_query("SELECT web_id FROM payment_link WHERE instl_id=".$row["instl_id"]." LIMIT 1;");

				// Prep email Content
				$notify_info["fname"] = substr($row["name"], 0, ((strpos($row["name"], " ") !== false) ? strpos($row["name"], " ") : strlen($row["name"])));
				$notify_info["email"] = $row["email"];
				$notify_info["phone"] = $row["phone"];
				$notify_info["due_date"] = $row["due_date"];
				$notify_info["paylink_id"] = $paylink_res[0]["web_id"];
				$notify_info["instl_count"] = strval($row["instl_count"]);
				$notify_info["instl_total"] = strval($row["instl_total"]);
				$notify_info["sum"] = $row["sum"];
				$notify_info["currency"] = $row["currency"];
				$notify_info["receipt_type"] = $row["receipt_type"];
			}

			// Prep Instl Info array
			$pay_info = payment_get_info($row["pay_id"]);
			$notify_info["instl"] = $pay_info["instl"];

			// Email & SMS
			send_email($notify_user_template_emails[$notify_stage], array("to" => $notify_info["email"]), $notify_info);
			if (isset($notify_user_template_sms[$notify_stage])) { 
                send_sms($notify_info["phone"], $notify_user_template_sms[$notify_stage]);
            }
		}
  	}

  	// Send internal email
  	send_email("subs.instl.report", array(), $notify_internal);

  	// All done
  	die();

?>
