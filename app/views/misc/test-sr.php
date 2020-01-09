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
		header('Location: https://www.jigsawacademy.com/');
		die();
	}

    // Start Output Buffering - Optional
    ob_start();

?>
<!DOCTYPE html>
    <html>
    <head>
        <title>JAWS - Test Page</title>
        <style>
            body {
                display:block;

                background-color: #FFF;
                color: #000;
                font-family: "consolas", "sans-serif";
                font-size: 90%;

                margin: 0;
                padding: 20px;

                overflow-x: hidden;
                overflow-y: scroll;
            }
        </style>
    </head>
    <body>

<?php

    error_reporting(E_ALL);
	ini_set("display_errors",1);

    echo "JAWS v".JAWS_VERSION, "<br/>Jigsaw Academy Workflow System", "<br/>Running PHP v".phpversion()." on ".$_SERVER['SERVER_SOFTWARE'], "<br/>&copy; Jigsaw Academy Education Pvt. Ltd.<br/>";
    echo "<br/>";

    echo "JAWS Test Routine - Testing Auto Loaders";
    echo " <br />";

    echo "Loading auto loaders..";
    echo " &#10004;<br /><br />";

    echo "JAWS Test Routine - Testing Manual Loaders";
    echo " <br />";

    echo "Loading ui..";
    load_module("ui");
    echo " &#10004;<br />";

    echo "Loading user..";
    load_module("user");
    echo " &#10004;<br />";

	echo "Loading user enrollment..";
    load_module("user_enrollment");
    echo " &#10004;<br />";

    echo "Loading course..";
    load_module("course");
    echo " &#10004;<br />";

    echo "Loading subs..";
    load_module("subs");
    echo " &#10004;<br />";

    echo "Loading leads..";
    load_module("leads");
    echo " &#10004;<br />";

    echo "Loading payment lib..";
    load_library("payment");
    echo " &#10004;<br />";

    echo "Loading email lib..";
    load_library("email");
    echo " &#10004;<br />";

    echo "Loading misc lib..";
    load_library("misc");
    echo " &#10004;<br />";

    echo "<br />";
    echo "Test Routine Successfull - Testing Custom Code..";
    echo "<br /><br />";

    // ----- CUSTOM CODE ---------------------------------------------------------
    // --------------------------------------------------------------------------------

    // $GLOBALS['jaws_exec_live'] = false;


	// Auto load following modules when this is loaded
	// load_module("user");
	// load_module("course");
	// load_library("email");

	// send_email_api("subs.instl.notify.due", "komal_sharma228@yahoo.com", 13070, 2, 10124 );
	// send_email_api("lms.setup", "komal_sharma228@yahoo.com");
	// send_email_api("subs.init.success", "meghashree.rao@gmail.com", 21335, 4, 10124);
	// send_email_api("subs.instl.success", "Srinivas.audina@gmail.com", 18480, 2, 10124); // 2nd paid
	// send_email_api("subs.instl.notify.due", "sandeep.kudva@gmail.com", 18652, 1, 10124); // 1st paid
	// send_email_api("subs.init", "komal_sharma228@yahoo.com", 13070, 2, 10124);
	// send_email_api("subs.instl.success", "komal_sharma228@yahoo.com", 13070, 2, 10124);
	// send_email_api("subs.init.success", "kunalayush212@gmail.com", 21627, 1, '2da29890bdec234aff0d8ea');

	// mail_check('2017-05-25.14.00.02');

	// ac_contact_add();

	 /* function send_email_api($context, $email, $sub_id = "", $instl_count = "", $web_id = ""){

		$error = array();

		if( empty($context) ){
			$error["message"]["context"] = "Please provide context.";
		}

		if( empty($email) ){
			$error["message"]["email"] = "Please provide email.";
		}

		if( $context !== "lms.setup" ){

			if( empty($sub_id) ){
				$error["message"]["sub_id"] = "Please provide sub id.";
			}

			if( empty($instl_count) ){
				$error["message"]["instl"] = "Please provide instalment count";
			}

			if( empty($web_id) ){
				$error["message"]["web_id"] = "Please provide web id";
			}

			if( empty($error) ){

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

					if (isset($combo_arr_free_exclusive[$course_id])){
						$course[$count]["free"] = true;
					} else {
						$course[$count]["free"] = false;
					}

					$count ++;
                }

                $bundle_details = array(); $batch_details = array();
                if(!empty($subs_meta['bundle_id'])){
                    $bundle_details = db_query("SELECT * FROM `course_bundle` WHERE `bundle_id` =". $subs_meta['bundle_id'] . ";");
                    if(!empty($subs_meta['batch_id'])){
                        $batch_details = db_query("SELECT * FROM `bootcamp_batches` WHERE `id` =". $subs_meta['batch_id'] . ";");
                    }
                }

                $content["batch_details"] = $batch_details[0];
                $content["bundle_details"] = $bundle_details[0];
				$content["paylink_id"] = $web_id;
				$content["sum"] = $payment_details["instl"][$instl_count]["sum"];
				$content["currency"] = $payment_details["instl"][$instl_count]["currency"];
				$content["courses"] = $course;
				$content["payment"] = $payment_details;

			} else {

				echo json_encode($error);

			}
		}

		if( empty($error) ){

			$user = user_get_by_email($email,true);

			// Prep email content
			$content["user_webid"] = $user["web_id"];
			$content["fname"] = substr($user["name"], 0, ((strpos($user["name"], " ") !== false) ? strpos($user["name"], " ") : strlen($user["name"])));

			$sent_mail = false;

			switch($context){

				case "subs.init.success":
					if ((!isset($user["lms_soc"])) || (strlen($user["lms_soc"]) == 0)){
						$content["allow_setup"] = true;
					} else {
						$content["allow_setup"] = false;
					}

					// Send Emails
					$template_email = "subs.init.success";

					$email = "sreepati@jigsawacademy.com";
					send_email($template_email, array("to" => $email), $content, true);
                    echo "init-success"; exit;
					$sent_mail = true;

				break;

				case "lms.setup":

					$content = array();

					// Prep email !
					$content["fname"] = substr($user["name"], 0, ((strpos($user["name"], " ") !== false) ? strpos($user["name"], " ") : strlen($user["name"])));

					// Done! Send an email
					$template = "lms.setup.success";

					$email = "sreepati@jigsawacademy.com";
					send_email($template, array("to" => $email), $content, true);

					$sent_mail = true;

				break;

				case "subs.init":

					if ((!isset($user["lms_soc"])) || (strlen($user["lms_soc"]) == 0)){
						$content["allow_setup"] = true;
					} else {
						$content["allow_setup"] = false;
					}

					// Send Emails
					$template_email = "subs.init";
					// $template_email = "new.payment";

					$email = "sreepati@jigsawacademy.com";
					send_email($template_email, array("to" => $email), $content, true);
                    exit;
					$sent_mail = true;

				break;

				case "subs.instl.success":
					// instl_count > 1
					$paylink_info["instl_count"] = 2;

					$content["instl_count"] = $paylink_info["instl_count"];

					// Send Emails
					$template_email = "subs.init.success.test";

					$email = "sreepati@jigsawacademy.com";
					send_email($template_email, array("to" => $email), $content, true);

					$sent_mail = true;

				break;

				case "subs.instl.notify.due":

					// Prep email Content
					$content["email"] = $user["email"];
					$content["phone"] = $user["phone"];
					$content["due_date"] = $row["due_date"];
					$content["instl_count"] = strval($row["instl_count"]);
					$content["instl_total"] = strval($row["instl_total"]);
					$content["instl"] = $payment_details["instl"];

					// Send Emails
					$template_email = "subs.init.success.test";

					$email = "sreepati@jigsawacademy.com";
					send_email($template_email, array("to" => $email), $content, true);

					$sent_mail = true;

				break;

			}

			if( $sent_mail == true ){
				echo json_encode(array( "status" => true, "message" => $context . " mail sent successfully."));
			} else {
				echo json_encode(array( "status" => false, "message" => $context . " mail could not be sent. Please try again." ));
			}

		} else {
			echo json_encode($error);
		}
	}

  /*
	function mail_check($sis_file){
	        $res_enrs = db_query("SELECT
                                enr.*,
                                user.name, user.email, user.lms_soc, user.soc_fb, user.soc_gp, user.soc_li,
                                course.name AS course_name, course.sis_id AS course_sis_id,
                                subs.start_date, subs.end_date, subs.end_date_ext,
                                bundle.name AS bundle_name
                            FROM
                                user_enrollment AS enr
                            INNER JOIN
                                user ON user.user_id = enr.user_id
                            INNER JOIN
                                subs ON subs.subs_id = enr.subs_id
                            INNER JOIN
                                subs_meta ON subs_meta.subs_id = subs.subs_id
                            INNER JOIN
                                course ON course.course_id = enr.course_id
                            LEFT JOIN
                                course_bundle AS bundle ON bundle.bundle_id = subs_meta.bundle_id
                            WHERE
                                shall_notify=1
                                AND
                                sis_file=".db_sanitize($sis_file)." ORDER BY enr_id ASC");

            foreach ($res_enrs as $enr) {
	        if (!isset($message_content[$enr["email"]])) {

                $message_content[$enr["email"]]["name"] = $enr["name"];
                $message_content[$enr["email"]]["lms_soc"] = $enr["lms_soc"];
                $message_content[$enr["email"]]["login"] = ($enr["lms_soc"] != "corp" ? $enr["soc_".$enr["lms_soc"]] : $enr["sis_id"]);

                $message_content[$enr["email"]]["bundle_name"] = $enr["bundle_name"];

                $message_content[$enr["email"]]["sis_id"] = $enr["sis_id"];
                $message_content[$enr["email"]]["lms_pass"] = $enr["lms_pass"];
                $message_content[$enr["email"]]["lab_user"] = $enr["lab_user"];
                $message_content[$enr["email"]]["lab_pass"] = $enr["lab_pass"];
                $message_content[$enr["email"]]["start_date"] = $enr["start_date"];
                $end_date = (empty($enr["end_date_ext"]) ? $enr["end_date"] : $enr["end_date_ext"]);

                $start_date = date_create_from_format("Y-m-d H:i:s", $enr["start_date"]);
                $end_date = date_create_from_format("Y-m-d H:i:s", $end_date);

                $message_content[$enr["email"]]["end_date"] = $end_date->format("j F, Y");
                $message_content[$enr["email"]]["duration"] = ceil(floatval($start_date->diff($end_date)->format("%a")) / 30);

                $message_content[$enr["email"]]["enr"] = [];
                $message_content[$enr["email"]]["lab"] = [];
                $message_content[$enr["email"]]["attachments"] = [];
                $message_content[$enr["email"]]["attachments"][] = "media/misc/attachments/jlc/Jigsaw_Learning_Center_Login_Guide.pdf";

            }

            $message_content[$enr["email"]]["enr"][] = ["course_id" => $enr["course_id"], "name" => $enr["course_name"], "learn_mode" => ($enr["learn_mode"] == "sp" ? "Regular" : "Premium")];

            if (!empty($enr["lab_ip"])) {

                if (!isset($message_content[$enr["email"]]["lab"][$enr["lab_ip"]])) {
                    $message_content[$enr["email"]]["lab"][$enr["lab_ip"]] = [];
                }
                $message_content[$enr["email"]]["lab"][$enr["lab_ip"]][] = $enr["course_name"];

            }

            $section = section_get_by_id($enr["section_id"]);
            if ($section["learn_mode"] == ($enr["learn_mode"] == "sp" ? "2" : "1")) {
                $message_content[$enr["email"]]["attachments"][] = "media/misc/attachments/jlc/prog_calendar/".$enr["course_id"]."/".$section["sis_id"].".pdf";
            }
	 	}

		foreach ($message_content as $email => $mail) {
			// print_r($mail); exit;
            send_email_with_attachment2("sis.welcome.email", ["to" => "sreepati@jigsawacademy.com"], $mail, $mail["attachments"]);
        }
	}

	function send_email_with_attachment2($template, $email_info, $content, $attachments = array()) {

		$res = db_query("SELECT * FROM system_email WHERE template_id=".db_sanitize($template).";");
		if (!isset($res[0])) return false;

		// Load content
		email_content_load($content);

		// Prep
		$from = explode(",", isset($email_info["from"]) ? $email_info["from"] : $res[0]["sender"]);
		$to = $GLOBALS["jaws_exec_live"] ? (isset($email_info["to"]) ? $email_info["to"] : "").(isset($res[0]["recipient"]) ? (",".implode(",", explode(";", $res[0]["recipient"]))) : "") : $GLOBALS["jaws_exec_test_email_to"];
		$cc = $GLOBALS["jaws_exec_live"] ? (isset($email_info["cc"]) ? $email_info["cc"] : "").(isset($res[0]["copied"]) ? (",".implode(",", explode(";", $res[0]["copied"]))) : "") : "";
		$bcc = $GLOBALS["jaws_exec_live"] ? (isset($email_info["bcc"]) ? $email_info["bcc"] : "").(isset($res[0]["copied_blind"]) ? (",".implode(",", explode(";", $res[0]["copied_blind"]))) : "") : "";
		$subject = isset($email_info["subject"]) ? $email_info["subject"] : $res[0]["subject"];

		// Render
		$content = render_email($res[0]["template_path"]);
		if (!$content) return false;

		// set data
		$boundary = '----=_NextPart_' . md5(uniqid(time()));

		// set headers
		$header = 'MIME-Version: 1.0' . PHP_EOL;
		$header .= 'Date: ' . date('D, d M Y H:i:s O') . PHP_EOL;
		$header .= 'From: '.(isset($from[1]) ? '=?UTF-8?B?' . base64_encode($from[1]) . '?= <' : '<') . $from[0] . '>' . PHP_EOL;
		$header .= 'Reply-To: '.(isset($from[1]) ? '=?UTF-8?B?' . base64_encode($from[1]) . '?= <' : '<') . $from[0] . '>' . PHP_EOL;
		$headers .= 'Cc: '.$cc . PHP_EOL;
		$headers .= 'Bcc: '.$bcc . PHP_EOL;
		$header .= 'Return-Path: ' . $from[0] . PHP_EOL;
		$header .= 'X-Mailer: PHP/' . phpversion() . PHP_EOL;
		$header .= 'Content-Type: multipart/mixed; boundary="' . $boundary . '"' . PHP_EOL . PHP_EOL;

		// create message
		$message  = '--' . $boundary . PHP_EOL;
		$message .= 'Content-Type: multipart/alternative; boundary="' . $boundary . '_alt"' . PHP_EOL . PHP_EOL;
		$message .= '--' . $boundary . '_alt' . PHP_EOL;
		$message .= 'Content-Type: text/html; charset="utf-8"' . PHP_EOL;
		$message .= 'Content-Transfer-Encoding: 8bit' . PHP_EOL . PHP_EOL;
		$message .= $content . PHP_EOL;
		$message .= '--' . $boundary . '_alt--' . PHP_EOL;

		// add attachments
		if(!empty($attachments)) {
			foreach ($attachments as $attachment) {
				if (file_exists($attachment)) {
					$handle = fopen($attachment, 'r');
					$content = fread($handle, filesize($attachment));
					fclose($handle);
					$message .= '--' . $boundary . PHP_EOL;
					$message .= 'Content-Type: application/octet-stream; name="' . basename($attachment) . '"' . PHP_EOL;
					$message .= 'Content-Transfer-Encoding: base64' . PHP_EOL;
					$message .= 'Content-Disposition: attachment; filename="' . basename($attachment) . '"' . PHP_EOL;
					$message .= 'Content-ID: <' . urlencode(basename($attachment)) . '>' . PHP_EOL;
					$message .= 'X-Attachment-Id: ' . urlencode(basename($attachment)) . PHP_EOL . PHP_EOL;
					$message .= chunk_split(base64_encode($content));
				}
			}
		}
		$message .= '--' . $boundary . '--' . PHP_EOL;
		// print_r($message);
		print_r($to); print_r($header);

		//send the mail
		// mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $message, $header);
	}

	function ac_contact_add(){

		$url = 'https://jigsawacademyautomation.api-us1.com';

		$params = array(
			'api_key'      => '515ad0ae47d0eb00af9c4ea6033fb85734ff3ebb25fc2f0fb72a8aeec93dac452eb69796',
			'api_action'   => 'contact_add',
			'api_output'   => 'json',
		);

		$post = array(
			'email'                    => 'test@example.com',
			'first_name'               => 'FirstName',
			'last_name'                => 'LastName',
			'phone'                    => '+1 312 201 0300',
			'orgname'                  => 'Testing',
			'tags'                     => 'api,registration',
		);

		$query = "";
		foreach( $params as $key => $value ){ $query .= urlencode($key) . '=' . urlencode($value) . '&'; }
		$query = rtrim($query, '& ');
		$data = "";
		foreach( $post as $key => $value ){ $data .= urlencode($key) . '=' . urlencode($value) . '&'; }
		$data = rtrim($data, '& ');
		$url = rtrim($url, '/ ');

		// define a final API request - GET
		$api = $url . '/admin/api.php?' . $query;

		$request = curl_init($api);
		curl_setopt($request, CURLOPT_HEADER, 0);
		curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($request, CURLOPT_POSTFIELDS, $data);
		curl_setopt($request, CURLOPT_FOLLOWLOCATION, true);

		$response = (string)curl_exec($request);
		curl_close($request);

		if ( !$response ) {
			die('Nothing was returned. Do you have a connection to Email Marketing server?');
		}

		$result = json_decode($response,true);

		// Result info that is always returned
		echo 'Result: ' . ( $result['result_code'] ? 'SUCCESS' : 'FAILED' ) . '<br />';
		echo 'Message: ' . $result['result_message'] . '<br />';

		// The entire result printed out
		echo 'The entire result printed out:<br />';
		echo '<pre>';
		print_r($result);
		echo '</pre>';

		// Raw response printed out
		echo 'Raw response printed out:<br />';
		echo '<pre>';
		print_r($response);
		echo '</pre>';

		// API URL that returned the result
		echo 'API URL that returned the result:<br />';
		echo $api;

		echo '<br /><br />POST params:<br />';
		echo '<pre>';
		print_r($post);
		echo '</pre>';
    }

    // bulk_email_send();
    function bulk_email_send(){

        $data = array(
            0 => array(
                'name' => 'Support',
                'email' => 'support@jigsawacademy.com'
            ),
            1 => array(
                'name' => 'Qurratulayn Yasin',
                'email' => 'qurrat@jigsawacademy.com'
            ),
            2 => array(
                'name' => 'Jagruti Dusane',
                'email' => 'jagruti@jigsawacademy.com'
            )
        );

        // $data = file("external/temp/bootcamp.csv");

        $usermail_header = "Welcome";
        $usermail_subheader = "";
        $usermail_text = "<br>Congratulations! It is great to have you as part of the <b>Full Stack Data Science Bootcamp</b> program.<br><br>If not done yet, please fill in the <a href='https://www.jigsawacademy.com/full-stack-data-science-bootcamp-program/'>Full Stack Data Science Bootcamp registration form</a> ASAP. This will help us understand your profile better. <b>We will conduct a 1:1 telephonic counselling session after the registration is complete. This will help you understand how to leverage the course and which aspects to fous on for a superior analytics career.</b><br><br>We are starting in-person classes for the Bootcamp from Saturday, 26th May. Classes will happen on both Saturday and Sunday for the next 10 weeks and detailes will be updated in the calendar section of the Jigsaw Learning Center (JLC). Classes will commence from 9:30 AM through 5:30 PM. Please be on time as the faculty will start at 9:30 AM, and you do not want to miss any details.<br><br>We are sharing the Program FAQ's and Calender for your perusal. ​Plese ensure you go through them well before you come in for the 1st session. In case you have any questions, we can address them in person.<br><br>The learning plan for each session will be shared on Tuesday. For the upcoming inaugural class, there is no pre-reading requisite.<br><br>Additonal updates ahead of the in-person classes:<ol><li>Venue of the class<ol type='a'><li>Jigsaw Academy<br>No. 308, 2nd Floor, 100ft Main Road, Indiranagar, 1st Stage, Bangalore-560038<br>Landmark: Above Domino’s Pizza</li></ol></li><li>You will need to bring your laptop and charger for all the in-person classes<ol type='a'><li>Minimum laptop configuration is 4GB RAM is recommended.</li><li>Microsoft Windows (8 or later editions) and Office needs to be installed</li></ol></li><li>You need to have admin rights to the laptop as you will need to install software to the laptop<ol type='a'><li>Your laptop needs to have MS Excel for the first weekend session</li><li>We will also help you to install R and Python software at suitable points in the course</li></ol></li><li>Also, please bring a notepad and pen if you want to take notes</li><li>Jigsaw will provide tea/coffee during the breaks. Lunch arrangements are your responsibility</li></ol><br>Please connect with Shinoj from Program Office or Chandan from Support team if there are any concerns when you come for the in-person session.<br><br>Look forward to meeting you on 26th May.<br><br>";

        foreach( $data as $d){

        	// $info = explode(",", $d);

            // $content["fname"] = trim($info[1]);
            $content["fname"] = $d["name"];
            $content["header"] = $usermail_header;
            $content["sub-header"] = $usermail_subheader;
            $content["text"] = $usermail_text;
            $content["subject"] = "Full Stack Data Science Bootcamp: Updates";

            $template = "lead.acknw-support";
			send_email($template, array("to" => $d['email'], "subject" => "Full Stack Data Science Bootcamp: Updates"), $content,true);
			echo "done";
            exit;
        }
        echo "done-completed";
	}  ?>

	<form action="" method="post">
		<input type="text" name="from" placeholder="FROM Number">
		<input type="text" name="to" placeholder="TO Number">
		<input type="submit" value="Call">
	</form>

	<?php

	if(!empty($_POST['from']) && !empty($_POST['to'])){

		load_plugin('exotel');
		print_r(connect_call_mcube($_POST['from'],$_POST['to']));
	} */


	// load_plugin('razorpay');
	// echo '<pre>';
	// razorpay_capture('pay_AI45RKqgxmEVyM', 41301);
    // exit;

    /*function package_exec_dummy($package_id) {

        load_module("subs");

		package_log($package_id);

		$res_package = db_query("SELECT * FROM package WHERE package_id=".$package_id.";");
		if (!isset($res_package[0])) {
			return false;
		}

		$res_package = $res_package[0];
		$subs_info["package_id"] = $package_id;
		$subs_info["combo"] = $res_package["combo"];
		$subs_info["combo_free"] = $res_package["combo_free"];
		//$subs_info["agent_id"] = $res_package["creator_id"];
		$subs_info["bundle_id"] = $res_package["bundle_id"];
		$subs_info["batch_id"] = $res_package["batch_id"];

		$serialized = json_decode($res_package["serialized"], true);
		if (!empty($serialized["course_start_date"])) {

			$start_date = date_create_from_format("d/m/Y", $serialized["course_start_date"]);
			$subs_info["start_date"] = $start_date->format("Y-m-d H:i:s");

		}

		$pay_info["status"] = "pending";
		if (!empty($res_package["pay_mode"]) && strcmp($res_package["pay_mode"], "online") != 0) {
			$pay_info["status"] = "paid";
		}
		$pay_info["currency"] = $res_package["currency"];
		$pay_info["sum_basic"] = $res_package["sum_basic"];
		$pay_info["sum_total"] = $res_package["sum_total"];
		$pay_info["instl_total"] = $res_package["instl_total"];
		$pay_info["instl"] = json_decode($res_package["instl"], true);
		$pay_info["agent_id"] = $res_package["creator_id"];

		$package_meta = json_decode($res_package["serialized"], true);
		$pay_info["sum_offered"] = $package_meta["data_offered_amount"];
		$pay_info["tax_amount"] = $package_meta["data_tax_amount"];

		// prep Instl creator
		if (isset($pay_info["agent_id"]) && (strlen($pay_info["agent_id"]) > 0)) {

			$count = 1;
			while ($count <= intval($pay_info["instl_total"])) {

				$pay_info["instl"][$count]["create_entity_type"] = "user";
				$pay_info["instl"][$count]["create_entity_id"] = $pay_info["agent_id"];
				$pay_info["instl"][$count]["pay_mode"] = $res_package["pay_mode"];
				$count ++;

			}

		}

		// Removed The following debug mode code -
		//$GLOBALS['jaws_exec_live'] = false;
		subscribe($res_package["email"], $subs_info, $pay_info, false, true, $res_package["name"]);
		//$GLOBALS['jaws_exec_live'] = true;

		db_exec("UPDATE package SET status='executed' WHERE package_id=".$package_id.";");

    }

    package_exec_dummy();*/

    /*function parseCSVsendMail(){
        // echo "hi";

        $csvFile = file(''); // file deleted
        $data = [];
        foreach ($csvFile as $line) {
            $data[] = str_getcsv($line);
        }
        unset($data[0]);

        $content = array();
        $content['title'] = 'Your Jigsaw Learning Center Account is Ready!';
        $template_email = "sis.welcome.email.lmg";
        $attachment = array(
            0 => 'external/temp/Jigsaw_Learning_Center_Login_Guide_-_Corporate.pdf', // file deleted
        );

        foreach($data as $user){

            $content['name'] = $user[4] /*. " " . $user[5]* /;
            $content['lab_user'] = $user[0];
            $content['lab_pass'] = $user[3];
            send_email_with_attachment($template_email, array("to" => $user[1]), $content, $attachment);

        }


        // send_email($template_email, array("to" => $user[1]), $content, true);
        // $email = "sreepati@jigsawacademy.com";

        // exit;
    }

    parseCSVsendMail(); */

    /*load_plugin('mpdf');
    hello();
    function hello(){
        echo "inside hello<br>";
        $sub_id = '21674'; // usd
        $sub_id = '21767'; // inr
        $installment_id = '22826';
        $state = 'karnataka';
        $data = array(
            'subs_id' => $sub_id,
            'name' => 'Sreepati',
            'email' => 'sreepati@jigsawacademy.com',
            'instl' => $installment_id,
            'state' => $state,
            'watermark' => true,
            'test_mode' => true
        );

        // will insert into db and create receipts. add state before making it live.
        $pdf = new PDFgen($data);
        echo '<br>file=='.$pdf->create_from_subs();
        $pdf->deleteFileFromServer();
        exit;
    }
 */



 /* $content= array(
	 "fname" => 'sreepati',
     "header" => 'We have received your enquiry!',
     "sub-header" => 'Thank you for your interest in our program.',
     "text" => "",
	 "mail_data"=>array(
		'post_id' => '12345',
        "name" => 'PGPDM',
        "temporary_email_template" => 'https://www.google.com',
        "post_url" => 'https://www.google.com',
    ));



    $content['mail_data']['earn_text'] = 'Salary Ranges';
    $content['mail_data']['work_text'] = 'Companies that hire';
    $content['mail_data']['work_description'] = '';
    $content['mail_data']['earn_description'] = 'Rs 6,00,00 to Rs 12,00,000';
    $content['mail_data']['earn_description_usd'] = 'Rs 15,00,000 to Rs 22,00,000';
    $content['mail_data']['footer_text'] = 'PGPDM';
    $content['mail_data']['footer_description'] = 'After completing this course you would have acquired a certification from the University of Chicago and technical analytics skills that drive business success, developing expertise in Machine Learning, AI and Deep Learning. You will have trained in making decisions with statistical software tools combined with case studies. ';
    $content['mail_data']['bgcolor'] = '#7f1416';

    $content['mail_data']['opportunity_text'] = 'GREATER NUMBER OF ROLE OPPOTUNITIES WHERE YOU WILL HAVE TO USE SKILLS LIKE';
    $content['mail_data']['opportunity_icons'] = '';
    $content['mail_data']['university_text'] = 'UNIVERSITY OF CHICAGO CERTIFICATION – GLOBALLY RECOGNIZED';
    $content['mail_data']['university_icons'] = '';

    $content['mail_data']['salary_text_indian'] = '( at Entry Level ) ';
    $content['mail_data']['salary_text_us'] = '( at mid level ) ';


 send_email("lead.acknw-pathfindertest", array("to" => "sreepati@jigsawacademy.com"), $content,
//  array(0=>'/var/www/live/html/jaws/media/misc/attachments/lp/2018/cipher/JIGSAW-EVERYDAY-EXCEL-TRICKS-2016.pdf'),
true);
 */




/* function demo_email(){
    $content = array();
    send_email("lead.acknw-test", array( 'to' => 'sreepati@jigsawacademy.com' ),$content);
}
demo_email(); */



?>


    <br /><br />
    Custom code execution complete - Ciao!
    </body>
