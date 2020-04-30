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

	// More Prep
	$login_params["return_url"] = JAWS_PATH_WEB."/pay?pay=".$_GET["pay"];

	// Start
	$paylink_web_id = $_GET["pay"];
	$paylink_info = payment_link_parse($paylink_web_id);

	// Prep feed vars
	$tags = ["payments"];
	$agent;
	if ($paylink_info["create_entity_type"] == "user") {
		$agent = user_get_by_id($paylink_info["create_entity_id"]);
		if ($agent !== false) $tags []= "for_".$agent["user_id"];
	}

	// Check
	if ($paylink_info === false) {

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
    $user_meta = user_get_meta($paylink_info['user_id']);

	// Prep payment beforehand
	$transaction_info["name"] = $user["name"];
	$transaction_info["email"] = $user["email"];
	$transaction_info["phone"] = $user["phone"];
	$transaction_info['sum'] = $paylink_info["sum"];
	$transaction_info['currency'] = $paylink_info["currency"];
	$transaction_info['invoice_id'] = $paylink_info["paylink_id"];
	$transaction_info['return_url'] = JAWS_PATH_WEB."/pay/success?pay=".$paylink_web_id;
	$transaction_info['extra']['web_id'] = $paylink_web_id;
	$transaction_info['user_state'] = $user_meta['state'];

        //JA-120 changes
        $pgInfo = [];
        //JA-120 
	$spl_bundles = [65];
	$spl_transaction_desc = "";

	// Prep Description
	$subs_info = subs_get_info($paylink_info["subs_id"]);
	if (!empty($subs_info["meta"]["bundle_id"]) && in_array($subs_info["meta"]["bundle_id"], $spl_bundles)) {
		$spl_transaction_desc = "(UCN) ";
    }

    // Set course shortcodes
    $transaction_info['extra']['desc'] = $spl_transaction_desc.course_get_short_code_str($subs_info["combo"].((isset($subs_info["combo_free"]) && (strlen($subs_info["combo_free"]) > 0)) ? ";".$subs_info["combo_free"] : ""));
    
    if (!empty($subs_info["meta"]["bundle_id"])) {

        $bundle = db_query("SELECT * FROM course_bundle WHERE bundle_id = ".$subs_info["meta"]["bundle_id"]);
        if (!empty($bundle)) {
           $transaction_info['extra']['desc'] = $bundle[0]["name"];
           //JA-120 starts
           $pgInfo['rpay_acc_flag'] = $bundle[0]["rpay_acc_flag"];
           $pgInfo['show_pg_selection'] = false;
           $paylink_info['rpay_acc_flag'] = $bundle[0]["rpay_acc_flag"];
           //JA-120 ends
        }

    }

	// Log Parse attempt - parse stage
	activity_create("ignore", "paylink.parse", "parse", "paylink_id", $paylink_info["paylink_id"], "user_id", $paylink_info["user_id"], "Payment Link Clicked", "logged");

	// Prep Feed
	$combo_str = $subs_info["combo"].";".$subs_info["combo_free"];
        $combo_arr = course_get_combo_arr($combo_str);
	$feed_course;

        //JA-120 changes
        
        //JA-120 ends
    foreach($combo_arr as $course_id => $learn_mode) {
        $res = db_query("SELECT * FROM course WHERE course_id=".$course_id." LIMIT 1;");
        
         //JA-120 changes
        if(empty($subs_info["meta"]["bundle_id"])){
             $pgInfo['rpay_acc_flag'] = $res[0]['rpay_acc_flag'];
             $pgInfo['show_pg_selection'] = false;
             $paylink_info['rpay_acc_flag'] = $res[0]["rpay_acc_flag"];
        }
         //JA-120 changes
        if (!isset($res[0]["name"])) continue;
		$feed_course[$res[0]["name"]] = ((strcmp($learn_mode, "1") == 0)? "Premium" : "Regular");
        }

	$feed_msg_user = [
		$user["email"].(empty($user["phone"]) ? "" : " (".$user["phone"].")"),
		"Offered by ".(($paylink_info["create_entity_type"] == "user") ? $agent["name"] : "(Website)")
	];

	$feed_msg_pricing = [
		"Instl. Amount" => (strtoupper($paylink_info["currency"]) == "INR" ? '&#8377;' : '&#36;').$paylink_info["sum"],
		"Nett Total" => (strtoupper($paylink_info["currency"]) == "INR" ? '&#8377;' : '&#36;').$paylink_info["sum_total"],
		"Installments" => $paylink_info["instl_count"]." / ".$paylink_info["instl_total"]
	];

	// store values in global so that can be used in payment gateway selection template.
	$GLOBALS["content"]['transaction_info'] = $transaction_info;
    $GLOBALS["content"]['paylink_id'] = $paylink_info["paylink_id"];
    $GLOBALS["content"]['paylink_info'] = $paylink_info;
    //JA-120 changes
    $GLOBALS["content"]['gateway_info'] = $pgInfo;
    //JA-120 ends
	// check if payment gateway selection is needed or not.
    // show payment gateway selection if paying in INR not for USD.
    $show_payment_gateway_selection = false; 
    // updated on 17-01-19. showing payment gateway selection for both inr and usd as razorpay can accept usd now.
	// if ( empty($_POST["pg"]) && $transaction_info['currency'] === 'inr' ) {
	if ( empty($_POST["pg"]) ) {
        $show_payment_gateway_selection = true;
    }

    // paylink_info receipt type if is pgpdm then show the neft razorpay only.
    $show_neft_razorpay = false;
    if( !empty($paylink_info['receipt_type']) && ( $paylink_info['receipt_type'] == 'pgpdm' || $paylink_info['receipt_type'] == 'ipba' ) ){
        $show_neft_razorpay = true;
        $show_payment_gateway_selection = true; // show payment gateway selection for usd also. razorpay order api handles usd and euro.
    }

	$pg = $_POST['pg'] ?? 'default';
	$user_state = trim($_POST['state']) ?? '';

	// Trying to use PSK Auth for forcelogin
	if (isset($_GET["token"])) {

		// Auth check - links belonging to partial/blocked accounts cannot be parsed this way.
		if (strcmp($user["status"],"active") != 0) {
			//log hack attempt - using forcelogin for partial accounts
			ui_render_msg_front(array(
				"type" => "error",
				"title" => "A problem ran into you :(",
				"header" => "Oops !",
				"text" => "Sorry, but this link seems to be invalid!<br /><br />Please contact our support team for assistance."
			));
			exit();
		}

		// PSK check
		$token = psk_get("payment_link", $paylink_info["paylink_id"],"user.login.force");
		
		if (strcmp($_GET["token"], $token) != 0) {
			//log hack attempt - invalid tokens for forcelogin
			ui_render_msg_front(array(
				"type" => "error",
				"title" => "A problem ran into you :(",
				"header" => "Oops !",
				"text" => "Sorry, but this link seems to be invalid!<br /><br />Please contact our support team for assistance."
			));
			exit();
		}

		// Do force login
		if (auth_session_is_logged()) {
			if (strcmp($user["user_id"], $_SESSION["user"]["user_id"]) != 0) {
				// log possible hack attempt but proceed !
				auth_session_logout();
				auth_session_login_forced($user["email"]);
			}
		}
		else auth_session_login_forced($user["email"]);


		if (isset($_SESSION["user"]) && $show_payment_gateway_selection) {
            // payment gateway selection. from website i.e wordpress not from email.
            if($show_neft_razorpay){
                // website should not have neft payment
                load_template("jaws/frontend", "pg.select.neft");
            } else {
                    
                load_template("jaws/frontend", "pg.select");
            }
			exit;
		}

		psk_expire("payment_link", $paylink_info["paylink_id"],"user.login.force");

		// Proceed to payment
		activity_create("ignore", "paylink.parse", "gateway", "paylink_id", $paylink_info["paylink_id"], "user_id", $paylink_info["user_id"], "Proceed to Gateway (DevInfo:1)", "logged");

		// Log attempt - feed
		try {
			if ($paylink_info["create_entity_type"] == "system") {
				activity_log(
					"[".ucwords($user["name"])."] is doing a website checkout.",
					$feed_msg_user,
					"[Pricing :]",
					$feed_msg_pricing,
					"[Courses :]",
					$feed_course,
					$tags,
					[
						'c' => "info",
						'by' => $paylink_info["user_id"]
					]
				);
			}

			activity_log(
				"[".ucwords($user["name"])."] is transacting.",
				$feed_msg_user,
				"[Pricing :]",
				$feed_msg_pricing,
				"[Courses :]",
				$feed_course,
				$tags,
				[
					'c' => "info",
					'by' => $paylink_info["user_id"]
				]
			);
		}
        catch (Exception $e) {}
            
        // save user state if available
        if( !empty($user_state) ){
            db_query("UPDATE `user_meta` SET `state` = " . db_sanitize($user_state) . "  WHERE `user_meta`.`user_id` = '".$paylink_info["user_id"]."';");
        }

		payment_transact($transaction_info, "payment_link", $paylink_info["paylink_id"], "paylink.confirm", $pg);

	}

	// Normal Auth
	else {

		if (auth_session_is_logged()) {

			// Auth check - see if link belongs to the person logged in
			if (strcmp($user["user_id"], $_SESSION["user"]["user_id"]) != 0) {

				// Auth check - links belonging to partial accounts
				if (strcmp($user["status"],"pending") == 0) {

					// Havent been offered options to Log in correctly before link is 'attached'
					if (!isset($_COOKIE["attach_partial"])) {
						auth_session_logout();
						setcookie("attach_partial", '1');

						// Offer login options here (to attach the link to the right account) .......................
						activity_create("ignore", "paylink.parse", "login", "paylink_id", $paylink_info["paylink_id"], "user_id", $paylink_info["user_id"], "PayLink Login Attempt (DevInfo:Partial Account,1)", "logged");

						// Log attempt - feed
						try {
							activity_log(
								"[".ucwords($user["name"])."] is doing a custom checkout.",
								$feed_msg_user,
								"[Pricing :]",
								$feed_msg_pricing,
								"[Courses :]",
								$feed_course,
								$tags,
								[
									'c' => "info",
									'by' => $paylink_info["user_id"]
								]
							);
						}
						catch (Exception $e) {}

						ui_render_login_front(array(
							"mode" => "create",
							 "reauth" => true,
							"return_url" => $login_params["return_url"],
							"text" => "Please sign-in or register your new account.<br/>Note: Use your own social account here. You can provide payment information separately in the next step."
							));

						exit();
					}

					// Offered options already
					else {

						// Auth - restrict internal Jigsaw teams from accidentally attaching links
						if ((isset($_SESSION["user"]["roles"]["feature_keys"]["subs.paylink.parse"])) && (!auth_session_is_allowed("subs.paylink.parse")))  {

							ui_render_msg_front(array(
								"type" => "error",
								"title" => "A problem ran into you :(",
								"header" => "Oops !",
								"text" => "Sorry, but either this link cannot be used by employees of Jigsaw Academy, or you do not have the required priviledges."
							));

							exit();

						}

						// Proceed with attach
						user_attach_partial($_SESSION["user"]["user_id"], $user["user_id"]);
										setcookie("attach_partial","", time() - 3600);

						// Refresh info
						auth_session_logout();
						auth_session_login_forced($user["email"]);

						// Proceed to payment
						activity_create("ignore", "paylink.parse", "gateway", "paylink_id", $paylink_info["paylink_id"], "user_id", $paylink_info["user_id"], "Proceed to Gateway (DevInfo:2)", "logged");

						// Log attempt - feed
						try {
							activity_log(
								"[".ucwords($user["name"])."] is transacting.",
								$feed_msg_user,
								"[Pricing :]",
								$feed_msg_pricing,
								"[Courses :]",
								$feed_course,
								$tags,
								[
									'c' => "info",
									'by' => $paylink_info["user_id"]
								]
							);
						}
						catch (Exception $e) {}

						if ($show_payment_gateway_selection) {
							// payment gateway selection. - from emailed link click.
							// echo "from else part - checkout url from mail";
							if($show_neft_razorpay){
                                load_template("jaws/frontend", "pg.select.neft");
                            } else {
                                load_template("jaws/frontend", "pg.select");
                            }
							exit;
						}

                        // save user state if available
                        if( !empty($user_state) ){
                            db_query("UPDATE `user_meta` SET `state` = " . db_sanitize($user_state) . "  WHERE `user_meta`.`user_id` = '".$paylink_info["user_id"]."';");
                        }

						payment_transact($transaction_info, "payment_link", $paylink_info["paylink_id"], "paylink.confirm", $pg);
					}

				}

				// Link belongs to normal account
				else {
					activity_create("ignore", "paylink.parse", "login", "paylink_id", $paylink_info["paylink_id"], "user_id", $paylink_info["user_id"], "PayLink Login Attempt - Re (DevInfo:".$_SESSION["user"]["email"].",".$_SESSION["user"]["user_id"].")", "logged");
					auth_session_logout();

					// Log attempt - feed
					try {
						activity_log(
							"[".ucwords($user["name"])."] is doing a custom checkout.",
							$feed_msg_user,
							"[Pricing :]",
							$feed_msg_pricing,
							"[Courses :]",
							$feed_course,
							$tags,
							[
								'c' => "info",
								'by' => $paylink_info["user_id"]
							]
						);
					}
					catch (Exception $e) {}

					// Say that this link does not belong to you and show re-login options here .......................
					ui_render_login_front(array(
							"mode" => "login",
							 "reauth" => true,
							"return_url" => $login_params["return_url"],
							"text" => "This payment link does not belong to the account you are signed-in to.<br />Please sign-in to the social network you have on your account with us."
							));
					exit();
				}

			}

			// Link belongs to the person logged in
			else  {
					activity_create("ignore", "paylink.parse", "gateway", "paylink_id", $paylink_info["paylink_id"], "user_id", $paylink_info["user_id"], "Proceed to Gateway (DevInfo:3)", "logged");

					// Log attempt - feed
					try {
						if ($paylink_info["create_entity_type"] == "system") {
							activity_log(
								"[".ucwords($user["name"])."] is doing a website checkout.",
								$feed_msg_user,
								"[Pricing :]",
								$feed_msg_pricing,
								"[Courses :]",
								$feed_course,
								$tags,
								[
									'c' => "info",
									'by' => $paylink_info["user_id"]
								]
							);
						}

						activity_log(
							"[".ucwords($user["name"])."] is transacting.",
							$feed_msg_user,
							"[Pricing :]",
							$feed_msg_pricing,
							"[Courses :]",
							$feed_course,
							$tags,
							[
								'c' => "info",
								'by' => $paylink_info["user_id"]
							]
						);
					}
					catch (Exception $e) {}
					
					if ($show_payment_gateway_selection) {
						// payment gateway selection. - from emailed link click.
						// echo "from else part - checkout url from mail";
						if($show_neft_razorpay){
                            load_template("jaws/frontend", "pg.select.neft");
                        } else {
                            load_template("jaws/frontend", "pg.select");
                        }
						exit;
                    }
                    
                    // save user state if available
                    if( !empty($user_state) ){
                        db_query("UPDATE `user_meta` SET `state` = " . db_sanitize($user_state) . "  WHERE `user_meta`.`user_id` = '".$paylink_info["user_id"]."';");
                    }

					payment_transact($transaction_info, "payment_link", $paylink_info["paylink_id"], "paylink.confirm", $pg);
				}

		}

		// Not logged in
		else {

			// Check - links belonging to partial accounts
			$partialflag = false;
			if (strcmp($user["status"],"pending") == 0) {
				setcookie("attach_partial", '1');
				$partialflag = true;
			}

			// Offer login options here (maybe to attach the link to the right account) .......................
			activity_create("ignore", "paylink.parse", "login", "paylink_id", $paylink_info["paylink_id"], "user_id", $paylink_info["user_id"], "PayLink Login Attempt".($partialflag ? " (DevInfo:Partial Account,2)": ''), "logged");

			// Log attempt - feed
			try {
				activity_log(
					"[".ucwords($user["name"])."] is doing a custom checkout.",
					$feed_msg_user,
					"[Pricing :]",
					$feed_msg_pricing,
					"[Courses :]",
					$feed_course,
					$tags,
					[
						'c' => "info",
						'by' => $paylink_info["user_id"]
					]
				);
			}
			catch (Exception $e) {}

			ui_render_login_front(array(
							"mode" => $partialflag ? "create" : 'login',
							 "reauth" => $partialflag,
							"return_url" => $login_params["return_url"],
							"text" => $partialflag ? "Please sign-in or register your new account.<br/>Note: Use your own social account here. You can provide payment information separately in the next step." : "Please sign-in to your account.<br/>Select the social network you have on your account with us."
						));
			exit();
		}

	}

	exit();

?>
