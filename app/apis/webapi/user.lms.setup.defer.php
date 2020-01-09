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

  	// This will send an email reminder to the user to complete their LMS setup process
    // Auth is based on logged-in user
    // Note: This API does NOT use persistence !! (Not needed by logic)

    // Init Session
    auth_session_init();

    // Auth
    if (!auth_session_is_logged()) die(json_encode(array("is_notified" => false)));

    // Check
    if ((isset($_SESSION["user"]["lms_soc"])) && (strlen($_SESSION["user"]["lms_soc"]) > 0)) die(json_encode(array("is_notified" => false)));

    // Prep email !  
    $content["fname"] = substr($_SESSION["user"]["name"], 0, ((strpos($_SESSION["user"]["name"], " ") !== false) ? strpos($_SESSION["user"]["name"], " ") : strlen($_SESSION["user"]["name"])));  
    $content["user_webid"] = $_SESSION["user"]["web_id"];

    // Done! Send an email   
    $template = "lms.setup.defer";
    load_library("email");      
    send_email($template, array("to" => $_SESSION["user"]["email"]), $content);

    // All Done!
    echo json_encode(array("is_notified" => true));   
    exit();

?>

