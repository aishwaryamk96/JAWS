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

	$sfwdb;

	try {
  		$sfwdb = new PDO("mysql:host=localhost;dbname=admin_minilms", "root", "Jigsaw@1234");
		$sfwdb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch(PDOException $e) {
		die(nl2br("JAWS failed to connect to the admin_minilms database.\n").$e->getMessage());
	}

	$qsfw = $sfwdb->query("SELECT * FROM leads WHERE `date` > DATE_SUB(NOW(), INTERVAL 1 DAY);");
	set_fetch_mode($qsfw, -1);
	$res_sfw = $qsfw->fetchAll();

	$res_jaws = db_query("SELECT * FROM user_leads_basic WHERE (`create_date` > DATE_SUB(NOW(), INTERVAL 1 DAY)) AND ad_lp='LP';");

	$count_missed = 0;
	$count_caught = 0;
	foreach($res_sfw as $ress) {
		$flag = false;
		foreach($res_jaws as $resj) {
			if ($ress['email'] == $resj['email']) {
				$flag=true;
				$count_caught++;
				break;
			}
		}
		if ($flag) continue;
		$count_missed++;

		$q = "INSERT INTO user_leads_basic (
					name, 
					email, 
					phone, 
					utm_source, 
					utm_campaign, 
					utm_term, 
					utm_medium, 
					utm_content, 
					utm_segment, 
					utm_numvisits, 
					gcl_id, 
					global_id_perm,
					referer, 
					ip, 
					ad_lp, 
					ad_url, 
					create_date,
					capture_trigger, 
					capture_type
				) VALUES (".
					db_sanitize($ress['name'] ?? '').", ".
					db_sanitize($ress['email'] ?? '').", ".
					db_sanitize($ress['phone'] ?? '').", ".
					db_sanitize($ress['source'] ?? '').", ".
					db_sanitize($ress['campaign'] ?? '').", ".
					db_sanitize($ress['term'] ?? '').", ".
					db_sanitize($ress['medium'] ?? '').", ".
					db_sanitize($ress['content'] ?? '').", ".
					db_sanitize($ress['segment'] ?? '').", ".
					db_sanitize($ress['numvisits'] ?? '').", ".
					db_sanitize($ress['gclid'] ?? '').", ".
					db_sanitize($ress['global_id'] ?? '').", ".
					db_sanitize($ress['referer'] ?? '').", ".
					db_sanitize($ress['ip'] ?? '').", ".
					db_sanitize('LP').", ".
					db_sanitize($ress['location'] ?? '').", ".
					db_sanitize($ress['date'] ?? '').", ".
					"'formsubmit'".", ".
					"'url'".");";

		db_exec($q);
	}

	activity_create('high','sfw.to.jaws','shuriken.fail.recompense','','','','',json_encode(["capture_failed"=>$count_missed, "capture_succeeded"=>$count_caught]));
	exit();
?>