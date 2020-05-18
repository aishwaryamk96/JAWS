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

	// Auto load following modules when this is loaded
	load_module("user");
	load_module("course");

	// This will subscribe a user to a course bundle and create the required payment information in the database
	// Note: send no start_date if the access is to be given immediately - func will calculate the start_date
	function subscribe($email, $subs_info, $pay_info, $notify_user = true, $allow_partial = false, $name = "", $return_subs = false, $phone = "") {
           
		// See if user exists - create if not
		$user = user_get_by_email($email);
		if ($user === false) {

			// Normal account not found...search partial account
			if ($allow_partial) {

				$user = user_get_by_email($email, true);

				// Create partial account if not found
				if ($user === false) {
					$user = user_create_partial($email, $name);
				}
				if ($user === false) {
					return false;
                }

			}
			else {
				return false;
			}

        }

        if (empty($user["phone"]) && !empty($phone)) {
            user_update($user["user_id"], ["phone" => $phone]);
        }
        $user["meta"] = user_get_meta($user["user_id"]);
        if (empty($user["meta"]["state"]) && !empty($subs_info["user_state"])) {

            user_update_meta($user["user_id"], ["state" => $subs_info["user_state"]]);
            $user["meta"]["state"] = $subs_info["user_state"];

        }

		// Prep
		if (!isset($pay_info["agent_id"])) {
			$pay_info["agent_id"] = "";
		}
		$subs_info["agent_id"] = $pay_info["agent_id"];

		if (strcmp($pay_info["status"], "pending") == 0) {
			$subs_info["status"] = "inactive";
		}
		else {
			$subs_info["status"] = "pending";
		}

		// Create Subs
		$subs = subs_create($user["user_id"], $subs_info);

		// Prep again !
		$pay_info["subs_id"] = $subs["subs_id"];

		// Create Pay Info
		$pay_info = payment_create($user["user_id"], $pay_info);

		// update link back foreign key
		db_exec("UPDATE subs SET pay_id=".$pay_info["pay_id"]." WHERE subs_id=".$subs["subs_id"].";");

		// Prep courses
		$combo_arr = course_get_combo_arr($subs_info["combo"].";".$subs_info["combo_free"]);
		$combo_arr_free_exclusive = course_get_combo_arr($subs_info["combo_free"]);
                //JA-54 starts
                $individual_course_free_exclusive = course_get_combo_arr($subs_info["individual_course"]);
                //JA-54 ends
		$course = [];
                $freeCourseList = $individualCourseList =[];////JA-54 changes
		$count = 0;
		$category = [];
		$application_number_format = "";
		$after_sales = [];
		foreach ($combo_arr as $course_id => $learn_mode) {

			$res = db_query("SELECT * FROM course WHERE course_id=".$course_id." LIMIT 1;");
			$res_meta = db_query("SELECT * FROM course_meta WHERE course_id=".$course_id." LIMIT 1;");

			if (!empty($res_meta[0]["category"])) {
				$category = explode(";", $res_meta[0]["category"]);
			}
			if (!empty($res[0]['app_num_format'])) {
				$application_number_format = $res[0]['app_num_format'];
			}

			$after_sales = json_decode($res[0]["after_sales"], true);

			if (!isset($res[0]["name"])) {
				continue;
			}
			if (!isset($res_meta[0])) {
				continue;
			}

			$course_content = json_decode($res_meta[0]["content"], true);
                        $course[$count]["course_id"] = $res[0]["course_id"];
			$course[$count]["name"] = $res[0]["name"];
			$course[$count]["learn_mode"] = ((strcmp($learn_mode, "1") == 0)? "Premium" : "Regular");
			$course[$count]["desc"] = $res_meta[0]["desc"];
			$course[$count]["img"] = $course_content["img_main_small"];
			$course[$count]["url"] = $course_content["url_web"];
			$course[$count]["status"] = $res[0]["status"];

                        
			if (isset($combo_arr_free_exclusive[$course_id])) {
				$course[$count]["free"] = true;
                                //JA-54 starts
                                $freeCourseList[$count]['course_id'] = $course_id;
                                $freeCourseList[$count]['course_name'] = $res[0]["name"];
                                //JA-54 ends
			}
			else {
				$course[$count]["free"] = false;
			}
                        
                        //JA-54 starts
                        if (isset($individual_course_free_exclusive[$course_id])) {
				
                                $individualCourseList[$count]['course_id'] = $course_id;
                                $individualCourseList[$count]['course_name'] = $res[0]["name"];
                                
                           }
                        //JA-54 ends
			$count ++;

		}


		$bundle_details = array();
		$batch_details = array();

		if (!empty($subs['bundle_id'])) {
			$bundle_details = db_query("SELECT * FROM `course_bundle` WHERE `bundle_id` =". $subs['bundle_id'] . ";");
			$bundle_details = $bundle_details[0];
			if (!empty($subs['batch_id'])) {
				$batch_details = db_query("SELECT * FROM `bootcamp_batches` WHERE `id` =". $subs['batch_id'] . ";");
				$batch_details = $batch_details[0];
			}
		}

		$content["batch_details"] = $batch_details;
		$content["bundle_details"] = $bundle_details;
                //JA-54 starts
                $content["free_course"] = $freeCourseList;
                $content["individual_course"] = $individualCourseList;
                //JA-54 ends
		// Prep email content
		$content["user_webid"] = $user["web_id"];
		$content["paylink_id"] = $pay_info["instl"][1]["web_id"];
		$content["fname"] = substr($user["name"], 0, ((strpos($user["name"], " ") !== false) ? strpos($user["name"], " ") : strlen($user["name"])));
		$content["sum"] = $pay_info["instl"][1]["sum"];
		$content["sum_total"] = $pay_info["sum_total"];
		$content["currency"] = $pay_info["currency"];
		$content["courses"] = $course;
		$content["payment"] = payment_get_info($pay_info["pay_id"]); // not very optimized approach but refreshes the payment info .. the create_payment func returns incomplete info...not useful for email due dates etc..
		if ((!isset($user["lms_soc"])) || (strlen($user["lms_soc"]) == 0)) {
			$content["allow_setup"] = true;
		}
		else {
			$content["allow_setup"] = false;
		}

                //QUick fix :JA-171
                //case of invidual course
                $mindCourseFLag = 0;
                if(empty($content['bundle_details'])){
                    
                    foreach($content['courses'] as $idx => $crsDetails){
                       if($crsDetails['course_id'] == 302){
                           $mindCourseFLag = 1;
                       }
                    }                    
                }elseif(count($content['bundle_details'])> 0){
                    if(in_array($content['bundle_details']['bundle_id'],[142,144])){
                           $mindCourseFLag = 1;
                       }
                }
                $content['mindCourseFLag'] = $mindCourseFLag;
                
                 //QUick fix :JA-171 ends

		// Send Emails
		$template_email = "subs.init.success";
                //JA-171 starts                
                if($content['mindCourseFLag'] ==1){
                            $template_email = "subs.init.mindschool";
                }
                //JA-171 ends
		$mail_with_receipt = false;
		$receipt_data = array();
		if (strcmp($pay_info["status"], "pending") == 0) {
			$template_email = "subs.init";
			if (!empty($bundle_details["platform_id"]) && $bundle_details["platform_id"] == 2) {
				$template_email .= ".edunxt";
			}
                        //JA-171 starts    
                        if($content['mindCourseFLag'] ==1){
                            $template_email = "subs.init.mindschool";
                        } //JA-171 ends
		}
		else {

			$template_email = "subs.init.success";
                        
                        //JA-171 starts
                        if($content['mindCourseFLag'] ==1){
                            $template_email = "subs.init.mindschool..success";
                        }
                        //JA-171 ends
            $mail_with_receipt = true; // make this true to start sending receipts with emails
            // receipt data
            $receipt_data = array(
                'subs_id' => $subs["subs_id"],
                'name' => $user["name"],
                'email' => $email,
                'instl' => $pay_info["instl"][1]['instl_id'],
                'state' => $user["meta"]["state"],
                'watermark' => true
            );

            if (!empty($after_sales)) {

            	if (isset($after_sales["jlc"]) && $after_sales["jlc"] == false) {
            		db_exec("UPDATE subs SET status = 'active' WHERE subs_id = " . $subs["subs_id"] . ";");
            	}

            	$mail_with_receipt = $after_sales["receipt"] ?? false; // receipt not to be sent by default if after sales is set
            	$template_email = $after_sales["template"] ?? "";

            	$content['application_number'] = getApplicationNumber($subs["subs_id"], $application_number_format);

            }else{
							if (!empty($bundle_details["platform_id"]) && $bundle_details["platform_id"] == 2) {
								$template_email .= ".edunxt";
							}
						}

	        /*if (in_array("others", $category)) {

	        	load_module("ui");

	            $mail_with_receipt = false; // receipt not to be sent for others category

	            // These subs should not be processed
	            // Hence, we also do not need setup access here...
	            // Receipt will not be sent for others category.
	            db_exec("UPDATE subs SET status = 'active' WHERE subs_id = " . $subs["subs_id"] . ";");

	            if (!in_array("ipba", $category) && !in_array("application-fees", $category)) {
	                // uc application fees payment done here. generate application number and add to email
	                $content['pgpdm_application_number'] = generateApplicationNumber($subs['subs_id'], $application_number_format);
	                $template_email = 'uc.pay.confirm';
	            } else if (in_array("ipba", $category)) {
	                $content['ipba_application_number'] = generateApplicationNumberIPBA($subs['subs_id'], $application_number_format);
	                $template_email = 'ipba.pay.confirm';

	                ui_render_msg_front([
	                    "type" => "lol",
	                    "title" => "Thank you for your payment",
	                    "header" => "",
	                    "text" => "The admissions panel will be reviewing your application and will reach out to you shortly."
	                ]);
	            } else {
	                $template_email = 'cyber.pay.confirm';
	                // ui_render_msg_front(array(
	                //     "type" => "lol",
	                //     "title" => "Thank you for your payment",
	                //     "header" => "",
	                //     "text" => "We have received your payment"
	                // ));
	            }
	        }*/

			/*if (in_array("others", $category)) {
                $mail_with_receipt = false; // receipt not to be sent for others category

				// These subs should not be processed
				// Hence, we also do not need setup access here...
				// Receipt will not be sent for others category.
				db_exec("UPDATE subs SET status = 'active' WHERE subs_id = ".$subs["subs_id"].";");

				// uc application fees payment done here. generate application number and add to email
				$content['pgpdm_application_number'] = generateApplicationNumber($subs['subs_id'], $application_number_format);
				$template_email = 'uc.pay.confirm';

				if (in_array("ipba", $category)) {

					$content['ipba_application_number'] = generateApplicationNumberIPBA($subs['subs_id'], $application_number_format);
					$template_email = 'ipba.pay.confirm';

					ui_render_msg_front([
						"type" => "lol",
						"title" => "Thank you for your payment",
						"header" => "",
						"text" => "The admissions panel will be reviewing your application and will reach out to you shortly."
					]);

				}

			}*/

		}
                
		// Email and SMS
		if ($notify_user) {

            load_library("email");
            if ($mail_with_receipt && !empty($receipt_data)) {
               
                // send email with receipt
                load_plugin('mpdf');
                $pdf = new PDFgen($receipt_data);
                $receipt = $pdf->create_from_subs();
                $attachments = [$receipt];
               
                if (!send_email_with_attachment($template_email, array("to" => $email), $content, $attachments)) {
                    activity_create("critical", "subs.email", "fail", "", "", "", "", "Receipt Email Library Returned False !", "logged");
                }
                die;
                $pdf->deleteFileFromServer();
            }
            else {
                echo "In else mail";die;
                if (!send_email($template_email, array("to" => $email), $content)) {
                    activity_create("critical", "subs.email", "fail", "", "", "", "", "Email Library Returned False !", "logged");
                }
            }

			//load_library("sms");
			//send_sms($notify_info["phone"], 'Hi, ');
		}

		// Done.. Return the webid of the paylink if payment is pending.
		if (strcmp($pay_info["status"], "pending") == 0) {
			return array("web_id" => $pay_info["instl"][1]["web_id"], "token" => $pay_info["instl"][1]["token"]);
		}
		else if (!$return_subs) {
			return true;
		}
		else {
			return $subs;
		}

	}

	function subs_create($user_id, $subs_info) {

		// prep
		if (!isset($subs_info["bundle_id"])) {
			$subs_info["bundle_id"] = "";
		}
		if (!isset($subs_info["corp"])) {
			$subs_info["corp"] = "";
		}
		if (!isset($subs_info["combo_free"])) {
			$subs_info["combo_free"] = "";
		}
                //JA-54 starts
                if (!isset($subs_info["individual_course"])) {
			$subs_info["individual_course"] = "";
		}
                //JA-54 ends
		if (empty($subs_info["agent_id"])) {
			$subs_info["agent_id"] = "NULL";
		}
		if (!isset($subs_info["package_id"])) {
			$subs_info["package_id"] = "";
		}

		$access_duration = $subs_info['access_duration'] ?? course_get_duration($subs_info["combo"], $subs_info["combo_free"], $subs_info["bundle_id"]);
		$end_date = "";

		if (strcmp($subs_info["status"],"inactive") == 0) {

			if (!isset($subs_info["start_date"])) {
				$subs_info["start_date"] = "";
			}
			else {

				if (strlen($subs_info["start_date"]) == 0) {
					$subs_info["start_date"] = "";
				}
				else {
					$end_date = strval(date('Y-m-d H:i:s', strtotime("+ ".(intval($access_duration))." days", strtotime($subs_info["start_date"]))));
				}

			}

		}
		else {

			if (!isset($subs_info["start_date"])) {
				$subs_info["start_date"] = strval(date("Y-m-d H:i:s"));
			}
			elseif (strlen($subs_info["start_date"]) == 0) {
				$subs_info["start_date"] = strval(date("Y-m-d H:i:s"));
			}

			$end_date = strval(date('Y-m-d H:i:s', strtotime("+ ".(intval($access_duration))." days", strtotime($subs_info["start_date"]))));

		}

		if (!empty($subs_info["start_date"])) {

            if (!empty($subs_info["batch_id"])) {

                $res_batch = db_query("SELECT * FROM bootcamp_batches WHERE id = ".db_sanitize($subs_info["batch_id"]).";");
                if (!empty($res_batch)) {

                    if (($end_date_defined = date_create_from_format("Y-m-d", $res_batch[0]["end_date"])) !== false) {

                    	$start_date = date_create_from_format("Y-m-d H:i:s", $subs_info["start_date"]);
                    	if ($end_date_defined > $start_date) {
	                        $end_date = $end_date_defined->format("Y-m-d H:i:s");
                    	}

                    }

                }
            }
			else if (!empty($subs_info["bundle_id"])) {

				$res_subs_meta = db_query("SELECT batch_end_date FROM course_bundle WHERE bundle_id = ".db_sanitize($subs_info["bundle_id"]).";");
				if (!empty($res_subs_meta)) {

                    $res_subs_meta = $res_subs_meta[0];
					if (!empty($res_subs_meta["batch_end_date"])) {

						if (($end_date_defined = date_create_from_format("Y-m-d", $res_subs_meta["batch_end_date"])) !== false) {
							$end_date = $end_date_defined->format("Y-m-d H:i:s");
						}

					}

				}

			}

		}

		// sanitize
		$start_date = db_sanitize($subs_info["start_date"]);
		$end_date = db_sanitize($end_date);
		$combo = db_sanitize($subs_info["combo"]);
		$combo_free = db_sanitize($subs_info["combo_free"]);
                //JA-54 starts
                $individual_course = db_sanitize($subs_info["individual_course"]);
                //JA-54 ends
		$corp = db_sanitize($subs_info["corp"]);
		$status = db_sanitize($subs_info["status"]);
		$bundle_id = $subs_info["bundle_id"];
		$batch_id = $subs_info["batch_id"] ?? "NULL";
		$agent_id = $subs_info["agent_id"];
		$create_date = db_sanitize(strval(date('Y-m-d H:i:s')));

                //JA-54 starts
		// create the main record
		db_exec("INSERT INTO subs (user_id, combo, combo_free, individual_course,corp, start_date, end_date, access_duration, package_id, status) VALUES (".$user_id.",".$combo.",".$combo_free.",".$individual_course.",".$corp.",".((strlen($start_date) > 2) ? $start_date : "NULL").",".((strlen($end_date) > 2) ? $end_date : "NULL").",".$access_duration.",".((strlen($subs_info["package_id"]) > 0) ? $subs_info["package_id"] : "NULL").",".$status.");");
                //JA-54 ends
		// get the created subs id
		$subs_id = db_get_last_insert_id();

		// create the meta record
		db_exec("INSERT INTO subs_meta (subs_id, bundle_id, batch_id, create_date, agent_id) VALUES (".$subs_id.",".((strlen($bundle_id) > 0) ? $bundle_id : "NULL").",".$batch_id.",".$create_date.",".$agent_id.");");

		// Update package table with user_id
		if (!empty($subs_info["package_id"])) {
			db_exec("UPDATE package SET user_id = ".$user_id." WHERE package_id = ".$subs_info["package_id"]);
		}

		// all done - return the subs created
		$subs["subs_id"] = $subs_id;
		$subs["user_id"] = $user_id;
		$subs["bundle_id"] = trim($bundle_id, "'");
		$subs["batch_id"] = $batch_id == "NULL" ? null : $batch_id;
		$subs["agent_id"] = trim($agent_id, "'");
		$subs["corp"] = trim($corp, "'");
		$subs["combo"] = trim($combo, "'");
		$subs["combo_free"] = trim($combo_free, "'");
                //JA-54 starts
                $subs["individual_course"] = trim($individual_course, "'");
                //JA-54 ends
		$subs["start_date"] = trim($start_date, "'");
		$subs["end_date"] = trim($end_date, "'");
		$subs["status"] = trim($status, "'");

		return $subs;

	}

	// This will get all information about a subcription - Note: this will not fetch payment information
	function subs_get_info($subs_id) {

		$res = db_query("SELECT * FROM subs WHERE subs_id=".$subs_id." LIMIT 1;");
		if (!isset($res[0])) {
			return false;
		}

		$res_meta = db_query("SELECT * FROM subs_meta WHERE subs_id=".$subs_id." LIMIT 1;");
		$res[0]["meta"] = $res_meta[0];

		return $res[0];

	}

	// Changes the "status" of a subscription. If any data pertaining to status change is required, it is passed to $data array
	function subs_update_status($subs_id, $status, $data = null) {

		// Get the subs info by subs_id
		$res = db_query("SELECT * FROM subs WHERE subs_id=".$subs_id.";");
		$res_meta = db_query("SELECT * FROM subs_meta WHERE subs_id=".$subs_id.";");
		if ($res) {

			$res = $res[0];
			if (!empty($res_meta)) {
				$res_meta = $res_meta[0];
			}

			// If the status is to be changed to "active"
			if (strcmp($status, "active") == 0) {

				// If freeze_date is set and current status is "blocked" or "frozen"
				if (!empty($res["freeze_date"]) && in_array($res["status"], ["blocked", "frozen"])) {

					// If unfree_date is set to a future date and status is "blocked", we don't do anything
					if (!empty($res["unfreeze_date"]) && $res["status"] == "blocked") {

						$unfreeze_date = date_create_from_format("Y-m-d H:i:s", $res["unfreeze_date"]);
						$now = date_create();
						// If unfreeze_date is greater than today's date, don't change the status
						if (date_diff($now, $unfreeze_date)->format("%a") > 0) {
							return false;
						}

					}
					$unfreeze_date = db_sanitize(date("Y-m-d"));
					db_exec("UPDATE subs SET status='active', $unfreeze_date=".$unfreeze_date." WHERE subs_id=".$subs_id.";");

				}
				else {

					if (empty($res["end_date"])) {

						$access_duration = $res['access_duration'] ?? course_get_duration($res["combo"], $res["combo_free"], $res["bundle_id"] ?? "");

						if (empty($res["start_date"])) {
							$res["start_date"] = new DateTime;
						}
						else {

							$today = new DateTime;
							$res["start_date"] = date_create_from_format("Y-m-d H:i:s", $res["start_date"]);
							if ($res["start_date"] < $today) {
								$res["start_date"] = $today;
							}

						}

						$interval = "P".$access_duration."M";
						$res["end_date"] = (clone $res["start_date"])->add(new DateInterval($interval));

						if (!empty($res_meta["batch_id"])) {

							$res_batch = db_query("SELECT * FROM bootcamp_batches WHERE id = ".db_sanitize($res_meta["batch_id"]).";");
							if (!empty($res_batch)) {

								if (($end_date_defined = date_create_from_format("Y-m-d", $res_batch[0]["end_date"])) !== false) {

									if ($end_date_defined > $res["start_date"]) {
										$res["end_date"] = $end_date_defined;
									}

								}

							}

						}
						else if (!empty($res_meta["bundle_id"])) {

							$res_subs_meta = db_query("SELECT batch_end_date FROM course_bundle WHERE bundle_id = ".db_sanitize($res_meta["bundle_id"]).";");
							if (!empty($res_subs_meta)) {

								$res_subs_meta = $res_subs_meta[0];
								if (!empty($res_subs_meta["batch_end_date"])) {

									if (($end_date_defined = date_create_from_format("Y-m-d", $res_subs_meta["batch_end_date"])) !== false) {
										$res["end_date"] = $end_date_defined;
									}

								}

							}

						}

						$start_date = db_sanitize($res["start_date"]->format("Y-m-d H:i:s"));
						$end_date = db_sanitize($res["end_date"]->format("Y-m-d H:i:s"));

						db_query("UPDATE subs SET status = 'active', start_date = $start_date, end_date = $end_date WHERE subs_id = $subs_id;");

					}
					else {
						db_exec("UPDATE subs SET status='active' WHERE subs_id=".$subs_id.";");
					}

				}

			}

			else if (strcmp($status, "pending") == 0) {

				// status can only be changed to "pending" if the current status is "inactive"                                
				if (strcmp($res["status"], "inactive") == 0) {
					db_exec("UPDATE subs SET status='pending' WHERE subs_id=".$subs_id.";");
                                        //JA-80 starts
                                        updateSubsDates($res, $res_meta, $subs_id);
                                        //JA -80 ends
				}
			}

			else if (strcmp($status, "frozen") == 0) {

				// If the status is to be changed to "fronzen", the freeze_date has to be present, unfreeze_date may not already be present
				if (!isset($data["freeze_date"]) || strlen($data["freeze_date"]) == 0) {
					return false;
				}

				$freeze_date = db_sanitize($data["freeze_date"]);
				$unfreeze_date = "NULL";
				if (isset($unfreeze_date)) {
					$unfreeze_date = db_sanitize($data["unfreeze_date"]);
				}

				db_exec("UPDATE subs SET status='frozen', freeze_date=".$freeze_date.", unfreeze_date=".$unfreeze_date." WHERE subs_id=".$subs_id.";");

			}

			//Even if the status is "frozen", if the installment payment is due we gonna block the subscription <<------------------------- IMPORTANT //////////////////////////////////////////////////////
			else if (strcmp($status, "blocked") == 0) {

				$freeze_date = db_sanitize(date("Y-m-d"));
				// EDIT REQUIRED!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
				db_exec("UPDATE subs SET status='blocked', freeze_date=".$freeze_date." WHERE subs_id=".$subs_id.";");

			}

			else if (strcmp($status, "alumni") == 0) {

				// Alumini status can only be set if the end_date has expired
				$end_date = date_create_from_format("Y-m-d H:i:s", $res["end_date"]);
				$date_now = date_create();
				// If the end_date of the subscription has been approached, set the subs status to "alumni"
				if (date_diff($date_now, $end_date)->format("%a") > 0) {
					db_exec("UPDATE subs SET status='alumni' WHERE subs_id=".$subs_id.";");
				}

			}

			else if (strcmp($status, "expired") == 0) {

				// A subscription only expires if it is more than 2 years old. We should probably make this customizable.
				$end_date = date_create_from_format("Y-m-d H:i:s", $res["end_date"]);
				$date_now = date_create();
				// Compare subs end_date with today's date; if the difference is more than 2 years (730 days), expire the subscription
				if (date_diff($date_now, $end_date)->format("%a") >= 730) {
					db_exec("UPDATE subs SET status='expired' WHERE subs_id=".$subs_id.";");
				}

			}

		}

	}

	function subs_get_info_by_user_id($user_id) {

		$res = db_query("SELECT * FROM subs WHERE user_id=".$user_id." ORDER BY subs_id ASC;");
		if (!$res) {
			return false;
		}

		$subs_arr = array();
		foreach ($res as $subs) {

			$res_meta = db_query("SELECT * FROM subs_meta WHERE subs_id=".$subs["subs_id"]." LIMIT 1;");
			$subs["meta"] = $res_meta[0];
			$subs_arr[] = $subs;

		}

		return $subs_arr;

	}
        
        
