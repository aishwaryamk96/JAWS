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
    if (!auth_api("coupon.reg")) die("You do not have the required priviledges to use this feature.");

    // Load stuff
    load_module("leads");
    load_module("user");

    load_library("email");

    if (!isset($_POST["emailid"]) || !isset($_POST["app_version"]))
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

    $res_avail = db_query("SELECT * FROM system_activity WHERE act_type='app.coupon.avail' AND context_id=".$user["user_id"]);
    if (!$res_avail)
        activity_create("ignore", "app.coupon.avail", "JAAPP500", "coupon", "", "user_id", $user["user_id"], "mobappversion".$_POST["app_version"]);
    else
    {
        echo json_encode(array("Items" => "success", "Count" => 1, "ScannedCount" => 1));
        exit();
    }

    $date = new DateTime("now");
    $date->add(new DateInterval("P15D"));

    db_exec(
        "INSERT INTO user_leads_basic (user_id, ad_lp, ad_url, capture_trigger, capture_type, meta)
        VALUES
        (".$user["user_id"].", 'mobapp', 'coupon', 'formsubmit', 'url', '{\"mobapp\":\"coupon\"}');"
    );

    // Prep
    $content["subject"] = "Jigsaw Academy Discount Coupon Confirmation";
    $content["fname"] = $user["name"];
    $content["header"] = "Jigsaw Academy Discount Coupon Confirmation";
    $content["sub-header"] = "Thank you for your interest in Jigsaw Academy courses.";
    $content["text"] = "Please quote the coupon code <b>'JAAPP500'</b> to avail a Rs. 500 discount on any course of your choice. Please note that this coupon is valid on enrollments within the next 15 days, i.e., <b>until ".$date->format("d-m-Y").".</b> <br /><br />Please do register for any course on Jigsaw Academy within this period and avail this additional discount. <br /><br />With Best Wishes <br />Team Jigsaw Academy <br />Contact: info@jigsawacademy.com | +91 92435-222-77 <br /><br />";

    // Send custom email
    $template = "wp.formsubmit";
    send_email($template, array("to" => $_POST["emailid"], "subject" => "Jigsaw Academy Discount Coupon Confirmation"), $content);

    /*$content["fname"] = $user["name"];
    $content["header"] = "Jigsaw Academy Mobile Coupon Confirmation";
    $content["sub-header"] = "Thank you for your interest in the coupon.";
    $content["text"] = "Dear ".$user["name"].",<br /><br />We are glad to offer to you the coupon <b> 'JAAPP500'.</b> Please note that this coupon is valid for 15 days ie <b>till ".$_POST["enddate"].".</b> <br /><br />Please do register for any course on Jigsaw Academy within this period and avail this additional discount. <br /><br />With Best Wishes <br />Team Jigsaw Academy <br />Contact: info@jigsawacademy.com | +91 92435-222-77 <br /><br />";*/

    // Send the email
    $to = "info@jigsawacademy.com";
    $subject = "Mobile App Lead - Coupon Availed";

    $message = "
    <html>
    <head>
    </head>
    <body>
    Dear Sales Team,
    A student has availed the mobile coupon JAAPP500.<br />Below are his details:<br/><br/>
    Name: ".$user["name"]."<br/>
    Phone: ".$user["phone"]."<br/>
    Email: ".$_POST["emailid"]."<br/>
    <br/>
    --<br/>
    Thanks,<br/>
    Mobile App Team
    </body>
    </html>";

    // More headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: <no-reply@jigsawacademy.com>' . "\r\n";
    $headers .= 'Cc: ravi@jigsawacademy.com' . "\r\n";
    mail($to, $subject, $message, $headers);

    echo json_encode(array("Items" => "success", "Count" => 1, "ScannedCount" => 1));
    exit();
?>