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
      	header('Location: https://www.jigsawacademy.com');
      	die();
    }

    // Load stuff
    load_library("payment");
    load_module("user");
    load_module("course");
    load_module("subs");
    load_module("ui");
    load_library("email");

    // Init Session
    auth_session_init();

    // Check
    if (!isset($_GET["pay"])) {

        ui_render_msg_front(array(
            "type" => "error",
            "title" => "A problem ran into you :(",
            "header" => "Oops !",
            "text" => "Sorry, but this link seems to be invalid!<br /><br />Please contact our support team for assistance."
        ));

        exit();

    }

    // Start
    $paylink_web_id = $_GET["pay"];
    $paylink_info = payment_link_parse($paylink_web_id);
    $payment_gateway_details = array(
        'pg' => $_GET["pg"] ?? "default",
        'ty' => $_GET["ty"] ?? ""
    );
    
    $transaction_response = payment_response_parse($payment_gateway_details,$paylink_info);
   
    // save user state if available
    if( !empty($_POST['state']) ){
        db_query("UPDATE `user_meta` SET `state` = " . db_sanitize($_POST['state']) . "  WHERE `user_meta`.`user_id` = '".$paylink_info["user_id"]."';");
    }

	// Prep feed vars
	$tags = ["payments"];
	$agent;
	if ($paylink_info["create_entity_type"] == "user") {
		$agent = user_get_by_id($paylink_info["create_entity_id"]);
		if ($agent !== false) $tags []= "for_".$agent["user_id"];
	}

    // Check
    if ($paylink_info === false) {

            // Log hack attempt here - no transaction response
            activity_create("low", "paylink.confirm", "fail", "paylink_web_id", $paylink_web_id, "", "", "payment_link_parse() Function Failed", "logged");

        ui_render_msg_front(array(
            "type" => "error",
            "title" => "A problem ran into you :(",
            "header" => "Oops !",
            "text" => "Sorry, but this link seems to be invalid!<br /><br />Either it has been used or it has expired. Please contact our support team for assistance."
        ));

        exit();

    }

    if ($transaction_response === false) {

        // Log hack attempt here - no transaction response
        activity_create("low", "paylink.confirm", "fail", "paylink_id", $paylink_info["paylink_id"], "user_id", $paylink_info["user_id"], "No Transaction Response", "logged");

        ui_render_msg_front(array(
            "type" => "error",
            "title" => "A problem ran into you :(",
            "header" => "Oops !",
            "text" => "Sorry, but this link seems to be invalid!<br /><br />Either it has been used or it has expired. Please contact our support team for assistance."
        ));

        exit();

    }

    // For auth
    $user = user_get_by_id($paylink_info["user_id"]);
    $user["meta"] = user_get_meta($user["user_id"]);

    // Validate payment
    if (!payment_validate("payment_link", $paylink_info["paylink_id"], "paylink.confirm")) {

        // Log hack attempt - transaction validation PSK mismatch
        activity_create("low", "paylink.confirm", "fail", "paylink_id", $paylink_info["paylink_id"], "user_id", $paylink_info["user_id"], "PSK Mismatch", "logged");

        ui_render_msg_front(array(
            "type" => "error",
            "title" => "A problem ran into you :(",
            "header" => "Oops !",
            "text" => "Sorry, but this link seems to be invalid!<br /><br />Either it has been used or it has expired. Please contact our support team for assistance."
        ));

        exit();

    }

    // Authenticate session
    if (auth_session_is_logged()) {
        if (strcmp($user["user_id"], $_SESSION["user"]["user_id"]) != 0) {

            // log possible hack attempt but proceed !
            auth_session_logout();
            auth_session_login_forced($user["email"]);

        }
    }
    else auth_session_login_forced($user["email"]);

    // Prep email content
    // This is done before more checks as email will be sent optionally within those checks as well

    // Load course info
    $subs = subs_get_info($paylink_info["subs_id"]);
    $subs_meta = $subs['meta'];
    $combo_str = $subs["combo"].";".$subs["combo_free"];
    $combo_arr = course_get_combo_arr($combo_str);
	$combo_arr_free_exclusive = course_get_combo_arr($subs["combo_free"]);
    $sc_str = "";
    $price_str = "";

    
    $course;
	$feed_course;
    $count = 0;
    $iotflag = false;
    $category = false;
    $application_number_format = "";
    $after_sales = false;
    foreach($combo_arr as $course_id => $learn_mode) {

        $res = db_query("SELECT * FROM course WHERE course_id=".$course_id." LIMIT 1;");
        $res_meta = db_query("SELECT * FROM course_meta WHERE course_id=".$course_id." LIMIT 1;");

        if (!isset($res[0]["name"])) continue;
        if (!isset($res_meta[0])) continue;

        $course_content = json_decode($res_meta[0]["content"], true);

        $course[$count]["name"] = $res[0]["name"];
        $course[$count]["learn_mode"] = ((strcmp($learn_mode, "1") == 0)? "Premium" : "Regular");
		$feed_course[$course[$count]["name"]] = $course[$count]["learn_mode"];
        $course[$count]["desc"] = $res_meta[0]["desc"];
        $course[$count]["img"] = $course_content["img_main_small"];
        $course[$count]["url"] = $course_content["url_web"];
        $course[$count]["status"] = $res[0]["status"];
        if (!empty($res_meta[0]["category"])) {
            $category = explode(";", $res_meta[0]["category"]);
        }
        if (in_array("iot", $category)) {
            $iotflag = true;
        }

        if (!empty($res[0]['app_num_format'])) {
            $application_number_format = $res[0]['app_num_format'];
        }

        $after_sales = json_decode($res[0]["after_sales"], true);

		if (isset($combo_arr_free_exclusive[$course_id])) $course[$count]["free"] = true;
		else $course[$count]["free"] = false;

        $sc_str .= ((strcmp($learn_mode, "1") == 0)? $res[0]["il_code"] : $res[0]["sp_code"]).",";
        $price_str .= ((strcmp($learn_mode, "1") == 0)? $res[0]["il_price_inr"] : $res[0]["sp_price_inr"]).",";

        $count ++;
    }

    $bundle_details = array(); $batch_details = array();
    if(!empty($subs_meta['bundle_id'])){
        $bundle_details = db_query("SELECT * FROM `course_bundle` WHERE `bundle_id` =". $subs_meta['bundle_id'] . ";");
        $bundle_details = $bundle_details[0];
        if(!empty($subs_meta['batch_id'])){
            $batch_details = db_query("SELECT * FROM `bootcamp_batches` WHERE `id` =". $subs_meta['batch_id'] . ";");
            $batch_details = $batch_details[0];
        }
    }

    // Prep More
    $content["batch_details"] = $batch_details;
    $content["bundle_details"] = $bundle_details;
    $content["user_webid"] = $user["web_id"];
    $content["paylink_id"] = $paylink_web_id;
    $content["fname"] = substr($user["name"], 0, ((strpos($user["name"], " ") !== false) ? strpos($user["name"], " ") : strlen($user["name"])));
    $content["sum"] = $paylink_info["sum"];
    $content["currency"] = $paylink_info["currency"];
    $content["courses"] = $course;
    $content["payment"] = payment_get_info($paylink_info["pay_id"]);
    if ((!isset($user["lms_soc"])) || (strlen($user["lms_soc"]) == 0)) $content["allow_setup"] = true;
    else $content["allow_setup"] = false;

    //case of invidual course
  // echo "<pre>"; print_r($content);
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
//echo "<pre>"; print_r($content);print_r($transaction_response);
     //QUick fix :JA-171 ends
    // Check response
    if (!$transaction_response["status"]) {

        // Log transaction failure - gateway was not successful
        activity_create("high", "paylink.confirm", "fail", "paylink_id", $paylink_info["paylink_id"], "user_id", $paylink_info["user_id"], "Gateway Cancelled/Failed", "logged");

        // Send email to student allowing them to resume the checkout - if it is a subs initiation from frontend
        if ((intval($paylink_info["instl_count"]) == 1) && (strcmp($paylink_info["create_entity_type"], "system") == 0)) send_email("subs.init.re", array("to" => $user["email"]), $content);

        // Show Error UI
        ui_render_msg_front(array(
            "type" => "error",
            "title" => "A problem ran into you :(",
            "header" => "Oops !",
            "text" => "Sorry, but your payment transaction seems to have failed! Do not worry if you have been charged for this - our support team will help you out. Otherwise you can try checking out again.",
            "btn" => array("url" => JAWS_PATH_WEB.'/pay?pay='.$paylink_web_id, "text" => "Retry")
        ));

        // All done
        exit();

    }

    // Confirm payment
    if (payment_link_confirm($paylink_web_id, $transaction_response) === false) {

        // log MAJOR internal error here
        activity_create("critical", "paylink.confirm", "fail", "paylink_id", $paylink_info["paylink_id"], "user_id", $paylink_info["user_id"], "PayConfirm() Failed", "logged");

        // Show Error UI
        ui_render_msg_front(array(
            "type" => "error",
            "title" => "A problem ran into you :(",
            "header" => "Oops !",
            "text" => "Sorry, but somehow we could not confirm your payment!<br /><br />Do not worry if you have been charged for this anyways. Our support team will help you out."
        ));

        // All done
        exit();

    }

    // Log success
    activity_create("ignore", "paylink.confirm", "success", "paylink_id", $paylink_info["paylink_id"], "user_id", $paylink_info["user_id"], "Payment Success", "logged");

    handle("payment_confirm__", $paylink_info["pay_id"]);

	// Log success - feed
	try {
		activity_log(
			"[".ucwords($user["name"])."] has [[paid]].",
			[
				$user["email"].(empty($user["phone"]) ? "" : " (".$user["phone"].")"),
				"Offered by ".(($paylink_info["create_entity_type"] == "user") ? $agent["name"] : "(Website)")
			],
			"[Pricing :]",
			[
				"Instl. Amount" => (strtoupper($paylink_info["currency"]) == "INR" ? '&#8377;' : '&#36;').$paylink_info["sum"],
				"Nett Total" => (strtoupper($paylink_info["currency"]) == "INR" ? '&#8377;' : '&#36;').$paylink_info["sum_total"],
				"Installments" => $paylink_info["instl_count"]." / ".$paylink_info["instl_total"]
			],
			"[Courses :]",
			$feed_course,
			$tags,
			[
				'c' => "success",
				'by' => $paylink_info["user_id"]
			]
		);
	}
	catch (Exception $e) {
		activity_debug_start();
		activity_debug_log("paylink.confirm.activity_log() failed : ".($e->getMessage()));
	}

    // Log payment success for updating package status
    activity_create("high", "package", "package.payment.update", "", "", "payment_instl", $paylink_info["instl_id"], "package.payment.update", "pending");

    // Email info is already prepped...except for the one updated by payment confirm func
    $content["payment"] = payment_get_info($paylink_info["pay_id"]);

    // Done! Send an email
    $template = "subs.init.success";
    //JA-171 starts                
    if($content['mindCourseFLag'] ==1){
        $template = "subs.init.mindschool.success";
    }
    $email_info['to'] = $user["email"];
    if (intval($paylink_info["instl_count"]) > 1) {
        $template = "subs.instl.success";
        if($content['mindCourseFLag'] ==1){
            $template = "subs.instl.mindschool.success";
        }
        $content["instl_count"] = $paylink_info["instl_count"];
        $user_id = db_sanitize($user["user_id"]);
        db_exec("INSERT INTO user_logs (user_id, category, created_by, status) VALUES ($user_id, 'access.grant', $user_id, 'pending');");
    }
    else {
        if ($iotflag) $email_info['cc'] = 'madhuri@jigsawacademy.com';
    }
    log_activity("payment.email", $bundle_details);
    if (!empty($bundle_details["platform_id"]) && $bundle_details["platform_id"] == 2) {
      $template .= ".edunxt";
    }
   // echo "after sales";print_r($after_sales);
    if (!empty($after_sales)) {

        if (isset($after_sales["jlc"]) && $after_sales["jlc"] == false) {
            db_exec("UPDATE subs SET status = 'active' WHERE subs_id = " . $subs["subs_id"] . ";");
        }

        $mail_with_receipt = $after_sales["receipt"] ?? false; // receipt not to be sent by default if after sales is set
        $template = $after_sales["template"] ?? "";

        if ($after_sales["app_num"] ?? false) {

            if ($application_number_format == "P") {
                $email_info["bcc"] = "pgpdmsupport@jigsawacademy.com";
            }
            elseif ($application_number_format == "IP") {
                $email_info["bcc"] = "ipbasupport@iimidr.ac.in";
            }

            $content['application_number'] = getApplicationNumber($subs["subs_id"], $application_number_format);

        }

        if(!$mail_with_receipt) {// echo "in llll";die;
            send_email($template, $email_info, $content);
        } else {
            fuckyouAwesomeName($subs, $user, $paylink_info, $template, $email_info, $content);
        }

        if (!empty($application_number_format)) {
            ui_render_msg_front(array(
                "type" => "lol",
                "title" => "Thank you for your payment",
                "header" => "",
                "text" => "The admissions panel will be reviewing your application and will reach out to you shortly."
            ));
        }
        else {
            ui_render_msg_front(array(
                "type" => "lol",
                "title" => "Thank you for your payment",
                "header" => "",
                "text" => "We have received your payment"
            ));
        }

        exit();

    }

