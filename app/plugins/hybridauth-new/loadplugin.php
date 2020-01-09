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

    // Hybrid Auth NEW Plugin
    // Implements social login and authentication functions.
    //-------------------

    // This will return user profile info on the given provider
    function soc_get_info($provider, $initsession = true, $reauth = false) {

    	// Convert array
    	$provider_abbr = array(
	 			"gp" => "Google",
	 			"fb" => "Facebook",
				"li" => "LinkedIn"
	 			);

    	// Check if provider specified is valid!
    	if (!array_key_exists($provider, $provider_abbr)) return false;

    	// Stop our session to let the plugin use it's own
    	auth_session_deinit();

    	// Load Config
		$config = array(
		"base_url" => "https://".$_SERVER["SERVER_NAME"]."/".JAWS_PATH_LOCAL."/hybridauth/",
		"providers" => array(
			"Google" => array(
				"force" => false,
				"enabled" => true,
				"keys" => array("id" => ($GLOBALS['jaws_exec_live'] ? JAWS_AUTH_SOCIAL_GP_ID : JAWS_AUTH_SOCIAL_GP_ID_TEST), "secret" => ($GLOBALS['jaws_exec_live'] ? JAWS_AUTH_SOCIAL_GP_KEY : JAWS_AUTH_SOCIAL_GP_KEY_TEST)),
				"scope" => "https://www.googleapis.com/auth/userinfo.profile "."https://www.googleapis.com/auth/userinfo.email",
				"access_type" => "online"
			),
			"Facebook" => array(
				"force" => $reauth,
				"enabled" => true,
				"keys" => array("id" => ($GLOBALS['jaws_exec_live'] ? JAWS_AUTH_SOCIAL_FB_SID : JAWS_AUTH_SOCIAL_FB_SID_TEST), "secret" => ($GLOBALS['jaws_exec_live'] ? JAWS_AUTH_SOCIAL_FB_KEY : JAWS_AUTH_SOCIAL_FB_KEY_TEST)),
				"scope"   => "email",
				"trustForwarded" => false
			),
			"LinkedIn" => array(
				"enabled" => true,
				"keys" => array("key" => ($GLOBALS['jaws_exec_live'] ? JAWS_AUTH_SOCIAL_LI_ID : JAWS_AUTH_SOCIAL_LI_ID_TEST), "secret" => ($GLOBALS['jaws_exec_live'] ? JAWS_AUTH_SOCIAL_LI_KEY : JAWS_AUTH_SOCIAL_LI_KEY_TEST))
			)
		),
		"debug_mode" => false,
		"debug_file" => "",
	);

    	// Load the file
    	require_once("app/plugins/hybridauth/hybridauth/Hybrid/Auth.php");

    	// Init
    	$hybridauth = new Hybrid_Auth($config);

    	// Authenticate with soc
    	$adapter = $hybridauth->authenticate($provider_abbr[$provider]);

		// Get the user profile
		$user_profile = $adapter->getUserProfile();


		// Debug Code <<-----------------------------------------------------------------------------------------------------REMOVE DEBUG CODE !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		//activity_debug_start();
    		//activity_debug_log('SOC '.(isset($user_profile->email) ? $user_profile->email : '').' NAME '.(isset($user_profile->displayName) ? $user_profile->displayName : '').' '.json_encode($user_profile));

		// Check
		if ((!isset($user_profile->email)) || (strlen($user_profile->email) == 0)) {

			// Special condition for FB
			if (strcmp($provider, "fb") == 0) {

				// Init
    			unset($hybridauth);
    			$config["providers"]["Facebook"]["auth_type"] = "rerequest";
    			$hybridauth = new Hybrid_Auth($config);
    			$hybridauth->logoutAllProviders();

    			// Authenticate with soc
    			$adapter = $hybridauth->authenticate($provider_abbr[$provider]);

				// Get the user profile
				$user_profile = $adapter->getUserProfile();

				// Check again
				if ((!isset($user_profile->email)) || (strlen($user_profile->email) == 0)) return false;

			}

			else return false;
		}

		// Logout
		$adapter->logout();

		// Destroy current session
    	session_destroy();

    	// Re-init our session
    	if ($initsession) auth_session_init();

		// All done
		return array(
               		"email" => $user_profile->email,
               		"name" => $user_profile->displayName,
               		"soc" => $provider,
               		"photo_url" => $user_profile->photoURL
               	);


    }


?>

