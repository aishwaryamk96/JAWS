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

	// Check Auth
	if (!auth_api("coupon.get")) die("You do not have the required priviledges to use this feature.");
/*
	load_module("coupon");

	$res_coupons = coupon_get_all();
	if (!$res_coupons)
	{
		echo json_encode(array("Items" => false, "Count" => 0, "ScannedCount" => 0));
		exit();
	}
	$coupons_arr = array();
	foreach ($res_coupons as $res_coupon)
	{
		$valid_days = date_diff(date_create_from_format("Y-m-d H:i:s", $res_coupon["start_date"]), date_create_from_format("Y-m-d H:i:s", $res_coupon["end_date"]), true);
		$coupons_arr[] = array("couponid" => $res_coupon["coupon_id"], "startdate" => $res_coupon["start_date"], "enddate" => $res_coupon["end_date"], "validdays" => $valid_days->format("l"), "value" => 500);
	}
*/
	$date = new DateTime("now");
	$start_date = $date->format("Y-m-d H:i:s");
	$date->add(new DateInterval("P15D"));
	$end_date = $date->format("Y-m-d H:i:s");
	$coupons_arr = array("couponid" => "JAAPP500", "startdate" => $start_date, "enddate" => $end_date, "validdays" => "15", "value" => "500");
	echo json_encode(array("Items" => array($coupons_arr), "Count" => count($coupons_arr), "ScannedCount" => count($coupons_arr)));
	exit();
?>