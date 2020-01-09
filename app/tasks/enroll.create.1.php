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

  	// Load prerequisites
  	load_module("user");
    load_module("user_enrollment");

    // Get all the subscriptions that should start today or before, i.e., start_date <= today's date and status=pending
    $date_today = new DateTime("now");
	$date_today = db_sanitize($date_today->format("Y-m-d"));
	$res_subs = db_query("SELECT * FROM subs WHERE (start_date <= DATE_ADD(".$date_today.", INTERVAL 1 DAY)) AND status='pending';");

    // If subscriptions found that satisfy the criteria, work on them
    if ($res_subs)
    {
	    foreach ($res_subs as $res_sub)
	    {
	    	$user = user_get_by_id($res_sub["user_id"]);
	    	if (strlen($res_sub["corp"]) > 0 || (isset($user["lms_soc"]) && strlen($user["lms_soc"]) > 0))
	        	enrollment_create($res_sub["subs_id"]);
	    }
	}

	// Now start the SIS import and lab file creation

    // Load prerequisites
    load_module("activity");
    load_library("setting");

    // Check if debug mode is on
    $debug_state = setting_get("ut_debug_mode");
    if ($debug_state)
        $debug_state = ((strcmp($debug_state, "true") == 0) ? true : false);

    //========================================================== SIS import Automation STARTS Here ==========================================================//

    // If the debug mode is on, work with beta LMS
    if ($debug_state)
    {
        $lms_url = setting_get("lms_url_debug");
        $lms_dev_key = setting_get("lms_dev_key_debug");
    }
    else
    {
        $lms_url = setting_get("lms_url");
        $lms_dev_key = setting_get("lms_dev_key");
    }

    // Proceed with new SIS import
    // Get current date and set as filename timestamp
    $date = new DateTime("now");
    $filename_timestamp = $date->format("Y-m-d.H.i.s");
    // Get the name of the last SIS file (only the timestamp portion of the file is storred in db, it is prepended with "user-"/"enroll-"/"zip-" and  appended with ".csv"/".zip")
    $filename = setting_get("last_sis_file");
    if (!$filename || strcmp($filename, "nil") == 0 || strlen($filename) == 0)
        $filename = $filename_timestamp;

    // Start with checking the status of any old SIS import batches
    $old_sis_batch_id = setting_get("pending_sis_import_id");
    if ($old_sis_batch_id && strcmp($old_sis_batch_id, "nil") != 0)
    {
        // Send a request to the LMS with the SIS Batch ID to get it's status
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $lms_url."api/v1/accounts/1/sis_imports/".$old_sis_batch_id);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: Bearer ".$lms_dev_key));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($curl);

        $result = json_decode($result, true);
        if (strcmp($result["workflow_state"], "imported") == 0)
        {
            // If the SIS import was successful, update sis_status to 'ul' for the corresponding records
            db_exec("UPDATE user_enrollment SET sis_status='ul' WHERE sis_file=".db_sanitize(setting_get("last_sis_file")));
            setting_set("pending_sis_import_id", "nil");
        }
        else
        {
            // Log the error and send some notification
            activity_create("critical", "sis.import.automation", "fail", "", "", "", "", "Import of sis-".$filename.".zip failed on LMS", "pending");
            // Send a mail to programoffice@jigsawacademy.com to report the problem
            $to = "programoffice@jigsawacademy.com";
            $subject = "SIS Import failed on LMS";

            $message = "
            <html>
            <head>
            </head>
            <body>
            Hi,<br /><br />
            SIS import failed on the LMS, and LMS responded with the following";

            if (isset($result["processing_warnings"]) || isset($result["processing_errors"]))
            {
                if (isset($result["processing_warnings"]))
                {
                	$message .= " warning(s): <br /><br />";
                    foreach ($result["processing_warnings"] as $warning)
                        $message .= $warning[0]." - ".$warning[1]."<br /><br />";
                }
                if (isset($result["processing_errors"]))
                {
                	$message .= " error(s): <br /><br />";
                    foreach ($result["processing_errors"] as $error)
                        $message .= $error[0]." - ".$error[1]."<br /><br />";
                }
            }
            else if (isset($result["data"]["error_message"]))
            {
            	$message .= " error message: <br /><br />";
                foreach ($result["error_message"] as $error)
                    $message .= $error[0]." - ".$error[1]."<br /><br />";
            }

            $message .= "Please take a look at the issue.<br />Please <a href='https://jigsawacademy.com/jaws/view/backend/temp/sis.get?sis=".$filename."'>click here</a> to download the troublesome file.<br /><br />
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

            setting_set("pending_sis_import_id", "nil");
        }
    }

  	// Get the enrollments that have SIS status as 'na'
    $res_sis = db_query("SELECT enr_id, user_id, subs_id, course_id, sis_id, lms_pass, section_id FROM user_enrollment WHERE sis_status='na'");

    if ($res_sis)
    {
        $filename = $filename_timestamp;

        $count = count($res_sis);

        // Set the header row for each file
        $user_lines = "user_id,login_id,authentication_provider_id,password,first_name,last_name,short_name,email,status\r\n";
        $enroll_lines = "course_id,user_id,role,section_id,status\r\n";

        $prev_user_id = -1;
        foreach ($res_sis as $sis_record)
        {
            // Get the status for each subscription
        	$res_subs = db_query("SELECT start_date, status FROM subs WHERE subs_id=".$sis_record["subs_id"]);
    	    $res_subs = $res_subs[0];

            // Set the defaults, which will be manipulated according to the subscription status
    	    $status = "active";
        	$role = "student";
            // New user specifies whether or not the user record should be generated, it is only set to true if the student has only one inactive subscription
            // Inactive subscription may also mean, expired subscriptions.
            // If the user has only one inactive subscription which is also expired, then the student record can be deleted from the JLC.
        	$new_user = false;

            // If the subscription status is 'frozen' or 'blocked', the enrollment status will be 'inactive'
        	if (strcmp($res_subs["status"], "frozen") == 0 || strcmp($res_subs["status"], "blocked") == 0)
        		$status = "inactive";
            // If the subscription status is 'expired', the enrollment status will be 'deleted'
        	else if (strcmp($res_subs["status"], "expired") == 0)
        		$status = "deleted";
            // If the subscription status is 'alumni', the role of the user for the present enrollment will be 'alumni'
        	else if (strcmp($res_subs["status"], "alumni") == 0)
        		$role = "alumni";
            // The subscription status is probably 'active', which is the normal scenario
        	else
            {
                // Get count of previous subscriptions to know if the user is new
                $res_subs_count = db_query("SELECT COUNT(subs_id) FROM subs WHERE user_id=".$sis_record["user_id"]." AND start_date<='".$res_subs["start_date"]."' AND status!='inactive'");
                if ($res_subs_count[0]["COUNT(subs_id)"] == 1)
        	        $new_user = true;
            }

        	if (($prev_user_id != $sis_record["user_id"]) && $new_user)
        	{
    	    	$res_user = db_query("SELECT name, email, lms_soc, soc_fb, soc_gp, soc_li FROM user WHERE user_id=".$sis_record["user_id"]);
    	    	$res_user = $res_user[0];

    	    	if (strcmp($res_user["lms_soc"], "fb") == 0)
    	    		$soc = "facebook";
    	    	else if (strcmp($res_user["lms_soc"], "gp") == 0)
    	    		$soc = "google";
    	    	else if (strcmp($res_user["lms_soc"], "li") == 0)
    	    		$soc = "linkedin";
                else if (strcmp($res_user["lms_soc"], "corp") == 0)
                    $soc= "";
                else
                    continue;

    	    	$user_lines .= $sis_record["sis_id"].",".($res_user["lms_soc"] == "corp" ? $res_user["email"] : $res_user["soc_".$res_user["lms_soc"]]).",".$soc.",".$sis_record["lms_pass"].",".str_replace(",", "-", $res_user["name"]).",,".str_replace(",", "-", $res_user["name"]).",".$res_user["email"].",";
                // If all the subscriptions of this user have expired, delete the student also
                if (strcmp($status, "deleted") == 0)
                    $user_lines .= "deleted\r\n";
                else
                    $user_lines .= "active\r\n";
        	}

        	$res_course_sis_id = db_query("SELECT sis_id FROM course WHERE course_id=".$sis_record["course_id"]);
        	$res_course_sis_id = $res_course_sis_id[0];

        	$enroll_lines .= $res_course_sis_id["sis_id"].",".$sis_record["sis_id"].",".$role.",".$sis_record["section_id"].",".$status."\r\n";
        	$prev_user_id = $sis_record["user_id"];

            // Update the sis_file field with the filename
            db_exec("UPDATE user_enrollment SET sis_file=".db_sanitize($filename).", sis_status='ul' WHERE enr_id=".$sis_record["enr_id"]);
        }

        // Get file save path
        $sis_path = setting_get("sis_file_save_path");
        // Write data to respective files
        $fuser = fopen($sis_path."user-".$filename.".csv", "a");
        $fenroll = fopen($sis_path."enroll-".$filename.".csv", "a");
        fwrite($fuser, $user_lines);
        fwrite($fenroll, $enroll_lines);
        fclose($fuser);
        fclose($fenroll);

        // Zip the files
        $zip = new ZipArchive;
    	$zip->open($sis_path."sis-".$filename.".zip", ZipArchive::CREATE);
    	$zip->addFile($sis_path."user-".$filename.".csv", "user.csv");
    	$zip->addFile($sis_path."enroll-".$filename.".csv", "enroll.csv");
    	$zip->close();

    	// Delete the original csv files
    	unlink($sis_path."user-".$filename.".csv");
    	unlink($sis_path."enroll-".$filename.".csv");

        // Put back the file name in system_setting
        setting_set("last_sis_file", $filename);

        $to = "programoffice@jigsawacademy.com, moses.kola@jigsawacademy.com";
        $subject = "Download SIS File to Import!";

        $message = "
        <html>
        <head>
        </head>
        <body>
        Hi,<br /><br />
        SIS file is available for importing.<br /><br />Please <a href='https://jigsawacademy.com/jaws/view/backend/temp/sis.get?sis=".$filename."'>click here</a> to download the latest SIS file.<br /><br />
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

        $result = false;
        $retry = 0;

        // Keep trying until curl_exec() returns something positive
        while ($retry < 3)
        {
            $file = fopen($sis_path."sis-".$filename.".zip", "rb");
            $fzip = fread($file, filesize($sis_path."sis-".$filename.".zip"));
            // Prepare to transfer the zip file to LMS
        	$curl = curl_init();
        	curl_setopt($curl, CURLOPT_URL, $lms_url."api/v1/accounts/1/sis_imports.json?import_type=instructure_csv");
        	curl_setopt($curl, CURLOPT_POST, true);
        	curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $fzip);
        	curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: Bearer ".$lms_dev_key, "Content-Type: application/zip"));
        	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            // Go! Go! GO!!
        	$result = curl_exec($curl);

        	if (!$result)
        	{
                // If SIS import has failed thrice, just log the error with priority as 'critical' and break
                if ($retry >= 2)
                {
                    // Log the problem
                    activity_create("critical", "sis.import.automation", "fail", "", "", "", "", "Import of sis-".$filename_timestamp.".zip failed thrice. Reason: ".curl_error($curl), "pending");
                    break;
                }
                $retry++;
        		// Log the problem with priority as 'high'
                activity_create("high", "sis.import.automation", "fail", "", "", "", "", "Import of sis-".$filename_timestamp.".zip failed. Reason: ".curl_error($curl).". Retry: ".$retry);
                // We might try implmenting a special API on LMS which will allow checking of account creation status through SIS ID.

        	}
        	else
        	{
        		// Check the status
        		$result = json_decode($result, true);

                // All went well
        		if (strcmp($result["workflow_state"], "imported") == 0)
                {
                    // Update the sis_status of the present record to 'ul'
        			db_exec("UPDATE course_enrollment SET sis_status='ul' WHERE sis_file=".db_sanitize($filename));
                    activity_create("low", "sis.import.automation", "success", "", "", "", "", "SIS Import successful at ".$filename_timestamp.". ".$count." records imported.");
                }
                // Import failed
        		else if (strcmp($result["workflow_state"], "failed") == 0)
        		{
        			// Log the event with result object as description and notify programoffice..
                    activity_create("critical", "sis.import.automation", "fail", "", "", "", "", "Import of sis-".$filename_timestamp.".zip returned error".json_encode($result));
        		}
                // Import is taking time
                else if (strcmp($result["workflow_state"], "created") == 0)
                {
                    // Save the import ID to check the status later and break the loop
                    setting_set("pending_sis_import_id", $result["id"]);
                }
                break;
        	}
        }
    }

    //========================================================== SIS import Automation ENDS Here ===========================================================//

    //=============================================================== Lab import STARTS Here ===============================================================//

    // Get the list of enrollments that have lab_status as 'na'
    $res_lab = db_query("SELECT enr_id, user_id, subs_id, course_id, lab_ip, lab_user, lab_pass FROM subs WHERE lab_status='na'");

    if ($res_lab)
    {
        // Set the header row
        $lab_lines = "Server,FN,LN,UserName,Password,Group\r\n";

        // Get the latest undownloaded lab input file
        $filename = setting_get("last_lab_file");
        if (!$filename || strcmp($filename, "nil") == 0)
            $filename = "lab-".$filename_timestamp.".csv";

        foreach ($res_lab as $lab_record)
        {
            $res_user = db_query("SELECT name FROM user WHERE user_id=".$lab_record["user_id"]);
            $res_course_lab = db_query("SELECT dir FROM course_lab WHERE course_id=".$lab_record["course_id"]." AND lab_ip=".$lab_record["lab_ip"]);
            $lab_lines .= $lab_record["lab_ip"].",".$res_user[0]["name"].",,".$lab_record["lab_user"].",".$lab_record["lab_pass"].",".$res_course_lab[0]["dir"]."\r\n";

            db_exec("UPDATE course_enrollment SET lab_status='cr', lab_file='".$filename."' WHERE enr_id=".$lab_record["enr_id"]);
        }

        // Write the Lab file only if the debug is off
        if (!$debug_state)
        {
            // Get file save path
            $lab_path = setting_get("lab_file_save_path");
            // Write the data to file
            $flab = fopen($lab_path.$filename, "w");
            fwrite($flab, $lab_lines);
            fclose($flab);
        }

        // Put back the file name in system_setting
        setting_set("last_lab_file", $filename);

        // After this, the file link is to be given for downloading
    }

    //================================================================ Lab import ENDS Here ================================================================//

?>