/*if (in_array("others", $category)) {

        // These subs should not be processed
        // Hence, we also do not need setup access here...
        // Receipt will not be sent for others category.
        db_exec("UPDATE subs SET status = 'active' WHERE subs_id = ".$subs["subs_id"].";");

        if(!in_array("ipba",$category) && !in_array("application-fees",$category)){

            // uc application fees payment done here. generate application number and add to email
            $content['pgpdm_application_number'] = generateApplicationNumber($subs['subs_id'],$application_number_format);
            $template = 'uc.pay.confirm';
        } else if (in_array("ipba",$category)) {

            // if( in_array( "application-fees",$category) ){
            //     $template = "ipba.commence";

            //     ui_render_msg_front(array(
            //        "type" => "lol",
            //        "title" => "Thank you for your payment",
            //        "header" => "",
            //        "text" => "We have received your payment."
            //    ));

            // } else {
                // $content['ipba_application_number'] = generateApplicationNumberIPBA($subs['subs_id'],$application_number_format);
                $template = 'ipba.pay.confirm';

                ui_render_msg_front(array(
                    "type" => "lol",
                    "title" => "Thank you for your payment",
                    "header" => "",
                    "text" => "The admissions panel will be reviewing your application and will reach out to you shortly."
                ));
            // }
        }
        else {
            $template = 'cyber.pay.confirm';
            ui_render_msg_front(array(
                "type" => "lol",
                "title" => "Thank you for your payment",
                "header" => "",
                "text" => "We have received your payment"
            ));

        }

        send_email($template, $email_info, $content);

        // All done
        exit();

    }*/

    fuckyouAwesomeName($subs, $user, $paylink_info, $template, $email_info, $content);

    // send_email($template, $email_info, $content);

    // Set alternate msg flag for successfull payment - populated with instl_count of the installment just paid
    $_SESSION["temp"]["lms.setup.alt"] = intval($paylink_info["instl_count"]);

    // Additional temp values for pixel tracking on success page
    $_SESSION["temp"]["pay.success.id"] = $paylink_web_id;
    $payresnew =  db_query("SELECT sum_total FROM payment WHERE pay_id=".$paylink_info["pay_id"].";");
    $_SESSION["temp"]["pay.success.total"] = $payresnew[0]["sum_total"];
    $_SESSION["temp"]["pay.success.scstr"] =  $sc_str;
    $_SESSION["temp"]["pay.success.pricestr"] =  $price_str;
    $linkresnew =  db_query("SELECT create_entity_type FROM payment_link WHERE pay_id=".$paylink_info["pay_id"].";");
    $_SESSION["temp"]["pay.success.creator"] = $linkresnew[0]["create_entity_type"];


    // Show payment successfull/LMS setup page
    // This is done via header redirection instead of load_view to -
    // 1. make tokens etc. transparent to user
    // 2. allow page refresh by user

    if (!empty($subs["corp"]) && $subs["corp"] == "FSKILL") {

        load_module("user_enrollment");

        enr_create($subs["subs_id"]);
        sis_import();

        header("Location: ".JAWS_PATH_WEB."/access_setup?i=".$subs["subs_id"]);

    }
    else {
        header("Location: ".JAWS_PATH_WEB."/setupaccess?m=".$mindCourseFLag);
    }


    function fuckyouAwesomeName($subs, $user, $paylink_info, $template, $email_info, $content) {
        
        $receipt_data = array();
        // receipt data
        $receipt_data = array(
            'subs_id' => $subs["subs_id"],
            'name' => $user["name"],
            'email' => $user["email"],
            'instl' => $paylink_info['instl_id'],
            'state' => $user["meta"]["state"],
            'watermark' => true
        );

        load_plugin('mpdf');
        $pdf = new PDFgen($receipt_data);
        $receipt = $pdf->create_from_subs();
        $attachments = array(
            0 => $receipt, // file attached
        );
        //echo "template".$template;die;
        if (!send_email_with_attachment($template, $email_info, $content, $attachments)) {
            activity_create("critical", "subs.email", "fail", "", "", "", "", "Receipt Email Library Returned False !", "logged");
        }
        $pdf->deleteFileFromServer();
    }

?>
