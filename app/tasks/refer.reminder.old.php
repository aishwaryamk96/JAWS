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

	load_module("user");
	load_library("email");

	//get current date
	$date = date("Y-m-d");
	
	$res_referral_act = db_query("SELECT * FROM system_activity WHERE act_type='jlc.referral' AND activity='referral' ORDER BY act_id DESC;");
	
	$no_action = array();
	
	foreach ($res_referral_act as $res)
	{
		$ref = array();
		$content = json_decode($res['content'], true);
		foreach ($content['r'] as $key=>$cont)
		{  
			if (!isset($cont["X"]))
			{
				$referred_subs = db_query("SELECT * FROM subs INNER JOIN user ON user.user_id = subs.user_id WHERE subs.status!='inactive' AND (user.email=".db_sanitize($cont["e"])." OR user.soc_fb=".db_sanitize($cont["e"])." OR user.soc_gp=".db_sanitize($cont["e"])." OR user.soc_li=".db_sanitize($cont["e"]).");");
				if (isset($referred_subs[0]))
					continue;
				$content_date = date("Y-m-d", strtotime($cont["d"]));
				$ref_date_plus_25 = date('Y-m-d', strtotime($content_date. ' + 25 days'));
				if ($date == $ref_date_plus_25)
				{
					if ($res["context_type"] == "user")
					{
						$user = user_get_by_id($res["context_id"]);
						$ref["referrer_email"] = $user["email"];
						$ref["referrer_name"] = $user['name'];
					}
					else
					{
						$user = json_decode(db_query("SELECT content FROM system_activity WHERE act_id=".$res["context_id"])[0]["content"], true);
						$ref["referrer_email"] = $user["email"];
						$ref["referrer_name"] = $user['name'];
					}

					$ref['referral'][$key]["referral_name"] = $cont['n'];
					$ref['referral'][$key]["referral_email"] = $cont['e'];
					$ref['referral'][$key]["date"] = $cont['d'];
					$ref['referral'][$key]["cc"] = $cont['cc'];

					$content["referrer_name"] = $user['name'];
					$content["referral_name"] = $cont["n"];
					$content["date"] = $cont['d'];
					$content["coupon_code"] = $cont["cc"];
					
					// send mail to referral (person who was referred)
					//send_email("no.action.referral", array("to" => (($GLOBALS["jaws_exec_live"]) ? $cont['e'] : "nikita@jigsawacademy.com"), "subject" => "Hurry up! Coupon shared by ".$user['name']." is about to expire"), $content);
				}
			} 
		}
		if(!empty($ref))
		{
			// send mail to referrer (person who referred his friend)
			//send_email("no.action.referrer", array("to" => (($GLOBALS["jaws_exec_live"]) ? $ref["referrer_email"] : "nikita@jigsawacademy.com"), "subject" => "Can't wait to give you Amazon voucher"), $ref);
		}
	}

?>