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

    // UI Module 
    // Contains functions to re-use most used UI features for both front end and back end

    $GLOBALS["content"];

    // This will put content data for any template into global vars
    function ui_content_load($content) {         
        $GLOBALS["content"] = $content; 
    }

    // This will render the login modal for logging-in to JAWS from the front-end
    function ui_render_login_front($args) {

        ui_render_head_front(array(
        "title" => "Sign-In",
        "scripts" => array(1 => "app/templates/jaws/frontend/login.js"),
        "styles" => array(1 => "app/templates/jaws/frontend/modal.css")
        ));
    
        ui_content_load($args);
        load_template("jaws/frontend","login");

    }

    // This will process what happens when the social login provider is selected by the user
    function ui_login_front_process($mode) {

        // Login selected - proccess login and redirect to return url
        if (isset($_GET["soc"])) {

            // Forced Re-Auth ?
            $reauth = false;
            if (isset($_GET["reauth"])) {
                if (strcmp($_GET["reauth"], "true") == 0) $reauth = true;
            }

            //load_plugin("hybridauth");
            auth_session_soc($_GET["soc"], $mode, $reauth); 
            header("Location: ".urldecode($_GET["return_url"]));

        }

        // Nothing was set !!! wtf
        else header("Location: ".JAWS_PATH_WWW);

    }

    // This will render the login modal for logging-in to JAWS from the back-end
    function ui_render_login_back($args) {

    }

    // This will render the msgbox modal for front-end
    function ui_render_msg_front($args) {

        ui_render_head_front(array(
        "title" => $args["title"],
        "scripts" => array(1 => "app/templates/jaws/frontend/modal.js"),
        "styles" => array(1 => "app/templates/jaws/frontend/modal.css")
        ));

        ui_content_load($args);
        load_template("jaws/frontend","modal");

    }

    // This will render the msgbox modal for front-end
    function ui_render_msg_back($args) {

    }

    // This will render the html head for the front-end
    function ui_render_head_front($args) {
        error_reporting(E_ALL);
        ui_content_load($args);
        load_template("jaws/frontend","head");
    }

    // This will render the html head for the back-end
    function ui_render_head_back($args) {
        
    }




?>