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

	load_module("subs");
	load_module("user");
	load_library("setting");

	$res_subs_act = db_query("SELECT * FROM system_activity WHERE activity='package.create' AND status='pending';");
	if (isset($res_subs_act[0]))
	{
		foreach ($res_subs_act as $subs_act)
		{
			$subs = subs_get_info($subs_act["context_id"]);
			if ($subs === false)
				continue;

			$user = user_get_by_id($subs["user_id"]);
			$pay = payment_get_info($subs["pay_id"]);

			$package_info["user_id"] = $subs["user_id"];
			$package_info["email"] = $user["email"];
			$package_info["name"] = $user["name"];
			$package_info["phone"] = $user["phone"];
			$package_info["combo"] = $subs["combo"];
			$package_info["combo_free"] = $subs["combo_free"];
			$package_info["bundle_id"] = (strlen($subs["meta"]["bundle_id"]) > 0 ? $subs["meta"]["bundle_id"] : "NULL");
			$package_info["currency"] = $pay["currency"];
			$package_info["sum_basic"] = $pay["sum_basic"];
			$package_info["sum_offered"] = $pay["sum_total"];
			$package_info["sum_total"] = $pay["sum_total"];
			$package_info["tax"] = json_decode(setting_get("payment.tax.percentage"), true)[$pay["currency"]];
			$package_info["instl_fees"] = json_decode(setting_get("payment.instl.fee"), true)[$pay["currency"]];
			$package_info["instl_total"] = $pay["instl_total"];
	//		$package_info["instl"] = array();
			foreach ($pay["instl"] as $instl_count => $instl)
				$package_info["instl"][$instl_count] = array("sum" => $instl["sum"], "due_days" => $instl["due_days"]);
			$package_info["pay_mode"] = "online";
			$package_info["create_date"] = $pay["create_date"];
			$package_info["creator_type"] = "system";
			$package_info["creator_id"] = "0";
			$package_info["creator_comment"] = array("instl" => "", "combo_free" => "", "discount" => "", "misc" => "");
			$package_info["approval_require_comment"] = "";
			$package_info["require_approval_sm"] = 0;
			$package_info["require_approval_pm"] = 0;
			$package_info["status_approval_sm"] = "approved";
			$package_info["status_approval_pm"] = "approved";
			$package_info["approver_comment_sm"] = "";
			$package_info["approver_comment_pm"] = "";
			$package_info["status"] = "executed";

			$package_info["data_courses_actual"] = "";
			$package_info["data_courses_discount"] = "";
			$package_info["data_payment_discount"] = "";
			$package_info["data_tax_amount"] = "";
			$package_info["data_discount_amount"] = "";
			$package_info["data_offered_amount"] = "";
			$package_info["data_instalment_amount"] = "";
			$package_info["data_net_payable"] = "";

			$package = package_create($package_info);

			$query = "UPDATE subs SET package_id=".$package["package_id"]." WHERE subs_id=".$subs["subs_id"].";";
			activity_create("ignore", "debug", "subs.package.update", "package", $package["package_id"], "subs", $subs["subs_id"], $query, "logged");
			db_exec($query);
			db_exec("UPDATE system_activity SET status='executed' WHERE act_id=".$subs_act["act_id"].";");
		}
	}

	$res_pay_act = db_query("SELECT * FROM system_activity WHERE activity='package.payment.update' AND status='pending';");
	if (!isset($res_pay_act[0]))
		exit();

	foreach ($res_pay_act as $payment)
	{
		$pay_instl = db_query("SELECT * FROM payment_instl WHERE instl_id=".$payment["context_id"]);
		$subs = subs_get_info($pay_instl[0]["subs_id"]);

		if (!isset($subs["package_id"]) || strlen($subs["package_id"]) == 0)
			continue;

		$pay_info = array("package_id" => $subs["package_id"], "instl_count" => $pay_instl["instl_count"], "pay_mode" => $pay_instl["pay_mode"], "sum" => $pay_instl["sum"], "status" => "paid");

		dynamics_pe_package_payment_update($pay_info);

		db_exec("UPDATE system_activity SET status='executed' WHERE act_id=".$payment["act_id"].";");
	}


?>