/**
 * JA-80 changes
 * @param type $res
 * @param type $res_meta
 * 
 * Function to add the start date / end date to a subscription
 * Access duration is considered as number of days
 */
        
    function updateSubsDates($res, $res_meta, $subs_id){

        if (empty($res["end_date"])) {

            $access_duration = $res['access_duration'] ?? course_get_duration($res["combo"], $res["combo_free"], $res["bundle_id"] ?? "");
            //echo "Acc".$access_duration;
            if (empty($res["start_date"])) {
                    $res["start_date"] = new DateTime;
            }
            else {

                    $today = new DateTime;
                    $res["start_date"] = date_create_from_format("Y-m-d H:i:s", $res["start_date"]);
                    if ($res["start_date"] < $today) {
                            $res["start_date"] = $today;
                    }

            }

            $interval = "P".$access_duration."D";

            $res["end_date"] = (clone $res["start_date"])->add(new DateInterval($interval));
//            echo "<pre>------------";
//            print_r($res);
//            print_r($res_meta);
            if (!empty($res_meta["batch_id"])) {

                    $res_batch = db_query("SELECT * FROM bootcamp_batches WHERE id = ".db_sanitize($res_meta["batch_id"]).";");
                    if (!empty($res_batch)) {

                            if (($end_date_defined = date_create_from_format("Y-m-d", $res_batch[0]["end_date"])) !== false) {

                                    if ($end_date_defined > $res["start_date"]) { //echo 1;die;
                                            $res["end_date"] = $end_date_defined;
                                    }

                            }

                    }

            }
            else if (!empty($res_meta["bundle_id"])) {

                    $res_subs_meta = db_query("SELECT batch_end_date FROM course_bundle WHERE bundle_id = ".db_sanitize($res_meta["bundle_id"]).";");
                    if (!empty($res_subs_meta)) {
                           // echo "---1111;"; print_r($res_subs_meta);die;
                            $res_subs_meta = $res_subs_meta[0];
                            if (!empty($res_subs_meta["batch_end_date"])) {
                                // echo "---2222222;";

                                    if (($end_date_defined = date_create_from_format("Y-m-d", $res_subs_meta["batch_end_date"])) !== false) {
                                            $res["end_date"] = $end_date_defined;
                                    }

                            }

                    }

            }
//            echo "Afain***************************888888";
//            print_r($res);
            $start_date = db_sanitize($res["start_date"]->format("Y-m-d H:i:s"));
            $end_date = db_sanitize($res["end_date"]->format("Y-m-d H:i:s"));
            
//            echo "End data".$end_date;
//            echo "<!---------->";
//            echo "UPDATE subs SET start_date = $start_date, end_date = $end_date WHERE subs_id = $subs_id;";die;

            db_query("UPDATE subs SET start_date = $start_date, end_date = $end_date WHERE subs_id = $subs_id;");

        }
    }

?>
