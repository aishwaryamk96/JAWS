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

  	$res_free_acts = db_exec("SELECT * FROM system_activity WHERE act_type='sis.import.automation.free' AND activity='pending' AND entity_type='sis.batch';");

  	if (!isset($res_free_acts[0]))
  		exit();

  	if (!$GLOBALS["jaws_exec_live"])
    {
        $lms_url = setting_get("lms_url_debug");
        $lms_dev_key = setting_get("lms_dev_key_debug");
    }
    else
    {
        $lms_url = setting_get("lms_url_free");
        $lms_dev_key = setting_get("lms_dev_key_free");
    }

  	foreach ($res_free_acts as $free_activity)
  	{
  		$content = json_decode($free_activity["content"], true);

  		$result = false;
        $retry = 0;
        while (!$result && $retry < 3)
        {
	  		// Send a request to the LMS with the SIS Batch ID to get it's status
	        $curl = curl_init();
	        curl_setopt($curl, CURLOPT_URL, $lms_url."api/v1/accounts/1/sis_imports/".$free_activity["entity_id"]);
	        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: Bearer ".$lms_dev_key));
	        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

	        $result = curl_exec($curl);

	        if (!$result)
            {
                $retry++;
                sleep(2);
                continue;
            }

            // Check the status
            $result = json_decode($result, true);

            // All went well
            if (strcmp($result["workflow_state"], "imported") == 0)
            {
                db_exec("UPDATE system_activity SET activity='success' WHERE act_id=".$free_activity["act_id"].";");
                // Update the activity
				db_exec("UPDATE system_active SET status='executed' WHERE act_type='jlc.free' AND activity='setup' AND content=".db_sanitize($content["email"]).";");

				// Get Course
				$course = course_get_info_by_id($course);

				// Trigger Welcome Email
		    	send_email('lms.free.setup.success', ['to' => $email], [
		    		'fname' => substr($name, 0, ((strpos($name, " ") !== false) ? strpos($name, " ") : strlen($name))),
		    		'course' => [
		    				'name' => $course['name']
		    				],
		    		'access' => [
		    				'duration' => 15,
		    				'end_date' => date('dS M Y', strtotime("+15 days")),
		    				'account' => [
		    					'mode' => (!isset($_REQUEST["corp"])) ? 'soc' : 'corp',
		    					'provider' => (!isset($_REQUEST["corp"])) ? ($_REQUEST["soc"] == 'fb' ? 'Facebook' : ($_REQUEST["soc"] == 'gp' ? 'Google+' : 'LinkedIn')) : '',
		    					'username' => $email,
		    					'password' => $password ?? ''
		    					]
		    				]
		    	]);
			    continue;
            }
            // Import failed
            else if (strcmp($result["workflow_state"], "failed") == 0)
            {
                // Log the event with result object as description
                db_exec("UPDATE system_activity SET activity='success' WHERE act_id=".$free_activity["act_id"].";");

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

	            $message .= "Please take a look at the issue.<br />Please <a href='https://jigsawacademy.com/jaws/view/backend/temp/sis.get?sis=".$content["sis_id"]."&free=1'>click here</a> to download the troublesome file.<br /><br />
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

            	continue;
            }
            // Import is taking time
            else if (strcmp($result["workflow_state"], "created") == 0)
            {
                $old_batch_id = $result["id"];
                $retry++;
                $result = false;
                sleep(2);
            }
	    }
	    if ($retry >= 3)
        {
            // Log the event with result object as description
            db_exec("UPDATE system_activity SET activity='pending.fail' WHERE act_id=".$free_activity["act_id"].";");
            // Send a mail to programoffice@jigsawacademy.com to report the problem
            $to = "programoffice@jigsawacademy.com";
            $subject = "<Freelearning> SIS Import pending on LMS";

            $message = "
            <html>
            <head>
            </head>
            <body>
            Hi,<br /><br />
            SIS import is pending on the Freelearning LMS for ususually long time. <br /><br />Please take a look at the issue.<br />Please <a href='https://jigsawacademy.com/jaws/view/backend/temp/sis.get?sis=".$content["sis_id"]."&free=1'>click here</a> to download the troublesome file.<br /><br />
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
        }
  	}

 ?>