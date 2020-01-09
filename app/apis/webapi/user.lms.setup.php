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

  	// This will validate setting up of the LMS access
    // Auth is based on logged-in user
    // Note: This API does NOT use persistence !! (Not needed by logic)

    // Init Session
    auth_session_init();

    // Check
    if (!isset($_POST["step"])) die(json_encode(array("is_valid" => false)));

    // Auth
    if (!auth_session_is_logged()) die(json_encode(array("is_valid" => false)));

    // Load stuff
    load_module("user");

    // Parse data
    $data["phone"] = $_POST["phone"];
    $data["email"] = $_POST["email"];
    $data["age"] = $_POST["age"];
    $data["gender"] = $_POST["gender"];
    $data["city"] = $_POST["city"];
    $data["state"] = $_POST["state"];
    $data["country"] = $_POST["country"];
    $data["zipcode"] = $_POST["zipcode"];
    $data["qualification"] = $_POST["qualification"];
    $data["experience"] = $_POST["experience"];
    $data["why"] = $_POST["why"];
    $data["marketing"] = $_POST["marketing"];
    $data["enquiry"] = $_POST["enquiry"];
    $data["sales"] = $_POST["sales"];
    $data["soc"] = $_POST["soc"];
    $step = intval($_POST["step"]);

    // Just validate if step is given
    if ($step > 0) echo json_encode(user_meta_setup_validate($data, $step));
    
    // Attemt to save the info if no step is given - implies all steps complete. Note: function will internally re-run validation again.
    else {

        $ret = user_meta_setup($data);
        if ($ret) echo json_encode(array("is_valid" => true));
        else echo json_encode(array("is_valid" => false));

    }

    //All done
    exit();


?>

