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

    // Check Auth
    if (!auth_api("user.coupon.get")) die("You do not have the required priviledges to use this feature.");

    // Load stuff
    load_module("user");

    if (!isset($_POST["emailid"]))
    {
        echo json_encode(array("Items" => "failure", "Count" => 0, "ScannedCount" => 0));
        exit();
    }

    $user = user_get_by_email($_POST["emailid"]);
    //$coupon = coupon_get_info_by_id($_POST["couponid"]);

    if (!$user)
    {
        echo json_encode(array("Items" => "failure", "Count" => 0, "ScannedCount" => 0));
        exit();
    }

    $res_avail = db_query("SELECT act_date FROM system_activity WHERE act_type='app.coupon.avail' AND context_id=".$user["user_id"]);
    if (!$res_avail)
    {
        echo json_encode(array("Items" => "failure", "Count" => 0, "ScannedCount" => 0));
        exit();
    }

    $res_avail = $res_avail[0];
    $date = date_create_from_format("Y-m-d H:i:s", $res_avail["act_date"]);
    $start_date = $date->format("Y-m-d");
    $date->add(new DateInterval("P15D"));
    $end_date = $date->format("Y-m-d");

    echo json_encode(array("Items" => array(array("userid" => $_POST["emailid"], "couponid" => "JAAPP500", "value" => "500", "startdate" => $start_date, "enddate" => $end_date)), "Count" => 1, "ScannedCount" => 1));
    exit();
?>