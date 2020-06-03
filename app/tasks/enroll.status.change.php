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

    // This task updates the status of a subscription and that of it's corresponding enrollments
    // This task checks if a subscription is supposed to switch from frozen to active, or active to alumni, or alumni to expired,
    // or if the freeze period is approaching, etc.
    load_module("subs");

    // Start with subscriptions for which the freeze period is approaching
//    $res_to_freeze = db_query("SELECT subs_id FRom subs WHERE freeze_date=CURDATE();");
//    if ($res_to_freeze)
//    {
//    	foreach ($res_to_freeze as $each_subs)
//    	{
//    		subs_update_status($each_subs["subs_id"], "frozen");
//    		db_exec("UPDATE user_enrollment SET sis_status='na' WHERE subs_id=".$each_subs["subs_id"]);
//    	}
//    }
//
//    // Pick those to unfreeze today
//    $res_to_unfreeze = db_query("SELECT subs_id FROM subs WHERE unfreeze_date=CURDATE();");
//    if ($res_to_unfreeze)
//    {
//    	foreach ($res_to_unfreeze as $each_subs)
//    	{
//    		subs_update_status($each_subs["subs_id"], "active");
//    		db_exec("UPDATE user_enrollment SET sis_status='na' WHERE subs_id=".$each_subs["subs_id"]);
//    	}
//    }
//JA-73 START
    $particularDate =db_sanitize(date("Y-m-d 00:00:00"));
// Pick those to set to alumni
    $res_to_alumni = "SELECT subs_id , end_date FROM subs WHERE end_date < ".$particularDate." and status ='active' order by subs_id desc";
    if ($res_to_alumni)
    {
        foreach ($res_to_alumni as $each_subs)
        {
            subs_update_status($each_subs["subs_id"], "alumni");
            db_exec("UPDATE user_enrollment SET sis_status='na' WHERE subs_id=".$each_subs["subs_id"]);
            sis_import($each_subs["subs_id"]);
        }
    }
//JA-73 END

// Pick those to expire today
//    $res_to_expire = db_query("SELECT subs_id FROM subs WHERE CURDATE()=DATE_ADD(end_date_ext,INTERVAL 1 YEAR;)");
//    if ($res_to_expire)
//    {
//    	foreach ($res_to_expire as $each_subs)
//    	{
//    		subs_update_status($each_subs["subs_id"], "alumni");
//    		db_exec("UPDATE user_enrollment SET sis_status='na' WHERE subs_id=".$each_subs["subs_id"]);
//    	}
//    }

    // Need to work on cases when payment is overdue and subscription has to be blocked