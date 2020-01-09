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

    	JIGSAW ACADEMY WORKFLOW SYSTEM v2				
    	---------------------------------
*/

    	// Prevent exclusive access
	if (!defined("JAWS")) {
		header('Location: https://www.jigsawacademy.com/index.php');
		die();
	}

	// Shuriken Hooks for Form submissions to JAWS Leads table.
	// -------------------

	// Hooks In
	hook('shuriken_event_end_form', 'shuriken_event_end_form_to_jaws');
	hook('shuriken_event_process_form', 'shuriken_event_process_form_to_jaws');

	// Event : form, Handler for form end
	function shuriken_event_process_form_to_jaws(&$kue, &$i) {
		//$kue['v'][$i]['storage_mode'] = 'replace';
	}

	// Event : form, Handler for form end
	function shuriken_event_end_form_to_jaws(&$kue, &$i) {

		try {
			$old = db_query('SELECT * FROM user_leads_basic WHERE name='.db_sanitize($kue['v'][$i]['d']["name"] ?? $kue['v'][$i]['d']["fname"] ?? $kue['v'][$i]['d']["firstname"] ?? $kue['v'][$i]['d']["fullname"] ?? $kue['v'][$i]['d']["lname"]).' AND email='.db_sanitize($kue['v'][$i]['d']["email"] ?? $kue['v'][$i]['d']["e-mail"] ?? $kue['v'][$i]['d']["username"] ?? $kue['v'][$i]['d']["emailid"]).' AND phone='.db_sanitize($kue['v'][$i]['d']["phone"] ?? $kue['v'][$i]['d']["mobile"] ?? $kue['v'][$i]['d']["contact"] ?? $kue['v'][$i]['d']["mobileno"]?? $kue['v'][$i]['d']["telephone"] ?? $kue['v'][$i]['d']["contactno"]).' AND create_date='.db_sanitize(strval(date("Y-m-d H:i:s"))).' LIMIT 1;');

			if (isset($old[0])) return;
			else db_exec("INSERT INTO user_leads_basic (
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
					xuid,
					referer, 
					ip, 
					ad_lp, 
					ad_url, 
					create_date,
					capture_trigger, 
					capture_type
				) VALUES (".
					db_sanitize($kue['v'][$i]['d']["name"] ?? $kue['v'][$i]['d']["fname"] ?? $kue['v'][$i]['d']["firstname"] ?? $kue['v'][$i]['d']["fullname"] ?? $kue['v'][$i]['d']["lname"]).", ".
					db_sanitize($kue['v'][$i]['d']["email"] ?? $kue['v'][$i]['d']["e-mail"] ?? $kue['v'][$i]['d']["username"] ?? $kue['v'][$i]['d']["emailid"]).", ".
					db_sanitize($kue['v'][$i]['d']["phone"] ?? $kue['v'][$i]['d']["mobile"] ?? $kue['v'][$i]['d']["contact"] ?? $kue['v'][$i]['d']["mobileno"]?? $kue['v'][$i]['d']["telephone"] ?? $kue['v'][$i]['d']["contactno"]).", ".
					db_sanitize($kue['v'][$i]['d']["source"] ?? $kue['url']['params']['utm_source'] ?? '').", ".
					db_sanitize($kue['v'][$i]['d']["campaign"] ?? $kue['url']['params']['utm_campaign'] ?? '').", ".
					db_sanitize($kue['v'][$i]['d']["term"] ?? $kue['url']['params']['utm_term'] ?? '').", ".
					db_sanitize($kue['v'][$i]['d']["medium"] ?? $kue['url']['params']['utm_medium'] ?? '').", ".
					db_sanitize($kue['v'][$i]['d']["content"] ?? $kue['url']['params']['utm_content'] ?? '').", ".
					db_sanitize($kue['v'][$i]['d']["segment"] ?? $kue['url']['params']['utm_segment'] ?? '').", ".
					db_sanitize($kue['v'][$i]['d']["numVisits"] ?? '').", ".
					db_sanitize($kue['v'][$i]['d']["gclid"] ?? '').", ".
					db_sanitize($kue['v'][$i]['d']["global_id"] ?? '').", ".
					db_sanitize($kue['url']['params']['xuid'] ?? '').", ".
					db_sanitize($kue['v'][$i]['d']["referer"] ?? '').", ".
					db_sanitize($kue["ip"]).", ".
					db_sanitize('LP').", ".
					db_sanitize(url_template_to_string($kue["url"])).", ".
					db_sanitize(strval(date("Y-m-d H:i:s"))).", ".
					"'formsubmit'".", ".
					"'url'".");");
		}
		catch (Exception $e) {
			activity_create('critical','shuriken.event.form','form.submit.to.jaws.fail','','','','',json_encode($kue['v'][$i]));
		}


	}