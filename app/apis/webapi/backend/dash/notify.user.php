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

	register_shutdown_function(function() {
		if (!empty($errors = error_get_last())) {
			echo json_encode($errors);
		}
	});

	// Init Session
	auth_session_init();

	// Auth Check - Expecting Session Only !
	if ((!auth_session_is_logged()) || (!auth_session_is_allowed("dash"))) {
		header("HTTP/1.1 401 Unauthorized");
		die();
	}

	if (count($_POST) == 0) {
		$_POST = json_decode(file_get_contents('php://input'), true);
	}

	load_module("user");
	load_module("course");
	load_library("email");
	load_module("subs");
    load_library("payment");
    load_plugin('mpdf');

	// TEST MODE ON !!!
	//$GLOBALS['jaws_exec_live'] = false;

	$context = $_POST["context"]; // what type of email to be sent.

	$email = $_POST["email"]; // email id of user to whom email to be sent.
	$sub_id = $_POST["sub_id"];  // subscription id to identify package details of the user.
	$instl_num = $_POST["instl"];  // if instalment alert being sent, specify for which instalment email to be sent.
	$pay_id = $_POST['pay_id']; // web id of the payment link to get using db query using payid and instl number
	$post_data = $_POST; // full post data to be used anywhere if required.

	$error = ""; $message = "";

	if( empty($context) ){
		$error = "Please provide context.";
	}

	if( empty($email) ){
		$error = "Please provide email.";
	}

	if( $context !== "lms.setup" ){

		if( empty($sub_id) ){
			$error = "Please provide Subscription ID.";
		}

		if( empty($instl_num) ){
			$error = "Please provide Instalment Number.";
		}

		if( empty($pay_id) ){
			$error = "Please provide Payment ID.";
		}

		if( $context == "disable_package" && empty($_POST["disable_type"]) ){
			$error = "Please select Disable Option.";
		}

		if( $context == "disable_package" && empty($_POST["comment"]) ){
			$error = "Please provide reason for Disabling.";
		}

		if( $context == "disable_package" && $_POST["disable_type"] == "paid" && empty($_POST["disable_mode"]) ){
			$error = "Please provide Payment Mode.";
        }

		if( empty($error) ){

			if( !empty($instl_num) && $context == "disable_package" ){
				// for disable - consecutive unpaid payment link and instl disable set status disable
				$disable = $_POST["disable_type"];
				$comment = $_POST["comment"];
				$mode = $_POST["disable_mode"];
				$payment_details_d = payment_get_info($pay_id);
				if($disable == "disable"){
					// disable current and all upcoming installments.
					// payment_link and payment_instl = disable
					// payment_instl assoc_entity_id = logged in user id, pay_comment = comment and assoc_entity_type = user.
					foreach($payment_details_d['instl'] as $instalment){
						if($instalment['instl_count'] >= $instl_num ){
							db_query("UPDATE `payment_instl` SET `status` = 'disabled', `pay_comment` = " . db_sanitize($comment) . ", `assoc_entity_type` = 'user', `assoc_entity_id` = '" . $_SESSION['user']['user_id'] . "'  WHERE `payment_instl`.`instl_id` = '".$instalment['instl_id']."';");
							db_query("UPDATE `payment_link` SET `status` = 'disabled'  WHERE `payment_link`.`paylink_id` = '".$instalment['paylink_id']."';");
						}
					}
					$disable = 'disabled';
				} elseif ( $disable == "paid" ){
					$now = new DateTime;
					// mark this installment as paid.
					// payment_link = disable, payment_instl = paid.
					// payment_instl assoc_entity_id = logged in user id, pay_comment = comment and assoc_entity_type = user.
					foreach($payment_details_d['instl'] as $instalment){

						if($instalment['instl_count'] == $instl_num ){
							db_query("UPDATE `payment_instl` SET `status` = 'paid', `pay_date` = ".db_sanitize($now->format("Y-m-d H:i:s")).", `pay_comment` = " . db_sanitize($comment) . ", `pay_mode` = " . db_sanitize($mode) . ", `assoc_entity_type` = 'user', `assoc_entity_id` = '" . $_SESSION['user']['user_id'] . "'  WHERE `payment_instl`.`instl_id` = '".$instalment['instl_id']."';");
							db_query("UPDATE `payment_link` SET `status` = 'disabled'  WHERE `payment_link`.`paylink_id` = '".$instalment['paylink_id']."';");

							if ($instl_num > 1) {
								db_exec("INSERT INTO user_logs (user_id, category, created_by, status) VALUES (".$payment_details_d["user_id"].", 'access.grant', ".$payment_details_d["user_id"].", 'pending');");
							}
						}
						else if ($instalment['instl_count'] > $instl_num && $_POST['disable_date_update'] != true ) {

							$interval = "P".$instalment["due_days"]."D";
							$now->add(new DateInterval($interval));

							db_query("UPDATE `payment_instl` SET status='enabled', due_date = ".db_sanitize($now->format("Y-m-d H:i:s"))."WHERE `instl_id` = '".$instalment['instl_id']."'");
							db_query("UPDATE `payment_link` SET `status` = 'enabled' , expire_date = ".db_sanitize($now->format("Y-m-d H:i:s"))." WHERE `payment_link`.`paylink_id` = '".$instalment['paylink_id']."';");

						}//JA-164 changes starts
                                                else if ($instalment['instl_count'] > $instl_num && $_POST['disable_date_update'] == true ) {

							$interval = "P".$instalment["due_days"]."D";
							$now->add(new DateInterval($interval));

							db_query("UPDATE `payment_instl` SET status='enabled' WHERE `instl_id` = '".$instalment['instl_id']."'");
							db_query("UPDATE `payment_link` SET `status` = 'enabled' , expire_date = ".db_sanitize($now->format("Y-m-d H:i:s"))." WHERE `payment_link`.`paylink_id` = '".$instalment['paylink_id']."';");

						}//JA-164 changes ends
					}

					$subs_id = $payment_details_d["subs_id"];

					$subs = db_query("SELECT * FROM subs WHERE subs_id = $subs_id;");
					if (!empty($subs)) {

						$subs = $subs[0];
						if ($subs["status"] == "inactive") {
							subs_update_status($subs_id, "pending");
						}
						else {
							subs_update_status($sub_id, "active");
						}

					}
					else {
						subs_update_status($sub_id, "active");
					}

				}
				if($instl_num == 1){
					db_query("UPDATE `payment` SET `status` = " . db_sanitize($disable) . " WHERE `payment`.`pay_id` = " . db_sanitize($pay_id) . ";");
				}
			}

			// enable payment link
			if( !empty($instl_num) && $context == "enable_payment_link" ){
				// enable all consecutive links. do not send any emails. for email send use notify button.
				// payment_instl status enabled for selected and all next
				// payment_instl due date + 7 days for selected and + 7 for subsequent if expired from selected instl due date.
				// payment_link status enabled
				$data = db_query("SELECT * FROM `payment_instl` WHERE subs_id=".db_sanitize($sub_id)." AND pay_id=".db_sanitize($pay_id).";");
                $instl_array = array();
                $date = date('Y-m-d H:i:s');
				foreach( $data as $d ){
					if (!empty($d["pay_date"]) && $d['status'] != 'paid') {
						// if pay date is available then already paid. update status to paid if not already marked as paid.
						// this condition for all payments which are marked as disabled by cron jobs or something else.
						db_query("UPDATE `payment_instl` SET `status` = 'paid' WHERE `instl_id` = ".db_sanitize($d['instl_id']).";");
					} else if(($d['instl_count'] == ($instl_num -1)) && $d['status'] != 'paid'){
						echo json_encode(array( "status" => false, "message" => 'Previous instalment is not paid.' ));
						exit;
					} else if( $d['instl_count'] >= $instl_num && $d['status'] == 'disabled' ){
						array_push($instl_array, $d['instl_count']);
                        $date = strtotime("+7 day", strtotime($date));
                        $query_instl = "UPDATE `payment_instl` SET `status` = 'enabled'";
                        $query_link = "UPDATE `payment_link` SET `status` = 'enabled'";
						if(!empty($d['due_date']) && ($date <= strtotime($d['due_date'])) ){
                            // current available instl due date is after the calculated new due date. no need to update due date.
                            // do nothing
						} else if ( !empty($d['due_date']) && ($date > strtotime($d['due_date'])) && ($_POST['disable_date'] != true) ){
							// current available instl due date is before the calculated new due date. update due date to give more time to user to pay.
							$query_instl .= ", due_date = ".db_sanitize(date('Y-m-d H:i:s',$date));
							$query_link .= ", `expire_date` = ".db_sanitize(date('Y-m-d H:i:s',$date));
						} else if( $_POST['disable_date' != true] ) {
                            // no due date available. update with calculated new date.
							$query_instl .= ", due_date = ".db_sanitize(date('Y-m-d H:i:s',$date));
							$query_link .= ", `expire_date` = ".db_sanitize(date('Y-m-d H:i:s',$date));
                        }
						/* if(!empty($d['due_date']) && ($date <= strtotime($d['due_date'])) ){
							// current available instl due date is after the calculated new due date. no need to update due date.
							db_query("UPDATE `payment_instl` SET `status` = 'enabled' WHERE `instl_id` = ".db_sanitize($d['instl_id']).";");
							db_query("UPDATE `payment_link` SET `status` = 'enabled' WHERE `paylink_id` = ".db_sanitize($d['paylink_id']).";");
						} else if ( !empty($d['due_date']) && ($date > strtotime($d['due_date'])) ){
							// current available instl due date is before the calculated new due date. update due date to give more time to user to pay.
							db_query("UPDATE `payment_instl` SET status='enabled', due_date = ".db_sanitize(date('Y-m-d H:i:s',$date))."WHERE `instl_id` = ".db_sanitize($d['instl_id'])."");
							db_query("UPDATE `payment_link` SET `status` = 'enabled' , `expire_date` = ".db_sanitize(date('Y-m-d H:i:s',$date))." WHERE `paylink_id` = ".db_sanitize($d['paylink_id']).";");
						} else {
							// no due date available. update with calculated new date.
							db_query("UPDATE `payment_instl` SET status='enabled', due_date = ".db_sanitize(date('Y-m-d H:i:s',$date))."WHERE `instl_id` = ".db_sanitize($d['instl_id'])."");
							db_query("UPDATE `payment_link` SET `status` = 'enabled' , expire_date = ".db_sanitize(date('Y-m-d H:i:s',$date))." WHERE `paylink_id` = ".db_sanitize($d['paylink_id']).";");
                        } */
                        db_exec($query_instl. " WHERE `instl_id` = " . db_sanitize($d['instl_id']) . ";");
                        db_exec($query_link. " WHERE `paylink_id` = " . db_sanitize($d['paylink_id']) . ";");
						$date = date('Y-m-d H:i:s',$date);
					}
				}
				echo json_encode(array( "status" => true, "message" => 'Instalment number '.implode(", ",$instl_array).' have been enabled.' ));
				exit;
			}

            $subs_info = subs_get_info($sub_id);
            $subs_meta = $subs_info['meta'];
			$payment_details = payment_get_info($subs_info["pay_id"]);

			// Prep courses
			$combo_arr = course_get_combo_arr($subs_info["combo"].";".$subs_info["combo_free"]);
			$combo_arr_free_exclusive = course_get_combo_arr($subs_info["combo_free"]);
			$course;
			$count = 0;
			foreach($combo_arr as $course_id => $learn_mode) {

				$res = db_query("SELECT * FROM course WHERE course_id=".$course_id." LIMIT 1;");
				$res_meta = db_query("SELECT * FROM course_meta WHERE course_id=".$course_id." LIMIT 1;");

				if (!isset($res[0]["name"])) {
					continue;
				}
				if (!isset($res_meta[0])) {
					continue;
				}

				$course_content = json_decode($res_meta[0]["content"], true);

				$course[$count]["name"] = $res[0]["name"];
				$course[$count]["learn_mode"] = ((strcmp($learn_mode, "1") == 0)? "Premium" : "Regular");
				$course[$count]["desc"] = $res_meta[0]["desc"];
				$course[$count]["img"] = $course_content["img_main_small"];
				$course[$count]["url"] = $course_content["url_web"];
				$course[$count]["status"] = $res[0]["status"];

				if (isset($combo_arr_free_exclusive[$course_id])){
					$course[$count]["free"] = true;
				} else {
					$course[$count]["free"] = false;
				}

				$count ++;
			}

            $pay_details = db_query("SELECT pl.web_id, pl.paylink_id, pl.create_entity_type, pi.due_date, pi.instl_total FROM `payment_link` AS `pl` JOIN `payment_instl` AS pi ON ( pl.paylink_id = pi.paylink_id ) WHERE pi.pay_id = '" . $pay_id . "' AND pi.instl_count = '" . $instl_num . "'");

            $bundle_details = array(); $batch_details = array();
            if(!empty($subs_meta['bundle_id'])){
                $bundle_details = db_query("SELECT * FROM `course_bundle` WHERE `bundle_id` =". $subs_meta['bundle_id'] . ";");
                $bundle_details = $bundle_details[0];
                if(!empty($subs_meta['batch_id'])){
                    $batch_details = db_query("SELECT * FROM `bootcamp_batches` WHERE `id` =". $subs_meta['batch_id'] . ";");
                    $batch_details = $batch_details[0];
                }
            }

            $content["batch_details"] = $batch_details;
            $content["bundle_details"] = $bundle_details;
			$content["paylink_id"] = $pay_details[0]['web_id'];
			$content["sum"] = $payment_details["instl"][$instl_num]["sum"];
			$content["currency"] = $payment_details["instl"][$instl_num]["currency"];
			$content["courses"] = $course;
			$content["payment"] = $payment_details;
			$content["receipt_type"] = $payment_details['type'];

			// find out if payment done from website or from kform
			$payment_done_through = $pay_details[0]['create_entity_type'];
			// due date for 2nd instalment onwards
			$due_date = $pay_details[0]['due_date'];
			// total number of instalments the user hash
			$instl_total = $pay_details[0]['instl_total'];

		} else {
			echo json_encode(array( "status" => false, "message" => $error ));
		}
	}

	if( empty($error) ){

		$user = user_get_by_email_all($email);

                
                $subsData = db_query("SELECT * FROM subs WHERE subs_id =". db_sanitize($sub_id));
                print_r($subsData);
                $comboCourseArr =array_keys(course_get_combo_arr($subsData[0]['combo']));
                
                //MindCourse
                $content['mindCourseFLag'] = 0;
                if(in_array(302, $comboCourseArr)){
                    $content['mindCourseFLag'] = 1;
                }else if(in_array(142, $comboCourseArr)){
                    $content['mindCourseFLag'] = 1;
                }else if(in_array(144, $comboCourseArr)){
                    $content['mindCourseFLag'] = 1;
                }
                
		// Prep email content
		$content["user_webid"] = $user["web_id"];
		$content["fname"] = substr($user["name"], 0, ((strpos($user["name"], " ") !== false) ? strpos($user["name"], " ") : strlen($user["name"])));

		// choose template to be sent.
		if( !empty($instl_num) && $context != "disable_package" && $context != "send_receipt"){
			// sending instalment related mails
			if ( $instl_total == 1 ){
				// for only single instalment from k form or to resume pay from website.
				// payment_done_through = user i.e. from kform
				// payment_done_through = system i.e. from website
				if( $payment_done_through == "user" ) $context = "subs.init";
				else if ( $payment_done_through == "system" ) $context = "subs.init.re";
			} else {
				// payment using instalment, only possible through kform. for 2nd instalment onwards, check due date. based on due date template changes.
				if( empty($due_date) && $instl_num == 1 ){
					// no due date means first instalment not paid, so mail being sent for first installment
					$context = "subs.init";
                                        if($content['mindCourseFLag'] ==1){
                                                    $template_email = "subs.init.mindschool";
                                        }
				} else {
					$days = floor((strtotime($due_date) - time())/(60*60*24));
					if( $days >= 7 ){
                        $context = "subs.instl.notify.due";
                    } else if( $days < 7 && $days >= 2 ){
                        $context = "subs.instl.notify.remind";
                    } else if( $days < 2/*  && $days >= 0  */){
                        $context = "subs.instl.notify.warn";
                    } else{
                        $context = "none";
                    }
				}
			}
		} else if( !empty($instl_num) && $context == "disable_package" ){

			if( $disable == "paid" && $instl_num != 1 ){
				$context = "subs.instl.success";
                                if($content['mindCourseFLag'] ==1){
                                            $template_email = "subs.instl.mindschool.success";
                                }
			} else if( $disable == "paid" && $instl_num == 1 ) {
				$context = "subs.init.success";
                                if($content['mindCourseFLag'] ==1){
                                    $template_email = "subs.init.mindschool.success";
                                }
			} else {
                $context = "none";
                $message = "Successfully Disabled";
			}
        }

        $receipt_data = array(); $state = '';
        if( !empty($post_data['attach_receipt']) && $post_data['attach_receipt'] == true ){
            if(!empty($post_data['state'])){
                db_query("UPDATE `user_meta` SET `state` = " . db_sanitize($post_data['state']) . "  WHERE `user_meta`.`user_id` = '".$user['user_id']."';");
                $state = $post_data['state'];
            } else {
                $state = db_query("SELECT * FROM user_meta WHERE user_id = " . db_sanitize($user['user_id']));
                $state = $state[0]['state'];
            }
            $instl = db_query("SELECT * FROM  `payment_instl` WHERE pay_id = '" . $pay_id . "' AND instl_count = '" . $instl_num . "'");
            $receipt_data = array(
                'subs_id' => $sub_id,
                'name' => $user["name"],
                'email' => $user["email"],
                'instl' => $instl[0]['instl_id'],
                'state' => $state,
                'watermark' => true
            );
        }

		$sent_mail = false;

		switch($context){

			case "subs.init.success":
                        case "subs.init.mindschool.success":
                            
			// sent to user for first instalment successfully paid. this will have link to setup lms

				if ((!isset($user["lms_soc"])) || (strlen($user["lms_soc"]) == 0)){
					$content["allow_setup"] = true;
				} else {
					$content["allow_setup"] = false;
				}

				// Send Emails
				$template_email = "subs.init.success";
                                if($content['mindCourseFLag'] ==1){
                                            $template_email = "subs.init.mindschool";
                                }
                if(!empty($receipt_data) ){

                    $pdf = new PDFgen($receipt_data);
                    $receipt = $pdf->create_from_subs();

                    $attachments = array(
                        0 => $receipt, // file deleted
                    );

                    send_email_with_attachment($template_email, array("to" => $email), $content, $attachments);

                    $pdf->deleteFileFromServer();
                } else {
                    send_email($template_email, array("to" => $email), $content);
                }


				$sent_mail = true;

			break;

			case "lms.setup":
			// sent to user after successfully setup lms

				$content = array();

				// Prep email !
				$content["fname"] = substr($user["name"], 0, ((strpos($user["name"], " ") !== false) ? strpos($user["name"], " ") : strlen($user["name"])));

				// Done! Send an email
				$template = "lms.setup.success";

				send_email($template, array("to" => $email), $content, true);

				$sent_mail = false;

			break;

			case "subs.instl.success":
                        case "subs.instl.mindschool.success":
			// Sent on successful payment of installment other than first!

				// instl_count > 1
				$content["instl_count"] = $instl_num;

				// Send Emails
				$template_email = "subs.instl.success";
				if($content['mindCourseFLag'] ==1){
                                            $template_email = "subs.instl.mindschool.success";
                                }
                                
				log_activity("payment.email", $bundle_details);
    			if (!empty($bundle_details["platform_id"]) && $bundle_details["platform_id"] == 2) {
				 $template_email .= ".edunxt";
				}

                if(!empty($receipt_data) ){

                    $pdf = new PDFgen($receipt_data);
                    $receipt = $pdf->create_from_subs();

                    $attachments = array(
                        0 => $receipt, // file
                    );

                    send_email_with_attachment($template_email, array("to" => $email), $content, $attachments);

                    $pdf->deleteFileFromServer();
                } else {
                    send_email($template_email, array("to" => $email), $content);
				}
				


				$sent_mail = true;

			break;



			case "subs.init":
                        case "subs.init.mindschool":
				// Sent to users requesting subscription coming by the way of offline subscription AKA custom link. or for first payment link

				if ((!isset($user["lms_soc"])) || (strlen($user["lms_soc"]) == 0)){
					$content["allow_setup"] = true;
				} else {
					$content["allow_setup"] = false;
				}

				

				// Send Emails
				$template_email = "subs.init";
                                if($content['mindCourseFLag'] ==1){
                                    $template_email = "subs.init.mindschool";
                                }
				send_email($template_email, array("to" => $email), $content);

				$sent_mail = true;

			break;

			case "subs.init.re":
				// Sent to users requesting subscription coming from website.

				if ((!isset($user["lms_soc"])) || (strlen($user["lms_soc"]) == 0)){
					$content["allow_setup"] = true;
				} else {
					$content["allow_setup"] = false;
				}

				// Send Emails
				$template_email = "subs.init.re";

				send_email($template_email, array("to" => $email), $content);

				$sent_mail = true;

			break;

			case "subs.instl.notify.warn":
			case "subs.instl.notify.remind":
			case "subs.instl.notify.due":
			// First email notification for installment due.

				// Prep email Content
				$content["email"] = $user["email"];
				$content["phone"] = $user["phone"];
				$content["instl"] = $payment_details["instl"];
				$content["due_date"] = $due_date;
				$content["instl_count"] = strval($instl_num);
				$content["instl_total"] = strval($instl_total);

                // Send Emails
                // $context = "subs.init.success.test";
				$template_email = $context;

				send_email($template_email, array("to" => $email), $content);

				$sent_mail = true;

			break;

            case "send_receipt":
                // receipt will not be generated from here
                $sent_mail = true;
            break;

			case "none":
				$sent_mail = false;
			break;

		}

		if( $sent_mail == true ){
			echo json_encode(array( "status" => true, "message" => /* $context . */"Mail sent successfully.", "data" => "$template_email"));
		} else {
			echo json_encode(array( "status" => false, "message" => ((!empty($message)) ? $message : /* $context . */"Mail could not be sent. Please try again."), "data" => "" ));
		}

	} else {
		echo json_encode(array( "status" => false, "message" => $error ));
	}

	exit;
?>