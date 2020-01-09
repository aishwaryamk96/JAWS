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

	function enrollment_free_create($email, $name, $lms_auth_key, $lms_auth_value, $course_ids, $retries = 3) {

    	// Get file save path
        $sis_path = setting_get("sis_file_save_path");
        $sis_id;

        // Try to fetch an old Jig ID from a pre-existing system_activity record
        $res_enroll_prev = db_query("SELECT content FROM system_activity WHERE act_type='sis.import.automation.free' AND activity='success' AND context_type='system_activity' AND context_id IN (SELECT act_id FROM system_activity WHERE act_type='jlc.free' AND activity='setup' AND status='executed' AND content=".db_sanitize($email).") LIMIT 1;");

        if (isset($res_enroll_prev[0])) {
        	$sis_id = json_decode($res_enroll_prev[0]["content"], true)["sis_id"];
        }
        else {

        	// This is a new user to freelearning JLC, create a user file for this student
        	$sis_id = "Jig".hash("sha256", $email.time());

        	$user_lines = "user_id,login_id,authentication_provider_id,password,first_name,last_name,short_name,email,status\r\n";
        	$user_lines .= $sis_id.",".$email.",";
        	if (strcmp($lms_auth_key, "soc") == 0) {

	            if (strcmp($lms_auth_value, "fb") == 0) {
	                $user_lines .= "facebook,,";
                }
	            else if (strcmp($lms_auth_value, "gp") == 0) {
	                $user_lines .= "google,,";
                }
	            else if (strcmp($lms_auth_value, "li") == 0) {
	                $user_lines .= "linkedin,,";
                }
                else {

                    $try_user = user_get_by_email($email);

                    activity_create("critical", "sis.import.automation.free", "soc.blank", "", "", "", "", json_encode(["email" => $email, "name" => $name, "lms_auth_key" => "soc", "lms_auth_value" => $lms_auth_value]));

                    if ($try_user !== false) {

                        if (strlen($try_user["soc_gp"]) > 0) {
                            $user_lines .= "google,,";
                        }
                        else if (strlen($try_user["soc_fb"]) > 0) {
                            $user_lines .= "facebook,,";
                        }
                        else if (strlen($try_user["soc_li"]) > 0) {
                            $user_lines .= "linkedin,,";
                        }
                        else {
                            $user_lines .= "google,,";
                        }

                    }
                    else {
                        $user_lines .= "google,,";
                    }

                }

	        }
	        else {
	            $user_lines .= ",".$lms_auth_value.",";
            }

            $name = str_replace(",", "-", $name);

	        $user_lines .= $name.",,".$name.",".$email.",Active\r\n";
	        $fuser = fopen($sis_path."user-".$sis_id.".csv", "a");
	        fwrite($fuser, $user_lines);
			fclose($fuser);

        }

        // Construct the enrollment data
        $enroll_lines = "course_id,user_id,role,section_id,status\r\n";

        if (is_array($course_ids)) {

            foreach ($course_ids as $course_id) {

                $res_course_code = db_query("SELECT sis_id FROM course WHERE course_id=".$course_id);
                $course_code = $res_course_code[0]["sis_id"];
                $enroll_lines .= $course_code.",".$sis_id.",student,Free".$course_code.",active\r\n";

            }

        }
        else {

            $res_course_code = db_query("SELECT sis_id FROM course WHERE course_id=".$course_ids);
            $course_code = $res_course_code[0]["sis_id"];
            $enroll_lines .= $course_code.",".$sis_id.",student,Free".$course_code.",active\r\n";

        }

        // Write data to enrollment file
        $fenroll = fopen($sis_path."enroll-".$sis_id.".csv", "a");
        fwrite($fenroll, $enroll_lines);
        fclose($fenroll);

        // Zip the files
        $sis_file_name = "free-".$sis_id.".zip";
        $zip = new ZipArchive;
        $zip->open($sis_path.$sis_file_name, ZipArchive::CREATE);
        if (!isset($res_enroll_prev[0])) {
        	$zip->addFile($sis_path."user-".$sis_id.".csv", "user.csv");
        }
        $zip->addFile($sis_path."enroll-".$sis_id.".csv", "enroll.csv");
        $zip->close();

        // Delete the original csv files
        if (!isset($res_enroll_prev[0])) {
        	unlink($sis_path."user-".$sis_id.".csv");
        }
        unlink($sis_path."enroll-".$sis_id.".csv");

        if (!$GLOBALS["jaws_exec_live"]) {

            $lms_url = setting_get("lms_url_debug");
            $lms_dev_key = setting_get("lms_dev_key_debug");

        }
        else {

            $lms_url = setting_get("lms_url_free");
            $lms_dev_key = setting_get("lms_dev_key_free");

        }

        $result = false;
        $retry = 0;
        $old_batch_id = -1;
        if (!is_int($retries)) {
            $retries = 0;
        }
        $retry_max = ($retries == 0 ? 3 : $retries);

        // Keep trying until curl_exec() returns something positive
        while (!$result && $retry < $retry_max) {

            // We are creating a new SIS import batch
            if ($old_batch_id == -1) {

                $file = fopen($sis_path.$sis_file_name, "rb");
                $fzip = fread($file, filesize($sis_path.$sis_file_name));
                // Prepare to transfer the zip file to LMS
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $lms_url."api/v1/accounts/1/sis_imports.json?import_type=instructure_csv");
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $fzip);
                curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: Bearer ".$lms_dev_key, "Content-Type: application/zip"));
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            }
            // We are checking the status of a previously created batch
            else {

                // Send a request to the LMS with the SIS Batch ID to get it's status
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $lms_url."api/v1/accounts/1/sis_imports/".$old_batch_id);
                curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: Bearer ".$lms_dev_key));
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            }
            // Go! Go! GO!!
            $result = curl_exec($curl);

            if (!$result) {

                $retry++;
                sleep(2);
                continue;

            }

            // Check the status
            $result = json_decode($result, true);

            // All went well
            if (strcmp($result["workflow_state"], "imported") == 0) {

            	if (is_array($course_ids)) {

            		foreach ($course_ids as $course_id) {

            			$res_act_id = db_query("SELECT act_id FROM system_activity WHERE act_type='jlc.free' AND activity='setup' AND status='pending' AND entity_type='course' AND entity_id=".$course_id." AND content=".db_sanitize($email).";");
            			activity_create("low", "sis.import.automation.free", "success", "sis.batch", $result["id"], "system_activity", $res_act_id[0]["act_id"], json_encode(array("email" => $email, "name" => $name, "lms_auth_key" => $lms_auth_key, "lms_auth_value" => $lms_auth_value, "sis_id" => $sis_id, "course_id" => $course_id, "response" => $result)));

                    }

                }
            	else {

                	$res_act_id = db_query("SELECT act_id FROM system_activity WHERE act_type='jlc.free' AND activity='setup' AND status='pending' AND entity_type='course' AND entity_id=".$course_ids." AND content=".db_sanitize($email).";");
            		activity_create("low", "sis.import.automation.free", "success", "sis.batch", $result["id"], "system_activity", $res_act_id[0]["act_id"], json_encode(array("email" => $email, "name" => $name, "lms_auth_key" => $lms_auth_key, "lms_auth_value" => $lms_auth_value, "sis_id" => $sis_id, "course_id" => $course_id, "response" => $result)));

                }
                return true;

            }
            // Import failed
            else if (strcmp($result["workflow_state"], "failed") == 0 || strcmp($result["workflow_state"], "imported_with_messages") == 0 || strcmp($result["workflow_state"], "failed_with_messages") == 0) {

            	if (is_array($course_ids)) {

            		foreach ($course_ids as $course_id) {

            			$res_act_id = db_query("SELECT act_id FROM system_activity WHERE act_type='jlc.free' AND activity='setup' AND status='pending' AND entity_type='course' AND entity_id=".$course_id." AND content=".db_sanitize($email).";");
            			activity_create("critical", "sis.import.automation.free", "fail", "sis.batch", $result["id"], "system_activity", $res_act_id[0]["act_id"], json_encode(array("email" => $email, "name" => $name, "lms_auth_key" => $lms_auth_key, "lms_auth_value" => $lms_auth_value, "sis_id" => $sis_id, "course_id" => $course_id, "response" => $result)));

                    }

                }
            	else {

            		$res_act_id = db_query("SELECT act_id FROM system_activity WHERE act_type='jlc.free' AND activity='setup' AND status='pending' AND entity_type='course' AND entity_id=".$course_ids." AND content=".db_sanitize($email).";");
            		activity_create("critical", "sis.import.automation.free", "fail", "sis.batch", $result["id"], "system_activity", $res_act_id[0]["act_id"], json_encode(array("email" => $email, "name" => $name, "lms_auth_key" => $lms_auth_key, "lms_auth_value" => $lms_auth_value, "sis_id" => $sis_id, "course_id" => $course_id, "response" => $result)));

                }

                // Send a mail to programoffice@jigsawacademy.com to report the problem
	            $to = "programoffice@jigsawacademy.com";
	            $subject = "<Freelearning> SIS Import failed on LMS";

	            $message = "
	            <html>
	            <head>
	            </head>
	            <body>
	            Hi,<br /><br />
	            SIS import failed on the Freelearning LMS, and LMS responded with the following";

	            if (isset($result["processing_warnings"]) || isset($result["processing_errors"])) {

	                if (isset($result["processing_warnings"])) {

	                	$message .= " warning(s): <br /><br />";
	                    foreach ($result["processing_warnings"] as $warning) {
	                        $message .= $warning[0]." - ".$warning[1]."<br /><br />";
                        }

	                }
	                if (isset($result["processing_errors"])) {

	                	$message .= " error(s): <br /><br />";
	                    foreach ($result["processing_errors"] as $error) {
	                        $message .= $error[0]." - ".$error[1]."<br /><br />";
                        }

	                }

	            }
	            else if (isset($result["data"]["error_message"])) {

	            	$message .= " error message: <br /><br />";
	                foreach ($result["error_message"] as $error) {
	                    $message .= $error[0]." - ".$error[1]."<br /><br />";
                    }

	            }

	            $message .= "Please take a look at the issue.<br />Please <a href='https://jigsawacademy.com/jaws/view/backend/temp/sis.get?sis=".$sis_id."&free=1'>click here</a> to download the troublesome file.<br /><br />
	            Thank you,<br />
	            JAWS
	            </body>
	            </html>";

	            // More headers
	            $headers = "MIME-Version: 1.0" . "\r\n";
	            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	            $headers .= 'From: <no-reply@jigsawacademy.com>' . "\r\n";
	            $headers .= 'Cc: himanshu@jigsawacademy.com' . "\r\n";
	            mail($to, $subject, $message, $headers);

            	return false;

            }
            // Import is taking time
            else if (strcmp($result["workflow_state"], "created") == 0) {

                $old_batch_id = $result["id"];
                $retry++;
                $result = false;
                sleep(2);

            }

        }
        if ($retry >= $retry_max) {

            // Log the event with result object as description
            if (is_array($course_ids)) {

        		foreach ($course_ids as $course_id) {

        			$res_act_id = db_query("SELECT act_id FROM system_activity WHERE act_type='jlc.free' AND activity='setup' AND status='pending' AND entity_type='course' AND entity_id=".$course_id." AND content=".db_sanitize($email).";");
        			activity_create("critical", "sis.import.automation.free", "pending", "sis.batch", $result["id"], "system_activity", $res_act_id[0]["act_id"], json_encode(array("email" => $email, "name" => $name, "lms_auth_key" => $lms_auth_key, "lms_auth_value" => $lms_auth_value, "sis_id" => $sis_id, "course_ids" => $course_id, "response" => $result)));
        		}

        	}
        	else {
        		$res_act_id = db_query("SELECT act_id FROM system_activity WHERE act_type='jlc.free' AND activity='setup' AND status='pending' AND entity_type='course' AND entity_id=".$course_ids." AND content=".db_sanitize($email).";");
        		activity_create("critical", "sis.import.automation.free", "pending", "sis.batch", $result["id"], "system_activity", $res_act_id[0]["act_id"], json_encode(array("email" => $email, "name" => $name, "lms_auth_key" => $lms_auth_key, "lms_auth_value" => $lms_auth_value, "sis_id" => $sis_id, "course_ids" => $course_ids, "response" => $result)));

            }

            return false;

        }

    }

?>