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

	// This view is used in showing popup for HybridAuth Social plugin
	// Popup is necessary when the plugin is called via AJAX
	// The result of the authentication is stored in session var ["auth"]["social"]
	// The social provider to be used is provided as a GET variable

    //Check
    if ((!isset($_GET["soc"])) || (!(in_array($_GET["soc"], array("fb", "gp", "li"))))) {
        echo "<script type='text/javascript'>";
        echo "window.close();";
        echo "</script>";
        
        die();
    }

    // Do we restore the session after hybridauth is done?
    $restore_session = true;
    if (isset($_GET["session"])) $restore_session = $_GET["session"];

	//Load stuff	
	load_plugin("hybridauth");
    
	//Authenticate and get info
    $soc = $_GET["soc"];
    $soc_info = soc_get_info($soc, $restore_session, true);

    //Store in session
    $_SESSION["auth"]["social"][$soc] = $soc_info;

    //All done - Force close this window
    echo "<script type='text/javascript'>";
    echo "window.close();";
    echo "</script>";

?>
