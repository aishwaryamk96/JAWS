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


    // This handles extra information related to users
    // --------------------

    // This will validate the access setup steps
    function user_meta_setup_validate($data, $step = 0) {

        // Prep
        auth_session_init();
        $err_arr["is_valid"] = true;

        // Check
        if (!auth_session_is_logged()) {
            $err_arr["is_valid"] = false;
            return $err_arr;
        }

        // Check step
        if (($step == 1) || ($step == 0)) {

            // Phone
            $data["phone"] = str_replace(" ", "", $data["phone"]);
            $data["phone"] = str_replace("-", "", $data["phone"]);
            $data["phone"] = str_replace("+", "", $data["phone"]);

            if ((strlen($data["phone"]) < 8) || (strlen($data["phone"]) > 13) || (!is_numeric($data["phone"]))) {
                $err_arr["is_valid"] = false;
                $err_arr["phone"] = "Enter a valid phone number";
            }

            // Comm Email
            if (!filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
                $err_arr["is_valid"] = false;
                $err_arr["email"] = "Enter a valid email ID";
            }

            else {
                $user = user_get_by_email($data["email"]);

                if (!($user === false)) {
                    if (strcmp($user["user_id"], $_SESSION["user"]["user_id"]) != 0) {
                        $err_arr["is_valid"] = false;
                        $err_arr["email"] = "This email ID is in use by another account";
                    }
                }
            }

            // length checks for drop downs
            if (strlen($data["age"]) > 12) {

                // Log possible hack attempt

                $err_arr["is_valid"] = false;
                $err_arr["age"] = "Invalid age range";
            }

            if (strlen($data["gender"]) > 6) {

                // Log possible hack attempt

                $err_arr["is_valid"] = false;
                $err_arr["gender"] = "Invalid gender";
            }

        }

        // Check step
        if (($step == 2) || ($step == 0)) {

            // length checks for drop downs
            if (strlen($data["country"]) > 50) {

                // Log possible hack attempt

                $err_arr["is_valid"] = false;
                $err_arr["country"] = "Invalid country";
            }

            if (strlen($data["state"]) > 25) {

                // Log possible hack attempt

                $err_arr["is_valid"] = false;
                $err_arr["state"] = "Invalid state";
            }

            // Length check for city
            if ( (strlen($data["city"]) > 25) || (empty($data["city"])) ) {

                // Log possible hack attempt

                $err_arr["is_valid"] = false;
                $err_arr["city"] = empty($data["city"]) ? "Please provide city." : "Invalid city";
            }

            //zipcode
            if (((strlen($data["zipcode"]) != 6) && (strlen($data["zipcode"]) != 0)) || ((strlen($data["zipcode"]) > 0) && (!is_numeric($data["zipcode"])))) {
                $err_arr["is_valid"] = false;
                $err_arr["zipcode"] = "Enter a valid zipcode";
            }

        }

        // Check step
        if (($step == 3) || ($step == 0)) {

            // length checks for drop downs
            if (strlen($data["qualification"]) > 25) {

                // Log possible hack attempt

                $err_arr["is_valid"] = false;
                $err_arr["qualification"] = "Invalid qualification";
            }

            if (strlen($data["experience"]) > 25) {

                // Log possible hack attempt

                $err_arr["is_valid"] = false;
                $err_arr["experience"] = "Invalid experience";
            }

        }

        // Check step
        if (($step == 4) || ($step == 0)) {

            // length checks for survey answers
            if (strlen($data["why"]) == 0) {
                $err_arr["is_valid"] = false;
                $err_arr["why"] = "Select atleast one option";
            }

        }

        // Check step
        if (($step == 5) || ($step == 0)) {

            // length checks for survey answers
            if (strlen($data["marketing"]) == 0) {
                $err_arr["is_valid"] = false;
                $err_arr["marketing"] = "Select atleast one option";
            }

        }

        // Check step
        if (($step == 6) || ($step == 0)) {

            // length checks for survey answers
            if ((strlen($data["enquiry"]) > 30) || (strlen($data["enquiry"]) == 0)) {
                $err_arr["is_valid"] = false;
                $err_arr["enquiry"] = "Select one option";
            }

        }

        // Check step
        if (($step == 7) || ($step == 0)) {

            // length checks for survey answers
            if (strlen($data["sales"]) == 0) {
                $err_arr["is_valid"] = false;
                $err_arr["sales"] = "Select one option";
            }

        }

        // Check step
        if (($step == 8) || ($step == 0)) {

            // length check
            if (strlen($data["soc"]) != 2) {

                // Log hack attempt

                $err_arr["is_valid"] = false;
                $err_arr["soc"] = "Invalid social network";
            }

            else {
    
                // See if the selected soc is not already set for the logged in user
                if ((!isset($_SESSION["user"]["soc_".$data["soc"]])) || (strlen($_SESSION["user"]["soc_".$data["soc"]]) == 0)) {    
    
                    //Check if social info is available in the session
                    if (!isset($_SESSION["auth"]["social"][$data["soc"]])) {

                        $err_arr["is_valid"] = false;
                        $err_arr["soc"] = "Could not retrieve your profile from this social network";
                    }

                    // process
                    else {

                        $soc_info = $_SESSION["auth"]["social"][$data["soc"]];
                        $user = user_get_by_email($soc_info["email"]);

                        if (!($user === false)) {
                        
                            if (strcmp($user["user_id"], $_SESSION["user"]["user_id"]) != 0) {
                                $err_arr["is_valid"] = false;
                                $err_arr["soc"] = "This social account is already in use by another user";
                            }
    
                            else {

                                // Associate the account and return it
                                $err_arr["soc_info"] = $soc_info["email"];
                                user_update($_SESSION["user"]["user_id"], array("soc_".$data["soc"] => $soc_info["email"]));

                                // update the session
                                $_SESSION["user"]["soc_".$data["soc"]] = $soc_info["email"];
                            }

                        }

                        else {

                            // Associate the account and return it
                            $err_arr["soc_info"] = $soc_info["email"];
                            user_update($_SESSION["user"]["user_id"], array("soc_".$data["soc"] => $soc_info["email"]));

                            // update the session
                            $_SESSION["user"]["soc_".$data["soc"]] = $soc_info["email"];
                        }

                        // unset the social auth session
                        unset($_SESSION["auth"]["social"][$data["soc"]]);

                    }

                }

                else $err_arr["soc_info"] = $_SESSION["user"]["soc_".$data["soc"]];

            }

        }

        // Log
        //if (!$err_arr["is_valid"]) activity_create("ignore", "lms.setup", "fail", "-", 0, "user_id", $_SESSION["user"]["user_id"], "Access Setup - Human Error : ".json_encode($err_arr), "logged");

        // All done
        return $err_arr;

    }

    // This will save the meta information provided on the lms setup page 
    // It will re-run everything through validation and sanitize it and return a status
    function user_meta_setup($data, $notify_user = true) {

        // Check
        if (!auth_session_is_logged()) return false;

        // Run full validation check
        $err_arr = user_meta_setup_validate($data);
        if (!$err_arr["is_valid"]) return false;

        // Prep - No need to sanitize - user_update functions internally sanitize all data !
        $survey_data = json_encode(array(
            "why" => $data["why"],
            "enquiry" => $data["enquiry"],
            "sales" => $data["sales"]
        ));

        // Update meta
        user_update_meta($_SESSION["user"]["user_id"], array(
            "age" => $data["age"],
            "gender" => $data["gender"],
            "country" => $data["country"],
            "state" => $data["state"],
            "city" => $data["city"],
            "zipcode" => $data["zipcode"],
            "qualification" => $data["qualification"],
            "experience" => $data["experience"],
            "leads_media_src" => $data["marketing"],
            "survey_data" => $survey_data,
            "survey_date" => strval(date("Y-m-d H:i:s"))
        ));          

        // Update main info
        user_update($_SESSION["user"]["user_id"], array(
            "phone" => $data["phone"],
            "email" => $data["email"],
            "lms_soc" => $data["soc"]
            ));    

        // Update in session
        $_SESSION["user"]["phone"] = $data["phone"];  
        $_SESSION["user"]["email"] = $data["email"]; 
        $_SESSION["user"]["lms_soc"] = $data["soc"];   

        // Send info about lms setup complete
        if ($notify_user) {

            // Prep email !  
            $content["fname"] = substr($_SESSION["user"]["name"], 0, ((strpos($_SESSION["user"]["name"], " ") !== false) ? strpos($_SESSION["user"]["name"], " ") : strlen($_SESSION["user"]["name"])));  

            // Done! Send an email   
            $template = "lms.setup.success";
            load_library("email");      
            send_email($template, array("to" => $_SESSION["user"]["email"]), $content);

        }

        // Log
        activity_create("ignore", "lms.setup", "success", "-", 0, "user_id", $_SESSION["user"]["user_id"], "Access Setup Complete", "logged");

        // All done !
        return true;

    }




?>